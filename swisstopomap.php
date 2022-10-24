<?php
/***********************************************
* File      :   swisstopomap.php
* Project   :   piwigo_swisstopo
* Descr     :   Display a world map
*
* Created   :   28.05.2013
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

if ( !defined('PHPWG_ROOT_PATH') )
  define('PHPWG_ROOT_PATH','../../');

include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
include_once( PHPWG_ROOT_PATH.'admin/include/functions.php' );
include_once( dirname(__FILE__) .'/include/functions.php');
include_once( dirname(__FILE__) .'/include/functions_map.php');

check_status(ACCESS_GUEST);

swisstopo_load_language();
load_language('plugin.lang', SWISSTOPO_PATH);

$section = '';
if ( $conf['question_mark_in_urls']==false and isset($_SERVER["PATH_INFO"]) and !empty($_SERVER["PATH_INFO"]) )
{
	$section = $_SERVER["PATH_INFO"];
	$section = str_replace('//', '/', $section);
	$path_count = count( explode('/', $section) );
	$page['root_path'] = PHPWG_ROOT_PATH.str_repeat('../', $path_count-1);
	if ( strncmp($page['root_path'], './', 2) == 0 )
	{
		$page['root_path'] = substr($page['root_path'], 2);
	}
}
else
{
	foreach ($_GET as $key=>$value)
	{
		if (!strlen($value)) $section=$key;
		break;
	}
}

// deleting first "/" if displayed
$tokens = explode('/', preg_replace('#^/#', '', $section));
$next_token = 0;
$result = swisstopo_parse_map_data_url($tokens, $next_token);
$page = array_merge( $page, $result );

if (isset($page['category']))
	check_restrictions($page['category']['id']);

/* If the config include parameters get them */
$zoom = isset($conf['swisstopo_conf']['left_menu']['zoom']) ? $conf['swisstopo_conf']['left_menu']['zoom'] : 2;
$center = isset($conf['swisstopo_conf']['left_menu']['center']) ? $conf['swisstopo_conf']['left_menu']['center'] : '0,0';
$center_arr = preg_split('/,/', $center);
$center_lat = isset($center_arr) ? $center_arr[0] : 0;
$center_lng = isset($center_arr) ? $center_arr[1] : 0;

/* If we have zoom and center coordonate, set it otherwise fallback default */
$zoom = isset($_GET['zoom']) ? $_GET['zoom'] : $zoom;
$center_lat = isset($_GET['center_lat']) ? $_GET['center_lat'] : $center_lat;
$center_lng = isset($_GET['center_lng']) ? $_GET['center_lng'] : $center_lng;

$local_conf = array();
$local_conf['zoom'] = $zoom;
$local_conf['center_lat'] = $center_lat;
$local_conf['center_lng'] = $center_lng;
$local_conf['contextmenu'] = 'false';
$local_conf['control'] = true;
$local_conf['img_popup'] = false;
$local_conf['paths'] = swisstopo_get_gps($page);
$local_conf = $local_conf + $conf['swisstopo_conf']['map'] + $conf['swisstopo_conf']['left_menu'];

$js_data = swisstopo_get_items($page);
$js = swisstopo_get_js($conf, $local_conf, $js_data);
swisstopo_gen_template($conf, $js, $js_data, 'swisstopo-map.tpl', $template);
?>