<!DOCTYPE html>
<html>
{html_head}
<meta http-equiv="content-type" content="text/html; charset={$CONTENT_ENCODING}" />
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<meta name="robots" content="noindex,nofollow" />
<title>{$GALLERY_TITLE}</title>
<link rel="stylesheet" href="{$SWISSTOPO_PATH}fontello/css/swisstopo.css" />
<link rel="stylesheet" href="{$SWISSTOPO_PATH}leaflet/leaflet.css" />
<link rel="stylesheet" href="{$SWISSTOPO_PATH}leaflet/leaflet-search.min.css" />
<link rel="stylesheet" href="{$SWISSTOPO_PATH}leaflet/MarkerCluster.css" />
<link rel="stylesheet" href="{$SWISSTOPO_PATH}leaflet/MarkerCluster.Default.css" />
<link rel="stylesheet" href="{$SWISSTOPO_PATH}leaflet/leaflet.contextmenu.css" />
<link rel="stylesheet" href="{$SWISSTOPO_PATH}leaflet/Control.MiniMap.css" />
<link rel="stylesheet" href="{$SWISSTOPO_PATH}leaflet/iconLayers.css" />
<script src="{$SWISSTOPO_PATH}leaflet/leaflet.js"></script>
<script src="{$SWISSTOPO_PATH}leaflet/leaflet-search.min.js"></script>
<script src="{$SWISSTOPO_PATH}leaflet/leaflet.markercluster.js"></script>
<script src="{$SWISSTOPO_PATH}leaflet/leaflet.contextmenu.js"></script>
<script src="{$SWISSTOPO_PATH}leaflet/leaflet-omnivore.min.js"></script>
<script src="{$SWISSTOPO_PATH}leaflet/Control.MiniMap.js"></script>
<script src="{$SWISSTOPO_PATH}leaflet/L.Control.ControlCenter.js"></script>
<script src="{$SWISSTOPO_PATH}leaflet/iconLayers.js"></script>
{html_style}
{literal}
html, body {
	height: 100%;
	width: 100%;
	margin: 0;
	padding: 0;
}

#map {
	position: absolute;
	top:0;
	left:0;
	right:0;
	bottom:0;
}

#content {
	position: absolute;
	bottom: 110px;
	left:0;
	right:0;
	height: 60px;
	z-index: 10;
	background-color: rgba(0,0,0,0.5);
}

#dialog {
	font-family: Arial,Helvetica,sans-serif;
	font-size: 10px;
	text-align: center;
	text-decoration: none;
}
{/literal}{/html_style}
{/html_head}
</head>
<body>
<noscript>Your browser must have JavaScript enable</noscript> 

<div id="map"></div>

<div id="dialog" title="Link to this map">
	<p>Copy and Paste the URL below:</p>
	<input type="text" value="" style="width: 550px;" onfocus="this.select();" id="textfield"></input>
</div>

<script type="text/javascript">{$SWISSTOPOJS}</script>

