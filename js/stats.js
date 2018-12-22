$(function(){

	// Provide global defaults for date range picker on stat pages

	"use strict";
	//$.ajaxSetup({async:false})

	var range = $("#range");
	var range_input = $("#range_input");
	

	range.pickmeup({
		flat	: true,
		mode	: 'range',
		format  : 'Y/m/d',
		change: function(){
            $(this).trigger('change');
        }
	});
	
	range.change(function(){
		var range_display = range.pickmeup('get_date', true);
		range_input.text("Date Range: " + range_display[0] + " - " + range_display[1]);
    });

	$("#status_totals").on("click keypress", function(e){
		var range_display = range.pickmeup('get_date', true); 
		$.post("status_totals.php", {range1: range_display[0], range2: range_display[1]}, function (data) {
			$("body").html(data);
			//range.pickmeup.date($range_display[0]);	
		});
		e.preventDefault();
	});
	
	$("#community_service_hours").on("click keypress", function(e){
		var range_display = range.pickmeup('get_date', true); 
		$.post("community_service_hours.php", {range1: range_display[0], range2: range_display[1]}, function (data) {
			$("body").html(data);
			//range.pickmeup.date($range_display[0]);	
		});
		e.preventDefault();
	});
	
	$("#dhhr_hours").on("click keypress", function(e){
		var range_display = range.pickmeup('get_date', true); 
		$.post("dhhr.php", {range1: range_display[0], range2: range_display[1]}, function (data) {
			$("body").html(data);
			//range.pickmeup.date($range_display[0]);	
		});
		e.preventDefault();
	});

	$("#shops").on("click keypress", function(e){
		var range_display = range.pickmeup('get_date', true); 
		$.post("shops.php", {range1: range_display[0], range2: range_display[1]}, function (data) {
			$("body").html(data);
			//range.pickmeup.date($range_display[0]);	
		});
		e.preventDefault();
	});
	
	$("#members").on("click keypress", function(e){
		var range_display = range.pickmeup('get_date', true); 
		$.post("members.php", {range1: range_display[0], range2: range_display[1]}, function (data) {
			$("body").html(data);
			//range.pickmeup.date($range_display[0]);	
		});
		e.preventDefault();
	});


	var volunteer_ids;
	var last_index = $("tr[id]").length;
	if (last_index) {
		last_index = last_index - 1;
		$.each($("tr[id]"), function(index) {  			
			
			if (this.id) {

				var id = this.id;

				if (last_index < 1) {
					volunteer_ids = "contacts.contact_id=" + id;
				}
				else if (!volunteer_ids) {
					volunteer_ids = "(contacts.contact_id=" + id + " OR ";
				} else if ( index === last_index) {
					volunteer_ids += "contacts.contact_id=" + id + ")";
				} else {
					volunteer_ids += "contacts.contact_id=" + id + " OR ";
				}		
			}
					   
		});
	}

	var d = new Date();
   volunteer_status(volunteer_ids); 

	// volunteer status 
	// Essentially, the same function (but with modifications) as in shop.js and transaction.js
	function volunteer_status(contacts) {

		var all_members_obj; //reuse this object
		var year = d.getFullYear();
			
		if (contacts) {  

			$.post("../json/transaction.php", { volunteer_benefits: 1, contact_id: contacts }, function (data) { 								

				all_members_obj = $.parseJSON(data);
				
			  	$.each(all_members_obj, function() {
			  																				
					var bikes_earned = 0;
					var volunteer_hours_redeemed = 0;								
					var obj = this;
				
					var volunteer = "", remaining = 0, vhr = "", max_bikes_earned = 0;
					if (obj.volunteer) {
						volunteer = $.parseJSON(obj.volunteer);
						if (volunteer.hasOwnProperty(year)) {
							remaining = obj.current_year_volunteer_hours - volunteer[year].volunteer_hours_redeemed;
							vhr = volunteer[year].volunteer_hours_redeemed;
							max_bikes_earned = volunteer[year].max_bike_earned;
						}
					} else {
						vhr = 0;
					}			
					
					var title = obj.normal_full_name + "\r\n" +
											"Volunteer Hours for last 365 days: " + obj.volunteer_hours + "\r\n" +
											"Volunteer Hours \(" + year + "\): " + obj.current_year_volunteer_hours + "\r\n" +
											"Volunteer Hours Redeemed: " +  vhr + "\r\n" +
											"Volunteer Hours Remaining: " + remaining + "\r\n" +
											"Max Bikes Earned: " + max_bikes_earned;		
					
					var volunteer_with_redeemed_hours_at_zero = obj.current_year_volunteer_hours - vhr;				
				
					if (obj.contact_id) {
											
						if (obj.volunteer_hours && obj.volunteer_hours !== '0') {
							
							if (volunteer_with_redeemed_hours_at_zero !== 0) {								
								$("#" + obj.contact_id + " td").first().
									css({textAlign: "center", cursor: "cell"}).
									prop("title",title).css({textAlign: "center"});
									
							} else {
								$("#" + obj.contact_id + " td").first().
									css({backgroundColor: "rgb(216, 198, 39)", textAlign: "center", cursor: "cell"}).
									prop("title",title).css({textAlign: "center"});			
							
							}
	
						} else {
							
							title = obj.normal_full_name + "\r\n" +
											"Volunteer Hours for last 365 days: None" + "\r\n";
								
							$("#" + obj.contact_id + " td").first().
								css({backgroundColor: "rgb(190, 199, 204)", textAlign: "center", cursor: "cell"}).
								prop("title",title).css({textAlign: "center"});
								
						}
										
					} else {										
							
						var name =  $("#" + id + " td a[href*='individual']").text();
						var name_obj = name.trim().split(", ");
						name = name_obj[1] + " " + name_obj[0];
	
						title = name + "\r\n" +
										"Volunteer Hours for last 365 days: None" + "\r\n";
							
						$("#" + obj.contact_id + " td").first().
							css({backgroundColor: "rgb(190, 199, 204)", textAlign: "center", cursor: "cell"}).
							prop("title",title).css({textAlign: "center"});												
					}	

				}); // each all_members_obj
			}); // post volunteer benefits					
		} // if this is a volunteer		
		
	} // function volunteer_status

});