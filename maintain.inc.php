<?php
if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

class maintenance_web_services_maintain extends PluginMaintain
{
  function __construct($plugin_id)
  {
    parent::__construct($plugin_id);
  }

  function install($plugin_version, &$errors=array())
  {
  }

  function activate($plugin_version, &$errors=array())
  {
  }

  function update($old_version, $new_version, &$errors=array())
  {
    $this->install($new_version, $errors);
  }
  
  function deactivate()
  {
  }

  function uninstall()
  {
  }
}
?>

