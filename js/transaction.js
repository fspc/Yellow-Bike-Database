/* jQuery fun with transactions - Jonathan Rosenbaum */

// currently css is just hardwired, but that reflects the coding style of YBDB :)

$(function() {

	$(".paid").click(function() {
		if ($(this).prop("checked")) { 
			//console.log("turn color on");
			$(this).closest("tr").css("background-color","#99CC33");  
	    	$.post("json/transaction.php",{ paid: 1, transaction_id: this.name } );
	 	} 
	  	else { 
	  		//console.log("turn color off");
	    	$(this).closest("tr").css("background-color","transparent");  
	    	$.post("json/transaction.php",{ paid: 0, transaction_id: this.name } );
	  	} 
	});

	// make tabbing more predictable
	$("input[name='shop_id']").attr("tabindex",1);
	$("select[name='transaction_type']").attr("tabindex",2);
	$("input[name='date']").attr("tabindex",3);
	$("input[name='amount']").attr("tabindex",4)
	$("input[name='quantity']").attr("tabindex",5)
	$("textarea[name='description']").attr("tabindex",6)
	$("select[name='sold_to']").attr("tabindex",8)
	$("select[name='sold_by']").attr("tabindex",9)


});