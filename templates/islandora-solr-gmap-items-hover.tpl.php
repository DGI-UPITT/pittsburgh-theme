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
  print '<div id="islandora-solr-gmap-items-hover">';
  print '<img src="'. $base_url .'/fedora/repository/'. $marker_items[0]['PID'] .'/TN"/>'; // autofit this somehow
  print '</div>';
}
else {
  print t('No data found');
}
