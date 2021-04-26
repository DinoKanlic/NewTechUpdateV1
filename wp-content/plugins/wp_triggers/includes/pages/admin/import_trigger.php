<?php $pagetitle = esc_html__("Import", "wp-triggers"); ?>

<div class="wrap plugin-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post">
		<br>
	</div>
	<h2><?php echo esc_html__($pagetitle, "wp-triggers"); ?></h2>
	<div>
		<p><?php echo esc_html__("Select the WP Triggers export file you would like to import. When you click the import button below, WP Triggers will create a new Trigger Box with the same settings.", "wp-triggers");?></p>
		<p><?php echo esc_html__("NOTE: Stats / Entries from the original Trigger Box are NOT included in this file.", "wp-triggers");?></p>
	</div>

	<form id="trigger_import_frm" method="post" enctype="multipart/form-data">
		<div class="mymessage"></div>
		<div class="Fields" style="margin-top: 50px;">
			<div class="left">
				<label style="font-size: 13px;"><?php echo esc_html__("Select file:", "wp-triggers");?></label>
			</div>
			<div class="right">
				<input type="file" id="trigger_import_file" name="trigger_import_file">
			</div>
		</div>
		<div class="clr"></div>
		<input type="submit" id="trigger_import" name="trigger_import" class="button-primary trigger_button" value="Import" style="margin-top: 20px;" />
	</form>

</div>