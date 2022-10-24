<?php
/***********************************************
* File      :   admin_tag.php
* Project   :   piwigo_swisstopo
* Descr     :   Create tags from reverse address
*
* Created   :   06.07.2015
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

// Generate default value
$sync_options = array(
	'overwrite'		=> true,
	'simulate'		=> true,
	'cat_id'		=> 0,
	'subcats_included'	=> true,
	'swisstopo_tag_group'		=> 'location',
	'swisstopo_tag_address_suburb' => false,
	'swisstopo_tag_address_city_district' => false,
	'swisstopo_tag_address_city' => true,
	'swisstopo_tag_address_county' => false,
	'swisstopo_tag_address_state' => false,
	'swisstopo_tag_address_country' => false,
	'swisstopo_tag_address_postcode' => false,
	'swisstopo_tag_address_country_code' => false,
);

// Check if tag_groups is present and active
$query="SELECT COUNT(*) FROM ".PLUGINS_TABLE." WHERE `id`='tag_groups' AND `state`='active';";
list($tag_groups) = pwg_db_fetch_array( pwg_query($query) );
if ($tag_groups != 1) {
        $page['warnings'][] = "To use this feature you need the <a href='http://piwigo.org/ext/extension_view.php?eid=781' target='_blank'>tag_groups plugin</a> to be activate";
}

// On submit
if ( $tag_groups == 1 and isset($_POST['swisstopo_tag_submit']) )
{
	// Override default value from the form
	$tmp = preg_split("/_/",$_POST['language']);
	$sync_options = array(
		'overwrite' => isset($_POST['overwrite']),
		'simulate' => isset($_POST['simulate']),
		'cat_id' => isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 0,
		'subcats_included' => isset($_POST['subcats_included']),
		'swisstopo_tag_group'	=> $_POST['swisstopo_taggroup'],
		'swisstopo_tag_address_suburb' => isset($_POST['swisstopo_tag_address_suburb']),
		'swisstopo_tag_address_city_district' => isset($_POST['swisstopo_tag_address_city_district']),
		'swisstopo_tag_address_city' => isset($_POST['swisstopo_tag_address_city']),
		'swisstopo_tag_address_county' => isset($_POST['swisstopo_tag_address_county']),
		'swisstopo_tag_address_state' => isset($_POST['swisstopo_tag_address_state']),
		'swisstopo_tag_address_country' => isset($_POST['swisstopo_tag_address_country']),
		'swisstopo_tag_address_postcode' => isset($_POST['swisstopo_tag_address_postcode']),
		'swisstopo_tag_address_country_code' => isset($_POST['swisstopo_tag_address_country_code']),
		'language' => $tmp[0],
	);

	// TODO allow to filter on overwrite
	// Define files with lat and lon available
	define('SQL_EXIF', "`latitude` IS NOT NULL AND `longitude` is NOT NULL");
	if ( $sync_options['cat_id']!=0 )
	{
		$query=' SELECT id FROM '.CATEGORIES_TABLE.' WHERE ';

		if ( $sync_options['subcats_included'])
			$query .= 'uppercats REGEXP \'(^|,)'.$sync_options['cat_id'].'(,|$)\'';
		else
			$query .= 'id='.$sync_options['cat_id'];
			$cat_ids = array_from_query($query, 'id');

		$query='SELECT `id`, `name`, `latitude`, `longitude` FROM '.IMAGES_TABLE.' INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id=image_id
			WHERE '. SQL_EXIF .' AND category_id IN ('.implode(',', $cat_ids).')
			GROUP BY id';
	}
	else
	{
		$query='SELECT `id`, `name`, `latitude`, `longitude` FROM '.IMAGES_TABLE.' WHERE '. SQL_EXIF;
	}

	$images = hash_from_query( $query, 'id');
	$datas = array();
	$errors = array();
	$warnings = array();
	$infos = array();
	foreach ($images as $image)
	{
		// Fech reverse location from API
		// http://wiki.swisstopo.org/wiki/Nominatim
		// https://nominatim.swisstopo.org/reverse?format=xml&lat=51.082333&lon=10.366229&zoom=12
		// https://open.mapquestapi.com/nominatim/v1/reverse.php?format=xml&lat=48.858366666667&lon=2.2942166666667&zoom=12
		//$swisstopo_url = "https://nominatim.swisstopo.org/reverse?format=json&addressdetails=1&zoom=12&lat=". $image['latitude'] ."&lon=". $image['longitude'];
		//  As of Sept 2015 require a API KEY
		//$swisstopo_url = "https://open.mapquestapi.com/nominatim/v1/reverse.php?format=json&addressdetails=1&zoom=12&lat=". $image['latitude'] ."&lon=". $image['longitude'];
		//$swisstopo_url = "http://localhost:8443/api/". $image['latitude'] ."/". $image['longitude'];
		$swisstopo_url = "https://nominatim-xbgmsharp.rhcloud.com/api/". $image['latitude'] ."/". $image['longitude'] ."/". $sync_options['language'];
		//print $swisstopo_url ."<br/>";

		// Ensure we do have PHP curl install
		// Or should fallback to fopen
		if (function_exists('curl_init'))
		{
			// Get Curl resource
			$curl = curl_init();
			// Set some options http://wiki.swisstopo.org/wiki/Nominatim_usage_policy
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => $swisstopo_url,
				CURLOPT_USERAGENT => 'Piwigo SWISSTOPO alessandro.rossi@d1f.ch',
				CURLOPT_REFERER => 'http://piwigo.org/'
			));
			// Send the request & save response to $json
			$json = curl_exec($curl);
			// Close request to clear up some resources
			curl_close($curl);

		} else {
			// Curl module unavailable, use fopen
			$opts = array(
				'http'=>array(
					'method'=>"GET",
					'header'=>"User-Agent: Piwigo SWISSTOPO alessandro.rossi@d1f.ch\r\n"
					)
			);
			$context = stream_context_create($opts);
			if (false !== ($json = @file_get_contents($swisstopo_url, false, $context))) {
 				// all good
				//return $json;
			} else {
				// error happened
        			//return false;
				$errors[] = "Error fetching reverse data";
			}
		}

		if (!isset($json) or empty($json))
			$errors[] = "Error fetching geo reverse address data for ". $image['id'];
		else
			//var_dump(json_decode($json, true));
			$response = json_decode($json, true);
			//print_r($response);

		// If reponse include [address]
		if (isset($response) and isset($response['success']) and isset($response['success'][0]) and isset($response['success'][0]['result'])
			and isset($response['success'][0]['result']['address']) and is_array($response['success'][0]['result']['address']))
		{
			$response['address'] = $response['success'][0]['result']['address'];
			//print_r($response['address']);
			//print_r($sync_options);
			$tag_names = array();
			if (isset($response['address']['suburb']) and $sync_options['swisstopo_tag_address_suburb']) {
				array_push( $tag_names, $response['address']['suburb'] );
			}
			if (isset($response['address']['city_district']) and $sync_options['swisstopo_tag_address_city_district']) {
				array_push( $tag_names, $response['address']['city_district'] );
			}
			if (isset($response['address']['city']) and $sync_options['swisstopo_tag_address_city']) {
				array_push( $tag_names, $response['address']['city'] );
			}
			if (isset($response['address']['county']) and $sync_options['swisstopo_tag_address_county']) {
				array_push( $tag_names, $response['address']['county'] );
			}
			if (isset($response['address']['state']) and $sync_options['swisstopo_tag_address_state']) {
				array_push( $tag_names, $response['address']['state'] );
			}
			if (isset($response['address']['country']) and $sync_options['swisstopo_tag_address_country']) {
				array_push( $tag_names, $response['address']['country'] );
			}
			if (isset($response['address']['postcode']) and $sync_options['swisstopo_tag_address_postcode']) {
				array_push( $tag_names, $response['address']['postcode'] );
			}
			if (isset($response['address']['country_code']) and $sync_options['swisstopo_tag_address_country_code']) {
				array_push( $tag_names, $response['address']['country_code'] );
			}
			//print_r($tag_names);
			if (!empty($tag_names))
			{
				if (!$sync_options['simulate'])
				{
					/* Create tag */
					$tag_ids = array();
					foreach ($tag_names as $tag_name)
					{
						array_push( $tag_ids, tag_id_from_tag_name($sync_options['swisstopo_tag_group'].":".$tag_name) );
					}
					/* Assign tags to image */
					//print_r($tag_ids);
					if (!empty($tag_ids))
					{
						add_tags($tag_ids, array($image['id']));
					}
				}
				$datas[] = $image['id'];
				$infos[] = "Set tags '". swisstopo_pprint_r($tag_names) ."' for ". $image['name'];
			}
			else
			{
				$warnings = "No valid tags for ". $image['name'] . " available tag: ". swisstopo_pprint_r(array_keys($response['address']));
			}
		}
		//die("Done one image");
	} // Images loop

	// Send sync result to template
	$template->assign('sync_errors', $errors );
	$template->assign('sync_warnings', $warnings );
	$template->assign('sync_infos', $infos );

	// Send result to templates
	$template->assign(
		'metadata_result',
		array(
			'NB_ELEMENTS_DONE'		=> count($datas),
			'NB_ELEMENTS_CANDIDATES'	=> count($images),
			'NB_ERRORS'			=> count($errors),
			'NB_WARNINGS'			=> count($warnings),
		)
	);
}

$query = 'SELECT COUNT(*) FROM '.IMAGES_TABLE.' WHERE `latitude` IS NOT NULL and `longitude` IS NOT NULL ';
list($nb_geotagged) = pwg_db_fetch_array( pwg_query($query) );

$query = 'SELECT id, CONCAT(name, IF(dir IS NULL, " (V)", "") ) AS name, uppercats, global_rank  FROM '.CATEGORIES_TABLE;
display_select_cat_wrapper($query,
                           array( $sync_options['cat_id'] ),
                           'categories',
                           false);

// Send value to templates
$template->assign(
	array(
		'SUBCATS_INCLUDED_CHECKED' 	=> $sync_options['subcats_included'] ? 'checked="checked"' : '',
		'NB_GEOTAGGED' 			=> $nb_geotagged,
		'SWISSTOPO_PATH'			=> SWISSTOPO_PATH,
		'sync_options'			=> $sync_options,
		'language_options' 		=> get_languages(),
		'language_selected'		=> get_default_language(),
	)
);

?>
