<?php

if(!defined('WPTGG_UNINSTALL_DELETE_DATA')) {
	// do nothing.
} else {
	if(WPTGG_UNINSTALL_DELETE_DATA === false) {
		// do nothing.
	} else {
		wptgg_delete_all_data();
	}
}

function wptgg_delete_all_data() {
	global $wpdb;

	delete_option("wptgg_info");
	$table_arr = array($wpdb->prefix . "trigger", $wpdb->prefix . "trigger_history");

	foreach($table_arr as $table_name) {
		$sql = "DROP TABLE ". $table_name;
		$wpdb->query($sql);
	}
}
?>