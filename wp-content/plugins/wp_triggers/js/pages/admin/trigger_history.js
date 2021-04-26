jQuery(document).ready(function() {

	"use strict";

	jQuery("#trigger_filter_link").on("click", function() {
		var triggier_box_id = jQuery("#ttigger_box_names").val();

		if (triggier_box_id) {
			var llink = jQuery(this).attr("href");

			llink += "&triggerbox=" + triggier_box_id;

			jQuery(this).attr("href", llink);
		}


		var trigger_date_range_start = jQuery("#trigger_date_range_start").val();

		if (trigger_date_range_start) {
			var llink = jQuery(this).attr("href");
			llink += "&date_range_start=" + trigger_date_range_start;

			jQuery(this).attr("href", llink);
		}


		var trigger_date_range_end = jQuery("#trigger_date_range_end").val();

		if (trigger_date_range_end) {
			var llink = jQuery(this).attr("href");
			llink += "&date_range_end=" + trigger_date_range_end;

			jQuery(this).attr("href", llink);
		}


		var trigger_page_entries = jQuery("#trigger_page_entries").val();

		if (trigger_page_entries) {
			var llink = jQuery(this).attr("href");
			llink += "&page_entries=" + trigger_page_entries;

			jQuery(this).attr("href", llink);
		}

		return true;



	});

	/* SELECT ALL CHECKBOXES */
	jQuery("#tgg-select-all-1").on("change", function() {
		if (jQuery(this).prop("checked")) {
			jQuery("table tbody input[name^=tgg-history]").prop("checked", true);
		} else {
			jQuery("table tbody input[name^=tgg-history]").prop("checked", false);
		}
	});

	var $trigger_modal_dialog = jQuery("#trigger_modal_dialog");
	$trigger_modal_dialog.dialog({
		'dialogClass': 'wp-dialog',
		'modal': true,
		'autoOpen': false,
		'closeOnEscape': true,
		'buttons': {
			"Yes": function() {
				jQuery('#trigger_actions').submit();
			},
			"No": function() {
				jQuery(this).dialog('close');
			}
		}
	});

	/* CLICK ON EXPORT CSV BUTTON */
	jQuery("#trigger_export_csv").on("click", function(e) {
		jQuery("#trigger_action").val("export-csv");

		exportTableToCSV.apply(this, [jQuery('#trigger_actions table'), 'csv-export.csv']);
	});

	/* CLICK ON DELETE BUTTON */
	jQuery("#trigger_delete").on("click", function(e) {
		e.preventDefault();

		jQuery("#trigger_action").val("delete");

		$trigger_modal_dialog.find('.trigger-modal-dialog-body').text('Are you sure you want to permanently delete these ' + jQuery("table tbody input[name^=tgg-history]:checked").length + ' entries?');
		$trigger_modal_dialog.dialog('open');
	});

	function exportTableToCSV($table, filename) {
		/* ---------- Head ---------- */
		var $head_row = $table.find('thead tr:has(th)');

		tmpColDelim = String.fromCharCode(11);
		tmpRowDelim = String.fromCharCode(0);
		colDelim = '","';
		rowDelim = '"\r\n"';

		var csv_head = '"' + $head_row.map(function(i_head, row_head) {
				var $row_head = jQuery(row_head),
					$cols_head = $row_head.find('th:not(.manage-column)');

				return $cols_head.map(function(j_head, col_head) {
					var $col_head = jQuery(col_head),
						text_head = $col_head.text();

					if ($col_head.find('input[id^=tgg-select]').length == 0) {
						return text_head.replace(/"/g, '""').trim();
					}
				}).get().join(tmpColDelim);
			}).get().join(tmpRowDelim)
			.split(tmpRowDelim).join(rowDelim)
			.split(tmpColDelim).join(colDelim) + rowDelim;
		/* ---------- Head ---------- */


		var $rows = $table.find('tr:has(td)'),
			tmpColDelim = String.fromCharCode(11), // vertical tab character
			tmpRowDelim = String.fromCharCode(0), // null character
			// actual delimiter characters for CSV format
			colDelim = '","',
			rowDelim = '"\r\n"',
			// Grab text from table into CSV formatted string
			csv = $rows.map(function(i, row) {
				var $row = jQuery(row),
					$cols = $row.find('td');

				if ($row.find('input[name^=tgg-history]').prop('checked')) {
					return $cols.map(function(j, col) {
						var $col = jQuery(col),
							text = $col.text();

						if ($col.find('input[name^=tgg-history]').length == 0) {
							return text.replace(/"/g, '""'); // escape double quotes
						}
					}).get().join(tmpColDelim);
				}
			}).get().join(tmpRowDelim)
			.split(tmpRowDelim).join(rowDelim)
			.split(tmpColDelim).join(colDelim) + '"',

			// Data URI
			csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv_head + csv);

		jQuery(this).attr({
			'download': filename,
			'href': csvData,
			'target': '_blank'
		});
	}
});