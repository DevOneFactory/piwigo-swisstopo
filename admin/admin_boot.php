<?php
/***********************************************
* File      :   admin_boot.php
* Project   :   piwigo_swisstopo
* Descr     :   Generate the admin panel
*
* Created   :   11.06.2013
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

// Batch_manager support
include_once(dirname(__FILE__).'/admin_batchmanager.php');

// Hook to add an photo edit tab in photo edit
add_event_handler('tabsheet_before_select','swisstopo_photo_add_tab', 50, 2);
function swisstopo_photo_add_tab($sheets, $id)
{
	if ($id == 'photo')
	{
		$sheets['swisstopo'] = array(
			'caption' => '<span class="swisstopo-globe"></span>Swisstopo',
			'url' => get_root_url().'admin.php?page=plugin&amp;section=piwigo_swisstopo/admin/admin_photo.php&amp;image_id='.$_GET['image_id'],
			);
	}

	return $sheets;
}

/* Pretty Print recursive */
function swisstopo_pprint_r(array $array, $glue = ', <br/>', $size = 4)
{
        // Split tag array in chuck of $size for nicer display
        $chunk_arr = array_chunk( $array, $size, true);

        // Generate ouput
        $output = "";
        foreach ( $chunk_arr as $row ) {
                foreach ( $row as $key ) {
                        //printf('[%2s] ', $key);
                        $output .= $key.", ";
                }
                $output .= "<br/>";
        }

        // Removes last $glue from string
        strlen($glue) > 0 and $output = substr($output, 0, -strlen($glue));

        return (string) $output;
}


?>