<script type="text/javascript">
{literal}

	/* Load leaflet PWG-SWISSTOPO ControlCenter Leaflet plugin */
	map.addControl( new L.Control.ControlCenter() );

	/* BEGIN leaflet-MiniMap https://github.com/Norkart/Leaflet-MiniMap */
	var swisstopo2 = new L.TileLayer(Url, {minZoom: 0, maxZoom: 13, attribution: Attribution});
	var miniMap = new L.Control.MiniMap(swisstopo2).addTo(map);
	/* END leaflet-MiniMap */

	/* BEGIN leaflet-search https://github.com/stefanocudini/leaflet-search */
	var jsonpurl = 'https://open.mapquestapi.com/nominatim/v1/search.php?q={s}'+
				   '&format=json&swisstopo_type=N&limit=100&addressdetails=0',
		jsonpName = 'json_callback';
	//third party jsonp service

	function filterJSONCall(rawjson) {	//callback that remap fields name
		var json = {},
			key, loc, disp = [];

		for(var i in rawjson)
		{
			disp = rawjson[i].display_name.split(',');
			key = disp[0] +', '+ disp[1];
			loc = L.latLng( rawjson[i].lat, rawjson[i].lon );
			json[ key ]= loc;	//key,value format
		}

		return json;
	}

	var searchOpts = {
			url: jsonpurl,
			jsonpParam: jsonpName,
			filterJSON: filterJSONCall,
			animateLocation: true,
			circleLocation: false,
			markerLocation: false,
			zoom: 12,
			minLength: 3,
			autoType: false,
		};

	map.addControl( new L.Control.Search(searchOpts) );
	/* END leaflet-search */

	/* https://github.com/codeforamerica/lv-trucks-map/blob/master/js/main.js */
	/* http://clvfoodtrucks.com/ */
	L.Map.prototype.panToOffset = function (latlng, offset, options) {
	  var x = this.latLngToContainerPoint(latlng).x - offset[0]
	  var y = this.latLngToContainerPoint(latlng).y - offset[1]
	  var point = this.containerPointToLatLng([x, y])
	  return this.setView(point, this._zoom, { pan: options })
	}

	/* BEGIN leaflet-contextmenu https://github.com/aratcliffe/Leaflet.contextmenu */
	function goHome (){
		window.location.assign('{/literal}{$HOME}{literal}');
	}

	function goBack (){
		window.location.assign('{/literal}{$HOME_PREV}{literal}');
	}

	function showCoordinates (e) {
		var popup = L.popup();
		popup
			.setLatLng(e.latlng)
			.setContent("You clicked the map at " + e.latlng.toString())
			.openOn(map);
	}

	function centerMap (e) {
		map.panTo(e.latlng);
		//getMarkers(); /* Center on Map is not consider as Move so we have to update the data ourself */
	}

	function goShowAll (e) {
		/* Get coordonates */
		var bounds = map.getBounds();
		var min = bounds.getSouthWest().wrap();
		var max = bounds.getNorthEast().wrap();

		/* Update ShowAll link */
		var root_url = '{/literal}{$MYROOT_URL}{literal}';
		var myurl = root_url+"swisstopomap.php?min_lat="+min.lat+"&min_lng="+min.lng+"&max_lat="+max.lat+"&max_lng="+max.lng;
		//console.log("ShowAll:"+myurl);
		window.open(myurl,'_blank');
	}

	function linkToThisMap (){
		var center = map.getCenter();
		var zoom = map.getZoom();

		var centerlat = center.lat;
		var centerlng = center.lng;

		var root_url = '{/literal}{$MYROOT_URL}{literal}';
		var myurl = root_url+"swisstopomap.php?zoom="+zoom+"&center_lat="+centerlat+"&center_lng="+centerlng;
		//console.log(myurl);
		document.getElementById('textfield').value = myurl;
		$('#dialog').dialog('open');
	}

	function findMyLocation (){
		/* http://leafletjs.com/examples/mobile-example.html */
		/* http://www.bennadel.com/blog/2023-Geocoding-A-User-s-Location-Using-Javascript-s-GeoLocation-API.htm */
		map.locate({setView: true, maxZoom: 16});
	}

	function zoomIn (e) {
		map.zoomIn();
	}

	function zoomOut (e) {
		map.zoomOut();
	}

	map.contextmenu.addItem({text: '{/literal}{$HOME_NAME}{literal}', iconCls: 'swisstopo-home', callback: goHome});
	map.contextmenu.addItem({text: '{/literal}{$HOME_PREV_NAME}{literal}', iconCls: 'swisstopo-left-big', callback: goBack});
	map.contextmenu.addItem('-');
	map.contextmenu.addItem({text: 'Show coordinates', iconCls: 'swisstopo-pin', callback: showCoordinates});
	map.contextmenu.addItem({text: 'Center map here', iconCls: 'swisstopo-location', callback: centerMap});
	map.contextmenu.addItem('-');
	map.contextmenu.addItem({text: 'Show all items', iconCls: 'swisstopo-link-ext', callback: goShowAll});
	map.contextmenu.addItem({text: 'Link to this map', iconCls: 'swisstopo-link', callback: linkToThisMap});
	map.contextmenu.addItem({text: 'Find my position', iconCls: 'swisstopo-direction', callback: findMyLocation});
	map.contextmenu.addItem({separator: true});
	map.contextmenu.addItem({text: 'Zoom in', iconCls: 'swisstopo-zoom-in', callback: zoomIn});
	map.contextmenu.addItem({text: 'Zoom out', iconCls: 'swisstopo-zoom-out', callback: zoomOut});
	/* END leaflet-locatecontrol */

	/* BEGIN piwigo_swisstopo plugin */
	map.on('moveend', onMapMove);

	function onMapMove(e){
		//getMarkers();
	}

	/* BEGIN leaflet Location */
	function onLocationFound(e) {
		var radius = e.accuracy / 2;

		L.marker(e.latlng).addTo(map)
			.bindPopup("You are within " + radius + " meters from this point").openPopup();

		L.circle(e.latlng, radius).addTo(map);
	}

	function onLocationError(e) {
		alert(e.message);
	}

	map.on('locationfound', onLocationFound);
	map.on('locationerror', onLocationError);


/* Providers list.
 * Here because of the icon path
 */

