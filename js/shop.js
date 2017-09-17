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
	membership_status();
	
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
											
				
					if (obj.contact_id) {
				
						$("#volunteer_hours_" + obj.contact_id).html("Summary").
							parent().css({backgroundColor: "#19a0cc", textAlign: "center", cursor: "cell"}).
							prop("title",title).css({textAlign: "center"});
										
					} else {

						$("#volunteer_hours_" + id).parent().css({cursor: "not-allowed"});				
						
					}			
			
				}); // post volunteer benefits		
			} // if id				
		}); // each tr
		
	} // function volunteer_status
	
									
	// Is this a paid member?
	function membership_status() {	
			
		var expiration_date;
		var membership_obj; //reuse this object
		var membership_transaction;
		
		$.each($("#shop_log tr"), function() {  
		
			if (this.id) {
				var id = this.id;
				
				$.post("json/transaction.php", { membership_benefits: 1, contact_id: this.id }, function (data) { 					
											
					membership_obj = $.parseJSON(data);
							
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
							$("#paid_membership_" + membership_obj.contact_id).html("Expired").
							parent().css({backgroundColor: "red", textAlign: "center", cursor: "cell"}).prop("title",title);
					
						// paid membership
						} else if (d < expiration_date) {
							$("#paid_membership_" + membership_obj.contact_id).html("Current").
							parent().css({backgroundColor: "green", textAlign: "center", cursor: "cell"}).prop("title",title).css({textAlign: "center"});
						
						}	// paid membership
					
					// never been a member						
					} else { 
					
						$("#paid_membership_" + id).parent().css({cursor: "not-allowed"});			

					} // never been a member	
					
				}); // end if this a paid member
			} // if this.id
		}); // each
	} // function membership status

});