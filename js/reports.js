$(function(){

	"use strict";

	$.ajaxSetup({async:false});

	var contact_id = $("#contact_id").text();
	
	// Basic prev and next buttons, and enhanced version would go in order of login per shop
	if (contact_id) {

		$("table").attr("width","");
		
		// handle logic for previous and next buttons
		//var prev, next;

		var prev = Number(contact_id) - 1, next = Number(contact_id) + 1;
		if (prev <= 0) {
			prev = 1;
		}
		if (next === 0) {
			next = 1;
		}
		
		$.post("json/reports.php", { total: 1 }, function (data) {
			var obj = $.parseJSON(data);

			if (Number(contact_id) > Number(obj.total)) {
				prev = obj.total;
				next = obj.total;
			} else if (Number(contact_id) === Number(obj.total)) {
				next = obj.total;
			}	
		});		
		
		$(".stats-left").attr("href","/individual_history_log.php?contact_id=" + prev);
		$(".stats-right").attr("href","/individual_history_log.php?contact_id=" + next);
		
		var record_count;
		$.post("json/reports.php", { record_count: 1 }, function (data) {		
			record_count = data;
			record_count = $.parseJSON(data);
		});		
		
		// name
		var date;
		$.post("json/reports.php", { name: 1, contact_id: contact_id }, function (data) {		
			if (data) {
				var d = new Date();
				date = d.toISOString().split('T')[0];
				var obj = $.parseJSON(data);
				var full_name_with_link = 	'<a style="color: rgb(27, 105, 30); text-decoration: none; cursor: crosshair;"' +
													' href="transaction_log.php?trans_date=' + date + 
													'&trans_type=all_types&shop_dayname=alldays&record_count=' + record_count.record_count + 
													'&contact_id_search=' +
					  								contact_id + '">' + obj.full_name + "</a>";
				$("#name").html(full_name_with_link);
				var pad_name;					
				if (obj.configurations.prefix) {
					pad_name = obj.configurations.prefix + "_pad_contact_id_" + contact_id;
				} else {
					pad_name = "pad_contact_id_" + contact_id;
				}				
				//console.log(pad_name);
				if ( obj.configurations.host && obj.full_name ) {
					$("#individual_history_pad").pad({
						"padId": pad_name, 
						"host": obj.configurations.host, 
						"showControls": true,
						"height": obj.configurations.height,
						"userName": obj.configurations.userName,
						"noColors": obj.configurations.noColors,
						"plugins" : obj.configurations.plugins
						});
				}
			}
			
		}); // name		
		
		// tabulator
		$.post("json/reports.php", { individual_history: 1, contact_id: contact_id }, function (data) {
			if (data) {
				var obj = $.parseJSON(data);			
				
				// use height:315 for 10 rows if non-pagination
				$("#individual_history").tabulator({
					pagination:"local",
	    			paginationSize:10,
					responsiveLayout:true, 
	    			layout:"fitColumns",
					columns:[
						{title:"Status", field:"shop_user_role", align:"center", width:125, editable:off, editor:statusEditor, headerFilter:true},
						{title:"Date", field:"date", align:"center",width:100, headerFilter:"input"},
						{title:"Day", field:"dayname", align:"center", width:100, editable:off, editor:dayEditor, headerFilter:true},
						{title:"Shop", field:"shop_id", sorter:"number", headerFilter:"number", headerFilterPlaceholder:"ShopId", align:"center", width:85,
							formatter:function(cell, formatterParams){
								var shop_id = cell.getValue();
	        					return '<a href="./shop_log.php?shop_id=' + shop_id + '">' + shop_id + "</a>";
	        				},
	        			},
						{title:"Time In", field:"time_in", align:"center", width:100, sorter:"time", sorterParams:{format:"hh:mm:ss"},
							mutator:function(value, data, type, mutatorParams, cell){
								var time_in = value.split(" ");
								return time_in[1];
	        				}						
						},
						{title:"Time Out", field:"time_out", align:"center", width:100, sorter:"time", sorterParams:{format:"hh:mm:ss"},
							mutator:function(value, data, type, mutatorParams, cell){
								var time_out = value.split(" ");
								return time_out[1];
	        				}						
						},
						{title:"Total", field:"et", align:"center", width:100, sorter:"time", sorterParams:{format:"h:mm"},
							formatter:function(cell, formatterParams){
								var hr_total = cell.getValue();
								if (hr_total) {	        					
	        						return hr_total + " hrs";
	        					}
	        				}  
						
						},
						{title:"Project", field:"project_id", align:"center", width:125, editable:off, editor:projectEditor, headerFilter:true},
						{title:"Comments", field:"comment", formatter:"textarea", headerFilter:"input"}			
					]			
				});			
				$("#individual_history").tabulator("setData", obj);
			}
			
		}); // tabulator
		
		// Is contact a member
		var contacts = "contact_id=" + contact_id;
		$.post("json/transaction.php", { membership_benefits: 1, contact_id: contacts }, function (data) {	
		
			var membership, membership_obj, membership_objs = $.parseJSON(data);

			/*
		 	Weird hack, before improving performance #46, there was always a property for membership_obj,
			membership_discount:10, which just allowed the code to work, now it is empty when a patron 
			actually is not a paid member, so this creates that obj & property if that is the case.
			*/
			membership_obj = membership_objs[0] || { membership_discount: 10 };				

			var title = membership_obj.normal_full_name + "\r\n" +
							membership_obj.email + "\r\n" +
							membership_obj.phone + "\r\n" +
							"expiration: " + membership_obj.expiration_date;						
									
			var expiration_date;
			var d = new Date();
			if (membership_obj.expiration_date) {
				var exp = membership_obj.expiration_date;
				expiration_date = new Date(exp.split("-").toString());
				if (d < expiration_date) {	
					membership = true;
				}
			}
			
			
			if (typeof membership_obj.expiration_date && membership_obj.expiration_date !== undefined) {
				
				if (d >= expiration_date) {
					var membership = '<a href="transaction_log.php?trans_date=' + date + 
													'&trans_type=all_types&shop_dayname=alldays&record_count=' + record_count.record_count + 
													'&contact_id_search=' +
					  								contact_id + '">' + "Expired Membership</a>";
					$("#membership_status").prop("title",title).html(membership).children().
													css({color: "rgb(27, 105, 30)", textAlign: "center", cursor: "help", textDecoration: "none"});
				} else if (d < expiration_date) {
					var membership = '<a href="transaction_log.php?trans_date=' + date + 
													'&trans_type=all_types&shop_dayname=alldays&record_count=' + record_count.record_count + 
													'&contact_id_search=' +
					  								contact_id + '">' + "Paid Membership</a>";
					$("#membership_status").prop("title",title).html(membership).children().
													css({color: "rgb(27, 105, 30)", textAlign: "center", cursor: "help", textDecoration: "none"});
					
				}
						
			}

		
		}); // Is contact a member
		
	} // if contact_id
	
	// stats_userhours
	if ( document.location.pathname.match(/stats_userhours_new\.php$/) ) {
		
		// everyone
		$.post("/json/reports.php", { everyone: 1 }, function (data) {	
	
			if (data) {
				var obj = $.parseJSON(data);
			}
			
		}); // everyone		
		
	} // status_userhours

	function off(cell){
		return false;
	}

	 //cell - the cell component for the editable cell
	 //onRendered - function to call when the editor has been rendered
	 //success - function to call to pass the succesfully updated value to Tabulator
	 //cancel - function to call to abort the edit and return to a normal cell
	 //editorParams - editorParams object set in column defintion
	//var dayEditor = 
	function dayEditor(cell, onRendered, success, cancel, editorParams){
	
	    //create and style editor
	    var editor = $("<select><option value=''></option>" +
	    									"<option value='Monday'>Monday</option>" +
	    									"<option value='Tuesday'>Tuesday</option>" +
	    									"<option value='Wednesday'>Wednesday</option>" +
	    									"<option value='Thursday'>Thursday</option>" +
	    									"<option value='Friday'>Friday</option>" +
	    									"<option value='Saturday'>Saturday</option>" +
	    									"<option value='Sunday'>Sunday</option>" +
	    						"</select>"
	    					);
	    editor.css({
	        "padding":"3px",
	        "width":"100%",
	        "box-sizing":"border-box",
	    });
	
	    //Set value of editor to the current value of the cell
	    editor.val(cell.getValue());
	
	    //set focus on the select box when the editor is selected (timeout allows for editor to be added to DOM)
	    onRendered(function(){
	      editor.focus();
	    });
	
	    //when the value has been set, trigger the cell to update
	    editor.on("change blur", function(e){
	        success(editor.val());
	    });
	
	    //return the editor element
	    return editor;
	} // date editor

	function projectEditor(cell, onRendered, success, cancel, editorParams){
	
		var projects;

		$.post("json/reports.php", { projects: 1 }, function (data) {	
			var obj = $.parseJSON(data);
			
			projects = "<select>";
			$.each(obj, function(k,v) { 
				projects += "<option value='" + v.project_id + "'>" + v.project_id + "</option>";
			});
			projects += "</select>";
			
		});		
		
	    //create and style editor
	    var editor = $(projects);
	    editor.css({
	        "padding":"3px",
	        "width":"100%",
	        "box-sizing":"border-box",
	    });
	
	    //Set value of editor to the current value of the cell
	    editor.val(cell.getValue());
	
	    //set focus on the select box when the editor is selected (timeout allows for editor to be added to DOM)
	    onRendered(function(){
	      editor.focus();
	    });
	
	    //when the value has been set, trigger the cell to update
	    editor.on("change blur", function(e){
	        success(editor.val());
	    });
	
	    //return the editor element
	    return editor;
	} // project editor

	function statusEditor(cell, onRendered, success, cancel, editorParams){
	
		var projects;

		$.post("json/reports.php", { roles: 1 }, function (data) {	
			var obj = $.parseJSON(data);
			
			projects = "<select><option value=''></option>";
			$.each(obj, function(k,v) { 
				projects += "<option value='" + v.shop_user_role_id + "'>" + v.shop_user_role_id + "</option>";
			});
			projects += "</select>";
			
		});		
		
	    //create and style editor
	    var editor = $(projects);
	    editor.css({
	        "padding":"3px",
	        "width":"100%",
	        "box-sizing":"border-box",
	    });
	

	    //Set value of editor to the current value of the cell
	    editor.val(cell.getValue());
	
	    //set focus on the select box when the editor is selected (timeout allows for editor to be added to DOM)
	    onRendered(function(){
	      editor.focus();
	    });
	
	    //when the value has been set, trigger the cell to update
	    editor.on("change blur", function(e){
	        success(editor.val());
	    });
	    
	    //return the editor element
	    return editor;
	} // project editor

});
