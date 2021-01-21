<?php


class CRM_WpCiviMosaico_Utils
{
    const DEFAULT_POSTS_COUNT = 10;

    public static function logme( $line )
    {
    	$dt = new DateTime();
    	file_put_contents( CRM_WpCiviMosaico_Utils::getPluginBaseDir() . '/log/wp_civi_mosaico.log',  "[" . $dt->format('Y-m-d\TH:i:s.u') . "] " . $line . "\n", FILE_APPEND | LOCK_EX );
    }
    public static function __( $string = '', $PostID = 0 )
    {
        if ( function_exists( 'pll__' ) )
        {
            if ( 0 != $PostID )
            {
              return pll_translate_string( $string, pll_get_post_language( $PostID ) );
            }
            return pll__( $string );
        }
        return $string;
    }
    public static function pll_register_string( $desc = '', $phrase = '' )
    {
        if ( function_exists( 'pll_register_string' ) )
        {
            pll_register_string( $desc, $phrase, 'WP Civi Mosaico', false );
        }
    }
    public static function getUrl( $path, $query, $frontend )
    {
        return CRM_Utils_System::url( $path, $query, true, NULL, false, $frontend );
    }
    public static function getPluginBaseDir()
    {
        return CRM_Core_Resources::singleton()->getPath( 'de.ergomation.wp-civi-mosaico' ) . '/';
    }
    public static function getPluginBaseUrl()
    {
        return CRM_Core_Resources::singleton()->getUrl( 'de.ergomation.wp-civi-mosaico' );
    }
    protected static function getAuthorArray( $author_id )
    {
        $email = get_the_author_meta( 'user_email', $author_id );
    	return array(
    		"author" => get_the_author_meta( 'display_name', $author_id ),
    		"url" => esc_url( get_author_posts_url( $author_id ) ),
    		"email" => $email,
            "image" => get_avatar_url( $author_id, array( "size" => 150, "height" => 150, "width" => 150, "default" => "blank" ) )
    	);
    }

    protected static function getAuthors( $PostID )
    {
    	$posting_authors = array();
    	if ( function_exists( 'coauthors_IDs' ) )
    	{
    		$posting_authors_Objects = get_coauthors( $PostID );
    		foreach( $posting_authors_Objects as $WP_User_author )
    		{
    			$posting_authors[] = CRM_WpCiviMosaico_Utils::getAuthorArray( $WP_User_author->ID );
    		}
    	}
    	else
    	{
			$WP_Post = get_post( $PostID );
			$author_id = $WP_Post->post_author;
    		$posting_authors[] = CRM_WpCiviMosaico_Utils::getAuthorArray( $author_id );
    	}
    	return $posting_authors;
    }

    protected static function getAuthorImages( $PostID )
    {
    	$posting_authors_IDs = array();
    	if ( function_exists( 'coauthors_IDs' ) )
    	{
    		$posting_authors_Objects = get_coauthors( $PostID );
    		foreach( $posting_authors_Objects as $WP_User_author )
    		{
    			$posting_authors_IDs[] = $WP_User_author->ID;
    		}
    	}
    	else
    	{
    		$author_id = get_post_field( 'post_author', $PostID );
    		$posting_authors_IDs[] = $author_id;
    	}
        foreach ( $posting_authors_IDs as $posting_authors_ID )
    	{
    		$result .= get_avatar( get_the_author_meta( 'user_email', $posting_authors_ID ), 150 );
    	}
    	return $result;
    }

    protected static function getAuthorFrom( $PostID )
    {
      return CRM_WpCiviMosaico_Utils::__( 'From', $PostID );
    }

    protected static function getAuthorConjunction( $PostID )
    {
      return CRM_WpCiviMosaico_Utils::__( 'and', $PostID );
    }

    protected static function getReadingTime( $PostID )
    {
        if ( shortcode_exists( 'rt_reading_time' ) )
        {
            $rt_string = CRM_WpCiviMosaico_Utils::__( 'Reading time:', $PostID );
            $rt_time = CRM_WpCiviMosaico_Utils::__( 'minutes', $PostID );
            $rt_time_singular = CRM_WpCiviMosaico_Utils::__( 'minute', $PostID );
            return ' (' . do_shortcode( '[rt_reading_time post_id="' . $PostID . '" label="' . $rt_string . '" postfix="' . $rt_time . '" postfix_singular="' . $rt_time_singular . '"]' ) . ')';
        }
        return '';
    }

    protected static function getReadingTimeCaption( $PostID )
    {
      return CRM_WpCiviMosaico_Utils::__( 'Continue reading', $PostID );
    }

