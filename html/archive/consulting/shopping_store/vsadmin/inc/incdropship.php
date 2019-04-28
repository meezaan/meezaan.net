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
$addsuccess = TRUE;
$success = TRUE;
$showaccount = TRUE;
$dorefresh = FALSE;
$alreadygotadmin = getadminsettings();
if(@$_POST["act"]=="domodify"){
	$sSQL = "UPDATE dropshipper SET dsEmail='" . mysql_escape_string(unstripslashes(trim(@$_POST["email"]))) . "'," .
		"dsName='" . mysql_escape_string(unstripslashes(trim(@$_POST["name"]))) . "'," .
		"dsAddress='" . mysql_escape_string(unstripslashes(trim(@$_POST["address"]))) . "'," .
		"dsCity='" . mysql_escape_string(unstripslashes(trim(@$_POST["city"]))) . "'," .
		"dsState='" . mysql_escape_string(unstripslashes(trim(@$_POST["state"]))) . "'," .
		"dsCountry='" . mysql_escape_string(unstripslashes(trim(@$_POST["country"]))) . "'," .
		"dsZip='" . mysql_escape_string(unstripslashes(trim(@$_POST["zip"]))) . "'," .
		"dsAction=" . mysql_escape_string(unstripslashes(trim(@$_POST["dsAction"]))) . " " .
		"WHERE dsID=" . mysql_escape_string(unstripslashes(trim(@$_POST["dsID"])));
	mysql_query($sSQL) or print(mysql_error());
	$dorefresh=TRUE;
}elseif(@$_POST["act"]=="doaddnew"){
	$sSQL = "INSERT INTO dropshipper (dsEmail,dsName,dsAddress,dsCity,dsState,dsCountry,dsZip,dsAction) VALUES (" .
		"'" . mysql_escape_string(unstripslashes(trim(@$_POST["email"]))) . "'," .
		"'" . mysql_escape_string(unstripslashes(trim(@$_POST["name"]))) . "'," .
		"'" . mysql_escape_string(unstripslashes(trim(@$_POST["address"]))) . "'," .
		"'" . mysql_escape_string(unstripslashes(trim(@$_POST["city"]))) . "'," .
		"'" . mysql_escape_string(unstripslashes(trim(@$_POST["state"]))) . "'," .
		"'" . mysql_escape_string(unstripslashes(trim(@$_POST["country"]))) . "'," .
		"'" . mysql_escape_string(unstripslashes(trim(@$_POST["zip"]))) . "'," .
		"" . mysql_escape_string(unstripslashes(trim(@$_POST["dsAction"]))) . ")";
	mysql_query($sSQL) or print(mysql_error());
	$dorefresh=TRUE;
}elseif(@$_POST["act"]=="delete"){
	$sSQL = "DELETE FROM dropshipper WHERE dsID=" . trim(@$_POST["id"]);
	mysql_query($sSQL) or print(mysql_error());
	$dorefresh=TRUE;
}
if($dorefresh){
	print '<meta http-equiv="refresh" content="2; url=admindropship.php">';
?>
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr>
          <td width="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyUpdSuc?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
                        <?php print $yyNoAuto?> <a href="admindropship.php"><strong><?php print $yyClkHer?></strong></a>.<br />
                        <br />
				<img src="../images/clearpixel.gif" width="300" height="3" alt="" />
                </td>
			  </tr>
			</table></td>
        </tr>
      </table>
<?php
}elseif(trim(@$_POST["act"])=="modify" || trim(@$_POST["act"])=="addnew"){
	if(trim(@$_POST["act"])=="modify"){
		$dsID=trim(@$_POST["id"]);
		$sSQL = "SELECT dsName,dsAddress,dsCity,dsState,dsZip,dsCountry,dsEmail,dsAction FROM dropshipper WHERE dsID=" . $dsID;
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_array($result)){
			$dsName = $rs["dsName"];
			$dsAddress = $rs["dsAddress"];
			$dsCity = $rs["dsCity"];
			$dsState = $rs["dsState"];
			$dsZip = $rs["dsZip"];
			$dsCountry = $rs["dsCountry"];
			$dsEmail = $rs["dsEmail"];
			$dsAction = $rs["dsAction"];
		}
		mysql_free_result($result);
	}else{
		$dsName = "";
		$dsAddress = "";
		$dsCity = "";
		$dsState = "";
		$dsZip = "";
		$dsCountry = "";
		$dsEmail = "";
		$dsAction = 0;
	}
