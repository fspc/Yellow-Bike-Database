$(function(){

	"use strict";
	
	$.ajaxSetup({async:false});

	// sensible defaults
	$("#shop_date").mask("0000-00-00", {placeholder: "yyyy-mm-dd" });
	
	$("[name='contact_id']").attr("tabindex",1);
	$("[name='contact_id']").trigger('chosen:activate');	
	$("[name='user_role']").attr("tabindex",2);
	$("[name='time_in']").attr("tabindex",3);
	$("[name='project']").attr("tabindex",4);
	$("[name='comment']").attr("tabindex",5);
	$("[name='Submit']").attr("tabindex",6);
	$(".shop_user_role").css({fontSize: ".75em", fontWeight: "bold"})
	
	// return banned list
	var banned_list, bl;
	$.post("json/contact.php", {banned: 1}, function (data) { 
		if (data) {
			bl = $.parseJSON(data);
			$(bl).each(function(i,v) { 
				if (i === 0) {		
					banned_list = "#" + v;
				} else { 
					banned_list = banned_list + ",#" + v; 
				}
			});
		}
	});

	$(banned_list).css({backgroundColor: "red"}).prop("title", "BANNED");

	// error handler for shops	with a popup dialog (TODO)	
	function error_handler(input,error_span,error,error_text,event) {		
		var trans_error = 0;
		if ( input == error ) {
			if ( !error_span.is(":visible") ) {
				error_span.show();		
			}									
			error_span.html(error_text);
			trans_error = 1;
		} else {
			trans_error = 0;
			error_span.hide();	
		}						
			
		if (trans_error) {
	   	event.preventDefault();
		} 
		
		return trans_error;

	} // end error_handling function


	function sign_out() {
		
		// Deferred Promise, since we don't know when the click will be made,
		// it is an asynchronous function, and we need to know the returned result.
		// Provides a clean separation of code.		
		var dfd = $.Deferred();	
	
		$(".sign_out").on("click keypress", function(e){ 
			
			//$.post("json/shop.php", {sign_out: 1});		
		
			dfd.resolve("Success"); 
			//event.preventDefault();
	
		});
		
		return dfd.promise();
	
	} // end sign_out
	
	//sign_out();
	
	// A function to provide a json string to provide apps with information on state of the current shop
	sign_out().done(function(success) {
		
		if (success === "Success") {
			$.post("json/shop.php", {sign_out: 1});
		}		

	} );


	// could have done this in php, but this separates out the view logic
	var d = new Date();
   volunteer_status();
   
	var membership_ids;
	var last_index = $("#shop_log tr").length;
	if (last_index) {
		last_index = last_index -1;
		$.each($("#shop_log tr"), function(index) {  
			
				if (this.id) {

					var id = this.id;

					// 2 tr for first created login
					if (last_index <= 2) {
						membership_ids = "contact_id=" + id;
					}
					else if (!membership_ids) {
						membership_ids = "(contact_id=" + id + " OR ";
					} else if ( index === last_index) {
						membership_ids += "contact_id=" + id + ")";
					} else {
						membership_ids += "contact_id=" + id + " OR ";
					}		
				}	   
		});
	}   
  	membership_status(membership_ids);
	
	// volunteer status
	function volunteer_status() {
		
		$.each($("#shop_log tr"), function() {  
		
			if (this.id) {
			 var id = this.id;

				$.post("json/transaction.php", { volunteer_benefits: 1, contact_id: this.id }, function (data) { 								
														
					var year = d.getFullYear();
					var bikes_earned = 0;
					var volunteer_hours_redeemed = 0;								
					var obj = $.parseJSON(data);
		
					var volunteer = "", remaining = 0, vhr = "", max_bikes_earned = 0;
					if (obj.volunteer) {
						volunteer = $.parseJSON(obj.volunteer);
						remaining = obj.current_year_volunteer_hours - volunteer[year].volunteer_hours_redeemed;
						vhr = volunteer[year].volunteer_hours_redeemed;
						max_bikes_earned = volunteer[year].max_bike_earned;
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
								$(".volunteer_hours_" + obj.contact_id).
									html("Summary | <span class='update_interests'><a href='./contact_add_edit_select.php?contact_id=" + 
									obj.contact_id + 
									"'>Update Interests</a></span>").
									parent().css({backgroundColor: "#19a0cc", textAlign: "center", cursor: "cell"}).
									prop("title",title).css({textAlign: "center"});
									
									$('.update_interests a').css({color: "#1b691e", textDecoration: "none", cursor: "crosshair"});
									$('.update_interests a').hover( function(e){ $(this).css("color",e.type === "mouseenter"?"blue":"#1b691e");});
							} else {
								$(".volunteer_hours_" + obj.contact_id).
									html("Summary | <span class='update_interests'><a href='./contact_add_edit_select.php?contact_id=" + 
									obj.contact_id + 
									"'>Update Interests</a></span>").
									parent().css({backgroundColor: "rgb(216, 198, 39)", textAlign: "center", cursor: "cell"}).
									prop("title",title).css({textAlign: "center"});
									
									$('.update_interests a').css({color: "#1b691e", textDecoration: "none", cursor: "crosshair"});
									$('.update_interests a').hover( function(e){ $(this).css("color",e.type === "mouseenter"?"blue":"#1b691e");});							
							
							}

						} else {
							
							title = obj.normal_full_name + "\r\n" +
											"Volunteer Hours for last 365 days: None" + "\r\n";
								
							$(".volunteer_hours_" + obj.contact_id).
								html("<span class='update_interests'><a href='./contact_add_edit_select.php?contact_id=" + 
								obj.contact_id + 
								"'>Update Interests</a></span>").
								parent().css({backgroundColor: "rgb(190, 199, 204)", textAlign: "center", cursor: "cell"}).
								prop("title",title).css({textAlign: "center"});
								
								$('.update_interests a').css({color: "#1b691e", textDecoration: "none", cursor: "crosshair"});
								$('.update_interests a').hover( function(e){ $(this).css("color",e.type === "mouseenter"?"blue":"#1b691e");});
								
						}
										
					} else {
						
						var name =  $("#" + id + " td a[href*='individual']").text();
						var name_obj = name.trim().split(", ");
						name = name_obj[1] + " " + name_obj[0];
	
						title = name + "\r\n" +
										"Volunteer Hours for last 365 days: None" + "\r\n";
							
						$(".volunteer_hours_" + id).
							html("<span class='update_interests'><a href='./contact_add_edit_select.php?contact_id=" + 
							id + 
							"'>Update Interests</a></span>").
							parent().css({backgroundColor: "rgb(190, 199, 204)", textAlign: "center", cursor: "cell"}).
							prop("title",title).css({textAlign: "center"});	
							
							$('.update_interests a').css({color: "#1b691e", textDecoration: "none", cursor: "crosshair"});
							$('.update_interests a').hover( function(e){ $(this).css("color",e.type === "mouseenter"?"blue":"#1b691e");});													
						
					}			
			
				}); // post volunteer benefits		
			} // if id				
		}); // each tr
		
	} // function volunteer_status
	
									
	// Is this a paid member?
	function membership_status(contacts) {	
			
		var expiration_date;
		var all_members_obj; //reuse this object
		var membership_transaction;
		
	
		//$.each($("#shop_log tr"), function() {  
		
			if (contacts) {
				
				$.post("json/transaction.php", { membership_benefits: 1, contact_id: contacts }, function (data) { 					
											
					all_members_obj = $.parseJSON(data);
					
				  	$.each(all_members_obj, function() {
					
						var membership_obj = this;
						
						var title = membership_obj.normal_full_name + "\r\n" +
												"expiration: " + membership_obj.expiration_date;
												
				
						if (membership_obj.expiration_date) {
							var exp = membership_obj.expiration_date;
							expiration_date = new Date(exp.split("-").toString());
							if (d < expiration_date) {	
								membership_transaction = true;
							}
						}
											
						if (typeof membership_obj.expiration_date && membership_obj.expiration_date !== undefined) {
			
							var exp = membership_obj.expiration_date;
							expiration_date = new Date(exp.split("-").toString());					
							
							// expired membership	
							if (d >= expiration_date) {												
								$(".paid_membership_" + membership_obj.contact_id).html("Expired").
								parent().css({backgroundColor: "red", textAlign: "center", cursor: "cell", textDecoration: "none"}).prop("title",title);
						
							// paid membership
							} else if (d < expiration_date) {
								$(".paid_membership_" + membership_obj.contact_id).html("Current").
								parent().css({backgroundColor: "green", textAlign: "center", cursor: "cell"}).prop("title",title).css({textAlign: "center"});
							
							}	// paid membership
											
						} 
						
					}); // each all_members_obj
				}); // end if this a paid member
				
				// never been a member				
				$(".paid_membership:not([title])").css({cursor: "not-allowed"});
				
			} // if contacts
	} // function membership status

});