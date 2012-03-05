=== Comprehensive Google Map Plugin ===
Contributors: alexanderzagniotov
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CWNZ5P4Z8RTQ8
Tags: google map, google map widget, google map short code, google map short code, map widget, map short code, fusion tables, google fusion tables, google fusion queries, fully documented, marker, controls, size, KML files, location by latitude/longitude, location by address, info window, directions, traffic/bike lanes, cross browser, google maps v3, google, multiple markers, panoramio photos, marker icons, custom marker icons, geo, geo mashup, marker geo mashup
Requires at least: 3.2.1
Tested up to: 3.3.1
Stable tag: 6.0.23

A simple and intuitive,  yet elegant and fully documented Google map plugin that installs as a widget and a short code.

== Description ==

A simple and intuitive, yet elegant and fully documented Google map plugin that installs as a widget and a short code. No limited plugin editions or limited functionality! This is the full version of this free and premium plugin, which comes packed with useful features: 

* Intuitive and user friendly interface, which makes it very easy to configure Google map. No complex configuration options. Facebook style interactive interface for managing multiple map markers
* Over 250 amazing custom marker icons to choose from!
* Aggregate all your post markers in one map - Marker Geo Mashup!
* Help tooltips enabled, which makes your configuration hassle free
* Google-like directions: driving and walking, distance shown in miles or KM, avoid tolls and highways options, direction print functionality
* Info window can display marker's street view within itself.
* Plugin is fully documented. If help tooltips are not enough, you can always refer to the full documentation about each of the settings
* Short-code builder available, which integrated with post/page editor. In other words, you do not need
manually to type the short-code in the editor (But you can if you want to).
* The plugin auto generates unique ID for each map. In other words - unlimited maps! You do not need to specify and maintain unique map
IDs explicitly when dealing with multiple maps on the same post/page.
* The plugin offers extensive configuration options for Google map marker, controls, size, KML files, location by latitude/longitude, location by address, info windows, traffic/bike lanes and more!
* Support for custom overlays: Panoramio photos on the map (http://www.panoramio.com/), bicycle lanes and traffic info.
* User can get directions to the locations on the map (markers)
* Widget enabled.
* Cross browser compatible
* Loads geographic markup from a KML, KMZ or GeoRSS file hosted remotely.

If you liked the plugin, you can join the <a href="http://www.facebook.com/pages/Comprehensive-Google-Map-Plugin/180316032076503" target="_blank">Comprehensive Google Map Fan Page</a> on Facebook. 
 

Please note: 
Although I try my best to release a bug-free code, one or two may slip through. It would be so cool and I would really appreciate it if you would report any bugs to me first at http://initbinder.com/comprehensive-google-map-plugin, before reporting the plugin to be broken. I am quick to respond. 
Thanks!

Licenses:
The plugin uses wonderful custom marker icons from the <a href="http://mapicons.nicolasmollet.com" target="_blank">Maps Icons Collection</a> <img src="http://mapicons.nicolasmollet.com/wp-content/uploads/2011/03/miclogo-88x31.gif" border="0" /> project by Nicolas Mollet.


== Installation ==

Install this plugin by downloading and unzipping the ZIP archive into your plugins directory (/wp-content/plugins). Alternatively, you can upload the ZIP archive using Wordpress upload function. Activate the plugin in order to start using it.

To use the widget, simply drag the 'AZ :: Google Map' widget into a sidebar. Please note, your theme has to be widget-enabled. To change the styles of the contact form, open style.css file in the plugin editor in Wordpress. The short code builder can be found under the post/page editor. To access documentation please find "Google Map" link, under the "Settings" in your Wordpress admin panel.

Do you have a question or a feature request? Sure, drop me a line here: http://initbinder.com/comprehensive-google-map-plugin

== Frequently Asked Questions ==

1. Where can I find the short code builder?<br />
After plugin installation look for the 'Google Map' menu item on the left hand side of your WP admin. The 'Shortcode builder' item is just there. After generating the short code, copy the contents of the popup and paste int your post/page.

2. How fast can you have a look at my bug?<br />
Well, I always check emails on the go, so my response times are amazingly short and fast. I always try to reply. Having said that, not always I can dive into the code. It can take me up to a few hours when I reach my laptop and Internet connection :)

