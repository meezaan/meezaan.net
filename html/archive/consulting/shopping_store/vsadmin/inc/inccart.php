<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protected under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$cartisincluded!=TRUE) include "./vsadmin/inc/uspsshipping.php";
$cartEmpty=FALSE;
$isInStock=TRUE;
$outofstockreason=0;
if(@$dateadjust=='') $dateadjust=0;
$errormsg = '';
$demomode = FALSE;
$WSP = $OWSP = '';
$nodiscounts=FALSE;
$maxshipoptions=20;
$success=TRUE;
$usehst = $checkIntOptions=FALSE;
$alldata = '';
$shipMethod = '';
$shipping = 0;
$iTotItems = 0;
$iWeight = 0;
$stateTaxRate=0;
$countryTax=0;
$stateTax=0;
$appliedcouponname = $ordAVS = $ordCVV = $stateAbbrev = $international = '';
$thePQuantity = 0;
$thePWeight = 0;
$appliedcouponamount = $totalquantity = $statetaxfree = $countrytaxfree = $shipfreegoods = $totalgoods = 0;
$somethingToShip = FALSE; $freeshippingapplied = FALSE; $freeshipamnt = 0; $rowcounter = 0;
$gotcpncode=FALSE; $isstandardship = FALSE; $numshipoptions=0; $homecountry = FALSE; $totalshipitems = 0; $stockrelitems = 0;
$payerid = $cpncode = $token = $checkoutmode = $shippingpost = $commerciallocpost = $wantinsurancepost = $payproviderpost = '';
if(@$cartisincluded != TRUE){
	if(@$_SERVER['CONTENT_LENGTH'] != '' && $_SERVER['CONTENT_LENGTH'] > 100000) exit;
	$cartisincluded=FALSE;
	$cpncode = trim(str_replace("'",'',@$_REQUEST['cpncode']));
	if(@$_POST['payerid'] != '') $payerid = $_POST['payerid']; else $payerid = '';
	$token = trim(@$_REQUEST['token']);
	if(trim(@$_POST['sessionid']) != '') $thesessionid=str_replace("'",'',trim($_POST['sessionid'])); else $thesessionid=session_id();
	$theid = mysql_escape_string(trim(@$_POST['id']));
	$checkoutmode	   = trim(@$_POST['mode']);
	$shippingpost	   = trim(@$_POST['shipping']);
	$commerciallocpost = trim(@$_POST['commercialloc']);
	$wantinsurancepost = trim(@$_POST['wantinsurance']);
	$payproviderpost   = trim(@$_POST['payprovider']);
}
$paypalexpress=FALSE;
$ppexpresscancel=FALSE;
function getipaddress(){
	if(trim(@$_SERVER['HTTP_X_FORWARDED_FOR'])!=''){
		$ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		$ip = explode(':', $ip[0]);
		return($ip[0]);
	}else
		return(@$_SERVER['REMOTE_ADDR']);
}
function get_wholesaleprice_sql(){
	global $WSP,$OWSP,$wholesaleoptionpricediff,$nowholesalediscounts,$nodiscounts;
	if(@$_SESSION['clientUser'] != ''){
		if(($_SESSION['clientActions'] & 8) == 8){
			$WSP = 'pWholesalePrice AS ';
			if(@$wholesaleoptionpricediff==TRUE) $OWSP = 'optWholesalePriceDiff AS ';
			if(@$nowholesalediscounts==TRUE) $nodiscounts=TRUE;
		}
		if(($_SESSION['clientActions'] & 16) == 16){
			$WSP = $_SESSION['clientPercentDiscount'] . '*pPrice AS ';
			if(@$wholesaleoptionpricediff==TRUE) $OWSP = $_SESSION['clientPercentDiscount'] . '*optPriceDiff AS ';
			if(@$nowholesalediscounts==TRUE) $nodiscounts=TRUE;
		}
	}
}
get_wholesaleprice_sql();
$alreadygotadmin = getadminsettings();
$countryTax=0; // At present both countryTaxRate and countryTax are set in incfunctions
$origShipType=$shipType;
if(@$cartisincluded != TRUE){
	if(@$alternateratesups != '' || @$alternateratesusps != '' || @$alternateratesweightbased != '' || @$alternateratescanadapost !='' || @$alternateratesfedex !='') $alternaterates=TRUE; else $alternaterates=FALSE;
	if(@$_POST['altrates'] != ''){
		$altrate=(int)@$_POST['altrates'];
		if(@$alternateratesups != '' && $altrate==4) $shipType=4;
		if(@$alternateratesusps != '' && $altrate==3) $shipType=3;
		if(@$alternateratesweightbased != '' && $altrate==2) $shipType=2;
		if(@$alternateratescanadapost != '' && $altrate==6) $shipType=6;
		if(@$alternateratesfedex != '' && $altrate==7) $shipType=7;
	}
	$ordPayProvider = str_replace("'",'',$payproviderpost);
}
if($ordPayProvider != '') eval('$handling += @$handlingcharge' . $ordPayProvider . ';$handlingchargepercent=@$handlingchargepercent' . $ordPayProvider . ';');
if(@$_SESSION["couponapply"] != ''){
	mysql_query("UPDATE coupons SET cpnNumAvail=cpnNumAvail+1 WHERE cpnID IN (0" . $_SESSION["couponapply"] . ")") or print(mysql_error());
	$_SESSION["couponapply"]='';
}
function show_states($tstate){
	global $xxOutState,$allstates,$numallstates,$usestateabbrev;
	$foundmatch=FALSE;
	if($xxOutState!='') print '<option value="">' . $xxOutState . '</option>';
	for($index=0;$index<$numallstates;$index++){
		print '<option value="' . str_replace('"','&quot;',(@$usestateabbrev==TRUE?$allstates[$index]['stateAbbrev']:$allstates[$index]['stateName'])) . '"';
		if($tstate==$allstates[$index]['stateName'] || $tstate==$allstates[$index]['stateAbbrev']){
			print ' selected';
			$foundmatch=TRUE;
		}
		print '>' . $allstates[$index]['stateName'] . "</option>\n";
	}
	return $foundmatch;
}
function show_countries($tcountry){
	global $numhomecountries,$nonhomecountries,$allcountries,$numallcountries;
	for($index=0;$index<$numallcountries;$index++){
		print '<option value="' . str_replace('"','&quot;',$allcountries[$index]["countryName"]) . '"';
		if($tcountry==$allcountries[$index]["countryName"]) print " selected";
		print '>' . $allcountries[$index][2] . "</option>\n";
	}
}
function checkuserblock($thepayprov){
	global $blockmultipurchase,$multipurchaseblockmessage;
	if(@$multipurchaseblockmessage=='') $multipurchaseblockmessage="I'm sorry. We are experiencing temporary difficulties at the moment. Please try your purchase again later.";
	$multipurchaseblocked=FALSE;
	if($thepayprov != "7" && $thepayprov != "13"){
		$theip = @$_SERVER["REMOTE_ADDR"];
		if($theip == '') $theip = "none";
		if(@$blockmultipurchase != ''){
			mysql_query("DELETE FROM multibuyblock WHERE lastaccess<'" . date("Y-m-d H:i:s", time()-(60*60*24)) . "'") or print(mysql_error());
			$sSQL = "SELECT ssdenyid,sstimesaccess FROM multibuyblock WHERE ssdenyip = '" . trim(mysql_escape_string($theip)) . "'";
			$result = mysql_query($sSQL) or print(mysql_error());
			if($rs = mysql_fetch_array($result)){
				mysql_query("UPDATE multibuyblock SET sstimesaccess=sstimesaccess+1,lastaccess='" . date("Y-m-d H:i:s", time()) . "' WHERE ssdenyid=" . $rs["ssdenyid"]) or print(mysql_error());
				if($rs["sstimesaccess"] >= $blockmultipurchase) $multipurchaseblocked=TRUE;
			}else{
				mysql_query("INSERT INTO multibuyblock (ssdenyip,lastaccess) VALUES ('" . trim(mysql_escape_string($theip)) . "','" . date("Y-m-d H:i:s", time()) . "')") or print(mysql_error());
			}
			mysql_free_result($result);
		}
		if($theip == "none")
			$sSQL = "SELECT dcid FROM ipblocking LIMIT 0,1";
		else
			$sSQL = "SELECT dcid FROM ipblocking WHERE (dcip1=" . ip2long($theip) . " AND dcip2=0) OR (dcip1 <= " . ip2long($theip) . " AND " . ip2long($theip) . " <= dcip2 AND dcip2 <> 0)";
		$result = mysql_query($sSQL) or print(mysql_error());
		if(mysql_num_rows($result) > 0)
			$multipurchaseblocked = TRUE;
	}
	return($multipurchaseblocked);
}
function checkpricebreaks($cpbpid,$origprice){
	global $WSP;
	$newprice='';
	$sSQL = "SELECT SUM(cartQuantity) AS totquant FROM cart WHERE cartCompleted=0 AND " . getsessionsql() . " AND cartProdID='".mysql_escape_string($cpbpid)."'";
	$result = mysql_query($sSQL) or print(mysql_error());
	$rs=mysql_fetch_assoc($result);
	if(is_null($rs['totquant'])) $thetotquant=0; else $thetotquant = $rs['totquant'];
	$sSQL="SELECT ".$WSP."pPrice FROM pricebreaks WHERE ".$thetotquant.">=pbQuantity AND pbProdID='".mysql_escape_string($cpbpid)."' ORDER BY " . ($WSP==''?"pPrice":str_replace(' AS ','',$WSP));
	$result = mysql_query($sSQL) or print(mysql_error());
	if($rs=mysql_fetch_assoc($result)) $thepricebreak = $rs['pPrice']; else $thepricebreak = $origprice;
	$sSQL = "UPDATE cart SET cartProdPrice=".$thepricebreak." WHERE cartCompleted=0 AND " . getsessionsql() . " AND cartProdID='".mysql_escape_string($cpbpid)."'";
	mysql_query($sSQL) or print(mysql_error());
}
function multShipWeight($theweight, $themul){
	return(($theweight*$themul)/100.0);
}
function subtaxesfordiscounts($theExemptions, $discAmount){
	global $statetaxfree,$countrytaxfree,$shipfreegoods;
	if(($theExemptions & 1)==1) $statetaxfree -= $discAmount;
	if(($theExemptions & 2)==2) $countrytaxfree -= $discAmount;
	if(($theExemptions & 4)==4) $shipfreegoods -= $discAmount;
}
function addadiscount($resset, $groupdiscount, $dscamount, $subcpns, $cdcpncode, $statetaxhandback, $countrytaxhandback, $theexemptions, $thetax){
	global $totaldiscounts, $cpnmessage, $statetaxfree, $countrytaxfree, $gotcpncode, $perproducttaxrate, $countryTax, $appliedcouponname, $appliedcouponamount;
	$totaldiscounts += $dscamount;
	if($groupdiscount){
		$statetaxfree -= ($dscamount * $statetaxhandback);
		$countrytaxfree -= ($dscamount * $countrytaxhandback);
	}else{
		subtaxesfordiscounts($theexemptions, $dscamount);
		if(@$perproducttaxrate) $countryTax -= (($dscamount * $thetax) / 100.0);
	}
	if(stristr($cpnmessage,"<br />" . $resset[getlangid("cpnName",1024)] . "<br />") == FALSE) $cpnmessage .= $resset[getlangid("cpnName",1024)] . "<br />";
	if($subcpns){
		$theres = mysql_query("SELECT cpnID FROM coupons WHERE cpnNumAvail>0 AND cpnNumAvail<30000000 AND cpnID=" . $resset["cpnID"]) or print(mysql_error());
		if($theresset = mysql_fetch_assoc($theres)) @$_SESSION["couponapply"] .= "," . $resset["cpnID"];
		mysql_query("UPDATE coupons SET cpnNumAvail=cpnNumAvail-1 WHERE cpnNumAvail>0 AND cpnNumAvail<30000000 AND cpnID=" . $resset["cpnID"]) or print(mysql_error());
	}
	if($cdcpncode!='' && strtolower(trim($resset['cpnNumber']))==strtolower($cdcpncode)){ $gotcpncode=TRUE; $appliedcouponname = $resset['cpnName']; $appliedcouponamount = $dscamount; }
}
function timesapply($taquant,$tathresh,$tamaxquant,$tamaxthresh,$taquantrepeat,$tathreshrepeat){
	if($taquantrepeat==0 && $tathreshrepeat==0)
		$tatimesapply = 1.0;
	elseif($tamaxquant==0)
		$tatimesapply = (int)(($tathresh - $tamaxthresh) / $tathreshrepeat)+1;
	elseif($tamaxthresh==0)
		$tatimesapply = (int)(($taquant - $tamaxquant) / $taquantrepeat)+1;
	else{
		$ta1 = (int)(($taquant - $tamaxquant) / $taquantrepeat)+1;
		$ta2 = (int)(($tathresh - $tamaxthresh) / $tathreshrepeat)+1;
		if($ta2 < $ta1) $tatimesapply = $ta2; else $tatimesapply = $ta1;
	}
	return($tatimesapply);
}
function calculatediscounts($cdgndtot, $subcpns, $cdcpncode){
	global $totaldiscounts, $cpnmessage, $statetaxfree, $countrytaxfree, $nodiscounts, $WSP, $cpncode, $gotcpncode, $thesessionid, $countryTaxRate, $countryTax;
	$totaldiscounts = 0;
	$cpnmessage = "<br />";
	$cdtotquant=0;
	if($cdgndtot==0){
		$statetaxhandback = 0.0;
		$countrytaxhandback = 0.0;
	}else{
		$statetaxhandback = 1.0 - (($cdgndtot - $statetaxfree) / $cdgndtot);
		$countrytaxhandback = 1.0 - (($cdgndtot - $countrytaxfree) / $cdgndtot);
	}
	if(! $nodiscounts){
		$sSQL = "SELECT cartProdID,SUM(cartProdPrice*cartQuantity) AS thePrice,SUM(cartQuantity) AS sumQuant,pSection,COUNT(cartProdID),pExemptions,pTax FROM products INNER JOIN cart ON cart.cartProdID=products.pID WHERE cartCompleted=0 AND " . getsessionsql() . " GROUP BY cartProdID,pSection,pExemptions,pTax";
		$cdresult = mysql_query($sSQL) or print(mysql_error());
		$cdadindex=0;
		while($cdrs = mysql_fetch_assoc($cdresult)){
			$cdalldata[$cdadindex++]=$cdrs;
		}
		for($index=0; $index<$cdadindex; $index++){
			$cdrs = $cdalldata[$index];
			$sSQL = "SELECT SUM(coPriceDiff*cartQuantity) AS totOpts FROM cart LEFT OUTER JOIN cartoptions ON cart.cartID=cartoptions.coCartID WHERE cartCompleted=0 AND " . getsessionsql() . " AND cartProdID='" . $cdrs["cartProdID"] . "'";
			$cdresult2 = mysql_query($sSQL) or print(mysql_error());
			$cdrs2 = mysql_fetch_assoc($cdresult2);
			if(! is_null($cdrs2["totOpts"])) $cdrs["thePrice"] += $cdrs2["totOpts"];
			$cdtotquant += $cdrs["sumQuant"];
			$topcpnids = $cdrs["pSection"];
			$thetopts = $cdrs["pSection"];
			if(is_null($cdrs["pTax"])) $cdrs["pTax"] = $countryTaxRate;
			for($cpnindex=0; $cpnindex<= 10; $cpnindex++){
				if($thetopts==0)
					break;
				else{
					$sSQL = "SELECT topSection FROM sections WHERE sectionID=" . $thetopts;
					$result2 = mysql_query($sSQL) or print(mysql_error());
					if($rs2 = mysql_fetch_assoc($result2)){
						$thetopts = $rs2["topSection"];
						$topcpnids .= "," . $thetopts;
					}else
						break;
				}
			}
			$sSQL = "SELECT DISTINCT cpnID,cpnDiscount,cpnType,cpnNumber,".getlangid("cpnName",1024).",cpnThreshold,cpnQuantity,cpnSitewide,cpnThresholdRepeat,cpnQuantityRepeat FROM coupons LEFT OUTER JOIN cpnassign ON coupons.cpnID=cpnassign.cpaCpnID WHERE cpnNumAvail>0 AND cpnEndDate>='" . date("Y-m-d",time()) ."' AND (cpnIsCoupon=0";
			if($cdcpncode != '') $sSQL .= " OR (cpnIsCoupon=1 AND cpnNumber='" . $cdcpncode . "')";
			$sSQL .= ") AND cpnThreshold<=" . $cdrs["thePrice"] . " AND (cpnThresholdMax>" . $cdrs["thePrice"] . " OR cpnThresholdMax=0) AND cpnQuantity<=" . $cdrs["sumQuant"] . " AND (cpnQuantityMax>" . $cdrs["sumQuant"] . " OR cpnQuantityMax=0) AND (cpnSitewide=0 OR cpnSitewide=2) AND ";
			$sSQL .= "(cpnSitewide=2 OR (cpaType=2 AND cpaAssignment='" . $cdrs["cartProdID"] . "') ";
			$sSQL .= "OR (cpaType=1 AND cpaAssignment IN ('" . str_replace(",","','",$topcpnids) . "')))";
			$result2 = mysql_query($sSQL) or print(mysql_error());
			while($rs2 = mysql_fetch_assoc($result2)){
				if($rs2["cpnType"]==1){ // Flat Rate Discount
					$thedisc = (double)$rs2["cpnDiscount"] * timesapply($cdrs["sumQuant"], $cdrs["thePrice"], $rs2["cpnQuantity"], $rs2["cpnThreshold"], $rs2["cpnQuantityRepeat"], $rs2["cpnThresholdRepeat"]);
					if($cdrs["thePrice"] < $thedisc) $thedisc = $cdrs["thePrice"];
					addadiscount($rs2, FALSE, $thedisc, $subcpns, $cdcpncode, $statetaxhandback, $countrytaxhandback, $cdrs["pExemptions"], $cdrs["pTax"]);
				}elseif($rs2["cpnType"]==2){ // Percentage Discount
					addadiscount($rs2, FALSE, (((double)$rs2["cpnDiscount"] * (double)$cdrs["thePrice"]) / 100.0), $subcpns, $cdcpncode, $statetaxhandback, $countrytaxhandback, $cdrs["pExemptions"], $cdrs["pTax"]);
				}
			}
		}
		$sSQL = "SELECT DISTINCT cpnID,cpnDiscount,cpnType,cpnNumber,".getlangid("cpnName",1024).",cpnSitewide,cpnThreshold,cpnThresholdMax,cpnQuantity,cpnQuantityMax,cpnThresholdRepeat,cpnQuantityRepeat FROM coupons WHERE cpnNumAvail>0 AND cpnEndDate>='" . date("Y-m-d",time()) ."' AND (cpnIsCoupon=0";
		if($cdcpncode != '') $sSQL .= " OR (cpnIsCoupon=1 AND cpnNumber='" . $cdcpncode . "')";
		$sSQL .= ") AND cpnThreshold<=" . $cdgndtot . " AND cpnQuantity<=" . $cdtotquant . " AND (cpnSitewide=1 OR cpnSitewide=3) AND (cpnType=1 OR cpnType=2)";
		$result2 = mysql_query($sSQL) or print(mysql_error());
		while($rs2 = mysql_fetch_assoc($result2)){
			$totquant = 0;
			$totprice = 0;
			if($rs2["cpnSitewide"]==3){
				$sSQL = "SELECT cpaAssignment FROM cpnassign WHERE cpaType=1 AND cpacpnID=" . $rs2["cpnID"];
				$result3 = mysql_query($sSQL) or print(mysql_error());
				$secids = '';
				$addcomma = '';
				while($rs3 = mysql_fetch_assoc($result3)){
					$secids .= $addcomma . $rs3["cpaAssignment"];
					$addcomma = ",";
				}
				if($secids != ''){
					$secids = getsectionids($secids, FALSE);
					$sSQL = "SELECT SUM(cartProdPrice*cartQuantity) AS totPrice,SUM(cartQuantity) AS totQuant FROM products INNER JOIN cart ON cart.cartProdID=products.pID WHERE cartCompleted=0 AND " . getsessionsql() . " AND products.pSection IN (" . $secids . ")";
					$result3 = mysql_query($sSQL) or print(mysql_error());
					$rs3 = mysql_fetch_assoc($result3);
					if(is_null($rs3["totPrice"])) $totprice = 0; else $totprice = $rs3["totPrice"];
					if(is_null($rs3["totQuant"])) $totquant=0; else $totquant = $rs3["totQuant"];
					$sSQL = "SELECT SUM(coPriceDiff*cartQuantity) AS optPrDiff FROM products INNER JOIN cart ON cart.cartProdID=products.pID LEFT OUTER JOIN cartoptions ON cart.cartID=cartoptions.coCartID WHERE cartCompleted=0 AND " . getsessionsql() . " AND products.pSection IN (" . $secids . ")";
					$result3 = mysql_query($sSQL) or print(mysql_error());
					$rs3 = mysql_fetch_assoc($result3);
					if(! is_null($rs3["optPrDiff"])) $totprice = $totprice+$rs3["optPrDiff"];
				}
			}else{ // cpnSitewide==1
				$totquant = $cdtotquant;
				$totprice = $cdgndtot;
			}
			if($totquant > 0 && $rs2["cpnThreshold"] <= $totprice && ($rs2["cpnThresholdMax"] > $totprice || $rs2["cpnThresholdMax"]==0) && $rs2["cpnQuantity"] <= $totquant && ($rs2["cpnQuantityMax"] > $totquant || $rs2["cpnQuantityMax"]==0)){
				if($rs2["cpnType"]==1){ // Flat Rate Discount
					$thedisc = (double)$rs2["cpnDiscount"] * timesapply($totquant, $totprice, $rs2["cpnQuantity"], $rs2["cpnThreshold"], $rs2["cpnQuantityRepeat"], $rs2["cpnThresholdRepeat"]);
					if($totprice < $thedisc) $thedisc = $totprice;
				}elseif($rs2["cpnType"]==2){ // Percentage Discount
					$thedisc = ((double)$rs2["cpnDiscount"] * (double)$totprice) / 100.0;
				}
				addadiscount($rs2, TRUE, $thedisc, $subcpns, $cdcpncode, $statetaxhandback, $countrytaxhandback, 3, 0);
				if(@$perproducttaxrate && $cdgndtot > 0){
					for($index=0; $index<$cdadindex; $index++){
						$cdrs = $cdalldata[$index];
						if($rs2["cpnType"]==1) // Flat Rate Discount
							$applicdisc = $thedisc / ($cdtotquant / $cdrs["sumQuant"]);
						elseif($rs2["cpnType"]==2) // Percentage Discount
							$applicdisc = $thedisc / ($cdgndtot / $cdrs["thePrice"]);
						if(($cdrs["pExemptions"] & 2) != 2) $countryTax -= (($applicdisc * $cdrs["pTax"]) / 100.0);
					}
				}
			}
		}
	}
	if($statetaxfree < 0) $statetaxfree = 0;
	if($countrytaxfree < 0) $countrytaxfree = 0;
	$totaldiscounts = round($totaldiscounts, 2);
}
function calculateshippingdiscounts($subcpns){
	global $freeshippingapplied, $nodiscounts, $totalgoods, $totalquantity, $cpncode, $freeshipapplies, $isstandardship, $cpnmessage, $shipping, $freeshipamnt, $gotcpncode;
	$freeshipamnt = 0;
	if(! $nodiscounts){
		$sSQL = "SELECT cpnID,".getlangid("cpnName",1024).",cpnNumber,cpnDiscount,cpnThreshold,cpnCntry FROM coupons WHERE cpnType=0 AND cpnSitewide=1 AND cpnNumAvail>0 AND cpnThreshold<=".$totalgoods." AND (cpnThresholdMax>".$totalgoods." OR cpnThresholdMax=0) AND cpnQuantity<=".$totalquantity." AND (cpnQuantityMax>".$totalquantity." OR cpnQuantityMax=0) AND cpnEndDate>='" . date("Y-m-d",time()) ."' AND (cpnIsCoupon=0 OR (cpnIsCoupon=1 AND cpnNumber='".$cpncode."'))";
		$result = mysql_query($sSQL) or print(mysql_error());
		while($rs=mysql_fetch_assoc($result)){
			if($freeshipapplies || (int)$rs["cpnCntry"]==0){
				if($cpncode!='' && strtolower(trim($rs['cpnNumber']))==strtolower($cpncode)){ $gotcpncode=TRUE; $appliedcouponname = $rs['cpnName']; }
				if($isstandardship){
					if(stristr($cpnmessage,"<br />" . $rs[getlangid("cpnName",1024)] . "<br />") == FALSE) $cpnmessage .= $rs[getlangid("cpnName",1024)] . "<br />";
					$freeshipamnt = $shipping;
					if($subcpns){
						$theres = mysql_query("SELECT cpnID FROM coupons WHERE cpnNumAvail>0 AND cpnNumAvail<30000000 AND cpnID=" . $rs["cpnID"]) or print(mysql_error());
						if($theresset = mysql_fetch_assoc($theres)) @$_SESSION["couponapply"] .= "," . $rs["cpnID"];
						mysql_query("UPDATE coupons SET cpnNumAvail=cpnNumAvail-1 WHERE cpnNumAvail>0 AND cpnNumAvail<30000000 AND cpnID=" . $rs["cpnID"]) or print(mysql_error());
					}
				}
				$freeshippingapplied = true;
			}
		}
		mysql_free_result($result);
	}
	if($freeshipamnt > $shipping) $freeshipamnt = $shipping;
}
function initshippingmethods(){
	global $shipType,$adminIntShipping,$allzones,$numzones,$splitUSZones,$shiphomecountry,$numshipoptions,$pzFSA,$intShipping,$success,$errormsg,$commercialloc,$codpaymentprovider,$signaturerelease,$allowsignaturerelease,$signatureoption;
	global $uspsmethods,$numuspsmeths,$international,$shipcountry,$maxshipoptions,$origCountry,$willpickuptext,$shipstate,$xxNoMeth,$shipinsuranceamt,$fedexaccount,$fedexmeter,$stateAbbrev,$shipStateAbbrev,$usestateabbrev,$payproviderpost;
	global $sXML,$uspsUser,$uspsPw,$upsAccess,$upsUser,$upsPw,$upspickuptype,$origZip,$origCountryCode,$destZip,$shipCountryCode,$adminCanPostUser,$packaging,$adminUnits,$xxPlsSta,$homedelivery,$originstatecode,$commerciallocpost;
	for($i=0; $i < $maxshipoptions; $i++){
		$intShipping[$i][0]=''; // Name
		$intShipping[$i][1]=''; // Delivery
		$intShipping[$i][2]=0; // Cost
		$intShipping[$i][3]=0; // Used
		$intShipping[$i][4]=0; // FSA
		$intShipping[$i][5]=''; // Name to match (USPS)
	}
	if($shipcountry != $origCountry){
		$international = 'Intl';
		$willpickuptext = '';
		if($adminIntShipping != 0 && @$_POST['altrates']=='') $shipType=$adminIntShipping;
	}
	if($shipType==2 || $shipType==5){ // Weight / Price based shipping
		$allzones='';
		$index=0;
		$numzones=0;
		$zoneid=0;
		if($splitUSZones && $shiphomecountry)
			$sSQL = "SELECT pzID,pzMultiShipping,pzFSA,pzMethodName1,pzMethodName2,pzMethodName3,pzMethodName4,pzMethodName5 FROM states INNER JOIN postalzones ON postalzones.pzID=states.stateZone WHERE stateName='" . mysql_escape_string($shipstate) . "' OR stateAbbrev='" . mysql_escape_string($shipstate) . "'";
		else
			$sSQL = "SELECT pzID,pzMultiShipping,pzFSA,pzMethodName1,pzMethodName2,pzMethodName3,pzMethodName4,pzMethodName5 FROM countries INNER JOIN postalzones ON postalzones.pzID=countries.countryZone WHERE countryName='" . mysql_escape_string($shipcountry) . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_array($result)){
			$zoneid=$rs["pzID"];
			$numshipoptions=$rs["pzMultiShipping"]+1;
			$pzFSA = $rs["pzFSA"];
			for($index3=0; $index3 < $numshipoptions; $index3++){
				$intShipping[$index3][0]=$rs["pzMethodName" . ($index3+1)];
				$intShipping[$index3][2]=0;
				$intShipping[$index3][3]=TRUE;
				$intShipping[$index3][4]=(($rs["pzFSA"] & (1 << $index3)) > 0 ? 1 : 0);
			}
		}else{
			$success=FALSE;
			if($splitUSZones && $shiphomecountry && $shipstate=='') $errormsg = $xxPlsSta; else $errormsg = 'Country / state shipping zone is unassigned.';
		}
		mysql_free_result($result);
		$sSQL = "SELECT zcWeight,zcRate,zcRate2,zcRate3,zcRate4,zcRate5,zcRatePC,zcRatePC2,zcRatePC3,zcRatePC4,zcRatePC5 FROM zonecharges WHERE zcZone=" . $zoneid . " ORDER BY zcWeight";
		$result = mysql_query($sSQL) or print(mysql_error());
		while($rs = mysql_fetch_row($result))
			$allzones[$index++] = $rs;
		mysql_free_result($result);
		$numzones=$index;
	}elseif($shipType==3 || $shipType==4 || $shipType==6 || $shipType==7){ // USPS / UPS / Canada Post / FedEx
		$uspsmethods='';
		$numuspsmeths=0;
		if($shipType==3){
			$sSQL = "SELECT uspsMethod,uspsFSA,uspsShowAs FROM uspsmethods WHERE uspsID<100 AND uspsUseMethod=1 AND uspsLocal=";
			if($international=='') $sSQL .= '1'; else $sSQL .= '0';
		}elseif($shipType==4){
			$shipinsuranceamt='';
			$sSQL = "SELECT uspsMethod,uspsFSA,uspsShowAs FROM uspsmethods WHERE uspsID>100 AND uspsID<200 AND uspsUseMethod=1";
		}elseif($shipType==6)
			$sSQL = "SELECT uspsMethod,uspsFSA,uspsShowAs FROM uspsmethods WHERE uspsID>200 AND uspsID<300 AND uspsUseMethod=1";
		elseif($shipType==7){
			$sSQL = "SELECT uspsMethod,uspsFSA,uspsShowAs,uspsLocal FROM uspsmethods WHERE uspsID>300 AND uspsID<400 AND uspsUseMethod=1";
			if($international=='' && $commerciallocpost=='Y') $sSQL .= " AND uspsMethod<>'GROUNDHOMEDELIVERY'";
		}
		$result = mysql_query($sSQL) or print(mysql_error());
		if(mysql_num_rows($result) > 0){
			while($rs = mysql_fetch_row($result))
				$uspsmethods[$numuspsmeths++] = $rs;
		}else{
			$success=FALSE;
			$errormsg = "Admin Error: " . $xxNoMeth;
		}
		mysql_free_result($result);
	}
	if($shipType==3)
		$sXML = "<" . $international . "RateRequest USERID=\"" . $uspsUser . "\" PASSWORD=\"" . $uspsPw . "\">";
	elseif($shipType==4){
		if($shipCountryCode=='US' && $shipStateAbbrev=='VI') $shipCountryCode='VI';
		$sXML = '<?xml version="1.0"?><AccessRequest xml:lang="en-US"><AccessLicenseNumber>' . $upsAccess . '</AccessLicenseNumber><UserId>' . $upsUser . '</UserId><Password>' . $upsPw . '</Password></AccessRequest><?xml version="1.0"?>';
		$sXML .= '<RatingServiceSelectionRequest xml:lang="en-US"><Request><TransactionReference><CustomerContext>Rating and Service</CustomerContext><XpciVersion>1.0001</XpciVersion></TransactionReference>';
		$sXML .= '<RequestAction>Rate</RequestAction><RequestOption>shop</RequestOption></Request>';
		if(@$upspickuptype!='') $sXML .= '<PickupType><Code>' . @$upspickuptype . '</Code></PickupType>';
		$sXML .= '<Shipment><Shipper><Address><PostalCode>' . $origZip . '</PostalCode><CountryCode>' . $origCountryCode . '</CountryCode></Address></Shipper>';
		$sXML .= '<ShipTo><Address><PostalCode>' . $destZip . '</PostalCode><CountryCode>' . $shipCountryCode . '</CountryCode>' . ($commercialloc!='Y' ? '<ResidentialAddress/>' : '') . '</Address></ShipTo>';
	}elseif($shipType==6){
		$sXML = '<?xml version="1.0" ?> <eparcel><language> en </language><ratesAndServicesRequest><merchantCPCID> ' . $adminCanPostUser . ' </merchantCPCID><fromPostalCode> ' . $origZip . ' </fromPostalCode><lineItems>';
	}elseif($shipType==7){ // FedEx
		if(@$packaging != '') $packaging='FEDEX' . strtoupper($packaging); else $packaging='YOURPACKAGING';
		$sXML = '<?xml version="1.0" encoding="UTF-8" ?>' .
			'<FDXRateAvailableServicesRequest xmlns:api="http://www.fedex.com/fsmapi" xmlns:xsi="http://www.w3.org/2001/XMLSchemainstance" xsi:noNamespaceSchemaLocation="FDXRateAvailableServicesRequest.xsd"><RequestHeader>' .
			'<AccountNumber>' . $fedexaccount . '</AccountNumber><MeterNumber>' . $fedexmeter . '</MeterNumber><CarrierCode></CarrierCode></RequestHeader>' .
			'<DropoffType>REGULARPICKUP</DropoffType><Packaging>' . $packaging . '</Packaging>' .
			'<WeightUnits>' . (($adminUnits & 1)==1 ? 'LBS' : 'KGS') . '</WeightUnits><OriginAddress>';
		if($origCountryCode=='US' || $origCountryCode=='CA') $sXML .= '<StateOrProvinceCode>'.@$originstatecode.'</StateOrProvinceCode>';
		$sXML .= '<PostalCode>' . $origZip . '</PostalCode><CountryCode>' . $origCountryCode . '</CountryCode></OriginAddress><DestinationAddress>';
		if($shipCountryCode=='US' || $shipCountryCode=='CA') $sXML .= '<StateOrProvinceCode>' . $shipStateAbbrev . '</StateOrProvinceCode>';
		$sXML .= '<PostalCode>' . $destZip . '</PostalCode><CountryCode>' . $shipCountryCode . '</CountryCode></DestinationAddress>' .
			'<Payment><PayorType>SENDER</PayorType></Payment><SpecialServices>';
		$sXML .= '<ResidentialDelivery>' . ($commerciallocpost=='Y' ? 'false' : 'true') . '</ResidentialDelivery>';
		if(@$saturdaydelivery=='Y') $sXML .= '<SaturdayDelivery>true</SaturdayDelivery>';
		if(@$saturdaypickup==TRUE) $sXML .= '<SaturdayPickup>true</SaturdayPickup>';
		if(@$insidedelivery=='Y') $sXML .= '<InsideDelivery>true</InsideDelivery>';
		if(@$insidepickup==TRUE) $sXML .= '<InsidePickup>true</InsidePickup>';
		if($payproviderpost != ''){
			if((int)$payproviderpost==@$codpaymentprovider) $sXML .= '<COD><CollectionAmount>XXXFILLCODAMTHEREYYY</CollectionAmount><CollectionType>ANY</CollectionType></COD>';
		}
		if(@$signaturerelease=='Y' && @$allowsignaturerelease==TRUE)
			;
		elseif(@$signatureoption=='indirect')
			$sXML .= '<SignatureOption>INDIRECT</SignatureOption>';
		elseif(@$signatureoption=='direct')
			$sXML .= '<SignatureOption>DIRECT</SignatureOption>';
		elseif(@$signatureoption=='adult')
			$sXML .= '<SignatureOption>ADULT</SignatureOption>';
		elseif(@$signatureoption=='none')
			$sXML .= '<SignatureOption>DELIVERWITHOUTSIGNATURE</SignatureOption>';
		$sXML .= '</SpecialServices>';
		if(@$homedelivery != '') $sXML .= '<HomeDelivery><Type>'.$homedelivery.'</Type></HomeDelivery>';
	}
}
$totalpackdims = array(0,0,0,0); // len : wid : hei : vol used
function addpackagedimensions($dimens){
	global $totalpackdims,$adminUnits;
	if(($adminUnits & 12) != 0){
		$origdimens = $totalpackdims;
		// print "adding package dimensions " . $dimens . "<br>";
		$proddims = split('x',$dimens);
		if(@$proddims[0] != '') $thelength = (double)$proddims[0]; else $thelength='';
		if(@$proddims[1] != '') $thewidth = (double)$proddims[1]; else $thewidth='';
		if(@$proddims[2] != '') $theheight =  (double)$proddims[2]; else $theheight='';
		if($thelength != '' && $thewidth != '' && $theheight != ''){
			$objvol = $thelength * $thewidth * $theheight;
			if($thelength > $totalpackdims[0]) $totalpackdims[0] = $thelength;
			if($thewidth > $totalpackdims[1]) $totalpackdims[1] = $thewidth;
			if($theheight > $totalpackdims[2]) $totalpackdims[2] = $theheight;
			if($objvol + $totalpackdims[3] > $totalpackdims[0] * $totalpackdims[1] * $totalpackdims[2]) $totalpackdims[2] = $totalpackdims[2] + ($origdimens[2] > 0 && $origdimens[2] < $theheight ? $origdimens[2] : $theheight);
			if($objvol + $totalpackdims[3] > $totalpackdims[0] * $totalpackdims[1] * $totalpackdims[2]) $totalpackdims[1] = $totalpackdims[1] + ($origdimens[1] > 0 && $origdimens[1] < $thewidth ? $origdimens[1] : $thewidth);
			if($objvol + $totalpackdims[3] > $totalpackdims[0] * $totalpackdims[1] * $totalpackdims[2]) $totalpackdims[0] = $totalpackdims[0] + ($origdimens[0] > 0 && $origdimens[0] < $thelength ? $origdimens[0] : $thelength);
			$totalpackdims[3] = $totalpackdims[3] + $objvol;
			if($totalpackdims[2] > $totalpackdims[1]){ $apdtemp = $totalpackdims[1]; $totalpackdims[1] = $totalpackdims[2]; $totalpackdims[2] = $apdtemp; }
			if($totalpackdims[1] > $totalpackdims[0]){ $apdtemp = $totalpackdims[0]; $totalpackdims[0] = $totalpackdims[1]; $totalpackdims[1] = $apdtemp; }
			if($totalpackdims[2] > $totalpackdims[1]){ $apdtemp = $totalpackdims[1]; $totalpackdims[1] = $totalpackdims[2]; $totalpackdims[2] = $apdtemp; }
		}
	}
	// print "Bin is : " . $totalpackdims[0] . ':' . $totalpackdims[1] . ':' . $totalpackdims[2] . ' = ' . ($totalpackdims[0]*$totalpackdims[1]*$totalpackdims[2]) . '<br />';
}
function addproducttoshipping($apsrs, $prodindex){
	global $shipping,$shipType,$packtogether,$shipThisProd,$somethingToShip,$itemsincart,$intShipping,$international,$shipcountry,$shippingpost;
	global $rowcounter,$origZip,$destZip,$sXML,$numshipoptions,$allzones,$numzones,$dHighWeight,$adminUnits,$shipCountryCode,$totalshipitems;
	global $upspacktype,$splitpackat,$iTotItems,$thePQuantity,$thePWeight,$iWeight,$totalgoods,$shipfreegoods,$packaging,$totalpackdims;
	addpackagedimensions($apsrs["pDims"]);
	if($packtogether) $iTotItems=1; else $iTotItems += 1;
	$shipThisProd=TRUE;
	if(($apsrs["pExemptions"] & 4)==4){ // No Shipping on this product
		if(! $packtogether) $iTotItems -= (int)$apsrs["cartQuantity"];
		$shipThisProd=FALSE;
	}
	if($shipType==1){ // Flat rate shipping
		if($shipThisProd) $shipping += $apsrs["pShipping"] + $apsrs["pShipping2"] * ($apsrs["cartQuantity"]-1);
	}elseif(($shipType==2 || $shipType==5) && $shippingpost==''){ // Weight / Price based shipping
		$havematch=FALSE;
		for($index3=0; $index3 < $numshipoptions; $index3++)
			$dHighest[$index3]=0;
		if(is_array($allzones)){
			if($shipThisProd){
				$somethingToShip=TRUE;
				if($shipType==2) $tmpweight = (double)$apsrs["pWeight"]; else $tmpweight = (double)$apsrs["cartProdPrice"];
				if($packtogether){
					$thePWeight += ((double)($apsrs["cartQuantity"])*$tmpweight);
					$thePQuantity = 1;
				}else{
					$thePWeight = $tmpweight;
					$thePQuantity = (double)$apsrs["cartQuantity"];
				}
			}
			if(((!$packtogether && $shipThisProd) || ($packtogether && ($prodindex == $itemsincart))) && $somethingToShip){ // Only calculate pack together when we have the total
				for($index2=0; $index2 < $numzones; $index2++){
					if($allzones[$index2][0] >= $thePWeight){
						$havematch=TRUE;
						for($index3=0; $index3 < $numshipoptions; $index3++){
							if($allzones[$index2][6+$index3] != 0) // Percentage
								$intShipping[$index3][2] += ((double)$allzones[$index2][1+$index3]*$thePQuantity*$thePWeight)/100.0;
							else
								$intShipping[$index3][2] += ((double)$allzones[$index2][1+$index3]*$thePQuantity);
							if((double)$allzones[$index2][1+$index3]==-99999.0) $intShipping[$index3][3]=FALSE;
						}
						break;
					}
					$dHighWeight = $allzones[$index2][0];
					for($index3=0; $index3 < $numshipoptions; $index3++){
						if($allzones[$index2][6+$index3] != 0) // Percentage
							$dHighest[$index3]=($allzones[$index2][1+$index3]*$dHighWeight)/100.0;
						else
							$dHighest[$index3]=$allzones[$index2][1+$index3];
					}
				}
				if(! $havematch){
					for($index3=0; $index3 < $numshipoptions; $index3++){
						$intShipping[$index3][2] += $dHighest[$index3];
						if($dHighest[$index3]==-99999.0) $intShipping[$index3][3]=FALSE;
					}
					if($allzones[0][0] < 0){
						$dHighWeight = $thePWeight - $dHighWeight;
						while($dHighWeight > 0){
							for($index3=0; $index3 < $numshipoptions; $index3++)
								$intShipping[$index3][2] += ((double)($allzones[0][1+$index3])*$thePQuantity);
							$dHighWeight += $allzones[0][0];
						}
					}
				}
				for($index3=$numshipoptions-1; $index3 >=0; $index3--){
					if($intShipping[$index3][3]==FALSE){
						for($index4=$index3+1; $index4<=$numshipoptions; $index4++)
							$intShipping[$index4-1]=$intShipping[$index4];
						$numshipoptions--;
					}
				}
			}
		}
	}elseif($shipType==3 && $shippingpost==''){ // USPS Shipping
		if($packtogether){
			if($shipThisProd){
				$somethingToShip=TRUE;
				$iWeight += ((double)$apsrs["pWeight"] * (int)$apsrs["cartQuantity"]);
			}
			if(($prodindex == $itemsincart) && $somethingToShip){
				$numpacks=1;
				if(@$splitpackat != '')
					if($iWeight > $splitpackat) $numpacks=ceil($iWeight/$splitpackat);
				if($numpacks > 1){
					if($international != '')
						$sXML .= addUSPSInternational($rowcounter,$splitpackat,$numpacks-1,"Package",$shipcountry);
					else
						$sXML .= addUSPSDomestic($rowcounter,"Parcel",$origZip,$destZip,$splitpackat,$numpacks-1,"None","REGULAR","True");
					$iTotItems++;
					$iWeight -= ($splitpackat*($numpacks-1));
					$rowcounter++;
				}
				if($international != '')
					$sXML .= addUSPSInternational($rowcounter,$iWeight,1,"Package",$shipcountry);
				else
					$sXML .= addUSPSDomestic($rowcounter,"Parcel",$origZip,$destZip,$iWeight,1,"None","REGULAR","True");
				$rowcounter++;
			}
		}else{
			if($shipThisProd){
				$somethingToShip=TRUE;
				$iWeight=$apsrs["pWeight"];
				$numpacks=1;
				if(@$splitpackat != '')
					if($iWeight > $splitpackat) $numpacks=ceil($iWeight/$splitpackat);
				if($numpacks > 1){
					if($international != '')
						$sXML .= addUSPSInternational($rowcounter,$splitpackat,$apsrs["cartQuantity"]*($numpacks-1),"Package",$shipcountry);
					else
						$sXML .= addUSPSDomestic($rowcounter,"Parcel",$origZip,$destZip,$splitpackat,$apsrs["cartQuantity"]*($numpacks-1),"None","REGULAR","True");
					$iTotItems++;
					$iWeight -= ($splitpackat*($numpacks-1));
					$rowcounter++;
				}
				if($international != '')
					$sXML .= addUSPSInternational($rowcounter,$iWeight,$apsrs["cartQuantity"],"Package",$shipcountry);
				else
					$sXML .= addUSPSDomestic($rowcounter,"Parcel",$origZip,$destZip,$iWeight,$apsrs["cartQuantity"],"None","REGULAR","True");
				$rowcounter++;
			}
		}
	}elseif(($shipType==4 || $shipType==6) && $shippingpost==''){ // UPS Shipping
		if(@$packaging != ''){
			if($packaging=='envelope') $packaging='01';
			if($packaging=='pak') $packaging='04';
			if($packaging=='box') $packaging='21';
			if($packaging=='tube') $packaging='03';
			if($packaging=='10kgbox') $packaging='25';
			if($packaging=='25kgbox') $packaging='24';
		}elseif(@$upspacktype != '')
			$packaging=$upspacktype;
		else
			$packaging='02';
		if($packtogether){
			if($shipThisProd){
				$somethingToShip=TRUE;
				$iWeight += ((double)$apsrs["pWeight"] * (int)$apsrs["cartQuantity"]);
			}
			if(($prodindex == $itemsincart) && $somethingToShip){
				$numpacks=1;
				if(@$splitpackat != '')
					if($iWeight > $splitpackat)
						$numpacks=ceil($iWeight/$splitpackat);
				for($index3=0;$index3 < $numpacks; $index3++)
					if($shipType==4)
						$sXML .= addUPSInternational($iWeight / $numpacks,$adminUnits,$packaging,$shipCountryCode,$totalgoods-$shipfreegoods,$totalpackdims);
					else
						$sXML .= addCanadaPostPackage($iWeight / $numpacks,$adminUnits,$packaging,$shipCountryCode,$totalgoods-$shipfreegoods,$totalpackdims);
			}
		}else{
			if($shipThisProd){
				$somethingToShip=TRUE;
				$iWeight=$apsrs["pWeight"];
				$numpacks=1;
				if(@$splitpackat != '')
					if($iWeight > $splitpackat)
						$numpacks=ceil($iWeight/$splitpackat);
				for($index2=0;$index2 < (int)$apsrs["cartQuantity"]; $index2++)
					for($index3=0;$index3 < $numpacks; $index3++)
						if($shipType==4)
							$sXML .= addUPSInternational($iWeight / $numpacks,$adminUnits,$packaging,$shipCountryCode,$apsrs["cartProdPrice"],$totalpackdims);
						else
							$sXML .= addCanadaPostPackage($iWeight / $numpacks,$adminUnits,$packaging,$shipCountryCode,$apsrs["cartProdPrice"],$totalpackdims);
			}
		}
	}elseif(($shipType==7) && $shippingpost==''){ // FedEx
		if($packtogether){
			$totalshipitems=1;
			if($shipThisProd){
				$somethingToShip=TRUE;
				$iWeight += ((double)$apsrs["pWeight"] * (int)$apsrs["cartQuantity"]);
			}
		}else{
			if($shipThisProd){
				$somethingToShip=TRUE;
				$iWeight += ((double)$apsrs["pWeight"] * (int)$apsrs["cartQuantity"]);
				if($splitpackat != ''){
					if((double)$apsrs["pWeight"] > $splitpackat)
						$totalshipitems += ceil((double)$apsrs["pWeight"]/$splitpackat) * (int)$apsrs["cartQuantity"];
					else
						$totalshipitems += (int)$apsrs["cartQuantity"];
				}else
					$totalshipitems += (int)$apsrs["cartQuantity"];
			}
		}
		if(($prodindex == $itemsincart) && $somethingToShip){
			if($packtogether && $splitpackat != ''){
				if($iWeight > $splitpackat) $totalshipitems = ceil((double)$apsrs["pWeight"]/$splitpackat);
			}
			$sXML .= addFedexPackage($iWeight,$totalshipitems,$totalgoods-$shipfreegoods,$totalpackdims);
		}
	}
}
function calculateshipping(){
	global $shipType,$isstandardship,$checkIntOptions,$somethingToShip,$willpickuptext,$willpickupcost,$allzones,$numshipoptions,$upsUser,$upsPw,$shipCountryCode,$destZip,$shippingpost;
	global $shipping,$shipMethod,$success,$errormsg,$xxNoMeth,$sXML,$intShipping,$pzFSA,$international,$iTotItems,$uspsmethods,$numuspsmeths,$shipstate,$maxshipoptions,$saturdaydelivery,$saturdaypickup;
	if($shipType==1){
		$isstandardship = TRUE;
	}elseif(($shipType==2 || $shipType==5) && ($somethingToShip || @$willpickuptext != '')){
		$checkIntOptions = ($shippingpost=='');
		if(is_array($allzones) && $numshipoptions>0){
			$shipping = $intShipping[0][2];
			$shipMethod = $intShipping[0][0];
			$isstandardship = (($pzFSA & 1) == 1);
			if($numshipoptions == 1 && @$willpickuptext=='')
				$checkIntOptions = FALSE;
		}else{
			if(@$willpickuptext != ''){
				if(@$willpickupcost != '') $shipping = $willpickupcost;
				$shipMethod = $willpickuptext;
			}else{
				$success = FALSE;
				$errormsg=$xxNoMeth;
				$checkIntOptions = FALSE;
			}
		}
	}elseif($shipType==3 && $somethingToShip){
		$checkIntOptions = ($shippingpost=='');
		if($shippingpost==''){
			$sXML .= "</" . $international . "RateRequest>";
			$success = USPSCalculate($sXML,$international,$shipping, $errormsg, $intShipping);
			if(substr($errormsg, 0, 30)=="Warning - Bound Printed Matter") $success=true;
			if($success && $checkIntOptions){ // Look for a single valid shipping option
				$totShipOptions = 0;
				for($indexmso=0; $indexmso<$maxshipoptions; $indexmso++){
					$shipRow = $intShipping[$indexmso];
					if($iTotItems==$shipRow[3]){
						for($index2=0;$index2<$numuspsmeths;$index2++){
							if(str_replace('-',' ',strtolower($shipRow[0])) == str_replace('-',' ',strtolower($uspsmethods[$index2][0]))){
								if($totShipOptions==0){
									$shipping = $shipRow[2];
									$shipMethod = trim($uspsmethods[$index2][2]);
									$isstandardship = (int)$uspsmethods[$index2][1];
								}
								$intShipping[$indexmso][5] = trim($uspsmethods[$index2][2]);
								$totShipOptions++;
							}
						}
					}
				}
				if($totShipOptions==1)
					$checkIntOptions=FALSE;
				elseif($totShipOptions==0 && @$willpickuptext==''){
					$checkIntOptions=FALSE;
					$success=FALSE;
					$errormsg=$xxNoMeth;
				}
				if(@$willpickuptext != '') $checkIntOptions = TRUE;
			}
		}
	}elseif($shipType==4 && $somethingToShip){
		$checkIntOptions = ($shippingpost=='');
		if($shippingpost==''){
			$sXML .= '<ShipmentServiceOptions>' . (@$saturdaydelivery=='Y' ? '<SaturdayDelivery/>' : '') . (@$saturdaypickup==TRUE ? '<SaturdayPickup/>' : '') . '</ShipmentServiceOptions></Shipment></RatingServiceSelectionRequest>';
			if(trim($upsUser) != '' && trim($upsPw) != '')
				$success = UPSCalculate($sXML,$international,$shipping, $errormsg, $intShipping);
			else{
				$success = FALSE;
				$errormsg = "You must register with UPS by logging on to your online admin section and clicking the &quot;Register with UPS&quot; link before you can use the UPS OnLine&reg; Shipping Rates and Services Selection";
			}
		}
	}elseif($shipType==6 && $somethingToShip){
		$checkIntOptions = (@$_POST['shipping']=='');
		if(@$_POST['shipping']==''){
			$sXML .= ' </lineItems><city> </city> ';
			if($shipstate!='')
				$sXML .= '<provOrState> ' . $shipstate . ' </provOrState>';
			else{
				if($shipCountryCode=='US' || $shipCountryCode=='CA'){
					$thestate = ((trim(@$_POST['sname']) != '' || trim(@$_POST['saddress']) != '') ? @$_POST['sstate2'] : @$_POST['state2']);
					if($thestate=='') $thestate=($shipCountryCode=='US' ? 'CA' : 'QC');
					$sXML .= '<provOrState> ' . $thestate . ' </provOrState>';
				}else
					$sXML .= '<provOrState> </provOrState>';
			}
			$sXML .= '<country>' . $shipCountryCode . '</country><postalCode>' . $destZip . '</postalCode></ratesAndServicesRequest></eparcel>';
			$success = CanadaPostCalculate($sXML,$international,$shipping, $errormsg, $intShipping);
		}
	}elseif($shipType==7 && $somethingToShip){
		$checkIntOptions = ($shippingpost=='');
		if($shippingpost==''){
			$sXML .= '</FDXRateAvailableServicesRequest>';
			$success = fedexcalculate($sXML,$international, $errormsg, $intShipping);
		}
	}
	if($success && @$_POST['shipping']=='' && $somethingToShip && ($shipType==4 || $shipType==6 || $shipType==7)){
		$totShipOptions = 0;
		for($indexmso=0; $indexmso<$maxshipoptions; $indexmso++){
			if($intShipping[$indexmso][3]==TRUE){
				$totShipOptions++;
				if($indexmso==0){
					$shipping = $intShipping[$indexmso][2];
					$shipMethod = $intShipping[$indexmso][0];
					$isstandardship = $intShipping[$indexmso][4];
				}
			}
		}
		if($totShipOptions==1)
			$checkIntOptions=FALSE;
		elseif($totShipOptions == 0 && @$willpickuptext==''){
			$checkIntOptions = FALSE;
			$success=FALSE;
			$errormsg=$xxNoMeth;
		}
		if(@$willpickuptext != '') $checkIntOptions = TRUE;
	}
	return($success);
}
function insuranceandtaxaddedtoshipping(){
	global $shipinsuranceamt,$shippingpost,$somethingToShip,$wantinsurance,$addshippinginsurance,$maxshipoptions;
	global $totalgoods,$shipping,$taxShipping,$shippingpost,$stateTaxRate,$countryTaxRate,$intShipping;
	if(is_numeric(@$shipinsuranceamt) && $shippingpost=='' && $somethingToShip){
		if(($wantinsurance=="Y" && @$addshippinginsurance==2) || @$addshippinginsurance==1){
			for($index3=0; $index3 < $maxshipoptions; $index3++)
				$intShipping[$index3][2] += (((double)$totalgoods*(double)$shipinsuranceamt)/100.0);
			$shipping += (((double)$totalgoods*(double)$shipinsuranceamt)/100.0);
		}elseif(($wantinsurance=="Y" && @$addshippinginsurance==-2) || @$addshippinginsurance==-1){
			for($index3=0; $index3 < $maxshipoptions; $index3++)
				$intShipping[$index3][2] += $shipinsuranceamt;
			$shipping += $shipinsuranceamt;
		}
	}
	if(@$taxShipping==1 && $shippingpost==''){
		for($index3=0; $index3 < $maxshipoptions; $index3++)
			$intShipping[$index3][2] += ((double)$intShipping[$index3][2]*((double)$stateTaxRate+(double)$countryTaxRate))/100.0;
		$shipping += ((double)$shipping*((double)$stateTaxRate+(double)$countryTaxRate))/100.0;
	}
}
function calculatetaxandhandling(){
	global $handlingchargepercent,$handling,$totalgoods,$shipping,$totaldiscounts,$freeshipamnt,$taxHandling,$stateTaxRate,$countryTaxRate,$taxShipping;
	global $stateTax,$countryTax,$canadataxsystem,$shipCountryID,$shipStateAbbrev,$usehst,$statetaxfree,$countrytaxfree,$proratashippingtax,$perproducttaxrate;
	if(@$handlingchargepercent != '') $handling += ((($totalgoods + $shipping + $handling) - ($totaldiscounts + $freeshipamnt)) * $handlingchargepercent / 100.0);
	if(@$taxHandling==1) $handling += ((double)$handling*((double)$stateTaxRate+(double)$countryTaxRate))/100.0;
	if(@$canadataxsystem==true && $shipCountryID==2 && ($shipStateAbbrev=="NB" || $shipStateAbbrev=="NF" || $shipStateAbbrev=="NS")) $usehst=true; else $usehst=false;
	if(@$canadataxsystem==true && $shipCountryID==2 && ($shipStateAbbrev=="PE" || $shipStateAbbrev=="QC")){
		$statetaxable = 0;
		$countrytaxable = 0;
		if(@$taxShipping==2 && ($shipping - $freeshipamnt > 0)){
			if(@$proratashippingtax==TRUE){
				if($totalgoods>0) $statetaxable += (((double)$totalgoods-((double)$totaldiscounts+(double)$statetaxfree)) / $totalgoods) * ((double)$shipping-(double)$freeshipamnt);
			}else
				$statetaxable += ((double)$shipping-(double)$freeshipamnt);
			$countrytaxable += ((double)$shipping-(double)$freeshipamnt);
		}
		if(@$taxHandling==2){
			$statetaxable += (double)$handling;
			$countrytaxable += (double)$handling;
		}
		if($totalgoods>0){
			$statetaxable += ((double)$totalgoods-((double)$totaldiscounts+(double)$statetaxfree));
			$countrytaxable += ((double)$totalgoods-((double)$totaldiscounts+(double)$countrytaxfree));
		}
		$countryTax = $countrytaxable*(double)$countryTaxRate/100.0;
		$stateTax = ($statetaxable+(double)$countryTax)*(double)$stateTaxRate/100.0;
	}else{
		if($totalgoods>0){
			$stateTax = ((double)$totalgoods-((double)$totaldiscounts+(double)$statetaxfree))*(double)$stateTaxRate/100.0;
			if(@$perproducttaxrate != TRUE) $countryTax = ((double)$totalgoods-((double)$totaldiscounts+(double)$countrytaxfree))*(double)$countryTaxRate/100.0;
		}
		if(@$taxShipping==2 && ($shipping - $freeshipamnt > 0)){
			if(@$proratashippingtax==TRUE){
				if($totalgoods>0) $stateTax += (((double)$totalgoods-((double)$totaldiscounts+(double)$statetaxfree)) / $totalgoods) * (((double)$shipping-(double)$freeshipamnt)*(double)$stateTaxRate/100.0);
			}else
				$stateTax += (((double)$shipping-(double)$freeshipamnt)*(double)$stateTaxRate/100.0);
			$countryTax += (((double)$shipping-(double)$freeshipamnt)*(double)$countryTaxRate/100.0);
		}
		if(@$taxHandling==2){
			$stateTax += ((double)$handling*(double)$stateTaxRate/100.0);
			$countryTax += ((double)$handling*(double)$countryTaxRate/100.0);
		}
	}
	if($stateTax < 0) $stateTax = 0;
	if($countryTax < 0) $countryTax = 0;
}
if(@$_GET["token"] != ''){
	if(getpayprovdetails(19,$username,$password,$data3,$demomode,$ppmethod)){
		$data2arr = split("&",$password);
		$password=urldecode(@$data2arr[0]);
		$isthreetoken=(trim(urldecode(@$data2arr[2]))=='1');
		$signature=''; $sslcertpath='';
		if($isthreetoken) $signature=urldecode(@$data2arr[1]); else $sslcertpath=urldecode(@$data2arr[1]);
	}
	$sXML = ppsoapheader($username, $password, $signature) .
		'<soap:Body><GetExpressCheckoutDetailsReq xmlns="urn:ebay:api:PayPalAPI"><GetExpressCheckoutDetailsRequest><Version xmlns="urn:ebay:apis:eBLBaseComponents">1.00</Version>' .
		'  <Token>' . $_GET["token"] . '</Token>' .
		'</GetExpressCheckoutDetailsRequest></GetExpressCheckoutDetailsReq></soap:Body></soap:Envelope>';
	if($demomode) $sandbox = ".sandbox"; else $sandbox = '';
	if(callcurlfunction('https://api-aa' . ($sandbox=='' && $isthreetoken ? '-3t' : '') . $sandbox . '.paypal.com/2.0/', $sXML, $res, $sslcertpath, $errormsg, FALSE)){
		$xmlDoc = new vrXMLDoc($res);
		$nodeList = $xmlDoc->nodeList->childNodes[0];
		$success=FALSE;
		$ordPhone = $ordEmail = $ordName='';
		$countryid=0;
		$ordShipName = $ordShipAddress = $ordShipAddress2 = $ordShipCity = $ordShipState = $ordShipZip = $ordShipPhone = '';
		$ordShipCountry = $ordAffiliate = $ordAddInfo = $ordExtra1 = $ordExtra2 = $ordShipExtra1 = $ordShipExtra2 = $ordCheckoutExtra1 = $ordCheckoutExtra2 = '';
		$ordPayProvider = '19';
		$insidedelivery = $commercialloc = $wantinsurance = $saturdaydelivery = $signaturerelease = '';
		$ordComLoc = 0;
		$gotaddress = FALSE;
		$token = $_GET["token"];
		if(abs(@$addshippinginsurance)==1) $ordComLoc += 2;
		for($i = 0; $i < $nodeList->length; $i++){
			if($nodeList->nodeName[$i]=="SOAP-ENV:Body"){
				$e = $nodeList->childNodes[$i];
				for($j = 0; $j < $e->length; $j++){
					if($e->nodeName[$j] == "GetExpressCheckoutDetailsResponse"){
						$ee = $e->childNodes[$j];
						for($jj = 0; $jj < $ee->length; $jj++){
							if($ee->nodeName[$jj] == 'Ack'){
								if($ee->nodeValue[$jj]=='Success' || $ee->nodeValue[$jj]=='SuccessWithWarning')
									$success=TRUE;
							}elseif($ee->nodeName[$jj] == "GetExpressCheckoutDetailsResponseDetails"){
								$ff = $ee->childNodes[$jj];
								for($kk = 0; $kk < $ff->length; $kk++){
									if($ff->nodeName[$kk] == "PayerInfo"){
										$gg = $ff->childNodes[$kk];
										for($ll = 0; $ll < $gg->length; $ll++){
											if($gg->nodeName[$ll] == "Payer"){
												$ordEmail = $gg->nodeValue[$ll];
											}elseif($gg->nodeName[$ll] == "PayerID"){
												$payerid = $gg->nodeValue[$ll];
											}elseif($gg->nodeName[$ll] == 'PayerStatus'){
												$ordCVV = 'U';
												$payer_status = strtolower($gg->nodeValue[$ll]);
												if($payer_status=='verified') $ordCVV = 'Y';
												elseif($payer_status=='unverified') $ordCVV = 'N';
											}elseif($gg->nodeName[$ll] == "PayerName"){
												$hh = $gg->childNodes[$ll];
												for($mm = 0; $mm < $hh->length; $mm++){
													if($hh->nodeName[$mm] == "FirstName"){
														$ordName = $hh->nodeValue[$mm] . ($ordName!=''?' '.$ordName:$ordName);
													}elseif($hh->nodeName[$mm] == "LastName"){
														$ordName = ($ordName!=''?$ordName.' ':$ordName) . $hh->nodeValue[$mm];
													}
												}
											}elseif($gg->nodeName[$ll] == "Address"){
												$hh = $gg->childNodes[$ll];
												for($mm = 0; $mm < $hh->length; $mm++){
													if($hh->nodeName[$mm] == "Street1"){
														$ordAddress = $hh->nodeValue[$mm];
													}elseif($hh->nodeName[$mm] == "Street2"){
														$ordAddress2 = $hh->nodeValue[$mm];
													}elseif($hh->nodeName[$mm] == "CityName"){
														$ordCity = $hh->nodeValue[$mm];
													}elseif($hh->nodeName[$mm] == "StateOrProvince"){
														$ordState = $hh->nodeValue[$mm];
													}elseif($hh->nodeName[$mm] == "Country"){
														$sSQL = "SELECT countryName,countryID FROM countries WHERE countryCode='".mysql_escape_string($hh->nodeValue[$mm])."'";
														$result = mysql_query($sSQL) or print(mysql_error());
														if($rs = mysql_fetch_array($result)){
															$ordCountry = $rs["countryName"];
															$countryid = $rs["countryID"];
														}
													}elseif($hh->nodeName[$mm] == "PostalCode"){
														$ordZip = $hh->nodeValue[$mm];
													}elseif($hh->nodeName[$mm] == "AddressStatus"){
														$ordAVS = 'U';
														$address_status = strtolower($hh->nodeValue[$mm]);
														$gotaddress = ($address_status != 'none');
														if($address_status=='confirmed') $ordAVS = 'Y';
														elseif($address_status=='unconfirmed') $ordAVS = 'N';
													}
												}
											}
										}
									}elseif($ff->nodeName[$kk] == 'Custom'){
										$customarr = split(':', $ff->nodeValue[$kk]);
										$thesessionid = $customarr[0];
										$ordAffiliate = $customarr[1];
										if(substr($thesessionid,0,3)=='cid'){
											$_SESSION['clientID'] = str_replace("'",'',substr($thesessionid,3));
											$sSQL = "SELECT clID,clUserName,clActions,clLoginLevel,clPercentDiscount FROM customerlogin WHERE clID='" . $_SESSION['clientID'] ."'";
											$result = mysql_query($sSQL) or print(mysql_error());
											if($rs = mysql_fetch_array($result)){
												$_SESSION['clientUser']=$rs['clUserName'];
												$_SESSION['clientActions']=$rs['clActions'];
												$_SESSION['clientLoginLevel']=$rs['clLoginLevel'];
												$_SESSION['clientPercentDiscount']=(100.0-(double)$rs['clPercentDiscount'])/100.0;
											}
										}else
											$thesessionid = str_replace("'",'',substr($thesessionid,3));
									}elseif($ff->nodeName[$kk] == 'ContactPhone'){
										$ordPhone=$ff->nodeValue[$kk];
									}
								}
							}elseif($ee->nodeName[$jj] == "Errors"){
								$ff = $ee->childNodes[$jj];
								for($kk = 0; $kk < $ff->length; $kk++){
									if($ff->nodeName[$kk] == "ShortMessage"){
										$errormsg=$ff->nodeValue[$kk].'<br>'.$errormsg;
									}elseif($ff->nodeName[$kk] == "LongMessage"){
										$errormsg.=$ff->nodeValue[$kk];
									}elseif($ff->nodeName[$kk] == "ErrorCode"){
										$errcode=$ff->nodeValue[$kk];
									}
								}
							}
						}
					}
				}
			}
		}
		if(! $gotaddress)
			$ppexpresscancel=TRUE;
		elseif($success){
			$paypalexpress=TRUE;
			if(($countryid==1 || $countryid==2) && @$usestateabbrev!=TRUE){
				$sSQL = "SELECT stateName FROM states WHERE stateAbbrev='" . mysql_escape_string($ordState) . "'";
				$result = mysql_query($sSQL) or print(mysql_error());
				if($rs = mysql_fetch_array($result))
					$ordState=$rs['stateName'];
				mysql_free_result($result);
			}
		}else{
			print 'PayPal Payment Pro error: ' . $errormsg;
		}
	}else{
		print 'PayPal Payment Pro error: ' . $errormsg;
	}
}elseif($checkoutmode=='paypalexpress1'){
	if(getpayprovdetails(19,$username,$password,$data3,$demomode,$ppmethod)){
		$data2arr = split("&",$password);
		$password=urldecode(@$data2arr[0]);
		$isthreetoken=(trim(urldecode(@$data2arr[2]))=='1');
		$signature=''; $sslcertpath='';
		if($isthreetoken) $signature=urldecode(@$data2arr[1]); else $sslcertpath=urldecode(@$data2arr[1]);
	}
	if($demomode) $sandbox = ".sandbox"; else $sandbox = '';
	if(@$pathtossl != ''){
		if(substr($pathtossl,-1) != "/") $storeurl = $pathtossl . "/"; else $storeurl = $pathtossl;
	}
	$sXML = ppsoapheader($username, $password, $signature) .
		'<soap:Body><SetExpressCheckoutReq xmlns="urn:ebay:api:PayPalAPI"><SetExpressCheckoutRequest><Version xmlns="urn:ebay:apis:eBLBaseComponents">1.00</Version>' .
		'  <SetExpressCheckoutRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">' .
		'    <OrderTotal currencyID="' . $countryCurrency . '">' . $_POST["estimate"] . '</OrderTotal>' .
		'    <ReturnURL>' . $storeurl . 'cart.php</ReturnURL><CancelURL>' . $storeurl . 'cart.php</CancelURL>' .
		'    <Custom>' . (@$_SESSION['clientID'] != '' ? 'cid' . $_SESSION['clientID'] : 'sid' . $thesessionid) . ':' . strip_tags(trim(@$_POST['PARTNER'])) . '</Custom>' .
		'    <PaymentAction>' . ($ppmethod==1?'Authorization':'Sale') . '</PaymentAction>' .
		'  </SetExpressCheckoutRequestDetails>' .
		'</SetExpressCheckoutRequest></SetExpressCheckoutReq></soap:Body></soap:Envelope>';
	if($username==''){
		print '<meta http-equiv="Refresh" content="0; URL=http://altfarm.mediaplex.com/ad/ck/3484-23890-3840-61">';
		print '<p align="center">' . $xxAutFo . '</p>';
		print '<p align="center">' . $xxForAut . ' <a href="http://altfarm.mediaplex.com/ad/ck/3484-23890-3840-61">' . $xxClkHere . '</a></p>';
	}elseif(callcurlfunction('https://api-aa' . ($sandbox=='' && $isthreetoken ? '-3t' : '') . $sandbox . '.paypal.com/2.0/', $sXML, $res, $sslcertpath, $errormsg, FALSE)){
		$xmlDoc = new vrXMLDoc($res);
		$nodeList = $xmlDoc->nodeList->childNodes[0];
		$success=FALSE;
		$token='';
		for($i = 0; $i < $nodeList->length; $i++){
			if($nodeList->nodeName[$i]=="SOAP-ENV:Body"){
				$e = $nodeList->childNodes[$i];
				for($j = 0; $j < $e->length; $j++){
					if($e->nodeName[$j] == "SetExpressCheckoutResponse"){
						$ee = $e->childNodes[$j];
						for($jj = 0; $jj < $ee->length; $jj++){
							if($ee->nodeName[$jj] == 'Ack'){
								if($ee->nodeValue[$jj]=='Success' || $ee->nodeValue[$jj]=='SuccessWithWarning')
									$success=TRUE;
							}elseif($ee->nodeName[$jj] == "Token"){
								$token=$ee->nodeValue[$jj];
							}elseif($ee->nodeName[$jj] == "Errors"){
								$ff = $ee->childNodes[$jj];
								for($kk = 0; $kk < $ff->length; $kk++){
									if($ff->nodeName[$kk] == "ShortMessage"){
										$errormsg=$ff->nodeValue[$kk].'<br>'.$errormsg;
									}elseif($ff->nodeName[$kk] == "LongMessage"){
										$errormsg.=$ff->nodeValue[$kk];
									}elseif($ff->nodeName[$kk] == "ErrorCode"){
										$errcode=$ff->nodeValue[$kk];
									}
								}
							}
						}
					}
				}
			}
		}
		if($success){
			if(ob_get_length()===FALSE){
				print '<meta http-equiv="Refresh" content="0; URL=https://www'.$sandbox.'.paypal.com/webscr?cmd=_express-checkout&token=' . $token . '">';
			}else{
				header('Location: https://www'.$sandbox.'.paypal.com/webscr?cmd=_express-checkout&token=' . $token);
			}
			print '<p align="center">' . $xxAutFo . '</p>';
			print '<p align="center">' . $xxForAut . ' <a href="https://www'.$sandbox.'.paypal.com/webscr?cmd=_express-checkout&token=' . $token . '">' . $xxClkHere . '</a></p>';
		}else{
			print "PayPal Payment Pro error: " . $errormsg;
		}
	}else{
		print "PayPal Payment Pro error: " . $errormsg;
	}
}elseif($checkoutmode=='update'){
	if($stockManage != 0) trimoldcartitems(time()-($stockManage*60*60)+($dateadjust*60*60));
	$_SESSION['xsshipping']=NULL; unset($_SESSION['xsshipping']);
	$_SESSION['discounts']=NULL; unset($_SESSION['discounts']);
	$_SESSION['xscountrytax']=NULL; unset($_SESSION['xscountrytax']);
	mysql_query("UPDATE orders SET ordTotal=0,ordShipping=0,ordStateTax=0,ordCountryTax=0,ordHSTTax=0,ordHandling=0,ordDiscount=0,ordDiscountText='' WHERE ordAuthNumber='' AND " . getordersessionsql()) or print(mysql_error());
	foreach(@$_POST as $objItem => $objValue){
		if(substr($objItem,0,5)=="quant"){
			$thecartid = (int)substr($objItem, 5);
			if((int)$objValue==0){
				$sSQL="DELETE FROM cartoptions WHERE coCartID='" . $thecartid . "'";
				mysql_query($sSQL) or print(mysql_error());
				$sSQL="DELETE FROM cart WHERE cartID='" . $thecartid . "'";
				mysql_query($sSQL) or print(mysql_error());
			}else{
				$totQuant = 0;
				$pPrice = 0;
				$pID = '';
				$sSQL="SELECT cartQuantity,pInStock,pID,pStockByOpts,".$WSP."pPrice FROM cart LEFT JOIN products ON cart.cartProdId=products.pID WHERE cartID='" . $thecartid . "'";
				$result = mysql_query($sSQL) or print(mysql_error());
				if($rs = mysql_fetch_array($result)){
					$pID = trim($rs["pID"]);
					$pInStock = (int)$rs["pInStock"];
					$pStockByOpts = (int)$rs["pStockByOpts"];
					$pPrice = $rs["pPrice"];
					$cartQuantity = (int)$rs["cartQuantity"];
					mysql_free_result($result);
					$sSQL = "SELECT SUM(cartQuantity) AS cartQuant FROM cart WHERE cartCompleted=0 AND cartProdID='" . $pID . "'";
					$result = mysql_query($sSQL) or print(mysql_error());
					if($rs = mysql_fetch_array($result))
						$totQuant = (int)$rs["cartQuant"];
				}
				mysql_free_result($result);
				if($pID != ''){
					if($stockManage != 0){
						$quantavailable = abs((int)$objValue);
						if((int)$pStockByOpts != 0){
							$hasalloptions=true;
							$sSQL = "SELECT coID,optStock,cartQuantity,coOptID FROM cart INNER JOIN cartoptions ON cart.cartID=cartoptions.coCartID INNER JOIN options ON cartoptions.coOptID=options.optID INNER JOIN optiongroup ON options.optGroup=optiongroup.optGrpID WHERE optType IN (-2,-1,1,2) AND cartID='" . $thecartid . "'";
							$result = mysql_query($sSQL) or print(mysql_error());
							if(mysql_num_rows($result)>0){
								while($rs = mysql_fetch_assoc($result)){
									$pInStock = (int)$rs["optStock"];
									$totQuant = 0;
									$cartQuantity = (int)$rs["cartQuantity"];
									$sSQL = "SELECT SUM(cartQuantity) AS cartQuant FROM cart INNER JOIN cartoptions ON cart.cartID=cartoptions.coCartID WHERE cartCompleted=0 AND coOptID=" . $rs["coOptID"];
									$result2 = mysql_query($sSQL) or print(mysql_error());
									if($rs2 = mysql_fetch_assoc($result2))
										if(! is_null($rs2["cartQuant"])) $totQuant = (int)$rs2["cartQuant"];
									mysql_free_result($result2);
									if((int)($pInStock - $totQuant + $cartQuantity) < $quantavailable) $quantavailable = ($pInStock - $totQuant + $cartQuantity);
									if(($pInStock - $totQuant + $cartQuantity - abs((int)$objValue)) < 0) $hasalloptions=false;
								}
								mysql_query("UPDATE cart SET cartQuantity=" . $quantavailable . " WHERE cartCompleted=0 AND cartID='" . $thecartid . "'") or print(mysql_error());
								if(! $hasalloptions) $isInStock = false;
							}
							mysql_free_result($result);
						}else{
							if(($pInStock - $totQuant + $cartQuantity - $quantavailable) < 0){
								$quantavailable = ($pInStock - $totQuant + $cartQuantity);
								if($quantavailable < 0) $quantavailable=0;
								$isInStock = FALSE;
							}
							mysql_query("UPDATE cart SET cartQuantity=" . $quantavailable . " WHERE cartCompleted=0 AND cartID='" . $thecartid . "'") or print(mysql_error());
						}
					}else
						mysql_query("UPDATE cart SET cartQuantity=" . abs((int)$objValue) . " WHERE cartCompleted=0 AND cartID='" . $thecartid . "'") or print(mysql_error());
					checkpricebreaks($pID,$pPrice);
				}
			}
		}elseif(substr($objItem,0,5)=="delet"){
			$result = mysql_query("SELECT cartID FROM cart WHERE cartCompleted=0 AND cartID='" . (int)substr($objItem, 5) . "'") or print(mysql_error());
			if(mysql_num_rows($result)>0){
				mysql_query("DELETE FROM cart WHERE cartID='" . (int)substr($objItem, 5) . "'") or print(mysql_error());
				mysql_query("DELETE FROM cartoptions WHERE coCartID='" . (int)substr($objItem, 5) . "'") or print(mysql_error());
			}
		}
	}
}
if($checkoutmode=='add'){
	if($stockManage != 0) trimoldcartitems(time()-($stockManage*60*60)+($dateadjust*60*60));
	$_SESSION['xsshipping']=NULL; unset($_SESSION['xsshipping']);
	$_SESSION['discounts']=NULL; unset($_SESSION['discounts']);
	$_SESSION['xscountrytax']=NULL; unset($_SESSION['xscountrytax']);
	mysql_query("UPDATE orders SET ordTotal=0,ordShipping=0,ordStateTax=0,ordCountryTax=0,ordHSTTax=0,ordHandling=0,ordDiscount=0,ordDiscountText='' WHERE ordAuthNumber='' AND " . getordersessionsql()) or print(mysql_error());
	$bExists = FALSE;
	if(trim(@$_POST['frompage'])!='') $_SESSION['frompage']=$_POST['frompage']; else $_SESSION['frompage']='';
	if(@$_POST['quant']=='' || ! is_numeric(@$_POST['quant'])) $quantity=1; else $quantity=abs((int)@$_POST['quant']);
	$origquantity = $quantity;
	foreach(@$_POST as $objItem => $objValue){ // Check if the product id is modified
		if(substr($objItem,0,4)=='optn' && trim($objValue)!=''){
			$sSQL="SELECT optRegExp FROM options WHERE optID='" . mysql_escape_string($objValue) . "'";
			$result2 = mysql_query($sSQL) or print(mysql_error());
			if($rs=mysql_fetch_assoc($result2)) $theexp = trim($rs['optRegExp']); else $theexp='';
			if($theexp != '' && substr($theexp, 0, 1) != '!'){
				$theexp = str_replace('%s', $theid, $theexp);
				if(strpos($theexp, ' ') !== FALSE){ // Search and replace
					$exparr = split(' ', $theexp, 2);
					$theid = str_replace($exparr[0], $exparr[1], $theid);
				}else
					$theid = $theexp;
			}
			mysql_free_result($result2);
		}
	}
	$sSQL = "SELECT cartID FROM cart WHERE cartCompleted=0 AND " . getsessionsql() . " AND cartProdID='" . $theid . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	while($rs = mysql_fetch_assoc($result)){
		$bExists = TRUE;
		$cartID = $rs["cartID"];
		foreach(@$_POST as $objItem => $objValue){ // We have the product. Check we have all the same options
			if(substr($objItem,0,4)=="optn"){
				if(@$_POST["v" . $objItem] != ''){
					$sSQL="SELECT coID FROM cartoptions WHERE coCartID=" . $cartID . " AND coOptID='" . mysql_escape_string($objValue) . "' AND coCartOption='" . mysql_escape_string(unstripslashes(trim(@$_POST["v" . $objItem]))) . "'";
					$result2 = mysql_query($sSQL) or print(mysql_error());
					if(mysql_num_rows($result2)==0) $bExists=FALSE;
					mysql_free_result($result2);
				}elseif(is_numeric($objValue)){
					$sSQL="SELECT coID FROM cartoptions WHERE coCartID=" . $cartID . " AND coOptID='" . mysql_escape_string($objValue) . "'";
					$result2 = mysql_query($sSQL) or print(mysql_error());
					if(mysql_num_rows($result2)==0) $bExists=FALSE;
					mysql_free_result($result2);
				}
			}
			if(! $bExists) break;
		}
		if($bExists) break;
	}
	mysql_free_result($result);
	$sSQL = "SELECT ".getlangid("pName",1).",".$WSP."pPrice,pInStock,pWeight,pStockByOpts FROM products WHERE pSell<>0 AND pID='" . $theid . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	if(! ($rsStock = mysql_fetch_array($result))){
		$rsStock[getlangid("pName",1)]=$theid;
		$stockManage=0;
		$isInStock=FALSE;
		$outofstockreason=2;
	}
	function push_stock_item(){
		global $stockrelarr,$stockrelitems,$rs;
		$stockrelarr[$stockrelitems]['cartID']=$rs['cartID'];
		$stockrelarr[$stockrelitems]['cartQuantity']=$rs['cartQuantity'];
		$stockrelarr[$stockrelitems]['cartDateAdded']=$rs['cartDateAdded'];
		$stockrelarr[$stockrelitems]['cartClientID']=$rs['cartClientID'];
		$stockrelarr[$stockrelitems]['cartSessionID']=$rs['cartSessionID'];
		$stockrelitems++;
	}
	$bestDate = time()-(60*60*24*62);
	if($stockManage != 0){
		$stockRelDate = time()+($dateadjust*60*60)-($stockManage*60*60); // For saved cart items
		$outofstockreason=1;
		if((int)$rsStock['pStockByOpts'] != 0){
			foreach(@$_POST as $objItem => $objValue){
				$totQuant = 0;
				if(substr($objItem,0,4)=='optn' && trim($objValue)!=''){
					$sSQL="SELECT optStock FROM options INNER JOIN optiongroup ON options.optGroup=optiongroup.optGrpID WHERE optType IN (-2,-1,1,2) AND optID='" . mysql_escape_string($objValue) . "'";
					$result = mysql_query($sSQL) or print(mysql_error());
					if($rs = mysql_fetch_array($result)) $stockQuant = $rs['optStock']; else $stockQuant = $origquantity;
					mysql_free_result($result);
					$sSQL = "SELECT cartQuantity,cartDateAdded,cartOrderID FROM cart INNER JOIN cartoptions ON cart.cartID=cartoptions.coCartID INNER JOIN options ON cartoptions.coOptID=options.optID INNER JOIN optiongroup ON options.optGroup=optiongroup.optGrpID WHERE optType IN (-2,-1,1,2) AND cartCompleted=0 AND coOptID='" . mysql_escape_string($objValue) . "' ORDER BY cartDateAdded DESC";
					$result = mysql_query($sSQL) or print(mysql_error());
					while($rs = mysql_fetch_array($result)){
						$totQuant += $rs['cartQuantity'];
						if((int)$rs['cartOrderID']==0){
							if(strtotime($rs['cartDateAdded']) > $bestDate && $totQuant+$stockQuant >= $origquantity) $bestDate = strtotime($rs['cartDateAdded']);
							if(strtotime($rs['cartDateAdded']) < $stockRelDate) push_stock_item();
						}
					}
					mysql_free_result($result);
					if($stockQuant-$totQuant < $quantity) $quantity = $stockQuant-$totQuant;
					if(($stockQuant+$totQuant) < $origquantity) $outofstockreason=0;
				}
			}
		}else{
			$totQuant = 0;
			$stockQuant = $rsStock['pInStock'];
			$sSQL = "SELECT cartID,cartQuantity,cartDateAdded,cartOrderID,cartClientID,cartSessionID FROM cart WHERE cartCompleted=0 AND cartProdID='" . $theid . "' ORDER BY cartDateAdded DESC";
			$result = mysql_query($sSQL) or print(mysql_error());
			while($rs = mysql_fetch_array($result)){
				$totQuant += $rs["cartQuantity"];
				if((int)$rs["cartOrderID"]==0){
					if(strtotime($rs["cartDateAdded"]) > $bestDate && $totQuant+$stockQuant >= $origquantity) $bestDate = strtotime($rs["cartDateAdded"]);
					if(strtotime($rs['cartDateAdded']) < $stockRelDate) push_stock_item();
				}
			}
			mysql_free_result($result);
			if($stockQuant-$totQuant < $quantity) $quantity = $stockQuant-$totQuant;
			if(($stockQuant+$totQuant) < $origquantity) $outofstockreason=0;
		}
		while($quantity <= 0 && $stockrelitems > 0){
			$stockrelitems--;
			if(@$_SESSION['clientID'] != ''){
				if($stockrelarr[$stockrelitems]['cartClientID'] != $_SESSION['clientID']){
					mysql_query("DELETE FROM cart WHERE cartID=" . $stockrelarr[$stockrelitems]['cartID']) or print(mysql_error());
					$quantity += $stockrelarr[$stockrelitems]['cartQuantity'];
				}
			}else{
				if($stockrelarr[$stockrelitems]['cartClientID'] != $thesessionid){
					mysql_query("DELETE FROM cart WHERE cartID=" . $stockrelarr[$stockrelitems]['cartID']) or print(mysql_error());
					$quantity += $stockrelarr[$stockrelitems]['cartQuantity'];
				}
			}
		}
		if($quantity > 0) $isInStock = TRUE; else $isInStock = FALSE;
	}
	if($isInStock){
		if($bExists){
			mysql_query("UPDATE cart SET cartQuantity=cartQuantity+" . $quantity . " WHERE cartCompleted=0 AND cartID=" . $cartID) or print(mysql_error());
		}else{
			$sSQL = "INSERT INTO cart (cartSessionID,cartClientID,cartProdID,cartQuantity,cartCompleted,cartProdName,cartProdPrice,cartOrderID,cartDateAdded) VALUES (";
			$sSQL .= "'" . $thesessionid . "','" . (@$_SESSION['clientID'] != '' ? $_SESSION['clientID'] : 0) . "','" . $theid . "',";
			$sSQL .= $quantity . ",0,'" . mysql_escape_string(strip_tags($rsStock[getlangid('pName',1)])) . "','" . $rsStock["pPrice"] . "',0,";
			$sSQL .= "'" . date("Y-m-d H:i:s", time() + ($dateadjust*60*60)) . "')";
			mysql_query($sSQL) or print(mysql_error());
			$cartID = mysql_insert_id();
			foreach(@$_POST as $objItem => $objValue){
				if(substr($objItem,0,4)=='optn'){
					if(trim(@$_POST['v' . $objItem])==''){
						if(is_numeric($objValue)){
							$sSQL="SELECT optID,".getlangid('optGrpName',16).','.getlangid('optName',32).',' . $OWSP . "optPriceDiff,optWeightDiff,optType,optFlags FROM options LEFT JOIN optiongroup ON options.optGroup=optiongroup.optGrpID WHERE optID='" . mysql_escape_string($objValue) . "'";
							$result = mysql_query($sSQL) or print(mysql_error());
							if($rs = mysql_fetch_array($result)){
								if(abs($rs['optType']) != 3){
									$sSQL = "INSERT INTO cartoptions (coCartID,coOptID,coOptGroup,coCartOption,coPriceDiff,coWeightDiff) VALUES (" . $cartID . ',' . $rs['optID'] . ",'" . mysql_escape_string($rs[getlangid('optGrpName',16)]) . "','" . mysql_escape_string($rs[getlangid('optName',32)]) . "',";
									if(($rs['optFlags']&1)==0) $sSQL .= $rs['optPriceDiff'] . ','; else $sSQL .= round(($rs['optPriceDiff'] * $rsStock['pPrice'])/100.0, 2) . ',';
									if(($rs['optFlags']&2)==0) $sSQL .= $rs['optWeightDiff'] . ')'; else $sSQL .= multShipWeight($rsStock['pWeight'],$rs['optWeightDiff']) . ')';
								}else
									$sSQL = "INSERT INTO cartoptions (coCartID,coOptID,coOptGroup,coCartOption,coPriceDiff,coWeightDiff) VALUES (" . $cartID . ',' . $rs['optID'] . ",'" . mysql_escape_string($rs[getlangid('optGrpName',16)]) . "','',0,0)";
								mysql_query($sSQL) or print(mysql_error());
							}
							mysql_free_result($result);
						}
					}else{
						$sSQL="SELECT optID,".getlangid("optGrpName",16).",".getlangid("optName",32)." FROM options LEFT JOIN optiongroup ON options.optGroup=optiongroup.optGrpID WHERE optID='" . mysql_escape_string($objValue) . "'";
						$result = mysql_query($sSQL) or print(mysql_error());
						$rs = mysql_fetch_array($result);
						$sSQL = "INSERT INTO cartoptions (coCartID,coOptID,coOptGroup,coCartOption,coPriceDiff,coWeightDiff) VALUES (" . $cartID . "," . $rs["optID"] . ",'" . mysql_escape_string($rs[getlangid("optGrpName",16)]) . "','" . mysql_escape_string(unstripslashes(trim(@$_POST["v" . $objItem]))) . "',0,0)";
						mysql_query($sSQL) or print(mysql_error());
						mysql_free_result($result);
					}
				}
			}
		}
		checkpricebreaks($theid,$rsStock["pPrice"]);
?>
      <table border="0" cellspacing="<?php print $maintablespacing?>" cellpadding="<?php print $maintablepadding?>" width="<?php print $maintablewidth?>" bgcolor="<?php print $maintablebg?>" align="center">
        <tr> 
          <td width="100%" align="center">
            <table width="<?php print $innertablewidth?>" border="0" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
			  <tr>
			    <td align="center"><p>&nbsp;</p>
<?php	if($quantity < $origquantity){
			print '<p><strong><font color="#FF0000">' . $xxInsuff . '</font></strong></p><p>' . str_replace('%s',$quantity,$xxOnlyAd) . '</p><p>' . $xxWanRem . '</p>';
			print '<form method="post" action="cart.php"><input type="hidden" name="delet' . $cartID . '" value="1"><input type="hidden" name="mode" value="update"><input type="submit" value="' . $xxDelete . '"> <input type="button" value="' . $xxCntShp . '" onclick="document.location=\'cart.php\'"></form>';
		}else{
			if(! @isset($cartrefreshseconds)) $cartrefreshseconds=3;
			if(trim(@$_POST['frompage'])!='' && @$actionaftercart==3){
				if($cartrefreshseconds==0 && ob_get_length()!==FALSE)
					header('Location: '. (@$_SERVER['HTTPS'] == 'on' || @$_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'].trim(@$_POST['frompage']));
				else
					print '<meta http-equiv="Refresh" content="'.$cartrefreshseconds.'; URL=' . trim(@$_POST['frompage']) . '">';
			}elseif(@$actionaftercart==4 || $cartrefreshseconds==0){
				if(ob_get_length()===FALSE) print '<meta http-equiv="Refresh" content="0; URL=cart.php">'; else header('Location: '.$storeurl.'cart.php');
			}else
				print '<meta http-equiv="Refresh" content="'.$cartrefreshseconds.'; URL=cart.php">';
			print '<p>' . $quantity . ' <strong>' . $rsStock[getlangid('pName',1)] . '</strong> ' . $xxAddOrd . '</p>';
			print '<p>' . $xxPlsWait . ' <a href="';
			if(trim(@$_POST['frompage'])!='' && @$actionaftercart==3) print trim(@$_POST['frompage']); else print 'cart.php';
			print '"><strong>' . $xxClkHere . '</strong></a>.</p>';
		}	?>
				<p>&nbsp;</p><p>&nbsp;</p>
				</td>
			  </tr>
			</table>
		  </td>
        </tr>
      </table>
<?php
	}else{
?>
      <table border="0" cellspacing="<?php print $maintablespacing?>" cellpadding="<?php print $maintablepadding?>" width="<?php print $maintablewidth?>" bgcolor="<?php print $maintablebg?>" align="center">
        <tr> 
          <td width="100%" align="center">
            <table width="<?php print $innertablewidth?>" border="0" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
			  <tr>
			    <td align="center"><p>&nbsp;</p>
<?php			$bestDate += $stockManage*(60*60);
				$totMins = (int)($bestDate - (time()+($dateadjust*60*60)));
				$totMins = (int)($totMins / 60)+1;
				if(($totMins>600 || $totMins<=0) && $outofstockreason==1) $outofstockreason=0;
				print '<p>' . $xxSrryItm . ' <strong>' . htmlspecialchars($rsStock[getlangid('pName',1)]) . '</strong> ' . $xxIsCntly;
				if($outofstockreason==1) print ' ' . $xxTemprly;
				if($outofstockreason==2) print ' not available in our product database.'; else print ' ' . $xxOutStck . '</p>';
				if($outofstockreason==1){
					print '<p>' . $xxNotChOu . ' ';
					if($totMins > 300)
						print $xxShrtWhl;
					else{
						if($totMins >= 60) print (int)($totMins / 60) . ' hour';
						if($totMins >= 120) print 's';
						$totMins -= ((int)($totMins / 60) * 60);
						if($totMins > 0) print ' ' . $totMins . ' minute';
						if($totMins > 1) print 's';
					}
					print $xxChkBack . '</p>';
				} ?>
				<p><?php print $xxPlease?> <a href="javascript:history.go(-1)"><strong><?php print $xxClkHere?></strong></a> <?php print $xxToRetrn?></p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				</td>
			  </tr>
			</table>
		  </td>
        </tr>
      </table>
<?php
	}
}elseif($checkoutmode=='checkout' || $ppexpresscancel){
	$remember=FALSE;
	$havestate=FALSE;
	if(@$_POST['checktmplogin'] != ''){
		$sSQL = "SELECT tmploginname FROM tmplogin WHERE tmploginid='" . mysql_escape_string(trim(@$_POST['sessionid'])) . "' AND tmploginchk='" . mysql_escape_string(@$_POST['checktmplogin']) . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_array($result)){
			$_SESSION['clientID']=$rs['tmploginname'];
			mysql_free_result($result);
			mysql_query("DELETE FROM tmplogin WHERE tmploginid='" . mysql_escape_string(trim(@$_POST["sessionid"])) . "'") or print(mysql_error());
			$sSQL = "SELECT clUserName,clActions,clLoginLevel,clPercentDiscount,clEmail,clPW FROM customerlogin WHERE clID='" . mysql_escape_string($_SESSION["clientID"]) . "'";
			$result = mysql_query($sSQL) or print(mysql_error());
			if($rs = mysql_fetch_array($result)){
				$_SESSION['clientUser']=$rs['clUserName'];
				$_SESSION['clientActions']=$rs['clActions'];
				$_SESSION['clientLoginLevel']=$rs['clLoginLevel'];
				$_SESSION['clientPercentDiscount']=(100.0-(double)$rs['clPercentDiscount'])/100.0;
				get_wholesaleprice_sql();
				if($rs['clEmail'] != @$_COOKIE['WRITECLL'] || $rs['clPW'] != @$_COOKIE['WRITECLP']) print '<script src="vsadmin/savecookie.php?WRITECLL=' . $rs['clEmail'] . '&WRITECLP=' . $rs['clPW'] . '"></script>';
			}
		}
		mysql_free_result($result);
	}
	if(@$_COOKIE['id1'] != '' && @$_COOKIE['id2'] != ''){
		$sSQL = "SELECT ordName,ordAddress,ordAddress2,ordCity,ordState,ordZip,ordCountry,ordEmail,ordPhone,ordShipName,ordShipAddress,ordShipAddress2,ordShipCity,ordShipState,ordShipZip,ordShipCountry,ordShipPhone,ordPayProvider,ordComLoc,ordExtra1,ordExtra2,ordShipExtra1,ordShipExtra2,ordCheckoutExtra1,ordCheckoutExtra2,ordAddInfo FROM orders WHERE ordID='" . mysql_escape_string(unstripslashes($_COOKIE['id1'])) . "' AND ordSessionID='" . mysql_escape_string(unstripslashes($_COOKIE['id2'])) . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_array($result)){
			$ordName = $rs['ordName'];
			$ordAddress = $rs['ordAddress'];
			$ordAddress2 = $rs['ordAddress2'];
			$ordCity = $rs['ordCity'];
			$ordState = $rs['ordState'];
			$ordZip = $rs['ordZip'];
			$ordCountry = $rs['ordCountry'];
			$ordEmail = $rs['ordEmail'];
			$ordPhone = $rs['ordPhone'];
			$ordShipName = $rs['ordShipName'];
			$ordShipAddress = $rs['ordShipAddress'];
			$ordShipAddress2 = $rs['ordShipAddress2'];
			$ordShipCity = $rs['ordShipCity'];
			$ordShipState = $rs['ordShipState'];
			$ordShipZip = $rs['ordShipZip'];
			$ordShipCountry = $rs['ordShipCountry'];
			$ordShipPhone = $rs['ordShipPhone'];
			$ordPayProvider = $rs['ordPayProvider'];
			$ordComLoc = $rs['ordComLoc'];
			$ordExtra1 = $rs['ordExtra1'];
			$ordExtra2 = $rs['ordExtra2'];
			$ordShipExtra1 = $rs['ordShipExtra1'];
			$ordShipExtra2 = $rs['ordShipExtra2'];
			$ordCheckoutExtra1 = $rs['ordCheckoutExtra1'];
			$ordCheckoutExtra2 = $rs['ordCheckoutExtra2'];
			$ordAddInfo = $rs['ordAddInfo'];
			$remember=TRUE;
		}
		mysql_free_result($result);
	}
	$sSQL = "SELECT stateName,stateAbbrev FROM states WHERE stateEnabled=1 ORDER BY stateName";
	$result = mysql_query($sSQL) or print(mysql_error());
	$numallstates=0;
	$numallcountries=0;
	while($rs = mysql_fetch_array($result))
		$allstates[$numallstates++]=$rs;
	mysql_free_result($result);
	$numhomecountries = 0;
	$nonhomecountries = 0;
	$sSQL = "SELECT countryName,countryOrder,".getlangid("countryName",8)." FROM countries WHERE countryEnabled=1 ORDER BY countryOrder DESC," . getlangid("countryName",8);
	$result = mysql_query($sSQL) or print(mysql_error());
	while($rs = mysql_fetch_array($result)){
		$allcountries[$numallcountries++]=$rs;
		if($rs["countryOrder"]==2)$numhomecountries++;else $nonhomecountries++;
	}
	mysql_free_result($result);
?>
	  <table border="0" cellspacing="<?php print $maintablespacing?>" cellpadding="<?php print $maintablepadding?>" width="<?php print $maintablewidth?>" bgcolor="<?php print $maintablebg?>" align="center">
        <tr> 
          <td width="100%">
<?php
	$alladdresses='';
	$numalladdresses=0;
	if(@$enableclientlogin==TRUE && @$_SESSION['clientID'] != ''){
		$sSQL = "SELECT addID,addIsDefault,addName,addAddress,addAddress2,addState,addCity,addZip,addPhone,addCountry FROM address WHERE addCustID=" . $_SESSION['clientID'] . " ORDER BY addIsDefault";
		$result = mysql_query($sSQL) or print(mysql_error());
		while($rs = mysql_fetch_assoc($result))
			$alladdresses[$numalladdresses++]=$rs;
	} ?>
			<form method="post" name="mainform" action="cart.php" onsubmit="return checkform(this)">
<?php
	if(is_array($alladdresses)){ ?>
<script language="javascript" type="text/javascript">
<!--
var addrs = new Array();
addrs[0]=new Array();addrs[0]['name']='';addrs[0]['address']='';addrs[0]['address2']='';addrs[0]['city']='';addrs[0]['state']='';addrs[0]['zip']='';addrs[0]['phone']='';addrs[0]['country']='';
function checkeditbutton(isshipping){
	adidobj = document.getElementById(isshipping + 'addressid');
	theaddy = adidobj[adidobj.selectedIndex].value;
	if(theaddy=='') document.getElementById(isshipping + 'editbutton').disabled=true; else document.getElementById(isshipping + 'editbutton').disabled=false;
}
function editaddress(isshipping,isaddnew){
	eval(isshipping+'checkaddress=true;');
	adidobj = document.getElementById(isshipping + 'addressid');
	theaddy = adidobj[adidobj.selectedIndex].value;
	if(isaddnew)theaddy=0;
	document.getElementById(isshipping + 'name').value=addrs[theaddy]['name'];
	document.getElementById(isshipping + 'address').value=addrs[theaddy]['address'];
<?php	if(@$useaddressline2==TRUE) print "document.getElementById(isshipping + 'address2').value=addrs[theaddy]['address2'];" ?>
	document.getElementById(isshipping + 'city').value=addrs[theaddy]['city'];
	document.getElementById(isshipping + 'zip').value=addrs[theaddy]['zip'];
	document.getElementById(isshipping + 'phone').value=addrs[theaddy]['phone'];
	thecntry = document.getElementById(isshipping + 'country')
	foundcntry=9999;
	for(ind=0; ind < thecntry.length; ind++){
		if(thecntry[ind].value==addrs[theaddy]['country']){
			thecntry.selectedIndex=ind;
			foundcntry=ind;
		}
	}
	if(foundcntry==9999)thecntry.selectedIndex=0;
	foundstate=0;havegotstate=false;
<?php
	if(is_array($allstates)){ ?>
	thestate = document.getElementById(isshipping + 'state')
	if(foundcntry < <?php print $numhomecountries?>){
		for(ind=0; ind < thestate.length; ind++){
			if(thestate[ind].value==addrs[theaddy]['state']){
				foundstate=ind;
				havegotstate=true;
			}
		}
	}
	thestate.selectedIndex=foundstate;
<?php
	}
	if($nonhomecountries>0) print "if(! havegotstate)document.getElementById(isshipping + 'state2').value=addrs[theaddy]['state'];" ?>
}
<?php	for($index=0; $index < $numalladdresses; $index++){
			print 'addrs[' . $alladdresses[$index]['addID'] . "]=new Array();\r\n";
			print 'addrs[' . $alladdresses[$index]['addID'] . "]['name']='" . str_replace("'","\'", $alladdresses[$index]['addName']) . "';\r\n";
			print 'addrs[' . $alladdresses[$index]['addID'] . "]['address']='" . str_replace("'","\'", $alladdresses[$index]['addAddress']) . "';\r\n";
			print 'addrs[' . $alladdresses[$index]['addID'] . "]['address2']='" . str_replace("'","\'", $alladdresses[$index]['addAddress2']) . "';\r\n";
			print 'addrs[' . $alladdresses[$index]['addID'] . "]['state']='" . str_replace("'","\'", $alladdresses[$index]['addState']) . "';\r\n";
			print 'addrs[' . $alladdresses[$index]['addID'] . "]['city']='" . str_replace("'","\'", $alladdresses[$index]['addCity']) . "';\r\n";
			print 'addrs[' . $alladdresses[$index]['addID'] . "]['zip']='" . str_replace("'","\'", $alladdresses[$index]['addZip']) . "';\r\n";
			print 'addrs[' . $alladdresses[$index]['addID'] . "]['phone']='" . str_replace("'","\'", $alladdresses[$index]['addPhone']) . "';\r\n";
			print 'addrs[' . $alladdresses[$index]['addID'] . "]['country']='" . str_replace("'","\'", $alladdresses[$index]['addCountry']) . "';\r\n";
		} ?>
//-->
</script>
<?php
	}
	writehiddenvar('mode', 'go');
	writehiddenvar('sessionid', strip_tags(trim($thesessionid)));
	writehiddenvar('PARTNER', strip_tags(trim(@$_POST['PARTNER']))); ?>
	<input type="hidden" name="addaddress" id="addaddress" value="<?php print ($numalladdresses>0 ? '' : 'add')?>">
	<input type="hidden" name="saddaddress" id="saddaddress" value="<?php print ($numalladdresses>0 ? '' : 'add')?>">
			  <table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
				<tr height="34">
				  <td class="cobhl" bgcolor="#EBEBEB" align="center" colspan="4"><strong><?php print $xxCstDtl?></strong></td>
				</tr>
<?php
	function writeshippingflags($colspan){
		global $commercialloc,$saturdaydelivery,$addshippinginsurance,$allowsignaturerelease,$signatureoption,$insidedelivery,$ordComLoc,$xxComLoc,$xxSatDel,$xxWantIns,$xxSigRel,$xxInsDel;
		if(@$commercialloc==TRUE){ ?>
			<tr><td align="right" class="cobhl" bgcolor="#EBEBEB" colspan="<?php print $colspan?>"><input type="checkbox" name="commercialloc" value="Y" <?php if(($ordComLoc & 1)==1) print 'checked'?> /></td>
			<td align="left" class="cobll" bgcolor="#FFFFFF" colspan="<?php print 4-$colspan?>"><font size="1"><?php print $xxComLoc?></font></td></tr>
<?php	}
		if(@$saturdaydelivery==TRUE){ ?>
			<tr><td align="right" class="cobhl" bgcolor="#EBEBEB" colspan="<?php print $colspan?>"><input type="checkbox" name="saturdaydelivery" value="Y" <?php if(($ordComLoc & 4)==4) print 'checked'?> /></td>
			<td align="left" class="cobll" bgcolor="#FFFFFF" colspan="<?php print 4-$colspan?>"><font size="1"><?php print $xxSatDel?></font></td></tr>
<?php	}
		if(abs(@$addshippinginsurance)==2){ ?>
			<tr><td align="right" class="cobhl" bgcolor="#EBEBEB" colspan="<?php print $colspan?>"><input type="checkbox" name="wantinsurance" value="Y" <?php if(($ordComLoc & 2)==2) print 'checked'?> /></td>
			<td align="left" class="cobll" bgcolor="#FFFFFF" colspan="<?php print 4-$colspan?>"><font size="1"><?php print $xxWantIns?></font></td></tr>
<?php	}
		if(@$allowsignaturerelease==TRUE && @$signatureoption != ''){ ?>
			<tr><td align="right" class="cobhl" bgcolor="#EBEBEB" colspan="<?php print $colspan?>"><input type="checkbox" name="signaturerelease" value="Y" <?php if(($ordComLoc & 8)==8) print 'checked'?> /></td>
			<td align="left" class="cobll" bgcolor="#FFFFFF" colspan="<?php print 4-$colspan?>"><font size="1"><?php print $xxSigRel?></font></td></tr>
<?php	}
		if(@$insidedelivery==TRUE){ ?>
			<tr><td align="right" class="cobhl" bgcolor="#EBEBEB" colspan="<?php print $colspan?>"><input type="checkbox" name="insidedelivery" value="Y" <?php if(($ordComLoc & 16)==16) print 'checked'?> /></td>
			<td align="left" class="cobll" bgcolor="#FFFFFF" colspan="<?php print 4-$colspan?>"><font size="1"><?php print $xxInsDel?></font></td></tr>
<?php	}
	}
	if($numalladdresses > 0){ ?>
				<tr height="30">
				  <td align="right" class="cobhl" bgcolor="#EBEBEB" colspan="2" width="30%"><strong><?php print $xxBilAdd?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF" colspan="2">
<?php	function writeaddressspans($isshp){
			global $useaddressline2,$extraorderfield1html,$extraorderfield2html,$extraorderfield1required,$extraorderfield2required,$extraorderfield1,$extraorderfield2,$numallstates,$nonhomecountries,$numalladdresses,$alladdresses,$xxSamAs,$xxName,$xxAddress,$xxAddress2,$xxCity,$xxState,$xxNonState,$xxCountry,$zipoptional,$xxZip,$xxPhone;
?>		<span name="<?php print $isshp?>addressspan1" id="<?php print $isshp?>addressspan1" style="display:block"><select name="<?php print $isshp?>addressid" id="<?php print $isshp?>addressid" size="1" onchange="checkeditbutton('<?php print $isshp?>')"><?php
		if($isshp=='s') print '<option value="">' . $xxSamAs . '</option>';
		for($index=0; $index < $numalladdresses; $index++){
			print '<option value="' . $alladdresses[$index]['addID'] . '"' . ($alladdresses[$index]['addIsDefault']==($isshp=='s'?2:1) ? ' selected' : '') . '>' . $alladdresses[$index]['addName'] . ', ' . $alladdresses[$index]['addAddress'] . (trim($alladdresses[$index]['addAddress2']) != '' ? ', ' . $alladdresses[$index]['addAddress2'] : '') . ', ' . $alladdresses[$index]['addState'] . '</option>';
		} ?></select> <input type="button" value="Edit" id="<?php print $isshp?>editbutton" onclick="editaddress('<?php print $isshp?>',false);document.getElementById('<?php print $isshp?>addressspan1').style.display='none';document.getElementById('<?php print $isshp?>addressspan2').style.display='block';document.getElementById('<?php print $isshp?>addaddress').value='edit';"> <input type="button" value="New" onclick="editaddress('<?php print $isshp?>',true);document.getElementById('<?php print $isshp?>addressspan1').style.display='none';document.getElementById('<?php print $isshp?>addressspan2').style.display='block';document.getElementById('<?php print $isshp?>addaddress').value='add';">
		</span><span name="<?php print $isshp?>addressspan2" id="<?php print $isshp?>addressspan2" style="display:none">
		<table class="cobtbl" width="100%" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
		<?php	if(trim(@$extraorderfield1) != ''){ ?>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB" style="font-size: 11px;"><strong><?php print (@$extraorderfield1required==TRUE ? '<font color="#FF0000">*</font>' : '') . $extraorderfield1 ?>:</strong></td><td align="left" class="cobll" bgcolor="#FFFFFF"><?php if(@$extraorderfield1html != '') print $extraorderfield1html; else print '<input type="text" name="' . $isshp . 'ordextra1" id="' . $isshp . 'ordextra1" size="20"  style="font-size: 11px;" />'?></td></tr>
		<?php	} ?>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB" style="font-size: 11px;"><strong><font color='#FF0000'>*</font><?php print $xxName?>:</strong></td><td align="left" class="cobll" bgcolor="#FFFFFF"><input type="text" name="<?php print $isshp?>name" id="<?php print $isshp?>name" size="20" style="font-size: 11px;" /></td></tr>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB" style="font-size: 11px;"><strong><font color='#FF0000'>*</font><?php print $xxAddress?>:</strong></td><td align="left" class="cobll" bgcolor="#FFFFFF"><input type="text" name="<?php print $isshp?>address" id="<?php print $isshp?>address" size="25" style="font-size: 11px;" /></td></tr>
		<?php	if(@$useaddressline2==TRUE){ ?>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB" style="font-size: 11px;"><strong><?php print $xxAddress2?>:</strong></td><td align="left" class="cobll" bgcolor="#FFFFFF"><input type="text" name="<?php print $isshp?>address2" id="<?php print $isshp?>address2" size="25" style="font-size: 11px;" /></td></tr>
		<?php	} ?>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB" style="font-size: 11px;"><strong><font color='#FF0000'>*</font><?php print $xxCity?>:</strong></td><td align="left" class="cobll" bgcolor="#FFFFFF"><input type="text" name="<?php print $isshp?>city" id="<?php print $isshp?>city" size="20" style="font-size: 11px;" /></td></tr>
		<?php	if($numallstates > 0){ ?>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB" style="font-size: 11px;"><strong><font color='#FF0000'><span id="<?php print $isshp?>outspandd" style="visibility:hidden">*</span></font><?php print $xxState?>:</strong></td><td align="left" class="cobll" bgcolor="#FFFFFF"><select name="<?php print $isshp?>state" id="<?php print $isshp?>state" size="1" onchange="dosavestate('')" style="font-size: 11px;"><?php $havestate = show_states(-1) ?></select></td></tr>
		<?php	}
				if($nonhomecountries != 0){ ?>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB" style="font-size: 11px;"><strong><font color='#FF0000'><span id="<?php print $isshp?>outspan" style="visibility:hidden">*</span></font><?php print $xxNonState?>:</strong></td><td align="left" class="cobll" bgcolor="#FFFFFF"><input type="text" name="<?php print $isshp?>state2" id="<?php print $isshp?>state2" size="20" style="font-size: 11px;" /></td></tr>
		<?php	} ?>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB" style="font-size: 11px;"><strong><font color='#FF0000'>*</font><?php print $xxCountry?>:</strong></td><td align="left" class="cobll" bgcolor="#FFFFFF"><select name="<?php print $isshp?>country" id="<?php print $isshp?>country" size="1" onchange="checkoutspan('<?php print $isshp?>')"  style="font-size: 11px;"><?php show_countries(-1) ?></select></td></tr>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB" style="font-size: 11px;"><strong><font color='#FF0000'><?php if(@$zipoptional != TRUE) print '*'?></font><?php print $xxZip?>:</strong></td><td align="left" class="cobll" bgcolor="#FFFFFF"><input type="text" name="<?php print $isshp?>zip" id="<?php print $isshp?>zip" size="10" style="font-size: 11px;" /></td></tr>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB" style="font-size: 11px;"><strong><font color='#FF0000'><?php if($isshp=='') print '*'?></font><?php print $xxPhone?>:</strong></td><td align="left" class="cobll" bgcolor="#FFFFFF"><input type="text" name="<?php print $isshp?>phone" id="<?php print $isshp?>phone" size="20" style="font-size: 11px;" /></td></tr>
		<?php	if(trim(@$extraorderfield2) != ''){ ?>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB" style="font-size: 11px;"><strong><?php print (@$extraorderfield2required==TRUE ? '<font color="#FF0000">*</font>' : '') . $extraorderfield2 ?>:</strong></td><td align="left" class="cobll" bgcolor="#FFFFFF"><?php if(@$extraorderfield2html != '') print $extraorderfield2html; else print '<input type="text" name="' . $isshp . 'ordextra2" id="' . $isshp . 'ordextra2" size="20" style="font-size: 11px;" />'?></td></tr>
		<?php	} ?>
		<tr><td align="center" colspan="2" class="cobll" bgcolor="#FFFFFF" style="font-size: 11px;"><input type="button" value="Cancel" onclick="document.getElementById('<?php print $isshp?>addressspan2').style.display='none';document.getElementById('<?php print $isshp?>addressspan1').style.display='block';document.getElementById('<?php print $isshp?>addaddress').value='';<?php print $isshp?>checkaddress=false;" style="font-size: 11px;"></td></tr>
		</table></span>
<?php	}
		writeaddressspans(''); ?>
				  </td>
				</tr>
<?php	writeshippingflags(2);
		if(@$noshipaddress != TRUE){ ?>
				<tr height="30">
				  <td align="right" class="cobhl" bgcolor="#EBEBEB" colspan="2"><strong><?php print $xxShpAdd?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF" colspan="2"> 
<?php		writeaddressspans('s'); ?>
				  </td>
				</tr>
<?php	}
	}else{
		if(trim(@$extraorderfield1) != ''){ ?>
				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php if(@$extraorderfield1required==TRUE) print '<font color="#FF0000">*</font>';
									print $extraorderfield1 ?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF" colspan="3"><?php if(@$extraorderfield1html != '') print $extraorderfield1html; else print '<input type="text" name="ordextra1" size="20" value="' . @$ordExtra1 . '" />'?></td>
				</tr>
<?php	} ?>
				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB" width="25%"><strong><font color='#FF0000'>*</font><?php print $xxName?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF" width="25%"><input type="text" name="name" size="20" value="<?php print @$ordName?>" /></td>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB" width="25%"><strong><font color='#FF0000'>*</font><?php print $xxEmail?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF" width="25%"><input type="text" name="email" size="20" value="<?php print @$ordEmail?>" /></td>
				</tr>
				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><font color='#FF0000'>*</font><?php print $xxAddress?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"<?php if(@$useaddressline2==TRUE) print ' colspan="3"'?>><input type="text" name="address" id="address" size="25" value="<?php print @$ordAddress?>" /></td>
<?php	if(@$useaddressline2==TRUE){ ?>
				</tr>
				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php print $xxAddress2?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"><input type="text" name="address2" size="25" value="<?php print @$ordAddress2?>" /></td>
<?php	} ?>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><font color='#FF0000'>*</font><?php print $xxCity?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"><input type="text" name="city" size="20" value="<?php print @$ordCity?>" /></td>
				</tr>
<?php
	if($numallstates>0 || $nonhomecountries != 0){ ?>
				<tr>
<?php	if($numallstates > 0){ ?>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><font color='#FF0000'><span id="outspandd" style="visibility:hidden">*</span></font><?php print $xxState?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"><select name="state" size="1" onchange="dosavestate('')"><?php $havestate = show_states(@$ordState) ?></select></td>
<?php	}
		if($nonhomecountries==0)
			print '<td class="cobll" bgcolor="#FFFFFF" colspan="2">&nbsp;</td>';
		else{ ?>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><font color='#FF0000'><span id="outspan" style="visibility:hidden">*</span></font><?php print $xxNonState?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"><input type="text" name="state2" size="20" value="<?php if(! $havestate) print @$ordState?>" /></td>
<?php	}
		if($numallstates==0) print '<td class="cobll" bgcolor="#FFFFFF" colspan="2">&nbsp;</td>' ?>
				</tr>
<?php
	} ?>
				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><font color='#FF0000'>*</font><?php print $xxCountry?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"><select name="country" size="1" onchange="checkoutspan('')">
<?php
	show_countries(@$ordCountry) ?>
					</select>
				  </td>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><font color='#FF0000'><?php if(@$zipoptional != TRUE) print '*'?></font><?php print $xxZip?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"><input type="text" name="zip" size="10" value="<?php print @$ordZip?>" /></td>
				</tr>
				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><font color='#FF0000'>*</font><?php print $xxPhone?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"<?php	if(trim(@$extraorderfield2)=='') print ' colspan="3"'?>><input type="text" name="phone" size="20" value="<?php print @$ordPhone?>" /></td>
			<?php	if(trim(@$extraorderfield2) != ''){ ?>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php if(@$extraorderfield2required==TRUE) print '<font color="#FF0000">*</font>';
									print $extraorderfield2 ?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"><?php if(@$extraorderfield2html != '') print $extraorderfield2html; else print '<input type="text" name="ordextra2" size="20" value="' . @$ordExtra2 . '" />'?></td>
			<?php	} ?>
				</tr>
			<?php	writeshippingflags(1);
				if(@$noshipaddress != TRUE){ ?>
				<tr height="30">
				  <td align="center" colspan="4" class="cobhl" bgcolor="#EBEBEB"><strong><?php print $xxShpDiff?></strong></td>
				</tr>
<?php					if(trim(@$extraorderfield1) != ''){ ?>
				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php if(@$extraorderfield1required==TRUE) print '<font color="#FF0000">*</font>';
									print $extraorderfield1 ?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF" colspan="3"><?php if(@$extraorderfield1html != '') print $extraorderfield1html; else print '<input type="text" name="ordshipextra1" size="20" value="' . @$ordShipExtra1 . '" />'?></td>
				</tr>
<?php					} ?>
				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php print $xxName?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF" colspan="3"><input type="text" name="sname" size="20" value="<?php print @$ordShipName?>" /></td>
				</tr>
				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php print $xxAddress?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"<?php if(@$useaddressline2==TRUE) print ' colspan="3"'?>><input type="text" name="saddress" id="saddress" size="25" value="<?php print trim(@$ordShipAddress)?>" /></td>
<?php	if(@$useaddressline2==TRUE){ ?>
				</tr>
				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php print $xxAddress2?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"><input type="text" name="saddress2" size="25" value="<?php print @$ordShipAddress2?>" /></td>
<?php	} ?>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php print $xxCity?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"><input type="text" name="scity" size="20" value="<?php print @$ordShipCity?>" /></td>
				</tr>
<?php	if($numallstates > 0 || $nonhomecountries != 0){ ?>
				<tr>
<?php		if($numallstates > 0){ ?>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><font color='#FF0000'><span id="soutspandd" style="visibility:hidden">*</span></font><?php print $xxState?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"><select name="sstate" size="1" onchange="dosavestate('s')"><?php $havestate = show_states(@$ordShipState) ?></select></td>
<?php		}
			if($nonhomecountries==0)
				print '<td class="cobll" bgcolor="#FFFFFF" colspan="2">&nbsp;</td>';
			else{ ?>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><font color='#FF0000'><span id="soutspan" style="visibility:hidden">*</span></font><?php print $xxNonState?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"><input type="text" name="sstate2" size="20" value="<?php if(! $havestate) print @$ordShipState?>" /></td>
<?php		}
			if($numallstates==0) print '<td class="cobll" bgcolor="#FFFFFF" colspan="2">&nbsp;</td>'; ?>
				</tr>
<?php	} ?>
				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php print $xxCountry?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"><select name="scountry" size="1" onchange="checkoutspan('s')">
<?php		show_countries(@$ordShipCountry) ?>
					</select>
				  </td>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php print $xxZip?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"><input type="text" name="szip" size="10" value="<?php print @$ordShipZip?>" /></td>
				</tr>
				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php print $xxPhone?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"<?php	if(trim(@$extraorderfield2)=='') print ' colspan="3"' ?>><input type="text" name="sphone" size="20" value="<?php print @$ordShipPhone?>" /></td>
			<?php	if(trim(@$extraorderfield2) != ''){ ?>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php if(@$extraorderfield2required==TRUE) print '<font color="#FF0000">*</font>';
									print $extraorderfield2 ?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF"><?php if(@$extraorderfield2html != '') print $extraorderfield2html; else print '<input type="text" name="ordshipextra2" size="20" value="' . @$ordShipExtra2 . '" />'?></td>
			<?php	} ?>
				</tr>
			<?php	} // noshipaddress
	} // ($numalladdresses>0) ?>
				<tr height="30">
				  <td class="cobhl" bgcolor="#EBEBEB" align="center" colspan="4"><strong><?php print $xxMisc?></strong></td>
				</tr>
				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php print $xxAddInf?>.</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF" colspan="3"><textarea name="ordAddInfo" rows="3" wrap=virtual cols="44"><?php print @$ordAddInfo?></textarea></td>
				</tr>
<?php	if(trim(@$extracheckoutfield1) != ''){
			$checkoutfield1 = '<strong>' . ($extracheckoutfield1required==TRUE ? '<font color="#FF0000">*</font>' : '') . $extracheckoutfield1 . '</strong>';
			$checkoutfield2 = (@$extracheckoutfield1html != '' ? $extracheckoutfield1html : '<input type="text" name="ordcheckoutextra1" size="20" value="' . @$ordCheckoutExtra1 . '" />');
?>				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><?php if(@$extracheckoutfield1reverse) print $checkoutfield2; else print $checkoutfield1 . '<strong>:</strong>'?></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF" colspan="3"><?php if(@$extracheckoutfield1reverse) print $checkoutfield1; else print $checkoutfield2 ?></td>
				</tr>
<?php	}
		if(trim(@$extracheckoutfield2) != ''){
			$checkoutfield1 = '<strong>' . ($extracheckoutfield2required==TRUE ? '<font color="#FF0000">*</font>' : '') . $extracheckoutfield2 . '</strong>';
			$checkoutfield2 = (@$extracheckoutfield2html != '' ? $extracheckoutfield2html : '<input type="text" name="ordcheckoutextra2" size="20" value="' . @$ordCheckoutExtra2 . '" />');
?>				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><?php if(@$extracheckoutfield2reverse) print $checkoutfield2; else print $checkoutfield1 . '<strong>:</strong>' ?></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF" colspan="3"><?php if(@$extracheckoutfield2reverse) print $checkoutfield1; else print $checkoutfield2 ?></td>
				</tr>
<?php	}
		if(@$termsandconditions==TRUE){ ?>
				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><input type="checkbox" name="license" value="1" /></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF" colspan="3"><?php print $xxTermsCo?></td>
				</tr>
<?php	}
		if(@$_SESSION['clientID']=='' && @$noremember != TRUE){ ?>
				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><input type="checkbox" name="remember" value="1" <?php if($remember) print 'checked'?> /></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF" colspan="3"><strong><?php print $xxRemMe?></strong><br /><font size="1"><?php print $xxOpCook?></font></td>
				</tr>
<?php	}
		if(@$nomailinglist != TRUE){ ?>
				<tr>
				  <td align="right" class="cobhl" bgcolor="#EBEBEB"><input type="checkbox" name="allowemail" value="ON" <?php if(@$allowemaildefaulton) print 'checked'?> /></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF" colspan="3"><strong><?php print $xxAlPrEm?></strong><br /><font size="1"><?php print $xxNevDiv?></font></td>
				</tr>
<?php	}
				if(@$nogiftcertificate != true){ ?>
				<tr height="30"><td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php print $xxGifNum?>:</strong></td><td align="left" class="cobll" bgcolor="#FFFFFF" colspan="3"><input type="text" name="cpncode" size="20" /></td></tr>
<?php			}
					if(@$_SESSION['clientLoginLevel'] != '') $minloglevel=$_SESSION['clientLoginLevel']; else $minloglevel=0;
					$sSQL = "SELECT payProvID,".getlangid('payProvShow',128)." FROM payprovider WHERE payProvEnabled=1 AND payProvLevel<=" . $minloglevel . " AND NOT (payProvID IN (19,20)) ORDER BY payProvOrder";
					$result = mysql_query($sSQL) or print(mysql_error());
					if(mysql_num_rows($result)==0){ ?>
				<tr><td colspan="4" align="center" class="cobhl" bgcolor="#EBEBEB"><strong><?php print $xxNoPay?></strong></td></tr>
<?php				}elseif(mysql_num_rows($result)==1){
						$rs = mysql_fetch_assoc($result);
						writehiddenvar('payprovider',$rs['payProvID']);
					}else{ ?>
			    <tr height="30"><td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php print $xxPlsChz?>:</strong></td>
				  <td align="left" class="cobll" bgcolor="#FFFFFF" colspan="3"><select name="payprovider" size="1">
<?php					while($rs = mysql_fetch_assoc($result)){
							print "<option value='" . $rs['payProvID'] . "'";
							if(@$ordPayProvider==$rs['payProvID']) print ' selected';
							print '>' . $rs[getlangid('payProvShow',128)] . "</option>\n";
						} ?></select></td></tr>
<?php				} ?>
				<tr>
			      <td height="30" align="center" class="cobll" bgcolor="#FFFFFF" colspan="4"><table width="100%" cellspacing="0" cellpadding="0" border="0">
				    <tr>
					  <td class="cobll" bgcolor="#FFFFFF" width="16" height="26" align="right" valign="bottom">&nbsp;</td>
					  <td class="cobll" bgcolor="#FFFFFF" width="100%" align="center"><input type="image" value="Checkout" border="0" src="images/checkout.gif" alt="<?php print $xxCOTxt?>" /></td>
					  <td class="cobll" bgcolor="#FFFFFF" width="16" height="26" align="right" valign="bottom"><img src="images/tablebr.gif" alt="" /></td>
					</tr></table>
				  </td>
				</tr>
			  </table>
			</form>
		  </td>
        </tr>
      </table>
<script language="javascript" type="text/javascript">
var checkedfullname=false;
var numhomecountries=0,nonhomecountries=0,checkaddress=true,scheckaddress=true;
function checkform(frm)
{
if(checkaddress){
<?php if(trim(@$extraorderfield1)!='' && @$extraorderfield1required==TRUE){ ?>
if(frm.ordextra1.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $extraorderfield1?>\".");
	frm.ordextra1.focus();
	return (false);
}
<?php } ?>
if(frm.name.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxName?>\".");
	frm.name.focus();
	return (false);
}
gotspace=false;
var checkStr = frm.name.value;
for (i = 0; i < checkStr.length; i++){
	if(checkStr.charAt(i)==" ")
		gotspace=true;
}
if(!checkedfullname && !gotspace){
	alert("<?php print $xxFulNam?> \"<?php print $xxName?>\".");
	frm.name.focus();
	checkedfullname=true;
	return (false);
}
<?php	if(! is_array($alladdresses)){ ?>
if(frm.email.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxEmail?>\".");
	frm.email.focus();
	return (false);
}
validemail=0;
var checkStr = frm.email.value;
for (i = 0; i < checkStr.length; i++){
	if(checkStr.charAt(i)=="@")
		validemail |= 1;
	if(checkStr.charAt(i)==".")
		validemail |= 2;
}
if(validemail != 3){
	alert("<?php print $xxValEm?>");
	frm.email.focus();
	return (false);
}
<?php	} ?>
if(frm.address.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxAddress?>\".");
	frm.address.focus();
	return (false);
}
if(frm.city.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxCity?>\".");
	frm.city.focus();
	return (false);
}
if(frm.country.selectedIndex < numhomecountries){
<?php	if($numallstates>0 && $xxOutState!=''){ ?>
	if(frm.state.selectedIndex==0){
		alert("<?php print $xxPlsSlct . " " . $xxState?>");
		frm.state.focus();
		return (false);
	}
<?php	} ?>
}else{
<?php	if($nonhomecountries>0){ ?>
	if(frm.state2.value==""){
		alert("<?php print $xxPlsEntr?> \"<?php print str_replace("<br />"," ",$xxNonState)?>\".");
		frm.state2.focus();
		return (false);
	}
<?php	} ?>}
if(frm.zip.value==""<?php if(@$zipoptional==TRUE) print ' && FALSE'?>){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxZip?>\".");
	frm.zip.focus();
	return (false);
}
if(frm.phone.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxPhone?>\".");
	frm.phone.focus();
	return (false);
}
<?php if(trim(@$extraorderfield2)!='' && @$extraorderfield2required==TRUE){ ?>
if(frm.ordextra2.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $extraorderfield2?>\".");
	frm.ordextra2.focus();
	return (false);
}
<?php } ?>
}
<?php if(@$noshipaddress != TRUE){ ?>
if(scheckaddress && frm.saddress.value!=""){
<?php	if(trim(@$extraorderfield1)!='' && @$extraorderfield1required==TRUE){ ?>
if(frm.ordshipextra1.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $extraorderfield1?>\".");
	frm.ordshipextra1.focus();
	return (false);
}
<?php	} ?>
	if(frm.sname.value==""){
		alert("<?php print $xxShpDtls?>\n\n<?php print $xxPlsEntr?> \"<?php print $xxName?>\".");
		frm.sname.focus();
		return (false);
	}
	if(frm.scity.value==""){
		alert("<?php print $xxShpDtls?>\n\n<?php print $xxPlsEntr?> \"<?php print $xxCity?>\".");
		frm.scity.focus();
		return (false);
	}
	if(frm.scountry.selectedIndex < numhomecountries){
<?php	if($numallstates>0){ ?>
		if(frm.sstate.selectedIndex==0){
			alert("<?php print $xxShpDtls?>\n\n<?php print $xxPlsSlct . " " . $xxState?>.");
			frm.sstate.focus();
			return (false);
		}
<?php	} ?>
	}else{
<?php	if($nonhomecountries>0){ ?>
		if(frm.sstate2.value==""){
			alert("<?php print $xxShpDtls?>\n\n<?php print $xxPlsEntr?> \"<?php print str_replace("<br />"," ",$xxNonState)?>\".");
			frm.sstate2.focus();
			return (false);
		}
<?php	} ?>
	}
	if(frm.szip.value==""<?php if(@$zipoptional==TRUE) print ' && FALSE'?>){
		alert("<?php print $xxShpDtls?>\n\n<?php print $xxPlsEntr?> \"<?php print $xxZip?>\".");
		frm.szip.focus();
		return (false);
	}
<?php	if(trim(@$extraorderfield2) != '' && @$extraorderfield2required==TRUE){ ?>
if(frm.ordshipextra2.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $extraorderfield2?>\".");
	frm.ordshipextra2.focus();
	return (false);
}
<?php	} ?>
}
<?php }
		if(trim(@$extracheckoutfield1)!='' && @$extracheckoutfield1required==TRUE){ ?>
if(frm.ordcheckoutextra1.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $extracheckoutfield1?>\".");
	frm.ordcheckoutextra1.focus();
	return (false);
}
<?php	}
		if(trim(@$extracheckoutfield2)!='' && @$extracheckoutfield2required==TRUE){ ?>
if(frm.ordcheckoutextra2.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $extracheckoutfield2?>\".");
	frm.ordcheckoutextra2.focus();
	return (false);
}
<?php	}
		if(@$_SESSION['clientID']=='' && @$noremember != TRUE){ ?>
if(frm.remember.checked==false){
	if(confirm("<?php print $xxWntRem?>")){
		frm.remember.checked=true
	}
}
<?php	}
		if(@$termsandconditions==TRUE){ ?>
if(frm.license.checked==false){
	alert("<?php print $xxPlsProc?>");
	frm.license.focus();
	return (false);
}
<?php	} ?>
return (true);
}
<?php if(@$termsandconditions==TRUE){ ?>
function showtermsandconds(){
newwin=window.open("termsandconditions.php","Terms","menubar=no, scrollbars=yes, width=420, height=380, directories=no,location=no,resizable=yes,status=no,toolbar=no");
}
<?php } ?>
var savestate=0;
var ssavestate=0;
function dosavestate(shp){
	thestate = eval('document.forms.mainform.'+shp+'state');
	eval(shp+'savestate = thestate.selectedIndex');
}
function checkoutspan(shp){
if(shp=='s' && document.getElementById('saddress').value=="")visib='hidden';else visib='visible';<?php
if($nonhomecountries>0) print "thestyle = document.getElementById(shp+'outspan').style;\r\n";
if($numallstates>0){
	print "theddstyle = document.getElementById(shp+'outspandd').style;\r\n";
	print "thestate = eval('document.forms.mainform.'+shp+'state');\r\n";
} ?>
thecntry = eval('document.forms.mainform.'+shp+'country');
if(thecntry.selectedIndex < numhomecountries){<?php
if($nonhomecountries>0) print "thestyle.visibility='hidden';\r\n";
if($numallstates>0){
	print "theddstyle.visibility=visib;\r\n";
	print "thestate.disabled=false;\r\n";
	print "eval('thestate.selectedIndex='+shp+'savestate');\r\n";
} ?>
}else{<?php
if($nonhomecountries>0) print "thestyle.visibility=visib;\r\n";
if($numallstates>0){ ?>
theddstyle.visibility="hidden";
if(thestate.disabled==false){
thestate.disabled=true;
eval(shp+'savestate = thestate.selectedIndex');
thestate.selectedIndex=0;}
<?php	} ?>
}}
<?php
	if($numallstates>0 && ! is_array($alladdresses)) print "savestate = document.forms.mainform.state.selectedIndex;\r\n";
	if(is_array($alladdresses)) print "checkaddress=false;scheckaddress=false;\r\n";
	if(is_array($alladdresses) && @$noshipaddress!=TRUE) print "checkeditbutton('s');";
	print 'numhomecountries=' . $numhomecountries . ";checkoutspan('');\r\n";
	if(@$noshipaddress!=TRUE && ! is_array($alladdresses)){
		if($numallstates>0) print "ssavestate = document.forms.mainform.sstate.selectedIndex;\r\n";
		print "checkoutspan('s')\r\n";
	}
