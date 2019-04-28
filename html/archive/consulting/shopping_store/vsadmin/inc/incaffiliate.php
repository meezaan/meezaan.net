<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$_SERVER['CONTENT_LENGTH'] != '' && $_SERVER['CONTENT_LENGTH'] > 10000) exit;
$addsuccess = TRUE;
$success = TRUE;
$showaccount = TRUE;
if(@$_POST["editaction"] != ""){
	if(@$_POST["editaction"]=="modify"){
		$sSQL = "UPDATE affiliates SET affilPW='" . mysql_escape_string(unstripslashes(trim(@$_POST["affilpw"]))) . "',";
		$sSQL .= "affilEmail='" . mysql_escape_string(unstripslashes(trim(@$_POST["email"]))) . "',";
		$sSQL .= "affilName='" . mysql_escape_string(unstripslashes(trim(@$_POST["name"]))) . "',";
		$sSQL .= "affilAddress='" . mysql_escape_string(unstripslashes(trim(@$_POST["address"]))) . "',";
		$sSQL .= "affilCity='" . mysql_escape_string(unstripslashes(trim(@$_POST["city"]))) . "',";
		$sSQL .= "affilState='" . mysql_escape_string(unstripslashes(trim(@$_POST["state"]))) . "',";
		$sSQL .= "affilCountry='" . mysql_escape_string(unstripslashes(trim(@$_POST["country"]))) . "',";
		$sSQL .= "affilZip='" . mysql_escape_string(unstripslashes(trim(@$_POST["zip"]))) . "',";
		if(trim(@$_POST["inform"])=="ON")
			$sSQL .= "affilInform=1 ";
		else
			$sSQL .= "affilInform=0 ";
		$sSQL .= "WHERE affilID='" . mysql_escape_string(trim(@$_POST["affilid"])) . "'";
		mysql_query($sSQL) or print(mysql_error());
	}elseif(@$_POST["editaction"]=="new"){
		$sSQL = "SELECT affilID FROM affiliates WHERE affilID='" . mysql_escape_string(trim(@$_POST["affilid"])) . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if(mysql_num_rows($result) > 0) $addsuccess = FALSE;
		if($addsuccess){
			$sSQL = "INSERT INTO affiliates (affilID,affilPW,affilEmail,affilName,affilAddress,affilCity,affilState,affilCountry,affilZip,affilCommision,affilInform) VALUES (";
			$sSQL .= "'" . mysql_escape_string(unstripslashes(trim(@$_POST["affilid"]))) . "',";
			$sSQL .= "'" . mysql_escape_string(unstripslashes(trim(@$_POST["affilpw"]))) . "',";
			$sSQL .= "'" . mysql_escape_string(unstripslashes(trim(@$_POST["email"]))) . "',";
			$sSQL .= "'" . mysql_escape_string(unstripslashes(trim(@$_POST["name"]))) . "',";
			$sSQL .= "'" . mysql_escape_string(unstripslashes(trim(@$_POST["address"]))) . "',";
			$sSQL .= "'" . mysql_escape_string(unstripslashes(trim(@$_POST["city"]))) . "',";
			$sSQL .= "'" . mysql_escape_string(unstripslashes(trim(@$_POST["state"]))) . "',";
			$sSQL .= "'" . mysql_escape_string(unstripslashes(trim(@$_POST["country"]))) . "',";
			$sSQL .= "'" . mysql_escape_string(unstripslashes(trim(@$_POST["zip"]))) . "',";
			if(@$defaultcommission!=""){
				$sSQL .= $defaultcommission . ",";
				$_SESSION["affilCommision"]=(double)$defaultcommission;
			}else{
				$sSQL .= "0,";
				$_SESSION["affilCommision"]=0;
			}
			if(trim(@$_POST["inform"])=="ON")
				$sSQL .= "1) ";
			else
				$sSQL .= "0) ";
			mysql_query($sSQL) or print(mysql_error());
			print '<meta http-equiv="Refresh" content="0; URL=affiliate.php">';
		}
	}
	if($addsuccess){
		$_SESSION["xaffilid"] = unstripslashes(trim(@$_POST["affilid"]));
		$_SESSION["xaffilpw"] = unstripslashes(trim(@$_POST["affilpw"]));
		$_SESSION["xaffilName"] = unstripslashes(trim(@$_POST["name"]));
	}
}elseif(@$_POST["affillogin"] != ""){
	$sSQL = "SELECT affilID,affilName,affilCommision FROM affiliates WHERE affilID='" . mysql_escape_string(trim(@$_POST["affilid"])) . "' AND affilPW='" . mysql_escape_string(trim(@$_POST["affilpw"])) . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	if(mysql_num_rows($result)>0){
		$rs = mysql_fetch_assoc($result);
		$_SESSION["xaffilid"] = unstripslashes(trim(@$_POST["affilid"]));
		$_SESSION["xaffilpw"] = unstripslashes(trim(@$_POST["affilpw"]));
		$_SESSION["xaffilName"] = $rs["affilName"];
		$_SESSION["affilCommision"] = (double)$rs["affilCommision"];
		$showaccount=FALSE;
	}else
		$success=FALSE;
	mysql_free_result($result);
	if($success){
		print '<meta http-equiv="Refresh" content="3; URL=affiliate.php">';
?>
	  <table border="0" cellspacing="<?php print $maintablespacing?>" cellpadding="<?php print $maintablepadding?>" width="<?php print $maintablewidth?>" bgcolor="<?php print $maintablebg?>" align="center">
        <tr> 
          <td width="100%">
		    <form method="post" action="affiliate.php">
			  <table width="<?php print $innertablewidth?>" border="0" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
				<tr>
				  <td width="100%" align="center" colspan="2"><strong><?php print $xxAffPrg . " " . $xxWelcom . " " . htmlspecialchars($_SESSION['xaffilName'])?>.</strong></td>
				</tr>
				<tr>
				  <td width="100%" align="center" colspan="2">&nbsp;</td>
				</tr>
				<tr>
				  <td width="100%" align="center" colspan="2"><p><?php print $xxAffLog?></p>
					<p><?php print $xxForAut?> <a href="affiliate.php"><strong><?php print $xxClkHere?></strong></a>.</p></td>
				</tr>
			  </table>
			</form>
		  </td>
        </tr>
      </table>
<?php
	}
}elseif(@$_POST["logout"] != ""){
	$_SESSION["xaffilid"] = "";
	$_SESSION["xaffilpw"] = "";
	$_SESSION["xaffilName"] = "";
}
if(@$_POST["newaffil"]=="Go" || (@$_POST["editaffil"]!="" && trim(@$_SESSION["xaffilid"]) != "") || ! $addsuccess){
	$showaccount=FALSE;
?>
<script language="javascript" type="text/javascript">
<!--
function checkform(frm){
if(frm.affilid.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxAffID?>\".");
	frm.affilid.focus();
	return (false);
}
var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
var checkStr = frm.affilid.value;
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
if (!allValid){
    alert("<?php print $xxAlphaNu?> \"<?php print $xxAffID?>\".");
    frm.affilid.focus();
    return (false);
}
if(frm.affilpw.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxPwd?>\".");
	frm.affilpw.focus();
	return (false);
}
if(frm.name.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxName?>\".");
	frm.name.focus();
	return (false);
}
if(frm.email.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxEmail?>\".");
	frm.email.focus();
	return (false);
}
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
if(frm.state.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxAllSta?>\".");
	frm.state.focus();
	return (false);
}
if(frm.zip.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxZip?>\".");
	frm.zip.focus();
	return (false);
}
return (true);
}
//-->
</script>
<?php
	$sAffilName = "";
	$sAffilPW = "";
	$sAffilid = "";
	$sAffilAddress = "";
	$sAffilCity = "";
	$sAffilState = "";
	$sAffilZip = "";
	$sAffilCountry = "";
	$sAffilEmail = "";
	$sAffilInform = FALSE;
	if(! $addsuccess){
		$sAffilName = unstripslashes(@$_POST["name"]);
		$sAffilPW = unstripslashes(@$_POST["affilpw"]);
		$sAffilid = unstripslashes(@$_POST["affilid"]);
		$sAffilAddress = unstripslashes(@$_POST["address"]);
		$sAffilCity = unstripslashes(@$_POST["city"]);
		$sAffilState = unstripslashes(@$_POST["state"]);
		$sAffilZip = unstripslashes(@$_POST["zip"]);
		$sAffilCountry = unstripslashes(@$_POST["country"]);
		$sAffilEmail = unstripslashes(@$_POST["email"]);
		$sAffilInform = trim(@$_POST["inform"])=="ON";
	}elseif(@$_POST["editaffil"] != "" && trim(@$_SESSION["xaffilid"]) != ""){
		$sSQL = "SELECT affilName,affilPW,affilAddress,affilCity,affilState,affilZip,affilCountry,affilEmail,affilInform FROM affiliates WHERE affilID='" . mysql_escape_string(trim(@$_SESSION["xaffilid"])) . "' AND affilPW='" . mysql_escape_string(trim(@$_SESSION["xaffilpw"])) . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_array($result)){
			$sAffilName = $rs["affilName"];
			$sAffilPW = $rs["affilPW"];
			$sAffilAddress = $rs["affilAddress"];
			$sAffilCity = $rs["affilCity"];
			$sAffilState = $rs["affilState"];
			$sAffilZip = $rs["affilZip"];
			$sAffilCountry = $rs["affilCountry"];
			$sAffilEmail = $rs["affilEmail"];
			$sAffilInform = ((int)$rs["affilInform"])==1;
		}
		mysql_free_result($result);
	}
