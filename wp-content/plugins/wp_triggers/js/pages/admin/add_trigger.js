/**
 * WP Triggers: add_trigger.js
 */
jQuery(document).ready(function() {
	"use strict";

	jQuery("#add_set_button").on("click", function() {
		var obj = this;
		var nnum = jQuery(".trigger_one_set").length;

		nnum++;

		var data = {
			action: "get_trigger_set",
			nnumber: nnum
		};

		jQuery(this).parent().children("span").addClass("loading");

		jQuery.post(ajaxurl, data, function(response) {
			if(response) {
				jQuery(obj).parent().children("span").removeClass("loading");

				jQuery("#trigger_sets").append(response);

				var editor_textarea_id = jQuery("#trigger_sets fieldset").last().find('.display_txt').attr('id');
				wp.editor.remove(editor_textarea_id);
				init_wp_editor(editor_textarea_id);

				var new_trigger_set_checkboxes = jQuery("#trigger_sets fieldset").last().find('.js-switch');

				new_trigger_set_checkboxes.each(function(index, value) {
					var switchery = new Switchery(value, { className: 'switchery trigger-checkbox-small' });
				});
			}
		});
	});


	jQuery(document).on("click", ".trigger_set_remove", function() {
		jQuery(this).parent(".trigger_one_set").fadeOut(function() {
			jQuery(this).remove();
		});

		return false;
	});

	/*jQuery("#trigger_frm").submit(function( event ) {
		event.preventDefault();
	});*/

	jQuery("#trigger_box_save").on("click", function() {

		if(!jQuery("#triggerbox_name").val()) {
			var messaging = jQuery("#add-trigger-content").children(".mymessage");

			setting_message_show("Please insert trigger box name.", "err_message", messaging);

			return false;
		}

		var i = jQuery("input[name='activate']:checked").val();

		if(i) {
			var boxplace = jQuery(".boxPlacement").val();
			var Btn = jQuery("input[name='btnPlace']:checked").val();
			var messaging = jQuery("#add-trigger-content").children(".mymessage");
                    
			if(!Btn) {
				setting_message_show("Please Select Button Option.", "err_message", messaging);
				jQuery("html, body").animate({ scrollTop: 0 }, 600);

				return false;
			}

			if(Btn == '1')	{
				var btnLink = jQuery('.BtnLink').val();

				if(!btnLink) {
					setting_message_show("Please insert Button Label.", "err_message", messaging);
					jQuery("html, body").animate({ scrollTop: 0 }, 600);
					return false;
				}
			} else if(Btn == '2') {
				var BtnImg = jQuery('#customImageButtonURL').val();

				if(!BtnImg) {
					setting_message_show("Please insert Button Image.", "err_message", messaging);
					jQuery("html, body").animate({ scrollTop: 0 }, 600);
					return false;
				}
			}
		}

		if(!wptgg_chk_data()) { return false; }



		var save_sata = get_trigger_data();

		if(i) {
			var boxplace = jQuery("input[name='boxPlacement']:checked").val();
			var Btn = jQuery("input[name='btnPlace']:checked").val();
			var i = jQuery("input[name='activate']:checked").val();

			if(i) {
				jQuery("#hi_button_info").val(i);
			}

			if(boxplace != 'select') {
				jQuery("#hi_button_placement").val(boxplace);
			}


			if(Btn == '1') {
				/*var btnLink = jQuery('.BtnLink').val();
				jQuery("#hi_button_option").val(btnLink);*/

				var BtnOption = get_button_custom_style_data();
				jQuery("#hi_button_option").val(BtnOption);
			} else if(Btn == '2') {
				/*var BtnImg = jQuery('.BtnImg').val();
				jQuery("#hi_button_option").val(BtnImg);*/

				var BtnImg = get_image_button_custom_style_data();
				jQuery("#hi_button_option").val(BtnImg);
			}
		}

		jQuery("#hi_trigger_info").val(save_sata);



		var box_custom_styling_data = get_box_custom_style_data();

		jQuery("#hi_box_custom_style").val(box_custom_styling_data);


		var no_found_data = get_no_found_data();

		jQuery("#hi_no_found_data").val(no_found_data);

		jQuery("#trigger_frm").submit();
	});


	var wptgg_chk_data = function() {
		var chk = true;

		jQuery(".one_set_content").each(function() {
			if( chk ) {
				var messaging = jQuery(this).parent().children(".mymessage");
				var wptgg_valid = jQuery(this).find("input[class=wptrgg_valid]:checked").val();
				var wptgg_html_redirect = jQuery(this).find("input[class=trigger_html_redirect]:checked").val();
				var type_txt = jQuery(this).find(".type_txt").val();
				//var display_txt = jQuery(this).find(".display_txt").val();
				if(tinymce.get(jQuery(this).find(".display_txt").attr("id"))) {
					if(jQuery("#wp-" + jQuery(this).find(".display_txt").attr("id") + "-wrap").hasClass("tmce-active")) {
						var display_txt = tinymce.get(jQuery(this).find(".display_txt").attr("id")).getContent();
					} else {
						var display_txt = jQuery(this).find(".display_txt").val();
					}
				} else {
					var display_txt = jQuery(this).find(".display_txt").val();
				}
				var redirect_url = jQuery(this).find(".trigger_redirect_url").val();
				
				if( wptgg_valid != "email" && !type_txt ) {
					setting_message_show("Oops, you forgot to insert trigger!", "err_message", messaging);
					chk = false;
				}
				
				if(chk) {
					if( wptgg_html_redirect == "html" && !display_txt ) {
						setting_message_show("Oops, you forgot to insert html!", "err_message", messaging);
						chk = false;
					}
				}
				
				if(chk) {
					if( wptgg_html_redirect == "redirect" && !redirect_url ) {
						setting_message_show("Oops, you forgot to insert redirect url!", "err_message", messaging);
						chk = false;
					}
				}
			}
		});
		return chk ;
	}




	var get_trigger_data = function() {
		var saveData = {};

		var i = 1;

		jQuery(".one_set_content").each(function() {
			var oneData = {};
			oneData["trigger_email_valid"] = jQuery(this).find("input[class=wptrgg_valid]:checked").val();
			oneData["type_txt"] = get_valid_triggger_arr(jQuery(this).find(".type_txt").val());
			oneData["html_redirect"] = jQuery(this).find("input[class=trigger_html_redirect]:checked").val();
			//oneData["display_txt"] = jQuery(this).find(".display_txt").val();
			if(tinymce.get(jQuery(this).find(".display_txt").attr("id"))) {
				if(jQuery("#wp-" + jQuery(this).find(".display_txt").attr("id") + "-wrap").hasClass("tmce-active")) {
					oneData["display_txt"] = tinymce.get(jQuery(this).find(".display_txt").attr("id")).getContent();
				} else {
					oneData["display_txt"] = jQuery(this).find(".display_txt").val();
				}
			} else {
				oneData["display_txt"] = jQuery(this).find(".display_txt").val();
			}
			if(jQuery(this).find(".shortcode_chk").prop("checked")) {
				oneData["shortcode_chk"] = "checked";
			}
			oneData["redirect_url"] = jQuery(this).find(".trigger_redirect_url").val();

			saveData["data_" + i] = oneData;

			i++;
		});

		return jQuery.toJSON(saveData);
	}


	var get_button_data = function() {
		var oneData = {};

		var boxplace = jQuery(".boxPlacement").val();
		var Btn = jQuery("input[name='btnPlace']:checked").val();
		var i = jQuery("input[name='activate']:checked").val();

		if(i) {
			oneData["activate"] = i;
		}

		if(boxplace != 'select') {
			oneData["boxplacement"] = boxplace;
		}

		if(Btn == '1') {
			var btnLink = jQuery('.BtnLink').val();
			oneData["boxoption"] = btnLink;
			oneData["boxbtn_value"] = Btn;
		} else if(Btn == '2') {
			var BtnImg = jQuery('.BtnImg').val();
			oneData["boxoption"] = BtnImg;
			oneData["boxbtn_value"] = Btn;
		}

		return jQuery.toJSON(oneData);
	}
	
	var get_valid_triggger_arr = function(type_txt) {
		var redata = [];
		var temp = type_txt.split("\n");
		var i = 0;

		if(temp.length > 0) {
			for(i=0; i<temp.length; i++) {
				if( temp[i] )redata.push(temp[i]);
			}
			return redata;
		}

		return "";
 	}

	jQuery(document).on("click", ".wptrgg_valid", function() {	
		if(jQuery(this).val() == "trigger") {
			jQuery(this).parents(".one_set_content").find(".type_txt").attr("disabled", false).css("opacity", 1);			
		} else {
			jQuery(this).parents(".one_set_content").find(".type_txt").attr("disabled", true).css("opacity", 0.4);
		}
	});

	jQuery(document).on("click", ".trigger_html_redirect", function() {	
		if(jQuery(this).val() == "html") {
			jQuery(this).parents(".one_set_content").find(".display_txt").attr("disabled", false).css("opacity", 1);
			jQuery(this).parents(".one_set_content").find(".shortcode_chk_cont").css("opacity", 1);
			jQuery(this).parents(".one_set_content").find(".trigger_redirect_url").attr("disabled", true).css("opacity", 0.4);
		} else {
			jQuery(this).parents(".one_set_content").find(".display_txt").attr("disabled", true).css("opacity", 0.4);
			jQuery(this).parents(".one_set_content").find(".shortcode_chk_cont").css("opacity", 0.4);
			jQuery(this).parents(".one_set_content").find(".trigger_redirect_url").attr("disabled", false).css("opacity", 1);
		}
	});

	var get_box_custom_style_data = function() {
		var saveData = {};

		saveData["data_customTextAlignment"]	 = jQuery("#customTextAlignment").val();
		saveData["data_customTextColor"]	 = jQuery("#customTextColor").val();
		saveData["data_customBackgroundColor"]	 = jQuery("#customBackgroundColor").val();
		saveData["data_customBorderColor"]	 = jQuery("#customBorderColor").val();
		saveData["data_customTextSize"]		 = jQuery("#customTextSize").val();
		saveData["data_customCornerRadius"]	 = jQuery("#customCornerRadius").val();
		saveData["data_customVerticalPadding"]	 = jQuery("#customVerticalPadding").val();
		saveData["data_customHorizontalPadding"] = jQuery("#customHorizontalPadding").val();

		return jQuery.toJSON(saveData);
	}

	var get_button_custom_style_data = function() {
		var saveData = {};

		saveData["BtnLink"] = jQuery("#BtnLink").val();

		if(jQuery('input[name=btnCustomStyle]:checked').val() == 'yes') {
			saveData["data_customButtonBackgroundColor"]	 = jQuery("#customButtonBackgroundColor").val();
			saveData["data_customButtonCornerRadius"]	 = jQuery("#customButtonCornerRadius").val();
			saveData["data_customButtonVerticalPadding"]	 = jQuery("#customButtonVerticalPadding").val();
			saveData["data_customButtonHorizontalPadding"]	 = jQuery("#customButtonHorizontalPadding").val();
			saveData["data_customButtonTextColor"]		 = jQuery("#customButtonTextColor").val();
			saveData["data_customButtonTextSize"]		 = jQuery("#customButtonTextSize").val();
		}


		return jQuery.toJSON(saveData);
	}

	var get_image_button_custom_style_data = function() {
		var saveData = {};

		saveData["data_customImageButtonURL"]			 = jQuery("#customImageButtonURL").val();
		saveData["data_customImageButtonVerticalMargin"]	 = jQuery("#customImageButtonVerticalMargin").val();
		saveData["data_customImageButtonHorizontalMargin"]	 = jQuery("#customImageButtonHorizontalMargin").val();


		return jQuery.toJSON(saveData);
	}

	var get_no_found_data = function() {
		var saveData = {};

		saveData["no_found_html_redirect"] = jQuery("input[name='no_found_html_redirect']:checked").val();
		//saveData["no_found_txt"]	   = jQuery("#no_found_txt").val();
		if(tinymce.get("no_found_txt")) {
			if(jQuery("#wp-no_found_txt-wrap").hasClass("tmce-active")) {
				saveData["no_found_txt"] = tinymce.get("no_found_txt").getContent();
			} else {
				saveData["no_found_txt"] = jQuery("#no_found_txt").val();
			}
		} else {
			saveData["no_found_txt"] = jQuery("#no_found_txt").val();
		}
		if(jQuery("#no_found_shortcode").prop("checked")) {
			saveData["no_found_shortcode"] = "checked";
		}
		saveData["no_found_redirect_url"]  = jQuery("#no_found_redirect_url").val();

		saveData["no_found_optinbox"]               = jQuery("#no_found_optinbox:checked").val();
		saveData["no_found_optinbox_placeholder"]   = jQuery("#no_found_optinbox_placeholder").val();
		saveData["no_found_optinbox_button_label"]  = jQuery("#no_found_optinbox_button_label").val();
		saveData["no_found_optinbox_html_redirect"] = jQuery("#no_found_optinbox_html_redirect").val();
		if(tinymce.get("no_found_optinbox_txt")) {
			if(jQuery("#wp-no_found_optinbox_txt-wrap").hasClass("tmce-active")) {
				saveData["no_found_optinbox_txt"] = tinymce.get("no_found_optinbox_txt").getContent();
			} else {
				saveData["no_found_optinbox_txt"] = jQuery("#no_found_optinbox_txt").val();
			}
		} else {
			saveData["no_found_optinbox_txt"] = jQuery("#no_found_optinbox_txt").val();
		}
		if(jQuery("#no_found_optinbox_shortcode").prop("checked")) {
			saveData["no_found_optinbox_shortcode"] = "checked";
		}
		saveData["no_found_optinbox_html_redirect"] = jQuery("input[name='no_found_optinbox_html_redirect']:checked").val();
		saveData["no_found_optinbox_redirect_url"]  = jQuery("#no_found_optinbox_redirect_url").val();



		return jQuery.toJSON(saveData) ;
	}


	/* ========== TRIGGER BOX STYLING ========== */

	// ----- Apply custom style -----
	if(jQuery("#apply_custom_styles").prop("checked")) {
		jQuery(".trigger-box-styling-options-container").show();
	} else {
		jQuery(".trigger-box-styling-options-container").hide();
	}

	jQuery("#apply_custom_styles").change(function() {
		if(jQuery(this).prop("checked")) {
			jQuery(".trigger-box-styling-options-container").fadeIn(500);
		} else {
			jQuery(".trigger-box-styling-options-container").fadeOut(500);
		}
	});
	// ----- Apply custom style -----


	// ----- Text Color -----
	jQuery("#customTextColor").change(function() {
		jQuery("#customTextColor_textfield").val(jQuery(this).val());
	});

	jQuery("#customTextColor_textfield").keyup(function() {
		jQuery("#customTextColor").val(jQuery(this).val());

		if(!is_input_type_color_supported()) {
			var color_code = jQuery(this).val();

			jQuery("#customTextColor").spectrum({
				color: color_code
			});
		}
	});
	// ----- Text Color -----


	// ----- Background Color -----
	jQuery("#customBackgroundColor").change(function() {
		jQuery("#customBackgroundColor_textfield").val(jQuery(this).val());
	});

	jQuery("#customBackgroundColor_textfield").keyup(function() {
		jQuery("#customBackgroundColor").val(jQuery(this).val());

		if(!is_input_type_color_supported()) {
			var color_code = jQuery(this).val();

			jQuery("#customBackgroundColor").spectrum({
				color: color_code
			});
		}
	});
	// ----- Background Color -----


	// ----- Border Color -----
	jQuery("#customBorderColor").change(function() {
		jQuery("#customBorderColor_textfield").val(jQuery(this).val());
	});

	jQuery("#customBorderColor_textfield").keyup(function() {
		jQuery("#customBorderColor").val(jQuery(this).val());

		if(!is_input_type_color_supported()) {
			var color_code = jQuery(this).val();

			jQuery("#customBorderColor").spectrum({
				color: color_code
			});
		}
	});
	// ----- Border Color -----


	/* ========== TRIGGER BOX STYLING ========== */



	/* ========== TRIGGER BUTTON STYLING ========== */

	// ----- Activate button -----
	if(jQuery("#activate").prop("checked")) {
		jQuery("#trigger_button_options_container").show();
	} else {
		jQuery("#trigger_button_options_container").hide();
	}

	jQuery("#activate").change(function() {
		if(jQuery(this).prop("checked")) {
			jQuery("#trigger_button_options_container").fadeIn(500);
		} else {
			jQuery("#trigger_button_options_container").fadeOut(500);
		}
	});
	// ----- Activate button -----


	// ----- Background Color -----
	jQuery("#customButtonBackgroundColor").change(function() {
		jQuery("#customButtonBackgroundColor_textfield").val(jQuery(this).val());
	});

	jQuery("#customButtonBackgroundColor_textfield").keyup(function() {
		jQuery("#customButtonBackgroundColor").val(jQuery(this).val());

		if(!is_input_type_color_supported()) {
			var color_code = jQuery(this).val();

			jQuery("#customButtonBackgroundColor").spectrum({
				color: color_code
			});
		}
	});
	// ----- Background Color -----


	// ----- Text Color -----
	jQuery("#customButtonTextColor").change(function() {
		jQuery("#customButtonTextColor_textfield").val(jQuery(this).val());
	});

	jQuery("#customButtonTextColor_textfield").keyup(function() {
		jQuery("#customButtonTextColor").val(jQuery(this).val());

		if(!is_input_type_color_supported()) {
			var color_code = jQuery(this).val();

			jQuery("#customButtonTextColor").spectrum({
				color: color_code
			});
		}
	});
	// ----- Text Color -----


	/* ========== TRIGGER BUTTON STYLING ========== */



	/* ========== REMOVE PARAMETER "wp-trigger-status" FROM URL ========== */

	if ( window.history.replaceState ) {
		window.history.replaceState( null, null, window.location.href.replace(/&?wp-trigger-status=([^&]$|[^&]*)/i, "") + window.location.hash );
	}

	/* ========== REMOVE PARAMETER "wp-trigger-status" FROM URL ========== */



	/* ========== OPTIN BOX ========== */

	if(jQuery("#no_found_optinbox").prop("checked")) {
		jQuery(".wptgg-optinbox-options-container").show();
	} else {
		jQuery(".wptgg-optinbox-options-container").hide();
	}

	jQuery("#no_found_optinbox").change(function() {
		if(jQuery(this).prop("checked")) {
			jQuery(".wptgg-optinbox-options-container").fadeIn(500);
		} else {
			jQuery(".wptgg-optinbox-options-container").fadeOut(500);
		}
	});

	/* ========== OPTIN BOX ========== */


});

