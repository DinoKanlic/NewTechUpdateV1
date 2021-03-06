/**
 * WP Triggers: main.js
 */

var setting_message_show;
var setting_message_hide;

jQuery(document).ready(function() {
	"use strict";

	var timeObj = new Object();

	//-------error message---------
	setting_message_show = function(str, cclasss, oobj) {
		oobj.removeClass("err_message").removeClass("scss_message");
		oobj.addClass(cclasss).html(str).show();

		clearTimeout(timeObj);

		if (cclasss == "err_message") {
			jQuery(".save-action input").attr("disabled", false);
		}

		timeObj = setTimeout(function() {
			setting_message_hide(oobj);
		}, 2000);
	}


	setting_message_hide = function(oobj) {
		oobj.animate({
			height: "toggle"
		}, 500, function() {
			jQuery(".save-action input").attr("disabled", false);
		});
	}


	//---------default text------------
	jQuery(".default-txt").on("blur", function() {
		if (jQuery(this).val() == "") {
			var txt = jQuery(this).attr('alt');

			jQuery(this).val(txt).css("color", "#a4a4a4");
		}
	});


	jQuery(".default-txt").on("focus", function() {
		if (jQuery(this).val() == jQuery(this).attr("alt")) {
			jQuery(this).val("");
		}

		jQuery(this).css("color", "#464646");
	});


	jQuery(".default-txt").each(function() {
		if (!jQuery(this).val() || jQuery(this).val() == jQuery(this).attr("alt")) {
			jQuery(this).val(jQuery(this).attr("alt")).css("color", "#a4a4a4");

		}

	});




	//---------postbox close/open content--------- 

	jQuery(document).on("click", ".handlediv", function() {
		var obj = jQuery(this).parent();


		if (obj.hasClass("closed")) {
			obj.removeClass("closed");
		} else {
			obj.addClass("closed");
		}
	});


	/* ===== SWITCHER CHECKBOXES ===== */
	var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));


	elems.forEach(function(html) {

		var switchery = new Switchery(html, {
			className: 'switchery trigger-checkbox-small'
		});

	});

	/* ===== SWITCHER CHECKBOXES ===== */




	/* ===== SUB MENU GUIDE ===== */
	var sub_menu_text = jQuery("#admin_sub_menu_guide").text();


	jQuery("#admin_sub_menu_guide").parent().attr("target", "_blank").html(sub_menu_text);

	/* ===== SUB MENU GUIDE ===== */

});


function is_input_type_color_supported() {

	var colorInput = jQuery("<input type='color'/>")[0];

	return colorInput.type === "color" && colorInput.value !== "";

}