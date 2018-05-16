<?php
// new logic

if($_GET['visit_id']>0){
	$visit_id = $_GET['visit_id'];
} else {
	$visit_id =-1;}

if($_GET['contact_id']>0){
	$contact_id = $_GET['contact_id'];
} else {
	$contact_id = -1;
}

include("include_header.html");

?>

<div id="contact_id" style="display:none;"><?php echo $contact_id; ?></div>
<div style="text-align:center;"><div id="name" style="align-content:center; font-size:2em;"></div><div id="membership_status"></div></div>
<div id="individual_history"></div>

<div class="stats-clear nextprev" style="text-align:center; display:block; margin-top:9px;">
<a class="stats-left stats-btn" href="#">&#10094; Previous</a>
<a class="stats-right stats-btn" href="#">Next &#10095;</a>
</div>

<?php include("include_footer.html"); ?>

<div id="individual_history_pad"></div>