<?php
/***********************************************
* File      :   admin_config.php
* Project   :   piwigo_swisstopo
* Descr     :   Install / Uninstall method
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

// Check whether we are indeed included by Piwigo.
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

// Check access and exit when user status is not ok
check_status(ACCESS_ADMINISTRATOR);

// Setup plugin Language
load_language('plugin.lang', SWISSTOPO_PATH);

// Fetch the template.
global $template, $conf, $lang;

// Available baselayer
$available_baselayer = array(
    'swisstopo'        => 'Swisstopo',
);

// Available zoom value
$available_zoom = array(
    '1' => '1',
    '2' => '2',
    '3' => '3',
    '4' => '4',
    '5' => '5',
    '6' => '6',
    '7' => '7',
    '8' => '8',
    '9' => '9',
    '10'=> '10',
    '11'=> '11',
    '12'=> '12',
    '13'=> '13',
    '14'=> '14',
    '15'=> '15',
    '16'=> '16',
    '17'=> '17',
    '18'=> '18',
);

// Available options
$available_add_before = array(
    'Author'    => l10n('Author'),
    'datecreate'=> l10n('Created on'),
    'datepost'  => l10n('Posted on'),
    'Dimensions'=> l10n('Dimensions'),
    'File'      => l10n('File'),
    'Filesize'  => l10n('Filesize'),
    'Tags'      => l10n('Tags'),
    'Categories'=> l10n('Albums'),
    'Visits'    => l10n('Visits'),
    'Average'   => l10n('Rating score'),
    'rating'    => l10n('Rate this photo'),
    'Privacy'   => l10n('Who can see this photo?'),
);

// Available options
// 0 - PLUGIN_INDEX_CONTENT_BEGIN
// 1 - PLUGIN_INDEX_CONTENT_COMMENT
// 2 - PLUGIN_INDEX_CONTENT_END
$available_cat_index = array(
    '0' => l10n('Thumbnail'),
    '1' => l10n('Description'),
    '2' => l10n('Last'),
);

// Available pin
$available_pin = array(
    '0' => l10n('NOPIN'),
    '1' => l10n('DEFAULTPIN'),
    '2' => l10n('DEFAULTPINGREEN'),
    '3' => l10n('DEFAULTPINRED'),
    '4' => l10n('LEAFPINGREEN'),
    '5' => l10n('LEAFPINORANGE'),
    '6' => l10n('LEAFPINRED'),
    '7' => l10n('MAPICONSBLEU'),
    '8' => l10n('MAPICONSGREEN'),
    '9' => l10n('OWNPIN'),
    '10'=> l10n('IMAGE'),
);

// Available popup
$available_popup = array(
    '0' => l10n('CLICK'),
//    '1' => l10n('ALWAYS'),
    '2' => l10n('NEVER'),
);

// Available layout value
$available_layout = array(
    '1' => 'swisstopo-map.tpl',
    '2' => 'swisstopo-map2.tpl',
    '3' => 'swisstopo-map3.tpl',
//    '4' => 'swisstopo-map4.tpl',
);

$query = 'SELECT COUNT(*) FROM '.IMAGES_TABLE.' WHERE `latitude` IS NOT NULL and `longitude` IS NOT NULL ';
list($nb_geotagged) = pwg_db_fetch_array( pwg_query($query) );

// Update conf if submitted in admin site
if (isset($_POST['submit']) && !empty($_POST['swisstopo_height']))
{
	// Check the center GPS position is valid
        $swisstopo_left_center = (isset($_POST['swisstopo_left_center']) and strlen($_POST['swisstopo_left_center']) != 0) ? $_POST['swisstopo_left_center'] : '0,0';
        $center_arr = explode(',', $swisstopo_left_center);
        //print_r($center_arr);
        $latitude = $center_arr[0];
        $longitude = $center_arr[1];
        if (isset($latitude) and isset($longitude))
            if ( strlen($latitude)==0 and strlen($longitude)==0 )
                array_push($page['warnings'], l10n('Both latitude/longitude must not empty'));
            if (isset($latitude) and ($latitude <= -90 or $latitude >= 90))
                array_push($page['warnings'], l10n('The specify center latitude (-90=S to 90=N) is not valid'));
            if (isset($longitude) and ($longitude <= -180 or $longitude >= 180))
                array_push($page['warnings'], l10n('The specify center longitude (-180=W to 180=E) is not valid'));

	// On post admin form
	$conf['swisstopo_conf'] = array(
	'right_panel' => array(
            'enabled'    => get_boolean($_POST['swisstopo_right_panel']),
            'add_before' => $_POST['swisstopo_add_before'],
            'height'     => $_POST['swisstopo_height'],
            'zoom'       => $_POST['swisstopo_zoom'],
            'link'       => $_POST['swisstopo_right_link'],
            'linkcss'    => $_POST['swisstopo_right_linkcss'],
            'showswisstopo'    => get_boolean($_POST['swisstopo_showswisstopo']),
			),
	'left_menu' => array(
            'enabled'           => get_boolean($_POST['swisstopo_left_menu']),
            'link'              => $_POST['swisstopo_left_link'],
            'popup'             => $_POST['swisstopo_left_popup'],
            'popupinfo_name'    => isset($_POST['swisstopo_left_popupinfo_name']),
            'popupinfo_img'     => isset($_POST['swisstopo_left_popupinfo_img']),
            'popupinfo_link'    => isset($_POST['swisstopo_left_popupinfo_link']),
            'popupinfo_comment' => isset($_POST['swisstopo_left_popupinfo_comment']),
            'popupinfo_author'  => isset($_POST['swisstopo_left_popupinfo_author']),
            'zoom'              => $_POST['swisstopo_left_zoom'],
            'center'            => $swisstopo_left_center,
            'autocenter'        => get_boolean($_POST['swisstopo_left_autocenter']),
            'layout'            => $_POST['swisstopo_left_layout'],
			),
        'category_description' => array(
            'enabled' => get_boolean($_POST['swisstopo_category_description']),
            'height'  => $_POST['swisstopo_cat_height'],
            'width'   => $_POST['swisstopo_cat_width'],
            'index'   => $_POST['swisstopo_cat_index'],
            ),
	'main_menu' => array(
            'enabled' => get_boolean($_POST['swisstopo_main_menu']),
            'height'  => $_POST['swisstopo_menu_height'],
            ),
        'gpx' => array(
            'height' => $_POST['swisstopo_gpx_height'],
            'width'  => $_POST['swisstopo_gpx_width'],
            ),
        'batch' => array(
            'global_height' => $_POST['swisstopo_batch_global_height'],
            'unit_height'  => $_POST['swisstopo_batch_unit_height'],
            ),
	'map' => array(
            'baselayer'          => $_POST['swisstopo_baselayer'],
            'custombaselayer'    => $_POST['swisstopo_custombaselayer'],
            'custombaselayerurl' => $_POST['swisstopo_custombaselayerurl'],
            'noworldwarp'        => get_boolean($_POST['swisstopo_noworldwarp']),
            'attrleaflet'        => get_boolean($_POST['swisstopo_attrleaflet']),
            'attrimagery'        => get_boolean($_POST['swisstopo_attrimagery']),
            'attrplugin'         => get_boolean($_POST['swisstopo_attrplugin']),
            'mapquestapi'        => $_POST['swisstopo_mapquestapi'],
            ),
	'pin' => array(
            'pin'            => $_POST['swisstopo_pin'],
            'pinpath'        => $_POST['swisstopo_pinpath'],
            'pinsize'        => $_POST['swisstopo_pinsize'],
            'pinshadowpath'  => $_POST['swisstopo_pinshadowpath'],
            'pinshadowsize'  => $_POST['swisstopo_pinshadowsize'],
            'pinoffset'      => $_POST['swisstopo_pinoffset'],
            'pinpopupoffset' => $_POST['swisstopo_pinpopupoffset'],
	    ),
	);

    // Update config to DB
    conf_update_param('swisstopo_conf', serialize($conf['swisstopo_conf']));

    // the prefilter changes, we must delete compiled templatess
    $template->delete_compiled_templates();
    array_push($page['infos'], l10n('Your configuration settings are saved'));
}

// send value to template
$template->assign($conf['swisstopo_conf']);
$template->assign(
    array(
        'AVAILABLE_ADD_BEFORE' => $available_add_before,
        'AVAILABLE_CAT_INDEX'  => $available_cat_index,
        'AVAILABLE_ZOOM'       => $available_zoom,
        'AVAILABLE_BASELAYER'  => $available_baselayer,
        'AVAILABLE_PIN'        => $available_pin,
        'AVAILABLE_POPUP'      => $available_popup,
        'AVAILABLE_LAYOUT'     => $available_layout,
        'NB_GEOTAGGED'         => $nb_geotagged,
        'SWISSTOPO_PATH'             => SWISSTOPO_PATH,
        'GLOBAL_MODE'          => l10n('global mode'),
        'SINGLE_MODE'          => l10n('unit mode'),
    )
);

?>
