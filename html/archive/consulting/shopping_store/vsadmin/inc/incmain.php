<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$storesessionvalue=="") $storesessionvalue="virtualstore".time();
if($_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE) exit;
$success=TRUE;
if(@$_POST["posted"]=="1"){
	$admintweaks=0;
	if(is_array(@$_POST["admintweaks"])){
		foreach(@$_POST["admintweaks"] as $objValue)
			$admintweaks += $objValue;
	}
	$adminlangsettings=0;
	if(is_array(@$_POST["adminlangsettings"])){
		foreach(@$_POST["adminlangsettings"] as $objValue)
			$adminlangsettings += $objValue;
	}

	$sSQL = "UPDATE admin SET adminEmail='" . @$_POST["email"] . "',adminStoreURL='" . @$_POST["url"] . "' WHERE adminID=1";
	mysql_query($sSQL) or print(mysql_error());
	$sSQL = "UPDATE admin SET adminEmail='" . @$_POST["email"] . "',adminStoreURL='" . @$_POST["url"] . "',adminProdsPerPage='" . @$_POST["prodperpage"] . "',adminShipping=" . @$_POST["shipping"] . ",adminIntShipping=" . @$_POST["intshipping"] . ",adminUSPSUser='" . @$_POST["USPSUser"] . "',adminZipCode='" . @$_POST["zipcode"] . "',adminCountry=" . @$_POST["countrySetting"] . ",adminDelUncompleted=" . @$_POST["deleteUncompleted"] . ",adminClearCart=" . @$_POST["adminClearCart"] . ",adminDelCC=" . @$_POST["adminDelCC"] . ",adminPacking=" . @$_POST["packing"] . ",adminStockManage=" . @$_POST["stockManage"] . ",adminHandling=" . @$_POST["handling"] . ",adminTweaks=" . $admintweaks . ",adminCanPostUser='" . @$_POST["adminCanPostUser"] . "',";
	if(@$_POST["emailconfirm"]=="ON")
		$sSQL .= "adminEmailConfirm=1, ";
	else
		$sSQL .= "adminEmailConfirm=0, ";
	$sSQL .= "adminUnits=" . ((int)@$_POST["adminUnits"] + (int)@$_POST["adminDims"]);
	for($index=1;$index<=3;$index++){
		if(! is_numeric(@$_POST["currRate" . $index]))
			$sSQL .= ",currRate" . $index . "=0";
		else
			$sSQL .= ",currRate" . $index . "=" . @$_POST["currRate" . $index];
		$sSQL .= ",currSymbol" . $index . "='" . @$_POST["currSymbol" . $index] . "'";
	}
	$sSQL .= ",currLastUpdate='" . date("Y-m-d H:i:s", time()-100000) . "'";
	$sSQL .= ",currConvUser='" . @$_POST["currConvUser"] . "'";
	$sSQL .= ",currConvPw='" . @$_POST["currConvPw"] . "'";
	$sSQL .= ",adminlanguages='" . @$_POST["adminlanguages"] . "'";
	$sSQL .= ",adminlangsettings='" . $adminlangsettings . "'";
	mysql_query($sSQL) or print(mysql_error());
	print "<meta http-equiv=\"refresh\" content=\"3; url=admin.php\">";
}else{
	$allcurrencies="";
	$numcurrencies=0;
	$sSQL = "SELECT DISTINCT countryCurrency FROM countries ORDER BY countryCurrency";
	$result = mysql_query($sSQL) or print(mysql_error());
	while($rs=mysql_fetch_array($result))
		$allcurrencies[$numcurrencies++]=$rs;
	mysql_free_result($result);
	$sSQL = "SELECT countryID,countryName FROM countries WHERE countryLCID<>'' ORDER BY countryOrder DESC, countryName";
	$rsCountry = mysql_query($sSQL) or print(mysql_error());
}
?>
<script language="javascript" type="text/javascript">
<!--
function formvalidator(theForm)
{
  if(theForm.prodperpage.value == "")
  {
    alert("<?php print $yyPlsEntr?> \"<?php print $yyPPP?>\".");
    theForm.prodperpage.focus();
    return (false);
  }
  var checkOK = "0123456789";
  var checkStr = theForm.prodperpage.value;
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
	alert("<?php print $yyOnlyNum?> \"<?php print $yyPPP?>\".");
	theForm.prodperpage.focus();
	return (false);
  }