?></script><?php
}elseif($checkoutmode=='go' || $paypalexpress){
	if(! $paypalexpress){
		$thesessionid = trim(unstripslashes(@$_POST["sessionid"]));
		if(@$enableclientlogin && @$_SESSION['clientID'] != ''){
			$sSQL = "SELECT clEmail FROM customerlogin WHERE clEmail<>'' AND clID=" . $_SESSION['clientID'];
			$result = mysql_query($sSQL) or print(mysql_error());
			if($rs = mysql_fetch_assoc($result)) $ordEmail = trim($rs['clEmail']); else $ordEmail = trim(unstripslashes(@$_POST['email']));
		}else
			$ordEmail = trim(unstripslashes(@$_POST['email']));
		if(@$_POST['allowemail']=='ON')
			mysql_query("INSERT INTO mailinglist (email) VALUES ('" . mysql_escape_string(strtolower($ordEmail)) . "')");
		if(@$enableclientlogin && @$_POST['addressid'] != '' && @$_POST['addaddress']=='' && @$_SESSION['clientID'] != ''){
			$sSQL = "SELECT addName,addAddress,addAddress2,addCity,addState,addZip,addCountry,addPhone,addExtra1,addExtra2 FROM address WHERE addCustID=" . $_SESSION['clientID'] . " AND addID='" . mysql_escape_string(@$_POST['addressid']) . "'";
			$result = mysql_query($sSQL) or print(mysql_error());
			if($rs = mysql_fetch_assoc($result)){
				$ordName = $rs['addName'];
				$ordAddress = $rs['addAddress'];
				$ordAddress2 = $rs['addAddress2'];
				$ordCity = $rs['addCity'];
				$ordState = $rs['addState'];
				$ordZip = $rs['addZip'];
				$ordCountry = $rs['addCountry'];
				$ordPhone = $rs['addPhone'];
				$ordExtra1 = $rs['addExtra1'];
				$ordExtra2 = $rs['addExtra2'];
			}
		}else{
			$ordName = trim(unstripslashes(@$_POST['name']));
			$ordAddress = trim(unstripslashes(@$_POST['address']));
			$ordAddress2 = trim(unstripslashes(@$_POST['address2']));
			$ordCity = trim(unstripslashes(@$_POST['city']));
			if(trim(@$_POST['state']) != '')
				$ordState = trim(unstripslashes(@$_POST['state']));
			else
				$ordState = trim(unstripslashes(@$_POST['state2']));
			$ordZip = trim(unstripslashes(@$_POST['zip']));
			$ordCountry = trim(unstripslashes(@$_POST['country']));
			$ordPhone = trim(unstripslashes(@$_POST['phone']));
			$ordExtra1 = trim(unstripslashes(@$_POST['ordextra1']));
			$ordExtra2 = trim(unstripslashes(@$_POST['ordextra2']));
		}
		if(@$enableclientlogin && @$_POST['saddressid'] != '' && @$_POST['saddaddress']=='' && @$_SESSION['clientID'] != ''){
			$sSQL = "SELECT addName,addAddress,addAddress2,addCity,addState,addZip,addCountry,addPhone,addExtra1,addExtra2 FROM address WHERE addCustID='" . mysql_escape_string($_SESSION['clientID']) . "' AND addID='" . mysql_escape_string($_POST['saddressid']) . "'";
			$result = mysql_query($sSQL) or print(mysql_error());
			if($rs = mysql_fetch_assoc($result)){
				$ordShipName = $rs['addName'];
				$ordShipAddress = $rs['addAddress'];
				$ordShipAddress2 = $rs['addAddress2'];
				$ordShipCity = $rs['addCity'];
				$ordShipState = $rs['addState'];
				$ordShipZip = $rs['addZip'];
				$ordShipCountry = $rs['addCountry'];
				$ordShipPhone = $rs['addPhone'];
				$ordShipExtra1 = $rs['addExtra1'];
				$ordShipExtra2 = $rs['addExtra2'];
			}
		}else{
			$ordShipName = trim(unstripslashes(@$_POST['sname']));
			$ordShipAddress = trim(unstripslashes(@$_POST['saddress']));
			$ordShipAddress2 = trim(unstripslashes(@$_POST['saddress2']));
			$ordShipCity = trim(unstripslashes(@$_POST['scity']));
			if(trim(@$_POST['sstate']) != '')
				$ordShipState = trim(unstripslashes(@$_POST['sstate']));
			else
				$ordShipState = trim(unstripslashes(@$_POST['sstate2']));
			$ordShipZip = trim(unstripslashes(@$_POST['szip']));
			$ordShipCountry = trim(unstripslashes(@$_POST['scountry']));
			$ordShipPhone = trim(unstripslashes(@$_POST['sphone']));
			$ordShipExtra1 = trim(unstripslashes(@$_POST['ordshipextra1']));
			$ordShipExtra2 = trim(unstripslashes(@$_POST['ordshipextra2']));
		}
		if(@$_SESSION['clientID'] != ''){
			if(@$_POST['addaddress']=='add'){
				$sSQL = "INSERT INTO address (addCustID,addIsDefault,addName,addAddress,addAddress2,addCity,addState,addZip,addCountry,addPhone,addExtra1,addExtra2) VALUES (" . $_SESSION['clientID'] . ",0,'".mysql_escape_string($ordName)."','".mysql_escape_string($ordAddress)."','".mysql_escape_string($ordAddress2)."','".mysql_escape_string($ordCity)."','".mysql_escape_string($ordState)."','".mysql_escape_string($ordZip)."','".mysql_escape_string($ordCountry)."','".mysql_escape_string($ordPhone)."','".mysql_escape_string($ordExtra1)."','".mysql_escape_string($ordExtra2)."')";
				mysql_query($sSQL) or print(mysql_error());
			}elseif(@$_POST['addaddress']=='edit'){
				$sSQL = "UPDATE address SET addName='".mysql_escape_string($ordName)."',addAddress='".mysql_escape_string($ordAddress)."',addAddress2='".mysql_escape_string($ordAddress2)."',addCity='".mysql_escape_string($ordCity)."',addState='".mysql_escape_string($ordState)."',addZip='".mysql_escape_string($ordZip)."',addCountry='".mysql_escape_string($ordCountry)."',addPhone='".mysql_escape_string($ordPhone)."',addExtra1='".mysql_escape_string($ordExtra1)."',addExtra2='".mysql_escape_string($ordExtra2)."' WHERE addCustID='" . @$_SESSION['clientID'] . "' AND addID='" . mysql_escape_string(@$_POST['addressid']) . "'";
				mysql_query($sSQL) or print(mysql_error());
			}
			if($ordShipName != '' && $ordShipAddress != '' && $ordShipCity != ''){
				if(@$_POST['saddaddress']=='add'){
					$sSQL = "INSERT INTO address (addCustID,addIsDefault,addName,addAddress,addAddress2,addCity,addState,addZip,addCountry,addPhone,addExtra1,addExtra2) VALUES (" . $_SESSION['clientID'] . ",0,'".mysql_escape_string($ordShipName)."','".mysql_escape_string($ordShipAddress)."','".mysql_escape_string($ordShipAddress2)."','".mysql_escape_string($ordShipCity)."','".mysql_escape_string($ordShipState)."','".mysql_escape_string($ordShipZip)."','".mysql_escape_string($ordShipCountry)."','".mysql_escape_string($ordShipPhone)."','".mysql_escape_string($ordShipExtra1)."','".mysql_escape_string($ordShipExtra2)."')";
					mysql_query($sSQL) or print(mysql_error());
				}elseif(@$_POST['saddaddress']=='edit'){
					$sSQL = "UPDATE address SET addName='".mysql_escape_string($ordShipName)."',addAddress='".mysql_escape_string($ordShipAddress)."',addAddress2='".mysql_escape_string($ordShipAddress2)."',addCity='".mysql_escape_string($ordShipCity)."',addState='".mysql_escape_string($ordShipState)."',addZip='".mysql_escape_string($ordShipZip)."',addCountry='".mysql_escape_string($ordShipCountry)."',addPhone='".mysql_escape_string($ordShipPhone)."',addExtra1='".mysql_escape_string($ordShipExtra1)."',addExtra2='".mysql_escape_string($ordShipExtra2)."' WHERE addCustID=" . @$_SESSION['clientID'] . " AND addID='" . mysql_escape_string(@$_POST['addressid']) . "'";
					mysql_query($sSQL) or print(mysql_error());
				}
			}
		}
		$ordAVS = trim(unstripslashes(@$_POST['ppexp1']));
		$ordCVV = trim(unstripslashes(@$_POST['ppexp2']));
		$ordAddInfo = trim(unstripslashes(@$_POST['ordAddInfo']));
		$commercialloc = trim($commerciallocpost);
		$wantinsurance = trim(@$_POST['wantinsurance']);
		$saturdaydelivery = trim(@$_POST['saturdaydelivery']);
		$signaturerelease = trim(@$_POST['signaturerelease']);
		$insidedelivery = trim(@$_POST['insidedelivery']);
		if($commercialloc=='Y') $ordComLoc = 1; else $ordComLoc = 0;
		if($wantinsurance=='Y' || abs(@$addshippinginsurance)==1) $ordComLoc += 2;
		if($saturdaydelivery=='Y') $ordComLoc += 4;
		if($signaturerelease=='Y') $ordComLoc += 8;
		if($insidedelivery=='Y') $ordComLoc += 16;
		$ordAffiliate = strip_tags(trim(unstripslashes(@$_POST['PARTNER'])));
		$ordCheckoutExtra1 = trim(unstripslashes(@$_POST['ordcheckoutextra1']));
		$ordCheckoutExtra2 = trim(unstripslashes(@$_POST['ordcheckoutextra2']));
	}
	if($ordShipAddress != ''){
		$shipcountry = $ordShipCountry;
		$shipstate = $ordShipState;
		$destZip = $ordShipZip;
	}else{
		$shipcountry = $ordCountry;
		$shipstate = $ordState;
		$destZip = $ordZip;
	}
	$sSQL = "SELECT countryID,countryCode,countryOrder FROM countries WHERE countryName='" . mysql_escape_string($ordCountry) . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	if($rs = mysql_fetch_array($result)){
		$countryID = $rs["countryID"];
		$countryCode = $rs["countryCode"];
		$homecountry = ($rs["countryOrder"]==2);
	}
	mysql_free_result($result);
	if(! $homecountry) $perproducttaxrate=FALSE;
	$sSQL = "SELECT countryID,countryTax,countryCode,countryFreeShip,countryOrder FROM countries WHERE countryName='" . mysql_escape_string($shipcountry) . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	if($rs = mysql_fetch_array($result)){
		$countryTaxRate = $rs["countryTax"];
		$shipCountryID = $rs["countryID"];
		$shipCountryCode = $rs["countryCode"];
		$freeshipapplies = ($rs["countryFreeShip"]==1);
		$shiphomecountry = ($rs["countryOrder"]==2);
	}
	mysql_free_result($result);
	if($homecountry){
		$sSQL = "SELECT stateTax,stateAbbrev FROM states WHERE ".(@$usestateabbrev==TRUE?'stateAbbrev':'stateName')."='" . mysql_escape_string($ordState) . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_array($result))
			$stateAbbrev=$rs["stateAbbrev"];
		mysql_free_result($result);
	}
	if($shiphomecountry){
		$sSQL = "SELECT stateTax,stateAbbrev,stateFreeShip FROM states WHERE ".(@$usestateabbrev==TRUE?'stateAbbrev':'stateName')."='" . mysql_escape_string($shipstate) . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_array($result)){
			$stateTaxRate=$rs["stateTax"];
			$shipStateAbbrev=$rs["stateAbbrev"];
			$freeshipapplies = ($freeshipapplies && ($rs["stateFreeShip"]==1));
		}
		mysql_free_result($result);
	}
	if(trim(@$_SESSION["clientUser"]) != ''){
		if(((int)$_SESSION["clientActions"] & 1)==1) $stateTaxRate=0;
		if(((int)$_SESSION["clientActions"] & 2)==2) $countryTaxRate=0;
	}
	initshippingmethods();
	$sSQL = "SELECT cartID,cartProdID,cartProdPrice,cartQuantity,pWeight,pShipping,pShipping2,pExemptions,pSection,topSection,pDims,pTax FROM cart LEFT JOIN products ON cart.cartProdID=products.pId LEFT OUTER JOIN sections ON products.pSection=sections.sectionID WHERE cartCompleted=0 AND " . getsessionsql();
	$allcart = mysql_query($sSQL) or print(mysql_error());
	if(($itemsincart=mysql_num_rows($allcart))==0) $allcart = '';
	if($success && $allcart != ''){
		$rowcounter = 0;
		$index=0;
		while($rsCart=mysql_fetch_array($allcart)){
			$index++;
			$sSQL = "SELECT SUM(coPriceDiff) AS coPrDff FROM cartoptions WHERE coCartID=". $rsCart["cartID"];
			$result = mysql_query($sSQL) or print(mysql_error());
			if($rs = mysql_fetch_array($result)){
				$rsCart["cartProdPrice"] += (double)$rs["coPrDff"];
			}
			mysql_free_result($result);
			$sSQL = "SELECT SUM(coWeightDiff) AS coWghtDff FROM cartoptions WHERE coCartID=". $rsCart["cartID"];
			$result = mysql_query($sSQL) or print(mysql_error());
			if($rs = mysql_fetch_array($result)){
				$rsCart["pWeight"] += (double)$rs["coWghtDff"];
			}
			mysql_free_result($result);
			$runTot=$rsCart["cartProdPrice"] * (int)($rsCart["cartQuantity"]);
			$totalquantity += (int)($rsCart["cartQuantity"]);
			$totalgoods += $runTot;
			$thistopcat=0;
			if(trim(@$_SESSION['clientUser']) != '') $rsCart['pExemptions'] = ((int)$rsCart['pExemptions'] | (int)$_SESSION['clientActions']);
			if(($shipType==2 || $shipType==3 || $shipType==4 || $shipType==6 || $shipType==7) && (double)$rsCart['pWeight']<=0.0)
				$rsCart['pExemptions'] = ($rsCart['pExemptions'] | 4);
			if(($rsCart['pExemptions'] & 1)==1) $statetaxfree += $runTot;
			if(@$perproducttaxrate==TRUE){
				if(is_null($rsCart['pTax'])) $rsCart['pTax'] = $countryTaxRate;
				if(($rsCart['pExemptions'] & 2) != 2) $countryTax += (($rsCart['pTax'] * $runTot) / 100.0);
			}else{
				if(($rsCart['pExemptions'] & 2)==2) $countrytaxfree += $runTot;
			}
			if(($rsCart['pExemptions'] & 4)==4) $shipfreegoods += $runTot;
			addproducttoshipping($rsCart, $index);
		}
		calculatediscounts(round($totalgoods,2), true, $cpncode);
		if($shippingpost != ''){
			$shipArr = split('\|',$shippingpost,3);
			$shipping = (double)$shipArr[0];
			$isstandardship = ((int)$shipArr[1]==1);
			$shipMethod = $shipArr[2];
		}else
			calculateshipping();
		if($shippingpost=='' && $alternaterates && $somethingToShip) $checkIntOptions = TRUE;
		insuranceandtaxaddedtoshipping();
		if(! $checkIntOptions){
			calculateshippingdiscounts(true);
			if(@$_SESSION['clientUser'] != '' && @$_SESSION['clientActions'] != 0) $cpnmessage .= $xxLIDis . $_SESSION['clientUser'] . '<br />';
			$cpnmessage = substr($cpnmessage,6);
			if($totaldiscounts > $totalgoods) $totaldiscounts = $totalgoods;
			calculatetaxandhandling();
			$totalgoods = round($totalgoods,(@$overridecurrency==TRUE && is_numeric(@$orcdecplaces) ? $orcdecplaces : 2));
			$shipping = round($shipping,(@$overridecurrency==TRUE && is_numeric(@$orcdecplaces) ? $orcdecplaces : 2));
			$stateTax = round($stateTax,(@$overridecurrency==TRUE && is_numeric(@$orcdecplaces) ? $orcdecplaces : 2));
			$countryTax = round($countryTax,(@$overridecurrency==TRUE && is_numeric(@$orcdecplaces) ? $orcdecplaces : 2));
			$handling = round($handling,(@$overridecurrency==TRUE && is_numeric(@$orcdecplaces) ? $orcdecplaces : 2));
			if(@$addshippingtodiscounts){
				$totaldiscounts += $freeshipamnt;
				$freeshipamnt = 0;
			}
			$freeshipamnt = round($freeshipamnt,(@$overridecurrency==TRUE && is_numeric(@$orcdecplaces) ? $orcdecplaces : 2));
			$totaldiscounts = round($totaldiscounts,(@$overridecurrency==TRUE && is_numeric(@$orcdecplaces) ? $orcdecplaces : 2));
			$grandtotal = round(($totalgoods + $shipping + $stateTax + $countryTax + $handling) - ($totaldiscounts + $freeshipamnt),(@$overridecurrency==TRUE && is_numeric(@$orcdecplaces) ? $orcdecplaces : 2));
			if($grandtotal < 0) $grandtotal = 0;
			$sSQL = "SELECT ordID FROM orders WHERE ordAuthNumber='' AND " . getordersessionsql();
			$result = mysql_query($sSQL) or print(mysql_error());
			if($rs = mysql_fetch_array($result))
				$orderid=$rs["ordID"];
			else
				$orderid='';
			mysql_free_result($result);
			if($ordShipName=='' && $ordShipAddress=='' && $ordShipAddress2=='' && $ordShipCity=='') $ordShipCountry='';
			if($orderid==''){
				$sSQL = "INSERT INTO orders (ordSessionID,ordClientID,ordName,ordAddress,ordAddress2,ordCity,ordState,ordZip,ordCountry,ordEmail,ordPhone,ordShipName,ordShipAddress,ordShipAddress2,ordShipCity,ordShipState,ordShipZip,ordShipCountry,ordShipPhone,ordPayProvider,ordAuthNumber,ordShipping,ordStateTax,ordCountryTax,ordHSTTax,ordHandling,ordShipType,ordShipCarrier,ordTotal,ordDate,ordStatus,ordStatusDate,ordComLoc,ordIP,ordAffiliate,ordExtra1,ordExtra2,ordShipExtra1,ordShipExtra2,ordCheckoutExtra1,ordCheckoutExtra2,ordAVS,ordCVV,ordDiscount,ordDiscountText,ordAddInfo) VALUES (";
				$sSQL .= "'" . mysql_escape_string($thesessionid) . "',";
				$sSQL .= "'" . (@$_SESSION['clientID'] != '' ? $_SESSION['clientID'] : 0) . "',";
				$sSQL .= "'" . mysql_escape_string($ordName) . "',";
				$sSQL .= "'" . mysql_escape_string($ordAddress) . "',";
				$sSQL .= "'" . mysql_escape_string($ordAddress2) . "',";
				$sSQL .= "'" . mysql_escape_string($ordCity) . "',";
				$sSQL .= "'" . mysql_escape_string($ordState) . "',";
				$sSQL .= "'" . mysql_escape_string($ordZip) . "',";
				$sSQL .= "'" . mysql_escape_string($ordCountry) . "',";
				$sSQL .= "'" . mysql_escape_string($ordEmail) . "',";
				$sSQL .= "'" . mysql_escape_string($ordPhone) . "',";
				$sSQL .= "'" . mysql_escape_string($ordShipName) . "',";
				$sSQL .= "'" . mysql_escape_string($ordShipAddress) . "',";
				$sSQL .= "'" . mysql_escape_string($ordShipAddress2) . "',";
				$sSQL .= "'" . mysql_escape_string($ordShipCity) . "',";
				$sSQL .= "'" . mysql_escape_string($ordShipState) . "',";
				$sSQL .= "'" . mysql_escape_string($ordShipZip) . "',";
				$sSQL .= "'" . mysql_escape_string($ordShipCountry) . "',";
				$sSQL .= "'" . mysql_escape_string($ordShipPhone) . "',";
				$sSQL .= "'" . mysql_escape_string($ordPayProvider) . "',";
				$sSQL .= "'',";
				$sSQL .= "'" . mysql_escape_string($shipping-$freeshipamnt) . "',";
				if($usehst){
					$sSQL .= "0,";
					$sSQL .= "0,";
					$sSQL .= ($stateTax + $countryTax) . ",";
				}else{
					$sSQL .= "'" . mysql_escape_string($stateTax) . "',";
					$sSQL .= "'" . mysql_escape_string($countryTax) . "',";
					$sSQL .= "0,";
				}
				$sSQL .= "'" . mysql_escape_string($handling) . "',";
				$sSQL .= "'" . mysql_escape_string($shipMethod) . "',";
				$sSQL .= "'" . mysql_escape_string($shipType) . "',";
				$sSQL .= "'" . mysql_escape_string($totalgoods) . "',";
				$sSQL .= "'" . date("Y-m-d H:i:s", time() + ($dateadjust*60*60)) . "',";
				$sSQL .= "2,"; // Status
				$sSQL .= "'" . date("Y-m-d H:i:s", time() + ($dateadjust*60*60)) . "',";
				$sSQL .= "'" . $ordComLoc . "',";
				$sSQL .= "'" . @$_SERVER["REMOTE_ADDR"] . "',";
				$sSQL .= "'" . mysql_escape_string($ordAffiliate) . "',";
				$sSQL .= "'" . mysql_escape_string($ordExtra1) . "',";
				$sSQL .= "'" . mysql_escape_string($ordExtra2) . "',";
				$sSQL .= "'" . mysql_escape_string($ordShipExtra1) . "',";
				$sSQL .= "'" . mysql_escape_string($ordShipExtra2) . "',";
				$sSQL .= "'" . mysql_escape_string($ordCheckoutExtra1) . "',";
				$sSQL .= "'" . mysql_escape_string($ordCheckoutExtra2) . "',";
				$sSQL .= "'" . mysql_escape_string($ordAVS) . "',";
				$sSQL .= "'" . mysql_escape_string($ordCVV) . "',";
				$sSQL .= "'" . mysql_escape_string($totaldiscounts) . "',";
				$sSQL .= "'" . mysql_escape_string(trim(substr($cpnmessage,0,255))) . "',";
				$sSQL .= "'" . mysql_escape_string($ordAddInfo) . "')";
				mysql_query($sSQL) or print(mysql_error());
				$orderid = mysql_insert_id();
			}else{
				$sSQL = "UPDATE orders SET ";
				$sSQL .= "ordSessionID='" . mysql_escape_string($thesessionid) . "',";
				$sSQL .= "ordClientID='" . (@$_SESSION['clientID'] != '' ? $_SESSION['clientID'] : 0) . "',";
				$sSQL .= "ordName='" . mysql_escape_string($ordName) . "',";
				$sSQL .= "ordAddress='" . mysql_escape_string($ordAddress) . "',";
				$sSQL .= "ordAddress2='" . mysql_escape_string($ordAddress2) . "',";
				$sSQL .= "ordCity='" . mysql_escape_string($ordCity) . "',";
				$sSQL .= "ordState='" . mysql_escape_string($ordState) . "',";
				$sSQL .= "ordZip='" . mysql_escape_string($ordZip) . "',";
				$sSQL .= "ordCountry='" . mysql_escape_string($ordCountry) . "',";
				$sSQL .= "ordEmail='" . mysql_escape_string($ordEmail) . "',";
				$sSQL .= "ordPhone='" . mysql_escape_string($ordPhone) . "',";
				$sSQL .= "ordShipName='" . mysql_escape_string($ordShipName) . "',";
				$sSQL .= "ordShipAddress='" . mysql_escape_string($ordShipAddress) . "',";
				$sSQL .= "ordShipAddress2='" . mysql_escape_string($ordShipAddress2) . "',";
				$sSQL .= "ordShipCity='" . mysql_escape_string($ordShipCity) . "',";
				$sSQL .= "ordShipState='" . mysql_escape_string($ordShipState) . "',";
				$sSQL .= "ordShipZip='" . mysql_escape_string($ordShipZip) . "',";
				$sSQL .= "ordShipCountry='" . mysql_escape_string($ordShipCountry) . "',";
				$sSQL .= "ordShipPhone='" . mysql_escape_string($ordShipPhone) . "',";
				$sSQL .= "ordPayProvider='" . mysql_escape_string($ordPayProvider) . "',";
				$sSQL .= "ordAuthNumber='',"; // Not yet authorized
				$sSQL .= "ordShipping='" . ($shipping - $freeshipamnt) . "',";
				if($usehst){
					$sSQL .= "ordStateTax=0,";
					$sSQL .= "ordCountryTax=0,";
					$sSQL .= "ordHSTTax=" . ($stateTax + $countryTax) . ",";
				}else{
					$sSQL .= "ordStateTax='" . $stateTax . "',";
					$sSQL .= "ordCountryTax='" . $countryTax . "',";
					$sSQL .= "ordHSTTax=0,";
				}
				$sSQL .= "ordHandling='" . $handling . "',";
				$sSQL .= "ordShipType='" . $shipMethod . "',";
				$sSQL .= "ordShipCarrier='" . $shipType . "',";
				$sSQL .= "ordTotal='" . $totalgoods . "',";
				$sSQL .= "ordDate='" . date("Y-m-d H:i:s", time() + ($dateadjust*60*60)) . "',";
				$sSQL .= "ordComLoc=" . $ordComLoc . ",";
				$sSQL .= "ordIP='" . getipaddress() . "',";
				$sSQL .= "ordAffiliate='" . mysql_escape_string($ordAffiliate) . "',";
				$sSQL .= "ordExtra1='" . mysql_escape_string($ordExtra1) . "',";
				$sSQL .= "ordExtra2='" . mysql_escape_string($ordExtra2) . "',";
				$sSQL .= "ordShipExtra1='" . mysql_escape_string($ordShipExtra1) . "',";
				$sSQL .= "ordShipExtra2='" . mysql_escape_string($ordShipExtra2) . "',";
				$sSQL .= "ordCheckoutExtra1='" . mysql_escape_string($ordCheckoutExtra1) . "',";
				$sSQL .= "ordCheckoutExtra2='" . mysql_escape_string($ordCheckoutExtra2) . "',";
				$sSQL .= "ordAVS='" . mysql_escape_string($ordAVS) . "',";
				$sSQL .= "ordCVV='" . mysql_escape_string($ordCVV) . "',";
				$sSQL .= "ordDiscount='" . $totaldiscounts . "',";
				$sSQL .= "ordDiscountText='" . mysql_escape_string(trim(substr($cpnmessage,0,255))) . "',";
				$sSQL .= "ordAddInfo='" . mysql_escape_string($ordAddInfo) . "'";
				$sSQL .= " WHERE ordID='" . $orderid . "'";
				mysql_query($sSQL) or print(mysql_error());
			}
			$sSQL="UPDATE cart SET cartOrderID=". $orderid . " WHERE cartCompleted=0 AND " . getsessionsql();
			mysql_query($sSQL) or print(mysql_error());
			$descstr='';
			$addcomma = '';
			$sSQL="SELECT cartQuantity,cartProdName FROM cart WHERE cartOrderID=" . $orderid . " AND cartCompleted=0";
			$result = mysql_query($sSQL) or print(mysql_error());
			while($rs=mysql_fetch_assoc($result)){
				$descstr .= $addcomma . $rs["cartQuantity"] . " " . $rs["cartProdName"];
				$addcomma = ", ";
			}
			mysql_free_result($result);
			$descstr = str_replace('"','',$descstr);
			if(@$_POST["remember"]=="1")
				print "<script src='vsadmin/savecookie.php?id1=" . $orderid . "&id2=" . trim($thesessionid) . "'></script>";
		}
	}else{
		$success=FALSE;
	}
	if($checkIntOptions && $success || ($alternaterates && ! $success)){
		$hassuccess = $success;
		$success = FALSE; // So not to print the order totals.
?>
	<br />
	<form method="post" name="shipform" action="cart.php">
<?php
writehiddenvar('mode', 'go');
writehiddenvar('vrshippingoptions', '1');
writehiddenvar('sessionid', $thesessionid);
writehiddenvar('PARTNER', $ordAffiliate);
writehiddenvar('name', $ordName);
writehiddenvar('email', $ordEmail);
writehiddenvar('address', $ordAddress);
writehiddenvar('address2', $ordAddress2);
writehiddenvar('city', $ordCity);
writehiddenvar('state', $ordState);
writehiddenvar('country', $ordCountry);
writehiddenvar('zip', $ordZip);
writehiddenvar('phone', $ordPhone);
writehiddenvar('sname', $ordShipName);
writehiddenvar('saddress', $ordShipAddress);
writehiddenvar('saddress2', $ordShipAddress2);
writehiddenvar('scity', $ordShipCity);
writehiddenvar('sstate', $ordShipState);
writehiddenvar('scountry', $ordShipCountry);
writehiddenvar('szip', $ordShipZip);
writehiddenvar('sphone', $ordShipPhone);
writehiddenvar('ordAddInfo', $ordAddInfo);
writehiddenvar('ordextra1', $ordExtra1);
writehiddenvar('ordextra2', $ordExtra2);
writehiddenvar('ordshipextra1', $ordShipExtra1);
writehiddenvar('ordshipextra2', $ordShipExtra2);
writehiddenvar('ordcheckoutextra1', $ordCheckoutExtra1);
writehiddenvar('ordcheckoutextra2', $ordCheckoutExtra2);
writehiddenvar('ppexp1', $ordAVS);
writehiddenvar('ppexp2', $ordCVV);
writehiddenvar('cpncode', $cpncode);
writehiddenvar('payprovider', $ordPayProvider);
writehiddenvar('token', $token);
writehiddenvar('payerid', $payerid);
writehiddenvar('wantinsurance', $wantinsurance);
writehiddenvar('commercialloc', $commercialloc);
writehiddenvar('saturdaydelivery', $saturdaydelivery);
writehiddenvar('signaturerelease', $signaturerelease);
writehiddenvar('insidedelivery', $insidedelivery);
writehiddenvar('remember', @$_POST["remember"]);
?>			<table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
			  <tr>
			    <td height="34" align="center" class="cobhl" bgcolor="#EBEBEB" colspan="2"><strong><?php print $xxShpOpt?></strong></td>
			  </tr>
			  <tr>
				<td height="34" align="center" class="cobll" bgcolor="#FFFFFF" colspan="2">
<?php				if($hassuccess){ ?>
				  <table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF">
					<tr>
					  <td height="34" align="right" width="50%" class="cobll" bgcolor="#FFFFFF"><?php if($shipType==4) print '<img src="images/LOGO_S.gif" alt="UPS" />&nbsp;&nbsp;'; elseif($shipType==7) print '<img src="images/fedexsmall.gif" alt="FedEx" />&nbsp;&nbsp;'; else print "&nbsp;"; ?></td>
					  <td height="34" align="center" class="cobll" bgcolor="#FFFFFF"><?php
						calculateshippingdiscounts(false);
						print "<select name='shipping' size='1'>";
						if($shipType==2 || $shipType==5){
							if(is_array($allzones)){
								for($index3=0; $index3 < $numshipoptions; $index3++){
									print "<option value='" . $intShipping[$index3][2] . "|" . (($pzFSA & pow(2, $index3))!=0?"1":"0") . "|" . $intShipping[$index3][0] . "'>";
									print ($freeshippingapplied && ($pzFSA & pow(2, $index3))!=0 ? $xxFree . " " . $intShipping[$index3][0] : $intShipping[$index3][0] . " " . FormatEuroCurrency($intShipping[$index3][2])) . '</option>';
								}
							}
						}else{
							for($indexmso=0; $indexmso<$maxshipoptions; $indexmso++){
								$shipRow = $intShipping[$indexmso];
								if($shipType==3){
									if($iTotItems==$shipRow[3]){
										for($index2=0;$index2<$numuspsmeths;$index2++){
											if(str_replace('-',' ',strtolower($shipRow[0])) == str_replace('-',' ',strtolower($uspsmethods[$index2][0]))){
												print "<option value='" . $shipRow[2] . "|". trim($uspsmethods[$index2][1]) ."|" . trim($uspsmethods[$index2][2]) . "'" . (freeshippingapplied && $uspsmethods[$index2][1]==1 ? " selected>" : ">");
												print trim($uspsmethods[$index2][2]) . " (" . $shipRow[1] . ") " . ($freeshippingapplied && $uspsmethods[$index2][1]==1 ? $xxFree : FormatEuroCurrency($shipRow[2]));
												print "</option>";
											}
										}
									}
								}elseif($shipType==4 || $shipType==6 || $shipType==7){
									if($shipRow[3]){
										print "<option value='" . $shipRow[2] . "|". $shipRow[4] ."|" . $shipRow[0] . "'" . ($freeshippingapplied && $shipRow[4]==1 ? " selected>" : ">") . $shipRow[0] . " ";
										if(trim($shipRow[1]) != '') print "(" . $xxGuar . " " . $shipRow[1] . ") ";
										print ($freeshippingapplied && $shipRow[4]==1 ? $xxFree : FormatEuroCurrency($shipRow[2]));
										print "</option>";
									}
								}
							}
						}
						if(@$willpickuptext != ''){
							if(@$willpickupcost=='') $willpickupcost=0;
							print '<option value="' . $willpickupcost . "|1|" . str_replace('"','&quot;',$willpickuptext) . '">';
							print $willpickuptext . " " . FormatEuroCurrency($willpickupcost) . "</option>";
						}
						print "</select>";
					?></td>
					  <td height="34" align="left" width="50%" class="cobll" bgcolor="#FFFFFF">&nbsp;</td>
					</tr>
				  </table>
<?php				}else{
						print '<input type="hidden" name="shipping" value="">' . $errormsg;
					} ?>
				</td>
			  </tr>
<?php			if($alternaterates){ ?>
			  <tr>
			    <td height="34" align="center" class="cobhl" bgcolor="#EBEBEB" colspan="2"><strong><?php print $xxAltCar?></strong></td>
			  </tr>
			  <tr>
				<td height="34" align="center" class="cobll" bgcolor="#FFFFFF" colspan="2">
					<select name="altrates" size="1" onchange="document.forms.shipform.shipping.value='';document.forms.shipform.shipping.disabled=true;document.forms.shipform.submit();"><?php
				if(@$alternateratesups != '' || $origShipType==4) print '<option value="4"' . ($shipType==4 ? " selected" : '') . ">" . @$alternateratesups . '</option>';
				if(@$alternateratesusps != '' || $origShipType==3) print '<option value="3"' . ($shipType==3 ? " selected" : '') . ">" . @$alternateratesusps . '</option>';
				if(@$alternateratesweightbased != '' || $origShipType==2) print '<option value="2"' . ($shipType==2 ? " selected" : '') . ">" . @$alternateratesweightbased . '</option>';
				if(@$alternateratescanadapost != '' || $origShipType==6) print '<option value="6"' . ($shipType==6 ? " selected" : '') . ">" . @$alternateratescanadapost . '</option>';
				if(@$alternateratesfedex != '' || $origShipType==7) print '<option value="7"' . ($shipType==7 ? " selected" : '') . ">" . @$alternateratesfedex . '</option>';
						?></select>
				</td>
			  </tr>
<?php			}
				if($ordPayProvider=='19' && @$_GET['token']!=''){ ?>
			  <tr>
				<td class="cobhl" bgcolor="#EBEBEB" height="30" align="right" width="50%"><strong><?php if($cpncode!='' && $ordPayProvider=='19' && ! $gotcpncode) print '<font color="#FF0000">' . $xxCpnNoF . '</font>'; else print $xxGifCer.':'?></strong></td>
				<td class="cobll" bgcolor="#FFFFFF" height="30" align="left" width="50%"><font size="1"><?php
					if(! $gotcpncode) print '<input type="text" name="cpncode2" id="cpncode2" size="20" value="' . htmlspecialchars($cpncode) . '" /> <input type="button" value="' . $xxAppCpn . '" onclick="document.location=\'cart.php?token='.$_GET['token'].'&cpncode=\'+document.getElementById(\'cpncode2\').value" />'; else print htmlspecialchars($cpncode);
				}
?>				</td>
			  </tr>
			  <tr>
			    <td height="34" align="center" class="cobll" bgcolor="#FFFFFF" colspan="2"><table width="100%" cellspacing="0" cellpadding="0" border="0">
				    <tr>
					  <td class="cobll" bgcolor="#FFFFFF" width="16" height="26" align="right" valign="bottom">&nbsp;</td>
					  <td class="cobll" bgcolor="#FFFFFF" width="100%" align="center"><input type="image" value="Checkout" border="0" src="images/checkout.gif" alt="<?php print $xxCOTxt?>" /></td>
					  <td class="cobll" bgcolor="#FFFFFF" width="16" height="26" align="right" valign="bottom"><img src="images/tablebr.gif" alt="" /></td>
					</tr>
				  </table>
				</td>
			  </tr>
			</table>
		<?php if($shipType==4){ ?>
			<p align="center">&nbsp;<br /><font size="1">UPS&reg;, UPS & Shield Design&reg; and UNITED PARCEL SERVICE&reg; 
			  are<br />registered trademarks of United Parcel Service of America, Inc.</font></p>
		<?php }elseif($shipType==7){ ?>
			<p align="center">&nbsp;<br /><font size="1">FedEx&reg; is a registered service mark of Federal Express Corporation. FedEx logos used by permission. All rights reserved.
		<?php } ?>
	</form>
<?php
	}elseif(! $success){ ?>
      <table border="0" cellspacing="<?php print $maintablespacing?>" cellpadding="<?php print $maintablepadding?>" width="<?php print $maintablewidth?>" bgcolor="<?php print $maintablebg?>" align="center">
        <tr> 
          <td width="100%">
            <table width="<?php print $innertablewidth?>" border="0" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
			  <tr>
			    <td align="center"><p>&nbsp;</p><p><strong><?php print $xxSryErr?></strong></p><p><strong><?php print "<br />" . $errormsg ?></strong></p><p>&nbsp;</p></td>
			  </tr>
			</table>
		  </td>
        </tr>
      </table>
<?php
	}elseif($ordPayProvider != ''){
		$blockuser=checkuserblock($ordPayProvider);
		if($blockuser){
			$orderid = 0;
			$thesessionid = '';
			$xxMstClk = $multipurchaseblockmessage;
		}else
			getpayprovdetails($ordPayProvider,$data1,$data2,$data3,$demomode,$ppmethod);
		$origstoreurl = $storeurl;
		if(@$pathtossl != ''){
			if(substr($pathtossl,-1) != "/") $pathtossl .= "/";
			$storeurl = $pathtossl;
		}
		if($grandtotal > 0 && $ordPayProvider=='1'){ // PayPal
?>
	<form method="post" action="https://www.<?php if($demomode) print 'sandbox.'?>paypal.com/cgi-bin/webscr">
	<input type="hidden" name="cmd" value="_ext-enter" />
	<input type="hidden" name="redirect_cmd" value="_xclick" />
	<input type="hidden" name="rm" value="2" />
	<input type="hidden" name="business" value="<?php print $data1?>" />
	<input type="hidden" name="return" value="<?php print $storeurl?>thanks.php" />
	<input type="hidden" name="notify_url" value="<?php print $storeurl?>vsadmin/ppconfirm.php" />
	<input type="hidden" name="item_name" value="<?php print substr($descstr,0,127)?>" />
	<input type="hidden" name="custom" value="<?php print $orderid?>" />
<?php		if(@$paypallc != '') writehiddenvar('lc', $paypallc);
			if(@$splitpaypalshipping){
				writehiddenvar('shipping', number_format(round(($shipping + $handling) - $freeshipamnt, 2), (@$overridecurrency==TRUE && is_numeric(@$orcdecplaces) ? $orcdecplaces : 2),'.',''));
				writehiddenvar('amount', number_format(round(($totalgoods + $stateTax + $countryTax) - $totaldiscounts, 2), (@$overridecurrency==TRUE && is_numeric(@$orcdecplaces) ? $orcdecplaces : 2),'.',''));
			}else{
				writehiddenvar('amount', number_format($grandtotal, (@$overridecurrency==TRUE && is_numeric(@$orcdecplaces) ? $orcdecplaces : 2),'.',''));
			} ?>
	<input type="hidden" name="currency_code" value="<?php print $countryCurrency?>" />
	<input type="hidden" name="bn" value="ecommercetemplates.php.ecommplus" />
<?php		$thename = trim($ordName);
			if($thename != ''){
				if(strstr($thename," ")){
					$namearr = split(" ",$thename,2);
					print '<input type="hidden" name="first_name" value="' . $namearr[0] . "\" />\n";
					print '<input type="hidden" name="last_name" value="' . $namearr[1] . "\" />\n";
				}else
					print '<input type="hidden" name="last_name" value="' . $thename . "\" />\n";
			}
?>
	<input type="hidden" name="address1" value="<?php print $ordAddress?>" />
	<input type="hidden" name="address2" value="<?php print $ordAddress2?>" />
	<input type="hidden" name="city" value="<?php print $ordCity?>" />
<?php		writehiddenvar('state', $countryID==1 && $stateAbbrev != '' ? $stateAbbrev : $ordState); ?>
	<input type="hidden" name="country" value="<?php print $countryCode?>" />
	<input type="hidden" name="email" value="<?php print $ordEmail?>" />
	<input type="hidden" name="zip" value="<?php print $ordZip?>" />
	<input type="hidden" name="cancel_return" value="<?php print $origstoreurl?>cart.php" />
<?php		if($ppmethod==1) writehiddenvar('paymentaction', 'authorization');
		}elseif($grandtotal > 0 && $ordPayProvider=="2"){ // 2Checkout
			$courl='https://www.2checkout.com/cgi-bin/sbuyers/cartpurchase.2c';
			if(is_numeric($data1))
				if($data1>200000 || @$use2checkoutv2==TRUE) $courl='https://www2.2checkout.com/2co/buyer/purchase';
?>
	<form method="post" action="<?php print $courl?>">
	<input type="hidden" name="cart_order_id" value="<?php print $orderid?>" />
	<input type="hidden" name="merchant_order_id" value="<?php print $orderid?>" />
	<input type="hidden" name="sid" value="<?php print $data1?>" />
	<input type="hidden" name="total" value="<?php print $grandtotal?>" />
	<input type="hidden" name="card_holder_name" value="<?php print $ordName?>" />
	<input type="hidden" name="street_address" value="<?php print $ordAddress . (trim($ordAddress2)!='' ? ', ' . unstripslashes($ordAddress2) : '')?>" />
<?php		if($countryID==1 || $countryID==2){ ?>
	<input type="hidden" name="city" value="<?php print $ordCity?>" />
	<input type="hidden" name="state" value="<?php print $ordState?>" />
<?php		}else{ ?>
	<input type="hidden" name="city" value="<?php print $ordCity . ($ordState!='' ? ', ' . $ordState : '') ?>" />
	<input type="hidden" name="state" value="Outside US and Canada" />
<?php		} ?>
	<input type="hidden" name="zip" value="<?php print $ordZip?>" />
	<input type="hidden" name="country" value="<?php print $countryCode?>" />
	<input type="hidden" name="email" value="<?php print $ordEmail?>" />
	<input type="hidden" name="phone" value="<?php print $ordPhone?>" />
	<input type="hidden" name="id_type" value="1" />
<?php		$sSQL = "SELECT cartID,cartProdID,pName,pPrice,cartQuantity," . (@$digidownloads==TRUE ? "pDownload," : '') . "pDescription FROM cart INNER JOIN products on cart.cartProdID=products.pID WHERE cartCompleted=0 AND " . getsessionsql();
			$result = mysql_query($sSQL) or print(mysql_error());
			$index=1;
			while($rs=mysql_fetch_assoc($result)){
				$thedesc = substr(trim(preg_replace("(\r\n|\n|\r)",'\\n',strip_tags($rs["pDescription"]))),0,254);
				if($thedesc=='') $thedesc = substr(trim(preg_replace("(\r\n|\n|\r)",'\\n',strip_tags($rs["pName"]))),0,254);
				print '<input type="hidden" name="c_prod_' . $index . '" value="' . str_replace(',','&#44;',str_replace('"','&quot;',$rs["cartProdID"])) . "," . $rs["cartQuantity"] . "\" />\r\n";
				print '<input type="hidden" name="c_name_' . $index . '" value="' . str_replace('"','&quot;',strip_tags($rs["pName"])) . "\" />\r\n";
				print '<input type="hidden" name="c_description_' . $index . '" value="' . str_replace('"','&quot;',$thedesc) . "\" />\r\n";
				print '<input type="hidden" name="c_price_' . $index . '" value="' . number_format($rs["pPrice"],2,'.','') . "\" />\r\n";
				if(@$digidownloads==TRUE)
					if(trim($rs["pDownload"]) != '') print '<input type="hidden" name="c_tangible_' . $index . '" value="N" />' . "\r\n";
				$index++;
			}
			if(trim($ordShipName) != '' || trim($ordShipAddress) != ''){ ?>
	  <input type="hidden" name="ship_name" value="<?php print $ordShipName?>" />
	  <input type="hidden" name="ship_street_address" value="<?php print $ordShipAddress . (trim($ordShipAddress2)!='' ? ', ' . unstripslashes($ordShipAddress2) : '')?>" />
	  <input type="hidden" name="ship_city" value="<?php print $ordShipCity?>" />
	  <input type="hidden" name="ship_state" value="<?php print $ordShipState?>" />
	  <input type="hidden" name="ship_zip" value="<?php print $ordShipZip?>" />
	  <input type="hidden" name="ship_country" value="<?php print $ordShipCountry?>" />
<?php		}
			if($demomode) writehiddenvar('demo', 'Y');
			writehiddenvar('pay_method', 'CC');
			writehiddenvar('fixed', 'Y');
		}elseif($grandtotal > 0 && $ordPayProvider=="3"){ // Authorize.net SIM
			if(@$secretword != ''){
				$data1 = upsdecode($data1, $secretword);
				$data2 = upsdecode($data2, $secretword);
			} ?>
	<FORM METHOD=POST ACTION="https://secure.authorize.net/gateway/transact.dll">
	<input type="hidden" name="x_Version" value="3.0" />
	<input type="hidden" name="x_Login" value="<?php print $data1?>" />
	<input type="hidden" name="x_Show_Form" value="PAYMENT_FORM" />
<?php
	  if($ppmethod==1) print '<input type="hidden" name="x_type" value="AUTH_ONLY" />';
		function vrhmac($key, $text){
			$idatastr = "                                                                ";
			$odatastr = "                                                                ";
			$hkey = (string)$key;
			$idatastr .= $text;
			for($i=0; $i<64; $i++){
				$idata[$i] = $ipad[$i] = 0x36;
				$odata[$i] = $opad[$i] = 0x5C;
			}
			for($i=0; $i< strlen($hkey); $i++){
				$ipad[$i] ^= ord($hkey{$i});
				$opad[$i] ^= ord($hkey{$i});
				$idata[$i] = ($ipad[$i] & 0xFF);
				$odata[$i] = ($opad[$i] & 0xFF);
			}
			for($i=0; $i< strlen($text); $i++){
				$idata[64+$i] = ord($text{$i}) & 0xFF;
			}
			for($i=0; $i< strlen($idatastr); $i++){
				$idatastr{$i} = chr($idata[$i] & 0xFF);
			}
			for($i=0; $i< strlen($odatastr); $i++){
				$odatastr{$i} = chr($odata[$i] & 0xFF);
			}
			$innerhashout = md5($idatastr);
			for($i=0; $i<16; $i++)
				$odatastr .= chr(hexdec(substr($innerhashout,$i*2,2)));
			return md5($odatastr);
		}
		$thename = unstripslashes(trim($ordName));
		if($thename != ''){
			if(strstr($thename," ")){
				$namearr = split(" ",$thename,2);
				print '<input type="hidden" name="x_First_Name" value="' . str_replace('"','&quot;',$namearr[0]) . "\" />\n";
				print '<input type="hidden" name="x_Last_Name" value="' . str_replace('"','&quot;',$namearr[1]) . "\" />\n";
			}else
				print '<input type="hidden" name="x_Last_Name" value="' . str_replace('"','&quot;',$thename) . "\" />\n";
		}
		$sequence = $orderid;
		if(@$authnetadjust != '')
			$tstamp = time() + $authnetadjust;
		else
			$tstamp = time();
		$fingerprint = vrhmac($data2, $data1 . "^" . $sequence . "^" . $tstamp . "^" . number_format($grandtotal,2,'.','') . "^");
?>
	<input type="hidden" name="x_fp_sequence" value="<?php print $sequence?>" />
	<input type="hidden" name="x_fp_timestamp" value="<?php print $tstamp?>" />
	<input type="hidden" name="x_fp_hash" value="<?php print $fingerprint?>" />
	<input type="hidden" name="x_address" value="<?php print $ordAddress . (trim($ordAddress2)!='' ? ', ' . $ordAddress2 : '')?>" />
	<input type="hidden" name="x_city" value="<?php print $ordCity?>" />
	<input type="hidden" name="x_country" value="<?php print $ordCountry?>" />
	<input type="hidden" name="x_phone" value="<?php print $ordPhone?>" />
	<input type="hidden" name="x_state" value="<?php print $ordState?>" />
	<input type="hidden" name="x_zip" value="<?php print $ordZip?>" />
	<input type="hidden" name="x_cust_id" value="<?php print $orderid?>" />
	<input type="hidden" name="x_Invoice_Num" value="<?php print $orderid?>" />
	<input type="hidden" name="x_ect_ordid" value="<?php print $orderid?>" />
	<input type="hidden" name="x_Description" value="<?php print substr($descstr,0,255)?>" />
	<input type="hidden" name="x_email" value="<?php print $ordEmail?>" />
<?php		if(trim($ordShipName) != '' || trim($ordShipAddress) != ''){
				$thename = trim($ordShipName);
				if($thename != ''){
					if(strstr($thename," ")){
						$namearr = split(" ",$thename,2);
						print '<input type="hidden" name="x_Ship_To_First_Name" value="' . $namearr[0] . "\" />\n";
						print '<input type="hidden" name="x_Ship_To_Last_Name" value="' . $namearr[1] . "\" />\n";
					}else
						print '<input type="hidden" name="x_Ship_To_Last_Name" value="' . $thename . "\" />\n";
				} ?>
	<input type="hidden" name="x_ship_to_address" value="<?php print $ordShipAddress . (trim($ordShipAddress2)!='' ? ', ' . $ordShipAddress2 : '')?>" />
	<input type="hidden" name="x_ship_to_city" value="<?php print $ordShipCity?>" />
	<input type="hidden" name="x_ship_to_country" value="<?php print $ordShipCountry?>" />
	<input type="hidden" name="x_ship_to_state" value="<?php print $ordShipState?>" />
	<input type="hidden" name="x_ship_to_zip" value="<?php print $ordShipZip?>" />
<?php		} ?>
	<input type="hidden" name="x_Amount" value="<?php print number_format($grandtotal,2,'.','')?>" />
	<input type="hidden" name="x_Relay_Response" value="True" />
	<input type="hidden" name="x_Relay_URL" value="<?php print $storeurl?>vsadmin/wpconfirm.php" />
<?php		if($demomode) writehiddenvar('x_Test_Request', 'TRUE');
		}elseif($grandtotal == 0 || $ordPayProvider=="4"){ // Email ?>
	<form method="post" action="thanks.php">
	<input type="hidden" name="emailorder" value="<?php print $orderid?>" />
	<input type="hidden" name="thesessionid" value="<?php print $thesessionid?>" />
<?php	}elseif($grandtotal > 0 && $ordPayProvider=="17"){ // Email 2 ?>
	<form method="post" action="thanks.php">
	<input type="hidden" name="secondemailorder" value="<?php print $orderid?>" />
	<input type="hidden" name="thesessionid" value="<?php print $thesessionid?>" />
<?php	}elseif($grandtotal > 0 && $ordPayProvider=="5"){ // WorldPay ?>
	<form method="post" action="https://select.worldpay.com/wcc/purchase">
	<input type="hidden" name="instId" value="<?php print $data1?>" />
	<input type="hidden" name="cartId" value="<?php print $orderid?>" />
	<input type="hidden" name="amount" value="<?php print number_format($grandtotal,2,'.','')?>" />
	<input type="hidden" name="currency" value="<?php print $countryCurrency?>" />
	<input type="hidden" name="desc" value="<?php print substr($descstr,0,255)?>" />
	<input type="hidden" name="name" value="<?php print $ordName?>" />
	<input type="hidden" name="address" value="<?php print $ordAddress . (trim($ordAddress2)!='' ? ', ' . $ordAddress2 : '') . '&#10;' . $ordCity . '&#10;' . $ordState?>" />
	<input type="hidden" name="postcode" value="<?php print $ordZip?>" />
	<input type="hidden" name="country" value="<?php print $countryCode?>" />
	<input type="hidden" name="tel" value="<?php print $ordPhone?>" />
	<input type="hidden" name="email" value="<?php print $ordEmail?>" />
	<input type="hidden" name="authMode" value="<?php if($ppmethod==1) print 'E'; else print 'A'; ?>" />
<?php		if($demomode) writehiddenvar('testMode', '100');
			$data2arr = split("&",$data2);
			$data2 = @$data2arr[0];
			if($data2 != ''){
				print '<input type="hidden" name="signatureFields" value="amount:currency:cartId" />' . "\r\n";
				print '<input type="hidden" name="signature" value="' . md5($data2 . ":" . number_format($grandtotal,2,'.','') . ":" . $countryCurrency . ":" . $orderid) . '" />';
			}
		}elseif($grandtotal > 0 && $ordPayProvider=="6"){ // NOCHEX ?>
	<form method="post" action="https://www.nochex.com/nochex.dll/checkout">
	<input type="hidden" name="email" value="<?php print $data1?>" />
	<input type="hidden" name="returnurl" value="<?php print $storeurl . (TRUE ? 'thanks.php?ncretval=' . $orderid . '&ncsessid=' . $thesessionid : '')?>" />
	<input type="hidden" name="responderurl" value="<?php print $storeurl?>vsadmin/ncconfirm.php" />
	<input type="hidden" name="description" value="<?php print substr($descstr,0,255)?>" />
	<input type="hidden" name="ordernumber" value="<?php print $orderid?>" />
	<input type="hidden" name="amount" value="<?php print number_format($grandtotal,2,'.','')?>" />
	<input type="hidden" name="firstline" value="<?php print $ordAddress . (trim($ordAddress2)!='' ? ', ' . $ordAddress2 : '')?>" />
	<input type="hidden" name="town" value="<?php print $ordCity?>" />
	<input type="hidden" name="county" value="<?php print $ordState?>" />
	<input type="hidden" name="postcode" value="<?php print $ordZip?>" />
	<input type="hidden" name="email_address_sender" value="<?php print $ordEmail?>" />
<?php		$thename = trim($ordName);
			if($thename != ''){
				if(strstr($thename," ")){
					$namearr = split(" ",$thename,2);
					print '<input type="hidden" name="firstname" value="' . str_replace('"','&quot;',$namearr[0]) . "\" />\n";
					print '<input type="hidden" name="lastname" value="' . str_replace('"','&quot;',$namearr[1]) . "\" />\n";
				}else
					print '<input type="hidden" name="lastname" value="' . str_replace('"','&quot;',$thename) . "\" />\n";
			}
			if($demomode) writehiddenvar('status', 'test');
		}elseif($grandtotal > 0 && $ordPayProvider=="7"){ // VeriSign Payflow Pro ?>
	<form method="post" action="cart.php" onsubmit="return isvalidcard(this)">
	<input type="hidden" name="mode" value="authorize" />
	<input type="hidden" name="method" value="7" />
	<input type="hidden" name="ordernumber" value="<?php print $orderid?>" />
<?php	}elseif($grandtotal > 0 && $ordPayProvider=="8"){ // VeriSign Payflow Link
			$paymentlink = 'https://payments.verisign.com/payflowlink';
			if($data2=="VSA") $paymentlink='https://payments.verisign.com.au/payflowlink'; ?>
	<form method="post" action="<?php print $paymentlink?>">
	<input type="hidden" name="LOGIN" value="<?php print $data1?>" />
	<input type="hidden" name="PARTNER" value="<?php print $data2?>" />
	<input type="hidden" name="CUSTID" value="<?php print $orderid?>" />
	<input type="hidden" name="AMOUNT" value="<?php print number_format($grandtotal,2,'.','')?>" />
	<input type="hidden" name="TYPE" value="S" />
	<input type="hidden" name="DESCRIPTION" value="<?php print substr($descstr,0,255)?>" />
	<input type="hidden" name="NAME" value="<?php print $ordName?>" />
	<input type="hidden" name="ADDRESS" value="<?php print $ordAddress . (trim($ordAddress2)!='' ? ', ' . $ordAddress2 : '')?>" />
	<input type="hidden" name="CITY" value="<?php print $ordCity?>" />
	<input type="hidden" name="STATE" value="<?php print $ordState?>" />
	<input type="hidden" name="ZIP" value="<?php print $ordZip?>" />
	<input type="hidden" name="COUNTRY" value="<?php print $ordCountry?>" />
	<input type="hidden" name="EMAIL" value="<?php print $ordEmail?>" />
	<input type="hidden" name="PHONE" value="<?php print $ordPhone?>" />
	<input type="hidden" name="METHOD" value="CC" />
	<input type="hidden" name="ORDERFORM" value="TRUE" />
	<input type="hidden" name="SHOWCONFIRM" value="FALSE" />
<?php		if(trim($ordShipName) != '' || trim($ordShipAddress) != ''){ ?>
	<input type="hidden" name="NAMETOSHIP" value="<?php print $ordShipName?>" />
	<input type="hidden" name="ADDRESSTOSHIP" value="<?php print $ordShipAddress . (trim($ordShipAddress2)!='' ? ', ' . $ordShipAddress2 : '')?>" />
	<input type="hidden" name="CITYTOSHIP" value="<?php print $ordShipCity?>" />
	<input type="hidden" name="STATETOSHIP" value="<?php print $ordShipState?>" />
	<input type="hidden" name="ZIPTOSHIP" value="<?php print $ordShipZip?>" />
	<input type="hidden" name="COUNTRYTOSHIP" value="<?php print $ordShipCountry?>" />
<?php		} ?>
<?php	}elseif($grandtotal > 0 && $ordPayProvider=="9"){ // SECPay ?>
	<form method="post" action="https://www.secpay.com/java-bin/ValCard">
	<input type="hidden" name="merchant" value="<?php print $data1?>" />
	<input type="hidden" name="trans_id" value="<?php print $orderid?>" />
	<input type="hidden" name="amount" value="<?php print number_format($grandtotal,2,'.','')?>" />
	<input type="hidden" name="callback" value="<?php print $storeurl?>vsadmin/wpconfirm.php" />
	<input type="hidden" name="currency" value="<?php print $countryCurrency?>" />
	<input type="hidden" name="cb_post" value="true" />
	<input type="hidden" name="bill_name" value="<?php print $ordName?>" />
	<input type="hidden" name="bill_addr_1" value="<?php print $ordAddress?>" />
	<input type="hidden" name="bill_addr_2" value="<?php print $ordAddress2?>" />
	<input type="hidden" name="bill_city" value="<?php print $ordCity?>" />
	<input type="hidden" name="bill_state" value="<?php print $ordState?>" />
	<input type="hidden" name="bill_post_code" value="<?php print $ordZip?>" />
	<input type="hidden" name="bill_country" value="<?php print $ordCountry?>" />
	<input type="hidden" name="bill_email" value="<?php print $ordEmail?>" />
	<input type="hidden" name="bill_tel" value="<?php print $ordPhone?>" />
<?php		if(trim($ordShipName) != '' || trim($ordShipAddress) != ''){ ?>
	<input type="hidden" name="ship_name" value="<?php print $ordShipName?>" />
	<input type="hidden" name="ship_addr_1" value="<?php print $ordShipAddress?>" />
	<input type="hidden" name="ship_addr_2" value="<?php print $ordShipAddress2?>" />
	<input type="hidden" name="ship_city" value="<?php print $ordShipCity?>" />
	<input type="hidden" name="ship_state" value="<?php print $ordShipState?>" />
	<input type="hidden" name="ship_post_code" value="<?php print $ordShipZip?>" />
	<input type="hidden" name="ship_country" value="<?php print $ordShipCountry?>" />
<?php		}
			$data2arr = split("&",$data2);
			$data2md5=@$data2arr[0];
			$data2tpl=urldecode(@$data2arr[1]);
			if(trim($data2md5) != ''){
?>	<input type="hidden" name="digest" value="<?php print md5($orderid . number_format($grandtotal,2,'.','') . $data2md5)?>" />
	<input type="hidden" name="md_flds" value="trans_id:amount:callback" />
<?php		}
			if(trim($data2tpl) != '') print '<input type="hidden" name="template" value="' . $data2tpl . '" />';
			if($ppmethod==1) print '<input type="hidden" name="deferred" value="reuse:5:5" />';
			if(@$requirecvv==TRUE) print '<input type="hidden" name="req_cv2" value="true" />';
			if($demomode) writehiddenvar('options', 'test_status=true,dups=false');
		}elseif($grandtotal > 0 && $ordPayProvider=="10"){ // Capture Card ?>
	<form method="post" action="thanks.php" onsubmit="return isvalidcard(this)">
	<input type="hidden" name="docapture" value="vsprods" />
	<input type="hidden" name="ordernumber" value="<?php print $orderid?>" />
<?php	}elseif($grandtotal > 0 && ($ordPayProvider=="11" || $ordPayProvider=="12")){ // PSiGate ?>
	<form method="post" action="https://<?php print ($demomode ? 'dev' : 'checkout') ?>.psigate.com/HTMLPost/HTMLMessenger" <?php if($ordPayProvider=="12") print 'onsubmit="return isvalidcard(this)"' ?>>
	<input type="hidden" name="MerchantID" value="<?php print $data1?>" />
	<input type="hidden" name="Oid" value="<?php print $orderid?>" />
	<input type="hidden" name="FullTotal" value="<?php print number_format($grandtotal,2,'.','')?>" />
	<input type="hidden" name="ThanksURL" value="<?php print $storeurl?>thanks.php" />
	<input type="hidden" name="NoThanksURL" value="<?php print $storeurl?>thanks.php" />
	<input type="hidden" name="CustomerRefNo" value="<?php print substr(md5($orderid.':'.@$secretword), 0, 24)?>" />
	<input type="hidden" name="ChargeType" value="<?php if($ppmethod=="1") print "1"; else print "0"; ?>" />
	<?php if($ordPayProvider=="11"){ ?><input type="hidden" name="Bname" value="<?php print $ordName?>" /><?php } ?>
	<input type="hidden" name="Baddr1" value="<?php print $ordAddress?>" />
	<input type="hidden" name="Baddr2" value="<?php print $ordAddress2?>" />
	<input type="hidden" name="Bcity" value="<?php print $ordCity?>" />
	<input type="hidden" name="IP" value="<?php print @$_SERVER["REMOTE_ADDR"]?>" />
<?php			if($countryID==1 && $stateAbbrev != ''){ ?>
	<input type="hidden" name="Bstate" value="<?php print $stateAbbrev?>" />
<?php			}else{ ?>
	<input type="hidden" name="Bstate" value="<?php print $ordState?>" />
<?php			} ?>
	<input type="hidden" name="Bzip" value="<?php print $ordZip?>" />
	<input type="hidden" name="Bcountry" value="<?php print $countryCode?>" />
	<input type="hidden" name="Email" value="<?php print $ordEmail?>" />
	<input type="hidden" name="Phone" value="<?php print $ordPhone?>" />
<?php			if(trim($ordShipName) != '' || trim($ordShipAddress) != ''){ ?>
	<input type="hidden" name="Sname" value="<?php print $ordShipName?>" />
	<input type="hidden" name="Saddr1" value="<?php print $ordShipAddress?>" />
	<input type="hidden" name="Saddr2" value="<?php print $ordShipAddress2?>" />
	<input type="hidden" name="Scity" value="<?php print $ordShipCity?>" />
	<input type="hidden" name="Sstate" value="<?php print $ordShipState?>" />
	<input type="hidden" name="Szip" value="<?php print $ordShipZip?>" />
	<input type="hidden" name="Scountry" value="<?php print $ordShipCountry?>" />
<?php			}
				if($demomode) writehiddenvar('Result', '1');
		}elseif($grandtotal > 0 && $ordPayProvider=="13"){ // Authorize.net AIM ?>
	<form method="post" action="cart.php" onsubmit="return isvalidcard(this)">
	<input type="hidden" name="mode" value="authorize" />
	<input type="hidden" name="method" value="13" />
	<input type="hidden" name="ordernumber" value="<?php print $orderid?>" />
	<input type="hidden" name="description" value="<?php print substr($descstr,0,254)?>" />
<?php	}elseif($grandtotal > 0 && $ordPayProvider=="14"){ // Custom Pay Provider
			include "./vsadmin/inc/customppsend.php";
		}elseif($grandtotal > 0 && $ordPayProvider=="15"){ // Netbanx ?>
	<form method="post" action="https://www.netbanx.com/cgi-bin/payment/<?php print $data1;?>">
	<input type="hidden" name="order_id" value="<?php print $orderid?>" />
	<input type="hidden" name="payment_amount" value="<?php print number_format($grandtotal,2,'.','')?>" />
	<input type="hidden" name="currency_code" value="<?php print $countryCurrency?>" />
	<input type="hidden" name="cardholder_name" value="<?php print $ordName?>" />
	<input type="hidden" name="email" value="<?php print $ordEmail?>" />
	<input type="hidden" name="postcode" value="<?php print $ordZip?>" />
<?php	}elseif($grandtotal > 0 && $ordPayProvider=="16"){ // Linkpoint
			if($demomode) $theurl='https://staging.linkpt.net/lpc/servlet/lppay'; else $theurl='https://www.linkpointcentral.com/lpc/servlet/lppay';
			$lpsubtotal = round($totalgoods - $totaldiscounts, 2);
			$lpshipping = round(($shipping + $handling) - $freeshipamnt, 2);
			$lptax = round($stateTax + $countryTax, 2);
?>
	<form action="<?php print $theurl?>" method="post"<?php if($data2=="1") print ' onsubmit="return isvalidcard(this)"' ?>>
	<input type="hidden" name="storename" value="<?php print $data1?>" />
	<input type="hidden" name="mode" value="payonly" />
	<input type="hidden" name="ponumber" value="<?php print $orderid?>" />
	<input type="hidden" name="oid" value="<?php print $orderid.'.'.time()?>" />
	<input type="hidden" name="responseURL" value="<?php print $storeurl?>thanks.php" />
	<input type="hidden" name="subtotal" value="<?php print number_format($lpsubtotal,2,'.','')?>" />
	<input type="hidden" name="chargetotal" value="<?php print number_format($lpsubtotal+$lpshipping+$lptax,2,'.','')?>" />
	<input type="hidden" name="shipping" value="<?php print number_format($lpshipping,2,'.','')?>" />
	<input type="hidden" name="tax" value="<?php print number_format($lptax,2,'.','')?>" />
	<?php if($data2!="1"){ ?><input type="hidden" name="bname" value="<?php print $ordName?>" /><?php } ?>
	<input type="hidden" name="baddr1" value="<?php print $ordAddress?>" />
	<input type="hidden" name="baddr2" value="<?php print $ordAddress2?>" />
	<input type="hidden" name="bcity" value="<?php print $ordCity?>" />
<?php		if($countryID==1 && $stateAbbrev != ''){ ?>
		<input type="hidden" name="bstate" value="<?php print $stateAbbrev?>" />
<?php		}else{ ?>
		<input type="hidden" name="bstate2" value="<?php print $ordState?>" />
<?php		} ?>
	<input type="hidden" name="bzip" value="<?php print $ordZip?>" />	
	<input type="hidden" name="bcountry" value="<?php print $countryCode?>" />
	<input type="hidden" name="email" value="<?php print $ordEmail?>" />
	<input type="hidden" name="phone" value="<?php print $ordPhone?>" />
	<input type="hidden" name="txntype" value="<?php if($ppmethod==1) print "preauth"; else print "sale" ?>" />
<?php		if(trim($ordShipName) != '' || trim($ordShipAddress) != ''){ ?>
	<input type="hidden" name="sname" value="<?php print $ordShipName?>" />
	<input type="hidden" name="saddr1" value="<?php print $ordShipAddress?>" />
	<input type="hidden" name="saddr2" value="<?php print $ordShipAddress2?>" />
	<input type="hidden" name="scity" value="<?php print $ordShipCity?>" />
	<input type="hidden" name="sstate" value="<?php print $ordShipState?>" />
	<input type="hidden" name="szip" value="<?php print $ordShipZip?>" />
	<input type="hidden" name="scountry" value="<?php print $shipCountryCode?>" />
<?php		}
			if($demomode) writehiddenvar('txnmode', 'test');
		}elseif($grandtotal > 0 && $ordPayProvider=="18"){ // PayPal Direct Payment ?>
	<form method="post" action="cart.php" onsubmit="return isvalidcard(this)">
	<input type="hidden" name="mode" value="authorize" />
	<input type="hidden" name="method" value="18" />
	<input type="hidden" name="ordernumber" value="<?php print $orderid?>" />
	<input type="hidden" name="description" value="<?php print str_replace('"','&quot;',substr($descstr,0,254))?>" />
<?php	}elseif($grandtotal > 0 && $ordPayProvider=="19"){ // PayPal Express Payment ?>
	<form method="post" action="thanks.php">
	<input type="hidden" name="token" value="<?php print $token?>" />
	<input type="hidden" name="method" value="paypalexpress" />
	<input type="hidden" name="ordernumber" value="<?php print $orderid?>" />
	<input type="hidden" name="payerid" value="<?php print $payerid?>" />
	<input type="hidden" name="email" value="<?php print $ordEmail?>" />
<?php	}
	}
	if($success){
?><br />
            <table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB" height="30" colspan="2" align="center"><strong><?php print $xxChkCmp?></strong></td>
			  </tr>
<?php	if(($cpncode!='' || ($ordPayProvider=='19' && @$_GET['token']!='')) && ! $gotcpncode){ ?>
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB" height="30" align="right" width="50%"><strong><?php if($cpncode!='' && $ordPayProvider=='19' && ! $gotcpncode) print '<font color="#FF0000">' . $xxCpnNoF . '</font>'; else print $xxGifCer.':'?></strong></td>
				<td class="cobll" bgcolor="#FFFFFF" height="30" align="left" width="50%"><font size="1"><?php
						if($ordPayProvider=='19' && ! $gotcpncode && @$_GET['token']!=''){
							print '<input type="text" name="cpncode" id="cpncode" size="20" value="' . htmlspecialchars($cpncode) . '" /> <input type="button" value="' . $xxAppCpn . '" onclick="document.location=\'cart.php?token='.$_GET['token'].'&cpncode=\'+document.getElementById(\'cpncode\').value" />';
						}else{
							if($shippingpost=='') $jumpback=1; else $jumpback=2;
							printf($xxNoGfCr,$cpncode,$jumpback);
						} ?></font></td>
			  </tr>
<?php	}
		if($cpnmessage!=''){ ?>
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB" height="30" align="right" width="50%"><strong><?php print $xxAppDs?>:</strong></strong></td>
				<td class="cobll" bgcolor="#FFFFFF" height="30" align="left" width="50%"><?php print $cpnmessage?></td>
			  </tr>
<?php	} ?>
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB" height="30" align="right" width="50%"><strong><?php print $xxTotGds?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" height="30" align="left" width="50%"><?php print FormatEuroCurrency($totalgoods)?></td>
			  </tr>
<?php	if(@$combineshippinghandling==TRUE){ ?>
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB" height="30" align="right" width="50%"><strong><?php print $xxShipHa?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" height="30" align="left" width="50%"><?php print FormatEuroCurrency(($shipping+$handling)-$freeshipamnt)?></td>
			  </tr>
<?php	}else{
			if($shipType != 0){ ?>
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB" height="30" align="right" width="50%"><strong><?php print $xxShippg?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" height="30" align="left" width="50%"><?php print FormatEuroCurrency($shipping-$freeshipamnt)?></td>
			  </tr>
<?php		}
			if($handling != 0){ ?>
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB" height="30" align="right" width="50%"><strong><?php print $xxHndlg?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" height="30" align="left" width="50%"><?php print FormatEuroCurrency($handling)?></td>
			  </tr>
<?php		}
		}
		if(($totaldiscounts) !=0){ ?>
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB" height="30" align="right" width="50%"><strong><?php print $xxTotDs?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" height="30" align="left" width="50%"><font color="#FF0000"><?php print FormatEuroCurrency($totaldiscounts)?></font></td>
			  </tr>
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB" height="30" align="right" width="50%"><strong><?php print $xxSubTot?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" height="30" align="left" width="50%"><?php print FormatEuroCurrency(($totalgoods+$shipping+$handling)-($totaldiscounts+$freeshipamnt))?></td>
			  </tr>
<?php	}
		if($usehst){ ?>
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB" height="30" align="right" width="50%"><strong><?php print $xxHST?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" height="30" align="left" width="50%"><?php print FormatEuroCurrency($stateTax+$countryTax)?></td>
			  </tr>
<?php	}else{
			if($stateTax != 0.0){ ?>
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB" height="30" align="right" width="50%"><strong><?php print $xxStaTax?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" height="30" align="left" width="50%"><?php print FormatEuroCurrency($stateTax)?></td>
			  </tr>
<?php		}
			if($countryTax != 0.0){ ?>
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB" height="30" align="right" width="50%"><strong><?php print $xxCntTax?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" height="30" align="left" width="50%"><?php print FormatEuroCurrency($countryTax)?></td>
			  </tr>
<?php		}
		}?>
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB" height="30" align="right" width="50%"><strong><?php print $xxGndTot?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" height="30" align="left" width="50%"><?php print FormatEuroCurrency($grandtotal)?></td>
			  </tr>
<?php if($grandtotal > 0 && ($ordPayProvider=="7" || $ordPayProvider=="10" || $ordPayProvider=="12" || $ordPayProvider=="13" || ($ordPayProvider=="16" && $data2=="1") || $ordPayProvider=="18")){ // VeriSign Payflow Pro || Capture Card || PSiGate || Auth.NET AIM || PayPal Pro
			if($ordPayProvider=="7" || $ordPayProvider=="12" || $ordPayProvider=="13" || $ordPayProvider=="16" || $ordPayProvider=="18") $data1 = "XXXXXXX0XXXXXXXXXXXXXXXXX";
			$isPSiGate = ($ordPayProvider=="12");
			$isLinkpoint = ($ordPayProvider=="16");
			if($isPSiGate){
				$sscardname="bname";
				$sscardnum = "CardNumber";
				$ssexmon = "CardExpMonth";
				$ssexyear = "CardExpYear";
				$sscvv2 = 'CardIDNumber';
			}elseif($isLinkpoint){
				$sscardname="bname";
				$sscardnum = "cardnumber";
				$ssexmon = "expmonth";
				$ssexyear = "expyear";
				$sscvv2 = "cvm";
			}else{
				$sscardname="cardname";
				$sscardnum = "ACCT";
				$ssexmon = "EXMON";
				$ssexyear = "EXYEAR";
				$sscvv2 = "CVV2";
			}
			$acceptecheck = ((@$acceptecheck==true) && ($ordPayProvider=="13"));
?>
<input type="hidden" name="vrshippingoptions" value="<?php print @$_POST['vrshippingoptions']?>" />
<input type="hidden" name="sessionid" value="<?php print $thesessionid?>" />
<script language="javascript" type="text/javascript">
<!--
var isswitchcard=false;
function isCreditCard(st){
  // Encoding only works on cards with less than 19 digits
  if (st.length > 19)
    return (false);
  sum = 0; mul = 1; l = st.length;
  for (i = 0; i < l; i++) {
    digit = st.substring(l-i-1,l-i);
    tproduct = parseInt(digit ,10)*mul;
    if (tproduct >= 10)
      sum += (tproduct % 10) + 1;
    else
      sum += tproduct;
    if (mul == 1)
      mul++;
    else
      mul = mul - 1;
  }
  if ((sum % 10) == 0)
    return (true);
  else
    return (false);
}
function isVisa(cc){ // 4111 1111 1111 1111
  if (((cc.length == 16) || (cc.length == 13)) && (cc.substring(0,1) == 4))
    return isCreditCard(cc);
  return false;
}
function isMasterCard(cc){ // 5500 0000 0000 0004
  firstdig = cc.substring(0,1);
  seconddig = cc.substring(1,2);
  if ((cc.length == 16) && (firstdig == 5) && ((seconddig >= 1) && (seconddig <= 5)))
    return isCreditCard(cc);
  return false;
}
function isAmericanExpress(cc){ // 340000000000009
  firstdig = cc.substring(0,1);
  seconddig = cc.substring(1,2);
  if ((cc.length == 15) && (firstdig == 3) && ((seconddig == 4) || (seconddig == 7)))
    return isCreditCard(cc);
  return false;
}
function isDinersClub(cc){ // 30000000000004
  firstdig = cc.substring(0,1);
  seconddig = cc.substring(1,2);
  if ((cc.length == 14) && (firstdig == 3) &&
      ((seconddig == 0) || (seconddig == 6) || (seconddig == 8)))
    return isCreditCard(cc);
  return false;
}
function isDiscover(cc){ // 6011000000000004
  first4digs = cc.substring(0,4);
  if ((cc.length == 16) && (first4digs == "6011"))
    return isCreditCard(cc);
  return false;
}
function isAusBankcard(cc){ // 5610591000000009
  first4digs = cc.substring(0,4);
  if ((cc.length == 16) && (first4digs == "5610"))
    return isCreditCard(cc);
  return false;
}
function isEnRoute(cc){ // 201400000000009
  first4digs = cc.substring(0,4);
  if ((cc.length == 15) && ((first4digs == "2014") || (first4digs == "2149")))
    return isCreditCard(cc);
  return false;
}
function isJCB(cc){
  first4digs = cc.substring(0,4);
  if ((cc.length == 16) && ((first4digs == "3088") || (first4digs == "3096") || (first4digs == "3112") || (first4digs == "3158") || (first4digs == "3337") || (first4digs == "3528")))
    return isCreditCard(cc);
  return false;
}
function isSwitch(cc){ // 675911111111111128
  first4digs = cc.substring(0,4);
  if ((cc.length == 16 || cc.length == 17 || cc.length == 18 || cc.length == 19) && ((first4digs == "4903") || (first4digs == "4911") || (first4digs == "4936") || (first4digs == "5641") || (first4digs == "6333") || (first4digs == "6759") || (first4digs == "6334") || (first4digs == "6767"))){
    isswitchcard=isCreditCard(cc);
    return(isswitchcard);
  }
  return false;
}
function isvalidcard(theForm){
  cc = theForm.<?php print $sscardnum?>.value;
  newcode = "";
  l = cc.length;
  for(i=0;i<l;i++){
	digit = cc.substring(i,i+1);
	digit = parseInt(digit ,10);
	if(!isNaN(digit)) newcode += digit;
  }
  cc=newcode;
  if (theForm.<?php print $sscardname?>.value==""){
	alert("<?php print $xxPlsEntr . ' \"' . $xxCCName . '\"' ?>");
	theForm.<?php print $sscardname?>.focus();
    return false;
  }
<?php if($acceptecheck==true){ ?>
if(cc!="" && theForm.accountnum.value!=""){
alert("Please enter either Credit Card OR ECheck details");
return(false);
}else if(theForm.accountnum.value!=""){
  if(theForm.accountname.value==""){
    alert("Please enter a value in the field \"Account Name\".");
	theForm.accountname.focus();
    return false;
  }
  if(theForm.bankname.value==""){
    alert("Please enter a value in the field \"Bank Name\".");
	theForm.bankname.focus();
    return false;
  }
  if(theForm.routenumber.value==""){
    alert("Please enter a value in the field \"Routing Number\".");
	theForm.routenumber.focus();
    return false;
  }
  if(theForm.accounttype.selectedIndex==0){
    alert("Please select your account type: (Checking / Savings).");
	theForm.accounttype.focus();
    return false;
  }
<?php	if(@$wellsfargo==true){ ?>
  if(theForm.orgtype.selectedIndex==0){
    alert("Please select your account type: (Personal / Business).");
	theForm.orgtype.focus();
    return false;
  }
  if(theForm.taxid.value=="" && theForm.licensenumber.value==""){
    alert("Please enter either a Tax ID number or Drivers License Details.");
	theForm.taxid.focus();
    return false;
  }
  if(theForm.taxid.value==""){
	  if(theForm.licensestate.selectedIndex==0){
		alert("Please select your Drivers License State.");
		theForm.licensestate.focus();
		return false;
	  }
	  if(theForm.dldobmon.selectedIndex==0){
		alert("Please select your Drivers License D.O.B. Month.");
		theForm.dldobmon.focus();
		return false;
	  }
	  if(theForm.dldobday.selectedIndex==0){
		alert("Please select your Drivers License D.O.B. Day.");
		theForm.dldobday.focus();
		return false;
	  }
	  if(theForm.dldobyear.selectedIndex==0){
		alert("Please select your Drivers License D.O.B. year.");
		theForm.dldobyear.focus();
		return false;
	  }
  }
<?php	} ?>
}else{
<?php } ?>
  if (true <?php 
		if(substr($data1,0,1)=="X") print "&& !isVisa(cc) ";
		if(substr($data1,1,1)=="X") print "&& !isMasterCard(cc) ";
		if(substr($data1,2,1)=="X") print "&& !isAmericanExpress(cc) ";
		if(substr($data1,3,1)=="X") print "&& !isDinersClub(cc) ";
		if(substr($data1,4,1)=="X") print "&& !isDiscover(cc) ";
		if(substr($data1,5,1)=="X") print "&& !isEnRoute(cc) ";
		if(substr($data1,6,1)=="X") print "&& !isJCB(cc) ";
		if(substr($data1,7,1)=="X") print "&& !isSwitch(cc) ";
		if(substr($data1,8,1)=="X") print "&& !isAusBankcard(cc) "; ?>){
	<?php if($acceptecheck==true) $xxValCC="Please enter a valid credit card number or bank account details if paying by ECheck."; ?>
	alert("<?php print $xxValCC?>");
	theForm.<?php print $sscardnum?>.focus();
    return false;
  }
  if(theForm.<?php print $ssexmon?>.selectedIndex==0){
    alert("<?php print $xxCCMon?>");
	theForm.<?php print $ssexmon?>.focus();
    return false;
  }
  if(theForm.<?php print $ssexyear?>.selectedIndex==0){
    alert("<?php print $xxCCYear?>");
	theForm.<?php print $ssexyear?>.focus();
    return false;
  }
<?php if(substr($data1,7,1)=="X"){ ?>
  if(theForm.IssNum.value=="" && isswitchcard){
    alert("Please enter an issue number / start date for Switch/Solo cards.");
	theForm.IssNum.focus();
    return false;
  }
<?php }
	  if(@$requirecvv==TRUE){ ?>
  if(theForm.<?php print $sscvv2?>.value==""){
    alert("<?php print $xxPlsEntr . ' \"' . str_replace('"','\"',$xx34code) . '\"'?>");
	theForm.<?php print $sscvv2?>.focus();
    return false;
  }
<?php }
	  if(@$acceptecheck==true) print '}'; ?>
  return true;
}
//-->
</script>
<?php if(@$_SERVER["HTTPS"] != "on" && (@$_SERVER["SERVER_PORT"] != "443") && @$nochecksslserver != TRUE){ ?>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="center" colspan="2"><strong><font color="#FF0000">This site may not be secure. Do not enter real Credit Card numbers.</font></strong></td>
			  </tr>
<?php } ?>
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB" height="30" colspan="2" align="center"><strong><?php print $xxCCDets ?></strong></td>
			  </tr>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong><?php print $xxCCName?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" width="50%"><input type="text" name="<?php print $sscardname?>" size="21" value="<?php print $ordName?>" AUTOCOMPLETE="off" /></td>
			  </tr>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong><?php print $xxCrdNum?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" width="50%"><input type="text" name="<?php print $sscardnum?>" size="21" AUTOCOMPLETE="off" /></td>
			  </tr>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong><?php print $xxExpEnd?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" width="50%">
				  <select name="<?php print $ssexmon?>" size="1">
					<option value=""><?php print $xxMonth?></option>
					<?php	for($index=1; $index<=12; $index++){
								if($index < 10) $themonth = "0" . $index; else $themonth = $index;
								print "<option value='" . $themonth . "'>" . $themonth . "</option>\n";
							} ?>
				  </select> / <select name="<?php print $ssexyear?>" size="1">
					<option value=""><?php print $xxYear?></option>
					<?php	$thisyear=date("Y", time());
							for($index=$thisyear; $index <= $thisyear+10; $index++){
								if($isPSiGate)
									print "<option value='" . substr($index,-2) . "'>" . $index . "</option>\n";
								else
									print "<option value='" . $index . "'>" . $index . "</option>\n";
							} ?>
				  </select>
				</td>
			  </tr>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong><?php print $xx34code?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" width="50%"><input type="text" name="<?php print $sscvv2?>" size="4" AUTOCOMPLETE="off" /> <strong><?php if(@$requirecvv!=TRUE)print $xxIfPres?></strong></td>
			  </tr>
<?php			if(substr($data1,7,1)=="X"){ ?>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong>Issue Number / Start Date:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" width="50%"><input type="text" name="IssNum" size="4" AUTOCOMPLETE="off" /> <strong>(Switch/Solo Only)</strong></td>
			  </tr>
<?php			}
				if($acceptecheck==true){ // Auth.net ?>
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB" height="30" colspan="2" align="center"><strong>ECheck Details</strong><br /><font size="1">Please enter either Credit Card OR ECheck details</font></td>
			  </tr>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong>Account Name:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" width="50%"><input type="text" name="accountname" size="21" AUTOCOMPLETE="off" value="<?php print $ordName?>" /></td>
			  </tr>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong>Account Number:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" width="50%"><input type="text" name="accountnum" size="21" AUTOCOMPLETE="off" /></td>
			  </tr>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong>Bank Name:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" width="50%"><input type="text" name="bankname" size="21" AUTOCOMPLETE="off" /></td>
			  </tr>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong>Routing Number:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" width="50%"><input type="text" name="routenumber" size="10" AUTOCOMPLETE="off" /></td>
			  </tr>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong>Account Type:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" width="50%"><select name="accounttype" size="1"><option value=""><?php print $xxPlsSel?></option><option value="CHECKING">Checking</option><option value="SAVINGS">Savings</option><option value="BUSINESSCHECKING">Business Checking</option></select></td>
			  </tr>
<?php				if(@$wellsfargo==true){ ?>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong>Personal or Business Acct.:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" width="50%"><select name="orgtype" size="1"><option value=""><?php print $xxPlsSel?></option><option value="I">Personal</option><option value="B">Business</option></select></td>
			  </tr>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong>Tax ID:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" width="50%"><input type="text" name="taxid" size="21" AUTOCOMPLETE="off" /></td>
			  </tr>
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB" height="30" colspan="2" align="center"><font size="1">If you have provided a Tax ID then the following information is not necessary</font></td>
			  </tr>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong>Drivers License Number:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" width="50%"><input type="text" name="licensenumber" size="21" AUTOCOMPLETE="off" /></td>
			  </tr>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong>Drivers License State:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" width="50%"><select size="1" name="licensestate"><option value=""><?php print $xxPlsSel?></option><?php
				$sSQL = "SELECT stateName,stateAbbrev FROM states WHERE stateEnabled=1 ORDER BY stateName";
				$result = mysql_query($sSQL) or print(mysql_error());
				while($rs = mysql_fetch_array($result)){
					print '<option value="' . str_replace('"','&quot;',$rs["stateAbbrev"]) . '"';
					print '>' . $rs["stateName"] . "</option>\n";
				}
				mysql_free_result($result); ?></select></td>
			  </tr>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong>Date Of Birth On License:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" width="50%">
				  <select name="dldobmon" size="1">
					<option value=""><?php print $xxMonth?></option>
					<?php for($index=1; $index <= 12; $index++){ ?>
					<option value="<?php print $index?>"><?php print date("M", mktime(1,0,0,$index,1,1990))?></option>
					<?php } ?>
				  </select>
				  <select name="dldobday" size="1">
					<option value="">Day</option>
					<?php for($index=1; $index <= 31; $index++){ ?>
					<option value="<?php print $index?>"><?php print $index?></option>
					<?php } ?>
				  </select>
				  <select name="dldobyear" size="1">
					<option value=""><?php print $xxYear?></option>
					<?php $thisyear = date("Y");
						  for($index=$thisyear-100; $index <= $thisyear; $index++){ ?>
					<option value="<?php print $index?>"><?php print $index?></option>
					<?php } ?>
				  </select>
				</td>
			  </tr>
<?php				}
				}
	} ?>
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB" height="30" colspan="2" align="center"><strong><?php print $xxMstClk?></strong></td>
			  </tr>
			  <tr>
				<td class="cobll" bgcolor="#FFFFFF" colspan="2" align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0">
				    <tr>
					  <td class="cobll" bgcolor="#FFFFFF" width="16" height="26" align="right" valign="bottom">&nbsp;</td>
					  <td class="cobll" bgcolor="#FFFFFF" width="100%" align="center"><?php if($orderid != 0){ ?><input type="image" src="images/checkout.gif" border="0" alt="<?php print $xxCOTxt?>" /><?php } ?></td>
					  <td class="cobll" bgcolor="#FFFFFF" width="16" height="26" align="right" valign="bottom"><img src="images/tablebr.gif" alt="" /></td>
					</tr>
				  </table></td>
			  </tr>
			</table>
	</form>
<?php
	} // success
}elseif(@$_POST['mode']=='authorize'){
	$blockuser=checkuserblock('');
	$ordID = mysql_escape_string(str_replace("'",'',@$_POST['ordernumber']));
	$vsRESULT='x';
	$vsRESPMSG=$vsAVSADDR=$vsAVSZIP=$vsTRANSID='';
	$gobackplaces=1;
	getpayprovdetails(@$_POST['method'],$data1,$data2,$data3,$demomode,$ppmethod);
	if(@$_POST['method']=='7'){ // PayFlow Pro
		$vsdetails = split("&", $data1);
		$vs1=@$vsdetails[0];
		$vs2=@$vsdetails[1];
		$vs3=@$vsdetails[2];
		$vs4=@$vsdetails[3];
		$sSQL = "SELECT ordZip,ordShipping,ordStateTax,ordCountryTax,ordHandling,ordTotal,ordDiscount,ordAddress,ordAddress2,ordAuthNumber FROM orders WHERE ordID='" . $ordID . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		$rs = mysql_fetch_assoc($result);
		$vsAUTHCODE = $rs['ordAuthNumber'];
		$theaddress = $rs['ordAddress'] . ($rs['ordAddress2'] != '' ? ', ' . $rs['ordAddress2'] : '');
		if(@$pathtopfpro==''){
			$parmList = array( 'TRXTYPE'=>($ppmethod==1 ? 'A' : 'S'), 'TENDER'=>'C', 'ZIP' => $rs['ordZip'], 'STREET' => $theaddress, 'NAME' => @$_POST['cardname'], 'COMMENT1' => $ordID, 'ACCT' => str_replace(' ', '', @$_POST['ACCT']), 'CUSTIP' => @$_SERVER["REMOTE_ADDR"], 'PWD' => $vs4, 'USER' => $vs1, 'VENDOR' => $vs2, 'PARTNER' => $vs3, 'CVV2' => trim(@$_POST['CVV2']), 'EXPDATE' => @$_POST['EXMON'] . substr(@$_POST['EXYEAR'], -2), 'AMT' => number_format(($rs['ordShipping']+$rs['ordStateTax']+$rs['ordCountryTax']+$rs['ordTotal']+$rs['ordHandling'])-$rs['ordDiscount'],2,'.',''));
		}else{
			$parmList = 'TRXTYPE=' . ($ppmethod==1 ? 'A' : 'S') . '&TENDER=C&ZIP[' . strlen($rs['ordZip']) . ']=' . $rs['ordZip'] . '&STREET[' . strlen($theaddress) . ']=' . $theaddress;
			$parmList .= '&NAME[' . strlen(@$_POST['cardname']) . ']=' . @$_POST['cardname'];
			$parmList .= '&COMMENT1=' . $ordID . '&ACCT=' . str_replace(' ', '', @$_POST['ACCT']) . '&CUSTIP=' . @$_SERVER["REMOTE_ADDR"];
			$parmList .= '&PWD=' . $vs4 . '&USER=' . $vs1 . '&VENDOR=' . $vs2 . '&PARTNER=' . $vs3 . '&CVV2=' . trim(@$_POST['CVV2']);
			$parmList .= '&EXPDATE=' . @$_POST['EXMON'] . substr(@$_POST['EXYEAR'], -2);
			$parmList .= '&AMT=' . number_format(($rs['ordShipping']+$rs['ordStateTax']+$rs['ordCountryTax']+$rs['ordTotal']+$rs['ordHandling'])-$rs['ordDiscount'],2,'.','');
			if(trim(@$_POST['IssNum']) != ''){
				if(strlen(trim($_POST['IssNum']))==2) $parmList .= '&CARDISSUE=' . trim($_POST['IssNum']); else $parmList .= '&CARDSTART=' . trim($_POST['IssNum']);
			}
		}
		mysql_free_result($result);
		function process_pfpro($str, $server, $port, $timeout){
			global $pathtopfpro,$pathtopfprocert,$pathtopfprolib,$parmList;
			if(@$pathtopfprocert!='')
				putenv("PFPRO_CERT_PATH=$pathtopfprocert");
			if(@$pathtopfpro=='COM'){
				$objCOM = new COM('PFProCOMControl.PFProCOMControl.1');
				$ctx1 = $objCOM->CreateContext($server, $port, $timeout, '', 0, '', '');
				$pfret = $objCOM->SubmitTransaction($ctx1, $str, strlen($str));
				$objCOM->DestroyContext($ctx1);
			}elseif(@$pathtopfpro!=''){
				if(! file_exists($pathtopfpro)) print "cannot find pfpro executable. Check \$pathtopfpro<br>";
				if(@$pathtopfprolib!='')
					putenv("LD_LIBRARY_PATH=$pathtopfprolib");
				$sendstr = $pathtopfpro . ' ' . $server . ' ' . $port . ' "' . $str . '" ' . $timeout;
				exec ($sendstr, $pfret, $retvar);
				$pfret = implode("\n",$pfret);
			}else{
				$pfret = pfpro_process($parmList, $server);
			}
			return $pfret;
		}
		if($vsAUTHCODE==''){
			if($vs3=='VSA')
				if($demomode) $theurl = 'payflow-test.verisign.com.au'; else $theurl = 'payflow.verisign.com.au';
			else
				if($demomode) $theurl = 'test-payflow.verisign.com'; else $theurl = 'payflow.verisign.com';
			$curString = process_pfpro($parmList, $theurl, 443, 30);
			if(!is_array($curString)){
				$curStringArr = array();
				while(strlen($curString) != 0){
					if(strpos($curString,"&")!==FALSE)
						$varString = substr($curString, 0, strpos($curString , "&" ));
					else
						$varString = $curString;
					$name = substr($varString, 0, strpos($varString, '=' ));
					$curStringArr[$name] = substr($varString, (strlen($name)+1) - strlen($varString));
					if(strlen($curString) != strlen($varString)) $curString = substr($curString,  (strlen($varString)+1) - strlen($curString)); else $curString = '';
				}
				$curString = $curStringArr;
			}
			$vsRESULT=$curString['RESULT'];
			$vsPNREF=@$curString['PNREF'];
			$vsRESPMSG=@$curString['RESPMSG'];
			$vsAUTHCODE=@$curString['AUTHCODE'];
			$vsAVSADDR=@$curString['AVSADDR'];
			$vsAVSZIP=@$curString['AVSZIP'];
			$vsIAVS=@$curString['IAVS'];
			$vsCVV2=@$curString['CVV2MATCH'];
			if($vsRESULT=='0' || $vsRESULT=='126'){
				if($vsRESULT=='126'){ $underreview='Fraud Review:<br />';$vsRESPMSG='Approved'; }else $underreview='';
				do_stock_management($ordID);
				mysql_query("UPDATE cart SET cartCompleted=1 WHERE cartOrderID='" . $ordID . "'") or print(mysql_error());
				mysql_query("UPDATE orders SET ordStatus=3,ordAVS='" . mysql_escape_string($vsAVSADDR . $vsAVSZIP) . "',ordCVV='" . mysql_escape_string($vsCVV2) . "',ordAuthNumber='" . mysql_escape_string($underreview . $vsAUTHCODE) . "' WHERE ordID='" . $ordID . "'") or print(mysql_error());
				$vsRESULT='0';
			}
		}else{
			$vsRESULT='0';
			$vsRESPMSG='Approved';
		}
	}elseif(@$_POST['method']=='13'){ // Auth.net AIM
		if(@$secretword != ''){
			$data1 = upsdecode($data1, $secretword);
			$data2 = upsdecode($data2, $secretword);
		}
		$sSQL = "SELECT ordID,ordName,ordCity,ordState,ordCountry,ordPhone,ordHandling,ordZip,ordEmail,ordShipping,ordStateTax,ordCountryTax,ordTotal,ordDiscount,ordAddress,ordAddress2,ordIP,ordAuthNumber,ordShipName,ordShipAddress,ordShipAddress2,ordShipCity,ordShipState,ordShipCountry,ordShipZip FROM orders WHERE ordID='" . $ordID . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		$rs = mysql_fetch_array($result);
		mysql_free_result($result);
		$vsAUTHCODE = trim($rs["ordAuthNumber"]);
		$parmList = 'x_version=3.1&x_delim_data=True&x_relay_response=False&x_delim_char=|&x_duplicate_window=15';
		$parmList .= '&x_login=' . $data1 . '&x_tran_key=' . $data2 . '&x_cust_id=' . $rs['ordID'] . '&x_Invoice_Num=' . $rs['ordID'];
		$parmList .= '&x_amount=' . number_format(($rs['ordShipping']+$rs['ordStateTax']+$rs['ordCountryTax']+$rs['ordTotal']+$rs['ordHandling'])-$rs['ordDiscount'],2,'.','');
		$parmList .= '&x_currency_code=' . $countryCurrency . '&x_Description=' . substr(urlencode(unstripslashes(@$_POST['description'])),0,255);
		if(trim(@$_POST['accountnum']) != ''){
			$parmList .= '&x_method=ECHECK&x_echeck_type=WEB&x_recurring_billing=NO';
			$parmList .= '&x_bank_acct_name=' . urlencode(trim(unstripslashes(@$_POST['accountname']))) . '&x_bank_acct_num=' . urlencode(trim(@$_POST['accountnum']));
			$parmList .= '&x_bank_name=' . urlencode(trim(unstripslashes(@$_POST['bankname']))) . '&x_bank_aba_code=' . urlencode(trim(@$_POST['routenumber']));
			$parmList .= '&x_bank_acct_type=' . urlencode(trim(@$_POST['accounttype'])) . '&x_type=AUTH_CAPTURE';
			if(@$wellsfargo==true){
				$parmList .= '&x_customer_organization_type=' . trim(@$_POST['orgtype']);
				if(trim(@$_POST['taxid']) != '')
					$parmList .= '&x_customer_tax_id=' . urlencode(trim(@$_POST['taxid']));
				else
					$parmList .= '&x_drivers_license_num=' . urlencode(trim(@$_POST['licensenumber'])) . '&x_drivers_license_state=' . urlencode(trim(@$_POST['licensestate'])) . '&x_drivers_license_dob=' . urlencode(trim(@$_POST['dldobyear']) . '/' . trim(@$_POST['dldobmon']) . '/' . trim(@$_POST['dldobday']));
			}
		}else{
			$parmList .= '&x_method=CC&x_card_num=' . urlencode(@$_POST['ACCT']) . '&x_exp_date=' . @$_POST['EXMON'] . @$_POST['EXYEAR'];
			if(trim(@$_POST['CVV2']) != '') $parmList .= '&x_card_code=' . trim(@$_POST['CVV2']);
			if($ppmethod==1) $parmList .= '&x_type=AUTH_ONLY'; else $parmList .= '&x_type=AUTH_CAPTURE';
		}
		$thename = trim(unstripslashes(@$_POST['cardname']));
		if($thename != ''){
			if(strstr($thename,' ')){
				$namearr = split(' ',$thename,2);
				$parmList .= '&x_first_name=' . urlencode($namearr[0]) . '&x_last_name=' . urlencode($namearr[1]);
			}else
				$parmList .= '&x_last_name=' . urlencode($thename);
		}
		$parmList .= '&x_address=' . urlencode($rs['ordAddress']);
		if($rs['ordAddress2'] != '') $parmList .= urlencode(', ' . $rs['ordAddress2']);
		$parmList .= '&x_city=' . urlencode($rs['ordCity']) . '&x_state=' . urlencode($rs['ordState']) . '&x_zip=' . urlencode($rs['ordZip']) . '&x_country=' . urlencode($rs['ordCountry']) . '&x_phone=' . urlencode($rs['ordPhone']) . '&x_email=' . urlencode($rs['ordEmail']);
		$thename = trim($rs['ordShipName']);
		if($thename != '' || $rs['ordShipAddress'] != ''){
			if($thename != ''){
				if(strstr($thename,' ')){
					$namearr = split(' ',$thename,2);
					$parmList .= '&x_ship_to_first_name=' . urlencode($namearr[0]) . '&x_ship_to_last_name=' . urlencode($namearr[1]);
				}else
					$parmList .= '&x_ship_to_last_name=' . urlencode($thename);
			}
			$parmList .= '&x_ship_to_address=' . urlencode($rs['ordShipAddress']);
			if($rs['ordShipAddress2'] != '') $parmList .= urlencode(', ' . $rs['ordShipAddress2']);
			$parmList .= '&x_ship_to_city=' . urlencode($rs['ordShipCity']) . '&x_ship_to_state=' . urlencode($rs['ordShipState']) . '&x_ship_to_zip=' . urlencode($rs['ordShipZip']) . '&x_ship_to_country=' . urlencode($rs['ordShipCountry']);
		}
		if(trim($rs['ordIP']) != '') $parmList .= '&x_customer_ip=' . urlencode(trim($rs['ordIP']));
		if($demomode) $parmList .= '&x_test_request=TRUE';
		if($vsAUTHCODE==''){
			$success=true;
			if($blockuser){
				$success = FALSE;
				$vsRESPMSG = $multipurchaseblockmessage;
			}else
				$success = callcurlfunction('https://secure.authorize.net/gateway/transact.dll', $parmList, $res, '', $vsRESPMSG, TRUE);
			if($success){
				$varString = split('\|', $res);
				$vsRESULT=$varString[0];
				$vsERRCODE=$varString[2];
				$vsRESPMSG=$varString[3];
				if($vsERRCODE != "1" && $demomode) $vsRESPMSG = $vsERRCODE . " - " . $vsRESPMSG;
				$vsAUTHCODE=$varString[4];
				$vsAVSADDR=$varString[5];
				$vsTRANSID=$varString[6];
				$vsCVV2=$varString[38];
				if((int)$vsRESULT==1){
					$vsRESULT="0"; // Keep in sync with Payflow Pro
					do_stock_management($ordID);
					mysql_query("UPDATE cart SET cartCompleted=1 WHERE cartOrderID='" . $ordID . "'") or print(mysql_error());
					mysql_query("UPDATE orders SET ordStatus=3,ordAVS='".mysql_escape_string($vsAVSADDR)."',ordCVV='".mysql_escape_string($vsCVV2)."',ordAuthNumber='" . mysql_escape_string($vsAUTHCODE) . "',ordTransID='" . mysql_escape_string($vsTRANSID) . "' WHERE ordID='" . $ordID . "'") or print(mysql_error());
				}elseif((int)$vsRESULT==27)
					$gobackplaces=(@$_POST['vrshippingoptions']=="1" ? 3 : 2);
			}
		}else{
			$vsRESULT='0';
			$vsRESPMSG='This transaction has been approved.';
			$pos = strpos($vsAUTHCODE, '-');
			if (! ($pos === false))
				$vsAUTHCODE = substr($vsAUTHCODE, $pos + 1);
		}
	}elseif(@$_POST['method']=='18'){ // PayPal Pro
		@set_time_limit(120);
		$data2arr = split("&",$data2);
		$password=urldecode(@$data2arr[0]);
		$isthreetoken=(trim(urldecode(@$data2arr[2]))=='1');
		$signature=''; $sslcertpath='';
		if($isthreetoken) $signature=urldecode(@$data2arr[1]); else $sslcertpath=urldecode(@$data2arr[1]);
		$sSQL = "SELECT ordID,ordName,ordCity,ordState,ordCountry,ordPhone,ordHandling,ordZip,ordEmail,ordShipping,ordStateTax,ordCountryTax,ordTotal,ordDiscount,ordAddress,ordAddress2,ordIP,ordAuthNumber,ordShipName,ordShipAddress,ordShipAddress2,ordShipCity,ordShipState,ordShipCountry,ordShipZip FROM orders WHERE ordID='" . $ordID . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		$rs = mysql_fetch_array($result);
		mysql_free_result($result);
		$sSQL = "SELECT countryCode FROM countries WHERE countryName='" . mysql_escape_string($rs["ordCountry"]) . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs2 = mysql_fetch_array($result))
			$countryCode = $rs2["countryCode"];
		mysql_free_result($result);
		$sSQL = "SELECT countryCode FROM countries WHERE countryName='" . mysql_escape_string($rs["ordShipCountry"]) . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs2 = mysql_fetch_array($result))
			$shipCountryCode = $rs2["countryCode"];
		else
			$shipCountryCode = '';
		mysql_free_result($result);
		if($countryCode == 'US' || $countryCode == 'CA'){
			$sSQL = "SELECT stateAbbrev FROM states WHERE stateName='" . mysql_escape_string($rs["ordState"]) . "'";
			$result = mysql_query($sSQL) or print(mysql_error());
			if($rs2 = mysql_fetch_array($result)) $rs["ordState"]=$rs2["stateAbbrev"];
			mysql_free_result($result);
		}
		if($shipCountryCode == 'US' || $shipCountryCode == 'CA'){
			$sSQL = "SELECT stateAbbrev FROM states WHERE stateName='" . mysql_escape_string($rs["ordShipState"]) . "'";
			$result = mysql_query($sSQL) or print(mysql_error());
			if($rs2 = mysql_fetch_array($result)) $rs["ordShipState"]=$rs2["stateAbbrev"];
			mysql_free_result($result);
		}
		$vsAUTHCODE = trim($rs["ordAuthNumber"]);
		$thename = unstripslashes(trim(@$_POST["cardname"]));
		if(strstr($thename," ")){
			$namearr = split(" ",$thename,2);
			$firstname = $namearr[0];
			$lastname = $namearr[1];
		}else{
			$firstname = '';
			$lastname = $thename;
		}
		$cardnum = preg_replace('/\s+/', '', trim(@$_POST["ACCT"]));
		$cartype = "Visa";
		if(substr($cardnum, 0, 1)=="5")
			$cartype="MasterCard";
		elseif(substr($cardnum, 0, 1)=="6")
			$cartype="Discover";
		elseif(substr($cardnum, 0, 1)=="3")
			$cartype="Amex";
		$sXML = ppsoapheader($data1, $password, $signature) .
			'  <soap:Body><DoDirectPaymentReq xmlns="urn:ebay:api:PayPalAPI">' .
			'    <DoDirectPaymentRequest><Version xmlns="urn:ebay:apis:eBLBaseComponents">1.00</Version>' .
			'      <DoDirectPaymentRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">' .
			'        <PaymentAction>' . ($ppmethod==1?'Authorization':'Sale') . '</PaymentAction>' .
			'        <PaymentDetails>' .
			'          <OrderTotal currencyID="' . $countryCurrency . '">' . number_format(($rs["ordShipping"]+$rs["ordStateTax"]+$rs["ordCountryTax"]+$rs["ordTotal"]+$rs["ordHandling"])-$rs["ordDiscount"],2,'.','') . '</OrderTotal>' .
			'          <ButtonSource>ecommercetemplates_Cart_DP_US</ButtonSource>';
		if(trim($rs["ordShipAddress"]) != '')
			$sXML .= '<ShipToAddress><Name>' . vrxmlencode($rs["ordShipName"]) . '</Name><Street1>' . vrxmlencode($rs["ordShipAddress"]) . '</Street1><Street2>' . vrxmlencode($rs["ordShipAddress2"]) . '</Street2><CityName>' . $rs["ordShipCity"] . '</CityName><StateOrProvince>' . $rs["ordShipState"] . '</StateOrProvince><Country>' . $shipCountryCode . '</Country><PostalCode>' . $rs["ordShipZip"] . '</PostalCode></ShipToAddress>';
		else
			$sXML .= '<ShipToAddress><Name>' . vrxmlencode($rs["ordName"]) . '</Name><Street1>' . vrxmlencode($rs["ordAddress"]) . '</Street1><Street2>' . vrxmlencode($rs["ordAddress2"]) . '</Street2><CityName>' . $rs["ordCity"] . '</CityName><StateOrProvince>' . $rs["ordState"] . '</StateOrProvince><Country>' . $countryCode . '</Country><PostalCode>' . $rs["ordZip"] . '</PostalCode></ShipToAddress>';
		$sXML .= '   </PaymentDetails>' .
			'        <CreditCard>' .
			'          <CreditCardType>' . $cartype . '</CreditCardType><CreditCardNumber>' . vrxmlencode($cardnum) . '</CreditCardNumber>' .
			'          <ExpMonth>' . @$_POST["EXMON"] . '</ExpMonth><ExpYear>' . @$_POST["EXYEAR"] . '</ExpYear>' .
			'          <CardOwner>' .
			'            <Payer>' . vrxmlencode($rs["ordEmail"]) . '</Payer>' .
			'            <PayerName><FirstName>' . vrxmlencode($firstname) . '</FirstName><LastName>' . vrxmlencode($lastname) . '</LastName></PayerName>' .
			'            <PayerCountry>' . $countryCode . '</PayerCountry>' .
			'            <Address><Street1>' . vrxmlencode($rs["ordAddress"]) . '</Street1><Street2>' . vrxmlencode($rs["ordAddress2"]) . '</Street2><CityName>' . $rs["ordCity"] . '</CityName><StateOrProvince>' . $rs["ordState"] . '</StateOrProvince><Country>' . $countryCode . '</Country><PostalCode>' . $rs["ordZip"] . '</PostalCode></Address>' .
			'          </CardOwner>' .
			'          <CVV2>' . trim(@$_POST["CVV2"]) . '</CVV2>' .
			'        </CreditCard>' .
			'        <IPAddress>' . trim($rs["ordIP"]) . '</IPAddress><MerchantSessionId>' . $rs["ordID"] . '</MerchantSessionId>' .
			'      </DoDirectPaymentRequestDetails>' .
			'    </DoDirectPaymentRequest></DoDirectPaymentReq></soap:Body></soap:Envelope>';
		if($demomode) $sandbox = '.sandbox'; else $sandbox = '';
		$vsRESULT='-1';
		if($vsAUTHCODE==''){
			if($blockuser){
				$success=FALSE;
				$vsRESPMSG = $multipurchaseblockmessage;
			}else
				$success = callcurlfunction('https://api-aa' . ($sandbox=='' && $isthreetoken ? '-3t' : '') . $sandbox . '.paypal.com/2.0/', $sXML, $res, $sslcertpath, $vsRESPMSG, TRUE);
			if($success){
				$xmlDoc = new vrXMLDoc($res);
				$vsAUTHCODE='';$vsERRCODE='';$vsRESPMSG='';$vsAVSADDR='';$vsTRANSID='';$vsCVV2='';
				$nodeList = $xmlDoc->nodeList->childNodes[0];
				for($i = 0; $i < $nodeList->length; $i++){
					if($nodeList->nodeName[$i]=='SOAP-ENV:Body'){
						$e = $nodeList->childNodes[$i];
						for($j = 0; $j < $e->length; $j++){
							if($e->nodeName[$j] == 'DoDirectPaymentResponse'){
								$ee = $e->childNodes[$j];
								for($jj = 0; $jj < $ee->length; $jj++){
									if($ee->nodeName[$jj] == 'Ack'){
										if($ee->nodeValue[$jj]=='Success' || $ee->nodeValue[$jj]=='SuccessWithWarning'){
											$vsRESULT=1;
											$vsRESPMSG = 'Success';
										}
									}elseif($ee->nodeName[$jj] == 'TransactionID'){
										$vsAUTHCODE=$ee->nodeValue[$jj];
									}elseif($ee->nodeName[$jj] == 'AVSCode'){
										$vsAVSADDR=$ee->nodeValue[$jj];
									}elseif($ee->nodeName[$jj] == 'CVV2Code'){
										$vsCVV2=$ee->nodeValue[$jj];
									}elseif($ee->nodeName[$jj] == 'Errors'){
										$themsg='';
										$thecode='';
										$iswarning=FALSE;
										$ff = $ee->childNodes[$jj];
										for($kk = 0; $kk < $ff->length; $kk++){
											if($ff->nodeName[$kk] == 'ShortMessage'){
												//$vsRESPMSG=$ff->nodeValue[$kk].'<br>'.$vsRESPMSG;
											}elseif($ff->nodeName[$kk] == 'LongMessage'){
												$themsg=$ff->nodeValue[$kk];
											}elseif($ff->nodeName[$kk] == 'ErrorCode'){
												$thecode=$ff->nodeValue[$kk];
											}elseif($ff->nodeName[$kk] == 'SeverityCode'){
												$iswarning=($ff->nodeValue[$kk]=='Warning');
											}
										}
										if(! $iswarning){
											$vsRESPMSG=$themsg;
											$vsERRCODE=$thecode;
										}
									}
								}
							}
						}
					}
				}
				if((int)$vsRESULT==1){
					$vsRESULT='0'; // Keep in sync with Payflow Pro
					do_stock_management($ordID);
					mysql_query("UPDATE cart SET cartCompleted=1 WHERE cartOrderID='" . $ordID . "'") or print(mysql_error());
					mysql_query("UPDATE orders SET ordStatus=3,ordAVS='".mysql_escape_string($vsAVSADDR)."',ordCVV='".mysql_escape_string($vsCVV2)."',ordAuthNumber='" . mysql_escape_string($vsAUTHCODE) . "' WHERE ordID='" . $ordID . "'") or print(mysql_error());
				}elseif($vsERRCODE != ''){
					$vsERRCODE = (int)$vsERRCODE;
					if($vsERRCODE==10505 || ($vsERRCODE>=10701 && $vsERRCODE<=10751))
						$gobackplaces=(@$_POST['vrshippingoptions']=='1' ? 3 : 2);
				}
			}
		}else{
			$vsRESULT="0";
			$vsRESPMSG="This transaction has been approved.";
			$pos = strpos($vsAUTHCODE, "-");
			if (! ($pos === false))
				$vsAUTHCODE = substr($vsAUTHCODE, $pos + 1);
		}
	}
