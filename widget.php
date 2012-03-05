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

if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

class ComprehensiveGoogleMap_Widget extends WP_Widget {

	var $maindesc = "A simple and intuitive, yet elegant fully documented Google map plugin that installs as a widget and a short code. The plugin is packed with useful features. Widget and shortcode enabled. Offers extensive configuration options for marker, controls, size, KML files, location by latitude/longitude, location by address, info window, directions, traffic/bike lanes and more.";

	function ComprehensiveGoogleMap_Widget() {
		$widget_ops = array('classname' => 'comprehensivegooglemap_widget', 'description' => __( $this->maindesc, 'kalisto') );
		$cops = array('width' => 570);
		$this->WP_Widget('comprehensivegooglemap', __('AZ :: Google Map', 'kalisto'), $widget_ops, $cops);

		if ( is_active_widget(false, false, $this->id_base, true) ) {
			
		}
	}


	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Google Map', 'kalisto') : $instance['title'], $instance, $this->id_base);

		$width = empty($instance['width']) ? 250 : $instance['width'];
		$height = empty($instance['height']) ? 250 : $instance['height'];
		$zoom = empty($instance['zoom']) ? '7' : $instance['zoom'];
		$latitude = empty($instance['latitude']) ? '' : $instance['latitude'];
		$longitude = empty($instance['longitude']) ? '' : $instance['longitude'];
		$maptype = empty($instance['maptype']) ? 'ROADMAP' : $instance['maptype'];
		$showmarker = empty($instance['showmarker']) ? "true" : $instance['showmarker'];
		$animation = empty($instance['animation']) ? 'DROP' : $instance['animation'];
		$infobubblecontent = empty($instance['infobubblecontent']) ? '' : $instance['infobubblecontent'];
		$addresscontent = empty($instance['addresscontent']) ? '' : $instance['addresscontent'];
		$showbike = empty($instance['showbike']) ? 'false' : $instance['showbike'];
		$showtraffic = empty($instance['showtraffic']) ? 'false' : $instance['showtraffic'];
		$showpanoramio = empty($instance['showpanoramio']) ? 'false' : $instance['showpanoramio'];
		$bubbleautopan = empty($instance['bubbleautopan']) ? 'true' : $instance['bubbleautopan'];
		$markerdirections = empty($instance['markerdirections']) ? 'true' : $instance['markerdirections'];
		$kml = empty($instance['kml']) ? '' : $instance['kml'];
		$hiddenmarkers = empty($instance['addmarkerlisthidden']) ? '' : $instance['addmarkerlisthidden'];
		$addmarkermashup = empty($instance['addmarkermashuphidden']) ? 'false' : $instance['addmarkermashuphidden'];

		$geomashupbubble = empty($instance['addmarkermashupbubble']) ? 'false' : $instance['addmarkermashupbubble'];

		$mapalign = empty($instance['mapalign']) ? 'center' : $instance['mapalign'];
		$panoramiouid = empty($instance['panoramiouid']) ? '' : $instance['panoramiouid'];
		$directionhint = empty($instance['directionhint']) ? 'false' : $instance['directionhint'];
		$language = empty($instance['language']) ? 'default' : $instance['language'];


		$controlOpts = array();
		$controlOpts['m_aptypecontrol'] = empty($instance['m_aptypecontrol']) ? "true" : $instance['m_aptypecontrol'];
		$controlOpts['pancontrol'] = empty($instance['pancontrol']) ? "true" : $instance['pancontrol'];
		$controlOpts['z_oomcontrol'] = empty($instance['z_oomcontrol']) ? "true" : $instance['z_oomcontrol'];
		$controlOpts['scalecontrol'] = empty($instance['scalecontrol']) ? "true" : $instance['scalecontrol'];
		$controlOpts['streetviewcontrol'] = empty($instance['streetviewcontrol']) ? "true" : $instance['streetviewcontrol'];
		$controlOpts['scrollwheelcontrol'] = empty($instance['scrollwheelcontrol']) ? "false" : $instance['scrollwheelcontrol'];


		echo $before_widget;

		if ( $title) {
			echo $before_title . $title . $after_title;
		}

