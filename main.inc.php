<?php
/*
Plugin Name: SwissTopo
Version: auto
Description: SwissTopo integration for piwigo
Plugin URI: auto
Author: Alessandro Rossi
Author URI: https://dayonefactory.ch
Has Settings: webmaster
*/

// Chech whether we are indeed included by Piwigo.
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

// Define the path to our plugin.
define('SWISSTOPO_PATH', PHPWG_PLUGINS_PATH . basename(dirname(__FILE__)).'/');
global $conf;

// Prepare configuration
$conf['swisstopo_conf'] = safe_unserialize($conf['swisstopo_conf']);

// GPX support
include_once(dirname(__FILE__).'/gpx.inc.php');

// Plugin on picture page
if (script_basename() == 'picture')
{
	include_once(dirname(__FILE__).'/picture.inc.php');
}
elseif (script_basename() == 'index')
{
    include_once(dirname(__FILE__).'/category.inc.php');
    include_once(dirname(__FILE__).'/menu.inc.php');
}

// Do we have to show a link on the left menu
if ($conf['swisstopo_conf']['left_menu']['enabled'])
{
	// Hook to add link on the left menu
	add_event_handler('blockmanager_apply', 'swisstopo_blockmanager_apply');
}

// Hook to add worldmap link on the album/category thumbnails
add_event_handler('loc_begin_index_category_thumbnails', 'swisstopo_index_cat_thumbs_displayed');

// Hook to add worldmap link on the index thumbnails page
add_event_handler('loc_end_index', 'swisstopo_end_index' );

function swisstopo_index_cat_thumbs_displayed()
{
	global $page;
	$page['swisstopo_cat_thumbs_displayed'] = true;
}

define('SWISSTOPO_ACTION_MODEL', '<a href="%s" title="%s" rel="nofollow" class="pwg-state-default pwg-button"%s><span class="pwg-icon pwg-icon-globe">&nbsp;</span><span class="pwg-button-text">%s</span></a>');
function swisstopo_end_index()
{
	global $page, $filter, $template;

	if ( isset($page['chronology_field']) || $filter['enabled'] )
		return;

	if ( 'categories' == @$page['section'])
	{ // flat or no flat ; has subcats or not;  ?
		if ( ! @$page['swisstopo_cat_thumbs_displayed'] and empty($page['items']) )
			return;
	}
	else
	{
		if (
			!in_array( @$page['section'], array('tags','search','recent_pics','list') )
			)
			return;
		if ( empty($page['items']) )
			return;
	}

	include_once( dirname(__FILE__) .'/include/functions.php');

	if ( !empty($page['items']) )
	{
		if (!@$page['swisstopo_items_have_latlon'] and ! swisstopo_items_have_latlon( $page['items'] ) )
			return;
	}
	swisstopo_load_language();

	global $conf;
	$layout = isset($conf['swisstopo_conf']['left_menu']['layout']) ? $conf['swisstopo_conf']['left_menu']['layout'] : '2';
	$map_url = swisstopo_duplicate_map_index_url( array(), array('start') ) ."&v=".$layout;
	$link_title = sprintf( l10n('DISPLAY_ON_MAP'), strip_tags($page['title']) );
	$template->concat( 'PLUGIN_INDEX_ACTIONS' , "\n<li>".sprintf(SWISSTOPO_ACTION_MODEL,
		$map_url, $link_title, '', 'map', l10n('MAP')
		).'</li>');
}

// If admin do the init
if (defined('IN_ADMIN')) {
	include_once(SWISSTOPO_PATH.'/admin/admin_boot.php');
}


function swisstopo_blockmanager_apply($mb_arr)
{
	if ($mb_arr[0]->get_id() != 'menubar' )
		return;
	if ( ($block=$mb_arr[0]->get_block('mbMenu')) != null )
	{
		include_once( dirname(__FILE__) .'/include/functions.php');
		load_language('plugin.lang', SWISSTOPO_PATH);
		global $conf;
		$linkname = isset($conf['swisstopo_conf']['left_menu']['link']) ? $conf['swisstopo_conf']['left_menu']['link'] : l10n('OSWorldMap');
		$layout = isset($conf['swisstopo_conf']['left_menu']['layout']) ? $conf['swisstopo_conf']['left_menu']['layout'] : '2';
		$link_title = sprintf( l10n('DISPLAY_ON_MAP'), strip_tags($conf['gallery_title']) );
		$block->data['swisstopo'] = array(
			'URL' => swisstopo_make_map_index_url( array('section'=>'categories') ) ."&v=".$layout,
			'TITLE' => $link_title,
			'NAME' => $linkname,
			'REL'=> 'rel=nofollow'
		);
	}
}

function swisstopo_strbool($value)
{
	return $value ? 'true' : 'false';
}

?>
