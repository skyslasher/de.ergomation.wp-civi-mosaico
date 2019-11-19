<?php

use CRM_WpCiviMosaico_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_WpCiviMosaico_Form_Settings extends CRM_Admin_Form_Setting
{

  protected $_settings = array(
    'wp_civi_mosaico_embed_images' => 'Embed images into mail'
  );

  /**
   * Build the form object.
   */
  public function buildQuickForm()
  {
    parent::buildQuickForm();
  }

  public function postProcess()
  {
    $formValues = $this->controller->exportValues( $this->_name );
    Civi::settings()->set(
        'wp_civi_mosaico_embed_images',
        ( !empty( $formValues[ 'wp_civi_mosaico_embed_images' ] ) )
    );
  }
}
