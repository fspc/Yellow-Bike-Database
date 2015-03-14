$(function(){

	"use strict";
	
	$.ajaxSetup({async:false});

	var contact_id = $("#contact_id").val();
	var birth_date = $("#birth_date");
	var waiver_checkbox = $("#waiver_checkbox"), waiver_error = $("#waiver_error");
	var first_name = $("#first_name"), first_name_error = $("#first_name_error");
	var last_name = $("#last_name"), last_name_error = $("#last_name_error");
	var phone = $("#phone"), phone_error = $("#phone_error");
	var email = $("#email"), email_error = $("#email_error");
	var zip = $("#zip");
	var state_abbreviation = $("#state_abbreviation");


	// sensible defaults
	first_name.mask('#',{placeholder: "first", translation: {"#": {pattern: /[A-Za-z0-9.\-]/, recursive: true} } });
	last_name.mask('#',{placeholder: "last", translation: {"#": {pattern: /[A-Za-z0-9.\-]/, recursive: true} } });
	birth_date.mask("0000-00-00", {placeholder: "yyyy-mm-dd" });
	phone.mask('(000) 000-0000', {placeholder: "(000) 000-0000"});
	email.mask('#',{placeholder: "_@_", translation: {"#": {pattern: /[A-Za-z0-9@._\-+~!\$&''\(\)\*,;=:\%}{]/,
																			  recursive: true} } });
	zip.mask('00000-0000', {placeholder: "00000-0000"});
	state_abbreviation.mask('AA',{placeholder: "WV", translation: {"A": {pattern: /[A-Za-z]/, recursive: false} } });

	// make tabbing more predictable
	first_name.attr("tabindex",1);
	last_name.attr("tabindex",2);
	birth_date.attr("tabindex",3);
	email.attr("tabindex",4);
	phone.attr("tabindex",5);
	$('[name="address1"]').attr("tabindex",6);
	$('[name="city"]').attr("tabindex",7);
	$('[name="state"]').attr("tabindex",8);
	$('[name="zip"]').attr("tabindex",9);
	$('[name="password"]').attr("tabindex",10);
	$('#waiver_button').attr("tabindex",12);
	$('#interest_form_button').attr("tabindex",13);
	$('#submit_contact').attr("tabindex",14);

	first_name.focus();		
		
	// spiff up contact pull down
	var email_list_choice;	
	$("select[name='contact_id']").chosen();
	
	function save_contact() {				

		// Deferred Promise, since we don't know when the click will be made,
		// it is an asynchronous function, and we need to know the returned result.
		// Provides a clean separation of code.		
		var dfd = $.Deferred();	
	
			$("#submit_contact").on("click keypress", function(e) {					
				
				// check for errors
				//error_handler(input,error_span,error,error_text,event);
				
				var err0 = 0, err1 = 0, err2 = 0, err3 = 0, err4 = 0, err5 = 0;
				
				// if it is showing
				$("#email_list_error").hide();
		
				// first name & last name input
				err0 = error_handler(first_name.val(), first_name_error, "","*Required",e);
				err1 = error_handler(last_name.val(), last_name_error, "","*Required",e);		
				
				// email and phone input
				if (email.val() === "" && phone.val() === "") {
					
					err2 = error_handler(email.val(), email_error, "","*Required - email address and/or phone number",e);
					err3 = error_handler(phone.val(), phone_error, "","*Required - email address and/or phone number",e);	
					
				} else if (email.val() === "" && phone.val() !== "")  {
					
					email_error.hide();
					phone_error.hide();
					var r = phone_validator(phone.val(),e);
					
					var email_list_toggle = $("#email_list_toggle");
					var email_list_error = $("#email_list_error");
					if (r) {
						if(email_list_toggle.val() == 1) {
							err4 = error_handler(1, email_list_error, 1,"*Email address required for email list",e);
							email_list_choice = 1;
						} 				
					}
					
				} else if (email.val() !== "" && phone.val() === "") {
		
					email_error.hide();
					phone_error.hide();
					email_validator(email.val(),e);
		
				} else if (email.val()  && phone.val() ) {
		
					email_error.hide();
					phone_error.hide();				
					phone_validator(phone.val(),e);
					email_validator(email.val(),e);
					
				}
				
				// waiver checkbox
				err5 = error_handler(waiver_checkbox.prop("checked"),waiver_error,false,"*Required",e);
			
				if ((err0 + err1 + err2 + err3 + err4 + err5) > 0 ) {
					
				} else {
					//e.preventDefault();
					dfd.resolve("Success");
				}
	  	
	  	}); // end submit_contact

		return dfd.promise();  	
  	
  	} // end save_contact
  	
  	// successful submit of contact form
  	save_contact().done(function(success) { 
  	
  		// Process contact selects here (other than $_POST), waiver is always 1	unless not configured.
  		if (success === "Success") {
  		
	  		var email_list = $("#email_list_toggle").val();
	  		var waiver = waiver_checkbox.prop("checked");
	  		if (!email_list) {
				email_list = 0;  		
	  		}
	  		if (!waiver) {
				waiver = 0;  		
	  		} else if (waiver === true) {
				waiver = 1;  		
	  		} else if (waiver === false) {
	  			waiver = 0;
	  		}
			// update receive_newsletter and waiver in the database
	  		$.post("json/contact.php", {contact_id: contact_id, email_list: email_list, waiver: waiver });

			// email subscribe
			if (email_list == 1) {
				$.post("json/contact.php", {email_list_connector: 'subscribe', email: email.val(), 
													 first_name: first_name.val(), last_name: last_name.val()});
			} else {
				$.post("json/contact.php", {email_list_connector: 'unsubscribe', email: email.val(), 
											 		first_name: first_name.val(), last_name: last_name.val()});					
			}

		}  	
  	
  	 } ); // end successful submit of contact form
  		
  	
  	// show more button	
	function show_more(demo,demo_button) {
	  	$(demo).hide();
	  	var c=0;
	  	var button_value = demo_button.val();
	  	$(demo_button).click(function(e){
	  		e.preventDefault();
		  	if (c == 0) {
			$(demo).slideDown();
				$(this).attr("value","Show Less");
				c++;
		  	} else {
		   	$(demo).slideUp();
		   	$(this).attr("value",button_value);
		    	c--;
		    }
	  	} );  
	}  		
  	 
	show_more($('#waiver'),$('#waiver_button'));
	show_more($('#interest_form'),$('#interest_form_button'));
	  	 
 	$("#email_list_toggle").on("set",function() {  
  		if ($(this).val() == 0 && email_list_choice) {
  			$("#email_list_error").hide();
  		} 
  	} );	
  	
	function phone_validator(val, e) {
				var re = /^\(\d{3}\)\s?\d{3}-\d{4}$/;
				if ( !re.test(val) ) {
					error_handler(false, phone_error, false,"*Enter a correct phone number",e);
				} else {
					return true;				
				}
	}  	
  	

	function email_validator(val, e) {
				// https://fightingforalostcause.net/content/misc/2006/compare-email-regex.php
				var re = /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i;			
				if ( !re.test(val) ) {
					error_handler(false, email_error, false,"*Enter a correct email address",e);
				}
	}
	  	
  	
  	// error handler for contacts		
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
  	
  	
  	// email_list_toggle //
  	function toggle( value ){
		$(this).toggleClass('off', value === "0");
		$(this).toggleClass('on', value === "1");
	}


	// set waiver state
	$.post("json/contact.php", {contact_id: contact_id, waiver_value: 1 }, function(data) {
			if(data == 1) {
				$("#waiver_checkbox").prop("checked",true);			
			} else {
				$("#waiver_checkbox").prop("checked",false);		
			}		
		
	} );


	// beginning or stored state	
  	$.post("json/contact.php", {contact_id: contact_id, email_list_value: 1 }, function(data) {
	
		$("#email_list_toggle").noUiSlider({
			orientation: "horizontal",
			start: data,
			range: {
				'min': [0, 1],
				'max': 1
			},
			format: wNumb({
				decimals: 0
			})
		});
		
  	});

	
	$("#email_list_toggle").addClass('toggle');
	$("#email_list_toggle").addClass('noUi-extended');
	
		
	$("#email_list_toggle").Link('lower').to(toggle);
  	$("#email_list_toggle").Link('lower').to('-inline-<div id="off_or_on"></div>', function(value) {
		if (value == 0) {
			$(this).html("no");
		} else if (value == 1) {
			$(this).html("yes");
		}
  	});
	// end email_list_toggle //  	
  		
});