		$id = md5(time().' '.rand());

		if ($addmarkermashup == 'true') {
			$hiddenmarkers = make_marker_geo_mashup();
		} else if ($addmarkermashup == 'false') {
			$hiddenmarkers = update_markerlist_from_legacy_locations($latitude, $longitude, $addresscontent, $hiddenmarkers);
		}

		cgmp_set_google_map_language($language);
		cgmp_google_map_init_scripts();

		$result = '';
		$result .= cgmp_draw_map_placeholder($id, $width, $height, $mapalign, $directionhint);
		echo $result;

		$map_settings = array();
		$map_settings['id'] = $id;
		$map_settings['zoom'] = $zoom;
		$map_settings['maptype'] = $maptype;
		$map_settings['bubbleautopan'] = $bubbleautopan;
		$map_settings['maptypecontrol'] = $controlOpts['m_aptypecontrol'];
		$map_settings['pancontrol'] = $controlOpts['pancontrol'];
		$map_settings['zoomcontrol'] = $controlOpts['z_oomcontrol'];
		$map_settings['scalecontrol'] = $controlOpts['scalecontrol'];
		$map_settings['streetviewcontrol'] = $controlOpts['streetviewcontrol'];
		$map_settings['scrollwheelcontrol'] = $controlOpts['scrollwheelcontrol'];
		$map_settings['markerlist'] = $hiddenmarkers;
		$map_settings['addmarkermashup'] = $addmarkermashup;
		$map_settings['geomashupbubble'] = $geomashupbubble;
		$map_settings['kml'] = cgmp_clean_kml($kml);
		$map_settings['showbike'] = $showbike;
		$map_settings['showtraffic'] = $showtraffic;
		$map_settings['showpanoramio'] = $showpanoramio;
		$map_settings['directionhint'] = $directionhint;
		$map_settings['panoramiouid'] = cgmp_clean_panoramiouid($panoramiouid);

		cgmp_map_data_injector(json_encode($map_settings));

		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['width'] = strip_tags($new_instance['width']);
		$instance['height'] = strip_tags($new_instance['height']);
		$instance['zoom'] = strip_tags($new_instance['zoom']);
		$instance['latitude'] = strip_tags($new_instance['latitude']);
		$instance['longitude'] = strip_tags($new_instance['longitude']);
		$instance['maptype'] = strip_tags($new_instance['maptype']);
		$instance['showmarker'] = strip_tags($new_instance['showmarker']);
		$instance['animation'] = strip_tags($new_instance['animation']);
		$instance['infobubblecontent'] = strip_tags($new_instance['infobubblecontent']);
		$instance['m_aptypecontrol'] = strip_tags($new_instance['m_aptypecontrol']);
		$instance['pancontrol'] = strip_tags($new_instance['pancontrol']);
		$instance['z_oomcontrol'] = strip_tags($new_instance['z_oomcontrol']);
		$instance['scalecontrol'] = strip_tags($new_instance['scalecontrol']);
		$instance['streetviewcontrol'] = strip_tags($new_instance['streetviewcontrol']);
		$instance['scrollwheelcontrol'] = strip_tags($new_instance['scrollwheelcontrol']);
		$instance['addresscontent'] = strip_tags($new_instance['addresscontent']);
		$instance['showbike'] = strip_tags($new_instance['showbike']);
		$instance['showtraffic'] = strip_tags($new_instance['showtraffic']);
		$instance['kml'] = strip_tags($new_instance['kml']);
		$instance['showpanoramio'] = strip_tags($new_instance['showpanoramio']);
		$instance['addmarkerlisthidden'] = strip_tags($new_instance['addmarkerlisthidden']);
		$instance['addmarkermashuphidden'] = strip_tags($new_instance['addmarkermashuphidden']);
		$instance['addmarkermashupbubble'] = strip_tags($new_instance['addmarkermashupbubble']);

		$instance['bubbleautopan'] = strip_tags($new_instance['bubbleautopan']);
		$instance['markerdirections'] = strip_tags($new_instance['markerdirections']);
		$instance['mapalign'] = strip_tags($new_instance['mapalign']);
		$instance['panoramiouid'] = strip_tags($new_instance['panoramiouid']);
		$instance['directionhint'] = strip_tags($new_instance['directionhint']);
		$instance['language'] = strip_tags($new_instance['language']);

