<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$_SERVER['CONTENT_LENGTH'] != '' && $_SERVER['CONTENT_LENGTH'] > 10000) exit;
$alreadygotadmin = getadminsettings();
$incupscopyright=false;
$incfedexcopyright=false;
if(@$_REQUEST['carrier'] != '')
	$theshiptype=$_REQUEST['carrier'];
else{
	$possshiptypes=0;
	if(@$defaulttrackingcarrier!='') $theshiptype=$defaulttrackingcarrier; else $theshiptype='ups';
	if($shipType==3 || @$alternateratesusps != ''){
		$theshiptype='usps';
		$possshiptypes++;
	}
	if(@$shipType==4 || @$alternateratesups != ''){
		$theshiptype='ups';
		$incupscopyright=TRUE;
		$possshiptypes++;
	}
	if($shipType==7 || @$alternateratesfedex != ''){
		$theshiptype='fedex';
		$incfedexcopyright=TRUE;
		$possshiptypes++;
	}
	if($possshiptypes>1) $theshiptype='undecided';
}
?>
<script language="javascript" type="text/javascript">
<!--
function viewlicense()
{
	var prnttext = '<html><head><STYLE TYPE="text/css">A:link {COLOR: #333333; TEXT-DECORATION: none}A:visited {COLOR: #333333; TEXT-DECORATION: none}A:active {COLOR: #333333; TEXT-DECORATION: none}A:hover {COLOR: #f39000; TEXT-DECORATION: none}TD {FONT-FAMILY: Verdana;}P {FONT-FAMILY: Verdana;}HR {color: #637BAD;height: 1px;}</STYLE></head><body><table width="100%" border="0" cellspacing="1" cellpadding="3">\n';
	prnttext += '<tr><td colspan="2" align="center"><a href="javascript:window.close()"><strong>Close Window</strong></a></td></tr>';
	prnttext += '<tr><td width="40"><img src="images/LOGO_S.gif"  alt="UPS" /></td><td><p><font size="3" face="Verdana"><strong>UPS Tracking Terms and Conditions</strong></font></p></td></tr>';
	prnttext += '<tr><td colspan="2"><p><font size="2" face="Verdana">The UPS package tracking systems accessed via this Web Site (the &quot;Tracking Systems&quot;) and tracking information obtained through this Web Site (the &quot;Information&quot;) are the private property of UPS. UPS authorizes you to use the Tracking Systems solely to track shipments tendered by or for you to UPS for delivery and for no other purpose. Without limitation, you are not authorized to make the Information available on any web site or otherwise reproduce, distribute, copy, store, use or sell the Information for commercial gain without the express written consent of UPS. This is a personal service, thus your right to use the Tracking Systems or Information is non-assignable. Any access or use that is inconsistent with these terms is unauthorized and strictly prohibited.</font></p></td></tr>';
	prnttext += '<tr><td colspan="2"><hr /><font size="1" face="Verdana">Copyright&nbsp;&copy; 1994-2003 United Parcel Service of America, Inc. All rights reserved.</font></td></tr>';
	prnttext += '<tr><td colspan="2" align="center">&nbsp;<br /><a href="javascript:window.close()"><strong>Close Window</strong></a></td></tr>';
	prnttext += '</table></body></html>';
	var newwin = window.open("","viewlicense",'menubar=no, scrollbars=yes, width=500, height=400, directories=no,location=no,resizable=yes,status=no,toolbar=no');
	newwin.document.open();
	newwin.document.write(prnttext);
	newwin.document.close();
}
function checkaccept()
{
  if (document.trackform.agreeconds.checked == false)
  {
    alert("Please note: To track your package(s), you must accept the UPS Tracking Terms and Conditions by selecting the checkbox below.");
    return (false);
  }else{
	document.trackform.submit();
  }
  return (true);
}
//-->
</script>
<?php
if($theshiptype=="ups"){
?>
&nbsp;<br />
      <table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
		<tr>
		  <td class="cobll" bgcolor="#FFFFFF" colspan="2">
			<table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="">
			  <tr>
				<td width="40"><img src="images/LOGO_S.gif" alt="UPS" /></td><td align="center">&nbsp;<br /><font size="4"><strong>UPS Tracking Tool</strong></font><br />&nbsp;</td><td width="40">&nbsp;</td>
			  </tr>
			</table>
		  </td>
		</tr>
<?php
function getAddress($u, &$theAddress){
	$signedby = "";
	for($l = 0;$l < $u->length; $l++){
		//print "AddName : " . $u->nodeName[$l] . ", AddVal : " . $u->nodeValue[$l] . "<br />";
		if($u->nodeName[$l] == "AddressLine1")
			$addressline1 = $u->nodeValue[$l];
		elseif($u->nodeName[$l] == "AddressLine2")
			$addressline2 = $u->nodeValue[$l];
		elseif($u->nodeName[$l] == "AddressLine3")
			$addressline3 = $u->nodeValue[$l];
		elseif($u->nodeName[$l] == "City")
			$city = $u->nodeValue[$l];
		elseif($u->nodeName[$l] == "StateProvinceCode")
			$statecode = $u->nodeValue[$l];
		elseif($u->nodeName[$l] == "PostalCode")
			$postcode = $u->nodeValue[$l];
		elseif($u->nodeName[$l] == "CountryCode"){
			$sSQL = "SELECT countryName FROM countries WHERE countryCode='" . $u->nodeValue[$l] . "'";
			$result = mysql_query($sSQL) or print(mysql_error());
			if(mysql_num_rows($result) > 0){
				$rs = mysql_fetch_assoc($result);
				$countrycode = $rs["countryName"];
			}else
				$countrycode = $u->nodeValue[$l];
			mysql_free_result($result);
		}
	}
	$theAddress = "";
	if(@$addressline1 != "") $theAddress .= $addressline1 . "<br />";
	if(@$addressline2 != "") $theAddress .= $addressline2 . "<br />";
	if(@$addressline3 != "") $theAddress .= $addressline3 . "<br />";
	if(@$city != "") $theAddress .= $city . "<br />";
	if(@$statecode != "" && @$postcode != "")
		$theAddress .= $statecode . ", " . $postcode . "<br />";
	else{
		if(@$statecode != "") $theAddress .= $statecode . "<br />";
		if(@$postcode != "") $theAddress .= $postcode . "<br />";
	}
	if(@$countrycode != "") $theAddress .= $countrycode . "<br />";
}
function ParseUPSTrackingOutput($sXML, &$totActivity, &$shipperNo, &$serviceDesc, &$shipperaddress, &$shiptoaddress, &$scheddeldate, &$rescheddeldate, &$errormsg, &$activityList){
	$noError = TRUE;
	$totalCost = 0;
	$packCost = 0;
	$index = 0;
	$errormsg = "";
	$gotxml=FALSE;
	$theaddress="";
	// print str_replace("<","<br />&lt;",$sXML) . "<br />\n";
	$xmlDoc = new vrXMLDoc($sXML);
	// Set t2 = xmlDoc.getElementsByTagName("TrackResponse").Item(0)
	$nodeList = $xmlDoc->nodeList->childNodes[0];
	for($ii = 0; $ii < $nodeList->length; $ii++){
		if($nodeList->nodeName[$ii]=="Response"){
			$e = $nodeList->childNodes[$ii];
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
			}
		}elseif($nodeList->nodeName[$ii]=="Shipment"){ // no Top-level Error
			$e = $nodeList->childNodes[$ii];
			for($i = 0;$i < $e->length; $i++){
				// print "Nodename is : " . $e->nodeName[$i] . "<br />";
				switch($e->nodeName[$i]){
					case "Shipper":
						$t = $e->childNodes[$i];
						for($k = 0; $k < $t->length; $k++){
							if($t->nodeName[$k] == "ShipperNumber")
								$shipperNo = $t->nodeValue[$k];
							elseif($t->nodeName[$k] == "Address")
								getAddress($t->childNodes[$k], $shipperaddress);
						}
					break;
					case "ShipTo":
						$t = $e->childNodes[$i];
						for($k = 0; $k < $t->length; $k++){
							if($t->nodeName[$k] == "Address")
								getAddress($t->childNodes[$k], $shiptoaddress);
						}
					break;
					case "ScheduledDeliveryDate":
						$scheddeldate = $e->nodeValue[$i];
					break;
					case "Service":
						$t = $e->childNodes[$i];
						for($k = 0; $k < $t->length; $k++){
							if($t->nodeName[$k] == "X_Code_X"){
								switch((int)$t->nodeValue[$k]){
									case 1:
										$serviceDesc = "Next Day Air";
										break;
									case 2:
										$serviceDesc = "2nd Day Air";
										break;
									case 3:
										$serviceDesc = "Ground Service";
										break;
									case 7:
										$serviceDesc = "Worldwide Express";
										break;
									case 8:
										$serviceDesc = "Worldwide Expedited";
										break;
									case 11:
										$serviceDesc = "Standard service";
										break;
									case 12:
										$serviceDesc = "3-Day Select";
										break;
									case 13:
										$serviceDesc = "Next Day Air Saver";
										break;
									case 14:
										$serviceDesc = "Next Day Air Early AM";
										break;
									case 54:
										$serviceDesc = "Worldwide Express Plus";
										break;
									case 59:
										$serviceDesc = "2nd Day Air AM";
										break;
									case 64:
										$serviceDesc = "UPS Express NA1";
										break;
									case 65:
										$serviceDesc = "Express Saver";
										break;
								}
								// print "The service code is : " . $t->nodeName[$k] . ":" . $t->nodeValue[$k] . "<br />";
							}elseif($t->nodeName[$k] == "Description"){
								$serviceDesc = $t->nodeValue[$k];
							}
						}
					break;
					case "Package":
						$t = $e->childNodes[$i];
						for($k = 0; $k < $t->length; $k++){
							if($t->nodeName[$k] == "RescheduledDeliveryDate"){
								$rescheddeldate = $t->nodeValue[$k];
							}elseif($t->nodeName[$k] == "Activity"){
								$u = $t->childNodes[$k];
								for($l = 0; $l < $u->length; $l++){
									if($u->nodeName[$l] == "ActivityLocation"){
										$v = $u->childNodes[$l];
										for($m = 0; $m < $v->length; $m++){
											if($v->nodeName[$m] == "Address")
												getAddress($v->childNodes[$m], $activityList[$totActivity][0]);
											elseif($v->nodeName[$m] == "Description")
												$description = $v->nodeValue[$m];
											elseif($v->nodeName[$m] == "SignedForByName")
												$activityList[$totActivity][1] = $v->nodeValue[$m];
										}
									}elseif($u->nodeName[$l] == "Status"){
										$v = $u->childNodes[$l];
										for($m = 0; $m < $v->length; $m++){
											if($v->nodeName[$m] == "StatusType"){
												$w = $v->childNodes[$m];
												for($nn = 0; $nn < $w->length; $nn++){
													if($w->nodeName[$nn] == "Code")
														$activityList[$totActivity][3]=$w->nodeValue[$nn];
													elseif($w->nodeName[$nn] == "Description")
														$activityList[$totActivity][4]=$w->nodeValue[$nn];
												}
											}elseif($v->nodeName[$m] == "StatusCode"){
												$w = $v->childNodes[$m];
												for($nn = 0; $nn < $w->length; $nn++){
													if($w->nodeName[$nn] == "Code")
														$activityList[$totActivity][5]=$w->nodeValue[$nn];
												}
											}
										}
									}else{
										if($u->nodeName[$l]=="Date")
											$activityList[$totActivity][6]=$u->nodeValue[$l];
										elseif($u->nodeName[$l]=="Time")
											$activityList[$totActivity][7]=$u->nodeValue[$l];
									}
								}
								$totActivity++;
							}
						}
					break;
				}
			}
		}
	}
	return $noError;
}
function UPSTrack($trackNo){
	global $upsAccess,$upsUser,$upsPw,$maintablewidth,$pathtocurl,$curlproxy;
	// activityList(100,10)
	// ActivityList(0) = Address
	// ActivityList(1) = SignedForByName
	// ActivityList(2) = Not Used
	// ActivityList(3) = Activity -> Status -> StatusType -> Code
	// ActivityList(4) = Activity -> Status -> StatusType -> Description
	// ActivityList(5) = Activity -> Status -> StatusCode -> Code
	// ActivityList(6) = Activity -> Date
	// ActivityList(7) = Activity -> Time
	$lastloc="xxxxxx";
	$success = true;

	$sXML = '<?xml version="1.0"?><AccessRequest xml:lang="en-US"><AccessLicenseNumber>' . $upsAccess . "</AccessLicenseNumber><UserId>" . $upsUser . "</UserId><Password>" . $upsPw . "</Password></AccessRequest>";
	$sXML .= '<?xml version="1.0"?><TrackRequest xml:lang="en-US"><Request><TransactionReference><CustomerContext>Example 3</CustomerContext><XpciVersion>1.0001</XpciVersion></TransactionReference><RequestAction>Track</RequestAction><RequestOption>';
	if(trim(@$_POST["activity"])=="LAST") $sXML .= "none"; else $sXML .= "activity";
	$sXML .= "</RequestOption></Request>";
	if(FALSE){
		$sXML .= "<ReferenceNumber><Value>" . $trackNo . "</Value></ReferenceNumber>";
		$sXML .= "<ShipperNumber>116593</ShipperNumber></TrackRequest>";
	}else
		$sXML .= "<TrackingNumber>" . $trackNo . "</TrackingNumber></TrackRequest>";
	if(@$pathtocurl != ""){
		exec($pathtocurl . ' --data-binary \'' . str_replace("'","\'",$sXML) . '\' https://www.ups.com/ups.app/xml/Track', $res, $retvar);
		$res = implode("\n",$res);
	}else{
		if (!$ch = curl_init()) {
			$success = false;
			$errormsg = "cURL package not installed in PHP";
		}else{
			curl_setopt($ch, CURLOPT_URL,'https://www.ups.com/ups.app/xml/Track'); 
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
		}
	}
	if($success){
		$totActivity = 0;
		$success = ParseUPSTrackingOutput($res, $totActivity, $shipperNo, $serviceDesc, $shipperaddress, $shiptoaddress, $scheduleddeliverydate, $rescheddeliverydate, $errormsg, $activityList);

		if($success){
			for($index2=0; $index2 < $totActivity-1; $index2++){
				for($index=0; $index < $totActivity-1; $index++){
					if((int)($activityList[$index][6] . $activityList[$index][7]) > (int)($activityList[$index+1][6] . $activityList[$index+1][7])){
						$tempArr = $activityList[$index];
						$activityList[$index]=$activityList[$index+1];
						$activityList[$index+1]=$tempArr;
					}
				}
			}
			if(trim($shipperNo) != ""){ ?>
	  <tr>
		<td class="cobhl" bgcolor="#EBEBEB" width="30%"><strong>Shipper Number</strong> </td>
		<td class="cobll" bgcolor="#FFFFFF"><?php print $shipperNo?></td>
	  </tr>
	<?php	}
			if(trim($serviceDesc) != ""){ ?>
	  <tr>
		<td class="cobhl" bgcolor="#EBEBEB" width="30%"><strong>Service Description</strong> </td>
		<td class="cobll" bgcolor="#FFFFFF"><?php print $serviceDesc?></td>
	  </tr>
	<?php	}
			if(trim($shipperaddress) != ""){ ?>
	  <tr>
		<td class="cobhl" bgcolor="#EBEBEB" width="30%" valign="top"><strong>Shipper Address</strong> </td>
		<td class="cobll" bgcolor="#FFFFFF"><?php print $shipperaddress?></td>
	  </tr>
	<?php	}
			if(trim($shiptoaddress) != ""){ ?>
	  <tr>
		<td class="cobhl" bgcolor="#EBEBEB" width="30%" valign="top"><strong>Ship-To Address</strong> </td>
		<td class="cobll" bgcolor="#FFFFFF"><?php print $shiptoaddress?></td>
	  </tr>
	<?php	}
			if(trim($scheduleddeliverydate) != ""){ ?>
	  <tr>
		<td class="cobhl" bgcolor="#EBEBEB" width="30%" valign="top"><strong>Sched. Delivery Date</strong> </td>
		<td class="cobll" bgcolor="#FFFFFF"><?php print date("m-d-Y",mktime(0,0,0,substr($scheduleddeliverydate,4,2),substr($scheduleddeliverydate,6,2),substr($scheduleddeliverydate,0,4)))?></font></td>
	  </tr>
	<?php	}
			if(trim($rescheddeliverydate) != ""){ ?>
	  <tr>
		<td class="cobhl" bgcolor="#EBEBEB" width="30%" valign="top"><strong>ReSched. Delivery Date</strong> </td>
		<td class="cobll" bgcolor="#FFFFFF"><?php print date("m-d-Y",mktime(0,0,0,substr($rescheddeliverydate,4,2),substr($rescheddeliverydate,6,2),substr($rescheddeliverydate,0,4)))?></font></td>
	  </tr>
	  <tr>
		<td class="cobhl" bgcolor="#EBEBEB" width="30%" valign="top"><strong>Note</strong> </td>
		<td class="cobll" bgcolor="#FFFFFF">Your package is in the UPS system and has a rescheduled delivery date of <?php print date("m-d-Y",mktime(0,0,0,substr($rescheddeliverydate,4,2),substr($rescheddeliverydate,6,2),substr($rescheddeliverydate,0,4)))?></font></td>
	  </tr>
	<?php	} ?>
			</table>
	  &nbsp;
			<table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB"><strong>Location</strong></td>
				<td class="cobhl" bgcolor="#EBEBEB"><strong>Description</strong></td>
				<td class="cobhl" bgcolor="#EBEBEB"><strong>Date&nbsp;/&nbsp;Time</strong></td>
			  </tr>
<?php
	for($index=0; $index < $totActivity; $index++){ 
		if(($index % 2) == 0)
			$cellbg='class="cobll" bgcolor="#FFFFFF"';
		else
			$cellbg='class="cobhl" bgcolor="#EBEBEB"';
?>
			  <tr>
			    <td <?php print $cellbg?>><font size="1"><?php
									if($lastloc==$activityList[$index][0])
										print '<p align="center">"</p>';
									else{
										print $activityList[$index][0];
										$lastloc = $activityList[$index][0];
									} ?></font></td>
				<td <?php print $cellbg?>><font size="1"><?php print $activityList[$index][4];
									if(@$activityList[$index][1] != "") print "<br /><strong>Signed By :</strong> " . $activityList[$index][1]; ?></font></td>
				<td <?php print $cellbg?>><font size="1"><?php
					$theDate = $activityList[$index][6];
					$theTime = $activityList[$index][7];
					print date("m-d-Y\<\B\R\>H:i:s",mktime(substr($theTime,0,2),substr($theTime,2,2),substr($theTime,4,2),substr($theDate,4,2),substr($theDate,6,2),substr($theDate,0,4)))?></font></td>
			  </tr>
<?php
	} ?>
			</table>
	  <hr width="70%">
	  <table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
<?php
		}else{
?>
			  <tr>
			    <td class="cobll" bgcolor="#FFFFFF" colspan="2" height="30" align="center"><strong>The tracking system returned the following error : <?php print $errormsg?></strong></td>
			  </tr>
			</table>
	  <hr width="70%">
	  <table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
<?php
		}
	}
	return $success;
}
if(trim(@$_POST["trackno"]) != "")
	UPSTrack(trim(@$_POST["trackno"]));
