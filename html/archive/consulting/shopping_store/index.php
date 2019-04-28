<?php
session_cache_limiter('none');
session_start();
ob_start();
?>
<?php include "vsadmin/db_conn_open.php" ?>
<?php include "vsadmin/includes.php" ?>
<?php include "vsadmin/inc/languagefile.php" ?>
<?php include "vsadmin/inc/incfunctions.php" ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<link href="../menu.css" rel="stylesheet" type="text/css">
<link href="store.css" rel="stylesheet" type="text/css">
<link href="store_menu.css" rel="stylesheet" type="text/css">

<!-- to fix IE CSS dropdown problems -->
<script type="text/javascript"><!--//--><![CDATA[//><!--
sfHover = function() {
	var sfEls = document.getElementById("navmain5").getElementsByTagName("LI");
	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onmouseover=function() {
			this.className+=" sfhover";
		}
		sfEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", sfHover);

//--><!]]>
</script>
<title>Asif Nawaz Consulting > Shop Online</title>
</head>

<body bgcolor="#DDDBA0" leftmargin="0" marginwidth="0" marginheight="0">
<table width="100%" cellspacing="0">
<tr>
<td colspan="3" width="100%" bgcolor="#ffffff" class="heading" align="center"> ASIF NAWAZ CONSULTING 
</td>
</tr>
<tr>
<td width="68" bgcolor="#000000" border="0">&nbsp;</td>
<td bgcolor="#000000" width="650" >
<ul id="navmain5">
<li><a href="../about/index.html">about asif N.</a>
		<ul>
			<li><a href="../about/mission.html">mission</a></li>
			<li><a href="../about/strategy.html">strategy</a></li>
			<li><a href="../about/vision.html">vision</a></li>
			<li><a href="../about/contact.html">contact information</a></li>

		</ul>
	</li>
	<li><a href="../expertise/index.html">our expertise</a>
		<ul>
			<li><a href="../expertise/contacts.html">contacts</a></li>
			<li><a href="../expertise/past_projects.php">past projects</a></li>
			<li><a href="../expertise/industries.html">industries</a></li>
			<li><a href="../expertise/functions.html">functions</a></li>

		</ul>
	</li>

	<li><a href="../publications/index.php">publications</a>
	</li>
	
	<li><a href="../current_projects/index.php">current projects</a>
	</li>
	
	<li><a href="index.php">shop online</a>
	</li>

</ul>
</td>
<td width="30%" bgcolor="#000000">&nbsp;</td>
</tr>
</table>
<table width="700" border="0" bgcolor="#FFFFFF">
	<tr>
		<td colspan="3" class="storeheader" align="center"><br />
<strong> Welcome to the Asif Nawaz Consulting Shopping Store</strong><br />
<br />
		</td>
	</tr>
	<tr>
		<td width="150" colspan="1" align="center" valign="top">
<ul class="glossymenu">
<li><a href="index.php">store home</a></li>
<li><a href="categories.php" >product catalog</a></li>
<li><a href="orderstatus.php">track your order</a></li>
<li><a href="search.php">search</a></li>
<li><a href="policy.php">shipping & return policy</a></li>
<li><a href="contact.php">contact us </a></li>
</ul>
<br />
<img src="images/cart.jpg"> &nbsp; <span class="storecart"><b> My Cart</b> <br />
<?php include "vsadmin/inc/incminicart.php" ?>
<br />
</span>
<ul class="glossymenu">
<li><a href="cart.php" > Check Out </a></li>
</ul>

		</td>
		<td width ="550" valign="top" class="storebody">

The Asif Nawaz Store makes available to you a variety of financial and information technology products and services for purchase online.<br /><br />Browse our product catalog to see what we offer.<br /><br />

<table width="450" align="center">
	<tr>
		<td width="150" class="storebody" valign="top">
<a href="products.php?cat=6"><img src="images/front/domains.jpg" Border="0" ALT = "Domain Names for Sale by Asif Nawaz Consulting" TITLE = "Domain Names for Sale by Asif Nawaz Consulting"></a><br />

Over our years as web consultants, we have put together an assortment of domains names.  We've now put up some of the hottest domain names for sale here.  Check them out today!


		</td>
		<td width="150" class="storebody" valign="top">
<a href="products.php?cat=8"><img src="images/front/litefrontspecial.jpg" border="0" ALT = "Real Estate Web Site Management Software from Asif Nawaz Consulting"  TITLE = "Real Estate Web Site Management Software from Asif Nawaz Consulting"></a><br />
<font size="+1"><center><b><u>$9.99 ONLY!</u></b></center>
</font>
A spin off of the <a href="http://www.asifnawaz.net/expertise/displayproject.php?pid=12" class="intext">Team One</a> Project, we've developed this user friendly real estate web site management system on a more robust back end and an installation friendly package.  The Lite version is now available for purchase!! 
		</td>
		<td width="150" class="storebody" valign="top">
<a href="#"><img src="images/front/finacct.jpg" border="0" AlT ="Finance & Accounting Templates & Tools from Asif Nawaz Consulting" TITLE ="Finance & Accounting Templates & Tools from Asif Nawaz Consulting"></a>
Don't want to waste time designing your own excel spreadsheet to present to management?  Want to be able to publish statements and financial formulas and reports QUICKLY and EASILY?  Look no further!
		</td>
	</tr>
	<tr>
		<td colspan="3" align="center"><br />
		<img src="images/2co.jpg" ALT="2Checkout.com is an authorized retailer for Asif Nawaz Consulting" TITLE="2Checkout.com is an authorized retailer for Asif Nawaz Consulting">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<!-- PayPal Logo --><a href="#" onClick="javascript:window.open('https://www.paypal.com/cgi-bin/webscr?cmd=xpt/cps/popup/OLCWhatIsPayPal-outside','olcwhatispaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=350');"><img  src="https://www.paypal.com/en_US/i/bnr/vertical_solution_PPeCheck.gif" border="0" alt="Pay with Paypal"></a><!-- PayPal Logo -->
		
		</td>
	</tr>
</table><br /><br />
		</td>
	</tr>
	<tr>
	<td height="1" bgcolor="#000000" colspan="3">
	</td>
	</tr>
	<tr>
		<td colspan="3" class="storefooter"><br />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="../index.html" class="intext">Home</a> | <a href="../store/index.html" class="intext">Shopping Store</a> | <a href="../about/contact.html" class="intext">Contact</a> | <a href="../sitemap.html" class="intext">Sitemap</a>
		<br />
		<br />
		</td>
	</tr>
	<tr>
		<td colspan="3" class="storefooter" align="center">
		<a href="../privacy.html" class="intext">Privacy Policy &amp; Terms of Use</a> | <a href="mailto:webmaster@asifnawaz.net" class="intext">Contact Webmaster</a>
		</td>
	</tr>
</table>
</body>
</html>