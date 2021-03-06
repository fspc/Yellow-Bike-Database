/* jQuery fun with transactions - Jonathan Rosenbaum <gnuser@gmail.com> */

// currently some css is just hardwired, but that reflects the coding style of YBDB :)

$(function() {
    
	"use strict";
    
	$.ajaxSetup({async:false}); // best to do this in $.ajax, 
										 // but all ajax needs to be synchronous in this program because of the use of an "ancient" mysql

	$("[name='transaction_type']").attr("tabindex",1);
	$("[name='transaction_type']").focus().scrollTop();
	$("input[value='Create Transaction']").attr("tabindex",2);
	$("#trans_date").mask("0000-00-00", {placeholder: "yyyy-mm-dd" });
	$("[name='contact_id']").attr("tabindex",3);

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
 

	// contact search on main page
	$("select[name='contact_id_search']").chosen();
	
	// Volunteer Information on mouseover of contact
	var volunteer_ids;
	var last_index = $("td a[href*='individual_history']").length;
	if (last_index) {
		last_index = last_index - 1;
		$.each($("td a[href*='individual_history']"), function(index) {  			
			
			if (this.href.match(/\d*$/)) {

				var id = this.href.match(/\d+$/);

				if (last_index < 1) {
					volunteer_ids = "contacts.contact_id=" + id;
				}
				else if (!volunteer_ids) {
					volunteer_ids = "(contacts.contact_id=" + id + " OR ";
				} else if ( index === last_index) {
					volunteer_ids += "contacts.contact_id=" + id + ")";
				} else {
					volunteer_ids += "contacts.contact_id=" + id + " OR ";
				}		
			}
		   
		});
		
	}
	
	// For performance
	if (last_index <= 200) {
		volunteer_status(volunteer_ids);
	}
		
	// volunteer status
	function volunteer_status(contacts) {

		var d = new Date();
		var all_members_obj; //reuse this object
		var year = d.getFullYear();
			
		if (contacts) {  

			$.post("json/transaction.php", { volunteer_benefits: 1, contact_id: contacts }, function (data) { 								

				all_members_obj = $.parseJSON(data);
				
			  	$.each(all_members_obj, function() {
			  																				
					var bikes_earned = 0;
					var volunteer_hours_redeemed = 0;								
					var obj = this;
				
					var volunteer = "", remaining = 0, vhr = "", max_bikes_earned = 0;
					if (obj.volunteer) {
						volunteer = $.parseJSON(obj.volunteer);
						if (volunteer.hasOwnProperty(year)) {
							remaining = obj.current_year_volunteer_hours - volunteer[year].volunteer_hours_redeemed;
							vhr = volunteer[year].volunteer_hours_redeemed;
							max_bikes_earned = volunteer[year].max_bike_earned;
						}
					} else {
						vhr = 0;
					}			
					
					var title = obj.normal_full_name + "\r\n" +
											"Volunteer Hours for last 365 days: " + obj.volunteer_hours + "\r\n" +
											"Volunteer Hours \(" + year + "\): " + obj.current_year_volunteer_hours + "\r\n" +
											"Volunteer Hours Redeemed: " +  vhr + "\r\n" +
											"Volunteer Hours Remaining: " + remaining + "\r\n" +
											"Max Bikes Earned: " + max_bikes_earned;		
					
					var volunteer_with_redeemed_hours_at_zero = obj.current_year_volunteer_hours - vhr;				

					if (obj.contact_id) {
											//#d8c62757
						if (obj.volunteer_hours && obj.volunteer_hours !== '0') {
							
							if (volunteer_with_redeemed_hours_at_zero !== 0) {								
								$("td a[href='individual_history_log.php?contact_id=" + obj.contact_id + "']").
									parent().css({backgroundColor: "#19a0cc2b", cursor: "cell"}).
									prop("title",title);
								
								$("td a[href='individual_history_log.php?contact_id=" + obj.contact_id + "']").css({color: "#1b691e", textDecoration: "none", cursor: "crosshair"});
								$("td a[href='individual_history_log.php?contact_id=" + obj.contact_id + "']").hover( function(e){ $(this).css("color",e.type === "mouseenter"?"blue":"#1b691e");});									
									
							} else {
								$("td a[href='individual_history_log.php?contact_id=" + obj.contact_id + "']").
									parent().css({backgroundColor: "#d8c62757", cursor: "cell"}).
									prop("title",title);		
									
								$("td a[href='individual_history_log.php?contact_id=" + obj.contact_id + "']").css({color: "#1b691e", textDecoration: "none", cursor: "crosshair"});
								$("td a[href='individual_history_log.php?contact_id=" + obj.contact_id + "']").hover( function(e){ $(this).css("color",e.type === "mouseenter"?"blue":"#1b691e");});													
							}
	
						} else {
							
							title = obj.normal_full_name + "\r\n" +
											"Volunteer Hours for last 365 days: None" + "\r\n";
								
							$("td a[href='individual_history_log.php?contact_id=" + obj.contact_id + "']").
								parent().css({backgroundColor: "#bec7cc91", textAlign: "center", cursor: "cell"}).
								prop("title",title);
								
								$("td a[href='individual_history_log.php?contact_id=" + obj.contact_id + "']").css({color: "#1b691e", textDecoration: "none", cursor: "crosshair"});
								$("td a[href='individual_history_log.php?contact_id=" + obj.contact_id + "']").hover( function(e){ $(this).css("color",e.type === "mouseenter"?"blue":"#1b691e");});	
								
						}
										
					} else {										
							
						var name =  $("#" + id + " td a[href*='individual']").text();
						var name_obj = name.trim().split(", ");
						name = name_obj[1] + " " + name_obj[0];
	
						title = name + "\r\n" +
										"Volunteer Hours for last 365 days: None" + "\r\n";
							
						$("td a[href='individual_history_log.php?contact_id=" + obj.contact_id + "']").
							parent().css({backgroundColor: "rgb(190, 199, 204)", textAlign: "center", cursor: "cell"}).
							prop("title",title);	
							
								$("td a[href='individual_history_log.php?contact_id=" + obj.contact_id + "']").css({color: "#1b691e", textDecoration: "none", cursor: "crosshair"});
								$("td a[href='individual_history_log.php?contact_id=" + obj.contact_id + "']").hover( function(e){ $(this).css("color",e.type === "mouseenter"?"blue":"#1b691e");});													
		
					}	
					
				}); // each all_members_obj
			}); // post volunteer benefits					
		} // if this is a volunteer

		// not a current volunteer within the last 365 days, or never has been a volunteer					
		$("td a[href*='individual_history']").parent(":not([title])").children().each( function() { 

			var name =  $(this).text();
			var name_obj = name.trim().split(", ");
			name = name_obj[1] + " " + name_obj[0];
			
			var id = this.href.split("=")[1];
	
			var title = name + "\r\n" +
							"Volunteer Hours for last 365 days: None" + "\r\n";
							
			if (id) {						
			$("td a[href='individual_history_log.php?contact_id=" + id + "']").
				parent().css({cursor: "cell"}).
				prop("title",title);	
				
				$("td a[href='individual_history_log.php?contact_id=" + id + "']").css({color: "#1b691e", textDecoration: "none", cursor: "crosshair"});
				$("td a[href='individual_history_log.php?contact_id=" + id + "']").hover( function(e){ $(this).css("color",e.type === "mouseenter"?"blue":"#1b691e");});	
			}

		}); // .each not a current volunteer		
		
	} // end function volunteer_status	
	

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

		// If no min is found this makes it the same as max
		// only a problem for non transaction_log pages using transaction.js if there is only one deposit for the last year
		// not sure what will happen with 0 deposits for the last year
		if ( !range_obj["min"] ) {
			range_obj["min"] = range_obj["max"];		
		}

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
			
				if (data) {	
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
							var diff = deposit_amount - sum.toFixed(2);
							$("#" + k + "_difference span").text("$"+ diff.toFixed(2));
							if ( diff == 0 ) {
								$("#" + k + "_difference").css("color","green");
							} else {
								$("#" + k + "_difference").css("color","red");
							}					
						} else {
							$("#" + k + "_difference span").text("n/a");
						}
					
							
					}); // each					
				} // if data
				
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
	
	// invoke when volunteer hours run out for a member with a qualifying transaction
	function volunteer_hours_to_membership_discount(price, membership_obj) {
				
		var discount = (price * (membership_obj.membership_discount / 100).toFixed(2)).toFixed(2);
		var discount_price = (price - discount).toFixed(2);		
		if ( $("#transaction_type").val() !== "Stand Time" ) {
			$("#membership_discount").text("Member pays $" + discount_price).show();
			$("#membership_discount_price").text(discount_price);
			$("#redeemable_hours").spinner("disable");
			//amount.prop("disabled","disabled");
		} else {
			$("#membership_discount").empty();
			$("#membership_discount_price").empty();
		}					
									
	} // end function volunteer_hours_to_membership_discount	
	
	// volunteer hours magic
	function redeemable(obj, spinner_value, event, volunteer) {
		
		//var sweat_equity_hours = obj.sweat_equity_limit / (obj.volunteer_hours_redeemed * obj.volunteer_hour_value);							
		
		var redeemable_value;									
		if ($("#transaction_type").val() !== "Stand Time") {									
			redeemable_value = obj.volunteer_hour_value * spinner_value;
		} else {
			redeemable_value = obj.stand_time_value * spinner_value
		}
	
		// check box to use 25% or 50% ?  Also, check for 50% when no sweat_equity.
		var discount;
		if (obj.volunteer_hours >= obj.special_volunteer_hours_qualification) {
			// qualify for special volunteer discount
			discount = obj.special_volunteer_discount;
		}  else {
			// quality for normal volunteer discount
			discount = obj.volunteer_discount;
		} 

		if ($("#transaction_type").val() === "Stand Time") {
			if (obj.redeem_one_to_one === true) {
				discount = 100;
			}
		}

		// figure out remaining hours that can be redeemed if some, but not all, volunteer hours have been redeemed.
		var remaining, year = d.getFullYear(), exceeded_sweat_equity_limit = false, price_after_redeeming, spinner_difference;
		if (volunteer) {
			
			// will need to figure out these values when there isn't a property for the new year
			if (volunteer.hasOwnProperty(year)) {
				var vhr = obj.volunteer_hour_value * volunteer[year].volunteer_hours_redeemed + (spinner_value * obj.volunteer_hour_value);
				//console.log(obj.volunteer_hour_value + " * " +  volunteer[year].volunteer_hours_redeemed + " = " + vhr );
				remaining = obj.current_year_volunteer_hours - volunteer[year].volunteer_hours_redeemed;	
				//console.log(obj.current_year_volunteer_hours + " - " +  volunteer[year].volunteer_hours_redeemed + " = " + remaining );	
				if (vhr > obj.sweat_equity_limit) {
					exceeded_sweat_equity_limit = true;
					spinner_difference = (obj.sweat_equity_limit / obj.volunteer_hour_value) - volunteer[year].volunteer_hours_redeemed;
					price_after_redeeming =  price - (obj.volunteer_hour_value * spinner_difference);
					//console.log(spinner_difference + " " + price_after_redeeming);
				}
			}
		}	

		// no volunteer_hours_redeemed or still less than the allowable sweat_equity_hours
		
		// if running volunteer_hours >= special_volunteer_hours_qualification the special_discount kicks in
		// other wise it is 25%	
		
		// only 1 bike per year earned with sweat_equity_hours		
			
		if (price >= redeemable_value) {
			
			// discount is now applied if transaction is over special_volunteer_hours_qualification
			if (redeemable_value > obj.sweat_equity_limit && $("#transaction_type").val() !== "Stand Time" && !price_after_redeeming) {	
				
				var value_to_apply_discount, difference, hours_applied_with_value;

				var max_discount_price;
 				if ($("#transaction_type").val() !== "Stand Time") {	
					max_discount_price = obj.current_year_volunteer_hours * obj.volunteer_hour_value;
				} else {
					max_discount_price = obj.stand_time_value * obj.volunteer_hour_value;
				}						

				var max_discount_price_difference;
				
				if (price > max_discount_price) {
					max_discount_price_difference = price - max_discount_price;
					//console.log("we have to do things differently " + max_discount_price_difference);
					value_to_apply_discount = (price - max_discount_price_difference) - redeemable_value;
					difference = (price - max_discount_price_difference) - obj.sweat_equity_limit;
					hours_applied_with_value = difference - value_to_apply_discount;
					
					//console.log("(" + max_discount_price_difference + " +  " + difference + ") - (" + hours_applied_with_value + " * ." + discount + ")");
					redeemable_value = (max_discount_price_difference + difference) - 
											 (hours_applied_with_value * (discount / 100).toFixed(2));											
				}	else { 
					difference = price - obj.sweat_equity_limit;
					value_to_apply_discount = price - redeemable_value;
					hours_applied_with_value = difference - value_to_apply_discount;
					
					//console.log(difference + " - (" + hours_applied_with_value + " * ." + discount + ")");
					redeemable_value = difference - (hours_applied_with_value * (discount / 100).toFixed(2));				
				}
						
				amount.val(redeemable_value);
		
			// volunteer hours redeemed if the redeemable_value <= obj.sweat_equity_limit
			} else {
				if (exceeded_sweat_equity_limit === true) {
					if (price_after_redeeming && price_after_redeeming && spinner_difference > 0) {
						amount.val(price_after_redeeming - (((spinner_value - spinner_difference) * obj.volunteer_hour_value)  * (discount / 100).toFixed(2)));
					} else {						
						amount.val(price - (redeemable_value * (discount / 100).toFixed(2)));
					}
					//console.log(price_after_redeeming + " - " + redeemable_value + " * ." + discount);
				} else {
					// Never redeemed before
					amount.val(price - redeemable_value);
				}	
			}

		} else if (redeemable_value > price) {
			event.preventDefault();
		}
		
		
	} // end function redeemable 

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
		$("#redeemable_hours").attr("tabindex",11);
		$("select[name='sold_by']").attr("tabindex",12);
		$("input[value='Save']").attr("tabindex",13);
		$("input[value='Close']").attr("tabindex",14);		
		
		// common ids
		var transaction_id = $("input[name='transaction_id']").val();
		var sold_to = $("[name='sold_to']");
		var sold_by = $("[name='sold_by']");
		//var payment_type = $("input[name='payment_type']");
		var amount = $("#amount");
		var description = $("#description");
		description.autogrow({flickering: false});
		description.autogrow({flickering: false});
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
		if ( $("#transaction_type").val() === "Stand Time" ) {
			$("#paid_label").text("Amount Owed:");
		}


		// do not allow a prior saved transaction to be deleted
		$.post("json/transaction.php",{ history_select: 1, transaction_id: transaction_id }, function(data) {	
			if (data !== "First Transaction") {				
				$("#delete_transaction").hide(); 
			}
		});

		// show original price for transaction using volunteer / membership benefits
		$.post("json/transaction.php", { transaction_benefits: 1 }, function (data) {
			
			var obj = $.parseJSON(data);
			$("#original_price").empty();
					
			// Volunteer benefits
			if ( obj.transactions_with_volunteer_benefits[$("#transaction_type").val()] === true ) {
				$.post("json/transaction.php",{ history_select: 1, transaction_id: transaction_id }, function(data) {	
					if (data !== "First Transaction") {				
						var obj = $.parseJSON(data);
						var history = obj[obj.length - 1];
						
						// Check if individual has redeemed hours
						if (history.redeemed_hours !== "0.00") {
							$("#original_price").text(history.original_price).show();
							$("#volunteer_hours").text(history.redeemed_hours).show();
						}
					}
				});			
				
			} // end Volunteer benefits
			
			// Membership benefits
			if ( obj.transactions_with_membership_benefits[$("#transaction_type").val()] === true ) {
				$.post("json/transaction.php",{ history_select: 1, transaction_id: transaction_id }, function(data) {	
					if (data !== "First Transaction") {	
						var obj = $.parseJSON(data);
						var history = obj[obj.length - 1];
						
						//console.log("I am Here" + history.original_price);	
						
						// Check if individual has redeemed hours
						if (history.redeemed_hours === "0.00") { 
							$("#original_price").text(history.original_price).show();
						 } 
					}
				});			
				
			} // end Membership benefits					
			
		}); // show original price

		// Things to do before pressing the save / close button
		
		//  Membership and Volunteer Benefits
		var d = new Date();
		
		var price;
		amount.on("input",function () { 

			if ( $(this).cleanVal() >= 100 ) {
				price = ($(this).cleanVal() / 100).toFixed(2);
			} else if ($(this).cleanVal().match(/^[0]{1}[0-9]{1}/)) {
				price = ($(this).cleanVal() / 100).toFixed(2);
			} else {
				price = $(this).cleanVal();
			}
			//console.log("original " + price);
			$("#original_price").text(price);
			$("#redeemable_hours").val(0);		
		});	


		// membership transaction?
		var membership_transaction = false, membership_transaction_obj;
		$.post("json/transaction.php", { transaction_benefits: 1 }, function (data) {
	   	membership_transaction_obj = $.parseJSON(data);
	   	if ( membership_transaction_obj.transactions_with_membership_benefits[$("#transaction_type").val()] === true ) {
				membership_transaction = true;
			}
		}); // membership transaction?

		sold_to.change(function() { 					
			
			//sold_to.hide();
			amount.prop("disabled","");
			var membership_obj; //reuse this object
			
			if (this.value !== "no_selection") {
				var expiration_date;										
				
				// Is this a paid member?	
				// Determine membership benefits of current transaction
				var contact_id = "contact_id=" + this.value;
				$.post("json/transaction.php", { membership_benefits: 1, contact_id: contact_id }, function (data) { 
					
					var membership_objs = $.parseJSON(data);
					
					/*
				 	Weird hack, before improving performance #46, there was always a property for membership_obj,
					membership_discount:10, which just allowed the code to work, now it is empty when a patron 
					actually is not a paid member, so this creates that obj & property if that is the case.
					*/
					membership_obj = membership_objs[0] || { membership_discount: 10 };				

					var title = membership_obj.normal_full_name + "\r\n" +
									membership_obj.email + "\r\n" +
									membership_obj.phone + "\r\n" +
									"expiration: " + membership_obj.expiration_date;						
											
					$("#membership_discount").empty();
					$("#membership_discount_price").empty();
					amount.val("");

					//make an exception for an actual member for a Membership transaction
					if ($("#transaction_type").val() === "Memberships") {
						//amount.removeAttr("readonly");
					}
					if (membership_obj.expiration_date) {
						var exp = membership_obj.expiration_date;
						expiration_date = new Date(exp.split("-").toString());
						if (d < expiration_date && $("#transaction_type").val() === "Memberships") {	
							membership_transaction = true;
							//amount.attr("readonly", "readonly");
						}
					}
										
					
					if (membership_transaction === true) { // if membership transaction
						if (typeof membership_obj.expiration_date && membership_obj.expiration_date !== undefined) {
	
							var exp = membership_obj.expiration_date;
							expiration_date = new Date(exp.split("-").toString());	
							if (d >= expiration_date) {								
								amount.on("input", function () {					
									$("#membership_discount").empty();
									$("#membership_discount_price").empty();							
								});					
								if ($("#expired_membership").length === 1) {
									$("#expired_membership").prop("title",title).html("Expired Membership");
								} else {
									$("#paid_member").prop("id","expired_membership").prop("title",title).html("Expired Membership");				
								}
						
							// paid membership
							} else if (d < expiration_date) {
								if ($("#paid_member").length === 1) {							
									$("#paid_member").prop("title",title).html("Paid Member");
									amount.on("input", function () {				
										var discount = (price * (membership_obj.membership_discount / 100).toFixed(2)).toFixed(2);
										var discount_price = (price - discount).toFixed(2);
										////console.log("original " + price + " discount " + discount + " discounted " + discount_price);		
										if ( $("#transaction_type").val() !== "Stand Time" ) {
											$("#membership_discount").text("Member pays $" + discount_price).show();
											$("#membership_discount_price").text(discount_price);
										} else {
											$("#membership_discount").empty();
											$("#membership_discount_price").empty();
										}					
									});					
								} else {
									$("#expired_membership").prop("id","paid_member").prop("title",title).html("Paid Member");
									amount.on("input", function () {				
										var discount = (price * (membership_obj.membership_discount / 100).toFixed(2)).toFixed(2);
										var discount_price = (price - discount).toFixed(2);
										//console.log("original " + price + " discount " + discount + " discounted " + discount_price);			
										if ( $("#transaction_type").val() !== "Stand Time" ) {
											$("#membership_discount").text("Member pays $" + discount_price).show();
											$("#membership_discount_price").text(discount_price);
										} else {
											$("#membership_discount").empty();
											$("#membership_discount_price").empty();
										}								
									});												
								}
							}							
						} else {
							amount.on("input", function () {					
								$("#membership_discount").empty();
								$("#membership_discount_price").empty();							
							});
							if ($("#paid_member").length === 1) {
								$("#paid_member").empty();
							} else {
								$("#expired_membership").empty();
							}			
						}
					} // if membership transaction
				}); // end Is this a paid member

				// Stand Time	- if a paid member, nothing is owed
				if ( $("#transaction_type").val() === "Stand Time" ) {
					$.post("json/transaction.php", { stand_time: 1, contact_id: this.value, shop_id: shop_id }, function (data) {		
						$("#stand_time_total").empty();			
			
						if (data) {
							var obj = $.parseJSON(data);
							var current_membership, expiration_date;
							amount.val("");
							if (membership_obj.expiration_date) {
								var exp = membership_obj.expiration_date;
								var expiration_date = new Date(exp.split("-").toString());
								if (d >= expiration_date) {							
									current_membership = false;
								} else if (d < expiration_date) {
									current_membership = true;
								}
							} else {
								current_membership = false;
							}	
				
							// transaction has membership benefits
							if (membership_transaction) {
								amount.val("");	
								$("#stand_time_total").empty();
								$(".ui-spinner").hide();
							
								// not a member or an expired membership						
								if(current_membership === false) {	
									amount.val(obj.total + ".00"); // should improve this for amount values with digits
									price = obj.total;
									$("#stand_time_total").text(obj.hours + " hours " + obj.minutes + " minutes");
								}						
							} else {
								amount.val(data);	
								$("#stand_time_total").empty();
								$(".ui-spinner").hide();
							}
						}
					}); // stand time pos		
				}				

				// prexisting price without input, e.g. pre-filled Stand Time, needs to be taken into account	
				if ( typeof amount.val() !== 'undefined') {			
					if ( amount.cleanVal() >= 100 ) {
						price = (amount.cleanVal() / 100).toFixed(2);
					} else {
						price = amount.cleanVal();
					}
				}
				//console.log("original " + price);
				$("#original_price").text(price);	
								
				// How many hours does this volunteer have?
				$("#redeemable_hours").val("");
				var contact_id = "contacts.contact_id=" + this.value;													
				$.post("json/transaction.php", { volunteer_benefits: 1, contact_id: contact_id }, function (data) { 								
														
					var year = d.getFullYear();
					var bikes_earned = 0;
					var volunteer_hours_redeemed = 0;
					
					var volunteer_objs = $.parseJSON(data);
					
					/*
  				 	Weird hack, before improving performance #46, there was always a property for obj,
 					?, which just allowed the code to work, now it is empty when a patron 
 					has never been a volunteer, so this creates that obj & property if that is the case.
 					*/										
					var obj = volunteer_objs[0] || { volunteer: "" };												

					var volunteer = "", remaining = 0, vhr = "", max_bikes_earned = 0;
					if (obj.volunteer) {
						volunteer = $.parseJSON(obj.volunteer);
						if (volunteer.hasOwnProperty(year)) {
							remaining = obj.current_year_volunteer_hours - volunteer[year].volunteer_hours_redeemed;							
							vhr = volunteer[year].volunteer_hours_redeemed;
							max_bikes_earned = volunteer[year].max_bike_earned;
						}
					} else {
						vhr = 0;
					}					
					
					if (obj.current_year_volunteer_hours === null) {
						remaining = 0;
					}					
					
					var title = obj.normal_full_name + "\r\n" +
											obj.email + "\r\n" +
											obj.phone + "\r\n" +
											"Volunteer Hours for last 365 days: " + obj.volunteer_hours + "\r\n" +
											"Volunteer Hours \(" + year + "\): " + obj.current_year_volunteer_hours + "\r\n" +
											"Volunteer Hours Redeemed: " +  vhr + "\r\n" +
											"Volunteer Hours Remaining: " + remaining + "\r\n" +
											"Max Bikes Earned: " + max_bikes_earned;			

					$("#volunteer_hours").prop("title","").empty();	
					$("#redeemable_hours").hide();			
					
					var current_membership;
					if (membership_obj.expiration_date) {
						var exp = membership_obj.expiration_date;
						var expiration_date = new Date(exp.split("-").toString());
						if (d >= expiration_date) {							
							current_membership = false;
						} else if (d < expiration_date) {
							current_membership = true;
						}
					} else {
						current_membership = false;
					}		
				
					// if volunteer is a paid member
					if (obj.volunteer && current_membership === true) {
						// switch to membership discount when there are no remaining volunteer hours to redeem					
						if (remaining === 0) {
							amount.on("input", function () {				
								var discount = (price * (membership_obj.membership_discount / 100).toFixed(2)).toFixed(2);
								var discount_price = (price - discount).toFixed(2);
								//console.log("original " + price + " discount " + discount + " discounted " + discount_price);		
								if ( $("#transaction_type").val() !== "Stand Time" ) {
									$("#membership_discount").text("Member pays $" + discount_price).show();
									$("#membership_discount_price").text(discount_price);
								} else {
									$("#membership_discount").empty();
									$("#membership_discount_price").empty();
								}					
							});						
						// turn off membership discount if volunteer hours can be applied to transaction type
						} else {

							// find membership benefits that are unique when compared with volunteer benefits
							var mb = Object.keys(membership_transaction_obj.transactions_with_membership_benefits);
							var vb = Object.keys(membership_transaction_obj.transactions_with_volunteer_benefits);
							var diff = $(mb).not(vb).get();
							var unique = [];
							$.each( diff, function( key, value ) {
								unique[value] = true;
							});							;							
							
							// leave on discount
							if ( unique[$("#transaction_type").val()] ) {
								amount.on("input", function () {				
									var discount = (price * (membership_obj.membership_discount / 100).toFixed(2)).toFixed(2);
									var discount_price = (price - discount).toFixed(2);		
									if ( $("#transaction_type").val() !== "Stand Time" ) {
										$("#membership_discount").text("Member pays $" + discount_price).show();
										$("#membership_discount_price").text(discount_price);
									} else {
										$("#membership_discount").empty();
										$("#membership_discount_price").empty();
									}					
								});									
							
							// turn off discount	because volunteer hours can be applied						
							} else {
															
								amount.on("input", function () {					
									$("#membership_discount").empty();
									$("#membership_discount_price").empty();							
								});

							}						
						}		
												
					}				
					
					if (obj) { 
						var volunteer_hours = obj.volunteer_hours;						
							
						if ((volunteer_hours && volunteer_hours.length)) {						
							
							var max; 
							if ((remaining || remaining === 0) && (obj.volunteer &&  volunteer.hasOwnProperty(year))) {
								max = remaining;
							} else {
								max = obj.current_year_volunteer_hours;
							}																				
							
							$("#volunteer_hours").prop("title",title).html("Volunteer Hours");

							$(".ui-spinner").show(); 						
    						
							$("#redeemable_hours").spinner({
								step: 0.001,
								incremental: true,
								numberFormat: "n",
							   max: max,
							   min: 0,
							   spin: function( event, ui ) {
							   					
									// function redeemable(obj, spinner_value)
									if (max > 0) {
										redeemable(obj, ui.value, event, volunteer);
									} else {
										$(this).spinner("disable");
									}									
									
									// good place for a function to handle bug #4
							   	if (obj.volunteer && current_membership === true) {
								   	if (remaining === ui.value && remaining !== 0 && ui.value !== 0) {
											if ( typeof amount.val() !== 'undefined') {
												var price = amount.cleanVal();
												$("#volunteer_hours_to_membership_discount").text("true");			
												volunteer_hours_to_membership_discount(price, membership_obj);
											}
										}	
									}						   	
							   	
							   }
							}).on('input', function (e) {
								//var price = amount.val();
							 	if ($(this).data('onInputPrevented')) return;
							 	// test if value is greater than current_year_volunteer_hours
							 	
							 	var spinner_value;
							 	if ($(this).spinner("value") > obj.current_year_volunteer_hours) {
									spinner_value = obj.current_year_volunteer_hours;		 	
							 	} else {
							 		// in some cases this is 1 if value is greater than price like 16 * 8 = 128 > 120
							 		spinner_value = $(this).spinner("value"); 
							 		//console.log("weird " + spinner_value);
							 	}
							 	//console.log("spinner value " + spinner_value);				
						
								// function redeemable(obj, spinner_value)
								if (max > 0 || max === undefined) {
									redeemable(obj, spinner_value, event, volunteer);
								} else {
									$(this).spinner("disable");
								}
														
							   var val = this.value,
							   $this = $(this),
							   max = $this.spinner('option', 'max'),
							   min = $this.spinner('option', 'min'); 
							   // We set it to previous default value.
							   //[+-]?[\d]{0,} [+-]?([0-9]*[.])?[0-9]+ [+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)
								if (!val.match(/^[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)$/)) val = $(this).data('defaultValue');
							   this.value = val > max ? max : val < min ? min : val;
							   // set default value for spinner
							   if (!$(this).data('defaultValue')) $(this).data('defaultValue', "");
							   // To handle backspace
							   $(this).data('onInputPrevented', e.which === 8 ? true : false);
							   
							   // good place for a function to handle bug #4
							   if (obj.volunteer && current_membership === true) {
							   	if (remaining === Number(val) && remaining !== 0 && Number(val) !== 0) {
										if ( typeof amount.val() !== 'undefined') {	
											var price = amount.cleanVal();
											$("#volunteer_hours_to_membership_discount").text("true");
											volunteer_hours_to_membership_discount(price, membership_obj);
										}
									}								   
								}						   
							   
							}).show();	

						    					
    						
						} else { 
							$("#volunteer_hours").prop("title","").empty();
							$("#redeemable_hours").hide();
							$(".ui-spinner").hide();								
						}
					}	
					
					// Determine membership benefits of current transaction
					$.post("json/transaction.php", { transaction_benefits: 1 }, function (data) {
						var obj = $.parseJSON(data);
						
						// Volunteer benefits
						if ( obj.transactions_with_volunteer_benefits[$("#transaction_type").val()] === true ) {
							if ($("#redeemable_hours").data("ui-spinner")) 
								$("#redeemable_hours").spinner("enable");
						} else {
							if ($("#redeemable_hours").data("ui-spinner")) 
								$("#redeemable_hours").spinner("disable");							
						}
						
					});					
					
					// control spinner behavior of  transaction_type "Stand Time"						
					if ($("#transaction_type").val() === "Stand Time" && $("#stand_time_total").is(":empty")) {
						if ($("#redeemable_hours").data("ui-spinner")) 						
							$("#redeemable_hours").spinner("disable");
					} else if ($("#transaction_type").val() === "Stand Time") {
						if ($("#redeemable_hours").data("ui-spinner")) 
							$("#redeemable_hours").spinner("enable");
					}		
					
					// more than max_bike_limit turn off spinner	
					if ($("#transaction_type").val() === "Bicycles") { 
						if (volunteer && obj.max_bike_earned) {
							if (volunteer.hasOwnProperty(year)) {						
								if (volunteer[year].max_bike_earned >= obj.max_bike_earned) {
									$("#redeemable_hours").spinner("disable");
								}
							}
						}								
					}										
									
				}); // volunteers post
				
				
				// Free stand time use for 30 days if purchased bike recently
				if ($("#transaction_type").val() === "Stand Time") {
					$.post("json/transaction.php",{ free_stand_time_use: 1, contact_id: this.value }, function(data) {
						var obj = $.parseJSON(data);
						if (obj) {
							var most_recent_bike_purchase = obj[obj.length -1];
							//console.log(most_recent_bike_purchase);
							var now = new Date();
							var end = new Date(most_recent_bike_purchase.free_stand_time_period);
							if ( now.getTime() <= end.getTime() ) {
								//console.log("Free Stand Time is still good");
								if ($("#redeemable_hours").data("ui-spinner")) { 
									$("#redeemable_hours").spinner("disable");
								}
								amount.val("");
								if (!$("#paid_member").text()) {	
									$("#stand_time_total").text("Free Stand Time is good until " + end.toDateString());
								}				
							} else if ( now.getTime() > end.getTime() ) {
								//console.log("Free Stand Time is now over");
								
							}
						} // end Free stand time
					});
				}
			
			} // if not no_selection		
		
		}); // sold_to.change
		

		// note: it is possible to close with all error conditions being satisfied,
		//       however, it is no biggy.		
		save_or_close($("#close_transaction"), "Close"); 
		
		// Using deferred.promise .. pretty cool
		save_or_close($("#save_transaction"), "Save").done(function(success) { 

			// Save history and update volunteer benefits			
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
				
				// data structure for volunteer benefits history
				var year = d.getFullYear();
				var volunteer_benefits_history = {};
				var vhr;				
				if ($("#redeemable_hours").val() === "") {
					vhr = "0.00";
				}	else {
					vhr = parseFloat($("#redeemable_hours").val());
				}								
									
					
				// Don't require paid to be selected, only amount >= 0 
				//
				// Here is where equitable behaviour for earned bikes could be turned off/on
				// However, it actually was a feature induced bug or undesired depending how you look at it, 
				// see #78 and #80, 
				// because vhr always became 0 when amount was added if spinner was not used if the 
				// patron had become volunteer, which at the time made sense, but there
				// are exceptions to the rule, and it actually was not fair, but penalized those
				// who chose to volunteer and purchase a bike (usually for someone else) without redeeming hours
				// and then later redeemed their hours for a bike for themselves, only to find out they had already hit their
				// max eab.
				var max_bike_earned = 0, maximum_allowable_earned_bikes;
				if ($("#transaction_type").val() === "Bicycles") {
					// hours were redeemed and this is a Bicycle transaction
					if (vhr !== "0.00" && vhr !== 0) {
						max_bike_earned = 1;
					}
				}
				
				$.post("json/transaction.php",{ max_bike_earned: 1 }, function(data) {
					var obj = $.parseJSON(data);
					maximum_allowable_earned_bikes = obj.max_bike_earned;
				});				
							
				volunteer_benefits_history[year] = 	{ 
																	volunteer_hours_redeemed: vhr, 
																	max_bike_earned: max_bike_earned
																};			
				
				// Volunteer History query
				$.post("json/transaction.php",{ volunteer_history_select: 1, contact_id: sold_to }, function(data) {
					
					if (data === "First Volunteer History") {  //initialize volunteer history

						$.post("json/transaction.php",{ volunteer_history_update: 1, 
																 	contact_id: sold_to, 
																	volunteer_history: volunteer_benefits_history });					
					
					} else { // update redeemed hours
						
						volunteer_benefits_history = $.parseJSON(data);
						
						// check if new year - will have to test
						if (volunteer_benefits_history[year] === undefined) {
							
							volunteer_benefits_history[year] = 	{ 
																	volunteer_hours_redeemed: vhr, 
																	max_bike_earned: 0 
																};		
							$.post("json/transaction.php",{ volunteer_history_update: 1, 
																	 	contact_id: sold_to, 
																		volunteer_history: volunteer_benefits_history });										
						} else {
										
							if ($("#redeemable_hours").val().length) {
								
								// NaN bug #75
								if (volunteer_benefits_history[year].volunteer_hours_redeemed === "NaN")  {
									vhr = 0; 
								} else {
									vhr = parseFloat(volunteer_benefits_history[year].volunteer_hours_redeemed);
								}
								
								volunteer_benefits_history[year].volunteer_hours_redeemed = vhr + parseFloat($("#redeemable_hours").val());

								if (parseFloat(volunteer_benefits_history[year].max_bike_earned) <  maximum_allowable_earned_bikes) {																				
									volunteer_benefits_history[year].max_bike_earned = parseFloat(volunteer_benefits_history[year].max_bike_earned) + 
																										max_bike_earned;	
								}
							}
							
							
							$.post("json/transaction.php",{ volunteer_history_update: 1, 
																	 	contact_id: sold_to, 
																		volunteer_history: volunteer_benefits_history,
																		more_than_one: 1 });
						} // else same year
						
					} // update redeemed hours
					
				});
	
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
				
				var transaction_history = [], rh;
				if ($("#redeemable_hours").val() === "0" || $("#redeemable_hours").val() === "") {
					rh = "0.00";
				} else {
					rh = parseFloat($("#redeemable_hours").val());
				}

				// handle history for membership discount price
				var price, original_price;				
				if ($("#membership_discount_price").text()) {
					
					if ($("#volunteer_hours_to_membership_discount").text()) {
						price = $("#membership_discount_price").text();
						original_price = $("#original_price").text() + " (" + $("#amount").val() + ")";
						$("#amount").val(price);
					} else {
						price = $("#membership_discount_price").text();
						original_price = $("#amount").val();
						$("#amount").val(price);										
					}
					
					// update database to reflect change .. hopefully
					$.post("json/transaction.php",{discount_update: 1, transaction_id: transaction_id, price: price });
				} else {
					price = $("#amount").val();
					original_price = $("#original_price").text();
					if (original_price === "") {
						original_price = price;					
					}
				}	
				
				// Assuming local time is correct and synchronized with mysql time d.toLocaleDateString().replace(/\//g,'-')
				var current_date_time = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate() + " " + d.toLocaleTimeString().split(' ')[0];
				
				var current_transaction =
								{   			
										transaction_id: transaction_id,
										date_startstorage: $("#date_startstorage").val(),
										date: current_date_time,
										transaction_type: $("#transaction_type").val(),
										original_price: original_price,
										amount: price,
										redeemed_hours: parseFloat($("#volunteer_hours").text()) || rh,
										description: escape($("#description").val()), 
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
