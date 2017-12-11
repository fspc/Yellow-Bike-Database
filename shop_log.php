<?php
// new logic
require_once('Connections/YBDB.php');
require_once('Connections/database_functions.php'); 

if($_GET['shop_id']>0){
	$shop_id = $_GET['shop_id'];
} else {
	$shop_id = current_shop_by_ip();
	if (isset($shop_id)) {
		//$shop_id stays the same
	} else {
		$gotopage = PAGE_START_SHOP . "?error=no_shop"; 
		header(sprintf("Location: %s",$gotopage ));
	}
}
	
if($_GET['visit_id']>0){
	$visit_id = $_GET['visit_id'];
} else {
	$visit_id =-1;
}
	
if($_GET['new_user_id']>0){
	$new_user_id = $_GET['new_user_id'];
} else {
	$new_user_id = -1;
}


?>

<html>
	<head>
		<script src="js/jquery-2.1.1.js"></script>
		<script src="js/etherpad.js"></script>	
		<script type="text/javascript" >

			$(function() {
				
				$.post("json/contact.php", {global_pad: 1}, function(data) {
					var obj = $.parseJSON(data);
					var pad_name;					
					if (obj.configurations.prefix) {
						pad_name = obj.configurations.prefix + "_global_pad";
					} else {
						pad_name = "global_pad";
					}
					if ( obj.configurations.host && $("#epframeshop_log_pad").length !== 1 ) {
						$("#shop_log_pad").pad({
							"padId": pad_name, 
							"host": obj.configurations.host, 
							"showControls": true,
							"height": obj.configurations.height,
							"userName": obj.configurations.userName,
							"noColors": obj.configurations.noColors,
							"plugins" : obj.configurations.plugins
						});				
					}
				});				
					
				// does this work?	
		    	$("#shop_log_iframe").contents().find("#sign_in_button").on("click keypress", function(e){

			     	var body_margin = $("#shop_log_iframe").contents().find("body").css("margin");
			     	body_margin = body_margin.replace("px","");
			     	body_margin = body_margin * 4;
					var shop_log_height = $("#shop_log_iframe").contents().find("#shop_height").height() +
			      $("#shop_log_iframe").contents().find("#header_height").height() + body_margin;	        
    
		    		$("#shop_log_iframe").css({"height": shop_log_height}); 
		    		
		    	});      	
		    					 
 			}); // end $(function()
 
			$( window ).on( "load", function() {
	        
	        	if ( $("#epframeshop_log_pad").length ) {
	        		
	        	
			     	var body_margin = $("#shop_log_iframe").contents().find("body").css("margin");
			     	body_margin = body_margin.replace("px","");
			     	body_margin = body_margin * 4;
					var shop_log_height = $("#shop_log_iframe").contents().find("#shop_height").height() +
			      $("#shop_log_iframe").contents().find("#header_height").height() + body_margin;	        
			    
			    	$("#shop_log_iframe").css({"height": shop_log_height});  
			    	
			    	$("#shop_log_iframe").contents().find("#shop_log_link").attr("href","/shop_log_iframe.php"); 
			    	
			    	// does this work?
			    	$("#shop_log_iframe").contents().find("#sign_in_button").on("click keypress", function(e){
	
				     	var body_margin = $("#shop_log_iframe").contents().find("body").css("margin");
				     	body_margin = body_margin.replace("px","");
				     	body_margin = body_margin * 4;
						var shop_log_height = $("#shop_log_iframe").contents().find("#shop_height").height() +
				      $("#shop_log_iframe").contents().find("#header_height").height() + body_margin;	        
	    
			    		$("#shop_log_iframe").css({"height": shop_log_height}); 
			    		
			    	});  
				
				}	// if etherpad      
	      
	    	}); // end $( window ).on
	       
	    
		</script>
	</head>
	<body style="width: 100%; height: 100%; margin: 0; padding: 0;">
		<div>		
		<div>
		<iframe style="width: 100%; height: 100%; border: none;" name="shop_log_iframe" id="shop_log_iframe" 
					src="./shop_log_iframe.php?shop_id=<?php echo $shop_id; ?>&visit_id=<?php echo $visit_id; ?>&new_user_id=<?php echo $new_user_id; ?>">
		</iframe>
	 	</div>		
		<div id="shop_log_pad"></div>
		</div>			
		<script>	
			

								
		</script>	
	</body>
</html>
