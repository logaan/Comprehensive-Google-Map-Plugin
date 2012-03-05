<?php
/*
Copyright (C) 2011  Alexander Zagniotov

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( !function_exists('cgmp_google_map_plugin_menu') ):
      function cgmp_google_map_plugin_menu() {
      		$hook = add_menu_page("Comprehensive Google Map", 'Google Map', 'activate_plugins', CGMP_HOOK, 'cgmp_parse_menu_html', CGMP_PLUGIN_IMAGES .'/google_map.png');
	  		add_action('admin_print_scripts-'.$hook, 'cgmp_google_map_tab_script');
			$hook = add_submenu_page(CGMP_HOOK, 'Shortcode Builder', 'Shortcode Builder', 'activate_plugins', 'cgmp-shortcodebuilder', 'cgmp_shortcodebuilder_callback' );
			add_action('admin_print_scripts-'.$hook, 'cgmp_google_map_tab_script');
			$hook = add_submenu_page(CGMP_HOOK, 'Settings', 'Settings', 'activate_plugins', 'cgmp-settings', 'cgmp_settings_callback' );
		   	add_action('admin_print_scripts-'.$hook, 'cgmp_google_map_tab_script');
	  }
endif;

if ( !function_exists('cgmp_settings_callback') ):

	function cgmp_settings_callback() {

		if (!current_user_can('activate_plugins'))  {
             	wp_die( __('You do not have sufficient permissions to access this page.') );
		}

		if (isset($_POST['cgmp-save-settings']))  {
				update_option(CGMP_DB_SETTINGS_BUILDER_LOCATION, $_POST['builder-under-post']);
				cgmp_show_message("Settings updated successfully!");
		}

		$setting_builder_location = get_option(CGMP_DB_SETTINGS_BUILDER_LOCATION);

		$yes_display_radio_btn = "";
		$no_display_radio_btn = "checked='checked'";
		if (isset($setting_builder_location) && $setting_builder_location == "true") {
			$no_display_radio_btn = "";
			$yes_display_radio_btn = "checked='checked'";
		}

		$res = "<form action='' name='' id='' method='post'>
				<div id='google-map-container-settings' style='margin-top: 20px'>
				<fieldset>
				<legend>Shortcode Builder Location</legend>
				<table cellspacing='0' cellpadding='0' border='0'>
					<tbody>
						<tr>
							<td>Dispay shortcode builder under post/page HTML editor?</td>
						</tr>
						<tr>
							<td>
								<label id='yes-display-label' for='yes-display'>Yes</label><input type='radio' id='yes-display' name='builder-under-post' value='true' {$yes_display_radio_btn}/>&nbsp;
								<label id='no-display-label' for='no-display'>No</label><input type='radio' id='no-display' name='builder-under-post' value='false' {$no_display_radio_btn} /></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			</div><br /><br />
			<input type='submit' onclick='' class='button-primary' tabindex='4' value=' Save Settings ' id='cgmp-save-settings' name='cgmp-save-settings' />
		</form>";


		$template = file_get_contents(CGMP_PLUGIN_HTML."/settings.plug");
		$template_values = array();
        $template_values["SETTINGS_TOKEN"] = $res;

        $template = cgmp_replace_template_tokens($template_values, $template);
		echo $template;
	}

endif;


if ( !function_exists('cgmp_shortcodebuilder_callback') ):

	function cgmp_shortcodebuilder_callback() {

		if (!current_user_can('activate_plugins'))  {
             	wp_die( __('You do not have sufficient permissions to access this page.') );
        }

		include_once(CGMP_PLUGIN_INCLUDE_DIR.'/_shortcode_builder_form.php');

		$res = 
				"<input type='button' onclick='return displayShortcodeInPopup(\"google-map-container-metabox\");' 
		class='button-primary' tabindex='4' value=' GENERATE SHORTCODE ' id='send-to-editor' name='send-to-editor' /><br />
				<div id='google-map-container-metabox' style='margin-top: 20px'>
				{$template}
		</div>
		<input type='button' onclick='return displayShortcodeInPopup(\"google-map-container-metabox\");' 
		class='button-primary' tabindex='4' value=' GENERATE SHORTCODE ' id='send-to-editor' name='send-to-editor' />";


		$template = file_get_contents(CGMP_PLUGIN_HTML."/shortcodebuilder.plug");

		$template_values = array();
        $template_values["SHORTCODEBUILDER_TOKEN"] = $res;

        $template = cgmp_replace_template_tokens($template_values, $template);
        echo $template;


		//echo $res;

	}
endif;


if ( !function_exists('cgmp_parse_menu_html') ):
function cgmp_parse_menu_html() {
      if (!current_user_can('activate_plugins'))  {
                wp_die( __('You do not have sufficient permissions to access this page.') );
        }

	$template = file_get_contents(CGMP_PLUGIN_HTML."/documentation.plug");
        $template_content = file_get_contents(CGMP_PLUGIN_HTML."/form_body_template.plug");

        $template_values = array();
        $template_values["LABEL_WIDTH"] = "<b>Width</b>:";
        $template_values["INPUT_WIDTH"] = "The width of the map placeholder DIV in pixels";
        $template_values["LABEL_HEIGHT"] = "<b>Height</b>:";
        $template_values["INPUT_HEIGHT"] = "The height of the map placeholder DIV in pixels";
        $template_values["LABEL_LATITUDE"] = "<b>Latitude</b>:";
        $template_values["INPUT_LATITUDE"] = "Together with Longitude, makes a geographic coordinate of a location displayed on the Google map. The latitude coordinate value is measured in degrees";
        $template_values["LABEL_LONGITUDE"] = "<b>Longitude</b>:";
        $template_values["INPUT_LONGITUDE"] =  "Together with Latitude, makes a geographic coordinate of a location displayed on the Google map. The longitude coordinate value is measured in degrees";
        $template_values["LABEL_ZOOM"] = "<b>Zoom</b>:";
        $template_values["INPUT_ZOOM"] = "Each map also contains a zoom level, which defines the resolution of the current view. Zoom levels between 0 (the lowest zoom level, in which the entire world can be seen on one map) to 19 (the highest zoom level, down to individual buildings) are possible within the normal maps view. Zoom levels vary depending on where in the world you're looking, as data in some parts of the globe is more defined than in others. Zoom levels up to 20 are possible within satellite view. Please note: when using KML, the KML zoom needs to be set within the KML file. Zoom config option does not affect zoom of the map generated from KML.";
        $template_values["LABEL_MAPTYPE"] = "<b>Map&nbsp;type</b>:";
        $template_values["SELECT_MAPTYPE"] = "There are many types of maps available within the Google Maps. In addition to the familiar 'painted' road map tiles, the Google Maps API also supports other maps types. The following map types are available in the Google Maps API:
ROADMAP displays the default road map view, SATELLITE displays Google Earth satellite images, HYBRID displays a mixture of normal and satellite views, TERRAIN displays a physical map based on terrain information.";
		$template_values["LABEL_LANGUAGE"] = "<b>Map&nbsp;Language</b>";
		$template_values["SELECT_LANGUAGE"] = "The Google Maps API uses the browser's preferred language setting when displaying textual information such as the names for controls, copyright notices, driving directions and labels on maps. In most cases, this is preferable; you usually do not wish to override the user's preferred language setting. However, if you wish to change the Maps API to ignore the browser's language setting and force it to display information in a particular language, you can by selecting on of the available languages in this setting";
        $template_values["LABEL_SHOWMARKER"] = "<b>Primary&nbsp;Marker</b>";
        $template_values["SELECT_SHOWMARKER"] = "If a map is specified, the marker is added to the map upon construction. Note that the position must be set for the marker to display."; 
        $template_values["LABEL_ANIMATION"] = "<b>Animation</b>";
        $template_values["SELECT_ANIMATION"]    = "Animations can be played on a primary marker. Currently two types of animations supported: BOUNCE makes marker to bounce until animation is stopped, DROP makes primary marker to fall from the top of the map ending with a small bounce.";
        $template_values["LABEL_M_APTYPECONTROL"] = "<b>MapType</b>";
        $template_values["SELECT_M_APTYPECONTROL"] = "The MapType control lets the user toggle between map types (such as ROADMAP and SATELLITE). This control appears by default in the top right corner of the map";
	$template_values["LABEL_PANCONTROL"] = "<b>Pan</b>";
        $template_values["SELECT_PANCONTROL"] = "The Pan control displays buttons for panning the map. This control appears by default in the top left corner of the map on non-touch devices. The Pan control also allows you to rotate 45° imagery, if available.";
        $template_values["LABEL_Z_OOMCONTROL"] = "<b>Zoom</b>";
        $template_values["SELECT_Z_OOMCONTROL"] = "The Zoom control displays a slider (for large maps) or small '+/-' buttons (for small maps) to control the zoom level of the map. This control appears by default in the top left corner of the map on non-touch devices or in the bottom left corner on touch devices.";
        $template_values["LABEL_SCALECONTROL"] = "<b>Scale</b>"; 
        $template_values["SELECT_SCALECONTROL"] = "The Scale control displays a map scale element. This control is not enabled by default.";
        $template_values["LABEL_STREETVIEWCONTROL"] = "<b>StreetView</b>";
		$template_values["SELECT_STREETVIEWCONTROL"] = "The Street View control contains a Pegman icon which can be dragged onto the map to enable Street View. This control appears by default in the top left corner of the map";

		$template_values["LABEL_SCROLLWHEELCONTROL"] = "<b>ScrollWheel</b>";
        $template_values["SELECT_SCROLLWHEELCONTROL"] = "The Scroll Wheel control enables user to zoom in/out on mouse wheel scroll. This setting has 'disable' setting by default";


        $template_values["LABEL_INFOBUBBLECONTENT"] = "<b>Content&nbsp;Text</b>"; 
        $template_values["INPUT_INFOBUBBLECONTENT"] = "Text to be displayed inside info bubble (info window).";

        $template_values["LABEL_ADDRESSCONTENT"] = "<b>Address&nbsp;Text</b>"; 
        $template_values["INPUT_ADDRESSCONTENT"] = "Geographical gestination address string. The address supersedes longitude and latitude configuration. If the address provided cannot be parsed (eg: invalid address) by Google, the map will display error message in the info bubble over default location (New York, USA). Please note, address configuration *supersedes* latitude/longitude settings";

        $template_values["LABEL_SHOWBIKE"] = "<b>Bike&nbsp;Paths</b>";
        $template_values["SELECT_SHOWBIKE"] = "A layer showing bike lanes and paths as overlays on a Google Map.";
        $template_values["LABEL_SHOWTRAFFIC"] = "<b>Traffic&nbsp;Info</b>";
        $template_values["SELECT_SHOWTRAFFIC"] = "A layer showing vehicle traffic as overlay on a Google Map.";
        $template_values["LABEL_KML"] = "<b>KML/GeoRSS&nbsp;URL</b>";
		$template_values["INPUT_KML"] = "KML is a file format used to display geographic data in an earth browser, such as Google Earth, Google Maps, and Google Maps for mobile. A KML file is processed in much the same way that HTML (and XML) files are processed by web browsers. Like HTML, KML has a tag-based structure with names and attributes used for specific display purposes. Thus, Google Earth and Maps act as browsers for KML files. Please note, KML configuration *supersedes* address and latitude/longitude settings";
		$template_values["LABEL_ADDMARKERINPUT"] = "<b>Location</b>";
		$template_values["INPUT_ADDMARKERINPUT"] = "You can eneter either latitude and longitude, comma seperated or a full geographical address. The generated marker will have an info bubble attached to it, with marker's address as a bubble content. If latitude/longitude was provided as a marker location, the bubble content will contain location geographical address instead of the latitude/longitude. You can enter either latitude/longitude seperated by comma, or a fully qualified geographical address. You can also select a custom icon for your marker. If none is selected, default Google marker icon is used - the red pin with black dot. Please note that markers do not support animation at the moment.";
		$template_values["BUTTON_ADDMARKER"] = "";
		$template_values["CUSTOM_ADDMARKERICONS"] = "";
		$template_values["LIST_ADDMARKERLIST"] = "";
		$template_values["HIDDEN_ADDMARKERLISTHIDDEN"] = "";

		$template_values["GEO_ADDMARKERMASHUP"] = "";


		$template_values["GEOBUBBLE_ADDMARKERMASHUPBUBBLE"] = "When selecting a marker Geo mashup, you are also given an option to select what will appear in the marker info bubble window when marker is clicked. There are two options: to display marker's address and latitude/longitude or to display marker's original blog post title that is linked to the post and few words from post content as an excerpt. If the original blog post already has an excerpt set, then the latter will be used for the info bubble content.";
		$template_values["GEOHIDDEN_ADDMARKERMASHUPHIDDEN"] = "";
		$template_values["LABEL_ADDMARKERMASHUP"] = "If selected, the generated map will aggregate all markers from other maps created by you in your public published posts. In other words, you get a Geo marker mashup in one map! At the moment, the mashup does not include markers from maps on pages and widgets, posts ONLY. When Geo mashup is enabled, the KML and custom marker sections become hidden, in order to reduce the confusion for the user.";


		$template_values["LABEL_SHOWPANORAMIO"] = "<b>Panoramio</b>";
		$template_values["SELECT_SHOWPANORAMIO"] = "Panoramio (http://www.panoramio.com) is a geolocation-oriented photo sharing website. Accepted photos uploaded to the site can be accessed as a layer in Google Earth and Google Maps, with new photos being added at the end of every month. The site's goal is to allow Google Earth users to learn more about a given area by viewing the photos that other users have taken at that place.";


		$template_values["LABEL_BUBBLEAUTOPAN"] = "<b>Bubble&nbsp;Auto-Pan</b>";
		$template_values["SELECT_BUBBLEAUTOPAN"] = "Enables bubble auto-pan on marker click. By default, the info bubble will pan the map so that it is fully visible when it opens.";

		$template_values["LABEL_MAPALIGN"] = "<b>Alignment</b>";
		$template_values["SELECT_MAPALIGN"] = "Controls alignment of the generated map on the screen: LEFT, RIGHT or CENTER. Whats actually aligned is the placeholder DIV HTML element which wraps the generated map.";

		$template_values["LABEL_DIRECTIONHINT"] = "<b>Direction&nbsp;Hint</b>";
		$template_values["SELECT_DIRECTIONHINT"] = "Hint message displayed above the map, telling users if they want to get directions, they should click on map markers. ATM its in English, sorry :( Localization will come soon!";


		$template_values["LABEL_PANORAMIOUID"] = "<b>User&nbsp;ID&nbsp;(Opt.)</b>";
		$template_values["INPUT_PANORAMIOUID"] = "If specified, the Panoramio photos displayed on the map, will be filtered based on the specified user ID";

		$template_values["FOOTER_NOTICES"] = "";

	global $global_fieldset_names;
        $template_content = cgmp_replace_template_tokens($global_fieldset_names, $template_content);
        $template_content = cgmp_replace_template_tokens($template_values, $template_content);

        $template_values = array();
        $template_values["DOCUMENTATION_TOKEN"] = $template_content;

        $template = cgmp_replace_template_tokens($template_values, $template);
        echo $template;

}	
endif;

?>