    public static function getAjaxPosts()
    {
        try // exceptions in ajax-calls lead to strange 500 errors
        {
            $post_id = ( empty( $_REQUEST[ '$post_id' ] ) ) ? 0 : $_REQUEST[ '$post_id' ];
            $language = ( empty( $_REQUEST[ 'language' ] ) ) ? '' : $_REQUEST[ 'language' ];
            $page_num = ( empty( $_REQUEST[ 'page_num' ] ) ) ? 1 : $_REQUEST[ 'page_num' ];
            $post_status = ( empty( $_REQUEST[ 'post_status' ] ) ) ? 'any' : $_REQUEST[ 'post_status' ];
            if ( 0 != $post_id )
            {
                $num_pages = 1;
                $ajaxposts = array( get_post( $post_id, ARRAY_A ) );
            }
            else
            {
                $query_args = array (
                    'post_type' => 'post',
                    'posts_per_page' => CRM_WpCiviMosaico_Utils::DEFAULT_POSTS_COUNT,
                    'paged' => $page_num,
                    'post_status' => $post_status,
                );
                $custom_query = new WP_Query( $query_args );
                $num_pages = $custom_query->max_num_pages;
                $ajaxposts = $custom_query->get_posts();
            }
            foreach( $ajaxposts as $key => $value )
            {
                $PostID = $value->ID;
                // render shortcodes
                $ajaxposts[ $key ]->post_content = do_shortcode( $ajaxposts[ $key ]->post_content );
                // additional author info
                $ajaxposts[ $key ]->author_info = CRM_WpCiviMosaico_Utils::getAuthors( $PostID );
                $ajaxposts[ $key ]->author_images = CRM_WpCiviMosaico_Utils::getAuthorImages( $PostID );
                $ajaxposts[ $key ]->author_from = CRM_WpCiviMosaico_Utils::getAuthorFrom( $PostID );
                $ajaxposts[ $key ]->author_conjunction = CRM_WpCiviMosaico_Utils::getAuthorConjunction( $PostID );
                // reading time, if plugin exists
                $ajaxposts[ $key ]->reading_time = CRM_WpCiviMosaico_Utils::getReadingTime( $PostID );
                $ajaxposts[ $key ]->reading_time_caption = CRM_WpCiviMosaico_Utils::getReadingTimeCaption( $PostID );
                // featured image
                $ajaxposts[ $key ]->featured_image = get_the_post_thumbnail_url( $PostID, 'full' );
            }
            echo json_encode( [ "num_pages" => $num_pages, "posts" => $ajaxposts ]);
        }
        catch ( \Exception $e )
        {
            self::logme( 'Error in AJAX call to Wordpress: ' . $e->getMessage() );
            // todo: error message to frontend
            $http_return_code = 400;
            return;
        }
        CRM_Utils_System::civiExit();
    }

