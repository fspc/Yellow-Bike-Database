/* jQuery fun with transactions - Jonathan Rosenbaum */

// currently css is just hardwired, but that reflects the coding style of YBDB :)

$(function() {

	// paid or not?
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

	
	// editing a transaction
	if ( $("input[name='shop_id']").length ) {
	
		// make tabbing more predictable
		$("input[name='shop_id']").attr("tabindex",1);
		$("select[name='transaction_type']").attr("tabindex",2);
		$("input[name='date_startstorage']").attr("tabindex",3);
		$("input[name='date']").attr("tabindex",4);		
		$("input[name='quantity']").attr("tabindex",5);
		$("textarea[name='description']").attr("tabindex",6);
		$("input[name='amount']").attr("tabindex",7);
		$("input[name='payment_type']").attr("tabindex",8);
		$("select[name='sold_to']").attr("tabindex",9);
		$("select[name='sold_by']").attr("tabindex",10);
	
		$transaction_id = $("input[name='transaction_id']").val();
		
		// what type of payment? cash, credit or check?
		$("input[name='payment_type']").click(function() { 
			if ($(this).prop("checked")) { 
				$.post("json/transaction.php",{ payment_type: this.value, transaction_id: $transaction_id } );
			}
		});
		
		/* When the transaction is storage based, only show price and payment_type 
		   when a full date (yyyy-mm-dd) is entered. */
		if ( $("#date_startstorage").length ) {
			
			var date_value = $("#date").val();
			var date_test = /^\d{4}-((0\d)|(1[012]))-(([012]\d)|3[01])$/.test(date_value);
	
			if ( date_test && date_value != "0000-00-00" ) {
				$("#price").show();			
				$("#payment_type").show();
			} else {
				$("#price").hide();			
				$("#payment_type").hide();	
			}
			
			$("#date_fill").click(function(){ 
					$("#price").show();			
					$("#payment_type").show();
			})			
			
			$("#date").on("input", function(){ 
				
				date_test = /^\d{4}-((0\d)|(1[012]))-(([012]\d)|3[01])$/.test(this.value);
				if ( date_test && this.value != "0000-00-00" ) {
					$("#price").show();			
					$("#payment_type").show();
				} else {
					$("#price").hide();			
					$("#payment_type").hide();	
				}
			});
	
		} // end testing for storage presentation		   
			
			
	} // editing a transaction

});