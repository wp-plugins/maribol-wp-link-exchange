<?php
/*
Plugin Name: Maribol WP Link Exchange
Plugin URI: http://www.mlabs.info
Description: Make link echange with other websites automatically.<br /> Your website will automatically accept the websites that meet the requirements saved on options page.<br /> Some options: Backlink required, allow nofollow links, allow other anchor, allow other url, minimum pagerank, max. external links.
Version: 0.1.4
Author: Samuel Marian
Author URI: http://www.mlabs.info
License: GPL
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

	define('PLUGIN_DIR', dirname( __FILE__ ));
	define('PLUGIN_URL', plugin_dir_url( __FILE__ ));
	define('TPL_FILE', PLUGIN_DIR.'/form.html');

	if(is_admin()){
		require(PLUGIN_DIR.'/admin.php');
	}else{
		require(PLUGIN_DIR.'/front.php');
	}

	if(!function_exists('loadUrls')){
		function loadUrls(){
			global $wpdb;
			$settings = mysql_fetch_object(mysql_query(' SELECT option_value FROM  '.$wpdb->prefix.'options WHERE option_name="mlinkex" LIMIT 1'));
			$settings = unserialize($settings->option_value);
			
			if(file_exists(PLUGIN_DIR.'/link.html')){
				$links = file_get_contents(PLUGIN_DIR.'/links.html');
				$tpl = file_get_contents(PLUGIN_DIR.'/link.html');
				$full = 1;
			}else{
				$tpl = '<a href="%url%" title="%anchor%" rel="%rel%">%anchor%</a>';
			}
			$show_mlinkex = '';
			$sql = mysql_query('SELECT anchor,title,url,add_nofollow FROM  '.$wpdb->prefix.'mlinkex WHERE status="1"');
			while($rand = mysql_fetch_object($sql)){
				$item = $tpl;
				$item = str_replace('%anchor%', $rand->anchor, $item);
				$item = str_replace('%title%', $rand->title, $item);
				$item = str_replace('%url%', $rand->url, $item);
				$item = str_replace('%rel%', ($rand->add_nofollow == 1 || $settings['add_nofollow'] == 1) ? 'nofollow' : '', $item);
				
				$show_mlinkex .= $item;
			}
			if($full == 1){
				$links = str_replace('%add_link%', $settings['add_link'], $links);
				echo str_replace('%links%', $show_mlinkex, $links);
			}else{
				echo $show_mlinkex;
			}
		}
	}
	if(!function_exists('loadUrls_init')){
		function loadUrls_init(){
		  wp_register_sidebar_widget(1, ('Maribol WP Link Exchange'), 'loadUrls');
		}
	}
	
	add_action("plugins_loaded", "loadUrls_init");
	
?>