<?php

require_once('Connections/database_functions.php');

?>

<?php include("include_header.html"); ?>
      <p><span class="yb_heading2">Welcome to the Positive Spin Bike Project</span></p>
      <p>Here are a few things to know about using the shop:</p>
      <ul>
        <li><span class="yb_heading3red">This is Your Community Bike Shop; it is free of charge and open to the public</span>, 
        providing a space for people to work on bikes, and learn bike mechanics skills.</li>
      </ul>
      <ul>
        <li>Positive Spin is an all-volunteer non-profit organization  
        <span class="yb_heading3red">entirely supported by volunteer time, bike, part and tool donations, 
        										the purchase of reused bikes and parts, trade-ups, memberships
        									   and financial donations</span>. </li>
      </ul>
      <ul>
        <li><span class="yb_heading3red">We expect that you volunteer time back  to the project</span> 
        equal to the time in the shop spent on personal projects to leave the project a better place than you found it. </li>
      </ul>
      <ul>
        <li>If you are unable to contribute time to the project <span class="yb_heading3red">
        we suggest  a $5 donation for personal use of the shop</span> in addition to any other donations. </li>
      </ul>
      <ul>
        <li><span class="yb_heading3red">Donations go towards</span> 
        shop tools and supplies as well as  helping PS meet our other programming needs.</li>
      </ul>
      <ul>
        <li><span class="yb_heading3red">To get started,</span> 
        just sign-in and  talk to one of the coordinators. <span class="yb_heading3red">Make sure to sign-out</span> 
        when you are done. </li>
      </ul>
      <table height="40" border="1" align="center" cellpadding="1" cellspacing="0">
      <tr align="center">
        <td width="187"><span class="style8"><a href="<?php echo PAGE_EDIT_CONTACT; ?>?contact_id=new_contact">First Time User</a></span> <br />
        <span class="yb_standardCENTERred">Fill out intial information </span></td>
        <td width="195"><span class="style8"><a href="shop_log.php">Sign In</a> to Get Started</span><br /> 
        <span class="yb_standardCENTERred">Talk to a coordinator</span></td>
        <td width="203"><span class="style8"><a href="shop_log.php">Sign Out</a> Before Leaving</span><br /> 
        <span class="yb_standardCENTERred">Workspace cleaned up?</span></td>
      </tr>
    </table>
    <ul>
        <li><span class="yb_heading3red">Exisiting user,</span> 
        may update contact information at any time while in the shop, including 
        <span class="yb_heading3red">volunteer interests.</span> 
        </li>
    </ul>
    <table height="40" border="1" align="center" cellpadding="1" cellspacing="0">
     	<tr align="center">
    		<td width="250"><span class="style8"><a href="<?php echo PAGE_SELECT_CONTACT; ?>">Existing User</a></span> <br />
        <span class="yb_standardCENTERred">Update information (e.g. Interests) </span></td>
     </tr>
     </table>   
    <p><br />
      <span class="yb_pagetitle">Learn More</span>:<br />
        <span class="yb_heading3red">PS Info:   </span>
        <a href="http://positivespin.org" target="_blank">Positive Spin Home Page</a> | 
        <a href="http://positivespin.org/home2/content/view/34/71/" target="_blank">About PS</a> | 
        <a href="http://positivespin.org/home2/content/view/94/80/" target="_blank">Shop Schedule </a> | 
        <a href="http://positivespin.org/home2/content/view/13/14/" target="_blank">Shop Services</a><span class="yb_heading3red"><br />
        Giving Back:</span> <a href="" target="_blank">Volunteering at PS</a> | 
        <a href="http://positivespin.org/home2/content/view/94/80/" target="_blank">Volunteer Shops</a> | 
        <a href="http://positivespin.org/home2/content/view/92/79/" target="_blank">Projects</a> | 
        <a href="" target="_blank">Earn-A-Bike</a> | 
        <a href="" target="_blank">Donating Online</a><br />
    </p>
    <span class="yb_pagetitle">Repairs Policy</span>:<br />
    <blockquote style="height: 0px; width: 600px;">
    <p>We are all volunteers, and<strong> </strong>during shop hours open to repairs <strong>
    we will not repair your bike for you or schedule repairs</strong>, 
    but rather we will help you do-it-yourself.&nbsp; At Positive Spin you can find work stations with tools, 
    bicycle repair stands, repair manuals, reasonably priced bicycle parts, and volunteers with varying levels of
    experience in various areas of bicycle maintenance and repair.&nbsp; Volunteers may help you diagnose, repair and 
    provide instruction about your bicycle.&nbsp; You may discover that at the Spin even highly experienced volunteers learn 
    new skills from others!&nbsp; If you have a physical issue or handicap preventing you from physically
    repairing your bicycle, we still want you to be part of the process in whatever way you are able,
    and we will gladly help facilitate the repair of your bicycle. </p>
    </blockquote>
    <?php include("include_footer.html"); ?>
