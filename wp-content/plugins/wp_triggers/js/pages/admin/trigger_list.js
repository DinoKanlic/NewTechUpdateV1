jQuery(document).ready(function() {
	"use strict";

	jQuery(".row-actions a.menu_export_link").on("click", function(e) {
		e.preventDefault();

		var data = {
			action: "trigger_export",
			trigger_id: getQueryVariable(jQuery(this).attr("href"), "export")
		};

		var trigger_export_ajaxurl = ajaxurl + "?action=trigger_export&trigger_id=" + getQueryVariable(jQuery(this).attr("href"), "export");

		location.href = trigger_export_ajaxurl;
	});

});

function getQueryVariable(query_url, query_variable) {
	var vars = query_url.split("&");
	for (var i=0;i<vars.length;i++) {
		var pair = vars[i].split("=");
		if(pair[0] == query_variable) {
			return pair[1];
		}
	}

	return(false);
}