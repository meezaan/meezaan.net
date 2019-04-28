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
function ParseUPSLicenseOutput($sXML, $rootNodeName, &$thetext, &$errormsg){
	$noError = TRUE;
	$errormsg = "";
	$gotxml=FALSE;
	$thetext="";
	$xmlDoc = new vrXMLDoc($sXML);
	// Set t2 = xmlDoc.getElementsByTagName(rootNodeName).Item(0)
	if($xmlDoc->nodeList->nodeName[0] != $rootNodeName){
		print "Error with rootnode " . $rootNodeName . ", is " . $xmlDoc->nodeList->nodeName[0] . "<br />";
		return(false);
	}
	$nodeList = $xmlDoc->nodeList->childNodes[0];
	for($i = 0; $i < $nodeList->length; $i++){
		if($nodeList->nodeName[$i]=="Response"){
			$e = $nodeList->childNodes[$i];
			for($j = 0; $j < $e->length; $j++){
				if($e->nodeName[$j]=="ResponseStatusCode"){
					$noError = ((int)$e->nodeValue[$j])==1;
				}
				if($e->nodeName[$j]=="Error"){
					$errormsg = "";
					$t = $e->childNodes[$j];
					for($k = 0; $k < $t->length; $k++){
						if($t->nodeName[$k]=="ErrorSeverity"){
							if($t->nodeValue[$k]=="Transient")
								$errormsg = "This is a temporary error. Please wait a few moments then refresh this page.<br />" . $errormsg;
						}elseif($t->nodeName[$k]=="ErrorDescription"){
							$errormsg .= $t->nodeValue[$k];
						}
					}
				}
				// print "The Nodename is : " . e.nodeName . ":" . e.firstChild.nodeValue . "<br />";
			}
		}elseif($nodeList->nodeName[$i]=="AccessLicenseNumber"){
			$thetext = $nodeList->nodeValue[$i];
		}elseif($nodeList->nodeName[$i]=="AccessLicenseText"){
			$thetext = $nodeList->nodeValue[$i];
			$sSQL = "UPDATE admin SET adminUPSLicense='" . str_replace("'","\\'",$nodeList->nodeValue[$i]) . "' WHERE adminID=1";
			mysql_query($sSQL) or print(mysql_error());
		}elseif($nodeList->nodeName[$i]=="UserId"){
			$thetext = $nodeList->nodeValue[$i];
		}
	}
	return($noError);
}

