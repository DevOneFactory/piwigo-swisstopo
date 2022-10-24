{html_head}
<link rel="stylesheet" href="{$SWISSTOPO_PATH}fontello/css/swisstopo.css" />
<style>
  {literal}
    .swisstopo_layout {
      text-align: left;
      border: 2px solid rgb(221, 221, 221);
      padding: 1em;
      margin: 1em;
    }
  {/literal}
</style>
{/html_head}

This plugin display geographical location in your gallery using <a href="http://www.swisstopo.org/" target="_blank">Swisstopo</a>.
<br/><br/>
Refer to the <a href="https://github.com/xbgmsharp/piwigo_swisstopo/wiki" target="_blanck">plugin documentation</a> for additional information. Create an <a href="https://github.com/xbgmsharp/piwigo_swisstopo/issues" target="_blanck">issue</a> for support, or feedback, or feature request.

<div class="swisstopo_layout">
  <legend>{'Statistics'|@translate}</legend>
  <ul>
    <li class="update_summary_new">{$NB_GEOTAGGED} geotagged items in your gallery</li>
  </ul>
</div>

<form method="post" action="" class="properties">
	<fieldset>
		<legend>{'R_MAP'|@translate}</legend>
		<ul>
			<li>
				<label>{'SHOWLOCATION'|@translate} : </label>
				<label><input type="radio" name="swisstopo_right_panel" value="true" {if $right_panel.enabled}checked="checked"{/if}/> {'Yes'|@translate}</label>
				<label><input type="radio" name="swisstopo_right_panel" value="false" {if not $right_panel.enabled}checked="checked"{/if}/> {'No'|@translate}</label>
				<br/><small>{'SHOWLOCATION_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'ADD_BEFORE'|@translate} : </label>
				<select name="swisstopo_add_before">
					{html_options options=$AVAILABLE_ADD_BEFORE selected=$right_panel.add_before}
				</select>
				<br/><small>{'ADD_BEFORE_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'HEIGHT'|@translate} : </label>
				<input type="text" value="{$right_panel.height}" name="swisstopo_height" size="4" required/>
				<br/><small>{'HEIGHT_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'ZOOM'|@translate} : </label>
				<select name="swisstopo_zoom">
					{html_options options=$AVAILABLE_ZOOM selected=$right_panel.zoom}
				</select>
				<br/><small>{'ZOOM_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'RIGHTLINK'|@translate} : </label>
				<input type="text" value="{$right_panel.link}" name="swisstopo_right_link" size="20"/>
				<br/><small>{'RIGHTLINK_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'RIGHTLINKCSS'|@translate} : </label>
				<input type="text" value="{$right_panel.linkcss}" name="swisstopo_right_linkcss" size="60" placeholder="vertical-align: top; color: red;"/>
				<br/><small>{'RIGHTLINKCSS_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'SHOWSWISSTOPO'|@translate} : </label>
				<label><input type="radio" name="swisstopo_showswisstopo" value="true" {if $right_panel.showswisstopo}checked="checked"{/if}/> {'Yes'|@translate}</label>
				<label><input type="radio" name="swisstopo_showswisstopo" value="false" {if not $right_panel.showswisstopo}checked="checked"{/if}/> {'No'|@translate}</label>
				<br/><small>{'SHOWSWISSTOPO_DESC'|@translate}</small>
			</li>
		</ul>
	</fieldset>
	<fieldset>
		<legend>{'L_MAP'|@translate}</legend>
		<ul>
			<li>
				<label>{'SHOWWORLDMAPLEFT'|@translate} : </label>
				<label><input type="radio" name="swisstopo_left_menu" value="true" {if $left_menu.enabled}checked="checked"{/if}/> {'Yes'|@translate}</label>
				<label><input type="radio" name="swisstopo_left_menu" value="false" {if not $left_menu.enabled}checked="checked"{/if}/> {'No'|@translate}</label>
				<br/><small>{'SHOWWORLDMAPLEFT_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'LAYOUT_MAP'|@translate} : </label>
				<select name="swisstopo_left_layout">
					{html_options options=$AVAILABLE_LAYOUT selected=$left_menu.layout}
				</select>
				<br/><small>{'LAYOUT_MAP_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'LEFTLINK'|@translate} : </label>
				<input type="text" value="{$left_menu.link}" name="swisstopo_left_link" size="20"/>
				<br/><small>{'LEFTLINK_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'LEFTPOPUP'|@translate} : </label>
				<select name="swisstopo_left_popup">
					{html_options options=$AVAILABLE_POPUP selected=$left_menu.popup}
				</select>
				<br/><small>{'LEFTPOPUP_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'LEFTPOPUPINFO'|@translate} : </label><br/>
				<div style="padding-left: 25px">
				  <input type="checkbox" name="swisstopo_left_popupinfo_name" value="true" {if $left_menu.popupinfo_name}checked="checked"{/if}/> {'POPUPNAME'|@translate}<br />
				  <input type="checkbox" name="swisstopo_left_popupinfo_img" value="true" {if $left_menu.popupinfo_img}checked="checked"{/if}/> {'POPUPTHUMB'|@translate}<br /> 
				  <input type="checkbox" name="swisstopo_left_popupinfo_link" value="true" {if $left_menu.popupinfo_link}checked="checked"{/if}/> {'POPUPLINK'|@translate}<br /> 
				  <input type="checkbox" name="swisstopo_left_popupinfo_comment" value="true" {if $left_menu.popupinfo_comment}checked="checked"{/if}/> {'POPUPCOMMENT'|@translate}<br /> 
				  <input type="checkbox" name="swisstopo_left_popupinfo_author" value="true" {if $left_menu.popupinfo_author}checked="checked"{/if}/> {'POPUPAUTHOR'|@translate}
				</div>
				<small>{'LEFTPOPUPINFO_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'Auto center'|@translate} : </label>
				<label><input id="autocenter_enabled" type="radio" name="swisstopo_left_autocenter" value="true" {if $left_menu.autocenter}checked="checked"{/if} onchange="autocenter_toggle(this);"/> {'Yes'|@translate}</label>
				<label><input type="radio" name="swisstopo_left_autocenter" value="false" {if not $left_menu.autocenter}checked="checked"{/if} onchange="autocenter_toggle(this);"/> {'No'|@translate}</label>
				<br/><small>{'The map will be automatically centered and zoomed to contain all infos.'|@translate}</small>
			</li>
			<li id="swisstopo_left_zoom_block">
				<label>{'ZOOM'|@translate} : </label>
				<select name="swisstopo_left_zoom">
					{html_options options=$AVAILABLE_ZOOM selected=$left_menu.zoom}
				</select>
				<br/><small>{'ZOOM_DESC'|@translate}</small>
			</li>
			<li id="swisstopo_left_center_block">
				<label>{'CENTER_MAP'|@translate} : </label>
				<input type="text" value="{$left_menu.center}" name="swisstopo_left_center" size="30" placeholder="0,0"/>
				<br/><small>{'CENTER_MAP_DESC'|@translate}</small>
			</li>
		</ul>
	</fieldset>
	<fieldset>
		<legend>{'C_MAP'|@translate}</legend>
		<ul>
			<li>
				<label>{'SHOWCMAP'|@translate} : </label>
				<label><input type="radio" name="swisstopo_category_description" value="true" {if $category_description.enabled}checked="checked"{/if}/> {'Yes'|@translate}</label>
				<label><input type="radio" name="swisstopo_category_description" value="false" {if not $category_description.enabled}checked="checked"{/if}/> {'No'|@translate}</label>
				<br/><small>{'SHOWCMAP_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'POSITION_INDEX_CMAP'|@translate} : </label>
				<select name="swisstopo_cat_index">
					{html_options options=$AVAILABLE_CAT_INDEX selected=$category_description.index}
				</select>
				<br/><small>{'POSITION_INDEX_CMAP_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'HEIGHT'|@translate} : </label>
				<input type="text" value="{$category_description.height}" name="swisstopo_cat_height" size="4" required placeholder="200"/>
				<br/><small>{'HEIGHT_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'WIDTH'|@translate} : </label>
				<input type="text" value="{$category_description.width}" name="swisstopo_cat_width" size="4" required placeholder="auto"/>
				<br/><small>{'WIDTH_DESC'|@translate}</small>
			</li>
		</ul>
	</fieldset>
	<fieldset>
		<legend>{'M_MAP'|@translate}</legend>
		<ul>
			<li>
				<label>{'SHOWMMAP'|@translate} : </label>
				<label><input type="radio" name="swisstopo_main_menu" value="true" {if $main_menu.enabled}checked="checked"{/if}/> {'Yes'|@translate}</label>
				<label><input type="radio" name="swisstopo_main_menu" value="false" {if not $main_menu.enabled}checked="checked"{/if}/> {'No'|@translate}</label>
				<br/><small>{'SHOWMMAP_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'HEIGHT'|@translate} : </label>
				<input type="text" value="{$main_menu.height}" name="swisstopo_menu_height" size="4" required placeholder="200"/>
				<br/><small>{'HEIGHT_DESC'|@translate}</small>
			</li>
		</ul>
	</fieldset>
	<fieldset>
		<legend>{'GPX_MAP'|@translate}</legend>
		<ul>
			<li>
				<label>{'HEIGHT'|@translate} : </label>
				<input type="text" value="{$gpx.height}" name="swisstopo_gpx_height" size="4" required placeholder="500"/>
				<br/><small>{'HEIGHT_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'GPX_WIDTH'|@translate} : </label>
				<input type="text" value="{$gpx.width}" name="swisstopo_gpx_width" size="4" required placeholder="320"/>
				<br/><small>{'WIDTH_DESC'|@translate}</small>
			</li>
		</ul>
	</fieldset>
	<fieldset>
		<legend>{'Batch Manager'|@translate} {'MAP'|@translate}</legend>
		<ul>
			<li>
				<label>{'HEIGHT'|@translate} {$GLOBAL_MODE}: </label>
				<input type="text" value="{$batch.global_height}" name="swisstopo_batch_global_height" size="4" required placeholder="200"/>
				<br/><small>{'HEIGHT_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'HEIGHT'|@translate} {$SINGLE_MODE}: </label>
				<input type="text" value="{$batch.unit_height}" name="swisstopo_batch_unit_height" size="4" required placeholder="200"/>
				<br/><small>{'HEIGHT_DESC'|@translate}</small>
			</li>
		</ul>
	</fieldset>
	<fieldset>
		<legend>{'G_MAP'|@translate}</legend>
		<ul>
			<li>
				<img id="tile_preview" align="right" src="">
				<label>{'BASELAYER'|@translate} : </label>
				<select name="swisstopo_baselayer" id="swisstopo_baselayer" onchange="tile_toggle(this)">
					{html_options options=$AVAILABLE_BASELAYER selected=$map.baselayer}
				</select>
				<br/><small>{'BASELAYER_DESC'|@translate}</small><br/>
				<small>Check out <a href="http://leaflet-extras.github.io/leaflet-providers/preview/" target="_blank">this example</a> with half a hundred different layers to choose from.</small>
			</li>
			<div id="custom-tile-toggle" style="visibility:hidden; width:0px; height:0px; display:none;">
				<fieldset>
				<li>
					<label>{'CUSTOMBASELAYER'|@translate} : </label>
					<input type="text" value="{$map.custombaselayer}" name="swisstopo_custombaselayer" id="swisstopo_custombaselayer" size="40"/>
					<br/><small>{'CUSTOMBASELAYER_DESC'|@translate}</small>
				</li>
				<li>
					<label>{'CUSTOMBASELAYERURL'|@translate} : </label>
					<input type="text" value="{$map.custombaselayerurl}" name="swisstopo_custombaselayerurl" id="swisstopo_custombaselayerurl" onchange="tile_toggle(this)" size="40"/>
					<br/><small>{'CUSTOMBASELAYERURL_DESC'|@translate}</small>
				</li>
				</fieldset>
			</div>
			<div id="mapquest-tile-toggle" style="visibility:hidden; width:0px; height:0px; display:none;">
				<fieldset>
				<li>
					<label>{'MAPQUEST_APIKEY'|@translate} : </label>
					<input type="text" value="{$map.mapquest_apikey}" name="swisstopo_mapquestapi" id="swisstopo_mapquestapi" size="40"/>
					<br/><small>{'MAPQUEST_APIKEY_DESC'|@translate} Check out <a href="http://www.mapquestapi.com/" target="_blank">MapQuest API</a></small>
				</li>
				</fieldset>
			</div>
			<li>
				<label>{'NOWORLDWARP'|@translate} : </label>
				<label><input type="radio" name="swisstopo_noworldwarp" value="true" {if $map.noworldwarp}checked="checked"{/if}/> {'Yes'|@translate}</label>
				<label><input type="radio" name="swisstopo_noworldwarp" value="false" {if not $map.noworldwarp}checked="checked"{/if}/> {'No'|@translate}</label>
				<br/><small>{'NOWORLDWARP_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'ATTRLEAFLET'|@translate} : </label>
				<label><input type="radio" name="swisstopo_attrleaflet" value="true" {if $map.attrleaflet}checked="checked"{/if}/> {'Yes'|@translate}</label>
				<label><input type="radio" name="swisstopo_attrleaflet" value="false" {if not $map.attrleaflet}checked="checked"{/if}/> {'No'|@translate}</label>
				<br/><small>{'ATTRLEAFLET_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'ATTRIMAGERY'|@translate} : </label>
				<label><input type="radio" name="swisstopo_attrimagery" value="true" {if $map.attrimagery}checked="checked"{/if}/> {'Yes'|@translate}</label>
				<label><input type="radio" name="swisstopo_attrimagery" value="false" {if not $map.attrimagery}checked="checked"{/if}/> {'No'|@translate}</label>
				<br/><small>{'ATTRIMAGERY_DESC'|@translate}</small>
			</li>
			<li>
				<label>{'ATTRPLUGIN'|@translate} : </label>
				<label><input type="radio" name="swisstopo_attrplugin" value="true" {if $map.attrplugin}checked="checked"{/if}/> {'Yes'|@translate}</label>
				<label><input type="radio" name="swisstopo_attrplugin" value="false" {if not $map.attrplugin}checked="checked"{/if}/> {'No'|@translate}</label>
				<br/><small>{'ATTRPLUGIN_DESC'|@translate}</small>
			</li>

		</ul>
           <fieldset>
		  <legend>{'H_PIN'|@translate}</legend>
		  <ul>
			<li>
				<img id="pin_preview" align="left" src="">
				<label >{'PIN'|@translate} : </label>
				<select name="swisstopo_pin" id="swisstopo_pin" onchange="pin_toggle(this)">
					{html_options options=$AVAILABLE_PIN selected=$pin.pin}
				</select>
				<br/><small>{'PIN_DESC'|@translate}</small>
			</li>
			<div id="custom-pin-toggle" style="visibility:hidden; width:0px; height:0px; display:none;">
				<li>
					<label>{'PINPATH'|@translate} : </label>
					<input type="text" value="{$pin.pinpath}" name="swisstopo_pinpath" size="40"/>
					<br/><small>{'PINPATH_DESC'|@translate}</small>
				</li>
				<li>
					<label>{'PINSIZE'|@translate} : </label>
					<input type="text" value="{$pin.pinsize}" name="swisstopo_pinsize" size="6"/>
					<br/><small>{'PINSIZE_DESC'|@translate}</small>
				</li>
				<li>
					<label>{'PINSHADOWPATH'|@translate} : </label>
					<input type="text" value="{$pin.pinshadowpath}" name="swisstopo_pinshadowpath" size="40"/>
					<br/><small>{'PINSHADOWPATH_DESC'|@translate}</small>
				</li>
				<li>
					<label>{'PINSHADOWSIZE'|@translate} : </label>
					<input type="text" value="{$pin.pinshadowsize}" name="swisstopo_pinshadowsize" size="4"/>
					<br/><small>{'PINSHADOWSIZE_DESC'|@translate}</small>
				</li>
				<li>
					<label>{'PINOFFSET'|@translate} : </label>
					<input type="text" value="{$pin.pinoffset}" name="swisstopo_pinoffset" size="4"/>
					<br/><small>{'PINOFFSET_DESC'|@translate}</small>
				</li>
				<li>
					<label>{'PINPOPUPOFFSET'|@translate} : </label>
					<input type="text" value="{$pin.pinpopupoffset}" name="swisstopo_pinpopupoffset" size="4"/>
					<br/><small>{'PINPOPUPOFFSET_DESC'|@translate}</small>
				</li>
			</div>
		  </ul>
		</fieldset>
	</fieldset>

	<p>
		<input class="submit" type="submit" value="{'Save Settings'|@translate}" name="submit"/>
	</p>
</form>

{literal}
<script type="text/javascript">
function tile_toggle()
{
	var div_custom = document.getElementById("custom-tile-toggle");
	var div_mapquest = document.getElementById("mapquest-tile-toggle");
	var select = document.getElementById("swisstopo_baselayer").value;
	//alert(select.selectedIndex);
	if (select == "custom") // If custom
	{
		div_custom.removeAttribute("style");
		div_mapquest.setAttribute("style","visibility:hidden; width:0px; height:0px; display:none;");
	} else if (select.startsWith("mapquest")) // If mapquest
	{
		div_mapquest.removeAttribute("style");
		div_custom.setAttribute("style","visibility:hidden; width:0px; height:0px; display:none;");
	} else {
		div_custom.setAttribute("style","visibility:hidden; width:0px; height:0px; display:none;");
		div_mapquest.setAttribute("style","visibility:hidden; width:0px; height:0px; display:none;");
	}
	tile_preview();
}

function pin_toggle()
{
	var div = document.getElementById("custom-pin-toggle");
	var select = document.getElementById("swisstopo_pin");
	//alert(select.selectedIndex);
	if (select.selectedIndex == 9) // If custom
	{
		div.removeAttribute("style");
	} else {
		div.setAttribute("style","visibility:hidden; width:0px; height:0px; display:none;");
	}
	pin_preview();
}

function autocenter_toggle()
{
	var radio = document.getElementById("autocenter_enabled");
	var zoom_block = document.getElementById("swisstopo_left_zoom_block");
	var center_block = document.getElementById("swisstopo_left_center_block");
	if (radio.checked) // If autocenter
	{
		zoom_block.setAttribute("style", "display:none;");
		center_block.setAttribute("style", "display:none;");
	} else {
		zoom_block.removeAttribute("style");
		center_block.removeAttribute("style");
	}
}

function tile_preview()
{
	var select = document.getElementById("swisstopo_baselayer");
	var custom_url = document.getElementById("swisstopo_custombaselayerurl").value;
	if ( custom_url ) {
		custom_url = custom_url.replace('{z}', '5').replace('{x}', '15').replace('{y}', '11');
	} else {
		custom_url = 'NULL';
	}
	baselayer = new Array(
		'{/literal}{$SWISSTOPO_PATH}{literal}leaflet/icons/preview_swisstopo.png'
	);
	//alert(baselayer[select.selectedIndex]);
	var img_elem = document.getElementById("tile_preview");
	if (baselayer[select.selectedIndex] == "NULL")
	{
		img_elem.setAttribute("style","visibility:hidden; width:0px; height:0px; display:none;");
	} else {
		img_elem.removeAttribute("style");
		img_elem.src = baselayer[select.selectedIndex];
	}
}

function pin_preview()
{
	var select = document.getElementById("swisstopo_pin");
	pins = new Array(
		'NULL',
		'{/literal}{$SWISSTOPO_PATH}{literal}leaflet/images/marker-blue.png',
		'{/literal}{$SWISSTOPO_PATH}{literal}leaflet/images/marker-green.png',
		'{/literal}{$SWISSTOPO_PATH}{literal}leaflet/images/marker-red.png',
		'{/literal}{$SWISSTOPO_PATH}{literal}leaflet/images/leaf-green.png',
		'{/literal}{$SWISSTOPO_PATH}{literal}leaflet/images/leaf-orange.png',
		'{/literal}{$SWISSTOPO_PATH}{literal}leaflet/images/leaf-red.png',
		'{/literal}{$SWISSTOPO_PATH}{literal}leaflet/images/mapicons-blue.png',
		'{/literal}{$SWISSTOPO_PATH}{literal}leaflet/images/mapicons-green.png',
		'NULL',
		'NULL'
	);
	//alert(pins[select.selectedIndex]);
	var img_elem = document.getElementById("pin_preview");
	if (pins[select.selectedIndex] == "NULL")
	{
		img_elem.setAttribute("style","visibility:hidden; width:0px; height:0px; display:none;");
	} else {
		img_elem.removeAttribute("style");
		img_elem.setAttribute("style","padding-right: 5px;");
		img_elem.src = pins[select.selectedIndex];
	}
}

window.onload = pin_preview();
window.onload = tile_preview();
window.onload = autocenter_toggle()

</script>
{/literal}
