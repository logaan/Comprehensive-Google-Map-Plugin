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

function sendShortcodeToEditor(container_id) {
	var id = '#' + container_id;
	var code = buildShortcode(id, jQueryCgmp);
	send_to_editor('<br />' + code + '<br />');
}


function displayShortcodeInPopup(container_id) {
	var id = '#' + container_id;
	var code = buildShortcode(id, jQueryCgmp);
	var content = "Select the generated shortcode text below including the square brackets and press CTRL+C (CMMND+C on Mac) to copy:<br /><br /><div id='inner-shortcode-dialog'><b>" + code + "</b></div><br /><br />Paste the copied text into your post/page";
	displayPopupWithContent(content);
}

function displayPopupWithContent(content)  {

	(function ($, content) {

		var mask = $('<div id="mask"/>');
		var shortcode_dialog = $('<div id="shortcode-dialog" class="window" />');
		shortcode_dialog.html("<p style='padding: 10px 10px 0 10px'>" + content + "</p><div align='center'><input type='button' class='close' value='Close' /></div>");

		$('body').append(mask);
		$('body').append(shortcode_dialog);

		var maskHeight = $(document).height();
		var maskWidth = $(window).width();
		$('#mask').css({'width':maskWidth,'height':maskHeight, 'opacity':0.1});
		$('#mask').show();	
		$('#mask').show();	
		var winH = $(window).height();
		var winW = $(window).width();
		$("div#shortcode-dialog").css('top',  winH/2-$("div#shortcode-dialog").height()/2);
		$("div#shortcode-dialog").css('left', winW/2-$("div#shortcode-dialog").width()/2);
		$("div#shortcode-dialog").fadeIn(500); 
		$('.window .close').click(function (e) {
			e.preventDefault();
			$('#mask').remove();
			$('.window').remove();
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

	}(jQueryCgmp, content));

}

function buildShortcode(id, $) {
	var code = "[google-map-v3 ";
	$(id + ' .shortcodeitem').each(function() {
	
		var role = $(this).attr('role');
		var val =  $(this).attr('value');

		if ($(this).attr('type') == "checkbox") {
			val = $(this).is(":checked");
		}

		if ($(this).attr('type') == "radio") {
			var name = $(this).attr('name');
			val = $('input[name=' + name + ']:checked').val();
			role = name;
		}
	
		if (typeof role == "undefined" || role == "undefined") {
			role = $(this).attr('id');
		}

		if (role != null && role != "" && val != null && val != "") {

			if (role.indexOf("_") > 0) {
				role = role.replace(/_/g,"");
			} if (role.indexOf("hidden") > 0) {
				role = role.replace(/hidden/g,"");
			}
			code += role + "=" + "\"" + val + "\" ";
		}
	});
	code = code.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	code += "]";
	return code;
}



function hideShowCustomMarker(hiddenElemId) {

		if (hiddenElemId.indexOf('_i_') == -1) {
			var val = jQueryCgmp('#' + hiddenElemId).val();
			var checkboxId = hiddenElemId.replace("hidden", "");
			var customIconsId = checkboxId.replace("mashup", "icons");
			var kmlId = checkboxId.replace("addmarkermashup", "kml");


			if (val == 'true') {
				jQueryCgmp("#" + kmlId).closest("fieldset").hide();
				jQueryCgmp("#" + customIconsId).closest("fieldset").hide();
				jQueryCgmp("#" + checkboxId).attr("checked", "checked");
			} else {
				jQueryCgmp("#" + kmlId).closest("fieldset").show();
				jQueryCgmp("#" + customIconsId).closest("fieldset").show();
				jQueryCgmp("#" + checkboxId).removeAttr("checked");
			}
		}
}


(function ($) {

	var lists = [];

		function initTokenHolders()  {

				lists = [];
				var parentElements = "div#widgets-right  ul.token-input-list, div#google-map-container-metabox ul.token-input-list";

				$.map($(parentElements), function(element) {
					var id = $(element).attr("id");

					if (id != null) {
						var hiddenInput = "#" + element.id + "hidden";
						var csv = $(hiddenInput).val();

						var holderList = $(element).tokenInput({holderId: id});

						if (csv != null && csv != "") {
							var locations = csv.split("|");
							$.map(locations, function (element) {
								holderList.add(element);
							});
						}

						lists.push({id : id, obj: holderList});
					}
				});
		}

		function initAddLocationEevent()  {

			$("input.add-additonal-location").click(function (source) {

				var listId = $(this).attr("id") + "list";
				var tokenList = {};
				$.map($(lists), function(element) {
					if (element.id == listId) {
						tokenList = element.obj;
						return;
					}
				});

				var targetInput = "#" + $(this).attr("id") + "input";
				var customIconListId = "#" + $(this).attr("id") + "icons";
				var selectedIcon = $(customIconListId + " input[name='custom-icons-radio']:checked").val();

				if ($(targetInput).val() != null && $(targetInput).val() != "") {

					var target = $(targetInput).val().replace(/^\s+|\s+$/g, '');
					var chars = /^(?=.*(\d|[a-zA-Z])).{5,}$/;
					var hasValidChars = chars.test(target);
					if (hasValidChars) {

						tokenList.add(target + CGMPGlobal.sep + selectedIcon);

						resetPreviousIconSelection($(customIconListId));

						$(customIconListId + " img#default-marker-icon").attr("style", "cursor: default; ");
						$(customIconListId + " img#default-marker-icon").addClass('selected-marker-image');
						$(customIconListId + " input#default-marker-icon-radio").attr('checked', 'checked');

						$(targetInput).attr("style", "");
						$(targetInput).addClass("default-marker-icon");
						$(targetInput).val("");
						$(targetInput).focus();

					} else {
						fadeInOutOnError(targetInput);
					}
				} else {
					fadeInOutOnError(targetInput);
				}

				return false;
			});
		}

		function fadeInOutOnError(targetInput)  {

			$(targetInput).fadeIn("slow", function() {
				$(this).addClass("errorToken");
			});

			$(targetInput).focus().fadeOut(function() {
				$(this).removeClass("errorToken");
				$(this).fadeIn("slow");
			});
		}


		function resetPreviousIconSelection(parentDiv)  {
			$.each(parentDiv.children(), function() {
				var liImg = $(this).find("img");

				if (liImg != null) {
					$(liImg).attr("style", "");
					$(liImg).removeClass('selected-marker-image');
				}
			});
		}

		function initMarkerIconEvents() {

			$("div.custom-icons-placeholder a img").click(function () {
				var currentSrc = $(this).attr('src');
				if (currentSrc != null) {

					var parentDiv = $(this).closest("div.custom-icons-placeholder");
					resetPreviousIconSelection(parentDiv);
					$(this).parent("a").siblings('input[name="custom-icons-radio"]').attr("checked", "checked");
					doMarkerIconUpdateOnSelection(parentDiv, $(this));
				}
			});


			$("input[name='custom-icons-radio']").click(function () {

				var img = $(this).siblings("a").children('img');
				var currentSrc = $(img).attr('src');
					if (currentSrc != null) {
						var parentDiv = $(this).closest("div.custom-icons-placeholder");
						resetPreviousIconSelection(parentDiv);
						doMarkerIconUpdateOnSelection(parentDiv, img);
					}
			});
		}

		function doMarkerIconUpdateOnSelection(parentDiv, img)  {

			$(img).attr("style", "cursor: default; ");
			$(img).addClass('selected-marker-image');

			var currentSrc = $(img).attr('src');
			var inputId = $(parentDiv).attr("id").replace("icons", "input");
			$("#" + inputId).attr("style", "background: url('" + currentSrc + "') no-repeat scroll 0px 0px #F9F9F9 !important");
			$("#" + inputId).removeClass("default-marker-icon");
			$("#" + inputId).focus();
		}

		function initTooltips()  {

			$('a.google-map-tooltip-marker').hover(function() {
			var tooltip_marker_id = $(this).attr('id');

				$("a#" + tooltip_marker_id + "[title]").tooltip({
					effect: 'slide',
					opacity: 0.8,
					tipClass : "google-map-tooltip",
					offset: [-5, 0],
					events: {
						def: "click, mouseleave"
					}
				});

				$("a#" + tooltip_marker_id).mouseout(function(event) {
					if ($(this).data('tooltip')) {
						$(this).data('tooltip').hide();
					}
				});
			});
		}

		function initGeoMashupEvent() {

			$("input.marker-geo-mashup").change(function (source) {
				var checkboxId = $(this).attr("id");
				var customIconsId = checkboxId.replace("mashup", "icons");
				var kmlId = checkboxId.replace("addmarkermashup", "kml");

				if ($(this).is(":checked")) {
					$("#" + kmlId).closest("fieldset").fadeOut();
					$("#" + customIconsId).closest("fieldset").fadeOut();
					$("#" + checkboxId + "hidden").val("true");
				} else {
					$("#" + kmlId).closest("fieldset").fadeIn();
					$("#" + customIconsId).closest("fieldset").fadeIn();
					$("#" + checkboxId + "hidden").val("false");
				}
			});
		}

		function checkedGeoMashupOnInit() {

			$.each($("input.marker-geo-mashup"), function() {
				var checkboxId = $(this).attr("id");
				var hiddenIdVal = $("#" + checkboxId + "hidden").val();

				if (hiddenIdVal == "true") {
					$(this).attr("checked", "checked");
				} else {
					$(this).removeAttr("checked");
				}
			});
		}


		function initAll() {
			initTokenHolders();
			initAddLocationEevent();
			initTooltips();
			initMarkerIconEvents();
			checkedGeoMashupOnInit();
			initGeoMashupEvent() ;
		}

		$(document).ready(function() {
			initAll();

			if (typeof $("ul.tools-tabs-nav").tabs == "function") {
				$("ul.tools-tabs-nav").tabs("div.tools-tab-body", {
					tabs: 'li',
					effect: 'default'
				});
			}
		});


		$(document).ajaxSuccess(
			function (e, x, o) {
				$(document).ready(
					function ($) {
						if (o.data != null)	{
							var indexOf = o.data.indexOf('id_base=comprehensivegooglemap');

							if (indexOf > 0) {
								initAll();
							}
						}
					}
				);
			}
		);

}(jQueryCgmp));