if(@$_POST["upsstep"]=="4"){
	$sSQL = "SELECT adminUPSLicense FROM admin WHERE adminID=1";
	$result = mysql_query($sSQL) or print(mysql_error());
	$rs = mysql_fetch_array($result);
	$sXML = '<?xml version="1.0" encoding="ISO-8859-1"?>';
	$sXML .= '<AccessLicenseRequest xml:lang="en-US"><Request><TransactionReference><CustomerContext>Ecomm Plus UPS Reg</CustomerContext><XpciVersion>1.0001</XpciVersion></TransactionReference>';
	$sXML .= "<RequestAction>AccessLicense</RequestAction><RequestOption>AllTools</RequestOption></Request>";
	$sXML .= "<CompanyName>" . @$_POST["company"] . "</CompanyName>";
	$sXML .= "<Address><AddressLine1>" . @$_POST["address"] . "</AddressLine1>";
	if(trim(@$_POST["address2"]) != "") $sXML .= "<AddressLine2>" . @$_POST["address2"] . "</AddressLine2>";
	$sXML .= "<City>" . @$_POST["city"] . "</City>";
	if(trim(@$_POST["country"])=="US" || Trim(@$_POST["country"])=="CA")
		$sXML .= "<StateProvinceCode>" . @$_POST["usstate"] . "</StateProvinceCode>";
	else
		$sXML .= "<StateProvinceCode>XX</StateProvinceCode>";
	if(trim(@$_POST["postcode"]) != "") $sXML .= "<PostalCode>" . @$_POST["postcode"] . "</PostalCode>";
	$sXML .= "<CountryCode>" . @$_POST["country"] . "</CountryCode></Address>";
	$sXML .= "<PrimaryContact><Name>" . @$_POST["contact"] . "</Name><Title>" . @$_POST["ctitle"] . "</Title>";
	$sXML .= "<EMailAddress>" . @$_POST["email"] . "</EMailAddress><PhoneNumber>" . @$_POST["telephone"] . "</PhoneNumber></PrimaryContact>";
	$sXML .= "<CompanyURL>" . @$_POST["websiteurl"] . "</CompanyURL>";
	if(trim(@$_POST["upsaccount"]) != "") $sXML .= "<ShipperNumber>" . @$_POST["upsaccount"] . "</ShipperNumber>";
	$sXML .= "<DeveloperLicenseNumber>BB9341E83CC05B12</DeveloperLicenseNumber>";
	$sXML .= "<AccessLicenseProfile><CountryCode>" . @$_POST["countryCode"] . "</CountryCode><LanguageCode>" . @$_POST["languageCode"] . "</LanguageCode>";
	$sXML .= "<AccessLicenseText>" . $rs["adminUPSLicense"] . "</AccessLicenseText>";
	$sXML .= "</AccessLicenseProfile>";
	$sXML .= "<OnLineTool><ToolID>RateXML</ToolID><ToolVersion>1.0</ToolVersion></OnLineTool><OnLineTool><ToolID>TrackXML</ToolID><ToolVersion>1.0</ToolVersion></OnLineTool>";
	$sXML .= "<ClientSoftwareProfile><SoftwareInstaller>" . @$_POST["upsrep"] . "</SoftwareInstaller><SoftwareProductName>Ecommerce Plus Templates</SoftwareProductName><SoftwareProvider>Internet Business Solutions SL</SoftwareProvider><SoftwareVersionNumber>2.5</SoftwareVersionNumber></ClientSoftwareProfile>";
	$sXML .= "</AccessLicenseRequest>";
	mysql_free_result($result);
	
	// print str_replace("<","<br />&lt;",$sXML) . "<HR>\n";
	if(@$pathtocurl != ""){
		exec($pathtocurl . ' --data-binary \'' . str_replace("'","\'",$sXML) . '\' https://www.ups.com/ups.app/xml/License', $res, $retvar);
		$res = implode("\n",$res);
		$success = ParseUPSLicenseOutput($res, "AccessLicenseResponse", $accessnumber, $errormsg);
	}else{
		if (!$ch = curl_init()) {
			$success = false;
			$errormsg = "cURL package not installed in PHP";
		}else{
			curl_setopt($ch, CURLOPT_URL,'https://www.ups.com/ups.app/xml/License'); 
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $sXML);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			if(@$curlproxy!=''){
				curl_setopt($ch, CURLOPT_PROXY, $curlproxy);
			}
			$res = curl_exec($ch);
			curl_close($ch);
			// print str_replace("<","<br />&lt;",$res) . "<br />\n";
			$success = ParseUPSLicenseOutput($res, "AccessLicenseResponse", $accessnumber, $errormsg);
		}
	}

	if($success){
		$sSQL = "UPDATE admin SET adminUPSAccess='" . $accessnumber . "'";
		mysql_query($sSQL) or print(mysql_error());
		$noloops=0;
		srand((double)microtime()*1000000);
		$upperbound = "999999";
		$lowerbound = "100000";
		$thepw = "ecp" . rand($lowerbound, $upperbound);
		$theuser = "ecu" . rand($lowerbound, $upperbound);
		while($theuser != "" && $success && $noloops < 5){
			$saveuser = $theuser;
			$sXML = '<?xml version="1.0" encoding="ISO-8859-1"?>';
			$sXML .= "<RegistrationRequest><Request><TransactionReference><CustomerContext>Ecomm Plus UPS Reg</CustomerContext><XpciVersion>1.0001</XpciVersion></TransactionReference>";
			$sXML .= "<RequestAction>Register</RequestAction><RequestOption>suggest</RequestOption></Request>";
			$sXML .= "<UserId>" . $theuser . "</UserId><Password>" . $thepw . "</Password><RegistrationInformation>";
			$sXML .= "<UserName>" . @$_POST["contact"] . "</UserName>";
			$sXML .= "<CompanyName>" . @$_POST["company"] . "</CompanyName>";
			$sXML .= "<Title>" . @$_POST["ctitle"] . "</Title><Address>";
			$sXML .= "<AddressLine1>" . @$_POST["address"] . "</AddressLine1>";
			if(trim(@$_POST["address2"]) != "") $sXML .= "<AddressLine2>" . @$_POST["address2"] . "</AddressLine2>";
			$sXML .= "<City>" . @$_POST["city"] . "</City>";
			if(trim(@$_POST["country"])=="US" || Trim(@$_POST["country"])=="CA")
				$sXML .= "<StateProvinceCode>" . @$_POST["usstate"] . "</StateProvinceCode>";
			else
				$sXML .= "<StateProvinceCode>XX</StateProvinceCode>";
			if(trim(@$_POST["postcode"]) != "") $sXML .= "<PostalCode>" . @$_POST["postcode"] . "</PostalCode>";
			$sXML .= "<CountryCode>" . @$_POST["country"] . "</CountryCode></Address>";
			$sXML .= "<PhoneNumber>" . @$_POST["telephone"] . "</PhoneNumber>";
			$sXML .= "<EMailAddress>" . @$_POST["email"] . "</EMailAddress>";
			// if(trim(@$_POST["upsaccount"]) != "") $sXML .= "<ShipperNumber>" . @$_POST["upsaccount"] . "</ShipperNumber>";
			$sXML .= "</RegistrationInformation></RegistrationRequest>";
			
			// print str_replace("<","<br />&lt;",$sXML) . "<HR>\n";
			if(@$pathtocurl != ""){
				exec($pathtocurl . ' --data-binary \'' . str_replace("'","\'",$sXML) . '\' https://www.ups.com/ups.app/xml/Register', $res, $retvar);
				$res = implode("\n",$res);
				$success = ParseUPSLicenseOutput($res, "RegistrationResponse", $theuser, $errormsg);
			}else{
				if (!$ch = curl_init()) {
					$success = false;
					$errormsg = "cURL package not installed in PHP";
				}else{
					curl_setopt($ch, CURLOPT_URL,'https://www.ups.com/ups.app/xml/Register'); 
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $sXML);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					if(@$curlproxy!=''){
						curl_setopt($ch, CURLOPT_PROXY, $curlproxy);
					}
					$res = curl_exec($ch);
					curl_close($ch);
					// print str_replace("<","<br />&lt;",$res) . "<br />\n";
					$success = ParseUPSLicenseOutput($res, "RegistrationResponse", $theuser, $errormsg);
				}
			}
			$noloops++;
		}
	}
