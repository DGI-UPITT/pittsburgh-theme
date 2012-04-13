<?php

/**
 * @file islandora-solr-custom.tpl.php
 * Islandora solr search results template
 *
 * Variables available:
 * - $variables: all array elements of $variables can be used as a variable. e.g. $base_url equals $variables['base_url']
 * - $base_url: The base url of the current website. eg: http://example.com .
 * - $user: The user object.
 *
 * - $style: the style of the display ('div' or 'table'). Set in admin page by default. Overridden by the query value: ?display=foo
 * - $results: the array containing the solr search results
 * - $table_rendered: If the display style is set to 'table', this will contain the rendered table.
 *    For theme overriding, see: theme_islandora_solr_custom_table()
 * - $switch_rendered: The rendered switch to toggle between display styles
 *    For theme overriding, see: theme_islandora_solr_custom_switch()
 *
 */

 /*
<!--mods_title_ms ~ Title-->
<!--mods_topic_s ~ Topic-->
<!--mods_geographic_s ~ Location-->
<!--mods_publisher_s ~ Publisher-->
<!--mods_genre_s ~ Genre-->
<!--mods_subjectName_s ~ Subject-->
<!--mods_dateIssued_dt ~ Issued-->
<!--PID -> image-->
 */
print $switch_rendered;

if ($style == 'div') {
/*
  Basic layout:
  ol
    li (100%, some padding, border, alternating even/odd)
      div for thumbnail (fixed-width, vertically blocking, set minimum height with padding)
        img (thumbnail)
      div for rest (variable-width, height)
        foreach field (title (PAGE!?), creator):
        div (full width, fixed height?, borders)

  Total Field List:
    Title ~ mods_title_ms (: mods_subtitle_ms)
    Creator ~ mods_name_creator_ms
    Source Collection ~ mods_host_title_ms
    Type of Resource ~ mods_resource_type_ms
    Date ~ mods_dateOther_s
*/
print('<ol class="islandora_solr_results" start="'. $record_start .'">');
  if ($results == '') {
    print '<p>' . t('Your search yielded no results') . '</p>';
  }
  else {
    $z = 0;
    $zebra = 'even';

    $term = "";
    if (strstr($_SERVER["REQUEST_URI"], "dismax") === FALSE) {
      // advanced search
      $split = preg_split("[ AND | OR ]", $variables['results_raw']->responseHeader->params->q);
      foreach ($split as $field) {
        if (strpos($field, "ds_BOOKOCR") !== FALSE) {
          $term = substr($field, strpos($field, ":")+1);
          if (strpos($term, "(") == 0) {
            $term = substr($term, 1, strlen($term)-2);
          }
          break;
        }
      }
    }
    else {
      // simple search
      $term = $variables['results_raw']->responseHeader->params->q;
    }

    foreach ($results as $id => $result) {

      // if no page thumbnail exists, use the parent (book cover) one
      $is_page = ( strpos( $result['rels_hasModel_uri_ms']['value'], 'pageCModel' ) !== false && !empty($result['rels_isMemberOfCollection_uri_ms']['value'] ) );
      $item_title = $result['mods_title_ms']['value'];
      $link_url = url('fedora/repository/'.$result['PID']['value'].'/-/'.urlencode($item_title), array('query'=>array('solrq'=>$term)));
      $subtitle_value = ( empty( $result['mods_subTitle_ms']['value']) ? false : $result['mods_subTitle_ms']['value'] );
      $creator_value = ( empty( $result['mods_name_creator_ms']['value']) ? false : $result['mods_name_creator_ms']['value'] );
      $source_collection_value = ( empty( $result['mods_host_title_ms']['value']) ? false : $result['mods_host_title_ms']['value'] );
      $type_value = ( empty( $result['mods_resource_type_ms']['value']) ? false : $result['mods_resource_type_ms']['value'] );
      $date_value = ( empty( $result['mods_dateOther_s']['value']) ? false : $result['mods_dateOther_s']['value'] );
      $tn_url = $base_url."/fedora/repository/".$result['PID']['value']."/TN";
      $handle = @fopen($tn_url,'r');
      $thumbnail = '';

      if ($handle !== false) {
        // gravy
        $thumbnail = '<img src="'.$tn_url.'" title="' . $result['mods_title_ms']['value'] . '" alt="' . $result['mods_title_ms']['value'] . '" />';
        fclose($handle);
      }
      else {
        // look for, use, parent TN
        if ($is_page) {
          $thumbnail = '<img src="'.$base_url.'/fedora/repository/' .
                       substr( $result['rels_isMemberOfCollection_uri_ms']['value'], strlen('info:fedora/') ) .
                       '/TN" title="' . $result['mods_title_ms']['value'] . '" alt="' . $result['mods_title_ms']['value'] . '" />';
        }
        else {
          // finally, use "no image available"
          $thumbnail = '<img src="'.$base_url.'/'.drupal_get_path('theme','pittsburgh').'/images/not_available.png" alt="no image available" title="no image available"/>';
        }
      }

    $zebra = (($z % 2) ? 'odd' : 'even' );
    $z++;

    print (
    '<li class="islandora_solr_result '. $zebra .'">' .
      '<div class="solr-left">' .
        '<a href="'. $link_url .'">' .
          $thumbnail .
        '</a>' .
      '</div>' .
      '<div class="solr-right">' .
        '<div class="solr-field '. $result['mods_title_ms']['class'] .'">' .

          '<!--div class="label"><label>'. t($result['mods_title_ms']['label']) .'></label></div-->' .
          '<div class="value">' .
            '<a href="'. $link_url .'" title="'. $item_title .'">'. $item_title
          );
              if($subtitle_value) {
                print ': '.$subtitle_value;
              }
            print('</a>');
            if (!empty($result['rels_isPageNumber_literal_ms']['value'])) {
              print('<span class="solr-page-number '. $result['rels_isPageNumber_literal_ms']['class'] .'">p. '. $result['rels_isPageNumber_literal_ms']['value'] .'</span>');
            }
          print('</div>' .
        '</div>');
        if ($creator_value) {
        print('<div class="solr-field '. $result['mods_name_creator_ms']['class'] .'">' .
          '<div class="value"><label>'. t($result['mods_name_creator_ms']['label']) .'</label>'. $creator_value .'</div>' .
        '</div>');
        }
        if ($source_collection_value) {
        print('<div class="solr-field '. $result['mods_host_title_ms']['class'] .'">' .
          '<div class="value"><label>'. t($result['mods_host_title_ms']['label']) .'</label>'. $source_collection_value .'</div>' .
        '</div>');
        }
        if ($type_value) {
        print('<div class="solr-field '. $result['mods_resource_type_ms']['class'] .'">' .
          '<div class="value"><label>'. t($result['mods_resource_type_ms']['label']) .'</label>'. $type_value .'</div>' .
        '</div>');
        }
        if ($date_value) {
        print('<div class="solr-field '. $result['mods_dateOther_s']['class'] .'">' .
          '<div class="value"><label>'. t($result['mods_dateOther_s']['label']) .'</label>'. $date_value .'</div>' .
        '</div>');
        }

      print('</div>' .
      '<div class="solr-clear"></div>' .
    '</li>');
    }
  }
print('</ol>');

}
elseif ($style == 'table') {
  print $table_rendered;
}
