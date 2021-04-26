jQuery(document).ready(function() {
	"use strict";

	jQuery('.wptrigger1').each(function() {
		var iVAl = jQuery(this).val();
		if (!iVAl) {
			jQuery(this).next(".alignment").attr("disabled", "disabled");
		}
	});

	jQuery(".wptrigger1").keyup(function() {
		jQuery(this).next(".alignment").removeAttr("disabled", "disabled");
		jQuery(this).next(".alignment").attr("enabled", "enabled");
	});

	/* ========== CLICK ON BUTTON ========== */
	jQuery(document).on("click", ".alignment", function(event) {
		proceed_data_after_button_click(this, event);
	});
	/* ========== CLICK ON BUTTON ========== */

	/* ========== ENTERING THE ZIP IN FIELD ========== */
	jQuery(document).on("keyup", ".wptgg_pass_key1", function(event) {
		if (event.keyCode == 13) {
			proceed_data_after_button_click(this.parentElement.querySelector('.alignment'), event);
		}
	});
	/* ========== ENTERING THE ZIP IN FIELD ========== */


	function proceed_data_after_button_click(_this, event) {
		if (!jQuery(_this).prevAll('.wptgg_pass_key1').val()) return;

		var obj = _this;

		var triggerid = jQuery(_this).prevAll('.wptgg_pass_key1').parents(".wptrigger_contents").find(".hi_wptgg_id").val();
		var pass_key = jQuery(_this).prevAll('.wptgg_pass_key1').parents(".wptrigger_contents").find(".wptgg_pass_key1").val();

		jQuery(_this).prevAll('.wptgg_pass_key1').parents(".wptrigger_contents").find(".hi_pass_key").val(pass_key);

		var data = {
			action: "get_display_trigger",
			wptgg_id: triggerid,
			passkey: pass_key
		};

		// AJAX-spinner start
		jQuery.post(wptgg_ajaxurl, data, function(response) {
			info = jQuery.parseJSON(response);

			// AJAX-spinner stop
			jQuery(obj).parent().children("span").removeClass("wptgg_loading");

			if (response) {
				var info = {};

				info = jQuery.parseJSON(response);

				// OptinBox
				//attach_event_to_optinbox();

				// Add history id
				jQuery(obj).parents(".wptrigger_contents").find(".hi_wptgg_history_id").val(info["history_id"]);

				if (info["redirect"]) {
					window.open(info["redirect"], "_self");

					return;
				}

				if (info["shortcode"] == "checked" && info["valid_status"] == "yes") {
					jQuery(obj).parents(".wptrigger_contents").find(".hi_wptrr_pass_keys").attr("name", "hi_wptrr_pass_keys");
					jQuery(obj).parents(".wptrigger_contents").find(".hi_wptrr_pass_keys").val(get_wptgg_info());
					jQuery(obj).parents(".wptrigger_contents").find(".wptgg_form").submit();

					return;
				}

				if (info["shortcode"] == "checked" && info["valid_status"] == "no") {
					jQuery(obj).parents(".wptrigger_contents").find(".hi_wptrr_pass_keys").attr("name", "hi_wptrr_pass_keys");
					jQuery(obj).parents(".wptrigger_contents").find(".hi_wptrr_pass_keys").val(get_wptgg_info());
					jQuery(obj).parents(".wptrigger_contents").find(".wptgg_form").submit();

					return;
				}

				if (info["show_chk"] == "checked") {
					jQuery(obj).parent(".wptrigger_content").html(info["display_txt"]);
				} else {
					jQuery(obj).parent(".wptrigger_content").children(".wptrigger_append").html(info["display_txt"]);
				}

				set_trigger_box_layout();

			}
		});
	}


	jQuery(document).on("keyup", ".wptgg_pass_key", function(event) {
		if (event.keyCode == 13) {
			if (!jQuery(this).val()) return;

			var obj = this;
			var triggerid = jQuery(this).parents(".wptrigger_contents").find(".hi_wptgg_id").val();
			var pass_key = jQuery(this).parents(".wptrigger_contents").find(".wptgg_pass_key").val();

			jQuery(this).parents(".wptrigger_contents").find(".hi_pass_key").val(pass_key);

			var data = {
				action: "get_display_trigger",
				wptgg_id: triggerid,
				passkey: pass_key
			};

			// AJAX-spinner start
			jQuery.post(wptgg_ajaxurl, data, function(response) {
				info = jQuery.parseJSON(response);

				// AJAX-spinner stop
				jQuery(obj).parent().children("span").removeClass("wptgg_loading");

				if (response) {
					var info = {};

					info = jQuery.parseJSON(response);

					// Add history id
					jQuery(obj).parents(".wptrigger_contents").find(".hi_wptgg_history_id").val(info["history_id"]);


					if (info["redirect"]) {
						window.open(info["redirect"], "_self");

						return;
					}

					if (info["shortcode"] == "checked" && info["valid_status"] == "yes") {
						jQuery(obj).parents(".wptrigger_contents").find(".hi_wptrr_pass_keys").attr("name", "hi_wptrr_pass_keys");
						jQuery(obj).parents(".wptrigger_contents").find(".hi_wptrr_pass_keys").val(get_wptgg_info());
						jQuery(obj).parents(".wptrigger_contents").find(".wptgg_form").submit();

						return;
					}

					if (info["shortcode"] == "checked" && info["valid_status"] == "no") {
						jQuery(obj).parents(".wptrigger_contents").find(".hi_wptrr_pass_keys").attr("name", "hi_wptrr_pass_keys");
						jQuery(obj).parents(".wptrigger_contents").find(".hi_wptrr_pass_keys").val(get_wptgg_info());
						jQuery(obj).parents(".wptrigger_contents").find(".wptgg_form").submit();

						return;
					}

					if (info["show_chk"] == "checked") {
						jQuery(obj).parent(".wptrigger_content").html(info["display_txt"]);
					} else {
						jQuery(obj).parent(".wptrigger_content").children(".wptrigger_append").html(info["display_txt"]);
					}

					set_trigger_box_layout();

				}
			});

		}
	});


	/* ========== OPTIN BOX ACTION ========== */

	attach_event_to_optinbox();

	function proceed_optin_box_action(_this, event) {
		if (!jQuery(_this).parents().find(".wptgg-optinbox").val()) return;

		var obj = _this;
		var triggerid = jQuery(_this).parents(".wptrigger_contents").find(".hi_wptgg_id").val();
		var pass_key = jQuery(_this).parents(".wptrigger_contents").find(".hi_pass_key").val();
		var optinbox_value = jQuery(_this).parents(".wptrigger_contents").find(".wptgg-optinbox").val();
		var history_id = jQuery(_this).parents(".wptrigger_contents").find(".hi_wptgg_history_id").val();


		var data = {
			action: "get_display_optinbox",
			wptgg_id: triggerid,
			passkey: pass_key,
			optinbox_value: optinbox_value,
			history_id: history_id
		};


		// AJAX-spinner start

		jQuery.post(wptgg_ajaxurl, data, function(response) {
			info = jQuery.parseJSON(response);

			// AJAX-spinner stop
			jQuery(obj).parent().children("span").removeClass("wptgg_loading");

			if (response) {
				var info = {};

				info = jQuery.parseJSON(response);

				if (info["redirect"]) {
					window.open(info["redirect"], "_self");

					return;
				}


				if (info["shortcode"] == "checked") {
					jQuery(obj).parents(".wptrigger_contents").find(".hi_wptrr_pass_keys").attr("name", "hi_wptrr_pass_keys");
					jQuery(obj).parents(".wptrigger_contents").find(".hi_wptrr_pass_keys").val(get_wptgg_info());
					jQuery(obj).parents(".wptrigger_contents").find(".hi_wptgg_optinbox_sent").val("true");
					jQuery(obj).parents(".wptrigger_contents").find(".wptgg_form").submit();

					return;
				}

				jQuery(obj).parents(".wptrigger_contents").find(".wptrigger_append").html(info["display_txt"]);

			}
		});
	}

	function attach_event_to_optinbox() {

		jQuery(document).on("keyup", ".wptgg-optinbox", function(event) {
			if (event.keyCode == 13) {
				proceed_optin_box_action(this, event);
			}
		});

		jQuery(document).on("click", ".wptgg-optinbox-btn", function(event) {
			proceed_optin_box_action(this, event);
		});

	}

	/* ========== OPTIN BOX ACTION ========== */


	var get_wptgg_info = function() {
		var wptgg_info = {};

		jQuery(".wptrigger_contents").each(function() {

			var trigger_key = jQuery(this).find(".hi_pass_key").val();
			var trigger_num = jQuery(this).find(".hi_wptgg_num").val();

			if (trigger_key) wptgg_info["wptrr_" + trigger_num] = trigger_key;
		});

		return jQuery.toJSON(wptgg_info);
	}


	/* ---------- ONLY ALLOW NUMBERS ---------- */
	jQuery('.wptgg_only_number').keydown(function(e) {
		if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 || (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) || (e.keyCode >= 35 && e.keyCode <= 40)) {
			return;
		}

		if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
			e.preventDefault();
		}
	});
	/* ---------- ONLY ALLOW NUMBERS ---------- */


	/* ---------- SCROLL TO ---------- */
	jQuery(window).scrollTo('.wptgg-bookmark');
	/* ---------- SCROLL TO ---------- */


	/* ---------- SHAKE ---------- */
	jQuery('.wptgg-animated').wptrigger_appear();

	jQuery('.wptgg-animated').each(function() {
		var current_trigger = jQuery(this);

		if (current_trigger.is(':wptrigger_appeared')) {
			setTimeout(function() {
				current_trigger.addClass('animated tada');
			}, 1000);
		}

		current_trigger.on('wptrigger_appear', function(event, $all_appeared_elements) {
			setTimeout(function() {
				current_trigger.addClass('animated tada');
			}, 1000);
		});

		current_trigger.on('wptrigger_disappear', function(event, $all_appeared_elements) {
			setTimeout(function() {
				current_trigger.removeClass('animated tada');
			}, 1000);
		});
	});
	/* ---------- SHAKE ---------- */



	/* ---------- BUTTON LAYOUT ---------- */

	set_trigger_box_layout();

	function set_trigger_box_layout() {

		// ----- below 100% 100% -----
		var below_100_100_input = jQuery('.below_100_100:not(.wptgg-optinbox-btn)');

		below_100_100_input.each(function() {
			var below_100_100_css = '';
			var below_100_100_parent_css = '';

			if (jQuery(this)[0]) {
				if (jQuery(this)[0].hasAttribute('style')) {
					below_100_100_css = jQuery(this).attr('style');
				}

				if (jQuery(this).parent().find('.wptrigger1, .wptrigger')[0].hasAttribute('style')) {
					below_100_100_parent_css = jQuery(this).parent().find('.wptrigger1, .wptrigger').attr('style');
				}
			}

			below_100_100_css += 'margin-top: 10px;text-transform: none;';

			if (jQuery(this).attr('type') != 'image') {
				jQuery(this).css('cssText', below_100_100_css + 'width: 100% !important;');
			} else {
				jQuery(this).parent().css('cssText', 'text-align: center !important;');
			}

			jQuery(this).parent().find('.wptrigger1, .wptrigger').css('cssText', below_100_100_parent_css + 'width: 100% !important;');

		});

		// OPTINBOX
		var below_100_100_input_optinbox = jQuery('.below_100_100.wptgg-optinbox-btn');

		below_100_100_input_optinbox.each(function() {
			var below_100_100_css = '';
			var below_100_100_parent_css = '';

			if (jQuery(this)[0]) {
				if (jQuery(this)[0].hasAttribute('style')) {
					below_100_100_css = jQuery(this).attr('style');
				}

				if (jQuery(this).parent().find('.wptgg-optinbox')[0].hasAttribute('style')) {
					below_100_100_parent_css = jQuery(this).parent().find('.wptgg-optinbox').attr('style');
				}
			}

			below_100_100_css += 'margin-top: 10px;text-transform: none;';

			if (jQuery(this).attr('type') != 'image') {
				jQuery(this).css('cssText', below_100_100_css + 'width: 100% !important;');
			} else {
				jQuery(this).parent().css('cssText', 'text-align: center !important;');
			}

			jQuery(this).parent().find('.wptgg-optinbox').css('cssText', below_100_100_parent_css + 'width: 100% !important;');
		});
		// ----- below 100% 100% -----


		// ----- below 100% 60% -----
		var below_100_60_input = jQuery('.below_100_60:not(.wptgg-optinbox-btn)');

		below_100_60_input.each(function() {
			var below_100_60_css = '';
			var below_100_60_parent_css = '';
			var below_100_60_textbox_css = '';

			if (jQuery(this)[0]) {
				if (jQuery(this)[0].hasAttribute('style')) {
					below_100_60_css = jQuery(this).attr('style');
				}

				if (jQuery(this).parent()[0].hasAttribute('style')) {
					below_100_60_parent_css = jQuery(this).parent().attr('style');
				}

				if (jQuery(this).parent().find('.wptrigger1, .wptrigger')[0].hasAttribute('style')) {
					below_100_60_textbox_css = jQuery(this).parent().find('.wptrigger1, .wptrigger').attr('style');
				}
			}

			below_100_60_css += 'margin-top: 10px;text-transform: none;';

			if (jQuery(this).attr('type') != 'image') {
				jQuery(this).css('cssText', below_100_60_css + 'width: 60% !important;');
			} else {
				jQuery(this).css('cssText', below_100_60_css);
			}

			jQuery(this).parent().css('cssText', below_100_60_parent_css + 'text-align: center !important;');
			jQuery(this).parent().find('.wptrigger1, .wptrigger').css('cssText', below_100_60_textbox_css + 'width: 100% !important;');
		});


		// OPTINBOX
		var below_100_60_input_optinbox = jQuery('.below_100_60.wptgg-optinbox-btn');

		below_100_60_input_optinbox.each(function() {
			var below_100_60_css = '';
			var below_100_60_parent_css = '';
			var below_100_60_textbox_css = '';

			if (jQuery(this)[0]) {
				if (jQuery(this)[0].hasAttribute('style')) {
					below_100_60_css = jQuery(this).attr('style');
				}

				if (jQuery(this).parent()[0].hasAttribute('style')) {
					below_100_60_parent_css = jQuery(this).parent().attr('style');
				}

				if (jQuery(this).parent().find('.wptgg-optinbox')[0].hasAttribute('style')) {
					below_100_60_textbox_css = jQuery(this).parent().find('.wptgg-optinbox').attr('style');
				}
			}

			below_100_60_css += 'margin-top: 10px;text-transform: none;';

			if (jQuery(this).attr('type') != 'image') {
				jQuery(this).css('cssText', below_100_60_css + 'width: 60% !important;');
			} else {
				jQuery(this).css('cssText', below_100_60_css);
			}

			jQuery(this).parent().css('cssText', below_100_60_parent_css + 'text-align: center !important;');
			jQuery(this).parent().find('.wptgg-optinbox').css('cssText', below_100_60_textbox_css + 'width: 100% !important;');
		});
		// ----- below 100% 60% -----


		// ----- right 75% 25% -----
		var right_75_25_input = jQuery('.right_75_25:not(.wptgg-optinbox-btn)');

		right_75_25_input.each(function() {
			var right_75_25_css = '';
			var right_75_25_parent_css = '';

			if (jQuery(this)[0]) {
				if (jQuery(this)[0].hasAttribute('style')) {
					right_75_25_css = jQuery(this).attr('style');
				}

				if (jQuery(this).parent().find('.wptrigger1, .wptrigger')[0].hasAttribute('style')) {
					right_75_25_parent_css = jQuery(this).parent().find('.wptrigger1, .wptrigger').attr('style');
				}
			}

			right_75_25_css += 'margin-left: 10px;text-transform: none;';

			if (jQuery(this).attr('type') != 'image') {
				jQuery(this).css('cssText', right_75_25_css + 'width: 25% !important;');
			} else {
				jQuery(this).css('cssText', right_75_25_css);
			}

			jQuery(this).parent().find('.wptrigger1, .wptrigger').css('cssText', right_75_25_parent_css + 'float: left !important;width: calc(75% - 10px) !important;box-sizing: border-box !important;');
		});


		// OPTINBOX
		var right_75_25_input_optinbox = jQuery('.right_75_25.wptgg-optinbox-btn');

		right_75_25_input_optinbox.each(function() {
			var right_75_25_css = '';
			var right_75_25_parent_css = '';

			if (jQuery(this)[0]) {
				if (jQuery(this)[0].hasAttribute('style')) {
					right_75_25_css = jQuery(this).attr('style');
				}

				if (jQuery(this).parent().find('.wptgg-optinbox')[0].hasAttribute('style')) {
					right_75_25_parent_css = jQuery(this).parent().find('.wptgg-optinbox').attr('style');
				}
			}

			right_75_25_css += 'margin-left: 10px;text-transform: none;';

			if (jQuery(this).attr('type') != 'image') {
				jQuery(this).css('cssText', right_75_25_css + 'width: 25% !important;');
			} else {
				jQuery(this).css('cssText', right_75_25_css);
			}

			jQuery(this).parent().find('.wptgg-optinbox').css('cssText', right_75_25_parent_css + 'float: left !important;width: calc(75% - 10px) !important;box-sizing: border-box !important;');
		});
		// ----- right 75% 25% -----


		// ----- right 50% 50% -----
		var right_50_50_input = jQuery('.right_50_50:not(.wptgg-optinbox-btn)');

		right_50_50_input.each(function() {
			var right_50_50_css = '';
			var right_50_50_parent_css = '';

			if (jQuery(this)[0]) {
				if (jQuery(this)[0].hasAttribute('style')) {
					right_50_50_css = jQuery(this).attr('style');
				}

				if (jQuery(this).parent().find('.wptrigger1, .wptrigger')[0].hasAttribute('style')) {
					right_50_50_parent_css = jQuery(this).parent().find('.wptrigger1, .wptrigger').attr('style');
				}
			}

			right_50_50_css += 'margin-left: 10px;text-transform: none;';

			if (jQuery(this).attr('type') != 'image') {
				jQuery(this).css('cssText', right_50_50_css + ';width: 50% !important;');
			} else {
				jQuery(this).css('cssText', right_50_50_css);
			}

			jQuery(this).parent().find('.wptrigger1, .wptrigger').css('cssText', right_50_50_parent_css + ';float: left !important;width: calc(50% - 10px) !important;box-sizing: border-box !important;');
		});


		// OPTINBOX
		var right_50_50_input_optinbox = jQuery('.right_50_50.wptgg-optinbox-btn');

		right_50_50_input_optinbox.each(function() {
			var right_50_50_css = '';
			var right_50_50_parent_css = '';

			if (jQuery(this)[0]) {
				if (jQuery(this)[0].hasAttribute('style')) {
					right_50_50_css = jQuery(this).attr('style');
				}

				if (jQuery(this).parent().find('.wptgg-optinbox')[0].hasAttribute('style')) {
					right_50_50_parent_css = jQuery(this).parent().find('.wptgg-optinbox').attr('style');
				}
			}

			right_50_50_css += 'margin-left: 10px;text-transform: none;';

			if (jQuery(this).attr('type') != 'image') {
				jQuery(this).css('cssText', right_50_50_css + ';width: 50% !important;');
			} else {
				jQuery(this).css('cssText', right_50_50_css);
			}

			jQuery(this).parent().find('.wptgg-optinbox').css('cssText', right_50_50_parent_css + ';float: left !important;width: calc(50% - 10px) !important;box-sizing: border-box !important;');
		});
		// ----- right 50% 50% -----

	}


	/* ---------- BUTTON LAYOUT ---------- */


});