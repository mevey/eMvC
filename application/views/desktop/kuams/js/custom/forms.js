/*
 * 	Additional function for forms.html
 *	Written by ThemePixels	
 *	http://themepixels.com/
 *
 *	Copyright (c) 2012 ThemePixels (http://themepixels.com)
 *	
 *	Built for Amanda Premium Responsive Admin Template
 *  http://themeforest.net/category/site-templates/admin-templates
 */

jQuery(document).ready(function(){
	
	///// FORM TRANSFORMATION /////
	jQuery('input:checkbox, input:radio, select.uniformselect, input:file').uniform();


	///// DUAL BOX /////
	var db = jQuery('#dualselect').find('.ds_arrow .arrow');	//get arrows of dual select
	var sel1 = jQuery('#dualselect select:first-child');		//get first select element
	var sel2 = jQuery('#dualselect select:last-child');			//get second select element
	
	sel2.empty(); //empty it first from dom.
	
	db.click(function(){
		var t = (jQuery(this).hasClass('ds_prev'))? 0 : 1;	// 0 if arrow prev otherwise arrow next
		if(t) {
			sel1.find('option').each(function(){
				if(jQuery(this).is(':selected')) {
					jQuery(this).attr('selected',false);
					var op = sel2.find('option:first-child');
					sel2.append(jQuery(this));
				}
			});	
		} else {
			sel2.find('option').each(function(){
				if(jQuery(this).is(':selected')) {
					jQuery(this).attr('selected',false);
					sel1.append(jQuery(this));
				}
			});		
		}
	});
	
	///// FORM VALIDATION /////
	if (jQuery("#asset_add").length != 0) jQuery("#asset_add").validate({
		rules: {
			tag_id: "required",
			model: "required",
			cost: "required",
			date: {
				required: true,
				date: true
			}
		},
		messages: {
			tag_id: "Please enter a tag ID for the asset",
			model: "Please enter a model or type of the asset",
			cost: "Please enter the cost of the asset",
			date: "Please enter the supply date of the asset"
		}
	});
	
	if (jQuery("#dept_asset_add").length != 0) jQuery("#dept_asset_add").validate({
		rules: {
			tag_id: "required",
			model: "required",
			cost: "required",
			date: {
				required: true,
				date: true,
			}
		},
		messages: {
			tag_id: "Please enter a tag ID for the asset",
			model: "Please enter a model or type of the asset",
			cost: "Please enter the cost of the asset",
			date: "Please enter the supply date of the asset"
		}
	});
	
	if (jQuery("#dept_req_add").length != 0) jQuery("#dept_req_add").validate({
		rules: {
			model: "required",
			title: "required",
			body: "required"
		},
		messages: {
			title: "Please enter the title of the request",
			model: "Please enter a model or type of the asset",
			body: "Please enter a message here"
		}
	});
	
	if (jQuery("#memo_add").length != 0) jQuery("#memo_add").validate({
		rules: {
			title: "required",
			body: "required"
		},
		messages: {
			title: "Please enter the title of the request",
			body: "Please enter a message here"
		}
	});
	
	if (jQuery("#bulk_add").length != 0) jQuery("#bulk_add").validate({
		rules: {
			cost: "required",
			date: {
				required: true,
				date: true,
			}
		},
		messages: {
			cost: "Please enter the cost of the asset",
			date: "Please enter the supply date of the asset"
		}
	});
	
	if (jQuery("#cat_add").length != 0) jQuery("#cat_add").validate({
		rules: {
			name: "required",
			description: "required",
			dep_rate: "required",
			retire: "required"
		},
		messages: {
			name: "Please enter a name for this category",
			description: "Please enter a description for this category of assets",
			dep_rate: "Please enter the depreciation rate to be applied to this asset",
			retire: "Please enter the retire age for this category off assets"
		}
	});
	
	if (jQuery("#profile_edit").length != 0) jQuery("#profile_edit").validate({
		rules: {
			surname: "required",
			othername: "required",
			username: "required",
			office: "required",
			role: "required",
			id_number: "required",
			phone_number: {
				required: true,
				phone: true,
			},
			email: {
				required: true,
				email: true,
			}
		},
		messages: {
			surname: "Please enteryour surname",
			othername: "Please enter your second and/or last name",
			username: "Please enter your preferred username which you will use to login",
			office: "Please enter your current office location",
			role: "Please enter your the position you currently hold",
			id_number: "Please enter your National Identification number",
			phone_number: "Please enter a valid phone_number",
			email: "Please enter a valid email address",
		}
	});
	
	if (jQuery("#user_add").length != 0) jQuery("#user_add").validate({
		rules: {
			emp_number: "required",
			role: "required",
			date: {
				required: true,
				date: true,
			}
		},
		messages: {
			emp_number: "Please enter an employee number for this user",
			role: "Please enter the job title of this user",
			date: "Please enter the employment number for this user"
		}
	});
	
	if (jQuery("#old_pass").length != 0) jQuery("#old_pass").validate({
		rules: {
			old_password:{ required:true, password:true }
		},
		messages: {
			old_password: "Please enter your old password here"
			
		}
	});
	
	if (jQuery("#new_pass").length != 0) jQuery("#new_pass").validate({
		rules: {
			new_password: { required:true, password:true },
			confirm_password: { required:true, password:true }
		},
		messages: {
			new_password: "Please enter your old password here",
			confirm_password: "Please enter your new password again"
			
		}
	});
	
	///// DATE PICKER /////
	if (jQuery("#datepickfrom").length != 0 && jQuery("#datepickto").length != 0) jQuery( "#datepickfrom, #datepickto" ).datepicker();
	if (jQuery("#datepick").length != 0 ) jQuery( "#datepick" ).datepicker();
	if (jQuery("#date").length != 0 ) jQuery( "#date" ).datepicker();
	if (jQuery("#date1").length != 0 ) jQuery( "#date1" ).datepicker();
	if (jQuery("#date2").length != 0 ) jQuery( "#date2" ).datepicker();
	if (jQuery("#date3").length != 0 ) jQuery( "#date3" ).datepicker();
	if (jQuery("#date4").length != 0 ) jQuery( "#date4" ).datepicker();
	if (jQuery("#date5").length != 0 ) jQuery( "#date5" ).datepicker();
	if (jQuery("#date6").length != 0 ) jQuery( "#date6" ).datepicker();
	if (jQuery("#date7").length != 0 ) jQuery( "#date7" ).datepicker();
	///// TAG INPUT /////
	
	//jQuery('#tags').tagsInput();

	
	///// SPINNER /////
	
	//jQuery("#spinner").spinner({min: 0, max: 100, increment: 2});
	
	
	///// CHARACTER COUNTER /////
	
	/*jQuery("#textarea2").charCount({
		allowed: 120,		
		warning: 20,
		counterText: 'Characters left: '	
	});
	
	
	///// SELECT WITH SEARCH /////
	jQuery(".chzn-select").chosen();*/
	
});
