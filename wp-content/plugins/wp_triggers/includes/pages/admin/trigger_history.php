<?php

if(!empty($_POST["tgg-history"])) {
	$tgg_history_selected_rows = $_POST["tgg-history"];

	/* ========== DELETE ACTION ========== */
	if(!empty($_POST["trigger_action"])) {
		if($_POST["trigger_action"] === "delete") {
			wptggBackEnd::delete_trigger_history($tgg_history_selected_rows);
		}
	}
	/* ========== DELETE ACTION ========== */
}

$orderby = array(
		"trigger" => "trigger",
		"valid"   => "valid_status",
		"optin"   => "optin",
		"boxname" => "box_name",
		"date"    => "create_datetime"
		);

$data = array();

if( !empty($_GET["triggerbox"]) ) {
	$trigger = wptggBackEnd::get_trigger(array("ID" => $_GET["triggerbox"]));

	$data["trigger_box_id"] = $_GET["triggerbox"];
}

if( !empty($_GET["orderby"]) ) {
	$data["orderby"] = $orderby[$_GET["orderby"]];
	$data["order"]   = !empty($_GET["order"]) ? $_GET["order"] : '';
}

$date_range_start = date('Y-m-d', strtotime('-7 day', time()));

if(!empty($_GET["date_range_start"])) {
	$data["date_range_start"] = $_GET["date_range_start"];

	$date_range_start = $_GET["date_range_start"];
}

$date_range_end = date('Y-m-d', time());

if(!empty($_GET["date_range_end"])) {
	$data["date_range_end"] = $_GET["date_range_end"];

	$date_range_end = $_GET["date_range_end"];
}

$pagelbl = (isset($trigger->box_name)) ? $trigger->box_name : "All";

$histories = wptggBackEnd::get_trigger_history($data);


//----------------

$min_date    = wptggBackEnd::get_hitory_min_date($data);
$count_posts = count($histories);

$pagenum = (!empty($_GET["paged"])) ? $_GET["paged"] : 1;

$per_page = (isset($per_page)) ? $per_page : 100;

if(!empty($_GET["page_entries"])) {
	$per_page = $_GET["page_entries"];
}

$trigger_page_entries_options = array(25 => "25", 50 => "50", 100 => "100", 250 => "250", 500 => "500", 1000 => "1000");

?>

<!-- MODAL DIALOG -->
<div id="trigger_modal_dialog" style="display: none;">
	<span class="trigger-modal-dialog-body"></span>
</div>
<!-- MODAL DIALOG -->