?>	<br />
	<form method="post" action="thanks.php" name="checkoutform">
	<input type="hidden" name="xxpreauth" value="<?php print $ordID?>" />
	<input type="hidden" name="xxpreauthmethod" value="<?php print @$_POST['method']?>" />
	<input type="hidden" name="thesessionid" value="<?php print $thesessionid?>" />
            <table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
<?php	if($vsRESULT=="0"){ ?>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="center" colspan="2"><strong><?php print $xxTnxOrd?></strong></td>
			  </tr>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong><?php print $xxTrnRes?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" width="50%"><strong><?php print $vsRESPMSG?></strong></td>
			  </tr>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong><?php print $xxOrdNum?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" width="50%"><strong><?php print $ordID?></strong></td>
			  </tr>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong><?php print $xxAutCod?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" width="50%"><strong><?php print $vsAUTHCODE?></strong></td>
			  </tr>
			  <tr height="30">
				<td class="cobll" bgcolor="#FFFFFF" colspan="2">
				  <table width="100%" cellspacing="0" cellpadding="0" border="0">
				    <tr>
					  <td width="16" height="26" align="right" valign="bottom">&nbsp;</td>
					  <td class="cobll" bgcolor="#FFFFFF" width="100%" align="center">&nbsp;<br />
					  <input type="submit" value="Click to Confirm Order and View Receipt" /><br />&nbsp;
					  </td>
					  <td width="16" height="26" align="right" valign="bottom"><img src="images/tablebr.gif" alt="" /></td>
					</tr>
				  </table>
				</td>
			  </tr>
<?php		if(@$forcesubmit==TRUE){
				if(@$forcesubmittimeout=='') $forcesubmittimeout=5000;
				print '<script language="javascript" type="text/javascript">setTimeout("document.checkoutform.submit()",'.$forcesubmittimeout.');</script>\r\n';
			}
		}else{ ?>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="center" colspan="2"><strong><?php print $xxSorTrn?></strong></td>
			  </tr>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="right" width="50%"><strong><?php print $xxTrnRes?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" width="50%"><strong><?php print (@$vsERRCODE != '' ? '(' . $vsERRCODE . ') ' : '') . $vsRESPMSG?></strong></td>
			  </tr>
			  <tr height="30">
				<td class="cobll" bgcolor="#FFFFFF" colspan="2">
				  <table width="100%" cellspacing="0" cellpadding="0" border="0">
				    <tr>
					  <td width="16" height="26" align="right" valign="bottom">&nbsp;</td>
					  <td class="cobll" bgcolor="#FFFFFF" width="100%" align="center">&nbsp;<br />
					  <input type="button" value="<?php print $xxGoBack?>" onclick="javascript:history.go(-<?php print $gobackplaces?>)" /><br />&nbsp;
					  </td>
					  <td width="16" height="26" align="right" valign="bottom"><img src="images/tablebr.gif" alt="" /></td>
					</tr>
				  </table>
				</td>
			  </tr>
<?php	} ?>
			</table>
	</form>
