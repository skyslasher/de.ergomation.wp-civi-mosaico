<?php

// override Mosaico Editor.php

class CRM_WpCiviMosaico_Page_Editor extends CRM_Mosaico_Page_Editor
{

  const DEFAULT_MODULE_WEIGHT = 200;

    public function run()
    {
        $smarty = CRM_Core_Smarty::singleton();
        $smarty->assign( 'baseUrl', CRM_Mosaico_Utils::getMosaicoDistUrl( 'relative' ) );
        $smarty->assign( 'scriptUrls', $this->getScriptUrls() );
        $smarty->assign( 'styleUrls', $this->getStyleUrls() );
        $smarty->assign( 'mosaicoPlugins', $this->getMosaicoPlugins() );
        $smarty->assign( 'mosaicoConfig', json_encode(
            $this->createMosaicoConfig(),
            defined( 'JSON_PRETTY_PRINT' ) ? JSON_PRETTY_PRINT : 0
        ) );
        echo $smarty->fetch( self::getTemplateFileName() );
        CRM_Utils_System::civiExit();
    }

    protected function getScriptUrls()
    {
        $scripts = parent::getScriptUrls();

        // Allow scripts to be added by a hook
        if ( class_exists( '\Civi\Core\Event\GenericHookEvent') )
        {
          \Civi::dispatcher()->dispatch(
              'hook_civicrm_mosaicoScripts',
              \Civi\Core\Event\GenericHookEvent::create(
                  array( 'scripts' => &$scripts )
              )
          );
        }
        return $scripts;
    }

    protected function getStyleUrls()
    {
        $styles = parent::getStyleUrls();

        // Allow styles to be added by a hook
        if ( class_exists( '\Civi\Core\Event\GenericHookEvent') )
        {
          \Civi::dispatcher()->dispatch(
              'hook_civicrm_mosaicoStyles',
              \Civi\Core\Event\GenericHookEvent::create(
                  array( 'scripts' => &$styles )
              )
          );
        }
        return $styles;
    }

    protected function getMosaicoPlugins()
    {
        $plugins = [];

        // Allow plugins to be added by a hook
        if ( class_exists( '\Civi\Core\Event\GenericHookEvent') )
        {
          \Civi::dispatcher()->dispatch(
              'hook_civicrm_mosaicoPlugins',
              \Civi\Core\Event\GenericHookEvent::create(
                  array( 'scripts' => &$plugins )
              )
          );
        }
        return '[ ' . implode( ',', $plugins ) . ' ]';
    }
}

?>
