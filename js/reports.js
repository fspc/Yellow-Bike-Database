$(function(){

	"use strict";

	$.ajaxSetup({async:false});

	var contact_id = $("#contact_id").text();
		
	if (contact_id) {

		$("table").attr("width","");
		var prev = Number(contact_id) - 1, next = Number(contact_id) + 1;
		$(".stats-left").attr("href","/individual_history_log.php?contact_id=" + prev);
		$(".stats-right").attr("href","/individual_history_log.php?contact_id=" + next);
		
		// name
		$.post("json/reports.php", { name: 1, contact_id: contact_id }, function (data) {		
			if (data) {
				var obj = $.parseJSON(data);
				$("#name").text(obj.full_name)
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
		
	} // if contact_id

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
