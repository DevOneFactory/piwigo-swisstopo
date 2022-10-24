<?php
/***********************************************
* File      :   category.inc.php
* Project   :   piwigo_swisstopo
* Descr     :   Display an SWISSTOPO map on the category layout
*
* Created   :   10.10.2014
*
* Copyright 2013-2016 <xbgmsharp@gmail.com>
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
************************************************/

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

// Do we have to show the right panel
if ($conf['swisstopo_conf']['category_description']['enabled'])
{
    // Hook to add comment
    add_event_handler('loc_begin_index', 'swisstopo_render_category');
    // Hook to show data after Thumbnails
    // Use only one trigger as we do have all the data need on first run trigger
    // add_event_handler('loc_end_index', 'swisstopo_render_category');
}

function swisstopo_render_category()
{
        global $template, $page, $conf, $filter;

        include_once( dirname(__FILE__) .'/include/functions.php');
        include_once( dirname(__FILE__) .'/include/functions_map.php');
        swisstopo_load_language();
        load_language('plugin.lang', SWISSTOPO_PATH);

        $js_data = swisstopo_get_items($page);
        if ($js_data != array())
        {
            $local_conf = array();
            $local_conf['contextmenu'] = 'false';
            $local_conf['control'] = true;
            $local_conf['img_popup'] = false;
            $local_conf['popup'] = 1;
            $local_conf['center_lat'] = 0;
            $local_conf['center_lng'] = 0;
            $local_conf['zoom'] = 2;
            $local_conf['autocenter'] = 1;
            $local_conf['paths'] = swisstopo_get_gps($page);
            $height = isset($conf['swisstopo_conf']['category_description']['height']) ? $conf['swisstopo_conf']['category_description']['height'] : '200';
            $width = isset($conf['swisstopo_conf']['category_description']['width']) ? $conf['swisstopo_conf']['category_description']['width'] : 'auto';
            $js = swisstopo_get_js($conf, $local_conf, $js_data);
            $template->set_filename('map', dirname(__FILE__).'/template/swisstopo-category.tpl' );
            $template->assign(
                array(
                    'CONTENT_ENCODING' => get_pwg_charset(),
                    'SWISSTOPO_PATH'         => embellish_url(get_gallery_home_url().SWISSTOPO_PATH),
                    'HOME'             => make_index_url(),
                    'HOME_PREV'        => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : get_absolute_root_url(),
                    'HOME_NAME'        => l10n("Home"),
                    'HOME_PREV_NAME'   => l10n("Previous"),
                    'SWISSTOPOJS'            => $js,
                    'HEIGHT'           => $height,
                    'WIDTH'            => $width,
                )
            );

            $swisstopo_content = $template->parse('map', true);
            //$swisstopo_content = '<div id="swisstopomap"><div class="map_title">'.l10n('EDIT_MAP').'</div>' . $swisstopo_content . '</div>';
            $index = isset($conf['swisstopo_conf']['category_description']['index']) ? $conf['swisstopo_conf']['category_description']['index'] : 0;
            // 0 - PLUGIN_INDEX_CONTENT_BEGIN
            // 1 - PLUGIN_INDEX_CONTENT_COMMENT
            // 2 - PLUGIN_INDEX_CONTENT_END
            if ($index <= 1)
            {
              // From index category comment at L300
              if ($page['start']==0 and !isset($page['chronology_field']) )
              {
                if (empty($page['comment']))
                   $page['comment'] = $swisstopo_content;
                else
                {
                  if ($index == 0)
                     $page['comment'] = '<div>' . $swisstopo_content . $page['comment'] .'</div>';
                  else
                     $page['comment'] = '<div>' . $page['comment'] . $swisstopo_content . '</div>';
                }
              }
            }
	    else
            {
              $swisstopo_content = '<div id="swisstopomap">'. $swisstopo_content . '</div>';
              $template->concat( 'PLUGIN_INDEX_CONTENT_END' , "\n".$swisstopo_content);
            }
        }
}
