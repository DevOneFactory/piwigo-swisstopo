<?php
/***********************************************
* File      :   gpx.inc.php
* Project   :   piwigo_swisstopo
* Descr     :   Display an SWISSTOPO map with elevation on GPX item
*
* Created   :   01.11.2014
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

// Add GPX support file extensions
array_push($conf['file_ext'], 'gpx');

// Hook on to an event to display videos as standard images
add_event_handler('render_element_content', 'swisstopo_render_media', EVENT_HANDLER_PRIORITY_NEUTRAL, 2);
function swisstopo_render_media($content, $picture)
{
	global $template, $picture, $conf;

	//print_r( $picture['current']);
	// do nothing if the current picture is actually an image !
	if ( (array_key_exists('src_image', @$picture['current'])
		&& @$picture['current']['src_image']->is_original()) )
	{
		return $content;
	}
	// If not a GPX file
	if ( (array_key_exists('path', @$picture['current']))
		&& strpos($picture['current']['path'],".gpx") === false)
	{
		return $content;
	}

	$filename = embellish_url(get_gallery_home_url() . $picture['current']['element_url']);
	$height = isset($conf['swisstopo_conf']['gpx']['height']) ? $conf['swisstopo_conf']['gpx']['height'] : '500';
	$width = isset($conf['swisstopo_conf']['gpx']['width']) ? $conf['swisstopo_conf']['gpx']['width'] : '320';

	$local_conf = array();
	$local_conf['contextmenu'] = 'false';
	$local_conf['control'] = true;
	$local_conf['img_popup'] = false;
	$local_conf['popup'] = 2;
	$local_conf['center_lat'] = 0;
	$local_conf['center_lng'] = 0;
	$local_conf['zoom'] = '12';
	$local_conf['divname'] = 'mapgpx';

	$js_data = array(array(null, null, null, null, null, null, null, null));

	$js = swisstopo_get_js($conf, $local_conf, $js_data);

	// Select the template
	$template->set_filenames(
            array('swisstopo_content' => dirname(__FILE__)."/template/swisstopo-gpx.tpl")
	);

	// Assign the template variables
	$template->assign(
        array(
			'HEIGHT'   => $height,
			'WIDTH'    => $width,
			'FILENAME' => $filename,
			'SWISSTOPO_PATH' => embellish_url(get_gallery_home_url().SWISSTOPO_PATH),
			'SWISSTOPOGPX'   => $js,
            )
	);

	// Return the rendered html
	$swisstopo_content = $template->parse('swisstopo_content', true);
	return $swisstopo_content;
}

// Hook to display a fallback thumbnail if not defined
add_event_handler('get_mimetype_location', 'swisstopo_get_mimetype_icon');
function swisstopo_get_mimetype_icon($location, $element_info)
{
	if ($element_info == 'gpx')
	{
		$location = 'plugins/'
			. basename(dirname(__FILE__))
			. '/mimetypes/'. $element_info. '.png';
	}
	return $location;
}

?>
