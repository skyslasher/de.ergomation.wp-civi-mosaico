<?php

/*
 *
 * Nearly a 100% copy&paste from
 * https://github.com/civicrm/org.civicrm.flexmailer/blob/master/src/Listener/DefaultSender.php
 * except the function insert on line 51 to implement message composition with embedded images
 *
 */

class CRM_WpCiviMosaico_EmbedImagesSender extends \Civi\FlexMailer\Listener\BaseListener
{
    const BULK_MAIL_INSERT_COUNT = 10;

    public function onSend(Civi\FlexMailer\Event\SendBatchEvent $e) {
      static $smtpConnectionErrors = 0;

      if (!$this->isActive()) {
        return;
      }

      $e->stopPropagation();

      $job = $e->getJob();
      $mailing = $e->getMailing();
      $job_date = \CRM_Utils_Date::isoToMysql($job->scheduled_date);
      $mailer = \Civi::service('pear_mail');

      $targetParams = $deliveredParams = array();
      $count = 0;
      $retryBatch = FALSE;

      foreach ($e->getTasks() as $key => $task) {
        /** @var \Civi\FlexMailer\FlexMailerTask $task */
        /** @var \Mail_mime $message */
        if (!$task->hasContent()) {
          continue;
        }

        $message = \Civi\FlexMailer\MailParams::convertMailParamsToMime($task->getMailParams());

        if (empty($message)) {
          // lets keep the message in the queue
          // most likely a permissions related issue with smarty templates
          // or a bad contact id? CRM-9833
          continue;
        }
        /* insert start */
        $message = CRM_WpCiviMosaico_EmbedHTMLImages::doEmbed( $message );
        /* insert end */

        // disable error reporting on real mailings (but leave error reporting for tests), CRM-5744
        if ($job_date) {
          $errorScope = \CRM_Core_TemporaryErrorScope::ignoreException();
        }

        $headers = $message->headers();
        $result = $mailer->send($headers['To'], $message->headers(), $message->get());

        if ($job_date) {
          unset($errorScope);
        }

        if (is_a($result, 'PEAR_Error')) {
          /** @var \PEAR_Error $result */
          // CRM-9191
          $message = $result->getMessage();
          if ($this->isTemporaryError($result->getMessage())) {
            // lets log this message and code
            $code = $result->getCode();
            \CRM_Core_Error::debug_log_message("SMTP Socket Error or failed to set sender error. Message: $message, Code: $code");

            // these are socket write errors which most likely means smtp connection errors
            // lets skip them and reconnect.
            $smtpConnectionErrors++;
            if ($smtpConnectionErrors <= 5) {
              $mailer->disconnect();
              $retryBatch = TRUE;
              continue;
            }

            // seems like we have too many of them in a row, we should
            // write stuff to disk and abort the cron job
            $job->writeToDB($deliveredParams, $targetParams, $mailing, $job_date);

            \CRM_Core_Error::debug_log_message("Too many SMTP Socket Errors. Exiting");
            \CRM_Utils_System::civiExit();
          }
          else {
            $this->recordBounce($job, $task, $result->getMessage());
          }
        }
        else {
          // Register the delivery event.
          $deliveredParams[] = $task->getEventQueueId();
          $targetParams[] = $task->getContactId();

          $count++;
          if ($count % self::BULK_MAIL_INSERT_COUNT == 0) {
            $job->writeToDB($deliveredParams, $targetParams, $mailing, $job_date);
            $count = 0;

            // hack to stop mailing job at run time, CRM-4246.
            // to avoid making too many DB calls for this rare case
            // lets do it when we snapshot
            $status = \CRM_Core_DAO::getFieldValue(
              'CRM_Mailing_DAO_MailingJob',
              $job->id,
              'status',
              'id',
              TRUE
            );

            if ($status != 'Running') {
              $e->setCompleted(FALSE);
              return;
            }
          }
        }

        unset($result);

        // seems like a successful delivery or bounce, lets decrement error count
        // only if we have smtp connection errors
        if ($smtpConnectionErrors > 0) {
          $smtpConnectionErrors--;
        }

        // If we have enabled the Throttle option, this is the time to enforce it.
        $mailThrottleTime = \CRM_Core_Config::singleton()->mailThrottleTime;
        if (!empty($mailThrottleTime)) {
          usleep((int) $mailThrottleTime);
        }
      }

      $completed = $job->writeToDB(
        $deliveredParams,
        $targetParams,
        $mailing,
        $job_date
      );
      if ($retryBatch) {
        $completed = FALSE;
      }
      $e->setCompleted($completed);
    }