?>
	<form method="post" name="licform" action="admin.php">
	  <input type="hidden" name="upsstep" value="5" />
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr> 
          <td width="100%">
            <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr>
				<td rowspan="3" width="70" align="center" valign="top"><img src="../images/LOGO_S.gif" border="0" alt="UPS" /><br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</td>
                <td width="100%" align="center"><strong><?php print $yyUPSWiz?> - <?php if($success) print $yyRegSucc; else print $yyError; ?></strong><br />&nbsp;
                </td>
			  </tr>
<?php
	if($success){
		$sSQL = "UPDATE admin SET adminUPSUser='" . upsencode($saveuser, "") . "',adminUPSpw='" . upsencode($thepw, "") . "'";
		mysql_query($sSQL) or print(mysql_error());
?>
			  <tr> 
                <td width="100%" align="left">
				  <p><strong><?php print $yyRegSucc?> !</strong></p>
				  <p><?php print $yyUPSLi5?></p>
				  <p><?php print $yyUPSLi6?> <a href="http://www.ec.ups.com" target="_blank">www.ec.ups.com</a>.</p>
				  <p><?php print $yyUPSLi7?> <a href="adminmain.php"><?php print $yyAdmMai?></a>.</p>
				  <p><?php print $yyUPSLi8?> <a href="http://ups.com/bussol/solutions/internetship.html" target="_blank"><?php print $yyClkHer?></a>.</p>
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
				  <p><font size="1"><?php print $yyUPStm?></font></p>
                </td>
			  </tr>
            </table>
          </td>
        </tr>
      </table>
	</form>
<?php
}elseif(@$_POST["upsstep"]=="3" && @$_POST["doagree"]=="1"){
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
  if(theForm.ctitle.value == ""){
    alert("<?php print $yyPlsEntr?> \"<?php print $yyTitle?>\".");
    theForm.ctitle.focus();
    return (false);
  }
  if(!checkforamp(theForm.ctitle)) return(false);
  if(theForm.company.value == ""){
    alert("<?php print $yyPlsEntr?> \"<?php print $yyComNam?>\".");
    theForm.company.focus();
    return (false);
  }
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
  if(cntry!='CL' && cntry!='CO' && cntry!='CR' && cntry!='DO' && cntry!='GT' && cntry!='HK' && cntry!='IE' && cntry!='PA'){
	if (theForm.postcode.value == ""){
	  alert("<?php print $yyPlsEntr?> \"<?php print $yyPCode?>\".");
	  theForm.postcode.focus();
	  return (false);
	}
  }
  if(!checkforamp(theForm.postcode)) return(false);
  if(theForm.telephone.value == ""){
    alert("<?php print $yyPlsEntr?> \"<?php print $yyTelep?>\".");
    theForm.telephone.focus();
    return (false);
  }
  if(theForm.telephone.value.length < 10 || theForm.telephone.value.length > 14){
    alert("<?php print $yyValTN?>");
    theForm.telephone.focus();
    return (false);
  }
  var checkOK = "0123456789";
  var checkStr = theForm.telephone.value;
  var allValid = true;
  for (i = 0;  i < checkStr.length;  i++)
  {
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
  if(!allValid)
  {
    alert("<?php print $yyOnDig?> \"<?php print $yyTelep?>\".");
    theForm.telephone.focus();
    return (false);
  }
  if(theForm.websiteurl.value == ""){
    alert("<?php print $yyPlsEntr?> \"<?php print $yyWebURL?>\".");
    theForm.websiteurl.focus();
    return (false);
  }
  if(!checkforamp(theForm.contact)) return(false);
  var checkStr = theForm.websiteurl.value;
  var gotDot = false;
  var gotAt = false;
  for (i = 0;  i < checkStr.length;  i++)
  {
    ch = checkStr.charAt(i);
    if (ch == "@") gotAt = true;
	if (ch == ".") gotDot = true;
  }
  if(!(gotDot))
  {
    alert("<?php print $yyValEnt?> \"<?php print $yyWebURL?>\".");
    theForm.websiteurl.focus();
    return (false);
  }
  if(theForm.email.value == ""){
    alert("<?php print $yyPlsEntr?> \"<?php print $yyEmail?>\".");
    theForm.email.focus();
    return (false);
  }
  var checkStr = theForm.email.value;
  var gotDot = false;
  var gotAt = false;
  for (i = 0;  i < checkStr.length;  i++)
  {
    ch = checkStr.charAt(i);
    if (ch == "@") gotAt = true;
	if (ch == ".") gotDot = true;
  }
  if (!(gotDot && gotAt))
  {
    alert("<?php print $yyValEnt?> \"<?php print $yyEmail?>\".");
    theForm.email.focus();
    return (false);
  }
  if(theForm.upsrep[0].checked==false && theForm.upsrep[1].checked==false){
    alert("<?php print $yyUPSrep?>");
    return (false);
  }
  return (true);
}
//-->
</script>
	<form method="post" name="licform" action="adminupslicense.php" onsubmit="return formvalidator(this)">
	  <input type="hidden" name="upsstep" value="4" />
	  <input type="hidden" name="countryCode" value="<?php print @$_POST["countryCode"]?>" />
	  <input type="hidden" name="languageCode" value="<?php print @$_POST["languageCode"]?>" />
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr> 
          <td width="100%">
            <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr>
				<td rowspan="18" width="70" align="center" valign="top"><img src="../images/LOGO_S.gif" border="0" alt="UPS" /><br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</td>
                <td width="100%" align="center" colspan="2"><strong><?php print $yyUPSWiz?> - <?php print $yyStep?> 2</strong><br />&nbsp;
                </td>
			  </tr>
			  <tr> 
                <td width="40%" align="right"><strong><?php print $yyConNam?> : </strong></td>
				<td width="60%"><input type="text" name="contact" size="30" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><?php print $yyTitle?> : </strong></td>
				<td><input type="text" name="ctitle" size="10" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><?php print $yyComNam?> : </strong></td>
				<td><input type="text" name="company" size="30" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><?php print $yyStrAdd?> : </strong></td>
				<td><input type="text" name="address" size="30" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><?php print $yyAddr2?> : </strong></td>
				<td><input type="text" name="address2" size="30" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><?php print $yyCity?> : </strong></td>
				<td><input type="text" name="city" size="30" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><?php print $yyState?> <?php print $yyUSCan?> : </strong></td>
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
                <td align="right"><strong><?php print $yyCountry?> : </strong></td>
				<td><select name="country" size="1">
<option value=''><?php print $yySelect?></option>
<option value='AR'>Argentina</option>
<option value='AU'>Australia</option>
<option value='AT'>Austria</option>
<option value='BE'>Belgium</option>
<option value='BR'>Brazil</option>
<option value='CA'>Canada</option>
<option value='CL'>Chile</option>
<option value='CN'>China</option>
<option value='CO'>Colombia</option>
<option value='CR'>Costa Rica</option>
<option value='DK'>Denmark</option>
<option value='DO'>Dominican Republic</option>
<option value='FI'>Finland</option>
<option value='FR'>France</option>
<option value='DE'>Germany</option>
<option value='GR'>Greece</option>
<option value='GT'>Guatemala</option>
<option value='HK'>Hong Kong</option>
<option value='IN'>India</option>
<option value='IE'>Ireland</option>
<option value='IL'>Israel</option>
<option value='IT'>Italy</option>
<option value='JP'>Japan</option>
<option value='MY'>Malaysia</option>
<option value='MX'>Mexico</option>
<option value='NL'>Netherlands</option>
<option value='NZ'>New Zealand</option>
<option value='NO'>Norway</option>
<option value='PA'>Panama</option>
<option value='PH'>Philippines</option>
<option value='PT'>Portugal</option>
<option value='PR'>Puerto Rico</option>
<option value='SG'>Singapore</option>
<option value='KR'>South Korea</option>
<option value='ES'>Spain</option>
<option value='SE'>Sweden</option>
<option value='CH'>Switzerland</option>
<option value='TW'>Taiwan</option>
<option value='TH'>Thailand</option>
<option value='GB'>United Kingdom</option>
<option value='US'>United States</option>
				</select></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><?php print $yyPCode?> : </strong></td>
				<td><input type="text" name="postcode" size="15" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><?php print $yyTelep?> : </strong></td>
				<td><input type="text" name="telephone" size="30" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><?php print $yyWebURL?> : </strong></td>
				<td><input type="text" name="websiteurl" size="30" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><?php print $yyEmail?> : </strong></td>
				<td><input type="text" name="email" size="30" /></td>
			  </tr>
			  <tr> 
                <td align="right"><strong><?php print $yyUPSac?> : </strong></td>
				<td><input type="text" name="upsaccount" size="30" /></td>
			  </tr>
			  <tr> 
                <td align="center" colspan="2">
				  <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr> 
          <td width="70%" align="center"><?php print $yyUPSsr?><br /><input type="radio" name="upsrep" value="yes" /> <strong><?php print $yyYes?></strong> <input type="radio" name="upsrep" value="no" /> <strong><?php print $yyNo?></strong></td>
				   </tr></table></td>
			  </tr>
			  <tr>
                <td width="100%" align="center" colspan="2"><br />&nbsp;<input type="submit" name="agree" value="&nbsp;&nbsp;<?php print $yyNext?>&nbsp;&nbsp;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="button" value="<?php print $yyCancel?>" onclick="javascript:window.location='admin.php';" />
                </td>
			  </tr>
			  <tr> 
                <td align="center" colspan="2"><p><font size="1"><?php print $yyUPSop?> <a href="http://www.ups.com/content/us/en/resources/service/account.html" target="_blank"><?php print $yyClkHer?></a> <?php print $yyUPScl?><br />
				<?php print $yyUPSMI?> <a href="http://www.ec.ups.com" target="_blank"><?php print $yyClkHer?></a>.<br />
				<?php print $yyUPshp?> <a href="http://ups.com/bussol/solutions/internetship.html" target="_blank"><?php print $yyClkHer?></a></font></p>
				</td>
			  </tr>
			  <tr> 
                <td colspan="3" width="100%" align="center">
				  <p><img src="../images/clearpixel.gif" width="300" height="5" alt="" /></p>
				  <p><font size="1"><?php print $yyUPStm?></font></p>
                </td>
			  </tr>
            </table>
          </td>
        </tr>
      </table>
	</form>
<?php
}elseif(@$_POST["upsstep"]=="2"){
	$languageCode="EN";
	if($countryCode=="AR" || $countryCode=="ES" || $countryCode=="MX" || $countryCode=="CA" || $countryCode=="DO" || $countryCode=="GT" || $countryCode=="CR" || $countryCode=="CO" || $countryCode=="PA" || $countryCode=="PR" || $countryCode=="CL")
		$languageCode="ES";
	elseif($countryCode=="AT" || $countryCode=="DE")
		$languageCode="DE";
	elseif($countryCode=="PT" || $countryCode=="BR")
		$languageCode="PT";
	elseif($countryCode=="FR" || $countryCode=="CH" || $countryCode=="BE")
		$languageCode="FR";
	elseif($countryCode=="CN" || $countryCode=="HK")
		$languageCode="ZH";
	elseif($countryCode=="DK")
		$languageCode="DA";
	elseif($countryCode=="FI")
		$languageCode="FI";
	elseif($countryCode=="GR")
		$languageCode="EL";
	elseif($countryCode=="IN")
		$languageCode="GU";
	elseif($countryCode=="IL")
		$languageCode="IW";
	elseif($countryCode=="IT")
		$languageCode="IT";
	elseif($countryCode=="JP")
		$languageCode="JA";
	elseif($countryCode=="MY")
		$languageCode="MS";
	elseif($countryCode=="NL")
		$languageCode="NL";
	elseif($countryCode=="NO")
		$languageCode="NO";
	elseif($countryCode=="KR")
		$languageCode="KO";
	elseif($countryCode=="SE")
		$languageCode="SV";
	elseif($countryCode=="TH")
		$languageCode="TH";
	$sXML = '<?xml version="1.0" encoding="ISO-8859-1"?>';
	$sXML .= "<AccessLicenseAgreementRequest><Request><RequestOption>AllTools</RequestOption><TransactionReference><CustomerContext>Ecomm Plus UPS License</CustomerContext><XpciVersion>1.0001</XpciVersion></TransactionReference>";
	$sXML .= "<RequestAction>AccessLicense</RequestAction></Request><DeveloperLicenseNumber>8B8CC9F752512834</DeveloperLicenseNumber>";
	$sXML .= "<AccessLicenseProfile><CountryCode>" . $countryCode . "</CountryCode><LanguageCode>" . $languageCode . "</LanguageCode></AccessLicenseProfile>";
	$sXML .= "<OnLineTool><ToolID>RateXML</ToolID><ToolVersion>1.0</ToolVersion></OnLineTool><OnLineTool><ToolID>TrackXML</ToolID><ToolVersion>1.0</ToolVersion></OnLineTool></AccessLicenseAgreementRequest>";

	// print str_replace("<","<br />&lt;",$sXML) . "<HR>\n";

	if(@$pathtocurl != ""){
		exec($pathtocurl . ' --data-binary \'' . str_replace("'","\'",$sXML) . '\' https://www.ups.com/ups.app/xml/License', $res, $retvar);
		$res = implode("\n",$res);
		$success = ParseUPSLicenseOutput($res, "AccessLicenseAgreementResponse", $lictext, $errormsg);
	}else{
		if(!$ch = curl_init()) {
			$success = false;
			$errormsg = "cURL package not installed in PHP";
		}else{
			curl_setopt($ch, CURLOPT_URL,'https://www.ups.com/ups.app/xml/License'); 
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $sXML);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			if(@$curlproxy!=''){
				curl_setopt($ch, CURLOPT_PROXY, $curlproxy);
			}
			$res = curl_exec($ch);
			if(curl_error($ch) != "") print "Error with cURL installation: " . curl_error($ch) . "<br />";
			curl_close($ch);
			// print str_replace("<","<br />&lt;",$res) . "<br />\n";
			$success = ParseUPSLicenseOutput($res, "AccessLicenseAgreementResponse", $lictext, $errormsg);
		}
	}