?>
	  <table border="0" cellspacing="<?php print $maintablespacing?>" cellpadding="<?php print $maintablepadding?>" width="<?php print $maintablewidth?>" bgcolor="<?php print $maintablebg?>" align="center">
        <tr> 
          <td width="100%">
		    <form method="post" action="affiliate.php" onsubmit="return checkform(this)">
			  <table width="<?php print $innertablewidth?>" border="0" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
				<tr>
				  <td width="100%" align="center" colspan="4"><strong><?php print $xxAffDts?></strong></td>
				</tr>
<?php if(! $addsuccess){ ?>
				<tr>
				  <td width="100%" align="center" colspan="4"><strong><font color='#FF0000'><?php print $xxAffUse?></font></strong></td>
				</tr>
<?php } ?>
				<tr>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $xxAffID?>:</strong></td>
				  <td width="25%" align="left"><?php
					if(@$_POST['editaffil'] != '' && trim(@$_SESSION['xaffilid']) != ''){
						print htmlspecialchars(trim(@$_SESSION['xaffilid']));
						?><input type="hidden" name="affilid" size="20" value="<?php print htmlspecialchars(trim(@$_SESSION['xaffilid']))?>" />
						  <input type="hidden" name="editaction" value="modify" /><?php
					}else{
						?><input type="text" name="affilid" size="20" value="<?php print $sAffilid?>" />
						  <input type="hidden" name="editaction" value="new" /><?php
					} ?></td>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $xxPwd?>:</strong></td>
				  <td width="25%" align="left"><input type="password" name="affilpw" size="20" value="<?php print $sAffilPW?>" /></td>
				</tr>
				<tr>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $xxName?>:</strong></td>
				  <td width="25%" align="left"><input type="text" name="name" size="20" value="<?php print htmlspecialchars($sAffilName)?>" /></td>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $xxEmail?>:</strong></td>
				  <td width="25%" align="left"><input type="text" name="email" size="25" value="<?php print htmlspecialchars($sAffilEmail)?>" /></td>
				</tr>
				<tr>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $xxAddress?>:</strong></td>
				  <td width="25%" align="left"><input type="text" name="address" size="20" value="<?php print htmlspecialchars($sAffilAddress)?>" /></td>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $xxCity?>:</strong></td>
				  <td width="25%" align="left"><input type="text" name="city" size="20" value="<?php print htmlspecialchars($sAffilCity)?>" /></td>
				</tr>
				<tr>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $xxAllSta?>:</strong></td>
				  <td width="25%" align="left"><input type="text" name="state" size="20" value="<?php print htmlspecialchars($sAffilState)?>" /></td>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $xxCountry?>:</strong></td>
				  <td width="25%" align="left"><select name="country" size="1">
