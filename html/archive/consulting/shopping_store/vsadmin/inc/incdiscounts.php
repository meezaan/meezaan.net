<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$storesessionvalue=="") $storesessionvalue="virtualstore".time();
if($_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE) exit;
$success=TRUE;
$sSQL = "";
$alreadygotadmin = getadminsettings();
if(@$_POST["posted"]=="1"){
	if(@$_POST["act"]=="delete"){
		$sSQL = "DELETE FROM cpnassign WHERE cpaCpnID=" . @$_POST["id"];
		mysql_query($sSQL) or print(mysql_error());
		$sSQL = "DELETE FROM coupons WHERE cpnID=" . @$_POST["id"];
		mysql_query($sSQL) or print(mysql_error());
		print '<meta http-equiv="refresh" content="3; url=admindiscounts.php">';
	}elseif(@$_POST["act"]=="domodify"){
		$sSQL = "UPDATE coupons SET cpnName='" . mysql_escape_string(unstripslashes(@$_POST["cpnName"])) . "'";
			for($index=2; $index <= $adminlanguages+1; $index++){
				if(($adminlangsettings & 1024)==1024) $sSQL .= ",cpnName" . $index . "='" . mysql_escape_string(unstripslashes(@$_POST["cpnName" . $index])) . "'";
			}
			if(trim(@$_POST["cpnWorkingName"]) != "")
				$sSQL .= ",cpnWorkingName='" . mysql_escape_string(unstripslashes(@$_POST["cpnWorkingName"])) . "'";
			else
				$sSQL .= ",cpnWorkingName='" . mysql_escape_string(unstripslashes(@$_POST["cpnName"])) . "'";
			if(@$_POST["cpnIsCoupon"]=="0")
				$sSQL .= ",cpnNumber='',";
			else
				$sSQL .= ",cpnNumber='" . mysql_escape_string(unstripslashes(@$_POST["cpnNumber"])) . "',";
			$sSQL .= "cpnType=" . @$_POST["cpnType"] . ",";
			$numdays=0;
			if(is_numeric(@$_POST["cpnEndDate"])) $numdays = (int)@$_POST["cpnEndDate"];
			if($numdays > 0)
				$sSQL .= "cpnEndDate='" . date("Y-m-d",(time() + ($numdays*60*60*24))) . "',";
			else
				$sSQL .= "cpnEndDate='3000-01-01',";
			if(is_numeric(@$_POST["cpnDiscount"]) && @$_POST["cpnType"] != "0")
				$sSQL .= "cpnDiscount=" . @$_POST["cpnDiscount"] . ",";
			else
				$sSQL .= "cpnDiscount=0,";
			if(is_numeric(@$_POST["cpnThreshold"]))
				$sSQL .= "cpnThreshold=" . @$_POST["cpnThreshold"] . ",";
			else
				$sSQL .= "cpnThreshold=0,";
			if(is_numeric(@$_POST["cpnThresholdMax"]))
				$sSQL .= "cpnThresholdMax=" . @$_POST["cpnThresholdMax"] . ",";
			else
				$sSQL .= "cpnThresholdMax=0,";
			if(is_numeric(@$_POST["cpnThresholdRepeat"]))
				$sSQL .= "cpnThresholdRepeat=" . @$_POST["cpnThresholdRepeat"] . ",";
			else
				$sSQL .= "cpnThresholdRepeat=0,";
			if(is_numeric(@$_POST["cpnQuantity"]))
				$sSQL .= "cpnQuantity=" . @$_POST["cpnQuantity"] . ",";
			else
				$sSQL .= "cpnQuantity=0,";
			if(is_numeric(@$_POST["cpnQuantityMax"]))
				$sSQL .= "cpnQuantityMax=" . @$_POST["cpnQuantityMax"] . ",";
			else
				$sSQL .= "cpnQuantityMax=0,";
			if(is_numeric(@$_POST["cpnQuantityRepeat"]))
				$sSQL .= "cpnQuantityRepeat=" . @$_POST["cpnQuantityRepeat"] . ",";
			else
				$sSQL .= "cpnQuantityRepeat=0,";
			if(trim(@$_POST["cpnNumAvail"]) != "" && is_numeric(@$_POST["cpnNumAvail"]))
				$sSQL .= "cpnNumAvail=" . @$_POST["cpnNumAvail"] . ",";
			else
				$sSQL .= "cpnNumAvail=30000000,";
			if(@$_POST["cpnType"]=="0")
				$sSQL .= "cpnCntry=" . @$_POST["cpnCntry"] . ",";
			else
				$sSQL .= "cpnCntry=0,";
			$sSQL .= "cpnIsCoupon=" . @$_POST["cpnIsCoupon"] . ",";
			if(@$_POST["cpnType"]=="0")
				$sSQL .= "cpnSitewide=1";
			else
				$sSQL .= "cpnSitewide=" . @$_POST["cpnSitewide"];
			$sSQL .= " WHERE cpnID=" . @$_POST["id"];
		mysql_query($sSQL) or print(mysql_error());
		print '<meta http-equiv="refresh" content="3; url=admindiscounts.php">';
	}elseif(@$_POST["act"]=="doaddnew"){
		$sSQL = "INSERT INTO coupons (cpnName";
			for($index=2; $index <= $adminlanguages+1; $index++){
				if(($adminlangsettings & 1024)==1024) $sSQL .= ",cpnName" . $index;
			}
			$sSQL .= ",cpnWorkingName,cpnNumber,cpnType,cpnEndDate,cpnDiscount,cpnThreshold,cpnThresholdMax,cpnThresholdRepeat,cpnQuantity,cpnQuantityMax,cpnQuantityRepeat,cpnNumAvail,cpnCntry,cpnIsCoupon,cpnSitewide) VALUES (";
			$sSQL .= "'" . mysql_escape_string(unstripslashes(@$_POST["cpnName"])) . "',";
			for($index=2; $index <= $adminlanguages+1; $index++){
				if(($adminlangsettings & 1024)==1024) $sSQL .= "'" . mysql_escape_string(unstripslashes(@$_POST["cpnName" . $index])) . "',";
			}
			if(trim(@$_POST["cpnWorkingName"]) != "")
				$sSQL .= "'" . mysql_escape_string(unstripslashes(@$_POST["cpnWorkingName"])) . "',";
			else
				$sSQL .= "'" . mysql_escape_string(unstripslashes(@$_POST["cpnName"])) . "',";
			if(@$_POST["cpnIsCoupon"]=="0")
				$sSQL .= "'',";
			else
				$sSQL .= "'" . mysql_escape_string(unstripslashes(@$_POST["cpnNumber"])) . "',";
			$sSQL .= @$_POST["cpnType"] . ",";
			$numdays=0;
			if(is_numeric(@$_POST["cpnEndDate"])) $numdays = (int)@$_POST["cpnEndDate"];
			if($numdays > 0)
				$sSQL .= "'" . date("Y-m-d",(time() + ($numdays*60*60*24))) . "',";
			else
				$sSQL .= "'3000-01-01',";
			if(is_numeric(@$_POST["cpnDiscount"]) && @$_POST["cpnType"] != "0")
				$sSQL .= @$_POST["cpnDiscount"] . ",";
			else
				$sSQL .= "0,";
			if(is_numeric(@$_POST["cpnThreshold"]))
				$sSQL .= @$_POST["cpnThreshold"] . ",";
			else
				$sSQL .= "0,";
			if(is_numeric(@$_POST["cpnThresholdMax"]))
				$sSQL .= @$_POST["cpnThresholdMax"] . ",";
			else
				$sSQL .= "0,";
			if(is_numeric(@$_POST["cpnThresholdRepeat"]))
				$sSQL .= @$_POST["cpnThresholdRepeat"] . ",";
			else
				$sSQL .= "0,";
			if(is_numeric(@$_POST["cpnQuantity"]))
				$sSQL .= @$_POST["cpnQuantity"] . ",";
			else
				$sSQL .= "0,";
			if(is_numeric(@$_POST["cpnQuantityMax"]))
				$sSQL .= @$_POST["cpnQuantityMax"] . ",";
			else
				$sSQL .= "0,";
			if(is_numeric(@$_POST["cpnQuantityRepeat"]))
				$sSQL .= @$_POST["cpnQuantityRepeat"] . ",";
			else
				$sSQL .= "0,";
			if(trim(@$_POST["cpnNumAvail"]) != "" && is_numeric(@$_POST["cpnNumAvail"]))
				$sSQL .= @$_POST["cpnNumAvail"] . ",";
			else
				$sSQL .= "30000000,";
			if(@$_POST["cpnType"]=="0")
				$sSQL .= @$_POST["cpnCntry"] . ",";
			else
				$sSQL .= "0,";
			$sSQL .= @$_POST["cpnIsCoupon"] . ",";
			if(@$_POST["cpnType"]=="0")
				$sSQL .= "1)";
			else
				$sSQL .= @$_POST["cpnSitewide"] . ")";
		mysql_query($sSQL) or print(mysql_error());
		print '<meta http-equiv="refresh" content="3; url=admindiscounts.php">';
	}
}
?>
<script language="javascript" type="text/javascript">
<!--
var savebg, savebc, savecol;
function formvalidator(theForm)
{
  if(theForm.cpnName.value == "")
  {
    alert("<?php print $yyPlsEntr?> \"<?php print $yyDisTxt?>\".");
    theForm.cpnName.focus();
    return (false);
  }
  if(theForm.cpnName.value.length > 255)
  {
    alert("<?php print $yyMax255?> \"<?php print $yyDisTxt?>\".");
    theForm.cpnName.focus();
    return (false);
  }
  if(theForm.cpnType.selectedIndex!=0){
	if(theForm.cpnDiscount.value == "")
	{
	  alert("<?php print $yyPlsEntr?> \"<?php print $yyDscAmt?>\".");
	  theForm.cpnDiscount.focus();
	  return (false);
	}
	if(theForm.cpnType.selectedIndex==2){
	  if(theForm.cpnDiscount.value < 0 || theForm.cpnDiscount.value > 100){
		alert("<?php print $yyNum100?> \"<?php print $yyDscAmt?>\".");
		theForm.cpnDiscount.focus();
		return (false);
	  }
	}
  }
  if(theForm.cpnIsCoupon.selectedIndex==1){
	if(theForm.cpnNumber.value == "")
	{
	  alert("<?php print $yyPlsEntr?> \"<?php print $yyCpnCod?>\".");
	  theForm.cpnNumber.focus();
	  return (false);
	}
	var checkOK = "0123456789abcdefghijklmnopqrstuvwxyz-_";
	var checkStr = theForm.cpnNumber.value.toLowerCase();
	var allValid = true;
	for (i = 0;  i < checkStr.length;  i++)
	{
		ch = checkStr.charAt(i);
		for (j = 0;  j < checkOK.length;  j++)
			if (ch == checkOK.charAt(j))
				break;
		if (j == checkOK.length){
			allValid = false;
				break;
		}
	}
	if (!allValid)
	{
		alert("<?php print $yyAlpha2?> \"<?php print $yyCpnCod?>\".");
		theForm.cpnNumber.focus();
		return (false);
	}
  }
  var checkOK = "0123456789";
  var checkStr = theForm.cpnNumAvail.value;
  var allValid = true;
  for (i = 0;  i < checkStr.length;  i++)
  {
	ch = checkStr.charAt(i);
	for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
	if (j == checkOK.length){
		allValid = false;
			break;
	}
  }
  if (!allValid)
  {
	alert("<?php print $yyOnlyNum?> \"<?php print $yyNumAvl?>\".");
	theForm.cpnNumAvail.focus();
	return (false);
  }
  if(theForm.cpnNumAvail.value != "" && theForm.cpnNumAvail.value > 1000000)
  {
    alert("<?php print $yyNumMil?> \"<?php print $yyNumAvl?>\"<?php print $yyOrBlank?>");
    theForm.cpnNumAvail.focus();
    return (false);
  }
  var checkOK = "0123456789";
  var checkStr = theForm.cpnEndDate.value;
  var allValid = true;
  for (i = 0;  i < checkStr.length;  i++)
  {
	ch = checkStr.charAt(i);
	for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
	if (j == checkOK.length){
		allValid = false;
			break;
	}
  }
  if (!allValid)
  {
	alert("<?php print $yyOnlyNum?> \"<?php print $yyDaysAv?>\".");
	theForm.cpnEndDate.focus();
	return (false);
  }
  var checkOK = "0123456789.";
  var checkStr = theForm.cpnThreshold.value;
  var allValid = true;
  for (i = 0;  i < checkStr.length;  i++)
  {
	ch = checkStr.charAt(i);
	for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
	if (j == checkOK.length){
		allValid = false;
			break;
	}
  }
  if (!allValid)
  {
	alert("<?php print $yyOnlyDec?> \"<?php print $yyMinPur?>\".");
	theForm.cpnThreshold.focus();
	return (false);
  }
  var checkOK = "0123456789.";
  var checkStr = theForm.cpnThresholdRepeat.value;
  var allValid = true;
  for (i = 0;  i < checkStr.length;  i++)
  {
	ch = checkStr.charAt(i);
	for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
	if (j == checkOK.length){
		allValid = false;
			break;
	}
  }
  if (!allValid)
  {
	alert("<?php print $yyOnlyDec?> \"<?php print $yyRepEvy?>\".");
	theForm.cpnThresholdRepeat.focus();
	return (false);
  }
  var checkOK = "0123456789.";
  var checkStr = theForm.cpnThresholdMax.value;
  var allValid = true;
  for (i = 0;  i < checkStr.length;  i++)
  {
	ch = checkStr.charAt(i);
	for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
	if (j == checkOK.length){
		allValid = false;
			break;
	}
  }
  if (!allValid)
  {
	alert("<?php print $yyOnlyDec?> \"<?php print $yyMaxPur?>\".");
	theForm.cpnThresholdMax.focus();
	return (false);
  }
  var checkOK = "0123456789";
  var checkStr = theForm.cpnQuantity.value;
  var allValid = true;
  for (i = 0;  i < checkStr.length;  i++)
  {
	ch = checkStr.charAt(i);
	for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
	if (j == checkOK.length){
		allValid = false;
			break;
	}
  }
  if (!allValid)
  {
	alert("<?php print $yyOnlyNum?> \"<?php print $yyMinQua?>\".");
	theForm.cpnQuantity.focus();
	return (false);
  }
  var checkOK = "0123456789";
  var checkStr = theForm.cpnQuantityRepeat.value;
  var allValid = true;
  for (i = 0;  i < checkStr.length;  i++)
  {
	ch = checkStr.charAt(i);
	for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
	if (j == checkOK.length){
		allValid = false;
			break;
	}
  }
  if (!allValid)
  {
	alert("<?php print $yyOnlyNum?> \"<?php print $yyRepEvy?>\".");
	theForm.cpnQuantityRepeat.focus();
	return (false);
  }
  var checkOK = "0123456789";
  var checkStr = theForm.cpnQuantityMax.value;
  var allValid = true;
  for (i = 0;  i < checkStr.length;  i++)
  {
	ch = checkStr.charAt(i);
	for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
	if (j == checkOK.length){
		allValid = false;
			break;
	}
  }
  if (!allValid)
  {
	alert("<?php print $yyOnlyNum?> \"<?php print $yyMaxQua?>\".");
	theForm.cpnQuantityMax.focus();
	return (false);
  }
  var checkOK = "0123456789.";
  var checkStr = theForm.cpnDiscount.value;
  var allValid = true;
  for (i = 0;  i < checkStr.length;  i++)
  {
	ch = checkStr.charAt(i);
	for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
	if (j == checkOK.length){
		allValid = false;
			break;
	}
  }
  if (!allValid)
  {
	alert("<?php print $yyOnlyDec?> \"<?php print $yyDscAmt?>\".");
	theForm.cpnDiscount.focus();
	return (false);
  }
  document.mainform.cpnNumber.disabled=false;
  document.mainform.cpnDiscount.disabled=false;
  document.mainform.cpnCntry.disabled=false;
  document.mainform.cpnSitewide.disabled=false;
  document.mainform.cpnThresholdRepeat.disabled=false;
  document.mainform.cpnQuantityRepeat.disabled=false;
  return (true);
}
function couponcodeactive(forceactive){
	if(document.mainform.cpnIsCoupon.selectedIndex==0){
		document.mainform.cpnNumber.style.backgroundColor="#DDDDDD";
		document.mainform.cpnNumber.style.borderColor="#aa3300";
		document.mainform.cpnNumber.style.color="#aa3300";
		document.mainform.cpnNumber.disabled=true;
	}
	else if(document.mainform.cpnIsCoupon.selectedIndex==1){
		document.mainform.cpnNumber.style.backgroundColor=savebg;
		document.mainform.cpnNumber.style.borderColor=savebc;
		document.mainform.cpnNumber.style.color=savecol;
		document.mainform.cpnNumber.disabled=false;
	}
}
function changecouponeffect(forceactive){
	if(document.mainform.cpnType.selectedIndex==0){
		document.mainform.cpnDiscount.style.backgroundColor="#DDDDDD";
		document.mainform.cpnDiscount.style.borderColor="#aa3300";
		document.mainform.cpnDiscount.style.color="#aa3300";
		document.mainform.cpnDiscount.disabled=true;

		document.mainform.cpnCntry.style.backgroundColor=savebg;
		document.mainform.cpnCntry.style.borderColor=savebc;
		document.mainform.cpnCntry.style.color=savecol;
		document.mainform.cpnCntry.disabled=false;

		document.mainform.cpnSitewide.style.backgroundColor="#DDDDDD";
		document.mainform.cpnSitewide.style.borderColor="#aa3300";
		document.mainform.cpnSitewide.style.color="#aa3300";
		document.mainform.cpnSitewide.disabled=true;
	}else{
		document.mainform.cpnDiscount.style.backgroundColor=savebg;
		document.mainform.cpnDiscount.style.borderColor=savebc;
		document.mainform.cpnDiscount.style.color=savecol;
		document.mainform.cpnDiscount.disabled=false;

		document.mainform.cpnCntry.style.backgroundColor="#DDDDDD";
		document.mainform.cpnCntry.style.borderColor="#aa3300";
		document.mainform.cpnCntry.style.color="#aa3300";
		document.mainform.cpnCntry.disabled=true;

		document.mainform.cpnSitewide.style.backgroundColor=savebg;
		document.mainform.cpnSitewide.style.borderColor=savebc;
		document.mainform.cpnSitewide.style.color=savecol;
		document.mainform.cpnSitewide.disabled=false;
	}
	if(document.mainform.cpnType.selectedIndex==1){
		document.mainform.cpnThresholdRepeat.style.backgroundColor=savebg;
		document.mainform.cpnThresholdRepeat.style.borderColor=savebc;
		document.mainform.cpnThresholdRepeat.style.color=savecol;
		document.mainform.cpnThresholdRepeat.disabled=false;

		document.mainform.cpnQuantityRepeat.style.backgroundColor=savebg;
		document.mainform.cpnQuantityRepeat.style.borderColor=savebc;
		document.mainform.cpnQuantityRepeat.style.color=savecol;
		document.mainform.cpnQuantityRepeat.disabled=false;
	}else{
		document.mainform.cpnThresholdRepeat.style.backgroundColor="#DDDDDD";
		document.mainform.cpnThresholdRepeat.style.borderColor="#aa3300";
		document.mainform.cpnThresholdRepeat.style.color="#aa3300";
		document.mainform.cpnThresholdRepeat.disabled=true;

		document.mainform.cpnQuantityRepeat.style.backgroundColor="#DDDDDD";
		document.mainform.cpnQuantityRepeat.style.borderColor="#aa3300";
		document.mainform.cpnQuantityRepeat.style.color="#aa3300";
		document.mainform.cpnQuantityRepeat.disabled=true;
	}
}
//-->
</script>
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
<?php if(@$_POST["posted"]=="1" && (@$_POST["act"]=="modify" || @$_POST["act"]=="addnew")){
		if(@$_POST["act"]=="modify"){
			$sSQL = "SELECT cpnName,cpnName2,cpnName3,cpnWorkingName,cpnNumber,cpnType,cpnEndDate,cpnDiscount,cpnThreshold,cpnThresholdMax,cpnThresholdRepeat,cpnQuantity,cpnQuantityMax,cpnQuantityRepeat,cpnNumAvail,cpnCntry,cpnIsCoupon,cpnSitewide FROM coupons WHERE cpnID=" . @$_POST["id"];
			$result = mysql_query($sSQL) or print(mysql_error());
			$rs = mysql_fetch_array($result);
			$cpnName = $rs["cpnName"];
			for($index=2; $index <= $adminlanguages+1; $index++)
				$cpnNames[$index] = $rs["cpnName" . $index];
			$cpnWorkingName = $rs["cpnWorkingName"];
			$cpnNumber = $rs["cpnNumber"];
			$cpnType = $rs["cpnType"];
			$cpnEndDate = $rs["cpnEndDate"];
			$cpnDiscount = $rs["cpnDiscount"];
			$cpnThreshold = $rs["cpnThreshold"];
			$cpnThresholdMax = $rs["cpnThresholdMax"];
			$cpnThresholdRepeat = $rs["cpnThresholdRepeat"];
			$cpnQuantity = $rs["cpnQuantity"];
			$cpnQuantityMax = $rs["cpnQuantityMax"];
			$cpnQuantityRepeat = $rs["cpnQuantityRepeat"];
			$cpnNumAvail = $rs["cpnNumAvail"];
			$cpnCntry = $rs["cpnCntry"];
			$cpnIsCoupon = $rs["cpnIsCoupon"];
			$cpnSitewide = $rs["cpnSitewide"];
			mysql_free_result($result);
		}else{
			$cpnName = "";
			for($index=2; $index <= $adminlanguages+1; $index++)
				$cpnNames[$index] = "";
			$cpnWorkingName = "";
			$cpnNumber = "";
			$cpnType = 0;
			$cpnEndDate = '3000-01-01 00:00:00';
			$cpnDiscount = "";
			$cpnThreshold = 0;
			$cpnThresholdMax = 0;
			$cpnThresholdRepeat = 0;
			$cpnQuantity = 0;
			$cpnQuantityMax = 0;
			$cpnQuantityRepeat = 0;
			$cpnNumAvail = 30000000;
			$cpnCntry = 0;
			$cpnIsCoupon = 0;
			$cpnSitewide = 0;
		}
?>
        <tr>
		<form name="mainform" method="post" action="admindiscounts.php" onsubmit="return formvalidator(this)">
		  <td width="100%">
			<input type="hidden" name="posted" value="1" />
		<?php	if(@$_POST["act"]=="modify"){ ?>
			<input type="hidden" name="act" value="domodify" />
			<input type="hidden" name="id" value="<?php print @$_POST["id"]?>" />
		<?php	}else{ ?>
			<input type="hidden" name="act" value="doaddnew" />
		<?php	} ?>
            <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><strong><?php print $yyDscNew?></strong><br />&nbsp;</td>
			  </tr>
			  <tr>
				<td width="40%" align="right"><strong><?php print $yyCpnDsc?>:</td>
				<td width="60%"><select name="cpnIsCoupon" size="1" onChange="couponcodeactive(false);">
					<option value="0"><?php print $yyDisco?></option>
					<option value="1" <?php if((int)$cpnIsCoupon==1) print "selected" ?>><?php print $yyCoupon?></option>
					</select></td>
			  </tr>
			  <tr>
				<td width="40%" align="right"><strong><?php print $yyDscEff?>:</td>
				<td width="60%"><select name="cpnType" size="1" onChange="changecouponeffect(false);">
					<option value="0"><?php print $yyFrSShp?></option>
					<option value="1" <?php if((int)$cpnType==1) print "selected" ?>><?php print $yyFlatDs?></option>
					<option value="2" <?php if((int)$cpnType==2) print "selected" ?>><?php print $yyPerDis?></option>
					</select></td>
			  </tr>
			  <tr>
				<td width="40%" align="right"><strong><?php print $yyDisTxt?>:</strong></td>
				<td width="60%"><input type="text" name="cpnName" size="30" value="<?php print str_replace('"',"&quot;",$cpnName)?>" /></td>
			  </tr>
<?php		for($index=2; $index <= $adminlanguages+1; $index++){
				if(($adminlangsettings & 1024)==1024){ ?>
			  <tr>
				<td width="40%" align="right"><strong><?php print $yyDisTxt . " " . $index?>:</strong></td>
				<td width="60%"><input type="text" name="cpnName<?php print $index?>" size="30" value="<?php print str_replace('"',"&quot;",$cpnNames[$index])?>" /></td>
			  </tr>
<?php			}
			} ?>
			  <tr>
				<td width="40%" align="right"><strong><?php print $yyWrkNam?>:</strong></td>
				<td width="60%"><input type="text" name="cpnWorkingName" size="30" value="<?php print str_replace('"',"&quot;",$cpnWorkingName)?>" /></td>
			  </tr>
			  <tr>
				<td width="40%" align="right"><strong><?php print $yyCpnCod?>:</strong></td>
				<td width="60%"><input type="text" name="cpnNumber" size="30" value="<?php print $cpnNumber?>" /></td>
			  </tr>
			  <tr>
				<td width="40%" align="right"><strong><?php print $yyNumAvl?>:</strong></td>
				<td width="60%"><input type="text" name="cpnNumAvail" size="10" value="<?php if((int)$cpnNumAvail != 30000000) print $cpnNumAvail?>" /></td>
			  </tr>
			  <tr>
				<td width="40%" align="right"><strong><?php print $yyDaysAv?>:</strong></td>
				<td width="60%"><input type="text" name="cpnEndDate" size="10" value="<?php
				if($cpnEndDate != '3000-01-01 00:00:00')
					if(strtotime($cpnEndDate)-time() < 0) print "Expired"; else print floor((strtotime($cpnEndDate)-time())/(60*60*24))+1; ?>"></td>
			  </tr>
			  <tr>
				<td width="40%" align="right"><strong><?php print $yyMinPur?>:</strong></td>
				<td width="60%"><input type="text" name="cpnThreshold" size="10" value="<?php if((int)$cpnThreshold>0) print $cpnThreshold?>" /> <strong><?php print $yyRepEvy?>:</strong> <input type="text" name="cpnThresholdRepeat" size="10" value="<?php if((int)$cpnThresholdRepeat > 0) print $cpnThresholdRepeat?>" /></td>
			  </tr>
			  <tr>
				<td width="40%" align="right"><strong><?php print $yyMaxPur?>:</strong></td>
				<td width="60%"><input type="text" name="cpnThresholdMax" size="10" value="<?php if((int)$cpnThresholdMax>0) print $cpnThresholdMax?>" /></td>
			  </tr>
			  <tr>
				<td width="40%" align="right"><strong><?php print $yyMinQua?>:</strong></td>
				<td width="60%"><input type="text" name="cpnQuantity" size="10" value="<?php if((int)$cpnQuantity>0) print $cpnQuantity?>" /> <strong><?php print $yyRepEvy?>:</strong> <input type="text" name="cpnQuantityRepeat" size="10" value="<?php if((int)$cpnQuantityRepeat > 0) print $cpnQuantityRepeat?>" /></td>
			  </tr>
			  <tr>
				<td width="40%" align="right"><strong><?php print $yyMaxQua?>:</strong></td>
				<td width="60%"><input type="text" name="cpnQuantityMax" size="10" value="<?php if((int)$cpnQuantityMax>0) print $cpnQuantityMax?>" /></td>
			  </tr>
			  <tr>
				<td width="40%" align="right"><strong><?php print $yyDscAmt?>:</strong></td>
				<td width="60%"><input type="text" name="cpnDiscount" size="10" value="<?php print $cpnDiscount?>" /></td>
			  </tr>
			  <tr>
				<td width="40%" align="right"><strong><?php print $yyScope?>:</strong></td>
				<td width="60%"><select name="cpnSitewide" size="1">
					<option value="0"><?php print $yyIndCat?></option>
					<option value="3" <?php if((int)$cpnSitewide==3) print "selected" ?>><?php print $yyDsCaTo?></option>
					<option value="2" <?php if((int)$cpnSitewide==2) print "selected" ?>><?php print $yyGlInPr?></option>
					<option value="1" <?php if((int)$cpnSitewide==1) print "selected" ?>><?php print $yyGlPrTo?></option>
					</select></td>
			  </tr>
			  <tr>
				<td width="40%" align="right"><strong><?php print $yyRestr?>:</strong></td>
				<td width="60%"><select name="cpnCntry" size="1">
					<option value="0"><?php print $yyAppAll?></option>
					<option value="1" <?php if((int)$cpnCntry==1) print "selected" ?>><?php print $yyYesRes?></option>
					</select></td>
			  </tr>
			  <tr>
                <td width="100%" colspan="2" align="center"><br /><input type="submit" value="<?php print $yySubmit?>" /><br />&nbsp;</td>
			  </tr>
			  <tr> 
                <td width="100%" colspan="2" align="center"><br />
                          <a href="admin.php"><strong><?php print $yyAdmHom?></strong></a><br />
                          &nbsp;</td>
			  </tr>
            </table>
		  </td>
		</form>
        </tr>
<script language="javascript" type="text/javascript">
<!--
savebg=document.mainform.cpnNumber.style.backgroundColor;
savebc=document.mainform.cpnNumber.style.borderColor;
savecol=document.mainform.cpnNumber.style.color;
couponcodeactive(false);
changecouponeffect(false);
//-->
</script>
<?php }elseif(@$_POST["posted"]=="1" && $success){ ?>
        <tr>
          <td width="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyUpdSuc?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
                        <?php print $yyNoAuto?> <A href="admindiscounts.php"><strong><?php print $yyClkHer?></strong></a>.<br />
                        <br />
				<img src="../images/clearpixel.gif" width="300" height="3" alt="" />
                </td>
			  </tr>
			</table></td>
        </tr>
<?php }elseif(@$_POST["posted"]=="1"){ ?>
        <tr>
          <td width="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><font color="#FF0000"><strong><?php print $yyOpFai?></strong></font><br /><br /><?php print $errmsg?><br /><br />
				<a href="javascript:history.go(-1)"><strong><?php print $yyClkBac?></strong></a></td>
			  </tr>
			</table></td>
        </tr>
<?php }else{
?>
<script language="javascript" type="text/javascript">
<!--
function modrec(id) {
	document.mainform.id.value = id;
	document.mainform.act.value = "modify";
	document.mainform.submit();
}
function newrec(id) {
	document.mainform.id.value = id;
	document.mainform.act.value = "addnew";
	document.mainform.submit();
}
function delrec(id) {
cmsg = "<?php print $yyConDel?>\n"
if (confirm(cmsg)) {
	document.mainform.id.value = id;
	document.mainform.act.value = "delete";
	document.mainform.submit();
}
}
// -->
</script>
        <tr>
		  <form name="mainform" method="post" action="admindiscounts.php">
		  <td width="100%">
			<input type="hidden" name="posted" value="1" />
			<input type="hidden" name="act" value="xxxxx" />
			<input type="hidden" name="id" value="xxxxx" />
			<input type="hidden" name="selectedq" value="1" />
			<input type="hidden" name="newval" value="1" />
            <table width="100%" border="0" cellspacing="0" cellpadding="1" bgcolor="">
			  <tr> 
                <td width="100%" colspan="6" align="center"><br /><strong><?php print $yyDscAdm?></strong><br />&nbsp;</td>
			  </tr>
			  <tr>
				<td width="40%" align="left"><strong><?php print $yyWrkNam?></strong></td>
				<td width="10%" align="center"><strong><?php print $yyType?></strong></td>
				<td width="20%" align="center"><strong><?php print $yyExpDat?></strong></td>
				<td width="10%" align="center"><strong><?php print $yyGlobal?></strong></td>
				<td width="10%" align="center"><strong><?php print $yyModify?></strong></td>
				<td width="10%" align="center"><strong><?php print $yyDelete?></strong></td>
			  </tr>
<?php
	$bgcolor="";
	$sSQL = "SELECT cpnID,cpnWorkingName,cpnSitewide,cpnIsCoupon,cpnEndDate FROM coupons ORDER BY cpnIsCoupon,cpnWorkingName";
	$result = mysql_query($sSQL) or print(mysql_error());
	if(mysql_num_rows($result) > 0){
		while($alldata = mysql_fetch_row($result)){
			if($bgcolor=="#E7EAEF") $bgcolor="#FFFFFF"; else $bgcolor="#E7EAEF"; ?>
			  <tr bgcolor="<?php print $bgcolor?>">
				<td><?php print $alldata[1]?></td>
				<td align="center"><?php	if($alldata[3]==1) print $yyCoupon; else print $yyDisco;?></td>
				<td align="center"><?php	if($alldata[4]=='3000-01-01 00:00:00')
												print $yyNever;
											elseif(strtotime($alldata[4])-time() < 0)
												print '<font color="#FF0000">' . $yyExpird . '</font>';
											else
												print date("Y-m-d",strtotime($alldata[4])); ?></td>
				<td align="center"><?php if($alldata[2]==1 || $alldata[2]==2) print $yyYes; else print $yyNo; ?></td>
				<td align="center"><input type=button value="<?php print $yyModify?>" onclick="modrec('<?php print $alldata[0]?>')" /></td>
				<td align="center"><input type=button value="<?php print $yyDelete?>" onclick="delrec('<?php print $alldata[0]?>')" /></td>
			  </tr>
<?php	}
	}else{
?>
			  <tr> 
                <td width="100%" colspan="6" align="center"><br /><strong><?php print $yyNoDsc?><br />&nbsp;</td>
			  </tr>
<?php
	}
?>
			  <tr> 
                <td width="100%" colspan="6" align="center"><br /><strong><?php print $yyPOClk?> </strong>&nbsp;&nbsp;<input type="button" value="<?php print $yyNewDsc?>" onclick="newrec()" /><br />&nbsp;</td>
			  </tr>
			  <tr> 
                <td width="100%" colspan="6" align="center"><br />
                          <a href="admin.php"><strong><?php print $yyAdmHom?></strong></a><br />
				<img src="../images/clearpixel.gif" width="300" height="3" alt="" /></td>
			  </tr>
            </table></td>
		  </form>
        </tr>
<?php
}
?>
      </table>