$(function(){

	"use strict";

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

});