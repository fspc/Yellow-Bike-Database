/* jQuery fun with transactions - Jonathan Rosenbaum */

// currently css is just hardwired, but that reflects the coding style of YBDB :)

$(function() {

	$("select[name='transaction_type']").attr("tabindex",1);
	$("select[name='transaction_type']").focus();
	$("input[value='Create Transaction']").attr("tabindex",2);

	// paid or not?
	$(":checked").parent("td").prev().children().hide();
	$(".paid").click(function() {

		$.ajaxSetup({async:false});
		
		if ($(this).prop("checked")) { 
		
			$(this).closest("tr").css("background-color","#E6E7E6"); 
			$('[href*="' + this.name + '"]').hide(); 
	    	$.post("json/transaction.php",{ paid: 1, transaction_id: this.name } );
	 	} 
	 	else { 
	  		
	    	$(this).closest("tr").css("background-color","transparent");
	    	$('[href*="' + this.name + '"]').show(); 
	    	$.post("json/transaction.php",{ paid: 0, transaction_id: this.name } );
	  	} 

			// Deposit Calculator
			var deposit = {};		
			$(".deposit input").each(function(count){ 
				deposit[count] =  this.name;
			});			
			
					
			$.post("json/transaction.php",{"deposit": deposit}, function(data) {
				var obj = $.parseJSON(data);
				$.each(obj,function(k,v){
					//console.log(k + "= Cash: " + v.cash + " Check: " + v.check + " Credit: " + v.credit + "\n");
					$("#" + k + "_cash span").text("$" + v.cash);
					$("#" + k + "_check span").text("$" + v.check);
					$("#" + k + "_credit span").text("$" + v.credit);
				});
			});	  	
			$.ajaxSetup({async:true});	  	
	  	
	});

	// Deposit Calculator
	if ( $(".paid").length ) {	 // any transactions?
			
		var deposit = {};		
		$(".deposit input").each(function(count){ 
			deposit[count] =  this.name;
		});			
		
		$.post("json/transaction.php",{"deposit": deposit}, function(data) {
			var obj = $.parseJSON(data);
			$.each(obj,function(k,v){
				//console.log(k + "= Cash: " + v.cash + " Check: " + v.check + " Credit: " + v.credit + "\n");
				$("#" + k + "_cash span").text("$" + v.cash);
				$("#" + k + "_check span").text("$" + v.check);
				$("#" + k + "_credit span").text("$" + v.credit);
			});
		});
	
	}

	
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
		$("input[value='Save']").attr("tabindex",11);
		$("input[value='Close']").attr("tabindex",12);		
		
		// require that values be filled in a particular fashion
		$("#date").mask("0000-00-00");
	
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
			
			// require that values be filled in a particular fashion
			$("#date_startstorage").mask("0000-00-00");

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