<div class="wrap plugin-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php echo esc_html__($pagelbl . " Trigger Box Stats", "wp-triggers") ;?></h2>
	<div style="margin-top:15px;">
		<form id="trigger_actions" method="post" action="<?php echo admin_url().'admin.php?page='.$_GET['page'];?>">
			<input type="hidden" id="trigger_action" name="trigger_action" />
			<div class="tablenav" style="margin:0px;">
				<div style="font-size:17px;"><span class="trigger-history-info-bar"><?php echo $count_posts?> <?php echo esc_html__("Triggers have been submitted since", "wp-triggers");?> <?php echo $min_date;?></span></div>
			</div>

			<!-- FILTER BAR -->
			<div class="filter-bar-container">
				<div class="tablenav top">
					<div class="_alignleft actions action-container">
						<div class="actions-item">
							<span class="date-range-title"><?php echo esc_html__("Date Range:", "wp-triggers"); ?></span>
							<input type="date" id="trigger_date_range_start" name="trigger_date_range_start" value="<?php echo $date_range_start; ?>"> - <input type="date" id="trigger_date_range_end" name="trigger_date_range_end" value="<?php echo $date_range_end; ?>">
						</div>

						<div class="actions-item">
							<?php $tggs = wptggBackEnd::get_trigger(); ?>
							<select id="ttigger_box_names" style="float: none;">
								<option selected="selected" value=""><?php echo esc_html__("Show All Trigger Boxes", "wp-triggers");?></option>
								<?php
								if($tggs) {
									foreach($tggs as $tgg) {
										if( $_GET["triggerbox"] == $tgg->ID ) {
											echo "<option value='{$tgg->ID}' selected>{$tgg->box_name}</option>";
										} else {
											echo "<option value='{$tgg->ID}'>{$tgg->box_name}</option>";
										}
									}
								}
								?>
							</select>
						</div>

						<div class="actions-item">
							<span class="pagination-title"><?php echo esc_html__("Entries Per Page:", "wp-triggers"); ?></span>
							<select id="trigger_page_entries" style="float: none;">
								<?php
								foreach($trigger_page_entries_options as $trigger_page_entries_option_id => $trigger_page_entries_option_value) {
									echo '<option value="'.$trigger_page_entries_option_id.'" '.($per_page == $trigger_page_entries_option_id ? "selected" : "").'>'.$trigger_page_entries_option_value.'</option>';
								}
								?>
							</select>
						</div>

						<div class="actions-item">
							<a href="admin.php?page=trigger-history" id="trigger_filter_link"><input type="button" name="" id="post-query-submit" class="button-secondary filter-trigger-history-button" value="Filter"></a>
						</div>
					</div>
				</div>
			</div>
			<!-- FILTER BAR -->


			<!-- ACTIONS BAR -->
			<div class="action-bar-container">
				<div class="tablenav">
					<div style="float: left;">
						<?php echo esc_html__("Selected rows:", "wp-triggers");?>
						<a id="trigger_export_csv" href="#" style="margin-left: 10px;"><input type="button" class="button-primary trigger_button export-trigger-history-button" value="<?php echo esc_html__("Export as .csv", "wp-triggers");?>"></a>
						<button id="trigger_delete" class="button-primary trigger_button delete-trigger-history-button"><?php echo esc_html__("Delete", "wp-triggers");?></button>
					</div>
					<div class="tablenav-pages">
						<?php wptggBackEnd::get_page_link($count_posts,$pagenum, $per_page);?>
					</div>
				</div>
			</div>
			<!-- ACTIONS BAR -->


			<table class="wp-list-table widefat fixed posts trigger-history-table" cellspacing="0" border=0>
				<thead>
					<?php wptggBackEnd::get_hostory_table_header();?>
				</thead>
				<tfoot>
					<?php wptggBackEnd::get_hostory_table_header();?>
				</tfoot>
				<tbody>
				<?php

				if( $histories ) {
					$count = 0;
					$start = ($pagenum - 1) * $per_page;
					$end = $start + $per_page;

					foreach ($histories as $row) {
						if ( $count >= $end ) {
							break;
						}

						if ( $count >= $start ) {
							echo "<tr class='alternate author-self status-publish format-default iedit'>";
							echo "<th scope='row' ></th>";
							echo "<td class='manage-column column-cb check-column'><input id='tgg-select-{$row->ID}' type='checkbox' name='tgg-history[]' value='{$row->ID}'></td>";
							echo "<td>{$row->trigger}</td>";
							echo "<td>{$row->valid_status}</td>";
							echo "<td>{$row->optin}</td>";
							echo "<td>{$row->box_name}</td>";
							echo "<td>{$row->create_datetime}</td>";
							echo "</tr>";
						}

						$count++;
					}
				} else {
					$msg = "<lavel style='height:30px;'>No trigger history</label>";

					echo "<tr>";
					echo "<td colspan='5' style='padding:20px;'>$msg</td>";
					echo "</tr>";
				}
				?>
				</tbody>
			</table>

			<div class="tablenav">
				<div class="tablenav-pages">
					<?php wptggBackEnd::get_page_link($count_posts,$pagenum, $per_page);?>
				</div>
			</div>
		</form>
	</div>
</div>