		return $instance;
	}

	function form( $instance ) {

		$bools = array("Show" => "true", "Hide" => "false");
		$bools2 = array("Enable" => "false", "Disable" => "true");
		$bools3 = array("Enable" => "true", "Disable" => "false");
		$types = array("Roadmap"=>"ROADMAP", "Satellite"=>"SATELLITE", "Hybrid"=>"HYBRID", "Terrain" => "TERRAIN");
		$animations = array("Drop"=>"DROP", "Bounce"=>"BOUNCE");
		$aligns = array("Center"=>"center", "Right"=>"right", "Left" => "left");
		$languages = array("Default" => "default", "Arabic" => "ar", "Basque" => "eu", "Bulgarian" => "bg", "Bengali" => "bn", "Catalan" => "ca", "Czech" => "cs", "Danish" => "da", "English" => "en", "German" => "de", "Greek" => "el", "Spanish" => "es", "Farsi" => "fa", "Finnish" => "fi", "Filipino" => "fil", "French" => "fr", "Galician" => "gl", "Gujarati" => "gu", "Hindi" => "hi", "Croatian" => "hr", "Hungarian" => "hu", "Indonesian" => "id", "Italian" => "it", "Hebrew" => "iw", "Japanese" => "ja", "Kannada" => "kn", "Korean" => "ko", "Lithuanian" => "lt", "Latvian" => "lv", "Malayalam" => "ml", "Marathi" => "mr", "Dutch" => "nl", "Norwegian" => "no", "Oriya" => "or", "Polish" => "pl", "Portuguese" => "pt", "Romanian" => "ro", "Russian" => "ru", "Slovak" => "sk", "Slovenian" => "sl", "Serbian" => "sr", "Swedish" => "sv", "Tagalog" => "tl", "Tamil" => "ta", "Telugu" => "te", "Thai" => "th", "Turkish" => "tr", "Ukrainian" => "uk", "Vietnamese" => "vi", "Chinese (simpl)" => "zh-CN", "Chinese (tradi)" => "zh-TW");


		$title = isset($instance['title']) ? esc_attr($instance['title']) : 'Google Map';
		$width = isset($instance['width']) ? esc_attr($instance['width']) : '250';
		$height = isset($instance['height']) ? esc_attr($instance['height']) : '250';
		$zoom = isset($instance['zoom']) ? esc_attr($instance['zoom']) : '7';
		$latitude = isset($instance['latitude']) ? esc_attr($instance['latitude']) : '';
		$longitude = isset($instance['longitude']) ? esc_attr($instance['longitude']) : '';
        $maptype = isset($instance['maptype']) ? esc_attr($instance['maptype']) : 'ROADMAP';
		$showmarker = isset($instance['showmarker']) ? esc_attr($instance['showmarker']) : "true";
		$animation = isset($instance['animation']) ? esc_attr($instance['animation']) : 'DROP';

		$infobubblecontent = isset($instance['infobubblecontent']) ? esc_attr(htmlspecialchars_decode($instance['infobubblecontent'])) : "";
		$addresscontent = isset($instance['addresscontent']) ? esc_attr(htmlspecialchars_decode($instance['addresscontent'])) : "";

		$maptypecontrol = !empty($instance['m_aptypecontrol']) ? esc_attr($instance['m_aptypecontrol']) : 'true';
		$pancontrol = !empty($instance['pancontrol']) ? esc_attr($instance['pancontrol']) : 'true';
		$zoomcontrol = !empty($instance['z_oomcontrol']) ? esc_attr($instance['z_oomcontrol']) : 'true';
		$scalecontrol = !empty($instance['scalecontrol']) ? esc_attr($instance['scalecontrol']) : 'true';
		$streetviewcontrol = !empty($instance['streetviewcontrol']) ? esc_attr($instance['streetviewcontrol']) : 'true';
		$scrollwheelcontrol = !empty($instance['scrollwheelcontrol']) ? esc_attr($instance['scrollwheelcontrol']) : 'false';
		$showbike = !empty($instance['showbike']) ? esc_attr($instance['showbike']) : 'false';
		$showtraffic = !empty($instance['showtraffic']) ? esc_attr($instance['showtraffic']) : 'false';
		$showpanoramio = !empty($instance['showpanoramio']) ? esc_attr($instance['showpanoramio']) : 'false';
		$kml = !empty($instance['kml']) ? esc_attr($instance['kml']) : '';
		$hiddenmarkers = !empty($instance['addmarkerlisthidden']) ? esc_attr($instance['addmarkerlisthidden']) : '';
		$addmarkermashup = !empty($instance['addmarkermashuphidden']) ? esc_attr($instance['addmarkermashuphidden']) : 'false';
		$geomashupbubble = !empty($instance['addmarkermashupbubble']) ? esc_attr($instance['addmarkermashupbubble']) : 'false';

		$bubbleautopan = !empty($instance['bubbleautopan']) ? esc_attr($instance['bubbleautopan']) : 'true';
		$markerdirections = !empty($instance['markerdirections']) ? esc_attr($instance['markerdirections']) : 'true';
		$mapalign = !empty($instance['mapalign']) ? esc_attr($instance['mapalign']) : 'center';
		$panoramiouid = !empty($instance['panoramiouid']) ? esc_attr($instance['panoramiouid']) : '';
		$directionhint = !empty($instance['directionhint']) ? esc_attr($instance['directionhint']) : 'false';
		$language = !empty($instance['language']) ? esc_attr($instance['language']) : 'default';

		$hiddenmarkers = update_markerlist_from_legacy_locations($latitude, $longitude, $addresscontent, $hiddenmarkers);


		$title_template = file_get_contents(CGMP_PLUGIN_HTML."/form_title_template.plug");
		$template = file_get_contents(CGMP_PLUGIN_HTML."/form_body_template.plug");
		
		$template_values = array();
		
		$settings = array();

		$v = "title";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Title")); 
		$settings[] = array("type" => "input", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $title, "class" => "widefat", "style" => "width: 100% !important;"));

		$v = "width";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Width (px)")); 
		$settings[] = array("type" => "input@range", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $width, "class" => "widefat", "style" => "")); 


		$v = "height";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Height (px)")); 
		$settings[] = array("type" => "input@range", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $height, "class" => "widefat", "style" => "")); 

		$v = "latitude";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Latitude")); 
		$settings[] = array("type" => "input@range", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $latitude, "class" => "widefat", "style" => "")); 

		$v = "longitude";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Longitude")); 
		$settings[] = array("type" => "input@range", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $longitude, "class" => "widefat", "style" => "")); 

		$v = "zoom";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Zoom")); 
		$settings[] = array("type" => "input@range", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $zoom, "class" => "widefat", "style" => "")); 

		$v = "maptype";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Map type")); 
		$settings[] = array("type" => "select", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $maptype, "options" => $types)); 


		$v = "directionhint";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Directions Hint")); 
		$settings[] = array("type" => "select", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $directionhint, "options" => $bools3));

		$v = "language";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Map Language")); 
		$settings[] = array("type" => "select", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $language, "options" => $languages)); 


		$v = "showmarker";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Primary Marker")); 
		$settings[] = array("type" => "select", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $showmarker, "options" => $bools)); 

		
		$v = "animation";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Animation")); 
		$settings[] = array("type" => "select", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $animation, "options" => $animations)); 


		$v = "m_aptypecontrol";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "MapType")); 
		$settings[] = array("type" => "select", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $maptypecontrol, "options" => $bools)); 

		$v = "pancontrol";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Pan")); 
		$settings[] = array("type" => "select", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $pancontrol, "options" => $bools)); 


		$v = "z_oomcontrol";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Zoom")); 
		$settings[] = array("type" => "select", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $zoomcontrol, "options" => $bools)); 

		
		$v = "scalecontrol";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Scale")); 
		$settings[] = array("type" => "select", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $scalecontrol, "options" => $bools)); 

		$v = "streetviewcontrol";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "StreetView")); 
		$settings[] = array("type" => "select", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $streetviewcontrol, "options" => $bools)); 


		$v = "scrollwheelcontrol";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "ScrollWheel")); 
		$settings[] = array("type" => "select", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $scrollwheelcontrol, "options" => $bools3)); 

		$v = "infobubblecontent";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Content Text")); 
		$settings[] = array("type" => "input", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $infobubblecontent, "class" => "widefat", "style" => "width: 90% !important;"));


		$v = "addresscontent";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Address Text")); 
		$settings[] = array("type" => "input", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $addresscontent, "class" => "widefat", "style" => "width: 90% !important;"));


		$v = "showbike";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Bike Paths")); 
		$settings[] = array("type" => "select", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $showbike, "options" => $bools)); 

		$v = "showtraffic";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Traffic Info")); 
		$settings[] = array("type" => "select", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $showtraffic, "options" => $bools)); 

		$v = "showpanoramio";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Panoramio")); 
		$settings[] = array("type" => "select", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $showpanoramio, "options" => $bools)); 


		$v = "bubbleautopan";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Bubble Auto-Pan")); 
		$settings[] = array("type" => "select", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $bubbleautopan, "options" => $bools3)); 


		$v = "markerdirections";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Marker Directions")); 
		$settings[] = array("type" => "select", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $markerdirections, "options" => $bools3)); 

		$v = "kml";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "KML/GeoRSS")); 
		$settings[] = array("type" => "input", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $kml, "class" => "widefat", "style" => "width: 90% !important;"));

		$m = "addmarker";
		$settings[] = array("type" => "button", "token" => $m, "attr"=> array("id" => $this->get_field_id($m), "name" => $this->get_field_name($m), "value" => "Add Marker", "class" => "button-primary add-additonal-location", "style" => ""));


		$v = $m."input";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Location")); 
		$settings[] = array("type" => "input", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => '', "class" => "widefat marker-location-text default-marker-icon", "style" => ""));

		$v = $m."icons";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "")); 
		$settings[] = array("type" => "custom", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => "", "class" => "custom-icons-placeholder", "style" => ""));

		$v = $m."mashup";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Make this map a Marker Geo Mashup")); 
		$settings[] = array("type" => "geo", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => "", "class" => "marker-geo-mashup", "style" => ""));

		$v = $m."mashupbubble";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "")); 
		$settings[] = array("type" => "geobubble", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $geomashupbubble, "class" => "marker-bubble-geo-mashup", "style" => ""));


		$v = $m."mashuphidden";
		$settings[] = array("type" => "geohidden", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $addmarkermashup, "class" => "", "style" => ""));



		$v = $m."list";
		$settings[] = array("type" => "list", "token" => $v, "attr"=> array("id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "class" => "token-input-list", "style" => ""));

	
		$v = $v."hidden";
		$settings[] = array("type" => "hidden", "token" => $v, "attr"=> array("id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "class" => "", "value" => $hiddenmarkers, "style" => ""));


		$v = "mapalign";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "Alignment")); 
		$settings[] = array("type" => "select", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $mapalign, "options" => $aligns)); 

		$v = "panoramiouid";
		$settings[] = array("type" => "label", "token" => $v, "attr" => array("for" => $this->get_field_id($v), "value" => "User ID (Opt.)")); 
		$settings[] = array("type" => "input", "token" => $v, "attr"=> array("role" => $v, "id" => $this->get_field_id($v), "name" => $this->get_field_name($v), "value" => $panoramiouid, "class" => "widefat", "style" => "width: 85px !important;"));

		

		$template_values = cgmp_build_template_values($settings);

		global $global_fieldset_names;
		$title_template = cgmp_replace_template_tokens($global_fieldset_names, $title_template);
		$template = cgmp_replace_template_tokens($global_fieldset_names, $template);
		$template = cgmp_replace_template_tokens($template_values, $template);
		$title_template = cgmp_replace_template_tokens($template_values, $title_template);
		
		$res = "<div id='google-map-container-{$this->id}' class='widget-google-map-container'>
				<div id='slider'></div>
				{$title_template}
				{$template}
				</div>";
		echo $res;
	}
}
?>
