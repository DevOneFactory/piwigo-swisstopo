<?php
/***********************************************
* File      :   picture.inc.php
* Project   :   piwigo_swisstopo
* Descr     :   Display map on right panel
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

include_once( dirname(__FILE__) .'/include/functions_map.php');
// Do we have to show the right panel
if ($conf['swisstopo_conf']['right_panel']['enabled'])
{
    // Hook to add the div in the right menu, No idea about the number!!
    add_event_handler('loc_begin_picture', 'swisstopo_loc_begin_picture', 56);

    // Hook to populate the div in the right menu, No idea about the number after!!
    add_event_handler('loc_begin_picture', 'swisstopo_render_element_content', EVENT_HANDLER_PRIORITY_NEUTRAL+1 /*in order to have picture content*/, 2);
}

function swisstopo_loc_begin_picture()
{
    global $template;
    $template->set_prefilter('picture', 'swisstopo_insert_map');
}

function swisstopo_insert_map($content)
{
    global $conf;
    load_language('plugin.lang', SWISSTOPO_PATH);

/*	Would be better if it could be like the Metdata but how?
	$search = '#<dl id="Metadata" class="imageInfoTable">#';
	$replacement = '
<dl id="map-info" class="imageInfoTable">
	<h3>{\'LOCATION\'|@translate}</h3>
	<div class="imageInfo">
		<div id="map"></div>
	</div>
</dl>
<dl id="Metadata" class="imageInfoTable">';
*/

    $search = '#<div id="'. $conf['swisstopo_conf']['right_panel']['add_before'] .'" class="imageInfo">#';
    $replacement = '
{if $SWISSTOPOJS}
<div id="map-info" class="imageInfo">
    <dt {$SWISSTOPONAMECSS}>{$SWISSTOPONAME}</dt>
    <dd>
        <div id="map"></div>
        <script type="text/javascript">{$SWISSTOPOJS}</script>
        <div id="swisstopo_attrib" style="visibility: hidden; display: none;">
            <ul>
                <li>{"PLUGIN_BY"|@translate}</li>
                <li><a href="http://leafletjs.com/" target="_blank">Leaflet</a></li>
                <li>&copy; {"SWISSTOPO_CONTRIBUTORS"|@translate}</li>
            </ul>
        </div>
        {if $SHOWSWISSTOPO}
        <a href="{$SWISSTOPOLINK}" target="_blank">{"VIEW_SWISSTOPO"|@translate}</a>
        {/if}
    </dd>
</div>
{/if}
<div id="'. $conf['swisstopo_conf']['right_panel']['add_before'] .'" class="imageInfo">';

    return preg_replace($search, $replacement, $content);
}

function swisstopo_render_element_content()
{
    global $template, $picture, $page, $conf;
    load_language('plugin.lang', SWISSTOPO_PATH);

    if (empty($page['image_id']))
    {
        return;
    }

    // Load coordinates from picture
    $query = 'SELECT latitude,longitude FROM '.IMAGES_TABLE.' WHERE id = \''.$page['image_id'].'\' ;';
    //FIXME LIMIT 1 ?
    $result = pwg_query($query);
    $row = pwg_db_fetch_assoc($result);
    if (!$row or !$row['latitude'] or empty($row['latitude']))
    {
        return;
    }
    $lat = $row['latitude'];
    $lon = $row['longitude'];

    // Load parameter, fallback to default if unset
    $height = isset($conf['swisstopo_conf']['right_panel']['height']) ? $conf['swisstopo_conf']['right_panel']['height'] : '200';
    $zoom = isset($conf['swisstopo_conf']['right_panel']['zoom']) ? $conf['swisstopo_conf']['right_panel']['zoom'] : '12';
    $swisstoponame = isset($conf['swisstopo_conf']['right_panel']['link']) ? $conf['swisstopo_conf']['right_panel']['link'] : 'Location';
    $swisstoponamecss = isset($conf['swisstopo_conf']['right_panel']['linkcss']) ? $conf['swisstopo_conf']['right_panel']['linkcss'] : '';
    $showswisstopo = isset($conf['swisstopo_conf']['right_panel']['showswisstopo']) ? $conf['swisstopo_conf']['right_panel']['showswisstopo'] : 'true';
    if (strlen($swisstoponamecss) != 0)
    {
        $swisstoponamecss = "style='".$swisstoponamecss."'";
    }
    $swisstopolink="https://swisstopo.org/?mlat=".$lat."&amp;mlon=".$lon."&zoom=12&layers=M";

    $local_conf = array();
    $local_conf['contextmenu'] = 'false';
    $local_conf['control'] = false;
    $local_conf['img_popup'] = false;
    $local_conf['popup'] = 2;
    $local_conf['center_lat'] = $lat;
    $local_conf['center_lng'] = $lon;
    $local_conf['zoom'] = $zoom;

    $js_data = swisstopo_get_items($page);

    $js = swisstopo_get_js($conf, $local_conf, $js_data);

    // Select the template
    $template->set_filenames(
            array('swisstopo_content' => dirname(__FILE__)."/template/swisstopo-picture.tpl")
    );

    // Assign the template variables
    $template->assign(
        array(
            'HEIGHT'		=> $height,
            'SWISSTOPOJS' 		=> $js,
            'SWISSTOPO_PATH'		=> embellish_url(get_gallery_home_url().SWISSTOPO_PATH),
            'SWISSTOPONAME'		=> $swisstoponame,
            'SWISSTOPONAMECSS'	=> $swisstoponamecss,
            'SHOWSWISSTOPO'		=> $showswisstopo,
            'SWISSTOPOLINK'		=> $swisstopolink,
        )
    );

    // Return the rendered html
    $swisstopo_content = $template->parse('swisstopo_content', true);
    return $swisstopo_content;
}
