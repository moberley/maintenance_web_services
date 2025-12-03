<?php
if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

function moberley_maintws_get_derivative_types() {
  // from admin/maintenance_actions.php (except skip 'all' as not needed here)
  // latest commit: ad88ed9 
  $purge_urls = [];
  foreach (ImageStdParams::get_defined_type_map() as $params)
  {
    $purge_urls[ l10n($params->type) ] = $params->type;
  }
  $purge_urls[ l10n(IMG_CUSTOM) ] = IMG_CUSTOM;

  return $purge_urls;
}

?>