<?php
}elseif(@$_GET["token"] == '' && $checkoutmode != 'paypalexpress1' && $cartisincluded != TRUE){
	if(session_id()=='') print 'The PHP session has not been started. This can cause problems with the shopping cart function. For help please go to <a href="http://www.ecommercetemplates.com/support/">http://www.ecommercetemplates.com/support/</a>';
	$gshipmethods=array();
	function writeuniquegoogleshipmethod($theshipmethod){
		global $countryCurrency,$sXML,$googledefaultshipping,$gshipmethods;
		if(@$googledefaultshipping=='') $googledefaultshipping='999.99';
		$gotshipmethod=FALSE;
		if(! in_array($theshipmethod,$gshipmethods)){
			array_push($gshipmethods, $theshipmethod);
			$sXML .= '<merchant-calculated-shipping name="' . $theshipmethod . '"><price currency="' . $countryCurrency . '">' . $googledefaultshipping . '</price></merchant-calculated-shipping>';
		}
	}
	function generatemerchantcalcshiptypes($theshiptype){
		global $countryCurrency,$sXML,$xxShipHa,$somethingToShip,$googledefaultshipping,$splitUSZones,$gshipmethods;
		if($theshiptype==1 || ! $somethingToShip){
			writeuniquegoogleshipmethod(xmlencodecharref($xxShipHa));
		}elseif($theshiptype==2 || $theshiptype==5){
			for($index3=1; $index3<=5; $index3++){
				$sSQL = "SELECT DISTINCT pzMethodName" . $index3 . " FROM postalzones WHERE pzName<>'' AND pzMethodName" . $index3 . "<>''";
				if(! $splitUSZones) $sSQL .= ' AND pzID < 100';
				$result = mysql_query($sSQL) or print(mysql_error());
				while($rs = mysql_fetch_assoc($result)){
					writeuniquegoogleshipmethod(trim(xmlencodecharref($rs['pzMethodName' . $index3])));
				}
			}
		}elseif($theshiptype==3 || $theshiptype==4 || $theshiptype==6 || $theshiptype==7){
			if($theshiptype==3) $startid=0;
			if($theshiptype==4) $startid=1;
			if($theshiptype==6) $startid=2;
			if($theshiptype==7) $startid=3;
			$sSQL = "SELECT DISTINCT uspsShowAs,uspsFSA FROM uspsmethods WHERE (uspsID>" . ($startid*100) . " AND uspsID<" . (($startid+1)*100) . ") AND uspsUseMethod=1 ORDER BY uspsFSA DESC,uspsShowAs";
			$result = mysql_query($sSQL) or print(mysql_error());
			while($rs = mysql_fetch_assoc($result)){
				writeuniquegoogleshipmethod(xmlencodecharref($rs['uspsShowAs']));
			}
		}
	}
	function writegoogleparams($data1, $data2, $demomode){
		global $shipType,$adminIntShipping,$willpickuptext,$willpickupcost,$countryCurrency,$storeurl,$googlelineitems,$thesessionid,$sXML,$gcallbackpath;
		$sSQL = "SELECT cpnID FROM coupons WHERE cpnIsCoupon=1 AND cpnNumAvail>0 AND cpnEndDate>='" . date('Y-m-d',time()) ."'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if(mysql_num_rows($result)==0) $acoupondefined='false'; else $acoupondefined='true';
		$sXML = '<?xml version="1.0" encoding="UTF-8"?><checkout-shopping-cart xmlns="http://checkout.google.com/schema/2"><shopping-cart>';
		$sXML .= '<items>' . $googlelineitems . '</items>';
		$sXML .= '<merchant-private-data><privateitems><sessionid>' . (@$_SESSION['clientID'] != '' ? 'cid' . $_SESSION['clientID'] : 'sid' . $thesessionid) . '</sessionid><partner>' . xmlencodecharref(trim(@$_COOKIE['PARTNER'])) . '</partner></privateitems></merchant-private-data></shopping-cart>';
		$sXML .= '<checkout-flow-support><merchant-checkout-flow-support><platform-id>236638029623651</platform-id>';
		$sXML .= '<edit-cart-url>' . $storeurl . 'cart.php</edit-cart-url><continue-shopping-url>' . $storeurl . 'categories.php</continue-shopping-url>';
		$sXML .= '<shipping-methods>';
		generatemerchantcalcshiptypes($shipType);
		if($adminIntShipping != 0 && $adminIntShipping != $shipType) generatemerchantcalcshiptypes($adminIntShipping);
		if(@$willpickuptext != ''){
			if(@$willpickupcost=='') $willpickupcost=0;
			$sXML .= '<merchant-calculated-shipping name="' . xmlencodecharref($willpickuptext) . '"><price currency="' . $countryCurrency . '">' . $willpickupcost . '</price></merchant-calculated-shipping>';
		}
		$sXML .= '</shipping-methods>';
		$sXML .= '<request-buyer-phone-number>true</request-buyer-phone-number><tax-tables merchant-calculated="true"><default-tax-table><tax-rules></tax-rules></default-tax-table></tax-tables>';
		$sXML .= '<merchant-calculations><merchant-calculations-url>' . $gcallbackpath . '</merchant-calculations-url><accept-merchant-coupons>' . $acoupondefined . '</accept-merchant-coupons><accept-gift-certificates>false</accept-gift-certificates></merchant-calculations></merchant-checkout-flow-support></checkout-flow-support>';
		$sXML .= '</checkout-shopping-cart>';
		// print str_replace("<","<br />&lt;",str_replace("</","&lt;/",$sXML)) . "<br />\n";
		$thecart = base64_encode($sXML);
		$thesignature = base64_encode(CalcHmacSha1($sXML,$data2));
		$theurl = 'https://' . ($demomode ? 'sandbox' : 'checkout') . '.google.com' . ($demomode ? '/checkout' : '') . '/cws/v2/Merchant/' . $data1 . '/checkout'; // . '/diagnose';
		writehiddenvar('cart', $thecart);
		writehiddenvar('signature', $thesignature);
		return($theurl);
	}
	$requiressl = FALSE;
	if(@$pathtossl == ''){
		$sSQL = "SELECT payProvID FROM payprovider WHERE payProvEnabled=1 AND (payProvID IN (7,10,12,13,18) OR (payProvID=16 AND payProvData2='1'))"; // All the ones that require SSL
		$result = mysql_query($sSQL) or print(mysql_error());
		if(mysql_num_rows($result) > 0) $requiressl = TRUE;
		mysql_free_result($result);
	}
	if(@$googlecallbackscript=='') $googlecallbackscript='vsadmin/gcallback.php';
	if($requiressl || @$pathtossl != ''){
		if(@$pathtossl != ''){
			if(substr($pathtossl,-1) != '/') $pathtossl .= '/';
			$cartpath = $pathtossl . 'cart.php';
			$gcallbackpath = $pathtossl . $googlecallbackscript;
		}else{
			$cartpath = str_replace('http:','https:',$storeurl) . 'cart.php';
			$gcallbackpath = str_replace('http:','https:',$storeurl) . $googlecallbackscript;
		}
	}else{
		$cartpath='cart.php';
		$gcallbackpath= $storeurl . $googlecallbackscript;
	}
	$loginerror='';
	if(@$_GET['mode']=='logout'){
		$_SESSION['clientID']=NULL; unset($_SESSION['clientID']);
		$_SESSION['clientUser']=NULL; unset($_SESSION['clientUser']);
		$_SESSION['clientActions']=NULL; unset($_SESSION['clientActions']);
		$_SESSION['clientLoginLevel']=NULL; unset($_SESSION['clientLoginLevel']);
		$_SESSION['clientPercentDiscount']=NULL; unset($_SESSION['clientPercentDiscount']);
		$xxSryEmp=$xxLOSuc;
		print '<script src="vsadmin/savecookie.php?WRITECLL=x&WRITECLP=&permanent=Y"></script>';
	}
	$loginsuccess=FALSE;
	if($checkoutmode=='dologin' || ($checkoutmode=='donewaccount' && @$allowclientregistration==TRUE)){
		$loginsuccess=TRUE;
		$clientEmail = trim(@$_POST['email']);
		$clientPW = trim(str_replace("'",'',@$_POST['pass']));
		if($checkoutmode=='donewaccount'){
			$sSQL = "SELECT clID,clUserName,clActions,clLoginLevel,clPercentDiscount FROM customerlogin WHERE clEmail='" . mysql_escape_string($clientEmail) . "'";
			$result = mysql_query($sSQL) or print(mysql_error());
			if(mysql_num_rows($result) > 0){
				$loginsuccess=FALSE;
				$loginerror=$xxEmExi;
			}
			if($loginsuccess){
				$sSQL = "INSERT INTO customerlogin (clUserName,clEmail,clPw,clDateCreated) VALUES ('" . mysql_escape_string(@$_POST['name']) . "','" . mysql_escape_string($clientEmail) . "','" . mysql_escape_string($clientPW) . "','" . date("Y-m-d", time() + ($dateadjust*60*60)) . "')";
				mysql_query($sSQL) or print(mysql_error());
				if(@$_POST['allowemail']=='ON')
					mysql_query("INSERT INTO mailinglist (email) VALUES ('" . mysql_escape_string(strtolower($clientEmail)) . "')");
			}
		}
		if($loginsuccess){
			$sSQL = "SELECT clID,clUserName,clActions,clLoginLevel,clPercentDiscount FROM customerlogin WHERE (clEmail<>'' AND clEmail='" . mysql_escape_string($clientEmail) . "' AND clPW='" . mysql_escape_string($clientPW) . "') OR (clEmail='' AND clUserName='" . mysql_escape_string($clientEmail) . "' AND clPW='" . mysql_escape_string($clientPW) . "')";
			$result = mysql_query($sSQL) or print(mysql_error());
			$loginsuccess=FALSE;
			if($rs = mysql_fetch_assoc($result)){
				$_SESSION['clientID']=$rs['clID'];
				$_SESSION['clientUser']=$rs['clUserName'];
				$_SESSION['clientActions']=$rs['clActions'];
				$_SESSION['clientLoginLevel']=$rs['clLoginLevel'];
				$_SESSION['clientPercentDiscount']=(100.0-(double)$rs['clPercentDiscount'])/100.0;
				get_wholesaleprice_sql();
				print '<script src="vsadmin/savecookie.php?WRITECLL=' . $clientEmail . '&WRITECLP=' . $clientPW;
				if(@$_POST['cook']=='ON') print '&permanent=Y';
				print '"></script>';
				$loginsuccess=TRUE;
			}else
				$loginerror=$xxNoLogD;
		}
		if($loginsuccess){
			$sSQL = "SELECT cartID,cartProdID FROM cart WHERE cartCompleted=0 AND cartClientID=" . $_SESSION['clientID'];
			$result = mysql_query($sSQL) or print(mysql_error());
			while($cartarr = mysql_fetch_assoc($result)){
				$hasoptions=TRUE;
				$sSQL = "SELECT cartID,cartQuantity FROM cart WHERE cartClientID=0 AND cartCompleted=0 AND cartSessionID='" . $thesessionid . "' AND cartProdID='" . mysql_escape_string($cartarr['cartProdID']) . "'";
				$result2 = mysql_query($sSQL) or print(mysql_error());
				if($rs = mysql_fetch_array($result2)){ $thecartid=$rs['cartID']; $thequant=$rs['cartQuantity']; } else $thecartid='';
				if($thecartid != ''){ // check options
					$optarr1cnt=0; $optarr2cnt=0;
					$sSQL = "SELECT coOptID,coCartOption FROM cartoptions WHERE coCartID=" . $cartarr['cartID'];
					$result3 = mysql_query($sSQL) or print(mysql_error());
					while($rs2 = mysql_fetch_assoc($result3))
						$optarr1[$optarr1cnt++]=$rs2;
					$sSQL = "SELECT coOptID,coCartOption FROM cartoptions WHERE coCartID=" . $thecartid;
					while($rs2 = mysql_fetch_assoc($result3))
						$optarr2[$optarr2cnt++]=$rs2;
					if($optarr1cnt != $optarr2cnt) $hasoptions=FALSE;
					if($optarr1cnt > 0 && $optarr2cnt > 0){
						if($hasoptions){
							for($index2=0; $index2 < $optarr1cnt; $index2++){
								$hasthisoption=FALSE;
								for($index3=0; $index3 < $optarr2cnt; $index3++){
									if($optarr1[$index2]['coOptID']==$optarr2[$index3]['coOptID'] && $optarr1[$index2]['coCartOption']==$optarr2[$index3]['coCartOption']) $hasthisoption=TRUE;
								}
								if(! $hasthisoption) $hasoptions=FALSE;
							}
						}
					}
				}
				if($thecartid != '' && $hasoptions){
					$sSQL = "UPDATE cart SET cartQuantity=cartQuantity+" . $thequant . " WHERE cartID=" . $cartarr['cartID'];
					mysql_query($sSQL) or print(mysql_error());
					$sSQL = "DELETE FROM cart WHERE cartID=" . $thecartid;
					mysql_query($sSQL) or print(mysql_error());
				}
			}
			$sSQL = "UPDATE cart SET cartClientID=" . $_SESSION['clientID'] . " WHERE cartClientID=0 AND cartCompleted=0 AND cartSessionID='" . $thesessionid . "'";
			mysql_query($sSQL) or print(mysql_error());
			$sSQL = "SELECT cartID,cartProdID,".$WSP."pPrice FROM cart INNER JOIN products ON cart.cartProdId=products.pID WHERE cartClientID=" . $_SESSION['clientID'] . " AND cartCompleted=0";
			$result = mysql_query($sSQL) or print(mysql_error());
			while($rs = mysql_fetch_array($result))
				checkpricebreaks($rs['cartProdID'],$rs['pPrice']); // recalculate wholesale price plus quant discounts
			eval('$theref = @$clientloginref' . @$_SESSION['clientLoginLevel'] . ';');
			if($theref != '') $clientloginref=$theref;
			if(@$clientloginref=='referer' || @$clientloginref == '')
				if(trim(@$_POST['refurl'])!='') $refURL = trim(@$_POST['refurl']); else $refURL = 'cart.php';
			else
				$refURL = $clientloginref;
			print '<meta http-equiv="Refresh" content="1; URL=' . $refURL . '">';
		}
	}
	$addextrarows=0;
	$wantstateselector=FALSE;
	$wantcountryselector=FALSE;
	$wantzipselector=FALSE;
	if(@$estimateshipping==TRUE){
		$addextrarows=1;
		if($shipType==2 || $shipType==5){ // weight / price based
			$wantcountryselector=TRUE;
			if($splitUSZones){
				$addextrarows=3;
				$wantstateselector=TRUE;
			}else
				$addextrarows=2;
		}elseif($shipType==3 || $shipType==4 || $shipType==6 || $shipType==7){
			$addextrarows=3;
			$wantzipselector=TRUE;
			$wantcountryselector=TRUE;
		}
		$shiphomecountry=TRUE;
		if($cartisincluded != TRUE){
			if(@$_POST["state"] != ''){
				$shipstate = unstripslashes(@$_POST["state"]);
				$_SESSION["state"] = unstripslashes(@$_POST["state"]);
			}elseif(@$_SESSION["state"] != '')
				$shipstate = $_SESSION["state"];
			else
				$shipstate = @$defaultshipstate;
			if(@$_POST["zip"] != ''){
				$destZip = trim(unstripslashes(@$_POST["zip"]));
				$_SESSION["zip"] = trim(unstripslashes(@$_POST["zip"]));
			}elseif(@$_SESSION["zip"] != '')
				$destZip = $_SESSION["zip"];
			elseif(! (@$nodefaultzip==TRUE))
				$destZip = $origZip;
			else
				$destZip = '';
			if(@$_POST["country"] != ''){
				$shipcountry = unstripslashes(@$_POST["country"]);
				$_SESSION["country"] = unstripslashes(@$_POST["country"]);
				if(trim(@$_POST["state"])=='') $shipstate='';
			}elseif(@$_SESSION["country"] != '')
				$shipcountry = $_SESSION["country"];
			else{
				$shipCountryCode = $origCountryCode;
				$shipcountry = $origCountry;
			}
		}
		$sSQL = "SELECT countryID,countryTax,countryCode,countryFreeShip,countryOrder FROM countries WHERE countryName='" . mysql_escape_string($shipcountry) . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_array($result)){
			if(trim(@$_SESSION["clientUser"]) != '' && ((int)$_SESSION["clientActions"] & 2)==2) $countryTaxRate=0; else $countryTaxRate = $rs["countryTax"];
			$shipCountryID = $rs["countryID"];
			$shipCountryCode = $rs["countryCode"];
			$freeshipapplies = ($rs["countryFreeShip"]==1);
			$shiphomecountry = ($rs["countryOrder"]==2);
		}
		mysql_free_result($result);
		if(@$_SESSION["xsshipping"] == '') initshippingmethods();
	}
	if(@$showtaxinclusive) $addextrarows++;
	$alldata='';
	$sSQL = "SELECT cartID,cartProdID,cartProdName,cartProdPrice,cartQuantity,pWeight,pShipping,pShipping2,pExemptions,pSection,pDims,pTax,".getlangid('pDescription',2)." FROM cart INNER JOIN products ON cart.cartProdID=products.pID LEFT OUTER JOIN sections ON products.pSection=sections.sectionID WHERE cartCompleted=0 AND " . getsessionsql();
	$result = mysql_query($sSQL) or print(mysql_error());