3. I have dragged/scrolled/pulled my map to a direction, messed up my zoom view, how can I get get all my markers in view again with the original zoom?<br />
Just click once somewhere on the map

4. The map appears empty with "loading" image or just gray square why is that?
Please check the following when adding marker locations: 
[a] In the shortcode builder, did you click the 'Add Marker' button before clicking 'Send to Editor'?
[b] In the widget, did you click the 'Add Marker' button before clicking 'Save'?
Please revisit and reconfigure your widget or shortcode configuration. The map requires at least one marker location to be added.

== Screenshots ==

1. Widget editing interface.
2. Help tooltip in action
3. Documentation page
4. Shortcode builder page
5. Generated short code in the editor
6. Sliders
7. Facebook style interactive interface for managing multiple map markers and custom icons
8. Direction panel
9. Markers info window with the direction and street view
10. Street view in the marker's infer window
11. When having marker Geo mashup, you can display in the info bubble marker's original post and post content excerpt instead of normally address and lat/long

== Changelog ==

= 6.0.23 =
* Screenshot update
* Enhancement: Added settings screen. Now user can control whether display short code builder under the post/page editor.
* Enhancement: Replaced all native alert popups with JS popups

= 6.0.22 =
* Enhancement: Replaced native browser popup with jQuery popup when generating short code.  
* Enhancement: User error messages refined.
* Removed document.ready from map generating logic

= 6.0.21 =
* Enhancement: Text widget bow can parse the map short code
* Enhancement: Client JS scripts now loaded on demand if widget or short code are active. In other words, page that does not contain map won't load the JS
* Enhancement: Added setting to set the map language by adding the 'language' to the Google map API

= 6.0.20 =
* Enhancement: Important: the short code builder is now located on its dedicated page instead of under post/page editor. After plugin installation look for the 'Google Map' menu item on the left hand side of your WP admin. This makes sure that post/page edit page is loaded quicker. The downside for now, is that you have to manually copy the generated shortage into your target page/post.
* Enhancement: All JS is now loaded minified, including plugin admin side scripts
* Enhancement: Revisited and cleaned up plugin admin JS
* Enhancement: Removed explicit call to 'jquery-core' module when WP admin is active
* Enhancement: Not loading client side JS and Google API scripts in WP admin anymore, only on client side
* Enhancement: Accepting GIF and JPG/JPEG files as custom marker icons (in addition to PNG)


= 6.0.19 =
* Reverted Injecting Google API on the client side. Causes problems in Opera on Mac

= 6.0.18 =
* Enhancement: Added a check before creating Google object, whether the map DIV placeholder exists
* Enhancement: Refined user error dialog messages
* Enhancement: Got rid of the sliders until further notice. Too much overhead for only 3 sliders

= 6.0.17 =
* Enhancement: Injecting Google API on the client side. This is to workaround the problem when param sensor is missing due to plugins like Better WP Security.

= 6.0.16 =
* Enhancement: Added documentation FAQ section in the plugin documentation
* Enhancement: Added extra plugin row meta links
* Enhancement: Not pushing JSON object to footer anymore. Some users experienced problems with how it was done.

= 6.0.15 =
* Enhancement: Added check for GMap2 object from Google API v2 to identify conflicts with API v3
* Enhancement: Some code cleanup
* Enhancement: Refined user error dialog messages
* Enhancement: Replacing '&amp;' with just '&'
* Bug: Lower-casing KML URLs

