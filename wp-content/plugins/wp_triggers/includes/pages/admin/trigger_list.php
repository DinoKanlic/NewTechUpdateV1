<?php

$triggers = wptggBackEnd::get_trigger();

//----------------

$count_posts = count($triggers);				
$pagenum     = (!empty($_GET["paged"])) ? $_GET["paged"] : 1;
$per_page    = (!empty($per_page)) ? $per_page : 15;
?>

<div class="wrap plugin-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php echo esc_html__("WP Triggers", "wp-triggers") ;?><a href="admin.php?page=add-trigger" class="add-new-h2"><?php echo esc_html__("Add New", "wp-triggers"); ?></a></h2>
	<div>
		<div class="tablenav">
			<div class="tablenav-pages">
				<?php wptggBackEnd::get_page_link($count_posts,$pagenum, $per_page);?>
			</div>
		</div>

		<table class="wp-list-table widefat fixed posts trigger-list-table" cellspacing="0" border="0">
			<thead>
				<?php wptggBackEnd::get_table_header();?>
			</thead>

			<tfoot>
				<?php wptggBackEnd::get_table_header();?>
			</tfoot>

			<tbody>
				<?php 
				if( $triggers ) {
					$count = 0;
					$nnum  = 0;

					$start = ($pagenum - 1) * $per_page;
					$end   = $start + $per_page;

					foreach ($triggers as $trigger) {
						if ( $count >= $end ) {
							break;
						}

						if ( $count >= $start ) {
							$nnum++;

							echo "<tr class='alternate author-self status-publish format-default iedit'>";
							echo "<td><strong>{$nnum}</strong></td>";
							echo "<td>
									<a href='admin.php?page=wp-trigger&trigger_id={$trigger->ID}'><strong>{$trigger->box_name}</strong></a>

									<div class='row-actions'>						
										<span><a href='admin.php?page=wp-trigger&trigger_id={$trigger->ID}' class='menu_edit_link'>".esc_html__("Edit", "wp-triggers")."</a></span>&nbsp;|&nbsp; 
										<span><a href='admin.php?page=trigger-history&triggerbox={$trigger->ID}' class='menu_edit_link'>".esc_html__("Stats", "wp-triggers")."</a></span>&nbsp;|&nbsp;
										<span><a href='admin.php?page=wp-trigger&delete={$trigger->ID}' class='menu_delete_link'>".esc_html__("Delete", "wp-triggers")."</a></span>&nbsp;|&nbsp;
										<span><a href='admin.php?page=wp-trigger&export={$trigger->ID}' class='menu_export_link'>".esc_html__("Export Settings", "wp-triggers")."</a></span>&nbsp;|&nbsp;
										<span><a href='admin.php?page=wp-trigger&duplicate={$trigger->ID}' class='menu_duplicate_link'>".esc_html__("Duplicate", "wp-triggers")."</a></span>
									</div>
								</td>";

							echo "<td><strong><a href='admin.php?page=trigger-history&triggerbox={$trigger->ID}'>{$trigger->entries}</a></strong></td>";
							echo "<td>[wptrigger id={$trigger->ID}]</td>";
							echo "<td>#wptriggers{$trigger->ID}</td>";
							echo "<td>{$trigger->create_datetime}</td>";
							echo "</tr>";
						}

						$count++;
					}
				} else {
					$msg = "<lavel style='height:30px;'>".esc_html__("You don't have any Trigger Boxes yet! Let's go", "wp-triggers")." </label> <a href='admin.php?page=add-trigger'>".esc_html__("add one", "wp-triggers")."</a> !";

					echo "<tr>";
					echo "<td colspan='5' style='padding:20px;'>$msg</td>";
					echo "</tr>";
				}
				?>
				<tr>
				</tr>
			</tbody>
		</table>

		<div class="tablenav">
			<div class="tablenav-pages">
				<?php wptggBackEnd::get_page_link($count_posts,$pagenum, $per_page);?>
			</div>
		</div>
	</div>
</div>
