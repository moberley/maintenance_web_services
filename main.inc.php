<?php
/*
Plugin Name: Maintenance Web Services
Version: 1.1
Description: Exposes maintenance actions to the web service API as GET endpoints.
Plugin URI: http://piwigo.org
Author: moberley
Author URI: https://github.com/moberley
Has Settings: false
 */

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

/* set plugin constants */
define('MOBERLEY_MAINTWS_PATH', PHPWG_PLUGINS_PATH.basename(dirname(__FILE__)).'/');
define('MOBERLEY_MAINTWS_WSPATH', MOBERLEY_MAINTWS_PATH.'include/functions_ws.inc.php');

include_once(MOBERLEY_MAINTWS_PATH.'include/functions.inc.php');

define('MOBERLEY_MAINTWS_TYPELIST', implode(", ", array_values(moberley_maintws_get_derivative_types())));

add_event_handler('init', 'moberley_maintws_plugin_init');
function moberley_maintws_plugin_init()
{
  load_language('plugin.lang', MOBERLEY_MAINTWS_PATH);
}

add_event_handler('ws_add_methods', 'moberley_maintws_add_methods', EVENT_HANDLER_PRIORITY_NEUTRAL);
function moberley_maintws_add_methods($arr)
{
  $service = &$arr[0];

  $service->addMethod(
    'maintenance_ws.gallery.lock',
    'moberley_maintws_lock_gallery',
    array(),
    l10n('Lock gallery'),
    MOBERLEY_MAINTWS_WSPATH,
    array(
      'hidden' => false,
      'admin_only' => true,
      'post_only' => false,
    )
  );

  $service->addMethod(
    'maintenance_ws.gallery.unlock',
    'moberley_maintws_unlock_gallery',
    array(),
    l10n('Unlock gallery'),
    MOBERLEY_MAINTWS_WSPATH,
    array(
      'hidden' => false,
      'admin_only' => true,
      'post_only' => false,
    )
  );

  $service->addMethod(
    'maintenance_ws.updateAlbumsInfo',
    'moberley_maintws_update_albums_info',
    array(),
    l10n('Update albums informations'),
    MOBERLEY_MAINTWS_WSPATH,
    array(
      'hidden' => false,
      'admin_only' => true,
      'post_only' => false,
    )
  );

  $service->addMethod(
    'maintenance_ws.updatePhotosInfo',
    'moberley_maintws_update_photos_info',
    array(),
    l10n('Update photos information'),
    MOBERLEY_MAINTWS_WSPATH,
    array(
      'hidden' => false,
      'admin_only' => true,
      'post_only' => false,
    )
  );

  $service->addMethod(
    'maintenance_ws.repairDatabase',
    'moberley_maintws_repair_database',
    array(),
    l10n('Repair and optimize database'),
    MOBERLEY_MAINTWS_WSPATH,
    array(
      'hidden' => false,
      'admin_only' => true,
      'post_only' => false,
    )
  );

  $service->addMethod(
    'maintenance_ws.reinitIntegrityCheck',
    'moberley_maintws_reinit_integrity_check',
    array(),
    l10n('Reinitialize check integrity'),
    MOBERLEY_MAINTWS_WSPATH,
    array(
      'hidden' => false,
      'admin_only' => true,
      'post_only' => false,
    )
  );

  $service->addMethod(
    'maintenance_ws.purge.userCache',
    'moberley_maintws_purge_user_cache',
    array(),
    l10n('Purge user cache'),
    MOBERLEY_MAINTWS_WSPATH,
    array(
      'hidden' => false,
      'admin_only' => true,
      'post_only' => false,
    )
  );

  $service->addMethod(
    'maintenance_ws.purge.orphanTags',
    'moberley_maintws_purge_orphan_tags',
    array(),
    l10n('Delete orphan tags'),
    MOBERLEY_MAINTWS_WSPATH,
    array(
      'hidden' => false,
      'admin_only' => true,
      'post_only' => false,
    )
  );

  $service->addMethod(
    'maintenance_ws.purge.historyDetails',
    'moberley_maintws_purge_history_details',
    array(),
    l10n('Purge history detail'),
    MOBERLEY_MAINTWS_WSPATH,
    array(
      'hidden' => false,
      'admin_only' => true,
      'post_only' => false,
    )
  );

  $service->addMethod(
    'maintenance_ws.purge.historySummary',
    'moberley_maintws_purge_history_summary',
    array(),
    l10n('Purge history summary'),
    MOBERLEY_MAINTWS_WSPATH,
    array(
      'hidden' => false,
      'admin_only' => true,
      'post_only' => false,
    )
  );

  $service->addMethod(
    'maintenance_ws.purge.sessions',
    'moberley_maintws_purge_sessions',
    array(),
    l10n('Purge sessions'),
    MOBERLEY_MAINTWS_WSPATH,
    array(
      'hidden' => false,
      'admin_only' => true,
      'post_only' => false,
    )
  );

  $service->addMethod(
    'maintenance_ws.purge.unusedFeeds',
    'moberley_maintws_purge_unused_feeds',
    array(),
    l10n('Purge never used notification feeds'),
    MOBERLEY_MAINTWS_WSPATH,
    array(
      'hidden' => false,
      'admin_only' => true,
      'post_only' => false,
    )
  );

  $service->addMethod(
    'maintenance_ws.purge.searchHistory',
    'moberley_maintws_purge_search_history',
    array(),
    l10n('Purge search history'),
    MOBERLEY_MAINTWS_WSPATH,
    array(
      'hidden' => false,
      'admin_only' => true,
      'post_only' => false,
    )
  );

  $service->addMethod(
    'maintenance_ws.purge.compiledTemplates',
    'moberley_maintws_purge_compiled_tpl',
    array(),
    l10n('Purge compiled templates'),
    MOBERLEY_MAINTWS_WSPATH,
    array(
      'hidden' => false,
      'admin_only' => true,
      'post_only' => false,
    )
  );

  $service->addMethod(
    'maintenance_ws.purge.derivatives',
    'moberley_maintws_purge_derivatives',
    array(
      'types' => array(
        'default' => null,
        'flags' => WS_PARAM_FORCE_ARRAY,
        'info' => MOBERLEY_MAINTWS_TYPELIST
      ),
    ),
    l10n('Delete multiple size images'),
    MOBERLEY_MAINTWS_WSPATH,
    array(
      'hidden' => false,
      'admin_only' => true,
      'post_only' => false,
    )
  );
}

?>
