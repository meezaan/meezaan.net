<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$storesessionvalue=="") $storesessionvalue="virtualstore".time();
if($_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE) exit;
if(@$dateadjust=="") $dateadjust=0;
if(@$dateformatstr == "") $dateformatstr = "m/d/Y";
$admindatestr="Y-m-d";
if(@$admindateformat=="") $admindateformat=0;
if($admindateformat==1)
	$admindatestr="m/d/Y";
elseif($admindateformat==2)
	$admindatestr="d/m/Y";
$success = TRUE;
$showaccount = TRUE;
$alreadygotadmin = getadminsettings();
if(@$_POST["editaction"]=="modify"){
	$sSQL = "UPDATE affiliates SET affilPW='" . mysql_escape_string(unstripslashes(trim(@$_POST["affilpw"]))) . "',";
		$sSQL .= "affilEmail='" . mysql_escape_string(unstripslashes(trim(@$_POST["email"]))) . "',";
		$sSQL .= "affilName='" . mysql_escape_string(unstripslashes(trim(@$_POST["name"]))) . "',";
		$sSQL .= "affilAddress='" . mysql_escape_string(unstripslashes(trim(@$_POST["address"]))) . "',";
		$sSQL .= "affilCity='" . mysql_escape_string(unstripslashes(trim(@$_POST["city"]))) . "',";
		$sSQL .= "affilState='" . mysql_escape_string(unstripslashes(trim(@$_POST["state"]))) . "',";
		$sSQL .= "affilCountry='" . mysql_escape_string(unstripslashes(trim(@$_POST["country"]))) . "',";
		$sSQL .= "affilZip='" . mysql_escape_string(unstripslashes(trim(@$_POST["zip"]))) . "',";
		if(trim(@$_POST["affilCommision"])=="")
			$sSQL .= "affilCommision=0,";
		else
			$sSQL .= "affilCommision=" . trim(@$_POST["affilCommision"]) . ",";
		if(trim(@$_POST["inform"])=="ON")
			$sSQL .= "affilInform=1 ";
		else
			$sSQL .= "affilInform=0 ";
		$sSQL .= "WHERE affilID='" . mysql_escape_string(unstripslashes(trim(@$_POST["affilid"]))) . "'";
		mysql_query($sSQL) or print(mysql_error());
}elseif(@$_POST["editaction"]=="delete"){
	$sSQL = "DELETE FROM affiliates WHERE affilID='" . mysql_escape_string(unstripslashes(trim(@$_POST["affilid"]))) . "'";
	mysql_query($sSQL) or print(mysql_error());
}
if(trim(@$_GET["id"]) != ""){
	$sSQL = "SELECT affilName,affilPW,affilAddress,affilCity,affilState,affilZip,affilCountry,affilEmail,affilInform,affilCommision FROM affiliates WHERE affilID='" . trim(@$_GET["id"]) . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	if($rs = mysql_fetch_array($result)){
		$affilName = $rs["affilName"];
		$affilPW = $rs["affilPW"];
		$affilAddress = $rs["affilAddress"];
		$affilCity = $rs["affilCity"];
		$affilState = $rs["affilState"];
		$affilZip = $rs["affilZip"];
		$affilCountry = $rs["affilCountry"];
		$affilEmail = $rs["affilEmail"];
		$affilInform = ((int)$rs["affilInform"])==1;
		$affilCommision = $rs["affilCommision"];
	}
	mysql_free_result($result);
?>
<script language="javascript" type="text/javascript">
<!--
function checkform(frm){
if(frm.affilid.value==""){
	alert("<?php print $yyPlsEntr?> \"<?php print $yyAffId?>\".");
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
    alert("<?php print $yyOnlyAl?> \"<?php print $yyAffId?>\" field.");
    frm.affilid.focus();
    return (false);
}
if(frm.affilpw.value==""){
	alert("<?php print $yyPlsEntr?> \"<?php print $yyPass?>\".");
	frm.affilpw.focus();
	return (false);
}
if(frm.name.value==""){
	alert("<?php print $yyPlsEntr?> \"<?php print $yyName?>\".");
	frm.name.focus();
	return (false);
}
if(frm.email.value==""){
	alert("<?php print $yyPlsEntr?> \"<?php print $yyEmail?>\".");
	frm.email.focus();
	return (false);
}
if(frm.address.value==""){
	alert("<?php print $yyPlsEntr?> \"<?php print $yyAddress?>\".");
	frm.address.focus();
	return (false);
}
if(frm.city.value==""){
	alert("<?php print $yyPlsEntr?> \"<?php print $yyCity?>\".");
	frm.city.focus();
	return (false);
}
if(frm.state.value==""){
	alert("<?php print $yyPlsEntr?> \"<?php print $yyState?>\".");
	frm.state.focus();
	return (false);
}
if(frm.zip.value==""){
	alert("<?php print $yyPlsEntr?> \"<?php print $yyZip?>\".");
	frm.zip.focus();
	return (false);
}
return (true);
}
//-->
</script>
	  <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr> 
          <td width="100%">
		    <form method="post" action="adminaffil.php" onsubmit="return checkform(this)">
			  <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
				<tr>
				  <td width="100%" align="center" colspan="4"><strong><?php print $yyAffAdm?></strong></td>
				</tr>
				<tr>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $yyAffId?>:</strong></td>
				  <td width="25%" align="left"><?php print htmlspecialchars(trim(@$_GET['id']))?>
					<input type="hidden" name="affilid" size="20" value="<?php print htmlspecialchars(trim(@$_GET['id']))?>" />
					<input type="hidden" name="editaction" value="modify" /></td>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $yyPass?>:</strong></td>
				  <td width="25%" align="left"><input type="text" name="affilpw" size="20" value="<?php print htmlspecialchars($affilPW)?>" /></td>
				</tr>
				<tr>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $yyName?>:</strong></td>
				  <td width="25%" align="left"><input type="text" name="name" size="20" value="<?php print htmlspecialchars($affilName)?>" /></td>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $yyEmail?>:</strong></td>
				  <td width="25%" align="left"><input type="text" name="email" size="25" value="<?php print htmlspecialchars($affilEmail)?>" /></td>
				</tr>
				<tr>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $yyAddress?>:</strong></td>
				  <td width="25%" align="left"><input type="text" name="address" size="20" value="<?php print htmlspecialchars($affilAddress)?>" /></td>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $yyCity?>:</strong></td>
				  <td width="25%" align="left"><input type="text" name="city" size="20" value="<?php print htmlspecialchars($affilCity)?>" /></td>
				</tr>
				<tr>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $yyState?>:</strong></td>
				  <td width="25%" align="left"><input type="text" name="state" size="20" value="<?php print htmlspecialchars($affilState)?>" /></td>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $yyCountry?>:</strong></td>
				  <td width="25%" align="left"><select name="country" size="1">
<?php
function show_countries($tcountry){
	$sSQL = "SELECT countryName FROM countries ORDER BY countryOrder DESC, countryName";
	$result = mysql_query($sSQL) or print(mysql_error());
	while($rs = mysql_fetch_array($result)){
		print "<option value='" . $rs["countryName"] . "'";
		if($tcountry==$rs["countryName"])
			print " selected";
		print ">" . $rs["countryName"] . "</option>\n";
	}
}
show_countries(@$affilCountry)
?>
					</select>
				  </td>
				</tr>
				<tr>
				  <td width="25%" align="right"><strong><font color='#FF0000'>*</font><?php print $yyZip?>:</strong></td>
				  <td width="25%" align="left"><input type="text" name="zip" size="10" value="<?php print htmlspecialchars($affilZip)?>" /></td>
				  <td width="25%" align="right"><strong>Inform me:</strong></td>
				  <td width="25%" align="left"><input type="checkbox" name="inform" value="ON" <?php if($affilInform) print "checked";?> /></td>
				</tr>
				<tr>
				  <td align="right"><strong><font color='#FF0000'></font><?php print $yyCommis?>:</strong></td>
				  <td colspan="3"><input type="text" name="affilCommision" size="6" value="<?php print htmlspecialchars($affilCommision)?>" />%</td>
				</tr>
				<tr>
				  <td width="100%" colspan="4">
					<font size="1"><ul><li><?php print $yyAffInf?></li></ul></font>
				  </td>
				</tr>
				<tr>
				  <td width="50%" align="center" colspan="4"><input type="submit" value="<?php print $yySubmit?>" /> <input type="reset" value="<?php print $yyReset?>" /></td>
				</tr>
			  </table>
			</form>
		  </td>
        </tr>
      </table>
<?php
}else{
	$thetime=time() + ($dateadjust*60*60);
	if(@$_POST["sd"] != "")
		$sd = @$_POST["sd"];
	elseif(@$_GET["sd"] != "")
		$sd = @$_GET["sd"];
	else
		$sd = date($admindatestr, mktime(0, 0, 0, date("m",$thetime), 1, date("Y",$thetime)));
	if(@$_POST["ed"] != "")
		$ed = @$_POST["ed"];
	elseif(@$_GET["ed"] != "")
		$ed = @$_GET["ed"];
	else
		$ed = date($admindatestr, $thetime);
	$sd = parsedate($sd);
	$ed = parsedate($ed);
	if($sd > $ed) $ed = $sd;
	$sSQL = "SELECT affilID,affilName,affilPW,affilEmail,affilCommision FROM affiliates ORDER BY affilID";
	$alldata = mysql_query($sSQL) or print(mysql_error());
?>
<script language="javascript" type="text/javascript">
<!--
function delrec(id) {
cmsg = "<?php print $yyConDel?>\n"
if (confirm(cmsg)) {
	document.mainform.affilid.value = id;
	document.mainform.editaction.value = "delete";
	document.mainform.submit();
}
}
function dumpinventory(){
	document.mainform.action="dumporders.php";
	document.mainform.act.value = "dumpaffiliate";
	document.mainform.submit();
}
// -->
</script>
	  <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr> 
          <td width="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="">
			  <tr>
				<td width="100%" align="center" colspan="6"><strong><?php print $yyAffAdm?></strong><br />&nbsp;</td>
			  </tr>
			  <form method="post" action="adminaffil.php">
			  <tr> 
                <td width="100%" colspan="6" align="center"><?php if(! $success) print "<p><font color='#FF0000'>" . $errmsg . "</font></p>" ?><br /><strong><?php print $yyAffBet?>:</strong> <input type="text" size="10" name="sd" value="<?php print date($admindatestr, $sd)?>" /> <strong><?php print $yyAnd?>:</strong> <input type="text" size="10" name="ed" value="<?php print date($admindatestr, $ed)?>" /> <input type="submit" value="Go" /><br />&nbsp;</td>
			  </tr>
			  </form>
			  <form method="post" action="adminaffil.php">
			  <tr> 
                <td width="100%" colspan="6" align="center"><p><strong><?php print $yyAffFrm?>:</strong> <select name="sd" size="1"><?php
					$thetime = time() + ($dateadjust*60*60);
					$dayToday = date("d",$thetime);
					$monthToday = date("m",$thetime);
					$yearToday = date("Y",$thetime);
					for($index=$dayToday; $index > 0; $index--){
						$thedate = mktime(0, 0, 0, $monthToday, $index, $yearToday);
						$thedatestr = date($admindatestr, $thedate);
						print "<option value='" . $thedatestr . "'";
						if($thedate==$sd) print " selected";
						print ">" . $thedatestr . "</option>\n";
					}
					for($index=1; $index<=12; $index++){
						$thedate = mktime(0,0,0,$monthToday-$index,1,$yearToday);
						$thedatestr = date($admindatestr, $thedate);
						print "<option value='" . $thedatestr . "'";
						if($thedate==$sd) print " selected";
						print ">" . $thedatestr . "</option>\n";
					}
				?></select> <strong><?php print $yyTo?>:</strong> <select name="ed" size="1"><?php
					$dayToday = date("d",$thetime);
					$monthToday = date("m",$thetime);
					$yearToday = date("Y",$thetime);
					for($index=$dayToday; $index > 0; $index--){
						$thedate = mktime(0, 0, 0, $monthToday, $index, $yearToday);
						$thedatestr = date($admindatestr, $thedate);
						print "<option value='" . $thedatestr . "'";
						if($thedate==$ed) print " selected";
						print ">" . $thedatestr . "</option>\n";
					}
					for($index=1; $index<=12; $index++){
						$thedate = mktime(0,0,0,$monthToday-$index,1,$yearToday);
						$thedatestr = date($admindatestr, $thedate);
						print "<option value='" . $thedatestr . "'";
						if($thedate==$ed) print " selected";
						print ">" . $thedatestr . "</option>\n";
					}
				?></select> <input type="submit" value="Go" /><br />&nbsp;</p></td>
			  </tr>
			  </form>
			  <form name="mainform" method="post" action="adminaffil.php">
				<tr>
				  <td><strong><?php print $yyAffId?></strong></td>
				  <td><strong><?php print $yyName?></strong></td>
				  <td><strong><?php print $yyEmail?></strong></td>
				  <td align="right"><strong><?php print $yyTotSal?></strong></td>
				  <td align="right"><strong><?php print $yyCommis?></strong></td>
				  <td align="center"><strong><?php print $yyDelete?></strong></td>
				</tr>
				<input type="hidden" name="affilid" value="xxx" />
				<input type="hidden" name="editaction" value="xxx" />
				<input type="hidden" name="act" value="xxxxx" />
				<input type="hidden" name="ed" value="<?php print date($admindatestr, $ed)?>" />
				<input type="hidden" name="sd" value="<?php print date($admindatestr, $sd)?>" />
<?php
	if(mysql_num_rows($alldata) == 0){
?>
				<tr>
				  <td width="100%" align="center" colspan="6"><br />&nbsp;<br /><strong><?php print $yyNoAff?></strong><br />&nbsp;</td>
				</tr>
<?php
	}else{
		while($rs=mysql_fetch_array($alldata)){ ?>
				<tr>
				  <td><a href="adminaffil.php?id=<?php print htmlspecialchars($rs[0])?>"><strong><?php print htmlspecialchars($rs[0])?></strong></a></td>
				  <td><?php print htmlspecialchars($rs[1])?></td>
				  <td><a href="mailto:<?php print htmlspecialchars($rs[3])?>"><?php print htmlspecialchars($rs[3])?></a></td>
				  <td align=right><?php
			$sSQL2 = "SELECT SUM(ordTotal-ordDiscount) FROM affiliates LEFT JOIN orders ON affiliates.affilID=orders.ordAffiliate WHERE affilID='" . $rs[0] . "' AND ordStatus>=3 AND ordDate BETWEEN '" . date("Y-m-d", $sd) . "' AND '" . date("Y-m-d", $ed) . " 23:59:59'";
			$alldata2 = mysql_query($sSQL2) or print(mysql_error());
			$rs2=mysql_fetch_array($alldata2);
			if(! is_numeric($rs2[0])){
				print "-";
				$thistotal=0.0;
			}else{
				print FormatEuroCurrency($rs2[0]);
				$thistotal=(double)$rs2[0];
			} ?></td>
				  <td align=right><?php if($thistotal==0 || $rs["affilCommision"]==0) print "-"; else print FormatEuroCurrency(($rs["affilCommision"]*$thistotal)/100.0); ?></td>
				  <td align="center"><input type=button name=delete value="Delete" onclick="delrec('<?php print str_replace("'","\'",htmlspecialchars($rs[0]))?>')" /></td>
				</tr>
<?php
		} ?>
				<tr> 
				  <td width="100%" colspan="6" align="center"><input type="button" value="Affiliate Report" onclick="dumpinventory()" /></td>
				</tr>
<?php
	}
?>
			  </form>
			</table>
		  </td>
        </tr>
      </table>
<?php
}
?>