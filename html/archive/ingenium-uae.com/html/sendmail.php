<html>
<body>
<?php

  $fname = $_REQUEST['fname'] ;
  $lname = $_REQUEST['lname'] ;
  $company = $_REQUEST['company'] ;
  $address = $_REQUEST['address'] ;
  $city = $_REQUEST['city'] ;
  $state = $_REQUEST['state'] ;
  $zip = $_REQUEST['zip'] ;
  $country = $_REQUEST['country'] ;
  $phone1 = $_REQUEST['phone1'] ;
  $phone2 = $_REQUEST['phone2'] ;
  $phone3 = $_REQUEST['phone3'] ;
  $email = $_REQUEST['email'] ;
  $type = $_REQUEST['type'] ;
  $hear = $_REQUEST['hear'] ;
  $message = $_REQUEST['message'] ;
  

  if (!isset($_REQUEST['email'])) {
  ?>
<html>
<head>
<title>Ingenium Partners FZE - Error - Form Not Filled</title>
<meta http-equiv="REFRESH" content="5; URL=http://www.ingenium-uae.com/html/by_email.
html">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<meta name="contact_addr" content="PO Box 41665, Sharjah, UAE">
<meta name="Copyright" content="Ingenium Partners FZE">
<meta name="Description" content="Ingenium Partners FZE- Fastest growing UAE based 
Accounting, Finance, and Information Technology global business consultant.">
<meta name="Keywords" content="Ingenium Partners, Ingenium, Sharjah, UAE, Dubai, Dubai business, Sharjah business, business consulting, uae consulting, finance, financial consulting, accounts, accounting, accountants, uae accountants, sharjah accountants, hamriyah free zone, hfz, hfza, hfz accountants, dubai accountants, CA, Chartered Accountant, CPA, Certified Public Accountant, CA Sharjah, CA Dubai, CPA Sharjah, CPA Dubai, CPA UAE, CA UAE, InnoMakings, Inc., InnoMakings, fogbox, innodomains, web site, CFO, CIO, book keeping, web design, web hosting, site design, software, software development, start-ups, start up, Systems, systems alalysis, budget, budgets, budgeting, design, systems design, process, process costing, process analysis, analysis, shopping cart, automation, office, office automation, inventory management, information management, technology integration, copyrights, US business, American business, UK business, British business">
<meta name="Robots" content="index,follow">
<script>
<!--
function F_loadRollover(){} function F_roll(){}
//-->
</script>
<SCRIPT LANGUAGE="JavaScript1.2" SRC="../assets/rollover.js"></SCRIPT>
<link rel=stylesheet type="text/css" href="../html/style.css">
<link rel=stylesheet type="text/css" href="../html/site.css">
<style>
</style><nolayer>
<style id="NOF_STYLE_SHEET">
<!--

-->
</style>

</nolayer>
<script>
var hwndPopup_27b5;
function openpopup_27b5(url){
var popupWidth = 600;
var popupHeight = 570;
var popupTop = 101;
var popupLeft = 130;
var isFullScreen = false;
var isAutoCenter = true;
var popupTarget = "popupwin_27b5";
var popupParams = "toolbar=0, scrollbars=0, menubar=0, status=0, resizable=1";

if (isFullScreen) {
	popupParams += ", fullscreen=1";
} else if (isAutoCenter) {
	popupTop	= parseInt((window.screen.height - popupHeight)/2);
	popupLeft	= parseInt((window.screen.width - popupWidth)/2);
}

var ua = window.navigator.userAgent;
var isMac = (ua.indexOf("Mac") > -1);

//IE 5.1 PR on OSX 10.0.x does not support relative URLs in pop-ups the way they're handled below w/ document.writeln
if (isMac && url.indexOf("http") != 0) {
  url = location.href.substring(0,location.href.lastIndexOf('\/')) + "/" + url;
}

var isOpera = (ua.indexOf("Opera") > -1);
var operaVersion;
if (isOpera) {
	var i = ua.indexOf("Opera");
	operaVersion = parseFloat(ua.substring(i + 6, ua.indexOf(" ", i + 8)));
	if (operaVersion > 7.00) {
		var isAccessible = false;
		eval("try { isAccessible = ( (hwndPopup_27b5 != null) && !hwndPopup_27b5.closed ); } catch(exc) { } ");
		if (!isAccessible) {
			hwndPopup_27b5 = null;
		}
	}
}
if ( (hwndPopup_27b5 == null) || hwndPopup_27b5.closed ) {
	
	if (isOpera && (operaVersion < 7)) {
		if (url.indexOf("http") != 0) {
			hwndPopup_27b5 = window.open(url,popupTarget,popupParams + ((!isFullScreen) ? ", width=" + popupWidth +", height=" + popupHeight : ""));
			if (!isFullScreen) {
				hwndPopup_27b5.moveTo(popupLeft, popupTop);
			}
			hwndPopup_27b5.focus();
			return;
		}
	}
	if (!(window.navigator.appName == "Netscape" && !document.getElementById)) {
		//not ns4
		popupParams += ", width=" + popupWidth +", height=" + popupHeight + ", left=" + popupLeft + ", top=" + popupTop;
	} else {
		popupParams += ", left=" + popupLeft + ", top=" + popupTop;
	}
	//alert(popupParams);
	hwndPopup_27b5 = window.open("",popupTarget,popupParams);
	if (!isFullScreen) {
		hwndPopup_27b5.resizeTo(popupWidth, popupHeight);
		hwndPopup_27b5.moveTo(popupLeft, popupTop);
	}

	hwndPopup_27b5.focus();
	with (hwndPopup_27b5.document) {
		open();
		write("<ht"+"ml><he"+"ad></he"+"ad><bo"+"dy onLoad=\"window.location.href='" + url + "'\"></bo"+"dy></ht"+"ml>");
		close();
	}
} else {
	if (isOpera && (operaVersion > 7.00)) {
		eval("try { hwndPopup_27b5.focus();	hwndPopup_27b5.location.href = url; } catch(exc) { hwndPopup_27b5 = window.open(\""+ url +"\",\"" + popupTarget +"\",\""+ popupParams + ", width=" + popupWidth +", height=" + popupHeight +"\"); } ");
	} else {
		hwndPopup_27b5.focus();
		hwndPopup_27b5.location.href = url;
	}
}

}

</script>
</head>
<body NOF="(MB=(Ingeniumothers, 109, 35, 0, 0), L=(SearchLayout, 750, 225))" topmargin=0 leftmargin=0 marginwidth=0 marginheight=0>
 <body background="file:///C:/Documents and Settings/Asif Nawaz/My Documents/Ingenium Partners/Web/bg.gif" alt="Finance Consulting and Information Technology Consulting - IT Consulting">
 <table border=0 cellspacing=0 cellpadding=0 width=750 nof=ly>
  <tr valign=top align=left>
   <td height=94 colspan=5 width=750><img id="Picture2" height=94 width=750 src="../assets/images/top-banner.gif" border=0 alt="Ingenium Partners - Finance & IT Consultants - UAE based" title="Ingenium Partners - Finance & IT Consultants - UAE based"></td>
  </tr>
  <tr valign=top align=left>
   <td height=15></td>
   <td colspan=3 width=600>
    <table id="NavigationBar3" border=0 cellspacing=0 cellpadding=0 NOF=NB_UNHPNY020 width=600>
     <tr valign=top align=left>
      <td width=100><a href="../index.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton7','',0);F_roll('NavigationButton7',1)" onMouseOut="F_roll('NavigationButton7',0)"><img id="NavigationButton7" name="NavigationButton7" height=15 width=100 src="../assets/images/autogen/Home_Nbutton-regular.gif" onLoad="F_loadRollover(this,'Home_NRbutton-regular.gif',0)" border=0 alt="Home" title="Home"></a></td>
      <td width=100><a href="../html/about_ingenium.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton8','',0);F_roll('NavigationButton8',1)" onMouseOut="F_roll('NavigationButton8',0)"><img id="NavigationButton8" name="NavigationButton8" height=15 width=100 src="../assets/images/autogen/About_Ingenium_Nbutton-regular_1.gif" onLoad="F_loadRollover(this,'About_Ingenium_NRbutton-regular_1.gif',0)" border=0 alt="About Ingenium" title="About Ingenium"></a></td>
      <td width=100><a href="../html/services.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton9','',0);F_roll('NavigationButton9',1)" onMouseOut="F_roll('NavigationButton9',0)"><img id="NavigationButton9" name="NavigationButton9" height=15 width=100 src="../assets/images/autogen/Services_Nbutton-regular_1.gif" onLoad="F_loadRollover(this,'Services_NRbutton-regular_1.gif',0)" border=0 alt="Services" title="Services"></a></td>
      <td width=100><a href="../html/careers.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton10','',0);F_roll('NavigationButton10',1)" onMouseOut="F_roll('NavigationButton10',0)"><img id="NavigationButton10" name="NavigationButton10" height=15 width=100 src="../assets/images/autogen/Careers_Nbutton-regular_1.gif" onLoad="F_loadRollover(this,'Careers_NRbutton-regular_1.gif',0)" border=0 alt="Careers" title="Careers"></a></td>
      <td width=100><a href="../html/contact_us.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton11','',0);F_roll('NavigationButton11',1)" onMouseOut="F_roll('NavigationButton11',0)"><img id="NavigationButton11" name="NavigationButton11" height=15 width=100 src="../assets/images/autogen/Contact_Us_Nbutton-regular_1.gif" onLoad="F_loadRollover(this,'Contact_Us_NRbutton-regular_1.gif',0)" border=0 alt="Contact Us" title="Contact Us"></a></td>
      <td width=100 height=15><a href="../html/search.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton12','',0);F_roll('NavigationButton12',1)" onMouseOut="F_roll('NavigationButton12',0)"><img id="NavigationButton12" name="NavigationButton12" height=15 width=100 src="../assets/images/autogen/Search_Hbutton-regular.gif" onLoad="F_loadRollover(this,'Search_HRbutton-regular.gif',0)" border=0 alt="Search" title="Search"></a></td>
     </tr>
    </table>
<CENTER>
<p>
<FONT size="3" face="ARIAL" color="#3E3A96"><b> Error - Your request was NOT submitted. </b> <br>
You have attempted to submit the form without filling it.<br><br>
Please visit <a href="http://www.ingenium-uae.com/html/by_email.html"> http://www.ingenium-uae.com/html/by_email.html</a>, or wait to be redirected to fill the form in 5 seconds.<br><br>
Thank You.<br<br>
Ingenium Partners FZE Webmaster
</FONT>
</p>

</CENTER>
</td>
   <td></td>
  </tr>
  <tr valign=top align=left>
   <td width=75 height=236><img src="../assets/images/autogen/clearpixel.gif" width=75 height=1 border=0 alt=""></td>
   <td width=104><img src="../assets/images/autogen/clearpixel.gif" width=104 height=1 border=0 alt=""></td>
   <td width=394><img src="../assets/images/autogen/clearpixel.gif" width=394 height=1 border=0 alt=""></td>
   <td width=102><img src="../assets/images/autogen/clearpixel.gif" width=102 height=1 border=0 alt=""></td>
   <td width=75><img src="../assets/images/autogen/clearpixel.gif" width=75 height=1 border=0 alt=""></td>
  </tr>
  <tr valign=top align=left>
   <td colspan=2></td>
   <td width=394 class="TextObject">
    <p style="text-align: center;"><span style="font-family: Arial,Helvetica,Geneva,Sans-serif; font-size: 9px;">&#169; Copyright Ingenium Partners FZE.&nbsp;All Rights Reserved.<br></span><span style="font-family: Arial,Helvetica,Geneva,Sans-serif; font-size: 9px;">View our <a target="_self" href="javascript:openpopup_27b5('../html/ingenium_partners_-_privacy_po.html')">Privacy Policy and Terms &amp; Conditions</a>.</span></p>
   </td>
   <td colspan=2></td>
  </tr>
 </table>
</body>
</html>

<?php  

}
  elseif (empty($fname) || empty($lname) || empty($address) || empty($city) || empty($state) || empty($country) || empty($email)) {
  
  ?>
<html>
<head>
<title>Ingenium Partners FZE - Error - Incomplete Submission</title>
<meta http-equiv="REFRESH" content="5; URL=http://www.ingenium-uae.com/html/by_email.
html">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<meta name="contact_addr" content="PO Box 41665, Sharjah, UAE">
<meta name="Copyright" content="Ingenium Partners FZE">
<meta name="Description" content="Ingenium Partners FZE- Fastest growing UAE based Accouting, Finance, and Information Technology global business consultant.">
<meta name="Keywords" content="Ingenium Partners, Ingenium, Sharjah, UAE, Dubai, Dubai business, Sharjah business, business consulting, uae consulting, finance, financial consulting, accounts, accounting, accountants, uae accountants, sharjah accountants, hamriyah free zone, hfz, hfza, hfz accountants, dubai accountants, CA, Chartered Accountant, CPA, Certified Public Accountant, CA Sharjah, CA Dubai, CPA Sharjah, CPA Dubai, CPA UAE, CA UAE, InnoMakings, Inc., InnoMakings, fogbox, innodomains, web site, CFO, CIO, book keeping, web design, web hosting, site design, software, software development, start-ups, start up, Systems, systems alalysis, budget, budgets, budgeting, design, systems design, process, process costing, process analysis, analysis, shopping cart, automation, office, office automation, inventory management, information management, technology integration, copyrights, US business, American business, UK business, British business">
<meta name="Robots" content="index,follow">
<script>
<!--
function F_loadRollover(){} function F_roll(){}
//-->
</script>
<SCRIPT LANGUAGE="JavaScript1.2" SRC="../assets/rollover.js"></SCRIPT>
<link rel=stylesheet type="text/css" href="../html/style.css">
<link rel=stylesheet type="text/css" href="../html/site.css">
<style>
</style><nolayer>
<style id="NOF_STYLE_SHEET">
<!--

-->
</style>

</nolayer>
<script>
var hwndPopup_27b5;
function openpopup_27b5(url){
var popupWidth = 600;
var popupHeight = 570;
var popupTop = 101;
var popupLeft = 130;
var isFullScreen = false;
var isAutoCenter = true;
var popupTarget = "popupwin_27b5";
var popupParams = "toolbar=0, scrollbars=0, menubar=0, status=0, resizable=1";

if (isFullScreen) {
	popupParams += ", fullscreen=1";
} else if (isAutoCenter) {
	popupTop	= parseInt((window.screen.height - popupHeight)/2);
	popupLeft	= parseInt((window.screen.width - popupWidth)/2);
}

var ua = window.navigator.userAgent;
var isMac = (ua.indexOf("Mac") > -1);

//IE 5.1 PR on OSX 10.0.x does not support relative URLs in pop-ups the way they're handled below w/ document.writeln
if (isMac && url.indexOf("http") != 0) {
  url = location.href.substring(0,location.href.lastIndexOf('\/')) + "/" + url;
}

var isOpera = (ua.indexOf("Opera") > -1);
var operaVersion;
if (isOpera) {
	var i = ua.indexOf("Opera");
	operaVersion = parseFloat(ua.substring(i + 6, ua.indexOf(" ", i + 8)));
	if (operaVersion > 7.00) {
		var isAccessible = false;
		eval("try { isAccessible = ( (hwndPopup_27b5 != null) && !hwndPopup_27b5.closed ); } catch(exc) { } ");
		if (!isAccessible) {
			hwndPopup_27b5 = null;
		}
	}
}
if ( (hwndPopup_27b5 == null) || hwndPopup_27b5.closed ) {
	
	if (isOpera && (operaVersion < 7)) {
		if (url.indexOf("http") != 0) {
			hwndPopup_27b5 = window.open(url,popupTarget,popupParams + ((!isFullScreen) ? ", width=" + popupWidth +", height=" + popupHeight : ""));
			if (!isFullScreen) {
				hwndPopup_27b5.moveTo(popupLeft, popupTop);
			}
			hwndPopup_27b5.focus();
			return;
		}
	}
	if (!(window.navigator.appName == "Netscape" && !document.getElementById)) {
		//not ns4
		popupParams += ", width=" + popupWidth +", height=" + popupHeight + ", left=" + popupLeft + ", top=" + popupTop;
	} else {
		popupParams += ", left=" + popupLeft + ", top=" + popupTop;
	}
	//alert(popupParams);
	hwndPopup_27b5 = window.open("",popupTarget,popupParams);
	if (!isFullScreen) {
		hwndPopup_27b5.resizeTo(popupWidth, popupHeight);
		hwndPopup_27b5.moveTo(popupLeft, popupTop);
	}

	hwndPopup_27b5.focus();
	with (hwndPopup_27b5.document) {
		open();
		write("<ht"+"ml><he"+"ad></he"+"ad><bo"+"dy onLoad=\"window.location.href='" + url + "'\"></bo"+"dy></ht"+"ml>");
		close();
	}
} else {
	if (isOpera && (operaVersion > 7.00)) {
		eval("try { hwndPopup_27b5.focus();	hwndPopup_27b5.location.href = url; } catch(exc) { hwndPopup_27b5 = window.open(\""+ url +"\",\"" + popupTarget +"\",\""+ popupParams + ", width=" + popupWidth +", height=" + popupHeight +"\"); } ");
	} else {
		hwndPopup_27b5.focus();
		hwndPopup_27b5.location.href = url;
	}
}

}

</script>
</head>
<body NOF="(MB=(Ingeniumothers, 109, 35, 0, 0), L=(SearchLayout, 750, 225))" topmargin=0 leftmargin=0 marginwidth=0 marginheight=0>
 <body background="file:///C:/Documents and Settings/Asif Nawaz/My Documents/Ingenium Partners/Web/bg.gif" alt="Finance Consulting and Information Technology Consulting - IT Consulting">
 <table border=0 cellspacing=0 cellpadding=0 width=750 nof=ly>
  <tr valign=top align=left>
   <td height=94 colspan=5 width=750><img id="Picture2" height=94 width=750 src="../assets/images/top-banner.gif" border=0 alt="Ingenium Partners - Finance & IT Consultants - UAE based" title="Ingenium Partners - Finance & IT Consultants - UAE based"></td>
  </tr>
  <tr valign=top align=left>
   <td height=15></td>
   <td colspan=3 width=600>
    <table id="NavigationBar3" border=0 cellspacing=0 cellpadding=0 NOF=NB_UNHPNY020 width=600>
     <tr valign=top align=left>
      <td width=100><a href="../index.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton7','',0);F_roll('NavigationButton7',1)" onMouseOut="F_roll('NavigationButton7',0)"><img id="NavigationButton7" name="NavigationButton7" height=15 width=100 src="../assets/images/autogen/Home_Nbutton-regular.gif" onLoad="F_loadRollover(this,'Home_NRbutton-regular.gif',0)" border=0 alt="Home" title="Home"></a></td>
      <td width=100><a href="../html/about_ingenium.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton8','',0);F_roll('NavigationButton8',1)" onMouseOut="F_roll('NavigationButton8',0)"><img id="NavigationButton8" name="NavigationButton8" height=15 width=100 src="../assets/images/autogen/About_Ingenium_Nbutton-regular_1.gif" onLoad="F_loadRollover(this,'About_Ingenium_NRbutton-regular_1.gif',0)" border=0 alt="About Ingenium" title="About Ingenium"></a></td>
      <td width=100><a href="../html/services.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton9','',0);F_roll('NavigationButton9',1)" onMouseOut="F_roll('NavigationButton9',0)"><img id="NavigationButton9" name="NavigationButton9" height=15 width=100 src="../assets/images/autogen/Services_Nbutton-regular_1.gif" onLoad="F_loadRollover(this,'Services_NRbutton-regular_1.gif',0)" border=0 alt="Services" title="Services"></a></td>
      <td width=100><a href="../html/careers.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton10','',0);F_roll('NavigationButton10',1)" onMouseOut="F_roll('NavigationButton10',0)"><img id="NavigationButton10" name="NavigationButton10" height=15 width=100 src="../assets/images/autogen/Careers_Nbutton-regular_1.gif" onLoad="F_loadRollover(this,'Careers_NRbutton-regular_1.gif',0)" border=0 alt="Careers" title="Careers"></a></td>
      <td width=100><a href="../html/contact_us.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton11','',0);F_roll('NavigationButton11',1)" onMouseOut="F_roll('NavigationButton11',0)"><img id="NavigationButton11" name="NavigationButton11" height=15 width=100 src="../assets/images/autogen/Contact_Us_Nbutton-regular_1.gif" onLoad="F_loadRollover(this,'Contact_Us_NRbutton-regular_1.gif',0)" border=0 alt="Contact Us" title="Contact Us"></a></td>
      <td width=100 height=15><a href="../html/search.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton12','',0);F_roll('NavigationButton12',1)" onMouseOut="F_roll('NavigationButton12',0)"><img id="NavigationButton12" name="NavigationButton12" height=15 width=100 src="../assets/images/autogen/Search_Hbutton-regular.gif" onLoad="F_loadRollover(this,'Search_HRbutton-regular.gif',0)" border=0 alt="Search" title="Search"></a></td>
     </tr>
    </table>
<CENTER>
<p>
<FONT size="3" face="ARIAL" color="#3E3A96"><b> Error - Your request was NOT submitted. </b> <br>
One of the mandatory fields marked with an * was not filled.<br><br>
Please use the back button on your browser to correct the error, or wait to be redirected to re-fill the form in 5 seconds.<br><br>
Sorry for the Inconvenience.<br<br>
Ingenium Partners FZE Webmaster
</FONT>
</p>

</CENTER>
</td>
   <td></td>
  </tr>
  <tr valign=top align=left>
   <td width=75 height=236><img src="../assets/images/autogen/clearpixel.gif" width=75 height=1 border=0 alt=""></td>
   <td width=104><img src="../assets/images/autogen/clearpixel.gif" width=104 height=1 border=0 alt=""></td>
   <td width=394><img src="../assets/images/autogen/clearpixel.gif" width=394 height=1 border=0 alt=""></td>
   <td width=102><img src="../assets/images/autogen/clearpixel.gif" width=102 height=1 border=0 alt=""></td>
   <td width=75><img src="../assets/images/autogen/clearpixel.gif" width=75 height=1 border=0 alt=""></td>
  </tr>
  <tr valign=top align=left>
   <td colspan=2></td>
   <td width=394 class="TextObject">
    <p style="text-align: center;"><span style="font-family: Arial,Helvetica,Geneva,Sans-serif; font-size: 9px;">&#169; Copyright Ingenium Partners FZE.&nbsp;All Rights Reserved.<br></span><span style="font-family: Arial,Helvetica,Geneva,Sans-serif; font-size: 9px;">View our <a target="_self" href="javascript:openpopup_27b5('../html/ingenium_partners_-_privacy_po.html')">Privacy Policy and Terms &amp; Conditions</a>.</span></p>
   </td>
   <td colspan=2></td>
  </tr>
 </table>
</body>
</html>
  
  <?php
}
  else {
    mail( "anawaz@ingenium-uae.com", "Ingenium Partners Site Contact Form",
      "  From: $fname $lname \n
	  Company: $company \n
	  Address: $address \n
	  City: $city \n
	  State/Emirate/Province: $state \n
	  Zip: $zip \n
	  Country: $country \n
	  Phone: $phone1-$phone2-$phone3 \n
	  Email: $email \n
	  Type of Request: $type \n
	  Heard about Us: $hear \n
	  Feedback/Message: $message \n", "From: $fname $lname <$email>" );
	  ?>
	


<html>
<head>
<title>Ingenium Partners FZE - Request Submitted Successfully</title>
<meta http-equiv="REFRESH" content="5; URL=http://www.ingenium-uae.com/html/contact_us.html">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<meta name="contact_addr" content="PO Box 41665, Sharjah, UAE">
<meta name="Copyright" content="Ingenium Partners FZE">
<meta name="Description" content="Ingenium Partners FZE- Fastest growing UAE based Accouting, Finance, and Information Technology global business consultant.">
<meta name="Keywords" content="Ingenium Partners, Ingenium, Sharjah, UAE, Dubai, Dubai business, Sharjah business, business consulting, uae consulting, finance, financial consulting, accounts, accounting, accountants, uae accountants, sharjah accountants, hamriyah free zone, hfz, hfza, hfz accountants, dubai accountants, CA, Chartered Accountant, CPA, Certified Public Accountant, CA Sharjah, CA Dubai, CPA Sharjah, CPA Dubai, CPA UAE, CA UAE, InnoMakings, Inc., InnoMakings, fogbox, innodomains, web site, CFO, CIO, book keeping, web design, web hosting, site design, software, software development, start-ups, start up, Systems, systems alalysis, budget, budgets, budgeting, design, systems design, process, process costing, process analysis, analysis, shopping cart, automation, office, office automation, inventory management, information management, technology integration, copyrights, US business, American business, UK business, British business">
<meta name="Robots" content="index,follow">
<script>
<!--
function F_loadRollover(){} function F_roll(){}
//-->
</script>
<SCRIPT LANGUAGE="JavaScript1.2" SRC="../assets/rollover.js"></SCRIPT>
<link rel=stylesheet type="text/css" href="../html/style.css">
<link rel=stylesheet type="text/css" href="../html/site.css">
<style>
</style><nolayer>
<style id="NOF_STYLE_SHEET">
<!--

-->
</style>

</nolayer>
<script>
var hwndPopup_27b5;
function openpopup_27b5(url){
var popupWidth = 600;
var popupHeight = 570;
var popupTop = 101;
var popupLeft = 130;
var isFullScreen = false;
var isAutoCenter = true;
var popupTarget = "popupwin_27b5";
var popupParams = "toolbar=0, scrollbars=0, menubar=0, status=0, resizable=1";

if (isFullScreen) {
	popupParams += ", fullscreen=1";
} else if (isAutoCenter) {
	popupTop	= parseInt((window.screen.height - popupHeight)/2);
	popupLeft	= parseInt((window.screen.width - popupWidth)/2);
}

var ua = window.navigator.userAgent;
var isMac = (ua.indexOf("Mac") > -1);

//IE 5.1 PR on OSX 10.0.x does not support relative URLs in pop-ups the way they're handled below w/ document.writeln
if (isMac && url.indexOf("http") != 0) {
  url = location.href.substring(0,location.href.lastIndexOf('\/')) + "/" + url;
}

var isOpera = (ua.indexOf("Opera") > -1);
var operaVersion;
if (isOpera) {
	var i = ua.indexOf("Opera");
	operaVersion = parseFloat(ua.substring(i + 6, ua.indexOf(" ", i + 8)));
	if (operaVersion > 7.00) {
		var isAccessible = false;
		eval("try { isAccessible = ( (hwndPopup_27b5 != null) && !hwndPopup_27b5.closed ); } catch(exc) { } ");
		if (!isAccessible) {
			hwndPopup_27b5 = null;
		}
	}
}
if ( (hwndPopup_27b5 == null) || hwndPopup_27b5.closed ) {
	
	if (isOpera && (operaVersion < 7)) {
		if (url.indexOf("http") != 0) {
			hwndPopup_27b5 = window.open(url,popupTarget,popupParams + ((!isFullScreen) ? ", width=" + popupWidth +", height=" + popupHeight : ""));
			if (!isFullScreen) {
				hwndPopup_27b5.moveTo(popupLeft, popupTop);
			}
			hwndPopup_27b5.focus();
			return;
		}
	}
	if (!(window.navigator.appName == "Netscape" && !document.getElementById)) {
		//not ns4
		popupParams += ", width=" + popupWidth +", height=" + popupHeight + ", left=" + popupLeft + ", top=" + popupTop;
	} else {
		popupParams += ", left=" + popupLeft + ", top=" + popupTop;
	}
	//alert(popupParams);
	hwndPopup_27b5 = window.open("",popupTarget,popupParams);
	if (!isFullScreen) {
		hwndPopup_27b5.resizeTo(popupWidth, popupHeight);
		hwndPopup_27b5.moveTo(popupLeft, popupTop);
	}
	hwndPopup_27b5.focus();
	with (hwndPopup_27b5.document) {
		open();
		write("<ht"+"ml><he"+"ad></he"+"ad><bo"+"dy onLoad=\"window.location.href='" + url + "'\"></bo"+"dy></ht"+"ml>");
		close();
	}
} else {
	if (isOpera && (operaVersion > 7.00)) {
		eval("try { hwndPopup_27b5.focus();	hwndPopup_27b5.location.href = url; } catch(exc) { hwndPopup_27b5 = window.open(\""+ url +"\",\"" + popupTarget +"\",\""+ popupParams + ", width=" + popupWidth +", height=" + popupHeight +"\"); } ");
	} else {
		hwndPopup_27b5.focus();
		hwndPopup_27b5.location.href = url;
	}
}

}

</script>
</head>
<body NOF="(MB=(Ingeniumothers, 109, 35, 0, 0), L=(SearchLayout, 750, 225))" topmargin=0 leftmargin=0 marginwidth=0 marginheight=0>
 <body background="file:///C:/Documents and Settings/Asif Nawaz/My Documents/Ingenium Partners/Web/bg.gif" alt="Finance Consulting and Information Technology Consulting - IT Consulting">
 <table border=0 cellspacing=0 cellpadding=0 width=750 nof=ly>
  <tr valign=top align=left>
   <td height=94 colspan=5 width=750><img id="Picture2" height=94 width=750 src="../assets/images/top-banner.gif" border=0 alt="Ingenium Partners - Finance & IT Consultants - UAE based" title="Ingenium Partners - Finance & IT Consultants - UAE based"></td>
  </tr>
  <tr valign=top align=left>
   <td height=15></td>
   <td colspan=3 width=600>
    <table id="NavigationBar3" border=0 cellspacing=0 cellpadding=0 NOF=NB_UNHPNY020 width=600>
     <tr valign=top align=left>
      <td width=100><a href="../index.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton7','',0);F_roll('NavigationButton7',1)" onMouseOut="F_roll('NavigationButton7',0)"><img id="NavigationButton7" name="NavigationButton7" height=15 width=100 src="../assets/images/autogen/Home_Nbutton-regular.gif" onLoad="F_loadRollover(this,'Home_NRbutton-regular.gif',0)" border=0 alt="Home" title="Home"></a></td>
      <td width=100><a href="../html/about_ingenium.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton8','',0);F_roll('NavigationButton8',1)" onMouseOut="F_roll('NavigationButton8',0)"><img id="NavigationButton8" name="NavigationButton8" height=15 width=100 src="../assets/images/autogen/About_Ingenium_Nbutton-regular_1.gif" onLoad="F_loadRollover(this,'About_Ingenium_NRbutton-regular_1.gif',0)" border=0 alt="About Ingenium" title="About Ingenium"></a></td>
      <td width=100><a href="../html/services.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton9','',0);F_roll('NavigationButton9',1)" onMouseOut="F_roll('NavigationButton9',0)"><img id="NavigationButton9" name="NavigationButton9" height=15 width=100 src="../assets/images/autogen/Services_Nbutton-regular_1.gif" onLoad="F_loadRollover(this,'Services_NRbutton-regular_1.gif',0)" border=0 alt="Services" title="Services"></a></td>
      <td width=100><a href="../html/careers.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton10','',0);F_roll('NavigationButton10',1)" onMouseOut="F_roll('NavigationButton10',0)"><img id="NavigationButton10" name="NavigationButton10" height=15 width=100 src="../assets/images/autogen/Careers_Nbutton-regular_1.gif" onLoad="F_loadRollover(this,'Careers_NRbutton-regular_1.gif',0)" border=0 alt="Careers" title="Careers"></a></td>
      <td width=100><a href="../html/contact_us.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton11','',0);F_roll('NavigationButton11',1)" onMouseOut="F_roll('NavigationButton11',0)"><img id="NavigationButton11" name="NavigationButton11" height=15 width=100 src="../assets/images/autogen/Contact_Us_Nbutton-regular_1.gif" onLoad="F_loadRollover(this,'Contact_Us_NRbutton-regular_1.gif',0)" border=0 alt="Contact Us" title="Contact Us"></a></td>
      <td width=100 height=15><a href="../html/search.html" style="cursor:hand; text-decoration:none;" onMouseOver="F_loadRollover('NavigationButton12','',0);F_roll('NavigationButton12',1)" onMouseOut="F_roll('NavigationButton12',0)"><img id="NavigationButton12" name="NavigationButton12" height=15 width=100 src="../assets/images/autogen/Search_Hbutton-regular.gif" onLoad="F_loadRollover(this,'Search_HRbutton-regular.gif',0)" border=0 alt="Search" title="Search"></a></td>
     </tr>
    </table>
<CENTER>
<p>
<FONT size="3" face="ARIAL" color="#3E3A96"><b> Your Request has been submitted successfully. </b> <br>
An Associate will respond within 48 hours.<br><br>
Thank You.<br><br>
You will be automatically redirected to the Contact Us page in 5 seconds.
</FONT>
</p>

</CENTER>
</td>
   <td></td>
  </tr>
  <tr valign=top align=left>
   <td width=75 height=236><img src="../assets/images/autogen/clearpixel.gif" width=75 height=1 border=0 alt=""></td>
   <td width=104><img src="../assets/images/autogen/clearpixel.gif" width=104 height=1 border=0 alt=""></td>
   <td width=394><img src="../assets/images/autogen/clearpixel.gif" width=394 height=1 border=0 alt=""></td>
   <td width=102><img src="../assets/images/autogen/clearpixel.gif" width=102 height=1 border=0 alt=""></td>
   <td width=75><img src="../assets/images/autogen/clearpixel.gif" width=75 height=1 border=0 alt=""></td>
  </tr>
  <tr valign=top align=left>
   <td colspan=2></td>
   <td width=394 class="TextObject">
    <p style="text-align: center;"><span style="font-family: Arial,Helvetica,Geneva,Sans-serif; font-size: 9px;">&#169; Copyright Ingenium Partners FZE.&nbsp;All Rights Reserved.<br></span><span style="font-family: Arial,Helvetica,Geneva,Sans-serif; font-size: 9px;">View our <a target="_self" href="javascript:openpopup_27b5('../html/ingenium_partners_-_privacy_po.html')">Privacy Policy and Terms &amp; Conditions</a>.</span></p>
   </td>
   <td colspan=2></td>
  </tr>
 </table>
</body>
</html>
 

 
  <?php
         }
		 ?>
</body>
</html>
