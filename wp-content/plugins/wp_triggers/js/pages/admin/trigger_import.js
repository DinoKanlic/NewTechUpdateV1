jQuery(document).ready(function() {
	"use strict";

	var messaging = jQuery("#trigger_import_frm .mymessage");

	if(file_import_error) {
		setting_message_show(file_import_error, "err_message", messaging);
	}
});