var provider_mapping = {
    'mapnik'         : 'Swisstopo_Mapnik',
    'blackandwhite'  : 'Swisstopo_BlackAndWhite',
    'mapnikfr'       : 'Swisstopo_France',
    'mapnikde'       : 'Swisstopo_DE',
    'mapnikhot'      : 'Swisstopo_HOT',
    'mapquest'       : 'MapQuestOpen_SWISSTOPO',
    'mapquestaerial' : 'MapQuestOpen_Aerial',
    'cloudmade'      : 'CloudMade',
    'toner'          : 'Stamen_Toner',
    'custom'         : 'Custom',
};

var providers = {};

providers['Swisstopo_Mapnik'] = {
    title: 'swisstopo',
    icon: '{/literal}{$SWISSTOPO_PATH}{literal}leaflet/icons/swisstopo_mapnik.png',
    layer: L.tileLayer('https://{s}.tile.swisstopo.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.swisstopo.org/copyright">Swisstopo</a>'
    })
};

providers['Swisstopo_BlackAndWhite'] = {
    title: 'swisstopo bw',
    icon: '{/literal}{$SWISSTOPO_PATH}{literal}leaflet/icons/swisstopo_blackandwhite.png',
    layer: L.tileLayer('https://{s}.tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; <a href="http://www.swisstopo.org/copyright">Swisstopo</a>'
    })
};

providers['Swisstopo_France'] = {
    title: 'swisstopo fr',
    icon: 'https://a.tile.swisstopo.fr/swisstopofr/5/15/11.png',
    layer: L.tileLayer('https://{s}.tile.swisstopo.fr/swisstopofr/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; Swisstopo France | &copy; <a href="http://www.swisstopo.org/copyright">Swisstopo</a>'
    })
}

providers['Swisstopo_DE'] = {
    title: 'swisstopo de',
    icon: '{/literal}{$SWISSTOPO_PATH}{literal}leaflet/icons/swisstopo_de.png',
    layer: L.tileLayer('https://{s}.tile.swisstopo.de/tiles/swisstopode/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; <a href="http://www.swisstopo.org/copyright">Swisstopo</a>'
    })
}

providers['Swisstopo_HOT'] = {
    title: 'swisstopo HOT',
    icon: 'http://a.tile.swisstopo.fr/hot/5/15/11.png',
    layer: L.tileLayer('http://{s}.tile.swisstopo.fr/hot/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.swisstopo.org/copyright">Swisstopo</a>, Tiles courtesy of <a href="http://hot.swisstopo.org/" target="_blank">Humanitarian Swisstopo Team</a>'
    })
}

providers['MapQuestOpen_SWISSTOPO'] = {
    title: 'MapQuest',
    icon: 'http://otile1.mqcdn.com/tiles/1.0.0/map/5/15/11.png',
    layer: L.tileLayer('http://otile{s}.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: 'Tiles Courtesy of <a href="http://www.mapquest.com/">MapQuest</a> &mdash; Map data &copy; <a href="http://www.swisstopo.org/copyright">Swisstopo</a>',
        subdomains: '1234'
    })
}

providers['MapQuestOpen_Aerial'] = {
    title: 'MapQuest Aerial',
    icon: 'http://otile1.mqcdn.com/tiles/1.0.0/sat/5/15/11.png',
    layer: L.tileLayer('http://otile{s}.mqcdn.com/tiles/1.0.0/sat/{z}/{x}/{y}.png', {
        attribution: 'Tiles Courtesy of <a href="http://www.mapquest.com/">MapQuest</a> &mdash; Portions Courtesy NASA/JPL-Caltech and U.S. Depart. of Agriculture, Farm Service Agency',
        subdomains: '1234'
    })
}

providers['Stamen_Toner'] = {
    title: 'toner',
    icon: '{/literal}{$SWISSTOPO_PATH}{literal}leaflet/icons/stamen_toner.png',
    layer: L.tileLayer('https://stamen-tiles-{s}.a.ssl.fastly.net/toner/{z}/{x}/{y}.png', {
        attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Map data &copy; <a href="http://www.swisstopo.org/copyright">Swisstopo</a>',
        subdomains: 'abcd',
        minZoom: 0,
        maxZoom: 20,
        ext: 'png'
    })
};

providers['Esri_WorldTerrain'] = {
    title: 'esri terrain',
    icon: '{/literal}{$SWISSTOPO_PATH}{literal}leaflet/icons/esri_worldterrain.png',
    layer: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Terrain_Base/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: USGS, Esri, TANA, DeLorme, and NPS',
        maxZoom: 13
    })
};