    public static function processUpload()
    {
        global $http_return_code;

        try // exceptions in ajax-calls lead to strange 500 errors
        {
            $files = array();

            if ( $_SERVER[ "REQUEST_METHOD" ] == "GET" )
            {
                // image list request
                $query_images_args = array(
                    'post_type'      => 'attachment',
                    'post_mime_type' => 'image',
                    'post_status'    => 'inherit',
                    'posts_per_page' => - 1,
                );

                $query_images = new WP_Query( $query_images_args );

                foreach ( $query_images->posts as $image )
                {
                    $file = array(
                        "name" => "",
                        "url" => wp_get_attachment_url( $image->ID ),
                        "size" => 123456,
                        "thumbnailUrl" => wp_get_attachment_image_src( $image->ID, 'thumbnail' )[ 0 ]
                    );
                    $files[] = $file;
                }
            }
            elseif ( !empty( $_FILES ) )
            {
                // image upload. only one image a time, more will come in sequencially
                foreach ( $_FILES[ 'files' ][ 'name' ] as $key => $value )
                {
                    if ( $_FILES[ 'files' ][ 'error' ][ $key ] == UPLOAD_ERR_OK )
                    {
                        $uploadedfile = array(
                            'name'     => $_FILES[ 'files' ][ 'name' ][ $key ],
                            'type'     => $_FILES[ 'files' ][ 'type' ][ $key ],
                            'tmp_name' => $_FILES[ 'files' ][ 'tmp_name' ][ $key ],
                            'error'    => $_FILES[ 'files' ][ 'error' ][ $key ],
                            'size'     => $_FILES[ 'files' ][ 'size' ][ $key ]
                        );
                        $result = wp_handle_upload( $uploadedfile, array( 'test_form' => false ) );
                        if ( $result && !isset( $result[ 'error' ] ) )
                        {
                            require_once( ABSPATH . 'wp-admin/includes/image.php' );
                            require_once( ABSPATH . 'wp-admin/includes/file.php' );

                            // create attachment post, so the file shows up in the media library
                        	$attachment = array(
                        		"guid" => $result[ 'file' ],
                        		"post_mime_type" => $result[ 'type' ],
                        		"post_title" => 'Mosaico upload - ' . $uploadedfile[ 'name' ],
                        		"post_content" => "",
                        		"post_status" => "draft",
                        		"post_author" => 1
                        	);
                        	$attachment_id = wp_insert_attachment( $attachment, $result[ 'file' ], 0 );
                        	$attachment_data = wp_generate_attachment_metadata( $attachment_id, $result[ 'file' ] );
                        	wp_update_attachment_metadata( $attachment_id, $attachment_data );

                            // push upload to return array
                            $file = array(
                                "name" => $uploadedfile[ 'name' ],
                                "url" => $result[ 'url' ],
                                "size" => $uploadedfile[ 'size' ]
                            );
                            $files[] = $file;
                        }
                        else
                        {
                            $http_return_code = 400;
                            return;
                        }
                    }
                    else
                    {
                        $http_return_code = 400;
                        return;
                    }
                }
            }
            else
            {
                $http_return_code = 400;
                return;
            }

            header( "Content-Type: application/json; charset=utf-8" );
            header( "Connection: close" );

            echo json_encode( array( "files" => $files ) );
      }
      catch ( \Exception $e )
      {
          self::logme( 'Error uploading file: ' . $e->getMessage() );
          // todo: error message to frontend
          $http_return_code = 400;
          return;
      }
      CRM_Utils_System::civiExit();
    }

    /**
     * handler for img requests
     */
    public static function processImg()
    {
        try // exceptions in ajax-calls lead to strange 500 errors
        {
            $config = CRM_Mosaico_Utils::getConfig();
            $methods = [ 'placeholder', 'resize', 'cover' ];
            if ( $_SERVER[ "REQUEST_METHOD" ] == "GET" )
            {
                $method = CRM_Utils_Array::value( 'method', $_GET, 'cover' );
                if ( !in_array( $method, $methods ) )
                {
                    $method = 'cover';
                }

                $params = explode( ",", $_GET[ "params" ] );
                $width = ( int ) $params[ 0 ];
                $height = ( int ) $params[ 1 ];

                // Apply a sensible maximum size for images in an email
                if ( $width * $height > CRM_Mosaico_Utils::MAX_IMAGE_PIXELS )
                {
                    throw new \Exception( "The requested image size is too large" );
                }

                switch ( $method )
                {
                    case 'placeholder':
                        // Only privileged users can request generation of placeholders
                        if ( !CRM_Core_Permission::check( [ [ 'access CiviMail', 'create mailings', 'edit message templates' ] ] ) )
                        {
                            CRM_Utils_System::permissionDenied();
                        }

                        Civi::service( 'mosaico_graphics' )->sendPlaceholder( $width, $height );
                        break;

                    case 'resize':
                    case 'cover':
                        $func = ( $method === 'resize' ) ? 'createResizedImage' : 'createCoveredImage';

                        $path_parts = pathinfo( $_GET[ "src" ] );
                        // src can be outside the uploads dir, or any URL.
                        // Imagick accepts URLs, so we just leave it as is
                        $src_file = $_GET[ "src" ];
                        $cache_file = $config[ 'BASE_DIR' ] . $config[ 'STATIC_DIR' ] . $path_parts[ "basename" ];
                        // $cache_file = $config['BASE_DIR'] . $config['STATIC_DIR'] . $method . '-' . $width . "x" . $height . '-' . $path_parts["basename"];
                        // The current naming convention for cache-files is buggy because it means that all variants
                        // of the basename *must* have the same size, which breaks scenarios for re-using images
                        // from the gallery. However, to fix it, one must also fix CRM_Mosaico_ImageFilter.

                        if ( !file_exists( $cache_file ) )
                        {
                            Civi::service( 'mosaico_graphics' )->$func( $src_file, $cache_file, $width, $height );
                        }
                        CRM_Mosaico_Utils::sendImage( $cache_file );
                        break;
                }
            }
        }
        catch ( \Exception $e )
        {
            self::logme( 'Error processing image: ' . $e->getMessage() );
            // todo: error message to frontend
            $http_return_code = 400;
            return;
        }
        CRM_Utils_System::civiExit();
    }
}

?>