for(index=1;index<=3;index++){
  var checkOK = "0123456789.";
  var thisRate = eval("theForm.currRate" + index);
  var checkStr = thisRate.value;
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
	alert("<?php print $yyOnlyDec?> \"<?php print $yyConRat?> " + index + "\".");
	thisRate.focus();
	return (false);
  }
}

  if(theForm.handling.value == "")
  {
    alert('<?php print $yyPlsEntr?> \"<?php print $yyHanChg?>\". <?php print $yyNoHan?>');
    theForm.handling.focus();
    return (false);
  }
  var checkOK = "0123456789.";
  var checkStr = theForm.handling.value;
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
	alert("<?php print $yyOnlyDec?> \"<?php print $yyHanChg?>\".");
	theForm.handling.focus();
	return (false);
  }
  return (true);
}
//-->
</script>
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
<?php if(@$_POST["posted"]=="1" && $success){ ?>
        <tr>
          <td width="100%">
            <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyUpdSuc?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
                        <?php print $yyNoAuto?> <A href="admin.php"><strong><?php print $yyClkHer?></strong></a>.<br />
                        <br />
				<img src="../images/clearpixel.gif" width="300" height="1" alt="" /></td>
			  </tr>
			</table></td>
        </tr>
<?php }else{
		$sSQL = "SELECT adminEmail,adminStoreURL,adminProdsPerPage,adminShipping,adminIntShipping,adminUSPSUser,adminZipCode,adminEmailConfirm,adminCountry,adminUnits,adminDelUncompleted,adminClearCart,adminPacking,adminStockManage,adminHandling,adminTweaks,adminDelCC,currRate1,currSymbol1,currRate2,currSymbol2,currRate3,currSymbol3,currConvUser,currConvPw,adminCanPostUser,adminlanguages,adminlangsettings FROM admin WHERE adminID=1";
		$result = mysql_query($sSQL) or print(mysql_error());
		$rsAdmin = mysql_fetch_assoc($result);
		mysql_free_result($result);
?>
        <tr>
		        <form method="post" action="adminmain.php" onsubmit="return formvalidator(this)">
                  <td width="100%">
			<input type="hidden" name="posted" value="1" />
            <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyUpdAdm?></strong><br />&nbsp;</td>
			  </tr>
	<?php	if(! $success){ ?>
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><font color="#FF0000"><?php print $errmsg?></font></td>
			  </tr>
	<?php	} ?>
			<tr>
				<td width="100%" align="center" colspan="2"><?php print $yyCsSym?></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyCouSet?>: </strong></td>
				<td width="50%" align="left"><select name="countrySetting" size="1">
				  <?php
					while($rs = mysql_fetch_assoc($rsCountry)){
						print "<option value='" . $rs["countryID"] . "'";
						if($rsAdmin["adminCountry"]==$rs["countryID"]) print " selected";
						print ">". $rs["countryName"] . "</option>\n";
					}
				  ?>
				  </select></td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%"><?php print $yy3CurCon?><br />
				  <font size="1"><?php print $yyNo3Con?></font></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyConv?> 1: </strong></td>
				<td width="50%" align="left">&nbsp;<?php print $yyRate?> <input type="text" name="currRate1" size="10" value="<?php if($rsAdmin["currRate1"] != 0) print $rsAdmin["currRate1"]?>" />&nbsp;&nbsp;&nbsp;Symbol <select name="currSymbol1" size="1"><option value="">None</option>
				  <?php	for($index=0; $index<$numcurrencies; $index++){
							print "<option value='" . $allcurrencies[$index][0] . "'";
							if($rsAdmin["currSymbol1"]==$allcurrencies[$index][0]) print " selected";
							print ">" . $allcurrencies[$index][0] . "</option>\n";
						} ?></select></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyConv?> 2: </strong></td>
				<td width="50%" align="left">&nbsp;<?php print $yyRate?> <input type="text" name="currRate2" size="10" value="<?php if($rsAdmin["currRate1"] != 0) print $rsAdmin["currRate2"]?>" />&nbsp;&nbsp;&nbsp;Symbol <select name="currSymbol2" size="1"><option value="">None</option>
				  <?php	for($index=0; $index<$numcurrencies; $index++){
							print "<option value='" . $allcurrencies[$index][0] . "'";
							if($rsAdmin["currSymbol2"]==$allcurrencies[$index][0]) print " selected";
							print ">" . $allcurrencies[$index][0] . "</option>\n";
						} ?></select></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyConv?> 3: </strong></td>
				<td width="50%" align="left">&nbsp;<?php print $yyRate?> <input type="text" name="currRate3" size="10" value="<?php if($rsAdmin["currRate1"] != 0) print $rsAdmin["currRate3"]?>" />&nbsp;&nbsp;&nbsp;Symbol <select name="currSymbol3" size="1"><option value="">None</option>
				  <?php	for($index=0; $index<$numcurrencies; $index++){
							print "<option value='" . $allcurrencies[$index][0] . "'";
							if($rsAdmin["currSymbol3"]==$allcurrencies[$index][0]) print " selected";
							print ">" . $allcurrencies[$index][0] . "</option>\n";
						} ?></select></td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><font size="1"><?php print $yyAutoLogin?></font></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyUname?>: </strong></td>
				<td width="50%" align="left"><input type="text" name="currConvUser" size="15" value="<?php print $rsAdmin["currConvUser"]?>" /></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyPass?>: </strong></td>
				<td width="50%" align="left"><input type="text" name="currConvPw" size="15" value="<?php print $rsAdmin["currConvPw"]?>" /></td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%"><?php print $yyLikeCE?></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyConEm?>: </strong></td>
				<td width="50%" align="left"><input type="checkbox" name="emailconfirm" value="ON" <?php if((int)($rsAdmin["adminEmailConfirm"])==1) print "checked"?> /></td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%"><?php print $yyCEAddr?></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyEmail?>: </strong></td>
				<td width="50%" align="left"><input type="text" name="email" size="30" value="<?php print $rsAdmin["adminEmail"]?>" /></td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%"><?php print $yyURLEx & " " & $yyExample?><br />
						<strong><?php
						$guessURL = "http://" . @$_SERVER["SERVER_NAME"] . @$_SERVER["REQUEST_URI"];
						$guessURL = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
						$wherevs = strpos(strtolower($guessURL),"vsadmin");
						if($wherevs > 0)
							$guessURL = substr($guessURL, 0, $wherevs);
						else
							$guessURL = "http://www.myurl.com/mystore/";
						print $guessURL;
						?></strong></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyStoreURL?>: </strong></td>
				<td width="50%" align="left"><input type="text" name="url" size="35" value="<?php print $rsAdmin["adminStoreURL"]?>" /></td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%"><?php print $yyHMPPP?></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyPPP?>: </strong></td>
				<td width="50%" align="left"><input type="text" name="prodperpage" size="10" value="<?php print $rsAdmin["adminProdsPerPage"]?>" /></td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%"><?php print $yyHandEx?></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyHanChg?>: </strong></td>
				<td width="50%" align="left"><input type="text" name="handling" size="10" value="<?php print $rsAdmin["adminHandling"]?>" /></td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%"><?php print $yySelShp?></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyShpTyp?>: </strong></td>
				<td width="50%" align="left"><select name="shipping" size="1">
					<option value="0"><?php print $yyNoShp?></option>
					<option value="1" <?php if((int)($rsAdmin["adminShipping"])==1) print "selected"?>><?php print $yyFlatShp?></option>
					<option value="2" <?php if((int)($rsAdmin["adminShipping"])==2) print "selected"?>><?php print $yyWghtShp?></option>
					<option value="5" <?php if((int)($rsAdmin["adminShipping"])==5) print "selected"?>><?php print $yyPriShp?></option>
					<option value="3" <?php if((int)($rsAdmin["adminShipping"])==3) print "selected"?>><?php print $yyUSPS?></option>
					<option value="4" <?php if((int)($rsAdmin["adminShipping"])==4) print "selected"?>><?php print $yyUPS?></option>
					<option value="6" <?php if((int)($rsAdmin["adminShipping"])==6) print "selected"?>><?php print $yyCanPos?></option>
					<option value="7" <?php if((int)($rsAdmin["adminShipping"])==7) print "selected"?>><?php print $yyFedex?></option>
					</select></td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%"><?php print $yySelShI?></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyShpTyp?>: </strong></td>
				<td width="50%" align="left"><select name="intshipping" size="1">
					<option value="0"><?php print $yySamDom?></option>
					<option value="1" <?php if((int)($rsAdmin["adminIntShipping"])==1) print "selected"?>><?php print $yyFlatShp?></option>
					<option value="2" <?php if((int)($rsAdmin["adminIntShipping"])==2) print "selected"?>><?php print $yyWghtShp?></option>
					<option value="5" <?php if((int)($rsAdmin["adminIntShipping"])==5) print "selected"?>><?php print $yyPriShp?></option>
					<option value="3" <?php if((int)($rsAdmin["adminIntShipping"])==3) print "selected"?>><?php print $yyUSPS?></option>
					<option value="4" <?php if((int)($rsAdmin["adminIntShipping"])==4) print "selected"?>><?php print $yyUPS?></option>
					<option value="6" <?php if((int)($rsAdmin["adminIntShipping"])==6) print "selected"?>><?php print $yyCanPos?></option>
					<option value="7" <?php if((int)($rsAdmin["adminIntShipping"])==7) print "selected"?>><?php print $yyFedex?></option>
					</select></td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%"><?php print $yyHowPck?><br /><font size="1"><?php print $yyOnlyAf?></font></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyPackPr?>: </strong></td>
				<td width="50%" align="left"><select name="packing" size="1">
					<option value="0"><?php print $yyPckSep?></option>
					<option value="1" <?php if((int)($rsAdmin["adminPacking"])==1) print "selected"?>><?php print $yyPckTog?></option>
					</select></td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%"><?php print $yyIfUSPS?><br />
				<font size="1"><?php print $yyUPSForm?> <a href="adminupslicense.php"><?php print $yyHere?></a>.</font></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyUname?>: </strong></td>
				<td width="50%" align="left"><input type="text" size="15" name="USPSUser" value="<?php print $rsAdmin["adminUSPSUser"]?>" /></td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%"><?php print $yyEnMerI?></font></td>
			  </tr>
			  <tr>
				<td colspan="2" align="center"><strong><?php print $yyRetID?>: </strong><input type="text" size="36" name="adminCanPostUser" value="<?php print $rsAdmin["adminCanPostUser"]?>" /></td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%"><?php print $yyEntZip?></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyZip?>: </strong></td>
				<td width="50%" align="left"><input type="text" name="zipcode" size="10" value="<?php print $rsAdmin["adminZipCode"]?>" /></td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%"><?php print $yyUPSUnt?></td>
			  </tr>
			  <tr>
				<td width="50%" align="center"><strong><?php print $yyShpUnt?>: </strong>
					<select name="adminUnits" size="1">
					<option value="1" <?php if(((int)$rsAdmin["adminUnits"] & 3)==1) print "selected"?>>LBS</option>
					<option value="0" <?php if(((int)$rsAdmin["adminUnits"] & 3)==0) print "selected"?>>KGS</option>
					</select></td>
				<td width="50%" align="center"><strong><?php print $yyDims?>: </strong>
					<select name="adminDims" size="1">
					<option value="0"><?php print $yyNotSpe?></option>
					<option value="4" <?php if(((int)$rsAdmin["adminUnits"] & 12)==4) print "selected"?>>IN</option>
					<option value="8" <?php if(((int)$rsAdmin["adminUnits"] & 12)==8) print "selected"?>>CM</option>
					</select></td>
			  </tr>
			  <tr>
				<td width="100%" align="left" colspan="2"><ul>
				  <li><font size="1"><font color="#FF0000">*</font><?php print $yyUntNote?></font></li>
				  <li><font size="1"><font color="#FF0000">*</font><?php print $yyUntNo2?></font></li></ul></td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%"><?php print $yyStkMgt?><br />
					<font size="1"><?php print $yyTimUnv?></font>
				</td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyConUnv?>: </strong></td>
				<td width="50%" align="left"><select name="stockManage" size="1">
					<option value="0"><?php print $yyNoStk?></option>
					<option value="1" <?php if((int)($rsAdmin["adminStockManage"])==1) print "selected"?>>1 <?php print $yyHours?></option>
					<option value="2" <?php if((int)($rsAdmin["adminStockManage"])==2) print "selected"?>>2 <?php print $yyHours?></option>
					<option value="3" <?php if((int)($rsAdmin["adminStockManage"])==3) print "selected"?>>3 <?php print $yyHours?></option>
					<option value="4" <?php if((int)($rsAdmin["adminStockManage"])==4) print "selected"?>>4 <?php print $yyHours?></option>
					<option value="6" <?php if((int)($rsAdmin["adminStockManage"])==6) print "selected"?>>6 <?php print $yyHours?></option>
					<option value="8" <?php if((int)($rsAdmin["adminStockManage"])==8) print "selected"?>>8 <?php print $yyHours?></option>
					<option value="12" <?php if((int)($rsAdmin["adminStockManage"])==12) print "selected"?>>12 <?php print $yyHours?></option>
					<option value="24" <?php if((int)($rsAdmin["adminStockManage"])==24) print "selected"?>>24 <?php print $yyHours?></option>
					</select>
				</td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%" /><?php print $yyHowLan?></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyNumLan?>: </strong></td>
				<td width="50%" align="left"><select name="adminlanguages" size="1">
					<option value="0">1</option>
					<option value="1" <?php if((int)($rsAdmin["adminlanguages"])==1) print "selected"?>>2</option>
					<option value="2" <?php if((int)($rsAdmin["adminlanguages"])==2) print "selected"?>>3</option>
					</select>
				</td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%" /><?php print $yyWhMull?><br />
					<font size="1"><?php print $yyLonrel?></font></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyLaSet?>: </strong></td>
				<td width="50%" align="left"><select name="adminlangsettings[]" size="5" multiple>
					<option value="1" <?php if(((int)$rsAdmin["adminlangsettings"] & 1)==1) print "selected"?>><?php print $yyPrName?></option>
					<option value="2" <?php if(((int)$rsAdmin["adminlangsettings"] & 2)==2) print "selected"?>><?php print $yyDesc?></option>
					<option value="4" <?php if(((int)$rsAdmin["adminlangsettings"] & 4)==4) print "selected"?>><?php print $yyLnDesc?></option>
					<option value="8" <?php if(((int)$rsAdmin["adminlangsettings"] & 8)==8) print "selected"?>><?php print $yyCntNam?></option>
					<option value="16" <?php if(((int)$rsAdmin["adminlangsettings"] & 16)==16) print "selected"?>><?php print $yyPOName?></option>
					<option value="32" <?php if(((int)$rsAdmin["adminlangsettings"] & 32)==32) print "selected"?>><?php print $yyPOChoi?></option>
					<option value="64" <?php if(((int)$rsAdmin["adminlangsettings"] & 64)==64) print "selected"?>><?php print $yyOrdSta?></option>
					<option value="128" <?php if(((int)$rsAdmin["adminlangsettings"] & 128)==128) print "selected"?>><?php print $yyPayMet?></option>
					<option value="256" <?php if(((int)$rsAdmin["adminlangsettings"] & 256)==256) print "selected"?>><?php print $yyCatNam?></option>
					<option value="512" <?php if(((int)$rsAdmin["adminlangsettings"] & 512)==512) print "selected"?>><?php print $yyCatDes?></option>
					<option value="1024" <?php if(((int)$rsAdmin["adminlangsettings"] & 1024)==1024) print "selected"?>><?php print $yyDisTxt?></option>
					</select>
					</td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%"><?php print $yyDelUnc?></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyDelAft?>: </strong></td>
				<td width="50%" align="left"><select name="deleteUncompleted" size="1">
					<option value="0"><?php print $yyNever?></option>
					<option value="1" <?php if((int)($rsAdmin["adminDelUncompleted"])==1) print "selected"?>>1 <?php print $yyDay?></option>
					<option value="2" <?php if((int)($rsAdmin["adminDelUncompleted"])==2) print "selected"?>>2 <?php print $yyDays?></option>
					<option value="3" <?php if((int)($rsAdmin["adminDelUncompleted"])==3) print "selected"?>>3 <?php print $yyDays?></option>
					<option value="4" <?php if((int)($rsAdmin["adminDelUncompleted"])==4) print "selected"?>>4 <?php print $yyDays?></option>
					<option value="7" <?php if((int)($rsAdmin["adminDelUncompleted"])==7) print "selected"?>>1 <?php print $yyWeek?></option>
					<option value="14" <?php if((int)($rsAdmin["adminDelUncompleted"])==14) print "selected"?>>2 <?php print $yyWeeks?></option>
					</select>
					</td>
			  </tr>
<?php		if(@$enableclientlogin==TRUE){ ?>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%" /><?php print $yyRemLII?></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyDelAft?>: </strong></td>
				<td width="50%" align="left"><select name="adminClearCart" size="1">
					<option value="0"><?php print $yyNever?></option>
					<option value="14" <?php if((int)$rsAdmin['adminClearCart']==14) print 'selected'?>>2 <?php print $yyWeek?></option>
					<option value="28" <?php if((int)$rsAdmin['adminClearCart']==28) print 'selected'?>>4 <?php print $yyWeek?></option>
					<option value="70" <?php if((int)$rsAdmin['adminClearCart']==70) print 'selected'?>>10 <?php print $yyWeek?></option>
					<option value="140" <?php if((int)$rsAdmin['adminClearCart']==140) print 'selected'?>>20 <?php print $yyWeek?></option>
					<option value="210" <?php if((int)$rsAdmin['adminClearCart']==210) print 'selected'?>>30 <?php print $yyWeek?></option>
					<option value="364" <?php if((int)$rsAdmin['adminClearCart']==364) print 'selected'?>>52 <?php print $yyWeek?></option>
					<option value="525" <?php if((int)$rsAdmin['adminClearCart']==525) print 'selected'?>>75 <?php print $yyWeek?></option>
					<option value="728" <?php if((int)$rsAdmin['adminClearCart']==728) print 'selected'?>>104 <?php print $yyWeek?></option>
					</select>
					</td>
			  </tr>
<?php		}else{
				writehiddenvar('adminClearCart',$rsAdmin['adminClearCart']);
			} ?>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%"><?php print $yyDelCC?></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyDelAft?>: </strong></td>
				<td width="50%" align="left"><select name="adminDelCC" size="1">
					<option value="0"><?php print $yyNever?></option>
					<option value="1" <?php if((int)($rsAdmin["adminDelCC"])==1) print "selected"?>>1 <?php print $yyDay?></option>
					<option value="2" <?php if((int)($rsAdmin["adminDelCC"])==2) print "selected"?>>2 <?php print $yyDays?></option>
					<option value="3" <?php if((int)($rsAdmin["adminDelCC"])==3) print "selected"?>>3 <?php print $yyDays?></option>
					<option value="4" <?php if((int)($rsAdmin["adminDelCC"])==4) print "selected"?>>4 <?php print $yyDays?></option>
					<option value="7" <?php if((int)($rsAdmin["adminDelCC"])==7) print "selected"?>>1 <?php print $yyWeek?></option>
					<option value="14" <?php if((int)($rsAdmin["adminDelCC"])==14) print "selected"?>>2 <?php print $yyWeeks?></option>
					</select>
					</td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%"><?php print $yyAdmTwk?><br /><font size="1"><?php print $yyMulSel?></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyApTwk?>: </strong></td>
				<td width="50%" align="left"><select name="admintweaks[]" size="3" multiple>
					<option value="1" <?php if(((int)$rsAdmin["adminTweaks"] & 1)==1) print "selected"?>><?php print $yySmpCnt?></option>
					<option value="2" <?php if(((int)$rsAdmin["adminTweaks"] & 2)==2) print "selected"?>><?php print $yySmpOpt?></option>
					<option value="4" <?php if(((int)$rsAdmin["adminTweaks"] & 4)==4) print "selected"?>><?php print $yySmpSec?></option>
					</select>
					</td>
			  </tr>
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><input type="submit" value="<?php print $yySubmit?>" />&nbsp; &nbsp;<input type="reset" value="<?php print $yyReset?>" /><br />&nbsp;</td>
			  </tr>
            </table></td>
		  </form>
        </tr>
<?php } ?>
      </table>