providers['Esri_OceanBasemap'] = {
    title: 'esri ocean',
    icon: '{/literal}{$SWISSTOPO_PATH}{literal}leaflet/icons/esri_oceanbasemap.png',
    layer: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Ocean_Basemap/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Sources: GEBCO, NOAA, CHS, OSU, UNH, CSUMB, National Geographic, DeLorme, NAVTEQ, and Esri',
        maxZoom: 13
    })
};

providers['HERE_normalDay'] = {
    title: 'normalday',
    icon: '{/literal}{$SWISSTOPO_PATH}{literal}leaflet/icons/here_normalday.png',
    layer: L.tileLayer('https://{s}.{base}.maps.cit.api.here.com/maptile/2.1/maptile/{mapID}/normal.day/{z}/{x}/{y}/256/png8?app_id={app_id}&app_code={app_code}', {
        attribution: 'Map &copy; 1987-2014 <a href="http://developer.here.com">HERE</a>',
        subdomains: '1234',
        mapID: 'newest',
        app_id: 'Y8m9dK2brESDPGJPdrvs',
        app_code: 'dq2MYIvjAotR8tHvY8Q_Dg',
        base: 'base',
        maxZoom: 20
    })
};

providers['HERE_normalDayGrey'] = {
    title: 'normalday grey',
    icon: '{/literal}{$SWISSTOPO_PATH}{literal}leaflet/icons/here_normaldaygrey.png',
    layer: L.tileLayer('https://{s}.{base}.maps.cit.api.here.com/maptile/2.1/maptile/{mapID}/normal.day.grey/{z}/{x}/{y}/256/png8?app_id={app_id}&app_code={app_code}', {
        attribution: 'Map &copy; 1987-2014 <a href="http://developer.here.com">HERE</a>',
        subdomains: '1234',
        mapID: 'newest',
        app_id: 'Y8m9dK2brESDPGJPdrvs',
        app_code: 'dq2MYIvjAotR8tHvY8Q_Dg',
        base: 'base',
        maxZoom: 20
    })
};

providers['HERE_satelliteDay'] = {
    title: 'satellite',
    icon: '{/literal}{$SWISSTOPO_PATH}{literal}leaflet/icons/here_satelliteday.png',
    layer: L.tileLayer('https://{s}.{base}.maps.cit.api.here.com/maptile/2.1/maptile/{mapID}/satellite.day/{z}/{x}/{y}/256/png8?app_id={app_id}&app_code={app_code}', {
        attribution: 'Map &copy; 1987-2014 <a href="http://developer.here.com">HERE</a>',
        subdomains: '1234',
        mapID: 'newest',
        app_id: 'Y8m9dK2brESDPGJPdrvs',
        app_code: 'dq2MYIvjAotR8tHvY8Q_Dg',
        base: 'aerial',
        maxZoom: 20
    })
};

providers['CartoDB_Positron'] = {
    title: 'positron',
    icon: '{/literal}{$SWISSTOPO_PATH}{literal}leaflet/icons/cartodb_positron.png',
    layer: L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://www.swisstopo.org/copyright">Swisstopo</a> &copy; <a href="http://cartodb.com/attributions">CartoDB</a>',
        subdomains: 'abcd',
        maxZoom: 19
    })
};

{/literal}
{if $default_baselayer == 'custom'}
{literal}
    providers['Custom'] = {
        title: '{/literal}{$custombaselayer}{literal}',
        icon: '{/literal}{$iconbaselayer}{literal}',
        layer: L.tileLayer('{/literal}{$custombaselayerurl}{literal}', {
            // The following attribution might be wrong
            // Please refer to https://leaflet-extras.github.io/leaflet-providers/preview/
            // to get the correct attribution notice.
            attribution: '&copy; <a href="http://www.swisstopo.org/copyright">Swisstopo</a>',
            maxZoom: 19
        })
    };
{/literal}
{/if}
{literal}

	/* BEGIN Leaflet-IconLayers https://github.com/ScanEx/Leaflet-IconLayers */
	var layers = [];
	for (var providerId in providers) {
		layers.push(providers[providerId]); // Providers from providers.js
	}
    var il = L.control.iconLayers(layers);
    il.setActiveLayer(providers[provider_mapping['{/literal}{$default_baselayer}{literal}']].layer);
    var ctrl = il.addTo(map);
	/* END Leaflet-IconLayers */


</script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<script src="https://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
<script>

        /* Init Jquery */
        (function($) {

		$('#dialog').dialog({autoOpen: false, minHeight: 150, minWidth: 600});

                $('#opener').click(function() {
                        $('#dialog').dialog('open');
                });

        })(jQuery);

{/literal}
</script>

</body>
</html>
