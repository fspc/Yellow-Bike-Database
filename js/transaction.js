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

});