<?php
/*
Plugin Name: Comprehensive Google Map Plugin
Plugin URI: http://initbinder.com/comprehensive-google-map-plugin
Description: A simple and intuitive, yet elegant and fully documented Google map plugin that installs as a widget and a short code. The plugin is packed with useful features. Widget and shortcode enabled. Offers extensive configuration options for markers, over 250 custom marker icons, marker Geo mashup, controls, size, KML files, location by latitude/longitude, location by address, info window, directions, traffic/bike lanes and more. 
Version: 6.0.23
Author: Alexander Zagniotov
Author URI: http://initbinder.com
License: GPLv2


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

if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

define('CGMP_GOOGLE_API_URL', 'http://maps.googleapis.com/maps/api/js?libraries=panoramio&sensor=false');

define('CGMP_VERSION', '6.0.23');
define('CGMP_NAME', 'cgmp');
define('CGMP_SEP', '{}');
define('CGMP_DB_OPTION_NAME', 'cgmp_marker_locations');
define('CGMP_DB_POST_COUNT', 'cgmp_total_published_posts');
define('CGMP_DB_PUBLISHED_POST_MARKERS', 'cgmp_published_post_markers');
define('CGMP_DB_SELECTED_LANGUAGE', 'cgmp_selected_language');
define('CGMP_DB_SETTINGS_BUILDER_LOCATION', 'cgmp_settings_builder_location');
define('CGMP_HOOK', 'cgmp-documentation');
define('CGMP_PLUGIN_BOOTSTRAP', __FILE__ );
define('CGMP_PLUGIN_DIR', dirname( __FILE__ ));
define('CGMP_PLUGIN_INCLUDE_DIR', CGMP_PLUGIN_DIR.'/include');
define('CGMP_PLUGIN_URI', plugin_dir_url( __FILE__ ));
define('CGMP_PLUGIN_ASSETS_URI', CGMP_PLUGIN_URI.'assets');
define('CGMP_PLUGIN_ASSETS_DIR', CGMP_PLUGIN_DIR.'/assets');
define('CGMP_PLUGIN_CSS', CGMP_PLUGIN_ASSETS_URI . '/css');
define('CGMP_PLUGIN_CSS_DIR', CGMP_PLUGIN_ASSETS_DIR . '/css');
define('CGMP_PLUGIN_IMAGES', CGMP_PLUGIN_CSS . '/images');
define('CGMP_PLUGIN_IMAGES_DIR', CGMP_PLUGIN_CSS_DIR . '/images');
define('CGMP_PLUGIN_JS', CGMP_PLUGIN_ASSETS_URI . '/js');
define('CGMP_PLUGIN_HTML', CGMP_PLUGIN_DIR . '/assets/html');

define('CGMP_FIELDSETNAME_WIDGETTITLE', 'Widget Title');
define('CGMP_FIELDSETNAME_BASICSETTINGS', 'Basic Settings');
define('CGMP_FIELDSETNAME_MARKER_CONFIG', 'Map Markers');
define('CGMP_FIELDSETNAME_MARKER_INFOBUBBLE', 'Map Marker Info Bubbles');
define('CGMP_FIELDSETNAME_DESTINATION_ADDR_INFO', 'KML/Geo RSS');
define('CGMP_FIELDSETNAME_BIKE_TRAFFIC_PATH', 'Custom Overlays');
define('CGMP_FIELDSETNAME_CONTROL_CONFIG', 'Map Controls');
define('CGMP_FIELDSETNAME_KML', 'KML/GeoRSS');
define('CGMP_FIELDSETNAME_PANORAMIO', 'Panoramio Library');

$global_fieldset_names = array();
$global_fieldset_names["LEGEND_BASIC_SETTINGS"] = CGMP_FIELDSETNAME_BASICSETTINGS;
$global_fieldset_names["LEGEND_MARKER"] = CGMP_FIELDSETNAME_MARKER_CONFIG;
$global_fieldset_names["LEGEND_CONTROL"] = CGMP_FIELDSETNAME_CONTROL_CONFIG;
$global_fieldset_names["LEGEND_INFOBUBBLE"] = CGMP_FIELDSETNAME_MARKER_INFOBUBBLE;
$global_fieldset_names["LEGEND_ADDRESS"] = CGMP_FIELDSETNAME_DESTINATION_ADDR_INFO;
$global_fieldset_names["LEGEND_WIDGETTITLE"] = CGMP_FIELDSETNAME_WIDGETTITLE;
$global_fieldset_names["LEGEND_BIKE_AND_TRAFFIC"] = CGMP_FIELDSETNAME_BIKE_TRAFFIC_PATH;
$global_fieldset_names["LEGEND_KML"] = CGMP_FIELDSETNAME_KML;
$global_fieldset_names["LEGEND_PANORAMIO"] = CGMP_FIELDSETNAME_PANORAMIO;

$global_all_map_json_data = array();

$doc_url = get_option('siteurl')."/wp-admin/admin.php?page=cgmp-documentation";
$global_fieldset_names["DOC_URL"] = $doc_url;

$notices = '<span style="font-size: 9px;"><a href="'.$doc_url.'">Documentation</a> | <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=CWNZ5P4Z8RTQ8">Support</a></span>';
$global_fieldset_names["FOOTER_NOTICES"] = $notices;

require_once (CGMP_PLUGIN_DIR . '/functions.php');
require_once (CGMP_PLUGIN_DIR . '/widget.php');
require_once (CGMP_PLUGIN_DIR . '/shortcode.php');
require_once (CGMP_PLUGIN_DIR . '/metabox.php');
require_once (CGMP_PLUGIN_DIR . '/menu.php');
require_once (CGMP_PLUGIN_DIR . '/head.php');

//add_action('the_posts', 'is_map_shortcode_present');
add_action('init', 'cgmp_google_map_init_global_js');
add_action('init', 'cgmp_load_plugin_textdomain');
add_action('admin_notices', 'cgmp_show_message');
add_action('admin_init', 'cgmp_google_map_admin_add_style');
add_action('admin_init', 'cgmp_google_map_admin_add_script');

add_action('admin_menu', 'cgmp_google_map_plugin_menu');
add_action('widgets_init', create_function('', 'return register_widget("ComprehensiveGoogleMap_Widget");'));
add_shortcode('google-map-v3', 'cgmp_shortcode_googlemap_handler');
add_filter('widget_text', 'do_shortcode');

add_action('wp_head', 'cgmp_google_map_deregister_scripts', 200);

register_activation_hook( CGMP_PLUGIN_BOOTSTRAP, 'cgmp_extract_markers_from_published_posts');

add_action('publish_post', 'cgmp_invalidate_published_post_marker' );

add_filter( 'plugin_row_meta', 'cgmp_plugin_row_meta', 10, 2 );
?>
