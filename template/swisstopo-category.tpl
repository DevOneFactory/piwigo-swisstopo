{html_head}
<link href="{$SWISSTOPO_PATH}leaflet/leaflet.css" rel="stylesheet">
<script src="{$SWISSTOPO_PATH}leaflet/leaflet.js"></script>
<script src="{$SWISSTOPO_PATH}leaflet/leaflet-omnivore.min.js"></script>
<link rel="stylesheet" href="{$SWISSTOPO_PATH}leaflet/MarkerCluster.css" />
<link rel="stylesheet" href="{$SWISSTOPO_PATH}leaflet/MarkerCluster.Default.css" />
<script src="{$SWISSTOPO_PATH}leaflet/leaflet.markercluster.js"></script>
{/html_head}

{html_style}
{literal}
#map {
    height: {/literal}{$HEIGHT}{literal}px;
    width: {/literal}{$WIDTH}{literal}px;
    {/literal}{if $WIDTH != 'auto'}width: {$WIDTH}px;
    float: left;{/if}{literal}

}
{/literal}
{/html_style}

<div id="map"></div>
<script type="text/javascript">{$SWISSTOPOJS}

{literal}
    map.on('moveend', onMapMove);

    function onMapMove(e){
        getMarkers();
    }

    function getMarkers(){
        var bounds = map.getBounds();
    }
{/literal}
</script>

