<?php

$trigger_id = (!empty($_GET["trigger_id"])) ? $_GET["trigger_id"] : "";
$pagetitle  = ($trigger_id) ? esc_html__("Edit Trigger Box", "wp-triggers") : esc_html__("Add New Trigger Box", "wp-triggers");

if( $trigger_id ) {
	echo wptggBackEnd::change_option_page_title($pagetitle, "All Triggers");

	$trigger = wptggBackEnd::get_trigger(array("ID" => $trigger_id));
	$infos   = json_decode($trigger->box_info);
}

$wp_trigger_status = (!empty($_GET["wp-trigger-status"])) ? $_GET["wp-trigger-status"] : "";

if($wp_trigger_status == 'just_created') {
	wptggBackEnd::wptgg_trigger_admin_message('updated', esc_html__('Your new Trigger Box has been created! To embed this on a page or post use the shortcode ', "wp-triggers").'[wptrigger id='.$trigger_id.']');
}

?>
<div class="wrap plugin-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php echo esc_html__($pagetitle, "wp-triggers") ;?></h2>
	<form id="trigger_frm" method="post">
		<div id="add-trigger-content">
			<div class="mymessage"></div>
			<div>
				<label style="font-size:16px;"><?php echo esc_html__("Trigger Box Name", "wp-triggers");?></label><br/>
				<input type="text" id="triggerbox_name" name="triggerbox_name" class="trigger-txt" value="<?php echo (isset($trigger->box_name) ? $trigger->box_name : "");?>">
			</div>
			<div style="margin-top:20px;">
				<a href="http://webdesignersacademy.com/wptriggers/guide#wildcard" class="button wildcard-guide-button" target="_blank"><i class="wildcard-guide"> </i><?php echo esc_html__("Learn how to use Wildcards", "wp-triggers");?></a>
				<div id="trigger_sets">
					<?php
					if( isset($infos) ) {
						$i = 1;

						foreach ($infos as $v) {
							echo wptggBackEnd::get_trigger_one_set($i, $v);

							$i++;
						}
					} else {
						echo wptggBackEnd::get_trigger_one_set();
					}
					?>
				</div>
				<div style="margin-top:20px;">
					<input type="button" id="add_set_button" class="button trigger_button" style="float:right;" value="+<?php echo esc_html__("Add New Trigger Set", "wp-triggers");?>" />
					<span style="float:right;margin-top:5px;">&nbsp;</span>
				</div>
			</div>
			<div class="clear"></div>
			<div>
				<fieldset class="trigger-not-found-fieldset">
					<h3><?php echo esc_html__("Trigger Not Found", "wp-triggers");?></h3>
					<div>
						<div class="mymessage"></div>
						<div class="txtarea_div">
							<?php $no_found_options = isset($trigger) ? json_decode($trigger->no_found) : ""; ?>
							<label><?php echo esc_html__("If the visitor types in ANYTHING else, then display this...", "wp-triggers"); ?></label><br><br>
							<span class="wptgg_span_2">
								<input type="radio" name="no_found_html_redirect" class="trigger_html_redirect" value="html" <?php echo (isset($no_found_options->no_found_html_redirect) ? ($no_found_options->no_found_html_redirect == 'html' || strlen($no_found_options->no_found_html_redirect) == 0 ? 'checked' : '') : 'checked');?> />&nbsp;<?php echo esc_html__("Display this", "wp-triggers");?> <label style="font-weight:bold;vertical-align:top;"><?php echo esc_html__("Content", "wp-triggers");?></label>
							</span>
							<br style="line-height:15px;"/>
							<span class="wptgg_span_3"><?php echo esc_html__("Insert HTML below", "wp-triggers");?></span><br>
							<?php
							wp_editor((isset($no_found_options->no_found_txt) ? $no_found_options->no_found_txt : ''), 'no_found_txt', array(
								'wpautop'       => true,
								'textarea_name' => 'no_found_txt',
								'editor_class'  => '',
								'textarea_rows' => 2,
								'teeny'         => false
							));
							?>
							<span class="shortcode_chk_cont"><input type="checkbox" id="no_found_shortcode" name="no_found_shortcode" class="shortcode_chk js-switch" value="checked" <?php echo (isset($no_found_options->no_found_shortcode) ? $no_found_options->no_found_shortcode : ""); ?> />&nbsp;<label><?php echo esc_html__("Contains Shortcode", "wp-triggers");?></label></span>
							<div class="wptgg-optinbox-container">
								<h3><?php echo esc_html__("Optin Box Options", "wp-triggers");?></h3>
								<input type="checkbox" id="no_found_optinbox" name="no_found_optinbox" class="optinbox_chk js-switch" value="checked" <?php echo (isset($no_found_options->no_found_optinbox) ? $no_found_options->no_found_optinbox : ''); ?> />&nbsp;<label><?php echo esc_html__("Activate Optin Box", "wp-triggers");?></label>
								<div class="wptgg-optinbox-options-container">
									<!--PLACEHOLDER-->
									<h4><?php echo esc_html__("Placeholder text:", "wp-triggers");?></h4>
									<input type="text" id="no_found_optinbox_placeholder" class="optinbox_placeholder" value="<?php echo isset($no_found_options->no_found_optinbox_placeholder) ? esc_attr($no_found_options->no_found_optinbox_placeholder) : ''; ?>" />
									<br />
									<span><?php echo esc_html__("This text will appear within the Optin Box and will disappear as soon as the visitor types something in.", "wp-triggers");?></span>
									<!--PLACEHOLDER-->

									<br />

									<!--BUTTON LABEL-->
									<h4><?php echo esc_html__("Button label:", "wp-triggers");?></h4>
									<input type="text" id="no_found_optinbox_button_label" class="optinbox_button_label" value="<?php echo isset($no_found_options->no_found_optinbox_button_label) ? esc_attr($no_found_options->no_found_optinbox_button_label) : ''; ?>" />
									<!--BUTTON LABEL-->

									<br /><br />

									<!--CONTENT DISPLAYING-->
									<label><?php echo esc_html__("Once the Optin Box submitted, then do the following:", "wp-triggers");?></label>
									<br><br>
									<span class="wptgg_span_2">
										<input type="radio" name="no_found_optinbox_html_redirect" class="trigger_optinbox_html_redirect" value="html" <?php echo (isset($no_found_options->no_found_optinbox_html_redirect) ? ($no_found_options->no_found_optinbox_html_redirect == 'html' || strlen($no_found_options->no_found_optinbox_html_redirect) == 0 ? 'checked' : '') : 'checked');?> />&nbsp;<?php echo esc_html__("Display this", "wp-triggers");?> <label style="font-weight:bold;vertical-align:top;"><?php echo esc_html__("Content", "wp-triggers");?></label>
									</span>
									<br style="line-height:15px;"/>
									<br>
									<?php
									wp_editor((isset($no_found_options->no_found_optinbox_txt) ? $no_found_options->no_found_optinbox_txt : ''), 'no_found_optinbox_txt', array(
										'wpautop'       => true,
										'textarea_name' => 'no_found_optinbox_txt',
										'editor_class'  => '',
										'textarea_rows' => 2,
										'teeny'         => false
									));
									?>
									<br>
									<span class="shortcode_chk_cont">
										<input type="checkbox" id="no_found_optinbox_shortcode" name="no_found_optinbox_shortcode" class="shortcode_chk js-switch" value="checked" <?php echo (isset($no_found_options->no_found_optinbox_shortcode) ? $no_found_options->no_found_optinbox_shortcode : ""); ?> />&nbsp;<label><?php echo esc_html__("Contains Shortcode", "wp-triggers");?></label>
									</span>

									<br /><br />

									<input type="radio" name="no_found_optinbox_html_redirect" value="redirect" <?php echo (isset($no_found_options->no_found_optinbox_html_redirect) ? ($no_found_options->no_found_optinbox_html_redirect == 'redirect' ? 'checked' : '') : '');?> /><span style="margin-left:5px;"><?php echo esc_html__("Redirect visitors to this URL", "wp-triggers");?></span>
									<input type="text" id="no_found_optinbox_redirect_url" class="trigger_optinbox_redirect_url" value="<?php echo isset($no_found_options->no_found_optinbox_redirect_url) ? esc_attr($no_found_options->no_found_optinbox_redirect_url) : ''; ?>" />
									<!--CONTENT DISPLAYING-->
								</div>
							</div>

							<br /><br />

							<input type="radio" name="no_found_html_redirect" value="redirect" <?php echo (isset($no_found_options->no_found_html_redirect) ? ($no_found_options->no_found_html_redirect == 'redirect' ? 'checked' : '') : '');?> /><span style="margin-left:5px;">Redirect visitors to this URL</span>
							<input type="text" id="no_found_redirect_url" class="trigger_redirect_url" value="<?php echo isset($no_found_options->no_found_redirect_url) ? esc_attr($no_found_options->no_found_redirect_url) : ''; ?>" />
						</div>																				
					</div>													
				</fieldset>						
			</div>

                        <!------Start submit button------ -->


                        <!------updated Code for submit button------ -->

			<?php
			$chk             = checked((isset($trigger->show_chk) ? $trigger->show_chk : ""), "checked", false);
			$only_number_chk = checked((isset($trigger->only_number_chk) ? $trigger->only_number_chk : ""), "checked", false);
			$scroll_to_chk   = checked((isset($trigger->scroll_to_chk) ? $trigger->scroll_to_chk : ""), "checked", false);
			$shake_chk       = checked((isset($trigger->shake_chk) ? $trigger->shake_chk : ""), "checked", false);
			?>

			<div style="margin-top:15px;">
				<fieldset class="trigger_general_option">
					<h3><?php echo esc_html__("General Option", "wp-triggers"); ?></h3>
					<div style="margin-top:10px;">
						<div class="mymessage"></div>
						<div class="Fields">
							<div class="left"><?php echo esc_html__("Placeholder text:", "wp-triggers");?></div>
							<div class="right">
								<input type="text" name="placeholder" class="placeholder" value="<?php echo isset($trigger->placeholder) ? esc_attr($trigger->placeholder) : ''; ?>"/> <br /><span><?php echo esc_html__("This text will appear within the trigger box and will disappear as soon as the visitor types something in.", "wp-triggers");?></span>
							</div>
							<div class="clr"></div>
						</div>
						<div class="Fields">
							<div class="left"><?php echo esc_html__("Misc. Options:", "wp-triggers");?></div>
							<div class="right">
								<input type="checkbox" name="only_number_trigger_chk" id="only_number_trigger_chk" class="js-switch" value="checked" <?php echo $only_number_chk;?> /><label style="margin-left:5px;"><?php echo esc_html__("Only allow numbers to be entered into this Trigger Box", "wp-triggers");?></label>
								<br />
								<input type="checkbox" name="hide_trigger_chk" id="hide_trigger_chk" class="js-switch" value="checked" <?php echo $chk;?> /><label style="margin-left:5px;"><?php echo esc_html__("Hide this Trigger Box after a visitor searches for a Trigger", "wp-triggers");?></label>
								<br />
								<input type="checkbox" name="scroll_to_trigger_chk" id="scroll_to_trigger_chk" class="js-switch" value="checked" <?php echo $scroll_to_chk;?> /><label style="margin-left:5px;"><?php echo esc_html__("Once submitted, scroll the visitor down to this Trigger Box (anchor = #wptriggers", "wp-triggers");?><?php echo $trigger_id; ?>)<br /><?php echo esc_html__("(Useful if you are using WP shortcodes and you are placing this Trigger Box far down the page)", "wp-triggers");?></label>
								<br />
								<input type="checkbox" name="shake_trigger_chk" id="shake_trigger_chk" class="js-switch" value="checked" <?php echo $shake_chk;?> /><label style="margin-left:5px;"><?php echo esc_html__("Shake this trigger box a little to draw attention to it (works in MOST browsers / devices)", "wp-triggers");?></label>
							</div>
							<div class="clr"></div>
						</div>
					</div>
				</fieldset>
			</div>	
			<?php
				$box_custom_style = isset($trigger->box_custom_style) ? json_decode($trigger->box_custom_style) : '';
			?>
			<div style="margin-top:15px;">
				<fieldset class="trigger_styling_controls">
					<h3><?php echo esc_html__("Trigger Box Styling", "wp-triggers"); ?></h3>
					<div style="margin-top:10px;">
						<div class="mymessage"></div>
						<div>
							<p><?php echo esc_html__("By default, WP Triggers will use the default textbox style from your WordPress theme.", "wp-triggers");?></p>
							<p><?php echo esc_html__("You can also use the class", "wp-triggers");?> <strong>.wptrigger<?php echo isset($trigger->ID) ? $trigger->ID : '[ID]'; ?>-triggerbox</strong> <?php echo esc_html__("in your theme CSS file to style this Trigger Box.", "wp-triggers");?></p>
						</div>
						<div class="Fields">
							<div>
								<input type="checkbox" id="apply_custom_styles" name="apply_custom_styles" class="js-switch" <?php echo isset($trigger->apply_custom_styles) ? ($trigger->apply_custom_styles == "on" ? "checked" : "") : ""; ?>/> <?php echo esc_html__("Override my theme CSS and use these custom styles", "wp-triggers");?>
							</div>
							<div class="clr"></div>
						</div>
						<div class="trigger-box-styling-options-container">
							<div class="Fields">
								<div class="left"><?php echo esc_html__("Text Alignment:", "wp-triggers");?></div>
								<div class="right">
									<select id="customTextAlignment" name="customTextAlignment">
										<option value="center" <?php echo isset($box_custom_style->data_customTextAlignment) ? ($box_custom_style->data_customTextAlignment == 'center' ? 'selected' : '') : ''; ?>><?php echo esc_html__("Center", "wp-triggers");?></option>
										<option value="left" <?php echo isset($box_custom_style->data_customTextAlignment) ? ($box_custom_style->data_customTextAlignment == 'left' ? 'selected' : '') : ''; ?>><?php echo esc_html__("Left", "wp-triggers");?></option>
										<option value="right" <?php echo isset($box_custom_style->data_customTextAlignment) ? ($box_custom_style->data_customTextAlignment == 'right' ? 'selected' : '') : ''; ?>><?php echo esc_html__("Right", "wp-triggers");?></option>
									</select>
								</div>
								<div class="clr"></div>
							</div>
							<div class="Fields">
								<div class="left"><?php echo esc_html__("Text Color:", "wp-triggers");?></div>
								<div class="right">
									<?php $box_custom_style_TextColor = (!isset($box_custom_style->data_customTextColor) ? "#a1a1a1" : $box_custom_style->data_customTextColor); ?>
									<div class="color-field">
										<input type="color" id="customTextColor" name="customTextColor" value="<?php echo esc_attr($box_custom_style_TextColor); ?>" />
										<input type="text" id="customTextColor_textfield" value="<?php echo esc_attr($box_custom_style_TextColor); ?>" />
									</div>
								</div>
								<div class="clr"></div>
							</div>
							<div class="Fields">
								<div class="left"><?php echo esc_html__("Background Color:", "wp-triggers");?></div>
								<div class="right">
									<?php $box_custom_style_BackgroundColor = (!isset($box_custom_style->data_customBackgroundColor) ? "#ffffff" : $box_custom_style->data_customBackgroundColor); ?>
									<div class="color-field">
										<input type="color" id="customBackgroundColor" name="customBackgroundColor" value="<?php echo esc_attr($box_custom_style_BackgroundColor); ?>" />
										<input type="text" id="customBackgroundColor_textfield" value="<?php echo esc_attr($box_custom_style_BackgroundColor); ?>" />
									</div>
								</div>
								<div class="clr"></div>
							</div>
							<div class="Fields">
								<div class="left"><?php echo esc_html__("Border Color:", "wp-triggers");?></div>
								<div class="right">
									<?php $box_custom_style_BorderColor = (!isset($box_custom_style->data_customBorderColor) ? "#ebebeb" : $box_custom_style->data_customBorderColor); ?>
									<div class="color-field">
										<input type="color" id="customBorderColor" name="customBorderColor" value="<?php echo esc_attr($box_custom_style_BorderColor); ?>" />
										<input type="text" id="customBorderColor_textfield" value="<?php echo esc_attr($box_custom_style_BorderColor); ?>" />
									</div>
								</div>
								<div class="clr"></div>
							</div>
							<div class="Fields">
								<div class="left"><?php echo esc_html__("Text Size:", "wp-triggers");?></div>
								<div class="right">
									<?php $box_custom_style_TextSize = (!isset($box_custom_style->data_customTextSize) ? "1.1" : $box_custom_style->data_customTextSize); ?>
									<input type="text" id="customTextSize" name="customTextSize" value="<?php echo esc_attr($box_custom_style_TextSize); ?>" />&nbsp;em
								</div>
								<div class="clr"></div>
							</div>
							<div class="Fields">
								<div class="left"><?php echo esc_html__("Corner Radius:", "wp-triggers");?></div>
								<div class="right">
									<?php $box_custom_style_CornerRadius = (!isset($box_custom_style->data_customCornerRadius) ? "5" : $box_custom_style->data_customCornerRadius); ?>
									<input type="number" id="customCornerRadius" name="customCornerRadius" value="<?php echo esc_attr($box_custom_style_CornerRadius); ?>" />
								</div>
								<div class="clr"></div>
							</div>
							<div class="Fields">
								<div class="left"><?php echo esc_html__("Vertical Padding:", "wp-triggers");?></div>
								<div class="right">
									<?php $box_custom_style_VerticalPadding = (!isset($box_custom_style->data_customVerticalPadding) ? "5" : $box_custom_style->data_customVerticalPadding); ?>
									<input type="number" id="customVerticalPadding" name="customVerticalPadding" value="<?php echo esc_attr($box_custom_style_VerticalPadding); ?>" />
								</div>
								<div class="clr"></div>
							</div>
							<div class="Fields">
								<div class="left"><?php echo esc_html__("Horizontal Padding:", "wp-triggers");?></div>
								<div class="right">
									<?php $box_custom_style_HorizontalPadding = (!isset($box_custom_style->data_customHorizontalPadding) ? "10" : $box_custom_style->data_customHorizontalPadding); ?>
									<input type="number" id="customHorizontalPadding" name="customHorizontalPadding" value="<?php echo esc_attr($box_custom_style_HorizontalPadding); ?>" />
								</div>
								<div class="clr"></div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
			<?php
				$button_custom_style = isset($trigger->option) ? json_decode($trigger->option) : "";
			?>
			<div style="margin-top:15px;">
				<fieldset class="trigger_button_options">
					<h3><?php echo esc_html__("Button Options", "wp-triggers"); ?></h3>
					<div style="margin-top:10px;">
						<div class="mymessage"></div>
						<div class="Fields">
							<div class="left">
								<?php
								$checked = "";
								if(isset($trigger->btnactivate)) {
									if ($trigger->btnactivate=='on') {
										$checked = "checked";
									}
								}?>
								<input type="checkbox" id="activate" name="activate" <?php echo $checked;?> class="activate js-switch"/> <?php echo esc_html__("Activate Button?", "wp-triggers");?>
							</div>
							<div class="right">
							</div>
							<div class="clr"></div>
						</div>

						<div id="trigger_button_options_container">
							<div class="Fields">
								<div class="left"><?php echo esc_html__("Button Layout", "wp-triggers");?></div>
								<div class="right">
									<ul class="button-layout-list">
										<li>
											<input type="radio" name="boxPlacement" value="below_100_100" <?php checked((isset($trigger->placement) ? $trigger->placement : 'below_100_100'), 'below_100_100'); ?>></input>
											<br />
											<span><?php echo esc_html__("Below");?></span>
											<br />
											<span>100% / 100%</span>
											<br />
											<img src="<?php echo WPTGG_PLUGIN_URL; ?>img/below_100_100.png"/>
										</li>
										<li>
											<input type="radio" name="boxPlacement" value="below_100_60" <?php checked((isset($trigger->placement) ? $trigger->placement : ''), 'below_100_60'); ?> />
											<br />
											<span><?php echo esc_html__("Below", "wp-triggers");?></span>
											<br />
											<span>100% / 60%</span>
											<br />
											<img src="<?php echo WPTGG_PLUGIN_URL; ?>img/below_100_60.png"/>
										</li>
										<li>
											<input type="radio" name="boxPlacement" value="right_75_25" <?php checked((isset($trigger->placement) ? $trigger->placement : ''), 'right_75_25'); ?> />
											<br />
											<span><?php echo esc_html__("Right", "wp-triggers");?></span>
											<br />
											<span>75% / 25%</span>
											<br />
											<img src="<?php echo WPTGG_PLUGIN_URL; ?>img/right_75_25.png"/>
										</li>
										<li>
											<input type="radio" name="boxPlacement" value="right_50_50" <?php checked((isset($trigger->placement) ? $trigger->placement : ''), 'right_50_50'); ?> />
											<br />
											<span><?php echo esc_html__("Right", "wp-triggers");?></span>
											<br />
											<span>50% / 50%</span>
											<br />
											<img src="<?php echo WPTGG_PLUGIN_URL; ?>img/right_50_50.png"/>
										</li>
									</ul>
								</div>
								<div class="clr"></div>
							</div>

							<div class="Fields">
								<div class="left"><?php echo esc_html__("Button Options:", "wp-triggers");?></div>
								<div class="right">
									<div class='ft'>
										<input type="radio" name="btnPlace" class="btnPlace" value="1" <?php checked((isset($trigger->btn_value) ? $trigger->btn_value : '1'), '1'); ?>/> <?php echo esc_html__("Regular Button", "wp-triggers");?><br/>
										<label style="margin-left: 25px;margin-right: 146px;"><?php echo esc_html__("Button Label", "wp-triggers");?></label><input type="text" id="BtnLink" name="BtnLink" class="BtnLink" value="<?php echo !isset($button_custom_style->BtnLink) ? 'SUBMIT' : $button_custom_style->BtnLink;?>"/><br /><br />
										<div class='ft button-custom-style'>
											<input type="radio" name="btnCustomStyle" value="yes" <?php checked((isset($trigger->apply_button_custom_style) ? $trigger->apply_button_custom_style : 'yes'), 'yes'); ?>/> <?php echo esc_html__("Use the custom button styles below", "wp-triggers");?><br/>
											<div id="trigger_button_custom_options_container">
												<div class="Fields">
													<div class="clr"></div>
													<div class="left">
														<label><?php echo esc_html__("Background Color:", "wp-triggers");?></label>
													</div>
													<div class="right">
														<?php $button_custom_style_BackgroundColor = (!isset($button_custom_style->data_customButtonBackgroundColor) ? "#00b056" : $button_custom_style->data_customButtonBackgroundColor); ?>
														<div class="color-field">
															<input type="color" id="customButtonBackgroundColor" name="customButtonBackgroundColor" value="<?php echo esc_attr($button_custom_style_BackgroundColor); ?>" />
															<input type="text" id="customButtonBackgroundColor_textfield" name="customButtonBackgroundColor_textfield" value="<?php echo esc_attr($button_custom_style_BackgroundColor); ?>" />
														</div>
													</div>
												</div>
												<div class="Fields">
													<div class="left">
														<label><?php echo esc_html__("Corner Radius:", "wp-triggers");?></label>
													</div>
													<div class="right">
														<?php $button_custom_style_CornerRadius = (!isset($button_custom_style->data_customButtonCornerRadius) ? "5" : $button_custom_style->data_customButtonCornerRadius); ?>
														<input type="number" id="customButtonCornerRadius" name="customButtonCornerRadius" value="<?php echo esc_attr($button_custom_style_CornerRadius); ?>" />
													</div>
													<div class="clr"></div>
												</div>
												<div class="Fields">
													<div class="left">
														<label><?php echo esc_html__("Vertical Padding:", "wp-triggers");?></label>
													</div>
													<div class="right">
														<?php $button_custom_style_VerticalPadding = (!isset($button_custom_style->data_customButtonVerticalPadding) ? "10" : $button_custom_style->data_customButtonVerticalPadding); ?>
														<input type="number" id="customButtonVerticalPadding" name="customButtonVerticalPadding" value="<?php echo esc_attr($button_custom_style_VerticalPadding); ?>" />
													</div>
													<div class="clr"></div>
												</div>
												<div class="Fields">
													<div class="left">
														<label><?php echo esc_html__("Horizontal Padding:", "wp-triggers");?></label>
													</div>
													<div class="right">
														<?php $button_custom_style_HorizontalPadding = (!isset($button_custom_style->data_customButtonHorizontalPadding) ? "20" : $button_custom_style->data_customButtonHorizontalPadding); ?>
														<input type="number" id="customButtonHorizontalPadding" name="customButtonHorizontalPadding" value="<?php echo esc_attr($button_custom_style_HorizontalPadding); ?>" />
													</div>
													<div class="clr"></div>
												</div>
												<div class="Fields">
													<div class="left">
														<label><?php echo esc_html__("Button Text Color:", "wp-triggers");?></label>
													</div>
													<div class="right">
														<?php $button_custom_style_TextColor = (!isset($button_custom_style->data_customButtonTextColor) ? "#ffffff" : $button_custom_style->data_customButtonTextColor); ?>
														<div class="color-field">
															<input type="color" id="customButtonTextColor" name="customButtonTextColor" value="<?php echo esc_attr($button_custom_style_TextColor); ?>" />
															<input type="text" id="customButtonTextColor_textfield" name="customButtonTextColor_textfield" value="<?php echo esc_attr($button_custom_style_TextColor); ?>" />
														</div>
													</div>
													<div class="clr"></div>
												</div>
												<div class="Fields">
													<div class="left">
														<label><?php echo esc_html__("Button Text Size:", "wp-triggers");?></label>
													</div>
													<div class="right">
														<?php $button_custom_style_TextSize = (!isset($button_custom_style->data_customButtonTextSize) ? "1.5" : $button_custom_style->data_customButtonTextSize); ?>
														<input type="text" id="customButtonTextSize" name="customButtonTextSize" value="<?php echo esc_attr($button_custom_style_TextSize); ?>" />&nbsp;em
													</div>
													<div class="clr"></div>
												</div>
											</div>
											<div style="display: inline-block;/*width: 270px;*/">
												<input type="radio" name="btnCustomStyle" value="no" <?php checked((isset($trigger->apply_button_custom_style) ? $trigger->apply_button_custom_style : ''), 'no'); ?>/> <?php echo esc_html__("Use the default button styles from my WordPress theme", "wp-triggers");?><br />
												<?php echo esc_html__("You can also use the class", "wp-triggers");?> <strong>.wptrigger<?php echo isset($trigger->ID) ? $trigger->ID : '[ID]';?>-button</strong> <?php echo esc_html__("in your theme CSS file to style this button.", "wp-triggers");?>
											</div>
										</div>
									</div>
									<input type="radio" name="btnPlace" class="btnPlace" value="2" <?php checked((isset($trigger->btn_value) ? $trigger->btn_value : ''), '2'); ?>/> Image Button<br/>
									<div class='se image-button-style'>
										<div class="Fields">
											<div class="left">
												<label><?php echo esc_html__("Image URL:", "wp-triggers");?></label>
											</div>
											<div class="right">
												<input type="text" id="customImageButtonURL" name="customImageButtonURL" value="<?php echo (isset($button_custom_style->data_customImageButtonURL) ? esc_attr($button_custom_style->data_customImageButtonURL) : ""); ?>" style="width: 200%;" />
											</div>
											<div class="clr"></div>
										</div>
										<div class="Fields">
											<div class="left">
												<label><?php echo esc_html__("Vertical Margin:", "wp-triggers");?></label>
											</div>
											<div class="right">
												<?php $button_image_custom_style_VerticalMargin = (!isset($button_custom_style->data_customImageButtonVerticalMargin) ? "0" : $button_custom_style->data_customImageButtonVerticalMargin); ?>
												<input type="number" id="customImageButtonVerticalMargin" name="customImageButtonVerticalMargin" value="<?php echo esc_attr($button_image_custom_style_VerticalMargin); ?>" />
											</div>
											<div class="clr"></div>
										</div>
										<div class="Fields">
											<div class="left">
												<label><?php echo esc_html__("Horizontal Margin:", "wp-triggers");?></label>
											</div>
											<div class="right">
												<?php $button_image_custom_style_HorizontalMargin = (!isset($button_custom_style->data_customImageButtonHorizontalMargin) ? "20" : $button_custom_style->data_customImageButtonHorizontalMargin); ?>
												<input type="number" id="customImageButtonHorizontalMargin" name="customImageButtonHorizontalMargin" value="<?php echo esc_attr($button_image_custom_style_HorizontalMargin); ?>" />
											</div>
											<div class="clr"></div>
										</div>
									</div>
								</div>
								<div class="clr"></div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
			<div style="margin-top:30px;">
				<input type="submit" id="trigger_box_save" class="button-primary trigger_button" value="<?php echo esc_html__("Save Changes", "wp-triggers");?>" />
			</div>
		</div>	

		<input type="hidden" id="hi_trigger_info" name="hi_trigger_info" />
		<input type="hidden" id="hi_button_info" name="hi_button_info" />
		<input type="hidden" id="hi_button_placement" name="hi_button_placement" />
		<input type="hidden" id="hi_button_option" name="hi_button_option" />
		<input type="hidden" id="hi_box_custom_style" name="hi_box_custom_style" />
		<input type="hidden" id="hi_no_found_data" name="hi_no_found_data" />
		<input type="hidden" id="hi_trigger_id" name="hi_trigger_id" value="<?php echo $trigger_id;?>" />
	</form>
</div>