?>	<br />
<script language="javascript" type="text/javascript">
<!--
var checkedfullname=false;
function checknewaccount(){
frm=document.forms.checkoutform;
if(frm.name.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxName?>\".");
	frm.name.focus();
	return (false);
}
gotspace=false;
var checkStr = frm.name.value;
for (i = 0; i < checkStr.length; i++){
	if(checkStr.charAt(i)==" ")
		gotspace=true;
}
if(!checkedfullname && !gotspace){
	alert("<?php print $xxFulNam?> \"<?php print $xxName?>\".");
	frm.name.focus();
	checkedfullname=true;
	return (false);
}
if(frm.email.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxEmail?>\".");
	frm.email.focus();
	return (false);
}
validemail=0;
var checkStr = frm.email.value;
for (i = 0; i < checkStr.length; i++){
	if(checkStr.charAt(i)=="@")
		validemail |= 1;
	if(checkStr.charAt(i)==".")
		validemail |= 2;
}
if(validemail != 3){
	alert("<?php print $xxValEm?>");
	frm.email.focus();
	return (false);
}
if(frm.pass.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxPwd?>\".");
	frm.pass.focus();
	return (false);
}
document.forms.checkoutform.mode.value='donewaccount';
document.forms.checkoutform.action='cart.php';
return true;
}
function doupdate(){
	document.forms.checkoutform.mode.value='update';
	document.forms.checkoutform.action='cart.php';
	document.forms.checkoutform.onsubmit='';
	document.forms.checkoutform.submit();
}
//--></script>
	<form method="post" name="checkoutform" action="<?php print $cartpath?>"<?php if(mysql_num_rows($result) > 0) print ' onsubmit="return changechecker(this)"'?>>
	<input type="hidden" name="mode" value="checkout" />
	<input type="hidden" name="sessionid" value="<?php print session_id();?>" />
	<input type="hidden" name="PARTNER" value="<?php print strip_tags(trim(@$_COOKIE['PARTNER'])) ?>" />
            <table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
