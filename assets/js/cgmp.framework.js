/*
Copyright (C) 2011 - 2012 Alexander Zagniotov

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

var jQueryCgmp = jQuery.noConflict();
	(function ($) {

		if (typeof CGMPGlobal != "undefined") {
			if (!CGMPGlobal.maps instanceof Array) {
				CGMPGlobal.maps = [];
			}
		}

		var GoogleMapOrchestrator = (function() {

			var builder = {};
			var googleMap = {};
			var ControlType = {PAN: 0, ZOOM: 1, SCALE: 2, STREETVIEW: 3, MAPTYPE: 4, SCROLLWHEEL: 5};

			var initMap = function initMap(map, bubbleAutoPan, zoom, mapType)  {
				googleMap = map;

				if (mapType == "ROADMAP") {
					mapType = google.maps.MapTypeId.ROADMAP;
				} else if (mapType == "SATELLITE") {
					mapType = google.maps.MapTypeId.SATELLITE;
				} else if (mapType == "HYBRID") {
					mapType = google.maps.MapTypeId.HYBRID;
				} else if (mapType == "TERRAIN") {
					mapType = google.maps.MapTypeId.TERRAIN;
				}

				googleMap.setOptions({
					zoom: zoom,
					mapTypeId: mapType,
					mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU}
				});
			}

			var mapControl = function mapControl(isOn, mapControlType) {
				switch (mapControlType) {

					case GoogleMapOrchestrator.ControlType.SCROLLWHEEL:
						googleMap.setOptions({scrollwheel: (isOn == "false" ? false : true) });
					break;
					case GoogleMapOrchestrator.ControlType.MAPTYPE:
						googleMap.setOptions({mapTypeControl: (isOn == "false" ? false : true) });
					break;
					case GoogleMapOrchestrator.ControlType.PAN:
						googleMap.setOptions({panControl: (isOn == "false" ? false : true) });
					break;
					case GoogleMapOrchestrator.ControlType.ZOOM:
						googleMap.setOptions({zoomControl: (isOn == "false" ? false : true) });
					break;
					case GoogleMapOrchestrator.ControlType.SCALE:
						googleMap.setOptions({scaleControl: (isOn == "false" ? false : true) });
					break;
					case GoogleMapOrchestrator.ControlType.STREETVIEW:
						googleMap.setOptions({streetViewControl: (isOn == "false" ? false : true) });
					break;
					default:
						Logger.warn("Unknown map control type: " + mapControlType);
				}
			}

			return {
				initMap: initMap,
				mapControl: mapControl,
				ControlType: ControlType
			}
		})();


		var LayerBuilder = (function() {

			var googleMap = {};

			var init = function init(map) {
				googleMap = map;
			}

			var buildTrafficLayer = function buildTrafficLayer() {
				var trafficLayer = new google.maps.TrafficLayer();
				trafficLayer.setMap(googleMap);
			}

			var buildBikeLayer = function buildBikeLayer() {
				var bikeLayer = new google.maps.BicyclingLayer();
				bikeLayer.setMap(googleMap);
			}

			var buildPanoramioLayer = function buildPanoramioLayer(userId) {
				if (typeof google.maps.panoramio == "undefined" || !google.maps.panoramio || google.maps.panoramio == null ) {
					Logger.error("We cannot access Panoramio library. Aborting..");
					return false;
				}
				var panoramioLayer = new google.maps.panoramio.PanoramioLayer();
				if (panoramioLayer) {
					if (userId != null && userId != "") {
						panoramioLayer.setUserId(userId);
					}
					panoramioLayer.setMap(googleMap);
				} else {
					Logger.error("Could not instantiate Panoramio object. Aborting..");
				}
			}

			var buildKmlLayer = function buildKmlLayer(url) {
				if (url.toLowerCase().indexOf("http") < 0) {
					Logger.error("KML URL must start with HTTP(S). Aborting..");
					return false;
				}
				var kmlLayer = new google.maps.KmlLayer(url);

				google.maps.event.addListener(kmlLayer, "status_changed", function() {
					kmlLayerStatusEventCallback(kmlLayer);
				});
				kmlLayer.setMap(googleMap);

			}

			function kmlLayerStatusEventCallback(kmlLayer)  {
					var kmlStatus = kmlLayer.getStatus();
					if (kmlStatus == google.maps.KmlLayerStatus.OK) {
						//Hmmm...
					} else {
						var msg = '';
						switch(kmlStatus) {

								case google.maps.KmlLayerStatus.DOCUMENT_NOT_FOUND:
									msg = CGMPGlobal.errors.kmlNotFound;
								break;
								case google.maps.KmlLayerStatus.DOCUMENT_TOO_LARGE:
									msg = CGMPGlobal.errors.kmlTooLarge;
								break;
								case google.maps.KmlLayerStatus.FETCH_ERROR:
									msg = CGMPGlobal.errors.kmlFetchError;
								break;
								case google.maps.KmlLayerStatus.INVALID_DOCUMENT:
									msg = CGMPGlobal.errors.kmlDocInvalid;
								break;
								case google.maps.KmlLayerStatus.INVALID_REQUEST:
									msg = CGMPGlobal.errors.kmlRequestInvalid;
								break;
								case google.maps.KmlLayerStatus.LIMITS_EXCEEDED:
									msg = CGMPGlobal.errors.kmlLimits;
								break;
								case google.maps.KmlLayerStatus.TIMED_OUT:
									msg = CGMPGlobal.errors.kmlTimedOut;
								break;
								case google.maps.KmlLayerStatus.UNKNOWN:
									msg = CGMPGlobal.errors.kmlUnknown;
								break;
							}
					if (msg != '') {
						var error = CGMPGlobal.errors.kml.replace("[MSG]", msg);
						error = error.replace("[STATUS]", kmlStatus);
						Errors.alertError(error);
						Logger.error("Google returned KML error: " + msg + " (" + kmlStatus + ")");
						Logger.error("KML file: " + kmlLayer.getUrl());
					}
				}
			}

			return {
				init: init,
    			buildKmlLayer: buildKmlLayer,
				buildTrafficLayer: buildTrafficLayer,
				buildBikeLayer: buildBikeLayer,
				buildPanoramioLayer: buildPanoramioLayer
			}
		})();



		
		var MarkerBuilder = function () {

			var markers, storedAddresses, badAddresses, wasBuildAddressMarkersCalled, timeout, directionControlsBinded,
			googleMap, csvString, bubbleAutoPan, originalExtendedBounds, originalMapCenter, updatedZoom, mapDivId,
			geocoder, bounds, infowindow, streetViewService, directionsRenderer, directionsService;

			var init = function init(map, autoPan) {

				googleMap = map;
				mapDivId = googleMap.getDiv().id;
				bubbleAutoPan = autoPan;

				google.maps.event.addListener(googleMap, 'click', function () {
					resetMap();
				});

				markers = [];
				badAddresses = [];
				storedAddresses = [];

				updatedZoom = 5;

				timeout = null;
				csvString = null;
				originalMapCenter = null;
				originalExtendedBounds = null;

				directionControlsBinded = false;
				wasBuildAddressMarkersCalled = false;

				geocoder = new google.maps.Geocoder();
				bounds = new google.maps.LatLngBounds();
				infowindow = new google.maps.InfoWindow();
				streetViewService = new google.maps.StreetViewService();

				directionsService = new google.maps.DirectionsService();

				rendererOptions = {
					draggable: true
				};
				directionsRenderer = new google.maps.DirectionsRenderer(rendererOptions);
				directionsRenderer.setPanel(document.getElementById('rendered-directions-placeholder-' + mapDivId));
			}

			var isBuildAddressMarkersCalled = function isBuildAddressMarkersCalled() {
				return wasBuildAddressMarkersCalled;
			}

			var buildAddressMarkers = function buildAddressMarkers(markerLocations, isGeoMashap, isBubbleContainsPostLink) {

				wasBuildAddressMarkersCalled = true;
				csvString = Utils.trim(markerLocations);
				csvString = Utils.searchReplace(csvString, "'", "");

				if (isGeoMashap == "true") {
					var json = $.parseJSON(csvString);

					if (isBubbleContainsPostLink == "true") {
						parseJsonStructure(json, true);
					} else if (isBubbleContainsPostLink == "false") {
						parseJsonStructure(json, false);
					}
					queryGeocoderService();

				} else if (isGeoMashap == "false") {
					parseCsv();
					queryGeocoderService();
				}
			}


			function resetMap()  {
				if (originalExtendedBounds != null) {
					if (googleMap.setCenter() != originalExtendedBounds.getCenter()) {
						Logger.info("Panning map back to its original bounds center: " + originalExtendedBounds.getCenter() + " and updated zoom: " + updatedZoom);
						googleMap.setCenter(originalExtendedBounds.getCenter());
						googleMap.setZoom(updatedZoom);
					}
				} else 	if (originalMapCenter != null) {
					Logger.info("Panning map back to its original center: " + originalMapCenter  + " and updated zoom: " + updatedZoom);
					googleMap.setCenter(originalMapCenter);
					googleMap.setZoom(updatedZoom);
				}
			}

			function resetDirectionAddressFields(dirDivId)  {
				$(dirDivId + ' input#a_address').val('');
				$(dirDivId + ' input#b_address').val('');
				$(dirDivId + ' input#a_address').removeClass('d_error');
				$(dirDivId + ' input#b_address').removeClass('d_error');
			}

			function attachEventlistener(marker, markersElement) {

				var localBubbleData = buildBubble(marker.content, markersElement);
				var dirDivId = 'div#direction-controls-placeholder-' + mapDivId;
				var targetDiv = $("div#rendered-directions-placeholder-" + mapDivId);

				google.maps.event.addListener(marker, 'click', function () {

					resetDirectionAddressFields(dirDivId);

					$(dirDivId).fadeOut();
					directionsRenderer.setMap(null);
					targetDiv.html("");
					targetDiv.hide();
					$(dirDivId + ' button#print_sub').hide();

					infowindow.setContent(localBubbleData.bubbleContent);
					infowindow.setOptions({disableAutoPan: bubbleAutoPan == "true" ? false : true });
					infowindow.open(googleMap, this);
				});

				validateMarkerStreetViewExists(marker, localBubbleData, dirDivId);
				attachEventstoDirectionControls(marker, localBubbleData, dirDivId, targetDiv);
			}

			function attachEventstoDirectionControls(marker, localBubbleData, dirDivId, targetDiv)  {

				var parentInfoBubble = 'div#bubble-' + localBubbleData.bubbleHolderId;
				var addy = marker.content;

				addy = addy.replace("Lat/Long: ", "");

				$(parentInfoBubble + ' a.dirToHereTrigger').live("click", function() {
					var thisId = this.id;
					if (thisId == 'toHere-' + localBubbleData.bubbleHolderId) {
						$(dirDivId).fadeIn();
						$(dirDivId + ' input#a_address').val('');
						$(dirDivId + ' input#b_address').val(addy);
						$(dirDivId + ' input#radio_miles').attr("checked", "checked");
					}
				});

				$(parentInfoBubble + ' a.dirFromHereTrigger').live("click", function() {
					var thisId = this.id;
					if (thisId == 'fromHere-' + localBubbleData.bubbleHolderId) {
						$(dirDivId).fadeIn();
						$(dirDivId + ' input#a_address').val(addy);
						$(dirDivId + ' input#b_address').val('');
						$(dirDivId + ' input#radio_miles').attr("checked", "checked");
					}
				});

				$(dirDivId + ' div.d_close-wrapper').live("click", function(event) {

						resetDirectionAddressFields(dirDivId);

						$(this).parent().fadeOut();
						directionsRenderer.setMap(null);
						targetDiv.html("");
						targetDiv.hide();
						$(dirDivId + ' button#print_sub').hide();
						resetMap();

						return false;
				});
			}

			function validateMarkerStreetViewExists(marker, localBubbleData, dirDivId)  {

				streetViewService.getPanoramaByLocation(marker.position, 50, function (streetViewPanoramaData, status) {
					if (status === google.maps.StreetViewStatus.OK) {
						// ok
							$('a#trigger-' + localBubbleData.bubbleHolderId).live("click", function() {

								var panoramaOptions = {
										navigationControl: true,
										enableCloseButton: true,
										addressControl: false,
										linksControl: true,
										scrollwheel: false,
										addressControlOptions: {
											position: google.maps.ControlPosition.BOTTOM
										},
										position: marker.position,
										pov: {
											heading: 165,
											pitch:0,
											zoom:1
										}
								};	

								var pano = new google.maps.StreetViewPanorama(document.getElementById("bubble-" + localBubbleData.bubbleHolderId), panoramaOptions);
								pano.setVisible(true);

								google.maps.event.addListener(infowindow, 'closeclick', function() {

									resetDirectionAddressFields(dirDivId);
									$(dirDivId).fadeOut();

									if (pano != null) {
										pano.unbind("position");
										pano.setVisible(false);
									}

									pano = null;
								});

								google.maps.event.addListener(pano, 'closeclick', function() {
									if (pano != null) {
										pano.unbind("position");
										pano.setVisible(false);
										$('div#bubble-' + localBubbleData.bubbleHolderId).css("background", "none");
									}

									pano = null;
								});

						});
					} else {
						// no street view available in this range, or some error occurred
						Logger.warn("There is not street view available for this marker location: " + marker.position + " status: " + status);
						$('a#trigger-' + localBubbleData.bubbleHolderId).live("click", function(e) {
							e.preventDefault();
						});
						$('a#trigger-' + localBubbleData.bubbleHolderId).attr("style", "text-decoration: none !important; color: #ddd !important");

						google.maps.event.addListener(infowindow, 'domready', function () {
							$('a#trigger-' + localBubbleData.bubbleHolderId).removeAttr("href");
							$('a#trigger-' + localBubbleData.bubbleHolderId).attr("style", "text-decoration: none !important; color: #ddd !important");
						});
					}
				});
			}


			function bindDirectionControlsToEvents()  {

				var dirDivId = 'div#direction-controls-placeholder-' + mapDivId;
				var targetDiv = $("div#rendered-directions-placeholder-" + mapDivId);

				$(dirDivId + ' a#reverse-btn').live("click", function(e) {

						var old_a_addr = $(dirDivId + ' input#a_address').val();
						var old_b_addr = $(dirDivId + ' input#b_address').val();

						$(dirDivId + ' input#a_address').val(old_b_addr);
						$(dirDivId + ' input#b_address').val(old_a_addr);
						return false;
				});

				$(dirDivId + ' a#d_options_show').live("click", function() {
						$(dirDivId + ' a#d_options_hide').show();
						$(dirDivId + ' a#d_options_show').hide();
						$(dirDivId + ' div#d_options').show();
						return false;
				});

				$(dirDivId + ' a#d_options_hide').live("click", function() {
						$(dirDivId + ' a#d_options_hide').hide();
						$(dirDivId + ' a#d_options_show').show();
						$(dirDivId + ' div#d_options').hide();
						$(dirDivId + ' input#avoid_hway').removeAttr("checked");
						$(dirDivId + ' input#avoid_tolls').removeAttr("checked");
						$(dirDivId + ' input#radio_km').removeAttr("checked");
						$(dirDivId + ' input#radio_miles').attr("checked", "checked");
						return false;
				});
		
				$(dirDivId + ' button#d_sub').live("click", function() {
						var old_a_addr = $(dirDivId + ' input#a_address').val();
						var old_b_addr = $(dirDivId + ' input#b_address').val();
						var halt = false;
						if (old_a_addr == null || old_a_addr == '') {
							$(dirDivId + ' input#a_address').addClass('d_error');
							halt = true;
						}
			
						if (old_b_addr == null || old_b_addr == '') {
							$(dirDivId + ' input#b_address').addClass('d_error');
							halt = true;
						}

						if (!halt) {

							$(dirDivId + ' button#d_sub').attr('disabled', 'disabled').html("Please wait..");
							// Query direction service
							var travelMode = google.maps.DirectionsTravelMode.DRIVING;
							if ($(dirDivId + ' a#dir_w_btn').hasClass('selected')) {
								travelMode = google.maps.DirectionsTravelMode.WALKING;
							}

							var is_avoid_hway = $(dirDivId + ' input#avoid_hway').is(":checked");
							var is_avoid_tolls = $(dirDivId + ' input#avoid_tolls').is(":checked");
							var is_miles = $(dirDivId + ' input#radio_miles').is(":checked");
							var unitSystem = google.maps.DirectionsUnitSystem.METRIC;

							var request = {
								origin: old_a_addr,
								destination: old_b_addr,
								travelMode: travelMode,
								provideRouteAlternatives: true
							};

							if (is_avoid_hway) {
								request.avoidHighways = true;
							} 

							if (is_avoid_tolls) {
								request.avoidTolls = true;
							}

							if (is_miles) {
								request.unitSystem = google.maps.DirectionsUnitSystem.IMPERIAL;
							} else {
								request.unitSystem = google.maps.DirectionsUnitSystem.METRIC;
							}

							directionsService.route(request, function(response, status) {

								if (status == google.maps.DirectionsStatus.OK) {
									targetDiv.html("");
									targetDiv.show();
									directionsRenderer.setMap(googleMap);
									directionsRenderer.setDirections(response);
									$(dirDivId + ' button#d_sub').removeAttr('disabled').html("Get directions");
									$(dirDivId + ' button#print_sub').fadeIn();
									infowindow.close();

								} else {
									Logger.error('Could not route directions from "' + old_a_addr + '" to "' + old_b_addr + '", got result from Google: ' + status);
									targetDiv.html("<span style='font-size: 12px; font-weight: bold; color: red'>Could not route directions from<br />'" + old_a_addr + "' to<br />'" + old_b_addr + "'<br />Got result from Google: [" + status + "]</span>");

									$(dirDivId + ' button#print_sub').hide();
									$(dirDivId + ' button#d_sub').removeAttr('disabled').html("Get directions");
								}
							});
						}
				});

				$(dirDivId + ' button#print_sub').live("click", function() {
					var old_a_addr = $(dirDivId + ' input#a_address').val();
					var old_b_addr = $(dirDivId + ' input#b_address').val();

					var dirflag = "d";
					if ($(dirDivId + ' a#dir_w_btn').hasClass('selected')) {
						dirflag = "w";
					}

					var url = "http://maps.google.com/?saddr=" + old_a_addr + "&daddr=" + old_b_addr + "&dirflg=" + dirflag + "&pw=2";
					var is_miles = $(dirDivId + ' input#radio_miles').is(":checked");
					if (is_miles) {
						url += "&doflg=ptm";
					}

					window.open( url );
					return false;
				});

				$(dirDivId + ' input#a_address').live("change focus", function() {
					$(dirDivId + ' input#a_address').removeClass('d_error');
					return false;
				});

				$(dirDivId + ' input#b_address').live("change focus", function() {
					$(dirDivId + ' input#b_address').removeClass('d_error');
					return false;
				});


				$(dirDivId + ' .kd-button').live("click", function() {
					var thisId = this.id;

					if (thisId == 'dir_d_btn') {
						if ($(dirDivId + ' a#dir_d_btn').hasClass('selected')) {
							Logger.warn("Driving travel mode is already selected");
						} else {
							$(dirDivId + ' a#dir_d_btn').addClass('selected');
							$(dirDivId + ' a#dir_w_btn').removeClass('selected');
						}
					} else 	if (thisId == 'dir_w_btn') {
						if ($(dirDivId + ' a#dir_w_btn').hasClass('selected')) {
							Logger.warn("Walking travel mode is already selected");
						} else {
							$(dirDivId + ' a#dir_w_btn').addClass('selected');
							$(dirDivId + ' a#dir_d_btn').removeClass('selected');
						}
					}

					return false;
				});

			}

			function buildBubble(contentFromMarker, markersElement) {

				var localBubbleData = [];
				var randomNumber = Math.floor(Math.random() * 111111);

				randomNumber = randomNumber + "-" + mapDivId;

				var	bubble = "<div id='bubble-" + randomNumber + "' style='height: 130px !important; width: 300px !important;' class='bubble-content'>";

				if (!markersElement.geoMashup) {
					bubble += "<h4>Address:</h4>";
					bubble += "<p style='text-align: left'>" + contentFromMarker + "</p>";
				} else {
					var substr = markersElement.postTitle.substring(0, 30);
					bubble += "";
					bubble += "<p style='text-align: left'><a style='font-size: 15px !important; font-weight: bold !important;' title='Original post: " + markersElement.postTitle + "' href='" + markersElement.postLink  + "'>" + substr + "..</a></p>";
					bubble += "<p style='font-size: 12px !important; padding-left: 12px !important; padding-right: 6px !important; text-align: left; line-height: 130% !important'>" + markersElement.postExcerpt  + "</p>";
				}

				bubble += "<hr />";
				bubble += "<p style='text-align: left'>Directions: <a id='toHere-" + randomNumber + "' class='dirToHereTrigger' href='javascript:void(0);'>To here</a> - <a id='fromHere-" + randomNumber + "' class='dirFromHereTrigger' href='javascript:void(0);'>From here</a> | <a id='trigger-" + randomNumber + "' class='streetViewTrigger' href='javascript:void(0);'>Street View</a></p>";
				bubble += "</div>";

				return {bubbleHolderId : randomNumber, bubbleContent: bubble};
			}

			function parseCsv() {
				var locations = csvString.split("|");

				Logger.info("Exploded CSV into locations: " + locations);

				for (var i = 0; i < locations.length; i++) {
					var target = locations[i];
					if (target != null && target != "") {
						target = Utils.trim(target);
						if (target == "") {
							Logger.warn("Given extra marker address is empty");
							continue;
						}
						pushGeoDestination(target, (i + 1));
					}
				}
			}

			function parseJsonStructure(json, infoBubbleContainPostLink)  {

				var index = 1;
				$.each(json, function() {
					Logger.info("Looping over JSON object:\n\tTitle: " + this.title + "\n\tAddy: " + this.addy + "\n\tLink: " + this.permalink + "\n\tExcerpt: " + this.excerpt);

					var targetArr = this.addy.split(CGMPGlobal.sep);

					if (Utils.isNumeric(targetArr[0])) {
						addGeoPoint(targetArr[0], index, targetArr[1], this.title, this.permalink, this.excerpt, infoBubbleContainPostLink);
					} else if (Utils.isAlphaNumeric(targetArr[0])) {
						storeAddress(targetArr[0], index, targetArr[1], this.title, this.permalink, this.excerpt, infoBubbleContainPostLink);
					} else {
						storeAddress(targetArr[0], index, targetArr[1], this.title, this.permalink, this.excerpt, infoBubbleContainPostLink);
						Logger.warn("Unknown type of geo destination in regexp: " + targetArr[0] + ", fallingback to store it as an address");
					}
					index ++;
				});
			}
			
			function pushGeoDestination(target, index) {

				 var targetArr = target.split(CGMPGlobal.sep);

				 if (Utils.isNumeric(targetArr[0])) {
					 addGeoPoint(targetArr[0], index, targetArr[1], '', '', '', false);
				 } else if (Utils.isAlphaNumeric(targetArr[0])) {
					 storeAddress(targetArr[0], index, targetArr[1], '', '', '', false);
				 } else {
					 storeAddress(targetArr[0], index, targetArr[1], '', '', '', false);
					 Logger.warn("Unknown type of geo destination in regexp: " + targetArr[0] + ", fallingback to store it as an address");
				 }
			}

			function storeAddress(address, zIndex, markerIcon, postTitle, postLink, postExcerpt, geoMashup) {
					
					Logger.info("Storing address: " + address + " for marker-to-be for the map ID: " + mapDivId);
					storedAddresses.push({
						address: address,
						animation: google.maps.Animation.DROP,
						zIndex: zIndex,
						markerIcon: markerIcon,
						postTitle: postTitle,
						postLink: postLink,
						postExcerpt: postExcerpt,
						geoMashup: geoMashup
					});
				}
			
			function addGeoPoint(point, zIndex, markerIcon, postTitle, postLink, postExcerpt, geoMashup) {
				if (point == null || !point) {
					Logger.warn("Given GEO point containing Lat/Long is NULL");
					return false;
				}
				
				var latLng = point;
				if (!(latLng instanceof google.maps.LatLng)) {
					if (point.indexOf(",") != -1) {
						var latlngStr = point.split(",",4);

						if (latlngStr == null || latlngStr.length != 2) {
							Logger.warn("Exploded lat/long array is NULL or does not have length of two");
							return false;
						}

						if (latlngStr[0] == null || latlngStr[1] == null) {
							Logger.warn("Lat or Long are NULL");
							return false;
						}

						latlngStr[0] = 	Utils.trim(latlngStr[0]);
						latlngStr[1] = 	Utils.trim(latlngStr[1]);

						if (latlngStr[0] == '' || latlngStr[1] == '') {
							Logger.warn("Lat or Long are empty string");
							return false;
						}

						var lat = parseFloat(latlngStr[0]);
						var lng = parseFloat(latlngStr[1]);
						latLng = new google.maps.LatLng(lat, lng);
					}
				}
				storeAddress(latLng, zIndex, markerIcon, postTitle, postLink, postExcerpt, geoMashup);
			}
			
			function queryGeocoderService() {
				clearTimeout(timeout);
				if (storedAddresses.length > 0) {
					var element = storedAddresses.shift();
					Logger.info("Passing [" + element.address + "] to Geo service. Have left " + storedAddresses.length + " items to process!");

					if (element.address instanceof google.maps.LatLng) {
						buildLocationFromCoords(element);
					} else {
						var geocoderRequest = {"address": element.address};
						geocoder.geocode(geocoderRequest, function (results, status) {
							geocoderCallback(results, status, element);
						});
					}
				} else {
					setBounds();

					if (badAddresses.length > 0) {
						var msg = "";
						$.each(badAddresses, function (index, addy) {
							msg += "&nbsp;&nbsp;&nbsp;<b>" + (1 + index) + ". " + addy + "</b><br />";
						});

						Errors.alertError(CGMPGlobal.errors.badAddresses.replace('[REPLACE]', msg));
					}
					badAddresses = [];
				}
			}

			function setBounds() {

				if (markers.length > 1) {
					$.each(markers, function (index, marker) {
						if (!bounds.contains(marker.position)) {
							bounds.extend(marker.position);
						}
					});
					originalExtendedBounds = bounds;
					googleMap.fitBounds(bounds);
					updatedZoom = googleMap.getZoom();
				} else if (markers.length == 1) {
					googleMap.setCenter(markers[0].position);
					updatedZoom = googleMap.getZoom();
				}
			}

			function buildLocationFromCoords(element)  {
				var addressPoint = element.address;

				element.address = buildLatLongBubbleInfo(element, addressPoint);
				instrumentMarker(addressPoint, element);
				queryGeocoderService();
			}

			function buildLatLongBubbleInfo(element, addressPoint)  {
				if (element.zIndex == 1) {
					originalMapCenter = addressPoint;
				}

				var lat = addressPoint.lat();
				lat = parseFloat(lat);
				lat = lat.toFixed(5);

				var lng = addressPoint.lng();
				lng = parseFloat(lng);
				lng = lng.toFixed(5);

				return "Lat/Long: " + lat + ", " + lng;
			}

			function geocoderCallback(results, status, element) {
				if (status == google.maps.GeocoderStatus.OK) {

					var addressPoint = results[0].geometry.location;
					
					instrumentMarker(addressPoint, element);
					timeout = setTimeout(function() { queryGeocoderService(); }, 330);
				} else if (status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
					setBounds();
					storedAddresses.push(element);   	
					timeout = setTimeout(function() { queryGeocoderService(); }, 3000);
				} else if (status == google.maps.GeocoderStatus.ZERO_RESULTS) {
					Logger.warn("Got ZERO results for [" + element.address + "]. Have left " + markers.length + " items to process");
					badAddresses.push(element.address);
					timeout = setTimeout(function() { queryGeocoderService(); }, 400);
				}

			}


			function instrumentMarker(point, element) {
				var marker = new google.maps.Marker({
					position: point,
					title: element.address.replace("<br />", " :: "),
					content: element.address,
					zIndex: (element.zIndex + 1000),
					map: googleMap
				});
				if (marker) {

					if (element.markerIcon) {
						var markerIcon = element.markerIcon;
						marker.setIcon(CGMPGlobal.customMarkersUri + markerIcon);
						
						var shadow = null;
						var defaultMarkers = ['1-default.png', '2-default.png'];
						var defaultPins = ['4-default.png', '5-default.png', '6-default.png', '7-default.png'];

						if ($.inArray(markerIcon, defaultMarkers) != -1) {
							var url = "http://maps.google.com/mapfiles/ms/icons/msmarker.shadow.png";
							shadow = buildMarkerImage(url, 59, 32, 0, 0, 16, 33);
						} else 	if ($.inArray(markerIcon, defaultPins) != -1) {
							var url = "http://maps.google.com/mapfiles/ms/icons/msmarker.shadow.png";
							shadow = buildMarkerImage(url, 59, 32, 0, 0, 21, 34);
						} else if (markerIcon.indexOf('3-default') != -1) {
							var url = "http://code.google.com/apis/maps/documentation/javascript/examples/images/beachflag_shadow.png";
							shadow = buildMarkerImage(url, 37, 32, 0, 0, 10, 33);
						} else {
							shadow = buildMarkerImage(CGMPGlobal.customMarkersUri + "shadow.png", 68, 37, 0, 0, 32, 38);
						}

						marker.setShadow(shadow);
					}

					attachEventlistener(marker, element);
					if (!directionControlsBinded) {
						bindDirectionControlsToEvents();
						directionControlsBinded = true;
					}

					markers.push(marker);
				}
			}

			function buildMarkerImage(url, sizeX, sizeY, pointAX, pointAY, pointBX, pointBY)  {

				var image = new google.maps.MarkerImage(url,
							new google.maps.Size(sizeX, sizeY),
							new google.maps.Point(pointAX, pointAY),
							new google.maps.Point(pointBX, pointBY));

				return image;
			}

			return {
				init: init,
				buildAddressMarkers: buildAddressMarkers,
				isBuildAddressMarkersCalled: isBuildAddressMarkersCalled
			}
		};



		var Utils = (function() {
			var isNumeric = function isNumeric(subject) {
				var numericRegex = /^([0-9?(\-.,\s{1,})]+)$/;
				return numericRegex.test(subject);
			}
			var isAlphaNumeric = function isAlphaNumeric(subject) {
			    var addressRegex = /^([a-zA-Z0-9?(/\-.,\s{1,})]+)$/;
				return addressRegex.test(subject);
			}
			var trim = function trim(subject) {
				var leftTrimRegex = /^\s\s*/;
				var rightTrimRegex = /\s\s*$/;
				var trimRegex = /^\s+|\s+$/g;
				return subject.replace(trimRegex, '');
			}
			var searchReplace = function searchReplace(subject, search, replace) {
				return subject.replace(new RegExp(search, "g"), replace);
			}
			return {
    			isNumeric: isNumeric,
				isAlphaNumeric: isAlphaNumeric,
				trim: trim,
				searchReplace: searchReplace
			}
		})();



		var Logger = (function() {
			var info = function info(message) {
				var msg = "Info :: " + message;
				print(msg);
			}
			var raw = function raw(msg) {
				print(msg);
			}
			var warn = function warn(message) {
				var msg = "Warning :: " + message;
				print(msg);
			}
			var error = function error(message) {
				var msg = "Error :: " + message;
				print(msg);
			}
			var fatal = function fatal(message) {
				var msg = "Fatal :: " + message;
				print(msg);
			}
			var print = function print(message) {
				if ( $.browser.msie ) {
					//Die... die... die.... why dont you just, die???
				 } else {
					if ($.browser.mozilla && parseInt($.browser.version) >= 3 ) {
						console.log(message);
					} else {
						console.log("Logger could not print because browser is Mozilla [" + $.browser.mozilla + "] and its version is [" + parseInt($.browser.version) + "]");
					}
				 }
			}

			return {
    				info: info,
					raw: raw,
					warn: warn,
					error: error,
					fatal: fatal
  				}
		})();


			var Errors = (function() {

					var alertError = function alertError(content)  {

						var mask = $('<div id="mask"/>');
						var id = Math.random().toString(36).substring(3);
						var shortcode_dialog = $('<div id="' + id + '" class="shortcode-dialog window" />');
						shortcode_dialog.html("<p style='padding: 10px 10px 0 10px'>" + content + "</p><div align='center'><input type='button' class='close-dialog' value='Close' /></div>");

						$('body').append(mask);
						$('body').append(shortcode_dialog);

						var maskHeight = $(document).height();
						var maskWidth = $(window).width();
						$('#mask').css({'width':maskWidth,'height':maskHeight, 'opacity':0.3});

						if ($("#mask").length == 1) {
							$('#mask').show();
						}

						var winH = $(window).height();
						var winW = $(window).width();
						$("div#" + id).css('top',  winH/2-$("div#" + id).height()/2);
						$("div#" + id).css('left', winW/2-$("div#" + id).width()/2);
						$("div#" + id).fadeIn(500); 
						$('.window .close-dialog').click(function (e) {
							e.preventDefault();

							var parentDialog = $(this).closest("div.shortcode-dialog");
							if (parentDialog) {
								$(parentDialog).remove();
							}

							if ($("div.shortcode-dialog").length == 0) {
								$('#mask').remove();
							}
						});
						$('#mask').click(function () {
							$(this).remove();
							$('.window').remove();
						});
						$(window).resize(function () {
							var box = $('.window');
							var maskHeight = $(document).height();
							var maskWidth = $(window).width();
							$('#mask').css({'width':maskWidth,'height':maskHeight});
							var winH = $(window).height();
							var winW = $(window).width();
							box.css('top',  winH/2 - box.height()/2);
							box.css('left', winW/2 - box.width()/2);
						});
					}

				return {
    				alertError: alertError
				}
			})();


		//$(document).ready(function() {

			if (typeof CGMPGlobal != "undefined" && CGMPGlobal.maps) {
				Logger.info("The CGMPGlobal object has [" + CGMPGlobal.maps.length + "] map JSONs inside");
			} else {
				Logger.fatal("The CGMPGlobal object is undefined. Aborting map generation .. d[-_-]b");
				return;
			}

			$.each(CGMPGlobal.maps, function(index, json) {

				if (typeof google == "undefined" || !google) {
					Errors.alertError(CGMPGlobal.errors.msgNoGoogle);
					Logger.fatal("We do not have reference to Google API. Aborting map generation ..");
					return false;
				} else if (typeof GMap2 != "undefined" && GMap2) {
					Errors.alertError(CGMPGlobal.errors.msgApiV2);
					Logger.fatal("It looks like the webpage has reference to GMap2 object from Google API v2. Aborting map generation ..");
					return false;
				}

				if ($('div#' + json.id).length > 0) {

						var googleMap = new google.maps.Map(document.getElementById(json.id));

						GoogleMapOrchestrator.initMap(googleMap, json.bubbleautopan, parseInt(json.zoom), json.maptype);
						LayerBuilder.init(googleMap);
						var markerBuilder = new MarkerBuilder();
						markerBuilder.init(googleMap, json.bubbleautopan);

						GoogleMapOrchestrator.mapControl(json.maptypecontrol, GoogleMapOrchestrator.ControlType.MAPTYPE);
						GoogleMapOrchestrator.mapControl(json.pancontrol, GoogleMapOrchestrator.ControlType.PAN);
						GoogleMapOrchestrator.mapControl(json.zoomcontrol, GoogleMapOrchestrator.ControlType.ZOOM);
						GoogleMapOrchestrator.mapControl(json.scalecontrol, GoogleMapOrchestrator.ControlType.SCALE);
						GoogleMapOrchestrator.mapControl(json.scrollwheelcontrol, GoogleMapOrchestrator.ControlType.SCROLLWHEEL);
						GoogleMapOrchestrator.mapControl(json.streetviewcontrol, GoogleMapOrchestrator.ControlType.STREETVIEW);


						if (json.showpanoramio == "true") {
							LayerBuilder.buildPanoramioLayer(json.panoramiouid);
						}

						if (json.showbike == "true") {
							LayerBuilder.buildBikeLayer();
						}
						if (json.showtraffic == "true") {
							LayerBuilder.buildTrafficLayer();
						}

						if (json.kml != null && json.kml != '') {
							LayerBuilder.buildKmlLayer(json.kml);
						} else {

							if (json.markerlist != null && json.markerlist != '') {
								markerBuilder.buildAddressMarkers(json.markerlist, json.addmarkermashup, json.geomashupbubble);
							}

							var isBuildAddressMarkersCalled = markerBuilder.isBuildAddressMarkersCalled();
							if (!isBuildAddressMarkersCalled) {
								Errors.alertError(CGMPGlobal.errors.msgMissingMarkers);
							}
						}
				} else {
					Logger.fatal("It looks like the map DIV placeholder ID [" + json.id + "] does not exist in the page!");
				}
			//});
		});
	}(jQueryCgmp));
