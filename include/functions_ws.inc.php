<?php
if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

include_once(realpath(PHPWG_ROOT_PATH).'/admin/include/functions.php');

function moberley_maintws_deny() {
  return new PwgError(401, 'Access denied');
}

/* API function actions adapted from Piwigo .\admin\maintenance_actions.php 'actions' section (switch block) as there
 * generally do not seem to be single functions that encapsulate the entire process of each action on the maintenance
 * page that can be called from here.
 */

function moberley_maintws_lock_gallery($params, &$service)
{
  if (!is_webmaster()) { return moberley_maintws_deny(); }

  conf_update_param('gallery_locked', 'true');
  pwg_activity('system', ACTIVITY_SYSTEM_CORE, 'maintenance', array('maintenance_action'=>'lock_gallery'));

  // I had to choose between this string and 'The gallery is locked for maintenance. Please, come back later.' in
  // order to use only existing localized strings as there is no 'Gallery locked' string in core. Decided that 'come
  // back later' in an API response was more confusing.
  return l10n('A locked gallery is only visible to administrators');
}

function moberley_maintws_unlock_gallery($params, &$service)
{
  if (!is_webmaster()) { return moberley_maintws_deny(); }

  conf_update_param('gallery_locked', 'false');
  pwg_activity('system', ACTIVITY_SYSTEM_CORE, 'maintenance', array('maintenance_action'=>'unlock_gallery'));
  
  return l10n('Gallery unlocked');
}

function moberley_maintws_update_albums_info($params, &$service)
{
  if (!is_webmaster()) { return moberley_maintws_deny(); }

  images_integrity();
  categories_integrity();
  update_uppercats();
  update_category('all');
  update_global_rank();
  invalidate_user_cache(true);
  
  return sprintf('%s : %s', l10n('Update albums informations'), l10n('action successfully performed.'));
}

function moberley_maintws_update_photos_info($params, &$service)
{
  if (!is_webmaster()) { return moberley_maintws_deny(); }

  images_integrity();
  update_path();
	include_once(PHPWG_ROOT_PATH.'include/functions_rate.inc.php');
  update_rating_score();
  invalidate_user_cache();
  
  return sprintf('%s : %s', l10n('Update photos information'), l10n('action successfully performed.'));
}

function moberley_maintws_repair_database($params, &$service)
{
  if (!is_webmaster()) { return moberley_maintws_deny(); }

  do_maintenance_all_tables();

  return l10n('action successfully performed.');
}

function moberley_maintws_reinit_integrity_check($params, &$service)
{
  if (!is_webmaster()) { return moberley_maintws_deny(); }

  include_once(PHPWG_ROOT_PATH.'admin/include/check_integrity.class.php');
  $c13y = new check_integrity();
  $c13y->maintenance();
  
  return sprintf('%s : %s', l10n('Reinitialize check integrity'), l10n('action successfully performed.'));
}

function moberley_maintws_purge_user_cache($params, &$service)
{
  if (!is_webmaster()) { return moberley_maintws_deny(); }

  invalidate_user_cache();
  
  return sprintf('%s : %s', l10n('Purge user cache'), l10n('action successfully performed.'));
}

function moberley_maintws_purge_orphan_tags($params, &$service)
{
  if (!is_webmaster()) { return moberley_maintws_deny(); }

  delete_orphan_tags();
  
  return sprintf('%s : %s', l10n('Delete orphan tags'), l10n('action successfully performed.'));
}

function moberley_maintws_purge_history_details($params, &$service)
{
  if (!is_webmaster()) { return moberley_maintws_deny(); }

  $query = '
DELETE
  FROM '.HISTORY_TABLE.'
;';
  pwg_query($query);
  
  return sprintf('%s : %s', l10n('Purge history detail'), l10n('action successfully performed.'));
}

function moberley_maintws_purge_history_summary($params, &$service)
{
  if (!is_webmaster()) { return moberley_maintws_deny(); }

  $query = '
DELETE
  FROM '.HISTORY_SUMMARY_TABLE.'
;';
  pwg_query($query);
  
  return sprintf('%s : %s', l10n('Purge history summary'), l10n('action successfully performed.'));
}

function moberley_maintws_purge_sessions($params, &$service)
{
  if (!is_webmaster()) { return moberley_maintws_deny(); }

  global $conf;

  pwg_session_gc();

  // delete all sessions associated to invalid user ids (it should never happen)
  $query = '
SELECT
    id,
    data
  FROM '.SESSIONS_TABLE.'
;';
  $sessions = query2array($query);

  $query = '
SELECT
    '.$conf['user_fields']['id'].' AS id
  FROM '.USERS_TABLE.'
;';
  $all_user_ids = query2array($query, 'id', null);

  $sessions_to_delete = array();

  foreach ($sessions as $session)
  {
    if (preg_match('/pwg_uid\|i:(\d+);/', $session['data'], $matches))
    {
      if (!isset($all_user_ids[ $matches[1] ]))
      {
        $sessions_to_delete[] = $session['id'];
      }
    }
  }

  if (count($sessions_to_delete) > 0)
  {
    $query = '
DELETE
  FROM '.SESSIONS_TABLE.'
  WHERE id IN (\''.implode("','", $sessions_to_delete).'\')
;';
    pwg_query($query);
  }

  return sprintf('%s : %s', l10n('Purge sessions'), l10n('action successfully performed.'));
}

function moberley_maintws_purge_unused_feeds($params, &$service)
{
  if (!is_webmaster()) { return moberley_maintws_deny(); }

  $query = '
DELETE
  FROM '.USER_FEED_TABLE.'
  WHERE last_check IS NULL
;';
  pwg_query($query);
  
  return sprintf('%s : %s', l10n('Purge never used notification feeds'), l10n('action successfully performed.'));
}

function moberley_maintws_purge_search_history($params, &$service)
{
  if (!is_webmaster()) { return moberley_maintws_deny(); }

  $query = '
DELETE
  FROM '.SEARCH_TABLE.'
;';
  pwg_query($query);

  return sprintf('%s : %s', l10n('Purge search history'), l10n('action successfully performed.'));
}

function moberley_maintws_purge_compiled_tpl($params, &$service)
{
  if (!is_webmaster()) { return moberley_maintws_deny(); }

  global $template, $persistent_cache;

  $template->delete_compiled_templates();
  FileCombiner::clear_combined_files();
  $persistent_cache->purge(true);
  
  return sprintf('%s : %s', l10n('Purge compiled templates'), l10n('action successfully performed.'));
}

function moberley_maintws_purge_derivatives($params, &$service)
{
  if (!is_webmaster()) { return moberley_maintws_deny(); }

  if (!isset($params['types']) || $params['types'] == null)
  {
    clear_derivative_cache();
  } else {
    $valid_types = array_values(moberley_maintws_get_derivative_types());
    
    if (count(array_diff($params['types'], $valid_types)) > 0)
    {
      return new PwgError(WS_ERR_INVALID_PARAM, l10n('ERROR').' : '.l10n('Parameters'));
    }
    
    foreach ($params['types'] as $type_to_clear)
    {
      if (in_array($type_to_clear, $valid_types)) { clear_derivative_cache($type_to_clear); }
    }
  }
  
  return l10n('action successfully performed.');
}

?>