?>
			<form method="post" name="trackform" action="tracking.php">
			  <input type="hidden" name="carrier" value="ups">
			  <tr>
			    <td class="cobhl" width="50%" bgcolor="#EBEBEB" align="right">Please enter your UPS Tracking Number : </td>
				<td class="cobll" width="50%" bgcolor="#FFFFFF"><input type="text" size="30" name="trackno" value="<?php print trim(@$_REQUEST["trackno"])?>" /></td>
			  </tr>
			  <tr>
			    <td class="cobhl" width="50%" bgcolor="#EBEBEB" align="right">Show Activity : </td>
				<td class="cobll" width="50%" bgcolor="#FFFFFF"><select name="activity" size="1"><option value="LAST">Show Last Activity Only</option><option value="ALL"<?php if(trim(@$_POST["activity"])=="ALL") print " selected"?>>Show All Activity</option></select></td>
			  </tr>
			  <tr>
			    <td class="cobll" bgcolor="#FFFFFF" colspan="2"><table width="100%" cellspacing="0" cellpadding="0" border="0">
				    <tr>
					  <td class="cobll" bgcolor="#FFFFFF" width="17%" height="26">&nbsp;</td>
					  <td class="cobll" bgcolor="#FFFFFF" width="66%" align="center"><input type="button" onclick="viewlicense()" value="View License" /> <input type="button" value="Track Package" onclick="checkaccept()" /></td>
					  <td class="cobll" bgcolor="#FFFFFF" width="17%" height="26" align="right" valign="bottom"><img src="images/tablebr.gif" alt="" /></td>
					</tr>
				  </table></td>
			  </tr>
			  <tr>
				<td class="cobll" width="100%" bgcolor="#FFFFFF" height="30" colspan="2" align="center" valign="middle"><font size="1"><input type="checkbox" name="agreeconds" value="ON" <?php if(@$_POST["agreeconds"]=="ON") print "checked"?> /> By selecting this box and the "Track Package" button, I agree to these <a href="javascript:viewlicense();"><strong>Terms and Conditions</strong></a>.</font></td>
			  </tr>
			</form>
			</table>
	  <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="#FFFFFF" align="center">
        <tr>
          <td width="100%" align="center"><p>&nbsp;<br /><font size="1">UPS&reg;, UPS & Shield Design&reg; and UNITED PARCEL SERVICE&reg; 
				  are<br />registered trademarks of United Parcel Service of America, Inc.</font></p></td>
		</tr>
	  </table>
