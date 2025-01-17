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
#mapmenu {
    height: {/literal}{$HEIGHT}{literal}px;

}
{/literal}
{/html_style}

<dt>{'MAP'|@translate}</dt><dd>
<div id="mapmenu"></div></dd>
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