?>
<script language="javascript" type="text/javascript">
<!--
function checkform(frm)
{
if(frm.name.value=="")
{
	alert("<?php print $yyPlsEntr?> \"<?php print $yyName?>\".");
	frm.name.focus();
	return (false);
}
if(frm.email.value=="")
{
	alert("<?php print $yyPlsEntr?> \"<?php print $yyEmail?>\".");
	frm.email.focus();
	return (false);
}
if(frm.address.value=="")
{
	alert("<?php print $yyPlsEntr?> \"<?php print $yyAddress?>\".");
	frm.address.focus();
	return (false);
}
if(frm.city.value=="")
{
	alert("<?php print $yyPlsEntr?> \"<?php print $yyCity?>\".");
	frm.city.focus();
	return (false);
}
if(frm.state.value=="")
{
	alert("<?php print $yyPlsEntr?> \"<?php print $yyState?>\".");
	frm.state.focus();
	return (false);
}
if(frm.zip.value=="")
{
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
		    <form method="post" action="admindropship.php" onsubmit="return checkform(this)">
		<?php	if(trim(@$_POST["act"])=="modify"){ ?>
			<input type="hidden" name="act" value="domodify" />
		<?php	}else{ ?>
			<input type="hidden" name="act" value="doaddnew" />
		<?php	} ?>
			<input type="hidden" name="dsID" value="<?php print $dsID?>" />
			  <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
				<tr>
				  <td width="100%" align="center" colspan="4"><strong><?php print $yyDSAdm?></strong><br /></td>
				</tr>
				<tr>
				  <td width="20%" align="right"><strong><font color='#FF0000'>*</font><?php print $yyName?>:</strong></td>
				  <td width="30%" align="left"><input type="text" name="name" size="20" value="<?php print $dsName?>" /></td>
				  <td width="20%" align="right"><strong><font color='#FF0000'>*</font><?php print $yyEmail?>:</strong></td>
				  <td width="30%" align="left"><input type="text" name="email" size="25" value="<?php print $dsEmail?>" /></td>
				</tr>
				<tr>
				  <td align="right"><strong><font color='#FF0000'>*</font><?php print $yyAddress?>:</strong></td>
				  <td align="left"><input type="text" name="address" size="20" value="<?php print $dsAddress?>" /></td>
				  <td align="right"><strong><font color='#FF0000'>*</font><?php print $yyCity?>:</strong></td>
				  <td align="left"><input type="text" name="city" size="20" value="<?php print $dsCity?>" /></td>
				</tr>
				<tr>
				  <td align="right"><strong><font color='#FF0000'>*</font><?php print $yyState?>:</strong></td>
				  <td align="left"><input type="text" name="state" size="20" value="<?php print $dsState?>" /></td>
				  <td align="right"><strong><font color='#FF0000'>*</font><?php print $yyCountry?>:</strong></td>
				  <td align="left"><select name="country" size="1">
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
show_countries($dsCountry);
?>
					</select>
				  </td>
				</tr>
				<tr>
				  <td align="right"><strong><font color='#FF0000'>*</font><?php print $yyZip?>:</strong></td>
				  <td align="left"><input type="text" name="zip" size="10" value="<?php print $dsZip?>" /></td>
				  <td align="right"><strong><?php print $yyActns?>:</strong></td>
				  <td align="left"><select name="dsAction" size="1">
					<option value="0"><?php print $yyNoAct?></option>
					<option value="1"<?php if($dsAction==1) print " selected"?>><?php print $yySendEM?></option>
					</select>
				  </td>
				</tr>
				<tr>
				  <td width="100%" colspan="4">&nbsp;<br />
					<font size="1"><ul><li><?php print $yyDSInf?></li></ul></font>
				  </td>
				</tr>
				<tr>
				  <td width="50%" align="center" colspan="4"><input type="submit" value="<?php print $yySubmit?>" /> <input type="reset" value="<?php print $yyReset?>" /> </td>
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
	  <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr> 
          <td width="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="">
			  <tr>
				<td width="100%" align="center" colspan="6"><strong><?php print $yyDSAdm?></strong><br /></td>
			  </tr>
			  <form method="post" action="admindropship.php">
			  <tr> 
                <td width="100%" colspan="6" align="center"><?php if(! $success) print '<p><font color="#FF0000">' . $errmsg . '</font></p>' ?><br /><strong><?php print $yyAffBet?>:</strong> <input type="text" size="12" name="sd" value="<?php print date($admindatestr, $sd)?>" /> <strong><?php print $yyAnd?>:</strong> <input type="text" size="12" name="ed" value="<?php print date($admindatestr, $ed)?>" /> <input type="submit" value="Go" /><br />&nbsp;</td>
			  </tr>
			  </form>
			  <form method="post" action="admindropship.php">
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
			  <form name="mainform" method="post" action="admindropship.php">
				<tr>
				  <td><strong><?php print $yyID?></strong></td>
				  <td><strong><?php print $yyName?></strong></td>
				  <td><strong><?php print $yyEmail?></strong></td>
				  <td align="right"><strong><?php print $yyTotSal?></strong></td>
				  <td align="center"><strong><?php print $yyModify?></strong></td>
				  <td align="center"><strong><?php print $yyDelete?></strong></td>
				</tr>
				<input type="hidden" name="id" value="xxx" />
				<input type="hidden" name="act" value="xxxxx" />
				<input type="hidden" name="ed" value="<?php print date($admindatestr, $ed)?>" />
				<input type="hidden" name="sd" value="<?php print date($admindatestr, $sd)?>" />
<?php
	$sSQL = "SELECT dsID,dsName,dsEmail FROM dropshipper ORDER BY dsName";
	$alldata = mysql_query($sSQL) or print(mysql_error());
	if(mysql_num_rows($alldata)==0){
?>
				<tr>
				  <td width="100%" align="center" colspan="6"><br />&nbsp;<br /><strong><?php print $yyNoAff?></strong><br />&nbsp;</td>
				</tr>
<?php
	}else{
		while($rs=mysql_fetch_array($alldata)){
			$sSQL = "SELECT SUM(cartProdPrice*cartQuantity) AS sumSale FROM cart INNER JOIN products ON cart.cartProdID=products.pID WHERE pDropship=" . $rs["dsID"] . " AND cartCompleted=1 AND cartDateAdded BETWEEN '" . date("Y-m-d", $sd) . "' AND '" . date("Y-m-d", $ed) . " 23:59:59'";
			$alldata2 = mysql_query($sSQL) or print(mysql_error());
			$rs2=mysql_fetch_array($alldata2);
			if(! is_numeric($rs2['sumSale'])) $rs2['sumSale']=0;
			$sSQL = "SELECT SUM(coPriceDiff*cartQuantity) AS sumSale FROM cartoptions INNER JOIN cart ON cartoptions.coCartID=cart.cartID INNER JOIN products ON cart.cartProdID=products.pID WHERE pDropship=" . $rs["dsID"] . " AND cartCompleted=1 AND cartDateAdded BETWEEN '" . date("Y-m-d", $sd) . "' AND '" . date("Y-m-d", $ed) . " 23:59:59'";
			$alldata3 = mysql_query($sSQL) or print(mysql_error());
			$rs3=mysql_fetch_array($alldata3);
			if(is_numeric($rs3['sumSale'])) $rs2['sumSale']+=$rs3['sumSale'];
?>
				<tr>
				  <td><?php print $rs["dsID"]?></td>
				  <td><?php print $rs["dsName"]?></td>
				  <td><a href="mailto:<?php print $rs["dsEmail"]?>"><?php print $rs["dsEmail"]?></a></td>
				  <td align=right><?php if($rs2['sumSale']==0) print "-"; else print FormatEuroCurrency($rs2["sumSale"])?></td>
				  <td align="center"><input type=button value="Modify" onclick="modrec('<?php print $rs["dsID"]?>')" /></td>
				  <td align="center"><input type=button value="Delete" onclick="delrec('<?php print $rs["dsID"]?>')" /></td>
				</tr><?php
		}
	}
?>
				<tr> 
				  <td width="100%" colspan="6" align="center"><br /><input type="button" value="<?php print $yyAddNew?>" onclick="newrec()" /><br />&nbsp;</td>
				</tr>
				<tr> 
				  <td width="100%" colspan="6" align="center"><br />
                          <a href="admin.php"><strong><?php print $yyAdmHom?></strong></a><br />
				  <img src="../images/clearpixel.gif" width="300" height="3" alt="" /></td>
				</tr>
			  </form>
			</table>
		  </td>
        </tr>
      </table>
<?php
}
?>