<?php
	if(@$enableclientlogin==TRUE || @$forceclientlogin==TRUE){
		if((@$_GET['mode']=='newaccount' && @$allowclientregistration==TRUE) || ($checkoutmode=='donewaccount' && @$loginerror != '')){ ?>
			  <tr> 
                <td class="cobhl" bgcolor="#EBEBEB" align="center" height="26" colspan="6"><strong><?php print (@$loginerror != '' ? '<font color="#FF0000">' . $loginerror . '</font>' : $xxNewAcc)?></strong></td>
			  </tr>
			  <tr>
				<td class="cobhl" bgcolor="#EBEBEB" align="right" height="26"><font color="#FF0000">*</font><strong><?php print $xxName?>: </strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" height="26"><input type="text" name="name" size="31" value="<?php print htmlspecialchars(@$_POST['name'])?>" /></td>
<?php		if(@$nomailinglist==TRUE){ ?>
				<td class="cobhl" bgcolor="#EBEBEB" colspan="4">&nbsp;</td>
<?php		}else{ ?>
                <td class="cobhl" bgcolor="#EBEBEB" align="right" height="26"><input type="checkbox" name="allowemail" value="ON"<?php if(@$allowemaildefaulton==TRUE || @$_POST['allowemail']=='ON') print ' checked'?>></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" height="26" colspan="3"><strong><?php print $xxAlPrEm?></strong><br /><font size="1"><?php print $xxNevDiv?></font></td>
<?php		} ?>
			  </tr>
			  <tr>
				<td class="cobhl" bgcolor="#EBEBEB" align="right" height="26"><font color="#FF0000">*</font><strong><?php print $xxEmail?>: </strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" height="26"><input type="text" name="email" size="31" value="<?php print htmlspecialchars(@$_POST['email'])?>" /></td>
                <td class="cobhl" bgcolor="#EBEBEB" align="right" height="26"><font color="#FF0000">*</font><strong><?php print $xxPwd?>: </strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" height="26" colspan="3"><input type="password" name="pass" size="20" value="<?php print htmlspecialchars(@$_POST['pass'])?>" /></td>
			  </tr>
			  <tr>
				<td class="cobll" bgcolor="#FFFFFF" align="center" height="26" colspan="6"><input type="submit" value="<?php print $xxCrNwAc?>" onclick="return checknewaccount();"></td>
			  </tr>
<?php	}elseif(@$_GET['mode'] != 'login' && @$loginerror==''){
			if(@$_SESSION['clientUser'] != ''){ ?>
			  <tr height="30">
				<td class="cobll" bgcolor="#FFFFFF" colspan="6" align="center"><?php print $xxWelcom . ' ' . $_SESSION['clientUser'] . '. ' . $xxTLOP?> <a href="cart.php?mode=logout"><strong><?php print $xxClkHere?></strong></a>.</td>
			  </tr>
<?php		}elseif(@$noclientloginprompt!=TRUE){ ?>
			  <tr height="30">
				<td class="cobll" bgcolor="#FFFFFF" colspan="6" align="center"><?php print $xxNotLI . ' ' . $xxTLIP?> <a href="cart.php?mode=login"><strong><?php print $xxClkHere?></strong></a><?php if(@$allowclientregistration==TRUE) print ' ' . $xxOr . ' <a href="cart.php?mode=newaccount"><strong>' . $xxClkHere . '</strong></a> ' . $xxToCrAc?>.</td>
			  </tr>
<?php		}
		}else{
			writehiddenvar('refurl',@$_REQUEST['refurl']); ?>
			  <tr>
                <td class="cobhl" bgcolor="#EBEBEB" align="center" height="26" colspan="6"><strong><?php print (@$loginerror != '' ? '<font color="#FF0000">' . $loginerror . '</font>' : $xxLiDets)?></strong></td>
			  </tr>
			  <tr>
                <td class="cobhl" bgcolor="#EBEBEB" align="right" height="26"><strong><?php print $xxEmail?>: </strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" height="26" colspan="5"><input type="text" name="email" size="31" value="<?php print htmlspecialchars(@$_POST['email'])?>" /> 
				<input type="checkbox" name="cook" value="ON" /> <?php print $xxRemLog?></td>
			  </tr>
			  <tr>
                <td class="cobhl" bgcolor="#EBEBEB" align="right" height="26"><strong><?php print $xxPwd?>: </strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" height="26" colspan="5"><input type="password" name="pass" size="20" value="<?php print htmlspecialchars(@$_POST['pass'])?>" /> <input type="submit" value="<?php print $xxSubmt?>" onclick="document.forms.checkoutform.action='cart.php';document.forms.checkoutform.mode.value='dologin';"><?php if(@$allowclientregistration==TRUE) print '&nbsp;&nbsp;<input type="button" value="'.$xxNewAcc.'" onclick="document.location=\'cart.php?mode=newaccount\'">'?>&nbsp;&nbsp;<input type="button" value="<?php print $xxForPas?>" onclick="document.location='<?php if(@$customeraccounturl!='') print $customeraccounturl; else print 'clientlogin.php'?>?mode=lostpassword'"></td>
			  </tr>
<?php	}
	}
	if($loginsuccess){ ?>
              <tr>
			    <td class="cobll" bgcolor="#FFFFFF" colspan="6" align="center">
				  <p>&nbsp;</p><p><?php print $xxLISuc?></p><p>&nbsp;</p><p><a href="cart.php"><strong><?php print $xxPlWtFw?></strong></a></p><p>&nbsp;</p>
				</td>
			  </tr>
<?php
	}elseif(($itemsincart = mysql_num_rows($result)) > 0){
		if(! $isInStock){ ?>
			  <tr height="30">
			    <td class="cobll" bgcolor="#FFFFFF" colspan="6" align="center"><font color="#FF0000"><strong><?php print $xxNoStok?></strong></font></td>
			  </tr>
<?php	} ?>
			  <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" width="15%" align="left"><strong><?php print $xxCODets?></strong></td>
			    <td class="cobhl" bgcolor="#EBEBEB" width="33%" align="left"><strong><?php print $xxCOName?></strong></td>
				<td class="cobhl" bgcolor="#EBEBEB" width="14%" align="center"><strong><?php print $xxCOUPri?></strong></td>
				<td class="cobhl" bgcolor="#EBEBEB" width="14%" align="center"><strong><?php print $xxQuant?></strong></td>
				<td class="cobhl" bgcolor="#EBEBEB" width="14%" align="center"><strong><?php print $xxTotal?></strong></td>
				<td class="cobhl" bgcolor="#EBEBEB" width="10%" align="center"><strong><?php print $xxCOSel?></strong></td>
			  </tr>
<?php	$totaldiscounts = 0;
		$changechecker = '';
		$googlelineitems = '';
		$index = 0;
		while($alldata=mysql_fetch_assoc($result)){
			$index++;
			$changechecker .= 'if(document.checkoutform.quant' . $alldata["cartID"] . ".value!=" . $alldata["cartQuantity"] . ") dowarning=true;\n";
			$theoptions = '';
			$theoptionspricediff = 0;
			$sSQL = "SELECT coOptGroup,coCartOption,coPriceDiff,coWeightDiff FROM cartoptions WHERE coCartID=" . $alldata["cartID"] . " ORDER BY coID";
			$opts = mysql_query($sSQL) or print(mysql_error());
			$optPriceDiff=0;
			while($rs=mysql_fetch_assoc($opts)){
				$theoptionspricediff += $rs["coPriceDiff"];
				$alldata["pWeight"] += (double)$rs["coWeightDiff"];
				$theoptions .= '<tr height="25">';
				$theoptions .= '<td class="cobhl" bgcolor="#EBEBEB" align="right"><font style="font-size: 10px"><strong>' . $rs["coOptGroup"] . ':</strong></font></td>';
				$theoptions .= '<td class="cobll" bgcolor="#FFFFFF" align="left"><font style="font-size: 10px">&nbsp;- ' . str_replace(array("\r\n","\n"),array("<br />","<br />"),htmlspecialchars($rs['coCartOption'])) . '</font></td>';
				$theoptions .= '<td class="cobll" bgcolor="#FFFFFF" align="right"><font style="font-size: 10px">' . ($rs["coPriceDiff"]==0 || @$hideoptpricediffs==TRUE ? "- " : FormatEuroCurrency($rs["coPriceDiff"])) . '</font></td>';
				$theoptions .= '<td class="cobll" bgcolor="#FFFFFF" align="right">&nbsp;</td>';
				$theoptions .= '<td class="cobll" bgcolor="#FFFFFF" align="right"><font style="font-size: 10px">' . ($rs["coPriceDiff"]==0 || @$hideoptpricediffs==TRUE ? "- " : FormatEuroCurrency($rs["coPriceDiff"]*$alldata["cartQuantity"])) . '</font></td>';
				$theoptions .= '<td class="cobll" bgcolor="#FFFFFF" align="center">&nbsp;</td>';
				$theoptions .= "</tr>\n";
				$totalgoods += ($rs["coPriceDiff"]*(int)$alldata["cartQuantity"]);
			}
			$googlelineitems .= '<item><merchant-private-item-data><product-id>' . xmlencodecharref($alldata['cartProdID']) . '</product-id></merchant-private-item-data><item-name>' . xmlencodecharref(strip_tags($alldata['cartProdName'])) . '</item-name><item-description>' . xmlencodecharref(substr(strip_tags($alldata[getlangid('pDescription',2)]),0,301)) . '</item-description><unit-price currency="' . $countryCurrency . '">' . number_format($alldata['cartProdPrice'] + $theoptionspricediff,2,'.','') . '</unit-price><quantity>' . $alldata['cartQuantity'] . '</quantity></item>';
			mysql_free_result($opts); ?>
              <tr height="30">
			    <td class="cobhl" bgcolor="#EBEBEB" align="left"><strong><?php print $alldata['cartProdID']?></strong></td>
			    <td class="cobll" bgcolor="#FFFFFF" align="left"><?php print $alldata['cartProdName'] ?></td>
				<td class="cobll" bgcolor="#FFFFFF" align="right"><?php print (@$hideoptpricediffs==TRUE ? FormatEuroCurrency($alldata["cartProdPrice"] + $theoptionspricediff) : FormatEuroCurrency($alldata["cartProdPrice"]))?></td>
				<td class="cobll" bgcolor="#FFFFFF" align="center"><input type="text" name="quant<?php print $alldata["cartID"]?>" value="<?php print $alldata["cartQuantity"]?>" size="2" maxlength="5" /></td>
				<td class="cobll" bgcolor="#FFFFFF" align="right"><?php print (@$hideoptpricediffs==TRUE ? FormatEuroCurrency(($alldata["cartProdPrice"] + $theoptionspricediff)*$alldata["cartQuantity"]) : FormatEuroCurrency($alldata["cartProdPrice"]*$alldata["cartQuantity"]))?></td>
				<td class="cobll" bgcolor="#FFFFFF" align="center"><input type="checkbox" name="delet<?php print $alldata["cartID"]?>" /></td>
			  </tr>
<?php		print $theoptions;
			$runTot=$alldata['cartProdPrice'] * (int)$alldata['cartQuantity'];
			$totalquantity += (int)$alldata['cartQuantity'];
			$totalgoods += ($alldata['cartProdPrice']*(int)$alldata['cartQuantity']);
			$alldata['cartProdPrice'] += $theoptionspricediff;
			if(trim(@$_SESSION['clientUser']) != '') $alldata['pExemptions'] = ((int)$alldata['pExemptions'] | (int)$_SESSION['clientActions']);
			if(($shipType==2 || $shipType==3 || $shipType==4 || $shipType==6 || $shipType==7) && (double)$alldata['pWeight']<=0.0)
				$alldata['pExemptions'] = ($alldata['pExemptions'] | 4);
			if(@$perproducttaxrate==TRUE){
				if(is_null($alldata['pTax'])) $alldata['pTax'] = $countryTaxRate;
				if(($alldata['pExemptions'] & 2) != 2) $countryTax += (($alldata['pTax'] * $alldata['cartProdPrice'] * (int)$alldata['cartQuantity']) / 100.0);
			}else{
				if(($alldata['pExemptions'] & 2)==2) $countrytaxfree += $runTot + ($theoptionspricediff * (int)($alldata['cartQuantity']));
			}
			if(($alldata['pExemptions'] & 4)==4) $shipfreegoods += $runTot; else $somethingToShip=TRUE;
			if(@$estimateshipping==TRUE && @$_SESSION['xsshipping'] == '')
				addproducttoshipping($alldata, $index);
		}
		calculatediscounts($totalgoods, false, '');
		if($totaldiscounts > $totalgoods) $totaldiscounts = $totalgoods;
		if($totaldiscounts==0)
			$_SESSION['discounts'] = '';
		else{
			$_SESSION['discounts'] = $totaldiscounts;
			$addextrarows++;
			$glicpnmessage = substr($cpnmessage, 6, -6);
			$googlelineitems .= '<item><merchant-private-item-data><discountflag>true</discountflag></merchant-private-item-data><item-name>' . xmlencodecharref(strip_tags($xxAppDs)) . '</item-name><item-description>' . xmlencodecharref(strip_tags(str_replace('<br />', ' - ', $glicpnmessage))) . '</item-description><unit-price currency="' . $countryCurrency . '">-' . number_format($totaldiscounts,2,'.','') . '</unit-price><quantity>1</quantity></item>';
		}
		if($addextrarows > 0){ ?>
              <tr height="30">
				<td class="cobhl" bgcolor="#EBEBEB" rowspan="<?php print $addextrarows+4;?>">&nbsp;</td>
				<td class="cobll" bgcolor="#FFFFFF" align="right" colspan="3"><strong><?php print $xxSubTot?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="right"><?php print FormatEuroCurrency($totalgoods)?></td>
				<td class="cobll" bgcolor="#FFFFFF" align="center"><a href="javascript:doupdate()" onmouseover="window.status='<?php print str_replace("'","\\'",$xxDelete)?>';return true" onmouseout="window.status='';return true"><strong><?php print $xxDelete?></strong></a></td>
			  </tr>
<?php	}
		if($totaldiscounts>0){ ?>
			  <tr height="30">
				<td class="cobll" bgcolor="#FFFFFF" align="right" colspan="3"><font color="#FF0000"><strong><?php print $xxDsApp?></strong></font></td>
				<td class="cobll" bgcolor="#FFFFFF" align="right"><font color="#FF0000"><?php print FormatEuroCurrency($totaldiscounts)?></font></td>
				<td class="cobll" bgcolor="#FFFFFF" align="center">&nbsp;</td>
			  </tr>
<?php	}
		if(@$estimateshipping==TRUE){
			if(@$_SESSION["xsshipping"] == ''){
				if(calculateshipping()){
					if(is_numeric(@$shipinsuranceamt) && abs(@$addshippinginsurance)==1) $shipping += ($addshippinginsurance==1 ? (((double)$totalgoods*(double)$shipinsuranceamt)/100.0) : $shipinsuranceamt);
					if($taxShipping==1 && @$showtaxinclusive) $shipping += ((double)$shipping*((double)$countryTaxRate))/100.0;
					calculateshippingdiscounts(FALSE);
					$_SESSION["xsshipping"]=$shipping-$freeshipamnt;
				}
			}else
				$shipping = $_SESSION["xsshipping"];
			if($errormsg != ''){ ?>
              <tr height="30">
				<td class="cobll" bgcolor="#FFFFFF" align="right" colspan="3"><strong><?php print $xxShpEst?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" colspan="2"><font style="font-size: 10px" color="#FF0000"><strong><?php print $errormsg?></strong></font></td>
			  </tr>
<?php		}else{ ?>
              <tr height="30">
				<td class="cobll" bgcolor="#FFFFFF" align="right" colspan="3"><strong><?php print $xxShpEst?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="right"><?php if($freeshipamnt==$shipping) print '<p align="center"><font color="#FF0000"><strong>' . $xxFree . '</strong></font></p>'; else print FormatEuroCurrency($shipping)?></td>
				<td class="cobll" bgcolor="#FFFFFF" align="center">&nbsp;</td>
			  </tr>
<?php		}
			if($wantstateselector){
				$sSQL = "SELECT stateName,stateAbbrev FROM states WHERE stateEnabled=1 ORDER BY stateName";
				$result = mysql_query($sSQL) or print(mysql_error());
				$numallstates=0;
				$numallcountries=0;
				while($rs = mysql_fetch_array($result))
					$allstates[$numallstates++]=$rs;
				mysql_free_result($result);
				if(is_array($allstates)){ ?>
              <tr height="30">
				<td class="cobll" bgcolor="#FFFFFF" align="right" colspan="3"><strong><?php print $xxAllSta?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" colspan="2"><select name="state" size="1"><?php show_states($shipstate) ?></select></td>
			  </tr>
<?php			}
			}
			if($wantcountryselector){ ?>
              <tr height="30">
				<td class="cobll" bgcolor="#FFFFFF" align="right" colspan="3"><strong><?php print $xxCountry?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" colspan="2"><select name="country" size="1"><?php
				$sSQL = "SELECT countryName,countryCode," . getlangid("countryName",8) . " AS cnameshow FROM countries WHERE countryEnabled=1 ORDER BY countryOrder DESC," . getlangid("countryName",8);
				$result = mysql_query($sSQL) or print(mysql_error());
				while($rs = mysql_fetch_assoc($result)){
					print '<option value="' . $rs["countryName"] . '"';
					if($shipcountry==$rs["countryName"]) print ' selected';
					print '>' . $rs["cnameshow"] . "</option>\r\n";
				}
				mysql_free_result($result); ?></select></td>
			  </tr>
<?php		}
			if($wantzipselector){ ?>
              <tr height="30">
				<td class="cobll" bgcolor="#FFFFFF" align="right" colspan="3"><strong><?php print $xxZip?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="left" colspan="2"><input type="text" name="zip" size="8" value="<?php print htmlspecialchars($destZip)?>"></td>
			  </tr>
<?php		}
		}
		if(@$showtaxinclusive){
			if(@$perproducttaxrate!=TRUE) $countryTax = round(((($totalgoods-$countrytaxfree)+($taxShipping==2 ? $shipping-$freeshipamnt : 0))-$totaldiscounts)*$countryTaxRate/100.0, 2);
			$_SESSION['xscountrytax']=$countryTax;
?>			  <tr height="30">
				<td class="cobll" bgcolor="#FFFFFF" align="right" colspan="3"><strong><?php print $xxCntTax?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="right"><?php print FormatEuroCurrency($countryTax)?></td>
				<td class="cobll" bgcolor="#FFFFFF" align="center">&nbsp;</td>
			  </tr>
<?php	}else
			$countryTax=0; ?>
              <tr height="30">
			  <?php	if($addextrarows==0){ ?>
				<td class="cobhl" bgcolor="#EBEBEB" rowspan="3">&nbsp;</td>
			  <?php } ?>
				<td class="cobll" bgcolor="#FFFFFF" align="right" colspan="3"><strong><?php print $xxGndTot?>:</strong></td>
				<td class="cobll" bgcolor="#FFFFFF" align="right"><?php print FormatEuroCurrency(($totalgoods+$shipping+$countryTax)-($totaldiscounts+$freeshipamnt))?></td>
				<td class="cobll" bgcolor="#FFFFFF" align="center"><?php if($addextrarows==0) print '<a href="javascript:doupdate()" onmouseover="window.status=\'' . str_replace("'","\\'",$xxDelete) . '\';return true" onmouseout="window.status=\'\';return true"><strong>' . $xxDelete . '</strong></a>'; else print '&nbsp;'; ?></td>
			  </tr>
			  <tr height="30">
				<td class="cobll" bgcolor="#FFFFFF" colspan="5">
				  <table width="100%" cellspacing="0" cellpadding="0" border="0">
				    <tr>
					  <td class="cobll" bgcolor="#FFFFFF" width="50%" align="center"><a href="<?php if(trim(@$_SESSION["frompage"])!='' && (@$actionaftercart==2 || @$actionaftercart==3)) print $_SESSION['frompage']; else print $xxHomeURL?>" onmouseover="window.status='<?php print str_replace("'","\\'",$xxCntShp)?>';return true" onmouseout="window.status='';return true"><strong><?php print $xxCntShp?></strong></a></td>
					  <td class="cobll" bgcolor="#FFFFFF" width="50%" align="center"><a href="javascript:doupdate()" onmouseover="window.status='<?php print str_replace("'","\\'",$xxUpdTot)?>';return true" onmouseout="window.status='';return true"><strong><?php print $xxUpdTot?></strong></a></td>
					</tr>
				  </table>
				</td>
			  </tr>
			  <tr height="30">
				<td class="cobll" bgcolor="#FFFFFF" colspan="5">
				  <table width="100%" cellspacing="0" cellpadding="0" border="0">
				    <tr>
					  <td class="cobll" bgcolor="#FFFFFF" align="center">
<?php			if(trim(@$_SESSION["clientUser"]) != ''){
					srand((double)microtime()*1000000);
					$sequence = rand("10000000", "99999999");
					mysql_query("DELETE FROM tmplogin WHERE tmplogindate < '" . date("Y-m-d H:i:s", time()-(3*60*60*24)) . "' OR tmploginid='" . session_id() . "'") or print(mysql_error());
					mysql_query("INSERT INTO tmplogin (tmploginid, tmploginname, tmploginchk, tmplogindate) VALUES ('" . session_id() . "','" . trim($_SESSION['clientID']) . "'," . $sequence . ",'" . date('Y-m-d H:i:s', time()) . "')") or print(mysql_error());
					writehiddenvar('checktmplogin',$sequence);
					if(($_SESSION["clientActions"] & 8) == 8 || ($_SESSION["clientActions"] & 16) == 16){
						if(@$minwholesaleamount!='') $minpurchaseamount=$minwholesaleamount;
						if(@$minwholesalemessage!='') $minpurchasemessage=$minwholesalemessage;
					}
				} ?>
			  <table width="100%" cellspacing="2" cellpadding="2" border="0">
<?php		if($totalgoods < @$minpurchaseamount){ ?>
				<tr><td width="100%" align="center" colspan="2"><?php print @$minpurchasemessage?></td></tr>
<?php		}elseif(@$forceclientlogin==TRUE && @$_SESSION['clientID']==''){ ?>
				<tr><td width="100%" align="center" colspan="2"><?php print $xxBfChk?> <a href="cart.php?mode=login"><strong><?php print $xxLogin?></strong></a><?php if(@$allowclientregistration==TRUE) print ' ' . $xxOr . ' <a href="cart.php?mode=newaccount"><strong>' . $xxCrAc . '</strong></a>'?>.</td></tr>
<?php		}else{
				if(@$_SESSION['clientLoginLevel'] != '') $minloglevel=$_SESSION['clientLoginLevel']; else $minloglevel=0;
				$sSQL = "SELECT payProvID,payProvData1,payProvData2,payProvDemo FROM payprovider WHERE payProvEnabled=1 AND payProvLevel<=" . $minloglevel . " ORDER BY payProvOrder";
				$result = mysql_query($sSQL) or print(mysql_error());
				$regularcheckoutshown=FALSE;
				while($rs = mysql_fetch_assoc($result)){
					if($rs['payProvID']==19){ ?>
				<tr><td align="center" colspan="2"><?php print $xxPPPBlu?></td></tr>
				<tr><td colspan="2" align="center"><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckoutsm.gif" border="0" onclick="javascript:document.forms.checkoutform.mode.value='paypalexpress1';" alt="PayPal Express" /></td></tr>
<?php				}elseif($rs['payProvID']==20){
						$theurl = writegoogleparams($rs['payProvData1'], $rs['payProvData2'], $rs['payProvDemo']);
						if($xxGooCo != ''){ ?><tr><td align="center" colspan="2"><strong><?php print $xxGooCo?></strong></td></tr><?php } ?>
				<tr><td colspan="2" align="center"><input type="image" name="GBuy" alt="Google Checkout" src="http://checkout.google.com/buttons/checkout.gif?merchant_id=<?php print $rs['payProvData1'] . (@$googlebuttonparams!='' ? $googlebuttonparams : '&w=160&h=43&style=white&variant=text&loc=en_US') ?>" onclick="document.forms.checkoutform.action='<?php print $theurl?>';"></td></tr>
<?php				}elseif(! $regularcheckoutshown){
						$regularcheckoutshown=TRUE; ?>
				<tr><td width="100%" align="center" colspan="2"><strong><?php print $xxPrsChk?></strong></td></tr>
				<tr><td align="center" colspan="2"><input type="image" src="images/checkout.gif" border="0" onclick="javascript:document.forms.checkoutform.mode.value='checkout';" alt="<?php print $xxCOTxt?>" /></td></tr>
<?php				}
				}
mysql_free_result($result);
			} ?>
			  </table>
					  </td>
					  <td class="cobll" bgcolor="#FFFFFF" width="16" height="26" align="right" valign="bottom"><img src="images/tablebr.gif" alt="" /></td>
					</tr>
				  </table>
				</td>
			  </tr>
<script language="javascript" type="text/javascript">
<!--
function changechecker(){
dowarning=false;
<?php print $changechecker?>
if(dowarning){
	if(confirm('<?php print str_replace("'","\'",$xxWrnChQ)?>')){
		document.checkoutform.submit();
		return false;
	}else
		return(true);
}
return true;
}
//--></script>
<input type="hidden" name="estimate" value="<?php print number_format(($totalgoods+$shipping)-($totaldiscounts+$freeshipamnt),2,'.','') ?>" />
<?php
	}else{
		$cartEmpty=TRUE; ?>
              <tr>
			    <td class="cobll" bgcolor="#FFFFFF" colspan="6" align="center">
				  <p>&nbsp;</p><p><?php print $xxSryEmp?></p><p>&nbsp;</p>
<script language="javascript" type="text/javascript">
<!--
if(document.cookie=="") document.write("<?php print str_replace('"', '\"', $xxNoCk . " " . $xxSecWar)?>");
//--></script>
<noscript><?php print $xxNoJS . " " . $xxSecWar?></noscript>
				  <p><a href="<?php if(trim(@$_SESSION["frompage"])!='' && (@$actionaftercart==2 || @$actionaftercart==3)) print $_SESSION["frompage"]; else print $xxHomeURL?>"><strong><?php print $xxCntShp?></strong></a></p>
				  <p>&nbsp;</p>
				</td>
			  </tr>
<?php
	} ?>	</table>
	</form>
<?php
}
?>