function change_option_page_title(pagetitle, search_string) {
	var title = jQuery('head title').text();

	jQuery('head title').text(title.replace(search_string, pagetitle));
}

function init_wp_editor(editor_id) {
	wp.editor.initialize( editor_id, {
		tinymce: {
			wpautop  : true,
			theme    : 'modern',
			skin     : 'lightgray',
			language : 'en',
			formats  : {
				alignleft  : [
					{ selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: { textAlign: 'left' } },
					{ selector: 'img,table,dl.wp-caption', classes: 'alignleft' }
				],
				aligncenter: [
					{ selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: { textAlign: 'center' } },
					{ selector: 'img,table,dl.wp-caption', classes: 'aligncenter' }
				],
				alignright : [
					{ selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: { textAlign: 'right' } },
					{ selector: 'img,table,dl.wp-caption', classes: 'alignright' }
				],
				strikethrough: { inline: 'del' }
			},
			relative_urls       : false,
			remove_script_host  : false,
			convert_urls        : false,
			browser_spellcheck  : true,
			fix_list_elements   : true,
			entities            : '38,amp,60,lt,62,gt',
			entity_encoding     : 'raw',
			keep_styles         : false,
			paste_webkit_styles : 'font-weight font-style color',
			preview_styles      : 'font-family font-size font-weight font-style text-decoration text-transform',
			tabfocus_elements   : ':prev,:next',
			plugins    : 'charmap,hr,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpeditimage,wpgallery,wplink,wpdialogs,wpview',
			resize     : 'vertical',
			menubar    : false,
			indent     : false,
			toolbar1   : 'bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv',
			toolbar2   : 'formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',
			toolbar3   : '',
			toolbar4   : '',
			body_class : 'id post-type-post post-status-publish post-format-standard',
			wpeditimage_disable_captions: false,
			wpeditimage_html5_captions  : true
		},
		quicktags   : true,
		mediaButtons: true
	} );
}
