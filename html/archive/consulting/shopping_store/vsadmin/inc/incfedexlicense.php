<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$storesessionvalue=="") $storesessionvalue="virtualstore".time();
if($_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE) exit;
$success=TRUE;
$alreadygotadmin = getadminsettings();
$countryCode = $origCountryCode;
function ParseFedexOutput($sXML, $rootNodeName, &$thetext, &$errormsg){
	$noError = FALSE;
	$errormsg = "";
	$thetext="";
	$xmlDoc = new vrXMLDoc($sXML);
	if($xmlDoc->nodeList->nodeName[0] != $rootNodeName)
		$nodeList = $xmlDoc->nodeList;
	else
		$nodeList = $xmlDoc->nodeList->childNodes[0];
	for($i = 0; $i < $nodeList->length; $i++){
		// print "Node here:" . $nodeList->nodeName[$i] . "<br>";
		if($nodeList->nodeName[$i]=="Error"){
			$errormsg = "";
			$t = $nodeList->childNodes[$i];
			for($k = 0; $k < $t->length; $k++){
				if($t->nodeName[$k]=="Code"){
					$noError = ((int)$t->nodeValue[$k]==1);
				}elseif($t->nodeName[$k]=="Message"){
					$errormsg .= $t->nodeValue[$k];
				}
			}
		}elseif($nodeList->nodeName[$i]=="MeterNumber"){
			$thetext = $nodeList->nodeValue[$i];
			$noError = TRUE;
		}
	}
	return($noError);
}
if(@$_GET['act']=='version'){ ?>
	<form method="post" name="licform" action="admin.php">
	  <input type="hidden" name="upsstep" value="5" />
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr> 
          <td width="100%">
            <table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="">
			  <tr>
				<td rowspan="3" width="70" align="center" valign="top"><img src="../images/fedexsmall.gif" border="0" alt="FedEx" /><br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</td>
                <td width="100%" align="center"><strong><?php print $yyFdxWiz?> - Updating FedEx® version information.</strong><br />&nbsp;
                </td>
			  </tr>
			  <tr> 
                <td width="100%" align="left">
				  <p>&nbsp;</p>
				  <p>Please wait while we update your FedEx version information.</p>
				  <p>&nbsp;</p>
				  <p>Step 1, getting location id. <span name="step1span" id="step1span"><strong>Please wait!</strong></span></p>
				  <p>&nbsp;</p>
				  <p>Step 2, updating version. <span name="step2span" id="step2span"><strong>Please wait!</strong></span></p>
				  <p>&nbsp;</p>
				  <p align="center" name="donebutton" id="donebutton" style="display:none"><input type="submit" value="<?php print $yyDone?>" /></p>
				  <p>&nbsp;</p>
                </td>
			  </tr>
			  <tr> 
                <td colspan="2" width="100%" align="center">
				  <p><img src="../images/clearpixel.gif" width="300" height="5" alt="" /></p>
				  <p><font size="1">FedEx® is a registered service mark of Federal Express Corporation.
FedEx logos used by permission. All rights reserved.</font></p>
                </td>
			  </tr>
            </table>
          </td>
        </tr>
      </table>
	</form>
<?php
	flush();
	$sSQL = "SELECT adminVersion,FedexAccountNo,FedexMeter,adminZipCode,countryCode FROM admin INNER JOIN countries ON admin.adminCountry=countries.countryID WHERE adminID=1";
	$result = mysql_query($sSQL) or print(mysql_error());
	if($rs = mysql_fetch_array($result)){
		$version = trim($rs['adminVersion']);
		$fedexacctno = trim($rs['FedexAccountNo']);
		$fedexmeter = trim($rs['FedexMeter']);
		$zipcode = trim($rs['adminZipCode']);
		$countrycode = trim($rs['countryCode']);
	}
	$versionarray = split(' v', $version);
	$version = $versionarray[1];
	$versionarray = split('\.', $version);
	if((int)$versionarray[0]<10) $version = '0' . $versionarray[0] . $versionarray[1] . '0'; else $version = $versionarray[0] . $versionarray[1] . '0';
	$sXML = '<?xml version="1.0" encoding="UTF-8"?>';
	$sXML .= '<FDXZipInquiryRequest xmlns:api="http://www.fedex.com/fsmapi" xsi:noNamespaceSchemaLocation="FDXSubscriptionRequest.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
	$sXML .= '<RequestHeader><CustomerTransactionIdentifier>ZipRequest</CustomerTransactionIdentifier>';
	$sXML .= '<AccountNumber>' . $fedexacctno . '</AccountNumber><MeterNumber>' . $fedexmeter . '</MeterNumber>';
	$sXML .= '</RequestHeader>';
	$sXML .= '<DestinationPostalCode>' . $zipcode . '</DestinationPostalCode>';
	$sXML .= '<DestinationCountryCode>' . $countrycode . '</DestinationCountryCode>';
	$sXML .= '</FDXZipInquiryRequest>';
	$success = callcurlfunction('https://gateway.fedex.com:443/GatewayDC', $sXML, $xmlres, '', $errormsg, FALSE);
	$xmlDoc = new vrXMLDoc($xmlres);
	$nodeList = $xmlDoc->nodeList->childNodes[0];
	for($i = 0; $i < $nodeList->length; $i++){
		if($nodeList->nodeName[$i]=='DestinationLocationID'){
			$locationid = $nodeList->nodeValue[$i];
		}
	}
	print '<script language="javascript" type="text/javascript">document.getElementById(\'step1span\').innerHTML=\'<strong>Completed!</strong>\';</script>';
	flush();
	$sXML = '<?xml version="1.0" encoding="UTF-8"?>';
	$sXML .= '<FDXSSPVersionCaptureRequest xmlns:api="http://www.fedex.com/fsmapi" xsi:noNamespaceSchemaLocation="FDXSubscriptionRequest.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
	$sXML .= '<RequestHeader><CustomerTransactionIdentifier>VersionCapture</CustomerTransactionIdentifier>';
	$sXML .= '<AccountNumber>' . $fedexacctno . '</AccountNumber><MeterNumber>' . $fedexmeter . '</MeterNumber><CarrierCode></CarrierCode>';
	$sXML .= '</RequestHeader>';
	$sXML .= '<LocationID>' . $locationid . '</LocationID>';
	$sXML .= '<VendorProductID>IBTP</VendorProductID>';
	$sXML .= '<VendorProductPlatform>PHP</VendorProductPlatform>';
	$sXML .= '<VendorProductVersion>' . $version . '</VendorProductVersion>';
	$sXML .= '</FDXSSPVersionCaptureRequest>';
	$success = callcurlfunction('https://gateway.fedex.com:443/GatewayDC', $sXML, $xmlres, '', $errormsg, FALSE);
	print '<script language="javascript" type="text/javascript">document.getElementById(\'step2span\').innerHTML=\'<strong>Completed!</strong>\';document.getElementById(\'donebutton\').style.display=\'block\';</script>';
}elseif(@$_POST["upsstep"]=="3"){
	$sXML = '<?xml version="1.0" encoding="UTF-8"?>';
	$sXML .= '<FDXSubscriptionRequest xmlns:api="http://www.fedex.com/fsmapi" xsi:noNamespaceSchemaLocation="FDXSubscriptionRequest.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
	$sXML .= '<RequestHeader><CustomerTransactionIdentifier>Subscribe</CustomerTransactionIdentifier>';
	$sXML .= '<AccountNumber>' . trim(@$_POST['fedexaccount']) . '</AccountNumber>';
	$sXML .= '</RequestHeader><Contact>';
	$sXML .= '<PersonName>' . @$_POST['contact'] . '</PersonName>';
	if(trim(@$_POST['company']) != '') $sXML .= '<CompanyName>' . @$_POST['company'] . '</CompanyName>';
	if(trim(@$_POST['department']) != '') $sXML .= '<Department>' . @$_POST['department'] . '</Department>';
	$sXML .= '<PhoneNumber>' . @$_POST['telephone'] . '</PhoneNumber>';
	if(trim(@$_POST['pager']) != '') $sXML .= '<PagerNumber>' . @$_POST['pager'] . '</PagerNumber>';
	if(trim(@$_POST['fax']) != '') $sXML .= '<FaxNumber>' . @$_POST['fax'] . '</FaxNumber>';
	if(trim(@$_POST['email']) != '') $sXML .= '<E-MailAddress>' . @$_POST['email'] . '</E-MailAddress>';
	$sXML .= '</Contact><Address><Line1>' . @$_POST['address'] . '</Line1>';
	if(trim(@$_POST['address2']) != '') $sXML .= '<Line2>' . @$_POST['address2'] . '</Line2>';
	$sXML .= '<City>' . @$_POST['city'] . '</City>';
	if(trim(@$_POST['country'])=='US' || Trim(@$_POST['country'])=='CA')
		$sXML .= '<StateOrProvinceCode>' . @$_POST['usstate'] . '</StateOrProvinceCode>';
	else
		$sXML .= '<StateOrProvinceCode></StateOrProvinceCode>';
	$sXML .= '<PostalCode>' . @$_POST['postcode'] . '</PostalCode>';
	$sXML .= '<CountryCode>' . @$_POST['country'] . '</CountryCode></Address>';
	$sXML .= '<CSPSolutionType>100</CSPSolutionType><CSPIndicator>01</CSPIndicator></FDXSubscriptionRequest>';
	// print str_replace("<","<br />&lt;",str_replace("</","&lt;/",$sXML)) . "<br />\n";
	$success = callcurlfunction('https://gateway.fedex.com:443/GatewayDC', $sXML, $xmlres, '', $errormsg, FALSE);
	// print str_replace("<","<br />&lt;",str_replace("</","&lt;/",$xmlres)) . "<br />\n";
	if($success){
		$success = ParseFedexOutput($xmlres, 'FDXSubscriptionReply', $fedexmeter, $errormsg);
	}
?>
	<form method="post" name="licform" action="admin.php">
	  <input type="hidden" name="upsstep" value="5" />
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr> 
          <td width="100%">
            <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr>
				<td rowspan="3" width="70" align="center" valign="top"><img src="../images/fedexsmall.gif" border="0" alt="FedEx" /><br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</td>
                <td width="100%" align="center"><strong><?php print $yyFdxWiz?> - <?php if($success) print $yyRegSucc; else print $yyError; ?></strong><br />&nbsp;
                </td>
			  </tr>
<?php
	if($success){
		$sSQL = "UPDATE admin SET FedexAccountNo='" . trim($_POST["fedexaccount"]) . "',FedexMeter='" . $fedexmeter . "'";
		mysql_query($sSQL) or print(mysql_error());
?>
			  <tr> 
                <td width="100%" align="left">
				  <p><strong><?php print $yyRegSucc?> !</strong></p>
				  <p>Thank you for registering to use FedEx&reg; Rates and Tracking.</p>
				  <p>To learn more about FedEx shipping services please visit <a href="http://www.fedex.com" target="_blank">www.fedex.com</a>.</p>
				  <p>To begin using FedEx shipping calculations please don't forget to select FedEx Shipping from the <strong>Shipping Type</strong> dropdown in the page <a href="adminmain.php"><?php print $yyAdmMai?></a>.</p>
				  <p>&nbsp;</p>
				  <p align="center"><input type="submit" value="<?php print $yyDone?>" /></p>
				  <p>&nbsp;</p>
                </td>
			  </tr>
<?php
	}else{ ?>
			  <tr> 
                <td width="100%" align="center"><p><?php print $yySorErr?></strong></p>
				<p>&nbsp;</p>
				<p><?php print $errormsg ?></p>
				<p>&nbsp;</p>
				<p><?php print $yyTryBac?> <a href="javascript:history.go(-1)"><?php print $yyClkHer?></a>.</p>
				<p>&nbsp;</p>
                </td>
			  </tr>
<?php
	} ?>
			  <tr> 
                <td colspan="2" width="100%" align="center">
				  <p><img src="../images/clearpixel.gif" width="300" height="5" alt="" /></p>
				  <p><font size="1">FedEx® is a registered service mark of Federal Express Corporation.
FedEx logos used by permission. All rights reserved.</font></p>
                </td>
			  </tr>
            </table>
          </td>
        </tr>
      </table>
	</form>
<?php
}elseif(@$_POST["upsstep"]=="2"){
?>
<script language="javascript" type="text/javascript">
<!--
function checkforamp(checkObj){
  checkStr = checkObj.value;
  for (i = 0;  i < checkStr.length;  i++){
	if (checkStr.charAt(i) == "&"){
	  alert("Please do not use the ampersand \"&\" character in any field.");
	  checkObj.focus();
	  return(false);
	}
  }
  return(true);
}
function formvalidator(theForm)
{
  if(theForm.contact.value == ""){
    alert("<?php print $yyPlsEntr?> \"<?php print $yyConNam?>\".");
    theForm.contact.focus();
    return (false);
  }
  if(!checkforamp(theForm.contact)) return(false);
  if(!checkforamp(theForm.company)) return(false);
  if(theForm.address.value == ""){
    alert("<?php print $yyPlsEntr?> \"<?php print $yyStrAdd?>\".");
    theForm.address.focus();
    return (false);
  }
  if(!checkforamp(theForm.address)) return(false);
  if(theForm.city.value == ""){
    alert("<?php print $yyPlsEntr?> \"<?php print $yyCity?>\".");
    theForm.city.focus();
    return (false);
  }
  if(!checkforamp(theForm.city)) return(false);
  var cntry = theForm.country[theForm.country.selectedIndex].value;
  if(cntry=="US" || cntry=="CA"){
	if (theForm.usstate.selectedIndex == 0){
      alert("<?php print $yyPlsSel?> \"<?php print $yyState?>\".");
      theForm.usstate.focus();
      return (false);
	}
  }
  if(theForm.country.selectedIndex == 0){
    alert("<?php print $yyPlsSel?> \"<?php print $yyCountry?>\".");
    theForm.country.focus();
    return (false);
  }
  if (theForm.postcode.value == ""){
	alert("<?php print $yyPlsEntr?> \"<?php print $yyPCode?>\".");
	theForm.postcode.focus();
	return (false);
  }
  if(!checkforamp(theForm.postcode)) return(false);
  if(theForm.telephone.value == ""){
    alert("<?php print $yyPlsEntr?> \"<?php print $yyTelep?>\".");
    theForm.telephone.focus();
    return (false);
  }
  if(theForm.telephone.value.length < 6 || theForm.telephone.value.length > 16){
    alert("<?php print $yyValTN?>");
    theForm.telephone.focus();
    return (false);
  }
  var checkOK = "0123456789";
  var checkStr = theForm.telephone.value;
  var allValid = true;
  for (i = 0;  i < checkStr.length;  i++){
    ch = checkStr.charAt(i);
    for (j = 0;  j < checkOK.length;  j++)
      if (ch == checkOK.charAt(j))
        break;
    if (j == checkOK.length)
    {
      allValid = false;
      break;
    }
  }
  if(!allValid){
    alert("<?php print $yyOnDig?> \"<?php print $yyTelep?>\".");
    theForm.telephone.focus();
    return (false);
  }
  if(!checkforamp(theForm.fedexaccount)) return(false);
  if(theForm.fedexaccount.value == ""){
    alert("<?php print $yyPlsEntr?> \"Fedex Account Number\".");
    theForm.fedexaccount.focus();
    return (false);
  }
  var checkOK = "0123456789";
  var checkStr = theForm.fedexaccount.value;
  var allValid = true;
  for (i = 0;  i < checkStr.length;  i++){
    ch = checkStr.charAt(i);
    for (j = 0;  j < checkOK.length;  j++)
      if (ch == checkOK.charAt(j))
        break;
    if (j == checkOK.length)
    {
      allValid = false;
      break;
    }
  }
  if(!allValid){
    alert("<?php print $yyOnDig?> \"Fedex Account Number\".");
    theForm.fedexaccount.focus();
    return (false);
  }
  return (true);
}
//-->
</script>
	<form method="post" name="licform" action="adminfedexlicense.php" onsubmit="return formvalidator(this)">
	  <input type="hidden" name="upsstep" value="3" />
	  <input type="hidden" name="countryCode" value="<?php print @$_POST["countryCode"]?>" />
	  <input type="hidden" name="languageCode" value="<?php print @$_POST["languageCode"]?>" />
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr> 
          <td width="100%">
            <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr>
				<td rowspan="18" width="70" align="center" valign="top"><img src="../images/fedexsmall.gif" border="0" alt="FedEx" /><br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</td>
                <td width="100%" align="center" colspan="2"><strong><?php print $yyFdxWiz?></strong><br />&nbsp;
                </td>
			  </tr>
			  <tr> 
                <td width="40%" align="right"><strong><font color="#FF0000">*</font><?php print $yyConNam?> : </strong></td>
				<td width="60%"><input type="text" name="contact" size="30" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><?php print $yyComNam?> : </strong></td>
				<td><input type="text" name="company" size="30" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong>Department : </strong></td>
				<td><input type="text" name="department" size="10" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><font color="#FF0000">*</font><?php print $yyStrAdd?> : </strong></td>
				<td><input type="text" name="address" size="30" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><?php print $yyAddr2?> : </strong></td>
				<td><input type="text" name="address2" size="30" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><font color="#FF0000">*</font><?php print $yyCity?> : </strong></td>
				<td><input type="text" name="city" size="30" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><font color="#FF0000">*</font><?php print $yyState?> <?php print $yyUSCan?> : </strong></td>
				<td><select name="usstate" size="1">
<option value=''><?php print $yyOutUS?></option>
<option value='AL'>Alabama</option>
<option value='AK'>Alaska</option>
<option value='AB'>Alberta</option>
<option value='AZ'>Arizona</option>
<option value='AR'>Arkansas</option>
<option value='BC'>British Columbia</option>
<option value='CA'>California</option>
<option value='CO'>Colorado</option>
<option value='CT'>Connecticut</option>
<option value='DE'>Delaware</option>
<option value='DC'>District Of Columbia</option>
<option value='FL'>Florida</option>
<option value='GA'>Georgia</option>
<option value='HI'>Hawaii</option>
<option value='ID'>Idaho</option>
<option value='IL'>Illinois</option>
<option value='IN'>Indiana</option>
<option value='IA'>Iowa</option>
<option value='KS'>Kansas</option>
<option value='KY'>Kentucky</option>
<option value='LA'>Louisiana</option>
<option value='ME'>Maine</option>
<option value='MB'>Manitoba</option>
<option value='MD'>Maryland</option>
<option value='MA'>Massachusetts</option>
<option value='MI'>Michigan</option>
<option value='MN'>Minnesota</option>
<option value='MS'>Mississippi</option>
<option value='MO'>Missouri</option>
<option value='MT'>Montana</option>
<option value='NE'>Nebraska</option>
<option value='NV'>Nevada</option>
<option value='NB'>New Brunswick</option>
<option value='NH'>New Hampshire</option>
<option value='NJ'>New Jersey</option>
<option value='NM'>New Mexico</option>
<option value='NY'>New York</option>
<option value='NF'>Newfoundland</option>
<option value='NC'>North Carolina</option>
<option value='ND'>North Dakota</option>
<option value='NT'>Northwest Territories</option>
<option value='NS'>Nova Scotia</option>
<option value='NU'>Nunavut</option>
<option value='OH'>Ohio</option>
<option value='OK'>Oklahoma</option>
<option value='ON'>Ontario</option>
<option value='OR'>Oregon</option>
<option value='PA'>Pennsylvania</option>
<option value='PI'>Prince Edward Island</option>
<option value='PQ'>Quebec</option>
<option value='RI'>Rhode Island</option>
<option value='SK'>Saskatchewan</option>
<option value='SC'>South Carolina</option>
<option value='SD'>South Dakota</option>
<option value='TN'>Tennessee</option>
<option value='TX'>Texas</option>
<option value='UT'>Utah</option>
<option value='VT'>Vermont</option>
<option value='VA'>Virginia</option>
<option value='WA'>Washington</option>
<option value='WV'>West Virginia</option>
<option value='WI'>Wisconsin</option>
<option value='WY'>Wyoming</option>
<option value='YT'>Yukon</option>
</select></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><font color="#FF0000">*</font><?php print $yyCountry?> : </strong></td>
				<td><select name="country" size="1">
<option value=''><?php print $yySelect?></option>
<option value='US'>United States</option>
				</select></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><font color="#FF0000">*</font><?php print $yyPCode?> : </strong></td>
				<td><input type="text" name="postcode" size="15" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><font color="#FF0000">*</font><?php print $yyTelep?> : </strong></td>
				<td><input type="text" name="telephone" size="30" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong>Pager Number : </strong></td>
				<td><input type="text" name="pager" size="30" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong>Fax Number : </strong></td>
				<td><input type="text" name="fax" size="30" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><?php print $yyEmail?> : </strong></td>
				<td><input type="text" name="email" size="30" /></td>
			  </tr>
			  <tr> 
				<td align="right"><strong><font color="#FF0000">*</font>Fedex Account Number : </strong></td>
				<td><input type="text" name="fedexaccount" size="30" /></td>
			  </tr>
			  <tr>
                <td width="100%" align="center" colspan="2"><br />&nbsp;<input type="submit" name="agree" value="&nbsp;&nbsp;<?php print $yyNext?>&nbsp;&nbsp;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="button" value="<?php print $yyCancel?>" onclick="javascript:window.location='admin.php';" />

                </td>
			  </tr>
			  <tr> 
                <td colspan="2" width="100%" align="center">
				  <p><img src="../images/clearpixel.gif" width="300" height="5" alt="" /></p>
				  <p><font size="1">FedEx® is a registered service mark of Federal Express Corporation.
FedEx logos used by permission. All rights reserved.</font></p>
                </td>
			  </tr>
            </table>
          </td>
        </tr>
      </table>
	</form>
<?php
}else{ ?>
	<form method="post" action="adminfedexlicense.php">
	  <input type="hidden" name="upsstep" value="2" />
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr> 
          <td width="100%">
            <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr>
				<td rowspan="5" width="70" align="center" valign="top"><img src="../images/fedexsmall.gif" border="0" alt="FedEx" /><br />&nbsp;</td>
                <td width="100%" align="center"><strong><?php print $yyFdxWiz?></strong><br />&nbsp;
                </td>
			  </tr>
<?php	$isregistered=FALSE;
		$sSQL = "SELECT FedexAccountNo,FedexMeter FROM admin WHERE adminID=1";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_array($result)){
			if(trim($rs['FedexAccountNo']) != '' && trim($rs['FedexMeter']) != '') $isregistered=TRUE;
		}
		if($isregistered){ ?>
			  <tr> 
                <td width="100%">You have already successfully completed the FedEx licensing and registration wizard. If you would like to re-register then please 
				click the "Re-register" button below. If you would just like to update your Ecommerce Plus version information with 
				FedEx then please click the "Update Version" button below.
				<p>&nbsp;</p>
                </td>
			  </tr>
			  <tr> 
                <td width="100%" align="center"><input type="submit" name="agree" value="&nbsp;&nbsp;Re-Register&nbsp;&nbsp;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="button" value="Update Version" onclick="javascript:window.location='adminfedexlicense.php?act=version';" />
                </td>
			  </tr>
<?php	}else{ ?>
			  <tr> 
                <td width="100%"><ul><li>This wizard will assist you in completing the necessary licensing and registration requirements to activate and use the FedEx&reg; Rates and Tracking services from your Ecommerce Plus Template.<br /><br /></li>
				<li>If you do not wish to use any of the functions that utilize the FedEx Rates and Tracking services, click the Cancel button and those functions will not be enabled. If, at a later time, you wish to use these services, return to this section and complete the FedEx licensing and registration process.<br /><br /></li>
				<li>For more information about FedEx services, please <a href="http://www.fedex.com" target="_blank"><?php print $yyClkHer?></a>.<br /><br /></li>
				</ul>
				<p>&nbsp;</p>
                </td>
			  </tr>
			  <tr> 
                <td width="100%" align="center"><input type="submit" name="agree" value="&nbsp;&nbsp;<?php print $yyNext?>&nbsp;&nbsp;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="button" value="<?php print $yyCancel?>" onclick="javascript:window.location='admin.php';" />
                </td>
			  </tr>
<?php	} ?>
			  <tr> 
                <td align="center" colspan="2"><p><font size="1"><br />To open a FedEx account, please <a href="https://www.fedex.com/us/OADR/index.html?link=4" target="_blank"><strong><?php print $yyClkHer?></strong></a><br /> </p></td>
			  </tr>
			  <tr> 
                <td width="100%" align="center">
				  <p><img src="../images/clearpixel.gif" width="300" height="5" alt="" /></p>
				  <p><font size="1">FedEx® is a registered service mark of Federal Express Corporation.
FedEx logos used by permission. All rights reserved.</font></p>
                </td>
			  </tr>
            </table>
          </td>
        </tr>
      </table>
	</form>
<?php
}
?>