<?php
function show_countries($tcountry){
	$sSQL = "SELECT countryName,countryOrder,".getlangid('countryName',8)." FROM countries ORDER BY countryOrder DESC," . getlangid("countryName",8);
	$result = mysql_query($sSQL) or print(mysql_error());
	while($rs = mysql_fetch_array($result)){
		print "<option value='" . $rs['countryName'] . "'";
		if($tcountry==$rs['countryName'])
			print ' selected';
		print '>' . $rs[2] . "</option>\n";
	}
}
show_countries(@$sAffilCountry)
?>
					</select>
				  </td>
				</tr>
				<tr>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $xxZip?>:</strong></td>
				  <td width="25%" align="left"><input type="text" name="zip" size="10" value="<?php print htmlspecialchars($sAffilZip)?>" /></td>
				  <td width="25%" align="right"><strong><?php print $xxInfMe?>:</strong></td>
				  <td width="25%" align="left"><input type="checkbox" name="inform" value="ON" <?php if($sAffilInform) print "checked"?> /></td>
				</tr>
				<tr>
				  <td width="100%" colspan="4">
					<font size="1"><ul><li><?php print $xxInform?></li></ul></font>
				  </td>
				</tr>
				<tr>
				  <td width="50%" align="center" colspan="4"><input type="submit" value="<?php print $xxSubmt?>" /> <input type="reset" value="Reset" /> <?php
					if(@$_POST['editaffil'] != '' && trim(@$_SESSION['xaffilid']) != ''){
					  ?><br /><br /><input type="button" value="<?php print $xxBack?>" onclick="javascript:history.go(-1)" /><?php
					} ?></td>
				</tr>
			  </table>
			</form>
		  </td>
        </tr>
      </table>
