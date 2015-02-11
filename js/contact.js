$(function(){

	"use strict";

	var birth_date = $("#birth_date");
	var waiver_checkbox = $("#waiver_checkbox"), waiver_error = $("#waiver_error");
	var first_name = $("#first_name"), first_name_error = $("#first_name_error");
	var last_name = $("#last_name"), last_name_error = $("#last_name_error");
	var phone = $("#phone"), phone_error = $("#phone_error");
	var email = $("#email"), email_error = $("#email_error");
	var zip = $("#zip");
	var state_abbreviation = $("#state_abbreviation");

	// sensible defaults
	first_name.mask('#',{placeholder: "first", translation: {"#": {pattern: /[A-Za-z0-9@.]/, recursive: true} } });
	last_name.mask('#',{placeholder: "last", translation: {"#": {pattern: /[A-Za-z0-9@.]/, recursive: true} } });
	birth_date.mask("0000-00-00", {placeholder: "yyyy-mm-dd" });
	phone.mask('(000) 000-0000', {placeholder: "(000) 000-0000"});
	email.mask('#',{placeholder: "_@_", translation: {"#": {pattern: /[A-Za-z0-9@.]/, recursive: true} } });
	zip.mask('00000-0000', {placeholder: "00000-0000"});
	state_abbreviation.mask('AA',{placeholder: "WV", translation: {"A": {pattern: /[A-Za-z]/, recursive: false} } });
	$("#submit_contact").on("click keypress", function(e) {				
		
		// check for errors
		//error_handler(input,error_span,error,error_text,event);
		
		var err0 = 0,  err1 = 0;

		// first name & last name input
		error_handler(first_name.val(), first_name_error, "","*Required",e);
		error_handler(last_name.val(), last_name_error, "","*Required",e);		
		
		// email and phone input
		if (email.val() === "" && phone.val() === "") {
			
			error_handler(email.val(), email_error, "","*Required - email address and/or phone number",e);
			error_handler(phone.val(), phone_error, "","*Required - email address and/or phone number",e);	
			
		} else if ( (email.val() === "" && phone.val() !== "") ) {
			
			email_error.hide();
			phone_error.hide();
			phone_validator(phone.val(),e);
						
		} else if ( (email.val() !== "" && phone.val() === "") ) {

			email_error.hide();
			phone_error.hide();
			email_validator(email.val(),e);

		} else if ( email.val()  && phone.val() ) {

			email_error.hide();
			phone_error.hide();				
			phone_validator(phone.val(),e);
			email_validator(email.val(),e);
			
		}
		
		// waiver checkbox
		error_handler(waiver_checkbox.prop("checked"),waiver_error,false,"*Required",event);
	
	} );

	
	// waiver slideup/slidedown
  	$('#waiver').hide();
  	var c=0;
  	$('#waiver_button').click(function(e){
  		e.preventDefault();
	  	if (c == 0) {
		$('#waiver').slideDown();
			$(this).attr("value","Hide Waiver");
			c++;
	  	} else {
	   	$('#waiver').slideUp();
	   	$(this).attr("value","Show Waiver");
	    	c--;
  	} });
  	
  	
	function phone_validator(val, e) {
				var re = /^\(\d{3}\)\s?\d{3}-\d{4}$/;
				if ( !re.test(val) ) {
					error_handler(false, phone_error, false,"*Enter a correct phone number",e);
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
  		
});