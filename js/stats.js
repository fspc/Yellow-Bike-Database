$(function(){

	// Provide global defaults for date range picker on stat pages

	"use strict";
	//$.ajaxSetup({async:false})

	var range = $("#range");
	var range_input = $("#range_input");
	

	range.pickmeup({
		flat	: true,
		mode	: 'range',
		format  : 'Y/m/d',
		change: function(){
            $(this).trigger('change');
        }
	});
	
	range.change(function(){
		var range_display = range.pickmeup('get_date', true);
		range_input.text("Date Range: " + range_display[0] + " - " + range_display[1]);
    });

	$("#status_totals").on("click keypress", function(e){
		var range_display = range.pickmeup('get_date', true); 
		$.post("status_totals.php", {range1: range_display[0], range2: range_display[1]}, function (data) {
			$("body").html(data);
			//range.pickmeup.date($range_display[0]);	
		});
		e.preventDefault();
	});
	
	$("#community_service_hours").on("click keypress", function(e){
		var range_display = range.pickmeup('get_date', true); 
		$.post("community_service_hours.php", {range1: range_display[0], range2: range_display[1]}, function (data) {
			$("body").html(data);
			//range.pickmeup.date($range_display[0]);	
		});
		e.preventDefault();
	});

});