<br />
<?php
}elseif($theshiptype=="usps"){
?>
&nbsp;<br />
      <table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
		<tr>
		  <td class="cobll" bgcolor="#FFFFFF" colspan="2">
			<table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="">
			  <tr>
				<td width="40">&nbsp;</td><td align="center">&nbsp;<br /><font size="4"><strong>USPS Tracking Tool</strong></font><br />&nbsp;</td><td width="40">&nbsp;</td>
			  </tr>
			</table>
		  </td>
		</tr>
<?php
function ParseUSPSTrackingOutput($sXML, &$totActivity, $onlylast, &$serviceDesc, &$shipperaddress, &$shiptoaddress, &$scheddeldate, &$rescheddeldate, &$errormsg, &$activityList){
	$noError = TRUE;
	$totalCost = 0;
	$packCost = 0;
	$index = 0;
	$errormsg = "";
	$gotxml=FALSE;
	$theaddress="";
	// print str_replace("<","<br />&lt;",$sXML) . "<br />\n";
	$xmlDoc = new vrXMLDoc($sXML);

	if($xmlDoc->nodeList->nodeName[0] == "Error"){ // Top-level Error
		$noError = FALSE;
		$nodeList = $xmlDoc->nodeList->childNodes[0];
		for($i = 0; $i < $nodeList->length; $i++){
			if($nodeList->nodeName[$i]=="Description"){
				$errormsg = $nodeList->nodeValue[$i];
			}
		}
	}else{ // no Top-level Error
		$nodeList = $xmlDoc->nodeList->childNodes[0];
		for($i = 0; $i < $nodeList->length; $i++){
			if($nodeList->nodeName[$i]=="TrackInfo"){
				$e = $nodeList->childNodes[$i];
				for($j = 0; $j < $nodeList->childNodes[$i]->length; $j++){
					$companyname= "";
					$city="";
					$statecode="";
					$postcode="";
					$countrycode="";
					if($e->nodeName[$j] == "Error"){ // Lower-level error
						$t = $e->childNodes[$j];
						for($k = 0; $k < $t->length; $k++){
							if($t->nodeName[$k] == "Description"){
								$noError = FALSE;
								$errormsg = $t->nodeValue[$k];
							}
						}
					}elseif($e->nodeName[$j] == "TrackDetail"){
						if(!$onlylast){
							$t = $e->childNodes[$j];
							for($k = 0; $k < $t->length; $k++){
								switch($t->nodeName[$k]){
								case "EventDate":
									$activityList[$totActivity][6]=$t->nodeValue[$k];
									break;
								case "EventTime":
									$activityList[$totActivity][7]=$t->nodeValue[$k];
									break;
								case "Event":
									$activityList[$totActivity][4]=$t->nodeValue[$k];
									break;
								case "EventCity":
									$city = $t->nodeValue[$k];
									break;
								case "EventState":
									$statecode = $t->nodeValue[$k];
									break;
								case "EventZIPCode":
									$postcode = $t->nodeValue[$k];
									break;
								case "EventCountry":
									$countrycode = $t->nodeValue[$k];
									break;
								case "FirmName":
									$companyname = $t->nodeValue[$k];
									break;
								}
							}
							$theAddress = "";
							if(@$companyname != "") $theAddress .= $companyname . "<br />";
							if(@$city != "") $theAddress .= $city . "<br />";
							if(@$statecode != "" && @$postcode != "")
								$theAddress .= $statecode . ", " . $postcode . "<br />";
							else{
								if(@$statecode != "") $theAddress .= $statecode . "<br />";
								if(@$postcode != "") $theAddress .= $postcode . "<br />";
							}
							if(@$countrycode != "") $theAddress .= $countrycode . "<br />";
							$activityList[$totActivity][0] = $theAddress;
							$totActivity++;
						}
					}elseif($e->nodeName[$j] == "TrackSummary"){
						$t = $e->childNodes[$j];
						for($k = 0; $k < $t->length; $k++){
							switch($t->nodeName[$k]){
							case "EventDate":
								$scheddeldate=$t->nodeValue[$k] . $scheddeldate;
								break;
							case "EventTime":
								$scheddeldate=$scheddeldate . " " . $t->nodeValue[$k];
								break;
							case "Event":
								$serviceDesc=$t->nodeValue[$k];
								break;
							case "EventCity":
								$city = $t->nodeValue[$k];
								break;
							case "EventState":
								$statecode = $t->nodeValue[$k];
								break;
							case "EventZIPCode":
								$postcode = $t->nodeValue[$k];
								break;
							case "EventCountry":
								$countrycode = $t->nodeValue[$k];
								break;
							case "FirmName":
								$companyname = $t->nodeValue[$k];
								break;
							}
						}
						$theAddress = "";
						if(@$companyname != "") $theAddress .= $companyname . "<br />";
						if(@$city != "") $theAddress .= $city . "<br />";
						if(@$statecode != "" && @$postcode != "")
							$theAddress .= $statecode . ", " . $postcode . "<br />";
						else{
							if(@$statecode != "") $theAddress .= $statecode . "<br />";
							if(@$postcode != "") $theAddress .= $postcode . "<br />";
						}
						if(@$countrycode != "") $theAddress .= $countrycode . "<br />";
						$shiptoaddress = $theAddress;
					}
				}
				$totalCost += $packCost;
				$packCost = 0;
			}
		}
	}
	return $noError;
}
function USPSTrack($trackNo){
	global $uspsUser,$maintablewidth,$pathtocurl,$curlproxy;
	// activityList(100,10)
	// ActivityList(0) = Address
	// ActivityList(1) = SignedForByName
	// ActivityList(2) = Not Used
	// ActivityList(3) = Activity -> Status -> StatusType -> Code
	// ActivityList(4) = Activity -> Status -> StatusType -> Description
	// ActivityList(5) = Activity -> Status -> StatusCode -> Code
	// ActivityList(6) = Activity -> Date
	// ActivityList(7) = Activity -> Time
	$lastloc="xxxxxx";
	$success = true;
	$sXML = '<TrackFieldRequest USERID="'.$uspsUser.'"><TrackID ID="'.str_replace(' ','',@$_POST['trackno']).'"></TrackID></TrackFieldRequest>';
	//print str_replace("<","<br />&lt;",str_replace("</","&lt;/",$sXML)) . "<br />\n";
	$sXML = "API=TrackV2&XML=" . $sXML;
	if(@$usecurlforfsock){
		$success = callcurlfunction('http://production.shippingapis.com/ShippingAPI.dll', $sXML, $res, '', $errormsg, FALSE);
	}else{
		$header = "POST /ShippingAPI.dll HTTP/1.0\r\n";
		//$header = "POST /ShippingAPITest.dll HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= 'Content-Length: ' . strlen($sXML) . "\r\n\r\n";
		$fp = fsockopen ('production.shippingapis.com', 80, $errno, $errstr, 30);
		if (!$fp){
			echo "$errstr ($errno)"; // HTTP error handling
			return FALSE;
		}else{
			$res = "";
			fputs ($fp, $header . $sXML);
			while (!feof($fp)) {
				$res .= fgets ($fp, 1024);
			}
			fclose ($fp);
		}
	}
	//print str_replace("<","<br />&lt;",str_replace("</","&lt;/",$res)) . "<br />\n";
	if($success){
		$totActivity = 0;
		$success = ParseUSPSTrackingOutput($res, $totActivity, trim(@$_POST["activity"])=='LAST', $serviceDesc, $shipperaddress, $shiptoaddress, $scheduleddeliverydate, $rescheddeliverydate, $errormsg, $activityList);
		if($success){
			for($index2=0; $index2 < $totActivity-1; $index2++){
				for($index=0; $index < $totActivity-1; $index++){
					if(strtotime($activityList[$index][6] . " " . $activityList[$index][7]) > strtotime($activityList[$index+1][6] . ' ' . $activityList[$index+1][7])){
						$tempArr = $activityList[$index];
						$activityList[$index]=$activityList[$index+1];
						$activityList[$index+1]=$tempArr;
					}
				}
			}
			if(trim($serviceDesc) != ""){ ?>
	  <tr>
		<td class="cobhl" bgcolor="#EBEBEB" width="30%"><strong>Event</strong> </td>
		<td class="cobll" bgcolor="#FFFFFF"><?php print $serviceDesc?></td>
	  </tr>
	<?php	}
			if(trim($shiptoaddress) != ""){ ?>
	  <tr>
		<td class="cobhl" bgcolor="#EBEBEB" width="30%" valign="top"><strong>Address</strong> </td>
		<td class="cobll" bgcolor="#FFFFFF"><?php print $shiptoaddress?></td>
	  </tr>
	<?php	}
			if(trim($scheduleddeliverydate) != ""){ ?>
	  <tr>
		<td class="cobhl" bgcolor="#EBEBEB" width="30%" valign="top"><strong>Event Date</strong> </td>
		<td class="cobll" bgcolor="#FFFFFF"><?php print $scheduleddeliverydate?></font></td>
	  </tr>
	<?php	} ?>
			</table>
<?php		if($totActivity > 0){ ?>
	  &nbsp;
			<table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB"><strong>Location</strong></td>
				<td class="cobhl" bgcolor="#EBEBEB"><strong>Description</strong></td>
				<td class="cobhl" bgcolor="#EBEBEB"><strong>Date&nbsp;/&nbsp;Time</strong></td>
			  </tr>
<?php			for($index=0; $index < $totActivity; $index++){ 
					if(($index % 2) == 0)
						$cellbg='class="cobll" bgcolor="#FFFFFF"';
					else
						$cellbg='class="cobhl" bgcolor="#EBEBEB"'; ?>
			  <tr>
			    <td <?php print $cellbg?>><font size="1"><?php
									if($lastloc==$activityList[$index][0])
										print '<p align="center">"</p>';
									else{
										print $activityList[$index][0];
										$lastloc = $activityList[$index][0];
									} ?></font></td>
				<td <?php print $cellbg?>><font size="1"><?php print $activityList[$index][4];
									if(@$activityList[$index][1] != "") print "<br /><strong>Signed By :</strong> " . $activityList[$index][1]; ?></font></td>
				<td <?php print $cellbg?>><font size="1"><?php
					$theDate = $activityList[$index][6];
					$theTime = $activityList[$index][7];
					print $theDate . '<br />' . $theTime; ?></font></td>
			  </tr>
<?php			} ?>
			</table>
<?php		} ?>
	  <hr width="70%">
	  <table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
<?php
		}else{
?>
			  <tr>
			    <td class="cobll" bgcolor="#FFFFFF" colspan="2" height="30" align="center"><strong>The tracking system returned the following error : <?php print $errormsg?></strong></td>
			  </tr>
			</table>
	  <hr width="70%">
	  <table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
<?php
		}
	}
	return $success;
}
if(trim(@$_POST["trackno"]) != "")
	USPSTrack(trim(@$_POST["trackno"]));
?>
			<form method="post" name="trackform" action="tracking.php">
			  <input type="hidden" name="carrier" value="usps">
			  <tr>
			    <td class="cobhl" width="50%" bgcolor="#EBEBEB" align="right">Please enter your USPS Tracking Number : </td>
				<td class="cobll" width="50%" bgcolor="#FFFFFF"><input type="text" size="30" name="trackno" value="<?php print trim(@$_REQUEST["trackno"])?>" /></td>
			  </tr>
			  <tr>
			    <td class="cobhl" width="50%" bgcolor="#EBEBEB" align="right">Show Activity : </td>
				<td class="cobll" width="50%" bgcolor="#FFFFFF"><select name="activity" size="1"><option value="LAST">Show Last Activity Only</option><option value="ALL"<?php if(trim(@$_POST["activity"])=="ALL" || trim(@$_POST["activity"])=='') print " selected"?>>Show All Activity</option></select></td>
			  </tr>
			  <tr>
			    <td class="cobll" bgcolor="#FFFFFF" colspan="2"><table width="100%" cellspacing="0" cellpadding="0" border="0">
				    <tr>
					  <td class="cobll" bgcolor="#FFFFFF" width="17%" height="26">&nbsp;</td>
					  <td class="cobll" bgcolor="#FFFFFF" width="66%" align="center"><input type="submit" value="Track Package" /></td>
					  <td class="cobll" bgcolor="#FFFFFF" width="17%" height="26" align="right" valign="bottom"><img src="images/tablebr.gif" alt="" /></td>
					</tr>
				  </table></td>
			  </tr>
			</form>
			</table>
	  <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="#FFFFFF" align="center">
        <tr>
          <td width="100%" align="center"><p>&nbsp;</p></td>
		</tr>
	  </table>
<br />
<?php
}elseif($theshiptype=="fedex"){
?>
&nbsp;<br />
      <table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
		<tr>
		  <td class="cobll" bgcolor="#FFFFFF" colspan="2">
			<table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="">
			  <tr>
				<td width="40"><img src="images/fedexsmall.gif" alt="FedEx" /></td><td align="center">&nbsp;<br /><font size="4"><strong>FedEx<small>&reg;</small> Tracking Tool</strong></font><br />&nbsp;</td><td width="40">&nbsp;</td>
			  </tr>
			</table>
		  </td>
		</tr>
<?php
function getAddress($u, &$theAddress){
	$signedby = "";
	for($l = 0;$l < $u->length; $l++){
		//print "AddName : " . $u->nodeName[$l] . ", AddVal : " . $u->nodeValue[$l] . "<br />";
		if($u->nodeName[$l] == "AddressLine1")
			$addressline1 = $u->nodeValue[$l];
		elseif($u->nodeName[$l] == "AddressLine2")
			$addressline2 = $u->nodeValue[$l];
		elseif($u->nodeName[$l] == "AddressLine3")
			$addressline3 = $u->nodeValue[$l];
		elseif($u->nodeName[$l] == "City")
			$city = $u->nodeValue[$l];
		elseif($u->nodeName[$l] == "StateOrProvinceCode")
			$statecode = $u->nodeValue[$l];
		elseif($u->nodeName[$l] == "PostalCode")
			$postcode = $u->nodeValue[$l];
		elseif($u->nodeName[$l] == "CountryCode"){
			$sSQL = "SELECT countryName FROM countries WHERE countryCode='" . $u->nodeValue[$l] . "'";
			$result = mysql_query($sSQL) or print(mysql_error());
			if(mysql_num_rows($result) > 0){
				$rs = mysql_fetch_assoc($result);
				$countrycode = $rs["countryName"];
			}else
				$countrycode = $u->nodeValue[$l];
			mysql_free_result($result);
		}
	}
	$theAddress = "";
	if(@$addressline1 != "") $theAddress .= $addressline1 . "<br />";
	if(@$addressline2 != "") $theAddress .= $addressline2 . "<br />";
	if(@$addressline3 != "") $theAddress .= $addressline3 . "<br />";
	if(@$city != "") $theAddress .= $city . "<br />";
	if(@$statecode != "" && @$postcode != "")
		$theAddress .= $statecode . ", " . $postcode . "<br />";
	else{
		if(@$statecode != "") $theAddress .= $statecode . "<br />";
		if(@$postcode != "") $theAddress .= $postcode . "<br />";
	}
	if(@$countrycode != "") $theAddress .= $countrycode . "<br />";
}
function ParseFedexTrackingOutput($sXML, &$totActivity, &$deliverydate, &$serviceDesc, &$packagecount, &$shiptoaddress, &$scheddeldate, &$signedforby, &$errormsg, &$activityList){
	$noError = TRUE;
	$totalCost = 0;
	$packCost = 0;
	$index = 0;
	$errormsg = "";
	$gotxml=FALSE;
	$theaddress="";
	// print str_replace("<","<br />&lt;",$sXML) . "<br />\n";
	$xmlDoc = new vrXMLDoc($sXML);
	// Set t2 = xmlDoc.getElementsByTagName("TrackResponse").Item(0)
	$nodeList = $xmlDoc->nodeList->childNodes[0];
	for($ii = 0; $ii < $nodeList->length; $ii++){
		if($nodeList->nodeName[$ii]=="TrackProfile"){ // no Top-level Error
			$e = $nodeList->childNodes[$ii];
			for($i = 0;$i < $e->length; $i++){
				// print "Nodename is : " . $e->nodeName[$i] . "<br />";
				switch($e->nodeName[$i]){
					case "SoftError":
						$t = $e->childNodes[$i];
						for($k = 0; $k < $t->length; $k++){
							if($t->nodeName[$k] == "Message"){
								$noError = FALSE;
								$shipperNo = $t->nodeValue[$k];
							}
						}
					break;
					case "SignedForBy":
						$signedforby = $e->nodeValue[$i];
					break;
					case "DestinationAddress":
						getAddress($e, $shiptoaddress);
					break;
					case "DeliveredDate":
						$deliverydate = $e->nodeValue[$i] . $deliverydate;
					break;
					case "DeliveredTime":
						$deliverydate .= ' ' . $e->nodeValue[$i];
					break;
					case "Service":
						$serviceDesc = $e->nodeValue[$i];
					break;
					case "PackageCount":
						$packagecount = $e->nodeValue[$i];
					break;
					case "Scan":
						getAddress($e, $activityList[$totActivity][0]);
						$t = $e->childNodes[$i];
						for($k = 0; $k < $t->length; $k++){
							if($t->nodeName[$k] == "Date"){
								$activityList[$totActivity][6] = $t->nodeValue[$k];
							}elseif($t->nodeName[$k] == "Time"){
								$activityList[$totActivity][7] = $t->nodeValue[$k];
							}elseif($t->nodeName[$k] == "StatusExceptionCode"){
								$activityList[$totActivity][3] = $t->nodeValue[$k];
							}elseif($t->nodeName[$k] == "ScanDescription" || $t->nodeName[$k] == "StatusExceptionDescription"){
								if($t->nodeValue[$k] != "Package status") $activityList[$totActivity][4] = $t->nodeValue[$k];
							}
						}
						if($activityList[$totActivity][4] != '') $totActivity++;
					break;
				}
			}
		}
	}
	return $noError;
}
function FedexTrack($trackNo){
	global $fedexaccount,$fedexmeter,$maintablewidth;
	// activityList(100,10)
	// ActivityList(0) = Address
	// ActivityList(1) = SignedForByName
	// ActivityList(2) = Not Used
	// ActivityList(3) = Activity -> Status -> StatusType -> Code
	// ActivityList(4) = Activity -> Status -> StatusType -> Description
	// ActivityList(5) = Activity -> Status -> StatusCode -> Code
	// ActivityList(6) = Activity -> Date
	// ActivityList(7) = Activity -> Time
	$lastloc="xxxxxx";
	$success = true;
	$sXML = '<?xml version="1.0" encoding="UTF-8" ?>';
	$sXML .= '<FDXTrackRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="FDXTrackRequest.xsd">';
	$sXML .= '<RequestHeader>';
	$sXML .= '<CustomerTransactionIdentifier>transidentifier</CustomerTransactionIdentifier>';
	$sXML .= '<AccountNumber>' . $fedexaccount . '</AccountNumber>';
	$sXML .= '<MeterNumber>' . $fedexmeter . '</MeterNumber>';
	$sXML .= '<CarrierCode></CarrierCode>';
	$sXML .= '</RequestHeader>';
	$sXML .= '<PackageIdentifier>';
	$sXML .= '<Value>' . $trackNo . '</Value>';
	$sXML .= '<Type>TRACKING_NUMBER_OR_DOORTAG</Type>';
	$sXML .= '</PackageIdentifier>';
	if(trim(@$_POST["activity"])=="LAST") $sXML .= '<DetailScans>0</DetailScans>'; else $sXML .= '<DetailScans>1</DetailScans>';
	$sXML .= '</FDXTrackRequest>';
	$success = callcurlfunction('https://gateway.fedex.com:443/GatewayDC', $sXML, $xmlres, '', $errormsg, FALSE);
	if($success){
		$totActivity = 0;
		$success = ParseFedexTrackingOutput($xmlres, $totActivity, $deliverydate, $serviceDesc, $packagecount, $shiptoaddress, $scheduleddeliverydate, $signedforby, $errormsg, $activityList);
		if($success){
			for($index2=0; $index2 < $totActivity-1; $index2++){
				for($index=0; $index < $totActivity-1; $index++){
					if(($activityList[$index][6] . $activityList[$index][7]) > ($activityList[$index+1][6] . $activityList[$index+1][7])){
						$tempArr = $activityList[$index];
						$activityList[$index]=$activityList[$index+1];
						$activityList[$index+1]=$tempArr;
					}
				}
			}
			if(trim($serviceDesc) != ""){ ?>
	  <tr>
		<td class="cobhl" bgcolor="#EBEBEB" width="30%"><strong>Service Description</strong> </td>
		<td class="cobll" bgcolor="#FFFFFF"><?php print $serviceDesc?></td>
	  </tr>
	<?php	}
			if(trim($packagecount) != ""){ ?>
	  <tr>
		<td class="cobhl" bgcolor="#EBEBEB" width="30%" valign="top"><strong>Package Count</strong> </td>
		<td class="cobll" bgcolor="#FFFFFF"><?php print $packagecount?></td>
	  </tr>
	<?php	}
			if(trim($shiptoaddress) != ""){ ?>
	  <tr>
		<td class="cobhl" bgcolor="#EBEBEB" width="30%" valign="top"><strong>Ship-To Address</strong> </td>
		<td class="cobll" bgcolor="#FFFFFF"><?php print $shiptoaddress?></td>
	  </tr>
	<?php	}
			if(trim($signedforby) != ""){ ?>
	  <tr>
		<td class="cobhl" bgcolor="#EBEBEB" width="30%" valign="top"><strong>Signed For By</strong> </td>
		<td class="cobll" bgcolor="#FFFFFF"><?php print $signedforby?></font></td>
	  </tr>
	<?php	}
			if(trim($deliverydate) != ""){ ?>
	  <tr>
		<td class="cobhl" bgcolor="#EBEBEB" width="30%" valign="top"><strong>Delivery Date</strong> </td>
		<td class="cobll" bgcolor="#FFFFFF"><?php print $deliverydate?></font></td>
	  </tr>
	<?php	} ?>
			</table>
	  &nbsp;
			<table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB"><strong>Location</strong></td>
				<td class="cobhl" bgcolor="#EBEBEB"><strong>Description</strong></td>
				<td class="cobhl" bgcolor="#EBEBEB"><strong>Date&nbsp;/&nbsp;Time</strong></td>
			  </tr>
<?php
	for($index=0; $index < $totActivity; $index++){ 
		if(($index % 2) == 0)
			$cellbg='class="cobll" bgcolor="#FFFFFF"';
		else
			$cellbg='class="cobhl" bgcolor="#EBEBEB"';
?>
			  <tr>
			    <td <?php print $cellbg?>><font size="1"><?php
									if($lastloc==$activityList[$index][0])
										print '<p align="center">"</p>';
									else{
										print $activityList[$index][0];
										$lastloc = $activityList[$index][0];
									} ?></font></td>
				<td <?php print $cellbg?>><font size="1"><?php print $activityList[$index][4];
									if(@$activityList[$index][1] != "") print "<br /><strong>Signed By :</strong> " . $activityList[$index][1]; ?></font></td>
				<td <?php print $cellbg?>><font size="1"><?php
					$theDate = $activityList[$index][6];
					$theTime = $activityList[$index][7];
					print $theDate . '<br />' . $theTime;?></font></td>
			  </tr>
<?php
	} ?>
			</table>
	  <hr width="70%">
	  <table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
<?php
		}else{
?>
			  <tr>
			    <td class="cobll" bgcolor="#FFFFFF" colspan="2" height="30" align="center"><strong>The tracking system returned the following error : <?php print $errormsg?></strong></td>
			  </tr>
			</table>
	  <hr width="70%">
	  <table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
<?php
		}
	}
	return $success;
}
if(trim(@$_POST["trackno"]) != "")
	FedexTrack(trim(@$_POST["trackno"]));
?>
			<form method="post" name="trackform" action="tracking.php">
			  <input type="hidden" name="carrier" value="fedex">
			  <tr>
			    <td class="cobhl" width="50%" bgcolor="#EBEBEB" align="right">Please enter your FedEx Tracking Number : </td>
				<td class="cobll" width="50%" bgcolor="#FFFFFF"><input type="text" size="30" name="trackno" value="<?php print trim(@$_REQUEST["trackno"])?>" /></td>
			  </tr>
			  <tr>
			    <td class="cobhl" width="50%" bgcolor="#EBEBEB" align="right">Show Activity : </td>
				<td class="cobll" width="50%" bgcolor="#FFFFFF"><select name="activity" size="1"><option value="LAST">Show Last Activity Only</option><option value="ALL"<?php if(trim(@$_POST["activity"])=="ALL") print " selected"?>>Show All Activity</option></select></td>
			  </tr>
			  <tr>
			    <td class="cobll" bgcolor="#FFFFFF" colspan="2"><table width="100%" cellspacing="0" cellpadding="0" border="0">
				    <tr>
					  <td class="cobll" bgcolor="#FFFFFF" width="17%" height="26">&nbsp;</td>
					  <td class="cobll" bgcolor="#FFFFFF" width="66%" align="center"><input type="submit" value="Track Package" /></td>
					  <td class="cobll" bgcolor="#FFFFFF" width="17%" height="26" align="right" valign="bottom"><img src="images/tablebr.gif" alt="" /></td>
					</tr>
				  </table></td>
			  </tr>
			</form>
			</table>
	  <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="#FFFFFF" align="center">
        <tr>
          <td width="100%" align="center"><p>&nbsp;<br /><font size="1">FedEx&reg; is a registered service mark of Federal Express Corporation.<br />
			FedEx logos used by permission. All rights reserved.</font></p></td>
		</tr>
	  </table>
<br />
<?php
}else{ // undecided
?>
&nbsp;<br />
	  <table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
		<tr>
		  <td class="cobll" bgcolor="#FFFFFF" colspan="2">
			<table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="">
			  <tr>
				<td width="98" align="left"><?php if($incupscopyright){ ?><img src="images/LOGO_S.gif" alt="UPS" /><?php }else{ print '&nbsp;';} ?></td><td align="center">&nbsp;<br /><font size="4"><strong>Please select your shipping carrier.</strong></font><br />&nbsp;</td><td width="98"><?php if($incfedexcopyright){ ?><img src="images/fedexsmall.gif" alt="FedEx" /><?php }else{ print "&nbsp;"; } ?></td>
			  </tr>
			</table>
		  </td>
		</tr>
<?php	if(@$shipType==4 || @$alternateratesups != ''){ ?>
		<form method="post" action="tracking.php">
		  <input type="hidden" name="carrier" value="ups">
		  <tr>
			<td class="cobhl" width="50%" bgcolor="#EBEBEB" align="right">Products shipped via UPS : </td>
			<td class="cobll" width="50%" bgcolor="#FFFFFF"><input type="submit" value="<?php print $xxGo?>" /></td>
		  </tr>
		</form>
<?php	}
		if($shipType==3 || @$alternateratesusps != ''){ ?>
		<form method="post" action="tracking.php">
		  <input type="hidden" name="carrier" value="usps">
		  <tr>
			<td class="cobhl" width="50%" bgcolor="#EBEBEB" align="right">Products shipped via USPS : </td>
			<td class="cobll" width="50%" bgcolor="#FFFFFF"><input type="submit" value="<?php print $xxGo?>" /></td>
		  </tr>
		</form>
<?php	}
		if($shipType==7 || @$alternateratesfedex != ''){ ?>
		<form method="post" action="tracking.php">
		  <input type="hidden" name="carrier" value="fedex">
		  <tr>
			<td class="cobhl" width="50%" bgcolor="#EBEBEB" align="right">Products shipped via FedEx : </td>
			<td class="cobll" width="50%" bgcolor="#FFFFFF"><input type="submit" value="<?php print $xxGo?>" /></td>
		  </tr>
		</form>
<?php	} ?>
	  </table>
	  <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="#FFFFFF" align="center">
		<tr><td>&nbsp;</td></tr>
<?php	if($incupscopyright){ ?>
        <tr>
          <td width="100%" align="center"><p>&nbsp;<br /><font size="1">UPS&reg;, UPS & Shield Design&reg; and UNITED PARCEL SERVICE&reg; 
				  are<br />registered trademarks of United Parcel Service of America, Inc.</font></p></td>
		</tr>
<?php	}
		if($incfedexcopyright){ ?>
		<tr>
          <td width="100%" align="center"><p>&nbsp;<br /><font size="1">FedEx&reg; is a registered service mark of Federal Express Corporation.<br />
			FedEx logos used by permission. All rights reserved.</font></p></td>
		</tr>
<?php	} ?>
	  </table>
	  <br />
<?php
}
?>
