<?php include("include_header.html"); ?>

	<p class="yb_heading3red">Bookkeeping Reports</p>
	    <ul>         
          <li><a href="stats/stats_monthlysalestax.php">Sales Tax Report</a><br /></li>
          <li><a href="stats/stats_shoptransactiontotals.php">Shop Transaction Totals</a><br /></li>
          <li>Monthly Transaction Totals: <a href="stats/stats_monthlytransactiontotals.php">Volunteer Shops</a>, <a href="stats/stats_monthlytransactiontotals_paid.php">Mechanic Operation</a><br />
	    </ul>

		<p class="yb_heading3red">Membership</p>
	    <ul>
	    	<li><a href="stats/members.php" >Members (Running 12 Month Period)</a></li>
	    </ul>
	    
	    <p class="yb_heading3red">Volunteer Shops</p>
	    <ul>
	    	<li><a href="stats/status_totals.php" >Status Totals by Status and Date Range</a></li>
          <li><a href="stats/stats_userhours.php">Hours by User</a></li>
          <li><a href="stats/community_service_hours.php">Community Service Hours by Date Range</a></li>
	      <li>Volunteer Hours - <a href="stats/stats_userhours_year.php">Year Summary</a> | <a href="stats/stats_userhours_season.php">3 Month Summary</a> </li>
	      <li><a href="stats/stats_usersbyweek.php">New and Total Users by Week</a> </li>
	      <li><a href="stats/stats_usersbydayweek.php">New and Total Users by Day/Week</a></li>
	    </ul>

		<p class="yb_heading3red">All Shops</p>
	    <ul>
	    	<li><a href="stats/shops.php" >Totals by Types and Range</a></li>
	    </ul>
	    
	    <p class="yb_heading3red">Mechanic Operation Statistics</p>
	    <ul>
          <li>Staff Hours by: <a href="stats/stats_paidstaffhours_byPayPeriod.php">Pay Period</a> - <a href="stats/stats_paidstaffhours_byWeek.php">Week</a> - <a href="stats/stats_paidstaffhours_byMonth.php">Month</a></li>
          <li>Operation Metrics and Net by: <a href="stats/stats_MechanicOperationMetrics_byWeek.php">Week</a>, <a href="stats/stats_MechanicOperationMetrics_byMonth.php"> Month</a></li>
          <li><a href="stats/stats_employeemetrics.php">Employee Metrics</a></li>
          <li>Operation vs Volunteer Shop Outputs by: <a href="stats/stats_VolVsMechMetrics_byWeek.php">Week</a>, <a href="stats/stats_VolVsMechMetrics_byMonth.php">Month</a><br /></li>
	    </ul>
	    
	<?php include("include_footer.html"); ?>