    /**
     * Determine if an SMTP error is temporary or permanent.
     *
     * @param string $message
     *   PEAR error message.
     * @return bool
     *   TRUE - Temporary/retriable error
     *   FALSE - Permanent/non-retriable error
     */
    protected function isTemporaryError($message) {
      // SMTP response code is buried in the message.
      $code = preg_match('/ \(code: (.+), response: /', $message, $matches) ? $matches[1] : '';

      if (strpos($message, 'Failed to write to socket') !== FALSE) {
        return TRUE;
      }

      // Register 5xx SMTP response code (permanent failure) as bounce.
      if (isset($code{0}) && $code{0} === '5') {
        return FALSE;
      }

      if (strpos($message, 'Failed to set sender') !== FALSE) {
        return TRUE;
      }

      if (strpos($message, 'Failed to add recipient') !== FALSE) {
        return TRUE;
      }

      if (strpos($message, 'Failed to send data') !== FALSE) {
        return TRUE;
      }

      return FALSE;
    }

    /**
     * @param \CRM_Mailing_BAO_MailingJob $job
     * @param \Civi\FlexMailer\FlexMailerTask $task
     * @param string $errorMessage
     */
    protected function recordBounce($job, $task, $errorMessage) {
      $params = array(
        'event_queue_id' => $task->getEventQueueId(),
        'job_id' => $job->id,
        'hash' => $task->getHash(),
      );
      $params = array_merge($params,
        \CRM_Mailing_BAO_BouncePattern::match($errorMessage)
      );
      \CRM_Mailing_Event_BAO_Bounce::create($params);
    }

}

class CRM_WpCiviMosaico_EmbedHTMLImages
{
    // compile a list of images in the HTML, replace in HTML with aliases,
    // return an array of filename => alias
    private static function scanHTMLforImages( &$html_body )
    {
        $result = [];
        // supress warnings
        libxml_use_internal_errors( true );
        try
        {
            // convert HTML mail into DOM object
            $html_DOM = \DOMDocument::loadHTML( $html_body, LIBXML_BIGLINES );
            // simply return on error
            if ( false === $html_DOM )
                return $result;
            // setup URL parts of our upload directory
            $uploaddir = wp_get_upload_dir();
            $uploaddir_parts = parse_url( $uploaddir[ 'baseurl' ] );

            // search img tags
            $images = $html_DOM->getElementsByTagName( 'img' );
            foreach ( $images as $image )
            {
                // get src attribute
                if ( $image->hasAttribute( 'src' ) )
                {
                    $img_src = $image->getAttribute( 'src' );
                    // create image URL parts
                    $img_src_parts = parse_url( $img_src );
                    if ( $img_src_parts[ 'host' ] == $uploaddir_parts[ 'host' ] )
                    {
                        // file is and on our host
                        $query_array = [];
                        parse_str( $img_src, $query_array );
                        if ( ( array_key_exists( 'q', $query_array ) ) && ( 'civicrm/wp_civi_mosaico/img' == $query_array[ 'q' ] ) )
                        {
                            // our own image processor - src is image URL parameter
                            // create image URL parts
                            $img_src_parts = parse_url( $query_array[ 'src' ] );
                        }
                        $path_parts = explode( '.', $img_src_parts[ 'path' ] );
                        $suffix = end( $path_parts );
                        if ( ( 0 != strcasecmp( 'php', $suffix ) ) && ( 0 == strpos( $img_src_parts[ 'path' ], $uploaddir_parts[ 'path' ] ) ) )
                        {
                            // file is not a php file in our upload directory
                            $img_file_alias = str_replace( $uploaddir_parts[ 'path' ], '', $img_src_parts[ 'path' ] );
                            $img_file_name = $uploaddir[ 'basedir' ] . $img_file_alias;
                            // replace
                            $image->setAttribute( 'src', $img_file_alias );
                            // push into result array
                            if ( !array_key_exists( $img_file_alias, $result ) )
                                $result[ $img_file_alias ] = $img_file_name;
                        }
                    }
                }
            }
            // convert DOM back to HTML string
            $html_body = $html_DOM->saveHTML();
        }
        catch ( \Exception $e )
        {
            self::logme( 'Error scanning for HTML images: ' . $e->getMessage() );
            // todo: error message to frontend
        }
        libxml_clear_errors();
        \CRM_WpCiviMosaico_Utils::logme( "Exiting scanHTMLforImages" );
        return $result;
    }

    public static function doEmbed( $message )
    {
        $embedImages = CRM_Core_BAO_Setting::getItem( 'WP Civi Mosaico Preferences', 'wp_civi_mosaico_embed_images' );

        if ( $embedImages )
        {
            $html_body = $message->getHTMLBody();
            $image_array = self::scanHTMLforImages( $html_body );
            if ( !empty( $image_array ) )
            {
                $message->setHTMLBody( $html_body );
                foreach ( $image_array as $img_file_alias => $img_file_name )
                {
                    $img_mime = mime_content_type( $img_file_name );
                    if ( false === $img_mime )
                        $img_mime = 'application/octet-stream';
                    $message->addHTMLImage( $img_file_name, $img_mime, $img_file_alias );
                }
            }
        }
        return $message;
    }
}

?>
