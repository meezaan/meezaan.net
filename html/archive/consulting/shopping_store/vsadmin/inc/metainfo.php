<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
$prodid=trim(@$_GET["prod"]);
$catid=@$_GET["cat"];
$productid="";
$productname="";
$productdescription="";
$sectionname="";
$sectiondescription="";
$topsection="";
$sntxt = "sectionName";
$sdtxt = "sectionDescription";
$pntxt = "pName";
$pdtxt = "pDescription";
if(function_exists("getadminsettings")){
	$alreadygotadmin = getadminsettings();
	$sntxt = getlangid("sectionName",256);
	$sdtxt = getlangid("sectionDescription",512);
	$pntxt = getlangid("pName",1);
	$pdtxt = getlangid("pDescription",2);
}
if($prodid != ""){
	$result = mysql_query("SELECT pID,".$pntxt.",".$pdtxt.",".$sntxt." FROM products INNER JOIN sections ON products.pSection=sections.sectionID WHERE pId='" . mysql_escape_string($prodid) . "'") or print(mysql_error());
	if($rs = mysql_fetch_array($result)){
		$productid=str_replace('"', '&quot;', strip_tags($rs["pID"]));
		$productname=str_replace('"', '&quot;', strip_tags($rs[$pntxt]));
		$productdescription=str_replace('"', '&quot;', strip_tags($rs[$pdtxt]));
		$sectionname=str_replace('"', '&quot;', strip_tags($rs[$sntxt]));
	}
	if($catid != '' && is_numeric($catid)){
		$result = mysql_query("SELECT ".$sntxt." FROM sections WHERE sectionID=" . $catid) or print(mysql_error());
		if($rs = mysql_fetch_array($result)) $sectionname=str_replace('"', '&quot;', strip_tags($rs[$sntxt]));
	}
}elseif($catid != '' && is_numeric($catid)){
	$topsection=0;
	$result = mysql_query("SELECT ".$sntxt.",".$sdtxt.",topSection FROM sections WHERE sectionID=" . $catid) or print(mysql_error());
	if($rs = mysql_fetch_array($result)){
		$sectionname=str_replace('"', '&quot;', strip_tags($rs[$sntxt]));
		$sectiondescription=str_replace('"', '&quot;', strip_tags($rs[$sdtxt]));
		$topsection=$rs["topSection"];
	}
	if($topsection != 0){
		$result = mysql_query("SELECT ".$sntxt." FROM sections WHERE sectionID=" . $topsection) or print(mysql_error());
		if($rs = mysql_fetch_array($result))
			$topsection=str_replace('"', '&quot;', strip_tags($rs[$sntxt]));
	}else
		$topsection="";
}
?>