/* jQuery fun with transactions - Jonathan Rosenbaum <gnuser@gmail.com> */

// currently some css is just hardwired, but that reflects the coding style of YBDB :)

$(function() {
    
	"use strict";
    
	$.ajaxSetup({async:false}); // best to do this in $.ajax, 
										 // but all ajax needs to be synchronous in this program because of the use of mysql

	$("[name='transaction_type']").attr("tabindex",1);
	$("[name='transaction_type']").focus();
	$("input[value='Create Transaction']").attr("tabindex",2);
	$("#trans_date").mask("0000-00-00", {placeholder: "yyyy-mm-dd" });

	// Add focus for easier tab browsing
	// use .paid parent and hover & classes

	// 1.  If page has not been reloaded after a shop period ends, prevent edit from working.
   // Note: create transaction covered via a mysql_error()), but with a reload - header("Refresh:0;")
   // 2.  Don't send event signal to the save button when shop is open
	$('[href*="trans_id="]').click(function(e){  
 		var remember_me;
		$.post("json/transaction.php", {shop_exist: 1}, function(data) {
  
  			if (data == "no_shop") {
    			var start_new_shop = "Start New Shop";	
    			$("#current_shop").html("&nbsp<a href='shop_log.php'>" + start_new_shop + "</a>");
  			} else {
  				// First successful click
    			remember_me = "unbind";
  			}
		});
  		if (remember_me == "unbind") { 
  		 // Second successful click
  		 //$('[href*="trans_id="]').on('click'); 
  		 }
  		 else {  
  		 	e.preventDefault(); 
  		 }
	} );

  // Do the same thing as previously, but for editing a transaction (could make a function :)
	$('#save_transaction').on("click keypress", function(e){  
 		var remember_me;
		$.post("json/transaction.php", {shop_exist: 1}, function(data) {
  
  			if (data == "no_shop") {
    			var start_new_shop = "Start New Shop";	
    			$("#transaction_start_error").html("&nbsp<a href='shop_log.php'>" + start_new_shop + "</a>");
  			} else {
    			remember_me = "unbind";
  			}
		});
  		if (remember_me == "unbind") { 
  			$('#save_transaction').on('click keypress'); 
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
			
			$("input[name='Submit43']").on("click keypress", function(){
				$("#current_shop").html("&nbsp<a href='shop_log.php'>" + start_new_shop + "</a>");				
				event.preventDefault();		
			});
			$('[href*="trans_id="]').click(function(){
				$("#current_shop").html("&nbsp<a href='shop_log.php'>" + start_new_shop + "</a>");				
				event.preventDefault();			
			});
			$(".paid").click(function() {  
				$("#current_shop").html("&nbsp<a href='shop_log.php'>" + start_new_shop + "</a>");								
				return false; 
			});
			$('.editable_change').mouseover(function() {
				$("#current_shop").html("&nbsp<a href='shop_log.php'>" + start_new_shop + "</a>");								
				$('.editable_change').editable("disable");
			});		
		}
	} );

	// paid or not?
	$(":checked").parent("td").prev().children().not("#payment_type_label").hide();  // need to watch that not introduction bugs
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


	// transaction slider - on reload
	transaction_slider();	

	// transaction slider - on date change
	$("[name='gnucash_csv_year']").change(function() { transaction_slider(); });
	
	var slider;
	function transaction_slider() {

		// Establish range - transaction_id | deposited (yes or no) | date
		var range = [];
		var range_last_year = [];	
		$.post("json/transaction.php",{ transaction_slider: 1}, function(data) {
				
				var obj = $.parseJSON(data);
				// currently go by a Jan - Dec year as fiscal year	
				// max deposit from previous year needs to be included in range		
				var year = $("[name='gnucash_csv_year']").val();
				if (!year) {
					var d = new Date();
					year = d.getFullYear();				
				}
				var last_year = year - 1;		
		
				$.each(obj,function(k,v){
					var trans = obj[k];
					var trans_year = trans.date.split(" ",1);
					trans_year = trans_year[0].split("-",1).toString();							
					
					// find min and max for year,
					if (trans.deposited == "yes" && trans_year == year) {
						range.push(trans.transaction_id);		
					}
					// find max for last year
					if (trans.deposited == "yes" && trans_year == last_year) {
						range_last_year.push(trans.transaction_id);		
					}						
					
				});
			
		} );
		
		// If it is a new year with no new deposits we should only show last years deposits.
		// range will be undefined so it needs to be recalculated for last year.
		// list_distinct_shop_years in transaction_log.php shows all years, so show last two deposits for current year		
		
		// gnucash deposit range - min, max, and previous to max
		var min_range, max_range, max_range_last_year, prev_trans;
		if (range.length) {
			min_range = Number(range[0]);		
			max_range = Number(range[range.length - 1]);
			max_range_last_year = Number(range_last_year[range_last_year.length - 1]);
			prev_trans = Number(range[range.length - 2]);
		} else {		
			max_range = Number(range_last_year[range_last_year.length - 1]);
			prev_trans = Number(range_last_year[range_last_year.length - 2]);
			min_range = prev_trans;
		}	

		// ranges between min and max in percentages with min prepended and max appended as an object
		var range_obj = {};

		// add last deposit from last year if it exists		
		if(max_range_last_year) {
			range.unshift(max_range_last_year);
			min_range = max_range_last_year;
		}					
		var percentage_amounts = 100 / (range.length - 1);
		percentage_amounts = Number(Math.round(percentage_amounts+'e2')+'e-2');
		var percentage = percentage_amounts;

		var year_range = [];
		if (range.length) {
			year_range = range;
		} else {
			year_range = range_last_year;
		}

		$.each(year_range,function(k,v) {
			if (v == min_range) {
				range_obj["min"] = min_range;
			} else if (v == max_range) {
				range_obj["max"] = max_range;
			} else {
			 	range_obj[percentage_amounts + '%'] = Number(v);
			 	percentage_amounts = percentage_amounts + percentage;
			}
		});
		
		// watch that percentage doesn't acquire too many decimal points.
		//console.dir(range_obj);


		//initialize slider 
		if (!slider) {		
			slider = $('#gnucash_csv_range').noUiSlider({
				start: [ prev_trans, max_range ],
				range: range_obj,
				format: wNumb({decimals:0, prefix: "Transaction ID: "}),
				snap: true
			});
			slider.Link('lower').to($('#slider_lower'));	
			slider.Link('upper').to($('#slider_upper'));
		} else {  // on change	
			slider.noUiSlider({
				start: [ prev_trans, max_range ],
				range: range_obj,
				format: wNumb({decimals:0, prefix: "Transaction ID: "}),
				snap: true
			}, true);			
			slider.Link('lower').to($('#slider_lower'));	
			slider.Link('upper').to($('#slider_upper'));	
		}
		
	} // end function transaction_slider


	// make transaction slider keyboard friendly for lower and upper handle
	var slider_pointer = $('#gnucash_csv_range'); // slider does exist globally, but keeping namespaces clean
	var first_handle = $('#slider_lower');
	var second_handle = $('#slider_upper');
	slider_keyboard(slider_pointer,first_handle,"lower");
	slider_keyboard(slider_pointer,second_handle,"upper");	
	
	
	// Do not allow both handles to have the same value
	slider_pointer.on("set",function(){
		
		if( $(this).val()[0] == $(this).val()[1] ) {
			
			var Format = wNumb({decimals:0});
			var value = Format.from( $(this).val()[0] );			
			var options_object = slider_pointer.noUiSlider('options');
			
			// make an array from the range options 
			var options_range = [], c = 0;			
			$.each(options_object.range, function(k,v){
				options_range[c] = v;
				c++;
			});			
			
			// create a lookup object to map arrays to proper plus or minus value array for when both handles have the same value
			var lookup_object = {};
			lookup_object.minus = {};
			$.each(options_range, function(k,v) {
				// last array element
				if (k == options_range.length - 1) {
					lookup_object.minus[v] =  [options_range[k-1],v];				
				// first array element				
				} else if (k == 0) {
					lookup_object.minus[v] = [ options_range[k], options_range[k + 1] ];
				} else {
					lookup_object.minus[v] = [options_range[k - 1],v];				
				}
			});			
			
			$(this).val(lookup_object.minus[value]);
			
		}
		
	});  // End - Do not let both handles have the same value
	
	
	// keyboard friendly slider
	function slider_keyboard(slider_pointer, input, handle) {		
					
		input.keydown(function( e ) {

			var Format = wNumb({decimals:0});
			var options_object = slider_pointer.noUiSlider('options');
			var value;

			// Select the first handle.
			if (handle == "lower") {
				value = Format.from( slider_pointer.val()[0] );
			// Select the second handle.
			} else if (handle == "upper") {
				value = Format.from( slider_pointer.val()[1] );
			}	

			// create and array for plus (38) and minus (40) events 
			var plus_and_minus = [], c = 0;			
			$.each(options_object.range, function(k,v){
				plus_and_minus[c] = v;
				c++;
			});			
			
			// create a lookup object to map arrays to proper plus or minus value for respective event
			var lookup_object = {};
			lookup_object.plus = {};
			lookup_object.minus = {};		
			$.each(plus_and_minus, function(k,v) {
				// last array element
				if (k == plus_and_minus.length - 1) {
					lookup_object.plus[v] =  v;				
				} else {
					lookup_object.plus[v] = plus_and_minus[k + 1];				
				}
			});
			$.each(plus_and_minus, function(k,v) {
				// first array element
				if (k == 0) {
					lookup_object.minus[v] =  0;				
				} else {	
					lookup_object.minus[v] = plus_and_minus[k - 1];			
				}
			});
			

			// 13 is enter,
			// 38 is key up,
			// 40 is key down.
			switch ( e.which ) {
				case 13:
					$(this).change();
					break;
				case 38: if (handle == "lower") { // +
								slider_pointer.val( [lookup_object.plus[value] , null] ); 
							} else if (handle == "upper") {
								slider_pointer.val( [null, lookup_object.plus[value]] );							
							}
					break;
				case 40: if (handle == "lower") { // -
								slider_pointer.val( [lookup_object.minus[value], null] );
							} else if (handle == "upper") {
								slider_pointer.val( [null, lookup_object.minus[value]] );
							} 				
					break;
			}
		}); // keyboard friendly slider
	}

		
	// gnucash account multi-select
	$("#gnucash_csv_accounts").chosen({
		placeholder_text_multiple: "Select Accounts",
		width: "260px"
	});
	

	// year pull-down for transaction slider	
	$("[name='gnucash_csv_year']").chosen();


	// produce a GnuCash CSV file on submit
	gnucash_csv_submit();	
	
	function gnucash_csv_submit() {

		// grab values
		$("#gnucash_csv_submit").on("click keypress",function() {
			
			event.preventDefault();
			var Format = wNumb({decimals:0});
			
			var transaction_range = [];
			var gnucash_accounts = [];
			$.each($("#gnucash_csv_range").val(), function(k,v) { 
				transaction_range.push(Format.from(v));
			});
			gnucash_accounts = $("#gnucash_csv_accounts").val();
			
			// send values for processing individual csv files	for each selected account type
			if (gnucash_accounts) {
				$.each(gnucash_accounts, function(k,v) {
					$.post("json/transaction.php",{ gnucash_account_type: v, transaction_range: transaction_range}, function(data) {
							// download file - data is this directory/file
  							$("body").append("<iframe src='" + data + "' style='display: none;' ></iframe>"); 
					});		
				});
			}
		});			
	
	}
	
	
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
								
				
			}); // end function		
	
	} // Deposit Calculator


	// error handler for edited transactions		
	function error_handler(input,error_span,error,error_text,event) {	
		
		var trans_error = 0;
		if ( (error === "no_selection" && !input) || input == error ) {
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


	// On save (or close) check for errors	
	
	function save_or_close(button_choice, type_of_button) {				

		// Deferred Promise, since we don't know when the click will be made,
		// it is an asynchronous function, and we need to know the returned result.		
		var dfd = $.Deferred();
							
		$(button_choice).on("click keypress", function(e) {			
			
			//function error_handler(input,error_span,error,error_text,event)
			var err1 = 0, err2 = 0, err3 = 0, err4 = 0, err5 = 0, err6 = 0, err7 = 0, err8 = 0;				

			if (e.which === 13 || e.which === 1) {			
					
				// sold_to error		
				if ( !$("[name='sold_to']").is("span") ) { // Patron already performed transaction and isn't logged in
					if ( !$("#anonymous").prop("checked") ) {
						if (sold_to.length) {
							err1 = error_handler(sold_to.val(), sold_to_error, "no_selection", "*Required&nbsp;&nbsp;&nbsp;",e);
						}	
					} else if ( $("#anonymous").prop("checked") ) {
						sold_to_error.hide();
					}	
				}
				
				// sold_by error
				err2 = error_handler(sold_by.val(), sold_by_error, "no_selection", "*Required",e);
				
				// for storage transactions don't check for payment_type and payment until there is an actual date	
				var payment_type = $("input[name='payment_type']"); // payment_type variable needs to be kept within scope							
				var payment_type_result;
				if ( date.val() != "0000-00-00" && date.val() != "") {
					
					// payment type error
					if (payment_type.length) {
						payment_type.each(function(){ if ($(this).prop("checked") == true) { payment_type_result = true; } });			
						err3 = error_handler(payment_type_result, payment_type_error, undefined,"*Required",e);				
					}
					
					// payment error
					if (amount.length) {
						err4 = error_handler(amount.val(), payment_error, "","*Required",e);
					}
					
				}
				
				// description error		
				if ( $("#transaction_type").val() != "Deposit" && $("#transaction_type").val() != "Soft Drinks" ) {	// Deposit description is implicit
					err5 = error_handler(description.val(), description_error, "","*Required: a detailed description",e);
				}
				
				// check number error - error_handler()					
				var check_number = $("#check_number");	 // check number variable needs to be within this scope
				if ( check_number.is(":visible") || payment_type_result == "check" ) {
				 if (check_number.val() == undefined) {
				 	err6 = error_handler(check_number.val(), check_number_error, undefined,"*Required: enter a check number",e);
				 } else {
				 	err6 = error_handler(check_number.val(), check_number_error, "","*Required: enter a check number",e);
				 }
				} else if ( !check_number.is(":visible") ) {
					check_number_error.hide();	
				}
	
				// quantity
				err7 = error_handler(quantity.val(), quantity_error, "","*Required",e);			
				
				// date
				if (!$("#date_startstorage").length) { // not a storage transaction
					err8 = error_handler(date.val(), date_error, "","*Required",e);
				}				
				
				// Decides whether or not to post a parent error message (at the top)
				if ( ( err1 + err2 + err3 + err4 + err5 + err6 + err7) > 0) {
					if ( !transaction_error.is(":visible") ) {
					 	transaction_error.show();		
					}
					if (type_of_button === "Save") {
						transaction_error.text("Correct errors below");
					} else {
						transaction_error.text("Correct errors below, and save before closing.");
					}
				} else {
					transaction_error.hide();
					dfd.resolve("Success");	
				}			
			
			} // event type
		
		});
			
		return dfd.promise();			
			
	} // end function save_or_close

	// editing a transaction
	if ( $("input[name='shop_id']").length ) {
	
		// make tabbing more predictable
		$("input[name='shop_id']").attr("tabindex",1);
		// $("#transaction_type").attr("tabindex",2);
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
		
		// common ids
		var transaction_id = $("input[name='transaction_id']").val();
		var sold_to = $("[name='sold_to']");
		var sold_by = $("[name='sold_by']");
		//var payment_type = $("input[name='payment_type']");
		var amount = $("#amount");
		var description = $("#description");
		var quantity = $("#quantity");
		var date = $("#date");
		var shop_id = $("#shop_id").val();

		// require that values be filled in a particular fashion
		amount.mask("#0.00", {reverse: true, placeholder: "000.00"});		
		quantity.mask("#0", {placeholder: "0"});
		date.mask("0000-00-00", {placeholder: "yyyy-mm-dd" });
		$("#check_number").mask("#0", {reverse: true, placeholder: "check number"});

		// error spans & handling	
		var transaction_error = $("#transaction_start_error");	
		var date_error = $("#date_error");
		var sold_by_error = $("#sold_by_error"); 
		var sold_to_error = $("#sold_to_error"); 
		var description_error =	$("#description_error"); 
		var payment_error	= $("#payment_error"); 
		var payment_type_error = $("#payment_type_error");
		var check_number_error = $("#check_number_error");
		var quantity_error =	$("#quantity_error");
		var check_number_error = $("#check_number_error");
		//var check_number = $("#check_number").on("input");
		
		// hardwire label for Stand Time
		if ( $("#trans_type_info").text() === "Stand Time" ) {
			$("#paid_label").text("Amount Owed:");
		}

		// Things to do before pressing the save / close button
		
		//  Membership and Volunteer Benefits
		var d = new Date();	
		
		sold_to.change(function() { 
			
			if (this.value !== "no_selection") {
				
				// Is this a paid member?	
				$.post("json/transaction.php", { membership_benefits: 1, contact_id: this.value }, function (data) { 
												
					var obj = $.parseJSON(data);
					var title = obj.normal_full_name + "\r\n" +
											obj.email + "\r\n" +
											obj.phone + "\r\n" +
											"expiration: " + obj.expiration_date;
					
					if (typeof obj.expiration_date && obj.expiration_date !== undefined) {
						var exp = obj.expiration_date;
						var expiration_date = new Date(exp.split("-").toString());	
						if (d >= expiration_date) {
							if ($("#expired_membership").length === 1) {
								$("#expired_membership").prop("title",title).html("Expired Membership");
							} else {
								$("#paid_member").prop("id","expired_membership").prop("title",title).html("Expired Membership");				
							}
						} else if (d < expiration_date) {
							if ($("#paid_member").length === 1) {
								$("#paid_member").prop("title",title).html("Paid Member");
							} else {
								$("#expired_membership").prop("id","paid_member").prop("title",title).html("Paid Member");				
							}
						}							
					} else { 
							if ($("#paid_member").length === 1) {
								$("#paid_member").empty();
							} else {
								$("#expired_membership").empty();
							}			
					}
				}); // membership post
				
				// How many hours does this volunteer have?
				$.post("json/transaction.php", { volunteer_benefits: 1, contact_id: this.value }, function (data) { 
												
					var obj = $.parseJSON(data);
					var title = obj.normal_full_name + "\r\n" +
											obj.email + "\r\n" +
											obj.phone + "\r\n" +
											"Volunteer Hours for last 365 days: " + obj.volunteer_hours + "\r\n" +
											"Volunteer Hours \(" + d.getFullYear() + "\): " + obj.current_year_volunteer_hours + "\r\n" +
											"Volunteer Hours Redeemed: " + "\r\n" +
											"Volunteer Hours Remaining:";			

					$("#volunteer_hours").prop("title","").empty();					
					
					if (obj) {
						var volunteer_hours = obj.volunteer_hours;	
						if (volunteer_hours && volunteer_hours.length) {
							$("#volunteer_hours").prop("title",title).html("Volunteer Hours");
						} else { 
							$("#volunteer_hours").prop("title","").empty();
									
						}
					}													
											
				}); // volunteers post
			
				// Stand Time	
				if ( $("#trans_type_info").text() === "Stand Time" ) {
					$.post("json/transaction.php", { stand_time: 1, contact_id: this.value, shop_id: shop_id }, function (data) {
						if (data) {
							var obj = $.parseJSON(data);					
							amount.val(obj.total + ".00");
							$("#stand_time_total").text(obj.hours + " hours " + obj.minutes + " minutes");
						} else {
							amount.val(data);	
							$("#stand_time_total").empty();	
						}
					}); // stand time pos		
				}			
			
			} // if not no_selection		
		
		}); // sold_to.change
		

		// note: it is possible to close with all error conditions being satisfied,
		//       however, it is no biggy.		
		save_or_close($("#close_transaction"), "Close"); 
		
		// Using deferred.promise .. pretty cool
		save_or_close($("#save_transaction"), "Save").done(function(success) { 

			// Save history 				
			if (success === "Success") {

				transaction_id = $("input[name='transaction_id']").val();

				var date = $("#date").val();
				if (date === "") {
					date = "0000-00-00";				
				}

				// find new transaction_id if date has changed for a storage transaction
				//  this is the most recent transaction_id					
				if ($("#date_startstorage").val() && date !== "0000-00-00") {
					$.post("json/transaction.php",{ most_recent_transaction_id: 1 }, function(data) {
						transaction_id = Number(data) + 1;
					} );
				}	
	
				
				var span_or_select = $("[name='sold_to']").is("span"), sold_to;
				if (span_or_select) {
					sold_to = $("#sold_to").val();
				}	else {
					sold_to = $("[name='sold_to']").val();				
				}
	
				var payment_type_group = $("input[name='payment_type']"), payment_type, check_number; 
				if (payment_type_group.length) {
						payment_type_group.each(function(){ 
							if ($(this).prop("checked") === true) { 
								payment_type = $(this).val();
								if (payment_type === "credit" || payment_type === "cash") {
									check_number = undefined;								
								} else {
									check_number = $("#check_number").val();								
								}
							} 
						} ); 
				}	
				
				var anonymous;
				if ($("#anonymous").prop("checked")) { 
					anonymous = 1; 
				} else {
					anonymous = undefined;				
				}
	
				// store the transaction's history
				var transaction_history = [];
				var current_transaction =
								{   			
										transaction_id: transaction_id,
										date_startstorage: $("#date_startstorage").val(),
										date: date,
										transaction_type: $("#transaction_type").val(),
										amount: $("#amount").val(),
										description: $("#description").val(), 
										sold_to: sold_to,
										sold_by: $("[name='sold_by']").val(),
										quantity: $("#quantity").val(),
										shop_id: $("#shop_id").val(),
										payment_type: payment_type,
										check_number: check_number,
										anonymous: anonymous				
								};
	
				// transaction_id hasn't changed yet if it is a storage transaction
				if ($("#date_startstorage").val() && date !== "0000-00-00") {
					transaction_id = transaction_id - 1;
				}	
	
				// check for prior transactions
				$.post("json/transaction.php",{ history_select: 1, transaction_id: transaction_id }, function(data) {
			
					if (data === "First Transaction") {
						transaction_history.push(current_transaction);
						$.post("json/transaction.php",{ history_update: 1, 
																	transaction_id: transaction_id, 
																	history: transaction_history });
					
					} else { // more than 1 transaction in the history
					

						transaction_history = $.parseJSON(data);
						transaction_history.push(current_transaction);
						$.post("json/transaction.php",{ history_update: 1, 
																		transaction_id: transaction_id, 
																		history: transaction_history,
																		more_than_one: 1 });
						
					} // more than 1 transaction in the history
					
				} );	// check for prior transactions
					
	
			} // End Save History		

		
		})  // end function save_and_close
				
      // On reload if patron isn't logged in replace pull-down with patrons name 
		if (sold_to.val() == "no_selection") {
			$.post("json/transaction.php",{ not_logged_in: 1, transaction_id: transaction_id }, function(data) {
				var obj = $.parseJSON(data);
				if (obj.sold_to) {
					var obj = $.parseJSON(data);				
					sold_to.replaceWith("<span name='sold_to'>" + obj.full_name + 
																 "</span><input value='" + obj.sold_to + "' type='hidden' id='sold_to' name='sold_to'>");
				}
			} );
		}
	
		// Anonymous Transaction?
		if ($("#anonymous").prop("checked")) { // on reload
			sold_to.hide();
		} else {
			sold_to.show();
		} 
		$("#anonymous").click(function() { // on click
			if ($(this).prop("checked")) { 
				sold_to.hide();
				$.post("json/transaction.php",{ anonymous: 1, transaction_id: $("input[name='transaction_id']").val() } );
			} else {
				sold_to.show();
				$.post("json/transaction.php",{ anonymous: 0, transaction_id: $("input[name='transaction_id']").val() } );
			}
		});
		
		// what type of payment? cash, credit or check?
		$("input[name='payment_type']").click(function() { 
			if ($(this).prop("checked")) { 
				$.post("json/transaction.php",{ payment_type: this.value, transaction_id: $("input[name='transaction_id']").val() } );
		
				// check number?				
				if (this.value == "check") {					
					if ($("#check_number").length == 0) {
						$("#check").after('&nbsp;&nbsp;<input type="text" id="check_number" size="10" name="check_number" >');
						$("#check_number").attr("tabindex",9);
					}	else {
						$("#check_number").show();	
					}
					
					// return check #					
					$.post("json/transaction.php",{ check_number: true, transaction_id: $("input[name='transaction_id']").val() }, function(data) {
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
			
			var date_startstorage =	$("#date_startstorage").val();		
			
			// If storage start date has changed since original shop trans, show the original shop day. 
			$.post("json/transaction.php",{date_startstorage: date_startstorage, 
													transaction_id: $("input[name='transaction_id']").val() }, function(data) {
				if (data) {
					$("#original_shop_date").html("(transaction date: " + data + ")");
				}
			});
			
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
			
			$("#date_fill").click(function(e){ 
				var span_or_select = $("[name='sold_to']").is("span"), err0;
				if(span_or_select) {
					err0 = error_handler(span_or_select, date_error, true, "*Patron must be signed in to complete this transaction.",e);
				}
				if (err0 != 1) {
					$("#price").show();			
					$("#payment_type").show();
				}
			})			
			
			$("#date").on("input", function(e){ 

				var span_or_select = $("[name='sold_to']").is("span"), err0;
				if(span_or_select) {
					err0 = error_handler(span_or_select, date_error, true, "*Patron must be signed in to complete this transaction.",e);
				}
				
				date_test = /^\d{4}-((0\d)|(1[012]))-(([012]\d)|3[01])$/.test(this.value);
				if ( date_test && this.value != "0000-00-00" ) {
					if (err0 != 1) {						
						$("#price").show();			
						$("#payment_type").show();
					}
				} else {
					$("#amount").val("");					
					$("#price").hide();
			
					// this unchecks the payment type
					$('input[type=radio]').prop('checked',false).trigger('updateState');
					$("#payment_type").hide();					
					
					// reset payment_type && amount	
					$.post("json/transaction.php",{storage_payment_reset: 1, transaction_id: $("input[name='transaction_id']").val() });										
						
				}
			});
	
				
			// If storage date is NULL, update to 0000-00-00 on save	
			$("#save_transaction").on("click keypress", function(e) {
							
				if ( !$("#date").val().length ) {
						$("#date").val("0000-00-00");			
				}
				
				
			});	
	
		} // end testing for storage presentation		   
			
			
	} // editing a transaction

});
