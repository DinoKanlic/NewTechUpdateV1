<?php 

if(isset($_POST["hi_wptrr_pass_keys"])) {
	$wpttr_keys = json_decode(stripcslashes($_POST["hi_wptrr_pass_keys"]));
}

global $wpdb;

$id = intval($param['id']);

$table_name = $wpdb->prefix . "trigger";

$sql = $wpdb->prepare("SELECT * FROM ".$table_name." where ID=%d", array($id));
 
$trigger_data = $wpdb->get_results($sql); 


$objkey = "wptrr_" . $wptrr_num;


if($trigger_data[0]->apply_custom_styles == 'on') {
	$trigger_box_custom_style_data = json_decode($trigger_data[0]->box_custom_style);

	echo wptggFrontEnd::get_trigger_placeholder_style($trigger_data[0]->ID, $trigger_box_custom_style_data->data_customTextColor);
}


echo '<div class="wptrigger_contents">';
echo '<a name="wptriggers'.$trigger_data[0]->ID.'"></a>';

if(isset($wpttr_keys->$objkey)) {

	$wptgg_optinbox_sent = "false";

	if(isset($_POST["hi_wptgg_optinbox_sent"])) {
		$wptgg_optinbox_sent = $_POST["hi_wptgg_optinbox_sent"];
	}

	$keydata = array(
		"wptrr_id"      => $param['id'],
		"trigger"       => $wpttr_keys->$objkey,
		"method"        => "load",
		"optinbox_sent" => $wptgg_optinbox_sent
	);

	if(isset($_POST["hi_wptgg_history_id"])) {
		$wptgg_history_id = $_POST["hi_wptgg_history_id"];
	}

	$info = wptggFrontEnd::get_display_trigger_content($keydata);

	/* SCROLL TO */
	$scroll_to_css_class = '';
	if($info["scroll_to_chk"] == 'checked') {
		$scroll_to_css_class = ' wptgg-bookmark';
	}
	/* SCROLL TO */


	echo '<div class="wptrigger_content '.$scroll_to_css_class.'">';

	if( $info["show_chk"] == "checked" ) {
		echo $info["display_txt"];
	} else {
		/* CUSTOM STYLE */
		$trigger_box_custom_style_css = '';
		$trigger_box_custom_class = '';

		if($trigger_data[0]->apply_custom_styles == 'on') {
			$trigger_box_custom_style_css = wptggFrontEnd::get_trigger_box_custom_style($trigger_data[0]->box_custom_style);
		}

		$trigger_box_custom_class = 'wptrigger'.$trigger_data[0]->ID.'-triggerbox';
		$trigger_button_custom_class = 'wptrigger'.$trigger_data[0]->ID.'-button';
		/* CUSTOM STYLE */

		/* ONLY ALLOW NUMBER */
		$only_number_css_class = '';
		if($info['only_number_chk'] == 'checked') {
			$only_number_css_class = 'wptgg_only_number';
		}
		/* ONLY ALLOW NUMBER */

		/* ANIMATE TRIGGER BOX */
		$shake_css_class = '';
		if($info['shake_chk'] == 'checked') {
			$shake_css_class = 'wptgg-animated';
		}
		/* ANIMATE TRIGGER BOX */


		if($trigger_data[0]->btnactivate =='on'):
			$trigger_button_option = json_decode($trigger_data[0]->option);
			echo ' <input type="text" class="wptgg_pass_key wptrigger '.$only_number_css_class.' '.$shake_css_class.' '.$trigger_box_custom_class.'" placeholder="'.$trigger_data[0]->placeholder.'" '.(strlen($trigger_box_custom_style_css) > 0 ? ' style="'.$trigger_box_custom_style_css.'"' : '').' value="' . $wpttr_keys->$objkey . '" /> ';

			if($trigger_data[0]->btn_value == '1'):
				$trigger_button_custom_style_css = wptggFrontEnd::get_trigger_button_custom_style($trigger_data[0]->option);

				echo  '<input type="submit" class="alignment '.$trigger_data[0]->placement.' '.$trigger_button_custom_class.'" style="'.$trigger_button_custom_style_css.'" value="'.$trigger_button_option->BtnLink.'"></input>';
			else:
				$trigger_button_custom_style_css = wptggFrontEnd::get_trigger_button_custom_style($trigger_data[0]->option);
				$trigger_button_custom_style_css .= 'vertical-align: middle !important;';
				echo  '<input type="image" class="alignment '.$trigger_data[0]->placement.' '.$trigger_button_custom_class.'" value="" src="'.$trigger_button_option->data_customImageButtonURL.'" style="'.$trigger_button_custom_style_css.'" />';
			endif;
		else:
			echo '<input type="text" class="wptgg_pass_key wptrigger '.$only_number_css_class.' '.$shake_css_class.' '.$trigger_box_custom_class.'" placeholder="'.$trigger_data[0]->placeholder.'" value="' . $wpttr_keys->$objkey . '" />';
		endif;

		echo '<span class="wptgg_action">&nbsp;</span>
			<div class="wptrigger_append">' . $info["display_txt"] . '</div>';
	}

	echo '</div>';

} else {
	echo '<div class="wptrigger_contents">
			<div class="wptrigger_content">';



	/* CUSTOM STYLE */
	$trigger_box_custom_style_css = '';
	$trigger_box_custom_class = '';

	if($trigger_data[0]->apply_custom_styles == 'on') {
		$trigger_box_custom_style_css = wptggFrontEnd::get_trigger_box_custom_style($trigger_data[0]->box_custom_style);
	}

	$trigger_box_custom_class = 'wptrigger'.$trigger_data[0]->ID.'-triggerbox';
	$trigger_button_custom_class = 'wptrigger'.$trigger_data[0]->ID.'-button';
	/* CUSTOM STYLE */

	/* ALLOW ONLY NUMBER */
	$only_number_css_class = '';
	if($trigger_data[0]->only_number_chk == 'checked') {
		$only_number_css_class = 'wptgg_only_number';
	}
	/* ALLOW ONLY NUMBER */

	/* ANIMATE TRIGGER BOX */
	$shake_css_class = '';
	if($trigger_data[0]->shake_chk == 'checked') {
		$shake_css_class = 'wptgg-animated';
	}
	/* ANIMATE TRIGGER BOX */

	if($trigger_data[0]->btnactivate=='on'):
		$trigger_button_option = json_decode($trigger_data[0]->option);
		echo ' <input type="text" class="wptgg_pass_key1 wptrigger1 '.$only_number_css_class.' '.$shake_css_class.' '.$trigger_box_custom_class.'" placeholder="'.$trigger_data[0]->placeholder.'" '.(strlen($trigger_box_custom_style_css) > 0 ? ' style="'.$trigger_box_custom_style_css.'"' : '').' /> ';

		if($trigger_data[0]->btn_value=='1'):
			$trigger_button_custom_style_css = wptggFrontEnd::get_trigger_button_custom_style($trigger_data[0]->option);

			echo  '<input type="submit" class="alignment '.$trigger_data[0]->placement.' '.$trigger_button_custom_class.'" style="'.$trigger_button_custom_style_css.'" value="'.$trigger_button_option->BtnLink.'"></input>';
		else:
			$trigger_button_custom_style_css = wptggFrontEnd::get_trigger_button_custom_style($trigger_data[0]->option);
			$trigger_button_custom_style_css .= 'vertical-align: middle !important;';
			echo  '<input type="image" class="alignment '.$trigger_data[0]->placement.' '.$trigger_button_custom_class.'" value="" src="'.$trigger_button_option->data_customImageButtonURL.'" style="'.$trigger_button_custom_style_css.'" />';
		endif;
	else:
		echo ' <input type="text" class="wptgg_pass_key wptrigger '.$only_number_css_class.' '.$shake_css_class.' '.$trigger_box_custom_class.'" placeholder="'.$trigger_data[0]->placeholder.'" '.(strlen($trigger_box_custom_style_css) > 0 ? ' style="'.$trigger_box_custom_style_css.'"' : '').' /> ';
	endif;

	echo'<span class="wptgg_action">&nbsp;</span>
			<div class="wptrigger_append"></div>
		</div>
	</div>';
}
echo '<form class="wptgg_form" method="post" action="">
		<input type="hidden" class = "hi_wptgg_id" value="' . $param['id'] . '" />
		<input type="hidden" class = "hi_pass_key" value="' . (isset($wpttr_keys->$objkey) ? $wpttr_keys->$objkey : '') . '" />
		<input type="hidden" class = "hi_wptgg_num" value="' .  $wptrr_num . '" />
		<input type="hidden" name="" class="hi_wptrr_pass_keys" value="" />
		<input type="hidden" name="hi_wptgg_history_id" class="hi_wptgg_history_id" value="'.(isset($wptgg_history_id) ? $wptgg_history_id : '').'" />
		<input type="hidden" name="hi_wptgg_optinbox_sent" class="hi_wptgg_optinbox_sent" value="false" />
	</form>';
echo '</div>';
?>


