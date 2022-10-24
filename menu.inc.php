<?php
/***********************************************
* File      :   menu.inc.php
* Project   :   piwigo_swisstopo
* Descr     :   Display an SWISSTOPO map on mainmenu right
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

// Chech whether we are indeed included by Piwigo.
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

if ($conf['swisstopo_conf']['main_menu']['enabled'])
{
    add_event_handler('blockmanager_register_blocks', 'swisstopo_register_menu');
    add_event_handler('blockmanager_apply', 'swisstopo_apply_menu');
}
function swisstopo_register_menu( $menu_ref_arr )
{
    $menu = & $menu_ref_arr[0];
    if ($menu->get_id() != 'menubar')
        return;
    $menu->register_block( new RegisteredBlock( 'mbAbout', 'About', 'A1M'));
}

function swisstopo_apply_menu($menu_ref_arr)
{
    global $template, $page, $conf;

    $menu = & $menu_ref_arr[0];

    if (($block = $menu->get_block('mbLinks')) != null) {
        include_once( dirname(__FILE__) .'/include/functions.php');
        include_once(dirname(__FILE__).'/include/functions_map.php');
        swisstopo_load_language();
        load_language('plugin.lang', SWISSTOPO_PATH);

        // Comment are used only with this condition index.php l294
        if ($page['start']==0 and !isset($page['chronology_field']) )
        {
            $js_data = swisstopo_get_items($page);
            if ($js_data != array())
            {
                $local_conf = array();
                $local_conf['contextmenu'] = 'false';
                $local_conf['control'] = true;
                $local_conf['img_popup'] = false;
                $local_conf['popup'] = 2;
                $local_conf['center_lat'] = 0;
                $local_conf['center_lng'] = 0;
                $local_conf['zoom'] = 2;
                $local_conf['autocenter'] = 1;
                $local_conf['divname'] = 'mapmenu';
                $local_conf['paths'] = swisstopo_get_gps($page);
                $height = isset($conf['swisstopo_conf']['main_menu']['height']) ? $conf['swisstopo_conf']['main_menu']['height'] : '200';
                $js = swisstopo_get_js($conf, $local_conf, $js_data);
                $template->set_template_dir(dirname(__FILE__).'/template/');
                $template->assign(
                    array(
                        'SWISSTOPO_PATH' => embellish_url(get_gallery_home_url().SWISSTOPO_PATH),
                        'SWISSTOPOJS'    => $js,
                        'HEIGHT'   => $height,
                    )
                );
                $block->template = 'swisstopo-menu.tpl';
            }
        }
    }
}
?>
