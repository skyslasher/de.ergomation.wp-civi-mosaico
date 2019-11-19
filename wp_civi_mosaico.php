<?php

require_once 'wp_civi_mosaico.civix.php';
require_once 'CRM/WpCiviMosaico/Utils.php';
require_once 'CRM/WpCiviMosaico/EmbedImagesSender.php';

use CRM_WpCiviMosaico_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function wp_civi_mosaico_civicrm_config(&$config) {
  _wp_civi_mosaico_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function wp_civi_mosaico_civicrm_xmlMenu(&$files) {
  _wp_civi_mosaico_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function wp_civi_mosaico_civicrm_install() {
  _wp_civi_mosaico_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function wp_civi_mosaico_civicrm_postInstall() {
  _wp_civi_mosaico_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function wp_civi_mosaico_civicrm_uninstall() {
  _wp_civi_mosaico_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function wp_civi_mosaico_civicrm_enable() {
  // provide translations to WP PolyLang plugin if installed
  CRM_WpCiviMosaico_Utils::pll_register_string( 'Reading time:', 'Reading time:' );
  CRM_WpCiviMosaico_Utils::pll_register_string( 'minutes', 'minutes' );
  CRM_WpCiviMosaico_Utils::pll_register_string( 'minute', 'minute' );
  CRM_WpCiviMosaico_Utils::pll_register_string( 'Menu item', 'Wordpress <-> Mosaico integration settings' );
  CRM_WpCiviMosaico_Utils::pll_register_string( '4 digit year', '4 digit year: yyyy' );
  CRM_WpCiviMosaico_Utils::pll_register_string( 'Full date', 'Full date: dd.mm.yyyy' );

  _wp_civi_mosaico_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function wp_civi_mosaico_civicrm_disable() {
  _wp_civi_mosaico_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function wp_civi_mosaico_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _wp_civi_mosaico_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function wp_civi_mosaico_civicrm_managed(&$entities) {
  _wp_civi_mosaico_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function wp_civi_mosaico_civicrm_caseTypes(&$caseTypes) {
  _wp_civi_mosaico_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function wp_civi_mosaico_civicrm_angularModules(&$angularModules) {
  _wp_civi_mosaico_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function wp_civi_mosaico_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _wp_civi_mosaico_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function wp_civi_mosaico_civicrm_entityTypes(&$entityTypes) {
  _wp_civi_mosaico_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function wp_civi_mosaico_civicrm_themes(&$themes) {
  _wp_civi_mosaico_civix_civicrm_themes($themes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
function wp_civi_mosaico_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
function wp_civi_mosaico_civicrm_navigationMenu( &$menu )
{
    _wp_civi_mosaico_civix_insert_navigation_menu(
        $menu, 'Mailings', array(
            'label' => E::ts( CRM_WpCiviMosaico_Utils::__( 'Wordpress <-> Mosaico integration settings' ) ),
            'name' => 'wp_civi_mosaico_civicrm_settings',
            'url' => 'civicrm/admin/wp_civi_mosaico',
            'permission' => 'administer CiviCRM',
            'operator' => 'OR',
            'separator' => 0,
        )
    );
    _wp_civi_mosaico_civix_navigationMenu( $menu );
} // */

/*
 * hook into the send function to embed images
 */
function wp_civi_mosaico_civicrm_container( $container )
{
    $container->addResource( new \Symfony\Component\Config\Resource\FileResource( __FILE__ ) );
    $container->findDefinition( 'dispatcher' )->addMethodCall(
        'addListener',
        array( \Civi\FlexMailer\FlexMailer::EVENT_SEND, '_wp_civi_mosaico_send_batch' )
    );
}

function _wp_civi_mosaico_send_batch( \Civi\FlexMailer\Event\SendBatchEvent $event)
{
    $EmbedSender = new CRM_WpCiviMosaico_EmbedImagesSender();
    $EmbedSender->onSend( $event );
}

/*
 * add date tokens for CiviMail
*/

function wp_civi_mosaico_civicrm_tokens( &$tokens )
{
    $tokens[ 'date' ] = array(
        'date.date_short' => CRM_WpCiviMosaico_Utils::__( '4 digit year: yyyy' ),
        'date.date_med' => CRM_WpCiviMosaico_Utils::__( 'Full date: dd.mm.yyyy' )
    );
}

function wp_civi_mosaico_civicrm_tokenValues( &$values, $cids, $job = null, $tokens = array(), $context = null )
{
    // Date tokens
    if ( !empty( $tokens[ 'date' ] ) )
    {
        $date = array(
                'date.date_short' => date( 'Y' ),
                'date.date_med' => date( 'd.m.Y' ),
            );
        foreach ( $cids as $cid )
        {
            $values[ $cid ] = empty( $values[ $cid ] ) ? $date : $values[ $cid ] + $date;
        }
    }
}

/**
 * Implements hook_civicrm_check().
 */
function wp_civi_mosaico_civicrm_check( &$messages )
{
    // run only on Wordpress
    if ( !function_exists( 'has_post_format' ) )
    {
        $messages[] = new CRM_Utils_Check_Message(
            'wp_civi_mosaico',
            'This plugin only runs with Wordpress as a CMS for CiviCRM.',
            'No Wordpress installatino detected',
            \Psr\Log\LogLevel::CRITICAL,
            'fa-chain-broken'
        );
    }
    // Mosaico is needed
    try
    {
        $config = CRM_Mosaico_Utils::getConfig();
    }
    catch ( \Exception $e )
    {
        $messages[] = new CRM_Utils_Check_Message(
            'wp_civi_mosaico',
            'The <a href="https://github.com/veda-consulting/uk.co.vedaconsulting.mosaico" target="_blank">Mosaico</a> plugin is needed to activate and run this plugin.'
                . "<p><em>" . ts( "Error: %1", [ 1 => $e->getMessage() ] ) . "</em></p>",
            'Base plugin "Mosaico" not available',
            \Psr\Log\LogLevel::CRITICAL,
            'fa-chain-broken'
        );
    }
}

/**
 * Implementation of hook_civicrm_mosaicoConfig to modify the configuration
 */
function wp_civi_mosaico_civicrm_mosaicoConfig( &$config )
{
    // inject our image processor to serve the WP media gallery
    $config[ 'imgProcessorBackend' ] = CRM_WpCiviMosaico_Utils::getUrl( 'civicrm/wp_civi_mosaico/img', NULL, false );
    $config[ 'fileuploadConfig' ][ 'url' ] = CRM_WpCiviMosaico_Utils::getUrl( 'civicrm/wp_civi_mosaico/upload', NULL, false );
    // add bullet and numbered lists to the menu bar
    $config[ 'tinymceConfigFull' ][ 'toolbar1' ] = 'bold italic forecolor backcolor hr styleselect removeformat bullist numlist | civicrmtoken | link unlink | pastetext code';
}

/**
 * Implementation of hook_civicrm_mosaicoBaseTemplates to insert our enhanced versafix template
 */
function wp_civi_mosaico_civicrm_mosaicoBaseTemplates( &$templatesArray )
{
    $templatesDir = CRM_WpCiviMosaico_Utils::getPluginBaseDir() . 'mosaico-templates';
    $templatesUrl = CRM_WpCiviMosaico_Utils::getPluginBaseUrl() . 'mosaico-templates';

    foreach ( glob( "{$templatesDir}/*", GLOB_ONLYDIR ) as $dir )
    {
        $template = basename( $dir );
        $templateHTML = $templatesUrl . "/{$template}/template-{$template}.html";
        $templateThumbnail = $templatesUrl . "/{$template}/edres/_full.png";
        $templatesArray[ $template ] = array(
            'name' => $template,
            'title' => $template,
            'thumbnail' => $templateThumbnail,
            'path' => $templateHTML
        );
    }
}

/**
 * Implementation of hook_civicrm_mosaicoPlugins to modify the plugin list
 */
function wp_civi_mosaico_civicrm_mosaicoPlugins( &$plugins )
{
    // needed for our ajax function to return WP blogposts
    $plugins[] = 'function( vm ) { window.viewModel = vm; }';
    // the plugin itself
    $plugins[] = 'wordpressPostsWidgetPlugin';
}

/**
 * Implementation of hook_civicrm_mosaicoScripts to modify the list of loaded scripts
 */
function wp_civi_mosaico_civicrm_mosaicoScripts( &$scripts )
{
    $cacheCode = CRM_Core_Resources::singleton()->getCacheCode();
    $scripts[] = CRM_WpCiviMosaico_Utils::getPluginBaseUrl() . "js/wppostid.js?r=" . $cacheCode;
}

/**
 * Implementation of hook_civicrm_mosaicoStyles to modify the list of loaded stylesheets
 */
function wp_civi_mosaico_civicrm_mosaicoStyles( &$styles )
{
    $cacheCode = CRM_Core_Resources::singleton()->getCacheCode();
    $styles[] = CRM_WpCiviMosaico_Utils::getPluginBaseUrl() . "css/wppostid.css?r=" . $cacheCode;
}
