
function showAttributes(id, url) {
	jQuery.ajaxSetup ({  
		cache: false  
	});
	
	var ajax_load = "Loading Attribute Requirements ...";  
	  
	// load() functions  
	var loadUrl = url + '?id=' + id;  
	jQuery("#attrib_section").html(ajax_load).load(loadUrl); 
}

function showBulkAttributes(id, url) {
	jQuery.ajaxSetup ({  
		cache: false  
	});
	
	var ajax_load = "Loading Attribute Requirements ...";  
	  
	// load() functions  
	var loadUrl = url + '?id=' + id;  
	jQuery("#attrib_table_data").html(ajax_load).load(loadUrl); 
}

function showUsers(id, url) {
	jQuery.ajaxSetup ({  
		cache: false  
	});
	
	var ajax_load = "Loading User Details ...";  
	  
	// load() functions  
	var loadUrl = url + '?id=' + id;  
	jQuery("#users_section").html(ajax_load).load(loadUrl); 
}
