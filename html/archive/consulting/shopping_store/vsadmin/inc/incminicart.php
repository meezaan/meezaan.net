<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(trim(@$_POST["sessionid"]) != "")
	$thesession = trim(@$_POST["sessionid"]);
else
	$thesession = session_id();
$thesession = mysql_escape_string($thesession);
$useEuro=false;
$WSP = "";
if(@$_SESSION["clientUser"] != ""){
	if(($_SESSION["clientActions"] & 8) == 8)
		$WSP = "pWholesalePrice AS ";
	if(($_SESSION["clientActions"] & 16) == 16)
		$WSP = $_SESSION["clientPercentDiscount"] . "*pPrice AS ";
}
$mcgndtot=0;
$totquant=0;
$optPriceDiff=0;
$mcpdtxt="";
$sSQL = "SELECT countryLCID,countryCurrency,adminStoreURL FROM admin INNER JOIN countries ON admin.adminCountry=countries.countryID WHERE adminID=1";
$result = mysql_query($sSQL) or print(mysql_error());
$rs = mysql_fetch_array($result);
$adminLocale = $rs["countryLCID"];
$useEuro = ($rs["countryCurrency"]=="EUR");
$storeurl = $rs["adminStoreURL"];
if((substr(strtolower($storeurl),0,7) != "http://") && (substr(strtolower($storeurl),0,8) != "https://"))
	$storeurl = "http://" . $storeurl;
if(substr($storeurl,-1) != "/") $storeurl .= "/";
mysql_free_result($result);
$sSQL = "SELECT cartID,cartProdID,pName,".$WSP."pPrice,cartQuantity FROM cart INNER JOIN products ON cart.cartProdID=products.pID WHERE cartCompleted=0 AND cartSessionID='" . $thesession . "'";
$result = mysql_query($sSQL) or print(mysql_error());
while($rs = mysql_fetch_assoc($result)){
	$optPriceDiff=0;
	$mcpdtxt .= '<tr><td class="mincart" bgcolor="#F0F0F0">' . $rs["cartQuantity"] . ' ' . $rs["pName"] . "</td></tr>";
	$sSQL = "SELECT SUM(coPriceDiff) AS sumDiff FROM cartoptions WHERE coCartID=" . $rs["cartID"];
	$result2 = mysql_query($sSQL) or print(mysql_error());
	$rs2 = mysql_fetch_assoc($result2);
	if(! is_null($rs2["sumDiff"])) $optPriceDiff=$rs2["sumDiff"];
	mysql_free_result($result2);
	$subtot = (($rs["pPrice"]+$optPriceDiff)*(int)$rs["cartQuantity"]);
	$totquant++;
	$mcgndtot += $subtot;
}
mysql_free_result($result);
if(@$_POST["mode"]=="update") $docartupdate=true;
?>
        <?php print $totquant ?> Items &nbsp;|&nbsp; <?php print $xxTotal . " " . FormatEuroCurrency($mcgndtot)?>
        
        
      