<?php
}
if($showaccount){
	if(@$_SESSION['xaffilid']==''){
?>
	  <table border="0" cellspacing="<?php print $maintablespacing?>" cellpadding="<?php print $maintablepadding?>" width="<?php print $maintablewidth?>" bgcolor="<?php print $maintablebg?>" align="center">
        <tr> 
          <td width="100%">
			<table width="<?php print $innertablewidth?>" border="0" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
			  <form method="post" action="affiliate.php">
				<tr>
				  <td width="100%" align="center" colspan="2"><strong><?php print $xxAffPrg?></strong></td>
				</tr>
				<tr>
				  <td width="100%" align="center" colspan="2">&nbsp;</td>
				</tr>
				<tr>
				  <td width="50%" align="right"><?php print $xxNewAct?>:</td>
				  <td><input type="submit" name="newaffil" value="Go" /></td>
				</tr>
			  </form>
			  <form method="post" action="affiliate.php">
				<tr>
				  <td width="100%" align="center" colspan="2">&nbsp;</td>
				</tr>
				<tr>
				  <td width="100%" align="center" colspan="2"><strong><?php print $xxGotAct?></strong></td>
				</tr>
<?php if(! $success){ ?>
				<tr>
				  <td width="100%" align="center" colspan="2"><font color="#FF0000"><?php print $xxAffNo?></font></td>
				</tr>
<?php } ?>
				<tr>
				  <td width="50%" align="right"><?php print $xxAffID?>:</td>
				  <td><input type="text" name="affilid" size="20" value="<?php print unstripslashes(trim(@$_POST["affilid"]))?>" /></td>
				</tr>
				<tr>
				  <td width="50%" align="right"><?php print $xxPwd?>:</td>
				  <td><input type="password" name="affilpw" size="20" value="<?php print unstripslashes(trim(@$_POST["affilpw"]))?>" /></td>
				</tr>
				<tr>
				  <td width="100%" align="center" colspan="2"><input type="submit" name="affillogin" value="<?php print $xxAffLI?>" /></td>
				</tr>
			  </form>
			</table>
		  </td>
        </tr>
      </table>
<?php
	}else{
		$lastmonth = mktime (0,0,0,date("m")-1,date("d"), date("Y"));
		$totalDay=0.0;
		$totalYesterday=0.0;
		$totalMonth=0.0;
		$totalLastMonth=0.0;
		
		$sSQL = "SELECT Sum(ordTotal-ordDiscount) as theCount FROM orders WHERE ordStatus>=3 AND ordAffiliate='" . mysql_escape_string(trim(@$_SESSION["xaffilid"])) . "' AND ordDate BETWEEN '" . date("Y-m-d") . "' AND '" . date("Y-m-d") . " 23:59:59'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_assoc($result))
			$totalDay = $rs["theCount"];
		mysql_free_result($result);
		$sSQL = "SELECT Sum(ordTotal-ordDiscount) as theCount FROM orders WHERE ordStatus>=3 AND ordAffiliate='" . mysql_escape_string(trim(@$_SESSION["xaffilid"])) . "' AND ordDate BETWEEN '" . date("Y-m-d", time()-(60*60*24)) . "' AND '" . date("Y-m-d") . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_assoc($result))
			$totalYesterday = $rs["theCount"];
		mysql_free_result($result);
		$sSQL = "SELECT Sum(ordTotal-ordDiscount) as theCount FROM orders WHERE ordStatus>=3 AND ordAffiliate='" . mysql_escape_string(trim(@$_SESSION["xaffilid"])) . "' AND ordDate BETWEEN '" . date("Y-m-01") . "' AND '" . date("Y-m-d") . " 23:59:59'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_assoc($result))
			$totalMonth = $rs["theCount"];
		mysql_free_result($result);
		$sSQL = "SELECT Sum(ordTotal-ordDiscount) as theCount FROM orders WHERE ordStatus>=3 AND ordAffiliate='" . mysql_escape_string(trim(@$_SESSION["xaffilid"])) . "' AND ordDate BETWEEN '" . date("Y-m-01", $lastmonth) . "' AND '" . date("Y-m-01") . " 00:00:00'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_assoc($result))
			$totalLastMonth = $rs["theCount"];
		mysql_free_result($result);
		if(is_null($totalDay)) $totalDay=0.0;
		if(is_null($totalYesterday)) $totalYesterday=0.0;
		if(is_null($totalMonth)) $totalMonth=0.0;
		if(is_null($totalLastMonth)) $totalLastMonth=0.0;
		$alreadygotadmin = getadminsettings();
?>
	  <table border="0" cellspacing="<?php print $maintablespacing?>" cellpadding="<?php print $maintablepadding?>" width="<?php print $maintablewidth?>" bgcolor="<?php print $maintablebg?>" align="center">
        <tr> 
          <td width="100%">
		    <form method="post" action="affiliate.php">
			  <table width="<?php print $innertablewidth?>" border="0" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
				<tr>
				  <td width="100%" align="center" colspan="2"><strong><?php print $xxAffPrg . ' ' . $xxWelcom . ' ' . htmlspecialchars(@$_SESSION['xaffilName'])?>.</strong></td>
				</tr>
				<tr>
				  <td width="100%" align="center" colspan="2">&nbsp;</td>
				</tr>
				<tr>
				  <td width="50%" align="right"><strong><?php print $xxTotTod?>:</strong></td>
				  <td width="50%"><?php print FormatEuroCurrency($totalDay);
				  if($_SESSION["affilCommision"]!=0) print ' = ' . FormatEuroCurrency(($totalDay * $_SESSION["affilCommision"]) / 100.0) . ' <strong>' . $xxCommis . "</strong>";?></td>
				</tr>
				<tr>
				  <td width="50%" align="right"><strong><?php print $xxTotYes?>:</strong></td>
				  <td width="50%"><?php print FormatEuroCurrency($totalYesterday);
				  if($_SESSION["affilCommision"]!=0) print ' = ' . FormatEuroCurrency(($totalYesterday * $_SESSION["affilCommision"]) / 100.0) . ' <strong>' . $xxCommis . "</strong>";?></td>
				</tr>
				<tr>
				  <td width="50%" align="right"><strong><?php print $xxTotMTD?>:</strong></td>
				  <td width="50%"><?php print FormatEuroCurrency($totalMonth);
				  if($_SESSION["affilCommision"]!=0) print ' = ' . FormatEuroCurrency(($totalMonth * $_SESSION["affilCommision"]) / 100.0) . ' <strong>' . $xxCommis . "</strong>";?></td>
				</tr>
				<tr>
				  <td width="50%" align="right"><strong><?php print $xxTotLM?>:</strong></td>
				  <td width="50%"><?php print FormatEuroCurrency($totalLastMonth);
				  if($_SESSION["affilCommision"]!=0) print ' = ' . FormatEuroCurrency(($totalLastMonth * $_SESSION["affilCommision"]) / 100.0) . ' <strong>' . $xxCommis . "</strong>";?></td>
				</tr>
				<tr>
				  <td width="100%" align="center" colspan="2">&nbsp;</td>
				</tr>
				<tr>
				  <td width="100%" align="center" colspan="2"><input type="submit" name="editaffil" value="<?php print $xxEdtAff?>" /></td>
				</tr>
				<tr>
				  <td width="100%" align="center" colspan="2">&nbsp;</td>
				</tr>
				<tr>
				  <td width="100%" colspan="2"><font size="1">
				    <ul>
					  <li><?php print $xxAffLI1?> <strong>products.php?PARTNER=<?php print htmlspecialchars(trim(@$_SESSION['xaffilid']))?></strong></li>
					  <li><?php print $xxAffLI2?></li>
					  <?php if($_SESSION["affilCommision"]==0){ ?>
					  <li><?php print $xxAffLI3?></li>
					  <?php } ?>
					</ul></font></td>
				</tr>
				<tr>
				  <td width="100%" align="center" colspan="2"><input type="submit" name="logout" value="Logout" /></td>
				</tr>
			  </table>
			</form>
		  </td>
        </tr>
      </table>
<?php
	}
}
?>