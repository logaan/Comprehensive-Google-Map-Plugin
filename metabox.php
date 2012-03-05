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

$setting_builder_location = get_option(CGMP_DB_SETTINGS_BUILDER_LOCATION);

if (isset($setting_builder_location) && $setting_builder_location == "true") {
	add_action('admin_menu', 'cgmp_google_map_meta_boxes');
}


if ( !function_exists('cgmp_google_map_meta_boxes') ):
function cgmp_google_map_meta_boxes() {
		$id = "google_map_shortcode_builder";
		$title = "AZ :: Google Map Shortcode Builder"; 
		$context = "normal";
		add_meta_box($id, $title, 'cgmp_render_shortcode_builder_form', 'post', $context, 'high');
		add_meta_box($id, $title, 'cgmp_render_shortcode_builder_form', 'page', $context, 'high');
}
endif;


if ( !function_exists('cgmp_render_shortcode_builder_form') ):
function cgmp_render_shortcode_builder_form() {

		include_once(CGMP_PLUGIN_INCLUDE_DIR.'/_shortcode_builder_form.php');

  		$res = "<div id='google-map-container-metabox'>
				{$template}
		</div>
		<input type='button' onclick='return sendShortcodeToEditor(\"google-map-container-metabox\");' 
		class='button button-highlighted' tabindex='4' value='Send to Editor' id='send-to-editor' name='send-to-editor' />";
		echo $res;
}
endif;

?>
