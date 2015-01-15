/* jQuery fun with transactions - Jonathan Rosenbaum */

// currently css is just hardwired, but that reflects the coding style of YBDB :)


$(function() {

	$.ajaxSetup({async:false}); // best to do this in $.ajax, 
										 // but all ajax needs to be synchronous in this program because of the use of mysql

	$("select[name='transaction_type']").attr("tabindex",1);
	$("select[name='transaction_type']").focus();
	$("input[value='Create Transaction']").attr("tabindex",2);
	$("#trans_date").mask("0000-00-00", {placeholder: "yyyy-mm-dd" });


	// If page has not been reloaded after a shop period ends, prevent edit from working.
   // Note: create transaction covered via a mysql_error()), but with a reload.
	$('[href*="trans_id="]').click(function(e){  
 		var remember_me;
		$.post("json/transaction.php", {shop_exist: 1}, function(data) {
  
  			if (data == "no_shop") {
    			var start_new_shop = "Start New Shop";	
    			$("#current_shop").html("&nbsp<a style='color:red' href='shop_log.php'>" + start_new_shop + "</a>");
  			} else {
    			remember_me = "unbind";
  			}
		});
  		if (remember_me == "unbind") { 
  		 $('[href*="trans_id="]').on('click'); 
  		 }
  		 else {  
  		 	e.preventDefault(); 
  		 }
	} );

  // Do the same thing as previously, but for editing a transaction (could make a function :)
	$('#save_transaction').click(function(e){  
 		var remember_me;
		$.post("json/transaction.php", {shop_exist: 1}, function(data) {
  
  			if (data == "no_shop") {
    			var start_new_shop = "Start New Shop";	
    			$("#transaction_start_error").html("&nbsp<a style='color:red' href='shop_log.php'>" + start_new_shop + "</a>");
  			} else {
    			remember_me = "unbind";
  			}
		});
  		if (remember_me == "unbind") { 
  		 $('#save_transaction').on('click'); 
  		 }
  		 else {  
  		 	e.preventDefault(); 
  		 }
	} );

	// Does a current shop exist?
	var open_shop;
	$.post("json/transaction.php", {shop_exist: 1}, function(data) { 
		if (data == "no_shop") {
		
			open_shop = data;
			var start_new_shop = "Start New Shop";			
			
			$("input[name='Submit43']").click(function(){
				$("#current_shop").html("&nbsp<a style='color:red' href='shop_log.php'>" + start_new_shop + "</a>");				
				event.preventDefault();		
			});
			$('[href*="trans_id="]').click(function(){
				$("#current_shop").html("&nbsp<a style='color:red' href='shop_log.php'>" + start_new_shop + "</a>");				
				event.preventDefault();			
			});
			$(".paid").click(function() {  
				$("#current_shop").html("&nbsp<a style='color:red' href='shop_log.php'>" + start_new_shop + "</a>");								
				return false; 
			});
			$('.editable_change').mouseover(function() {
				$("#current_shop").html("&nbsp<a style='color:red' href='shop_log.php'>" + start_new_shop + "</a>");								
				$('.editable_change').editable("disable");
			});		
		}
	} );

	// paid or not?
	$(":checked").parent("td").prev().children().hide();
	$(".paid").click(function() {

		if (open_shop == "no_shop") {  return false; }
		
		if ($(this).prop("checked")) { 
		
			$(this).closest("tr").css("background-color","#E6E7E6"); 
			$('[href*="trans_id=' + this.name + '"]').hide();
			$.post("json/transaction.php",{ paid: 1, transaction_id: this.name } ); 
	 	} 
	 	else { 
	  		
	    	$(this).closest("tr").css("background-color","transparent");
	    	$('[href*="trans_id=' + this.name + '"]').show(); 
	    	$.post("json/transaction.php",{ paid: 0, transaction_id: this.name } );
	  	} 

			// Deposit Calculator for clicks
  			deposit_calculator();  	
	  	
	}); // paid or not?

	// Deposit Calculator on page reload
	if ( $(".paid").length ) {	 // any transactions?
			
			deposit_calculator();
	
	}

	// Make Change Fund editable
	$('.editable_change input').mask("#0.00",{reverse: true, placeholder: "000"});
	$('.editable_change').editable("json/transaction.php", 
	{ 
		tooltip: "edit", 
		event: "mouseover", 
		onblur: "submit",
		name: "editable_change",
		height: "auto", 
		callback : function(value, settings) {
      	var obj = $.parseJSON(value)
		   $("#" + this.id).text(obj.changed_change);
			var diff = Number(obj.changed_change) - Number(obj.change);
			var str = this.id;			
			var id = str.match(/\d+/);	
			if (diff != 0) {
				/*				
				if(!$("#" + id[0] + "_different_change").length) {
					$("#" + this.id).after("<span id=" + id[0] + 
													"_different_change style='padding-left: 5px; padding-right: 5px; color: red;'></span>")				
				} else {
					$("#" + id[0] + "_different_change").show();
				}
				*/	
				$("#" + id[0] + "_different_change").show();	
				$("#" + id[0] + "_different_change").text("(" + diff.toFixed(2) + ")");	
			} else {
				$("#" + id[0] + "_different_change").hide();
			}		
     }
	});

	// gnucash deposit range slider
		$('#gnucash_csv_range').noUiSlider({
		start: [ 20, 30 ],
		range: {
			'min': 10,
			'max': 40
		}
	});
	// gnucash account multi-select
	$("#gnucash_csv_accounts").chosen({
		placeholder_text_multiple: "Select Accounts",
		width: "260px"
	});
	
	$("[name='gnucash_csv_year']").chosen();

	// null or real number
	function payment_result(result) { 
		if (result == null) {
			 return 0;
		} else {
			return Number(result).toFixed(2);		
		}
	};

	// Deposit Calculator function
	function deposit_calculator() {

			var deposit = {};		
			$(".deposit input").each(function(count){ 
				deposit[count] =  this.name;
			});			
								
			$.post("json/transaction.php",{"deposit": deposit}, function(data) {
				var obj = $.parseJSON(data);
				$.each(obj,function(k,v){
					
					// Cash / Check / Credit					
					$("#" + k + "_cash span").text("$" + payment_result(v.cash));
					$("#" + k + "_check span").text("$" + payment_result(v.check));
					$("#" + k + "_credit span").text("$" + payment_result(v.credit));

					// Sum
					var sum = Number(v.check) + Number(v.cash);				
					$("#" + k + "_sum span").text("$" + sum.toFixed(2));				
					
					// Difference (regexp - good up to 6 places and 2 digits)					
					var deposit_amount = $('input[name="' + k + '"]').parent().prev().prev().text().replace(/\$(\d*(?:,\d{3})*\.\d*)\s+/, "$1" );
					deposit_amount = deposit_amount.replace(/,/, "");										
					if (deposit_amount != 0) {					
						var diff = deposit_amount - sum;
						$("#" + k + "_difference span").text("$"+ diff.toFixed(2));
						if ( diff == 0 ) {
							$("#" + k + "_difference").css("color","green");
						} else {
							$("#" + k + "_difference").css("color","red");
						}					
					} else {
						$("#" + k + "_difference span").text("n/a");
					}
				});
			
			});			
	
	} // Deposit Calculator


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
		$("#check_number").attr("tabindex",9);
		$("select[name='sold_to']").attr("tabindex",10);
		$("select[name='sold_by']").attr("tabindex",11);
		$("input[value='Save']").attr("tabindex",12);
		$("input[value='Close']").attr("tabindex",13);		
		
		// require that values be filled in a particular fashion
		$("#date").mask("0000-00-00", {placeholder: "yyyy-mm-dd" });
		$("#amount").mask("#0.00", {reverse: true, placeholder: "000.00"});
		$("#check_number").mask("#0", {reverse: true, placeholder: "check number"});	
	
		$transaction_id = $("input[name='transaction_id']").val();
		//var check_number = $("#check_number").on("input");
	
		// Anonymous Transaction?
		if ($("#anonymous").prop("checked")) { // on reload
			$("select[name='sold_to']").hide();
		} else {
			$("select[name='sold_to']").show();
		} 
		$("#anonymous").click(function() { // on click
			if ($(this).prop("checked")) { 
				$("select[name='sold_to']").hide();
				$.post("json/transaction.php",{ anonymous: 1, transaction_id: $transaction_id } );
			} else {
				$("select[name='sold_to']").show();
				$.post("json/transaction.php",{ anonymous: 0, transaction_id: $transaction_id } );
			}
		});
		
		// what type of payment? cash, credit or check?
		$("input[name='payment_type']").click(function() { 
			if ($(this).prop("checked")) { 
				$.post("json/transaction.php",{ payment_type: this.value, transaction_id: $transaction_id } );
		
				// check number?				
				if (this.value == "check") {					
					if ($("#check_number").length == 0) {
						$("#check").after('&nbsp;&nbsp;<input type="text" id="check_number" size="10" name="check_number" >');
						$("#check_number").attr("tabindex",9);
					}	else {
						$("#check_number").show();	
					}
					
					// return check #					
					$.post("json/transaction.php",{ check_number: true, transaction_id: $transaction_id }, function(data) {
						var obj = $.parseJSON(data);			
	   				if (obj.check_number) {
							$("#check_number").val(obj.check_number);		
	   				}				
	    			});				
				} else{
					$("#check_number").hide();				
				}
				
			}
		}); // what type of payment?
	
		
		/* When the transaction is storage based, only show price and payment_type 
		   when a full date (yyyy-mm-dd) is entered. */
		if ( $("#date_startstorage").length ) {
			
			// require that values be filled in a particular fashion
			$("#date_startstorage").mask("0000-00-00", {placeholder: "yyyy-mm-dd" });

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
					$("#amount").val("");					
					$("#price").hide();
			
					$('input[type=radio]').prop('checked',false).trigger('updateState');
					$("#payment_type").hide();					
					
					// reset payment_type && amount	
					$.post("json/transaction.php",{storage_payment_reset: 1, transaction_id: $transaction_id});										
						
				}
			});
	
			// If storage date is NULL, update to 0000-00-00 on save
			$("#save_transaction").click(function() {
				if ( !$("#date").val().length ) {
					$("#date").val("0000-00-00");				
				}
				
			});	
	
		} // end testing for storage presentation		   
			
			
	} // editing a transaction

});