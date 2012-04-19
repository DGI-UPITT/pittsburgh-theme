<?php

/**
 * @file islandora-solr-gmap-items.tpl.php
 * Islandora solr search marker items template
 *
 * Variables available:
 * - $variables: all array elements of $variables can be used as a variable. e.g. $base_url equals $variables['base_url']
 * - $base_url: The base url of the current website. eg: http://example.com .
 * - $user: The user object.
 *
 */

global $base_url;

if ($marker_items) {
  print '<div id="islandora-solr-gmap-items">';
  print '  <img src="'.$base_url.':8080/adore-djatoka/resolver?url_ver=Z39.88-2004&rft_id='.$base_url.'/fedora/repository/'.$marker_items[0]['PID'].'/JP2/&svc_id=info:lanl-repo/svc/getRegion&svc_val_fmt=info:ofi/fmt:kev:mtx:jpeg2000&svc.format=image/png&svc.level=3&svc.rotate=0">';
  print '</div>';
}
else {
  print t('No data found');
}