= 6.0.14 =
* Enhancement: Forcing map data JSON objects to be written into the footer area  
* Enhancement: Added config option to display direction hint above the map. ATM its in English, sorry :( Localisation will come soon!
* Enhancement: Refined user error dialog messages

= 6.0.13 =
* Reverted 6.0.12 -  sorry for the inconvenience 

= 6.0.12 =
* Enhancement: Forcing map data JSON objects to be written into the footer area  
* Enhancement: Added config option to display direction hint above the map. ATM its in English, sorry :( Localisation will come soon!
* Enhancement: Refined user error dialog messages

= 6.0.11 =
* Enhancement: User informative error messages around KML functionality 
* Enhancement: KML tooltip refinement

= 6.0.10 =
* Enhancement: When Google API cannot be reached, displaying popup warning dialog
* Enhancement: Displaying "loading" image when processing and loading the map
* Reverted: Help message above the map placeholder

= 6.0.9 =
* Enhancement: Not injecting anymore JS business logic to the client side. Instead injecting JSON object with map data.
* Enhancement: Plugin JS business logic now running within its own eco system. It knows how to read the above JSON object and pass the data to Google API. This way is safer against JS clash errors.
* Enhancement: Not displaying Lat/Long in info bubble anymore when Geo address provided.
* Enhancement: Added help message above the map placeholder that says to click on the markers if one needs directions
* Enhancement: Directions CSS tweak

= 6.0.8 =
* Bug: PHP notices due to wrong variable naming.

= 6.0.7 =
* Bug: Removed redundant call to JS timeout()

= 6.0.6 =
* Bug: New lines and carriage returns broke the JSON

= 6.0.5 =
* Bug: Single quotes in post titles broke the JSON

= 6.0.4 =
* Enhancement: Not using document.ready to wrap the Google map JS code anymore.
* Enhancement: Trying to apply workaround against Better WP Security (Reverted)
* Enhancement: Documentation refinement
* Enhancement: Stripping HTML tags from KML and Panoramio user ID fields
* Reverted loading plugins crypts on demand. It broke short codes in text widgets.

= 6.0.3 =
* Bug: Broken JSON
* Enhancement: Trying to apply workaround against Better WP Security's param stripping
* Enhancement: Documentation refinement

= 6.0.2 =
* Enhancement: When choosing marker geo mashup, user can now select what to display in the info bubble of the markers: Geo address or title and link to the marker's blog post (Check the screenshots)
* Enhancement: Zoom on mouse wheel scroll added as a config option
* Enhancement: Documentation refinement
* Enhancement: Making sure that Google API and plugin scripts only loaded if widgets and/or short codes are active
* Enhancement: Added notifications for users to let them know whether they: (a) have specified un-parsable by Google map locations or (b) have not provided locations at all
* Bug (unreported): When one of the provided locations was un-parsable by Google, the map was stopping from generation.

= 6.0.1 =
* Enhancement: Disabled zooming on mouse scroll
* Enhancement: Some CSS fixes in directions
* Enhancement: Using jQuerynoConflict();. Renamed all 'jQuery' into 'jQueryCgmp'

= 6.0.0 =
* Enhancement: Added marker Geo mashup option
* Enhancement: Documentation revisisted
* Enhancement: directions.css and override.css now merged into style.css and moved into the root of the plugin home directory so it can be accessible via WP plugin editor
* Bug: Preventing from marker location text field to be sent to editor

= 5.0.3 =
* Reverted: Making sure that Google API and plugin scripts only loaded if widgets and/or short codes are active. Apparently it started causing problems to some people. Need to do more testing.

= 5.0.2 =
* Enhancement: Some clarification to documentation
* Enhancement: Removing duplicates from the list of marker addresses
* Enhancement: Making sure that Google API and plugin scripts only loaded if widgets and/or short codes are active
* Enhancement: Added overflow: visible to direction button for IE

= 5.0.1 =
* Code refactoring and cleanup
* When displaying rendered directions, closing any open info bubbles

= 5.0.0 =
* Enhancement: Adding ability to choose custom marker icons from over 250 icons
* Enhancement: Fixed inconsistency when setting auto panning for info bubble.
* Enhancement: Documentation update.
* Enhancement: Some CSS tweaks
* Removed explicit settings for lat/long and address field. These should be set as part of the marker settings. These options are still supported for backwards compatibility.
* Removed marker animation setting.

= 4.0.9 =
* Enhancement: Directions CSS

= 4.0.8 =
* Bug: Inconsistency when loading Google map API, in other words - the experiment has been reverted. Sorry :)

= 4.0.7 =
* Enhancement: Experimenting with the load time of the Google API.
* Enhancement: Simplified logic of the 'bubbleautopan' option in the short code
* Enhancement: Removed 'Marker Direction' from the widget

= 4.0.6 =
* Enhancement: address parsing

= 4.0.5 =
* Bug: JS error

= 4.0.4 =
* Enhancement: Documentation refinement

= 4.0.3 =
* Enhancement: Miles are now default unit for directions. The direction options are not hidden anymore

= 4.0.2 =
* Enhancement: Made street view service less strict when checking if there is a street view available for a given marker location

= 4.0.1 =
* Unclosed HTML tag that broke layout for some of the users.

= 4.0.0 =
* Rewritten directions section. Now it is very Google-like looking with toll, highways and miles options. Printing functionality is also provided.
* Info bubble now can display marker's street view within itself

= 3.1.2 =
* Version increment to force reload of tooltip JS 

= 3.1.1 =
* Tooltips revisited. Worked around the conflict with Catalyst Theme. 

= 3.1.0 =
* Not loading jQuery UI from Google CDN anymore. Instead, loading jQuery UI core provided by WP. The new external JS that now has been included with the plugin is the jQuery UI slider, which does not come with WP.

= 3.0.9 =
* CSS changes

= 3.0.8 =
* Bug: Conflict with Slider Pro

= 3.0.7 =
* Extension of the previous version - more safety checks when creating markers from lat/long. 

= 3.0.6 =
* Bug: Preventing generation of default marker with lat zero and long zero 

= 3.0.5 =
* Enhancement: Now using Geo service only when geo address is provided. When lat/long are provided, the service is not used which does not cause the lost of location precision when generating the marker on the map.

= 3.0.4 =
* Bug: When primary marker is set to be hidden, the map was not generated 

= 3.0.3 =
* Enhancement: overriding background-image CSS property of the IMG tag to prevent some themes to mess up the map view. Thank you Eugene R. (http://kharkiv.vonvolt.com)
* Enhancement: Added a setting option to specify map alignment on the page
* Enhancement: When using Panoramio layer, added option to specify Panoramio user ID in order to filter photos displayed 

= 3.0.2 =
* Made the map to be centered by default. Will make an option for this setting in the future.

= 3.0.1 =
* Forgot to include functionality actually to disable directions when user does select "Disable"

= 3.0.0 =
* Bug: Removed clash between plugin and the Suffusion theme
* Enhancement: Added support for getting directions by car to marker's location (both for primary and additional markers)
* Enhancement: When clicking on the map once, the map view is centered back to the original location with its original zoom. Useful when user dragged the map view away  

= 2.0.8 =
* Bug: Trying to display primary location when KML is used.
* Disabled alert popups

= 2.0.7 =
* Enhancement: As a short term solution, additional marker pins now have blue colour. This helps to discriminate between additional and primary markers. Moving forward, as a long term solution, custom marker icons will be added.

= 2.0.6 =
* Enhancement: Added info-bubble auto pan configuration option
* Added information notice above documentation tabs
* Updated documentation

= 2.0.5 =
* Enhancement: Some code clean up
* Spike: An attempt to identify and remove duplicate Google map API from $wp_scripts, which can be loaded by another plugin and/or theme (Socialite)

= 2.0.4 =
* Enhancement: Allowing 5 decimal points for latitude and longitude
* Enhancement: Info bubble content is more descriptive now

= 2.0.3 =
* Bug: Added check for Firefox when using console logging

= 2.0.2 =
* Bug: Added check for null in Ajax onSuccess handler

= 2.0.1 =
* Enhancement: latitude/longitude now have 3 values after decimal point
* Enhancement: Now printing both address and lat/long in the info bubble

= 2.0 =
* Bug: Latitude range was starting from zero instead of from -90
* Enhancement: latitude/longitude now accept decimal values
* Enhancement: Added management section for multiple map markers (Facebook style tokens)
* Enhancement: Added support for Panoramio images (http://www.panoramio.com/)
* Enhancement: User interface flow revisited
* Enhancement: Documentation updated
* Enhancement: When clicking on the map once, the map view is centered back to the original location. Useful when user dragged the map view away   
* Change: Disabled auto-panning when marker is clicked 

= 1.0 =
* Initial release

== Upgrade Notice ==

Remove the old version of the plugin and install the most recent one. No additional configuration is required.