?>
<script language="javascript" type="text/javascript">
<!--
var origlictext="";
function printlicense()
{
	var prnttext = '<html><body>\n';
	if(origlictext != document.licform.lictext.value){
		alert("It appears that the license text has been modified. Cannot print license.");
		return;
	}
	prnttext += document.licform.lictext.value.replace(/\n|\r\n/g,'<br />');
	prnttext += '</body></html>';
	var newwin = window.open("","printlicense",'menubar=no, scrollbars=yes, width=500, height=400, directories=no,location=no,resizable=yes,status=no,toolbar=no');
	newwin.document.open();
	newwin.document.write(prnttext);
	newwin.document.close();
	newwin.print();
}
function checkaccept(theForm)
{
  if(origlictext != document.licform.lictext.value){
	alert("It appears that the license text has been modified. Cannot proceed.");
	return (false);
  }
  if (theForm.doagree[0].checked == false)
  {
    alert("<?php print $yyUPSLi4?>");
    return (false);
  }
  return (true);
}
//-->
</script>
	<form method="post" name="licform" action="adminupslicense.php" onsubmit="return checkaccept(this)">
	  <input type="hidden" name="upsstep" value="3" />
	  <input type="hidden" name="countryCode" value="<?php print $countryCode?>" />
	  <input type="hidden" name="languageCode" value="<?php print $languageCode?>" />
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr> 
          <td width="100%">
            <table width="100%" border="0" cellspacing="2" cellpadding="0" bgcolor="">
			  <tr>
                <td width="100%" align="center"><img src="../images/LOGO_S.gif" border="0" align="middle" alt="UPS" />&nbsp;&nbsp;<strong><?php print $yyUPSWiz?> - <?php print $yyStep?> 1</strong><br />&nbsp;
                </td>
			  </tr>
<?php	if($success){ ?>
			  <tr> 
                <td width="100%" align="center"><textarea cols="80" rows="20" name="lictext"><?php print $lictext?></textarea><br /><br />
				<p><?php print $yyUPSTer?></p>
				<p><?php print $yyAgree?> <input type="radio" name="doagree" value="1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print $yyNoAgre?> <input type="radio" name="doagree" value="0" /></p>
				<p>&nbsp;</p>
                </td>
			  </tr>
<script language="javascript" type="text/javascript">
<!--
var origlictext=document.licform.lictext.value;
//-->
</script>
<?php	}else{ ?>
			  <tr> 
                <td width="100%" align="center"><p><?php print $yySorErr?></strong></p>
				<p>&nbsp;</p>
				<p><?php print $errormsg?></p>
				<p>&nbsp;</p>
                </td>
			  </tr>
<?php	} ?>
			  <tr> 
                <td width="100%" align="center"><?php if($success){ ?><input type="button" value="&nbsp;<?php print $yyPrint?>&nbsp;" onclick="javascript:printlicense();" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="agree" value="&nbsp;&nbsp;<?php print $yyNext?>&nbsp;&nbsp;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
				<input type="button" value="<?php print $yyCancel?>" onclick="javascript:window.location='admin.php';" />
                </td>
			  </tr>
			  <tr> 
                <td align="center"><p><font size="1"><?php print $yyUPSop?> <a href="http://www.ups.com/content/us/en/resources/service/account.html" target="_blank">click here</a> or call 1-800-PICK-UPS.<br />
				<?php print $yyUPSMI?> <a href="http://www.ec.ups.com" target="_blank"><?php print $yyClkHer?></a>.<br />
				<?php print $yyUPshp?> <a href="http://ups.com/bussol/solutions/internetship.html" target="_blank"><?php print $yyClkHer?></a>.</font></p>
				</td>
			  </tr>
			  <tr> 
                <td width="100%" align="center">
				  <p><img src="../images/clearpixel.gif" width="300" height="5" alt="" /></p>
				  <p><font size="1"><?php print $yyUPStm?></font></p>
                </td>
			  </tr>
            </table>
          </td>
        </tr>
      </table>
	</form>
<?php
}else{ ?>
	<form method="post" action="adminupslicense.php">
	  <input type="hidden" name="upsstep" value="2" />
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr> 
          <td width="100%">
            <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr>
				<td rowspan="5" width="70" align="center" valign="top"><img src="../images/LOGO_S.gif" border="0" alt="UPS" /><br />&nbsp;</td>
                <td width="100%" align="center"><strong><?php print $yyUPSWiz?></strong><br />&nbsp;
                </td>
			  </tr>
			  <tr> 
                <td width="100%"><ul><li><?php print $yyUPSLi1?><br /><br /></li>
				<li><?php print $yyUPSLi2?><br /><br /></li>
				<li><?php print $yyUPSLi3?> <?php print $yyNoCou?> <a href="adminmain.php"><?php print $yyClkHer?></a>.<br /><br /></li>
				<li><?php print $yyUPSMI?> <a href="http://www.ec.ups.com" target="_blank"><?php print $yyClkHer?></a>.<br /><br /></li>
				<li><?php print $yyUPshp?> <a href="http://ups.com/bussol/solutions/internetship.html" target="_blank"><?php print $yyClkHer?></a>.</li>
				</ul>
				<p>&nbsp;</p>
                </td>
			  </tr>
			  <tr> 
                <td width="100%" align="center"><input type="submit" name="agree" value="&nbsp;&nbsp;<?php print $yyNext?>&nbsp;&nbsp;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="button" value="<?php print $yyCancel?>" onclick="javascript:window.location='admin.php';" />
                </td>
			  </tr>
			  <tr> 
                <td align="center" colspan="2"><p><font size="1"><?php print $yyUPSop?> <a href="http://www.ups.com/content/us/en/resources/service/account.html" target="_blank"><?php print $yyClkHer?></a> <?php print $yyUPScl?><br />
				<?php print $yyUPSMI?> <a href="http://www.ec.ups.com" target="_blank"><?php print $yyClkHer?></a>.<br />
				<?php print $yyUPshp?> <a href="http://ups.com/bussol/solutions/internetship.html" target="_blank"><?php print $yyClkHer?></a>.</font></p>
				</td>
			  </tr>
			  <tr> 
                <td width="100%" align="center">
				  <p><img src="../images/clearpixel.gif" width="300" height="5" alt="" /></p>
				  <p><font size="1"><?php print $yyUPStm?></font></p>
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