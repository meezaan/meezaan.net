<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$storesessionvalue=="") $storesessionvalue="virtualstore".time();
if($_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE) exit;
$success=TRUE;
$numzones=0;
$alreadygotadmin = getadminsettings();
$simpleShipping = (($adminTweaks & 1)==1);
$editzones = ($shipType==2 || $shipType==5 || $adminIntShipping==2 || $adminIntShipping==5 || @$alternateratesweightbased != '');
if(@$_POST["posted"]=="1"){
	for ($index=1; $index <= 300; $index++){
		if(trim(@$_POST["pos" . $index]) != ""){
			$cena=0;
			if(@$_POST["ena" . $index] != "") $cena=1;
			$fsa=0;
			if(@$_POST["fsa" . $index] != "") $fsa=1;
			$tax = @$_POST["tax" . $index];
			if(! is_numeric($tax)){
				$success=FALSE;
				$errmsg = $yyNum100 . ' "' . $yyTax . '".';
			}elseif($tax > 100 || $tax < 0){
				$success=FALSE;
				$errmsg = $yyNum100 . ' "' . $yyTax . '".';
			}else{
				if($editzones)
					$sSQL = "UPDATE countries SET countryEnabled=" . $cena . ",countryTax=" . $tax . ",countryOrder=" . @$_POST["pos" . $index] . ",countryFreeShip=" . $fsa . ",countryZone=" . @$_POST["zon" . $index] . " WHERE countryID=" . $index;
				else
					$sSQL = "UPDATE countries SET countryEnabled=" . $cena . ",countryTax=" . $tax . ",countryOrder=" . @$_POST["pos" . $index] . ",countryFreeShip=" . $fsa . " WHERE countryID=" . $index;
				mysql_query($sSQL) or print(mysql_error());
			}
		}
	}
	if($success)
		print '<meta http-equiv="refresh" content="3; url=admin.php">';
}else{
	$sSQL = "SELECT pzID,pzName FROM postalzones WHERE pzName<>'' AND pzID<100";
	$result = mysql_query($sSQL) or print(mysql_error());
	while($rs = mysql_fetch_assoc($result))
		$allzones[$numzones++] = $rs;
	mysql_free_result($result);
}
if(@$_POST["posted"]=="1" && $success){ ?>
			<table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyUpdSuc?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
                        <?php print $yyNoAuto?> <A href="admin.php"><strong><?php print $yyClkHer?></strong></a>.<br />
                        <br />
				<img src="../images/clearpixel.gif" width="300" height="3" alt="" />
                </td>
			  </tr>
			</table>
<?php
}elseif(@$_POST["posted"]=="1"){ ?>
			<table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><font color="#FF0000"><strong>Some records could not be updated.</strong></font><br /><br /><?php print $errmsg?><br /><br />
				<a href="javascript:history.go(-1)"><strong><?php print $yyClkBac?></strong></a></td>
			  </tr>
			</table>
<?php
}else{ ?>
<script language="javascript" type="text/javascript">
<!--
function wposzon(id,pos,zone,fsa) {
// fsa
document.write('<td><input type=checkbox name="fsa'+id+'"');
if(fsa==1) document.write(' checked');
document.write(' /></td>');
// pos
document.write('<td><select name="pos'+id+'" size="1">');
document.write('<option value="0"><?php print $yyAlphab?></option>');
document.write('<option value="1"');
if(pos==1)document.write(' selected');
document.write('><?php print str_replace("'","\'",$yyTop)?></option>');
document.write('<option value="2"');
if(pos==2)document.write(' selected');
document.write('><?php print str_replace("'","\'",$yyTopTop)?></option></select></td>');
// zone
<?php	if($editzones){ ?>
var foundzone=false;
document.write('<td><select name="zon'+id+'" size="1">');
<?php
	for($index=0; $index < $numzones; $index++){
		print "document.write('<option value=\"" . $allzones[$index]["pzID"] . "\"');\n";
		print "if(zone==" . $allzones[$index]["pzID"] . "){document.write(' selected');foundzone=true;}\n";
		print "document.write('>" . str_replace("'","\'",$allzones[$index]["pzName"]) . "</option>');\n";
	}
?>
if(!foundzone)document.write('<option value="0" selected><?php print str_replace("'","\'",$yyUndef)?></option>');
document.write('</select></td>');
<?php	}else{ ?>
document.write('<td>&nbsp;</td>');
<?php	} ?>
}
//-->
</script>
		  <form name="mainform" method="post" action="admincountry.php">
			<input type="hidden" name="posted" value="1" />
            <table width="100%" border="0" cellspacing="0" cellpadding="1" bgcolor="">
			  <tr> 
                <td width="100%" colspan="6" align="center"><strong><?php print $yyCntAdm?></strong><br />&nbsp;</td>
			  </tr>
			  <tr> 
                <td width="100%" colspan="6"><ul><li><?php print $yyHomCou?></li></ul></td>
			  </tr>
			  <tr>
				<td><strong><?php print $yyCntNam?></strong></td>
				<td align=center><strong><?php print $yyEnable?></strong></td>
				<td align=center><strong><?php print $yyTax?></strong></td>
				<td align=center><strong><acronym title="<?php print $yyFSApp?>"><?php print $yyFSA?></acronym></strong></td>
				<td align=center><strong><?php print $yyPosit?></strong></td>
				<td align=center><strong><?php	if($editzones)
												print $yyPZone;
											else
												print "&nbsp;"; ?></strong></td>
			  </tr><?php
	$bgcolor="#FFFFFF";
	$sSQL = "SELECT countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryFreeShip FROM countries ORDER BY countryOrder DESC,countryName";
	$result = mysql_query($sSQL) or print(mysql_error());
	if($simpleShipping){
		while($alldata = mysql_fetch_assoc($result)){
			if($bgcolor=="#E7EAEF") $bgcolor="#FFFFFF"; else $bgcolor="#E7EAEF";
			?><tr align=center bgcolor="<?php print $bgcolor?>">
<td align=left><strong><?php print $alldata["countryName"]?></strong></td>
<td><input type=checkbox name="ena<?php print $alldata["countryID"]?>"<?php if((int)$alldata["countryEnabled"]==1) print " checked" ?> /></td>
<td><input type=text name="tax<?php print $alldata["countryID"]?>" value="<?php print (double)$alldata["countryTax"]?>" size="4" />%</td>
<td><input type=checkbox name="fsa<?php print $alldata["countryID"]?>"<?php if((int)$alldata["countryFreeShip"]==1) print " checked"?> /></td>
<td><select name="pos<?php print $alldata["countryID"]?>" size="1">
<option value="0"><?php print $yyAlphab?></option>
<option value="1"<?php if((int)$alldata["countryOrder"]==1) print " selected" ?>><?php print $yyTop?></option>
<option value="2"<?php if((int)$alldata["countryOrder"]==2) print " selected" ?>><?php print $yyTopTop?></option></select></td><?php
			if($editzones){
				$foundzone=FALSE;
				print '<td><select name="zon' . $alldata["countryID"] . '" size="1">';
				for($index=0; $index < $numzones; $index++){
					print '<option value="' . $allzones[$index]["pzID"] . '"';
					if($alldata["countryZone"]==$allzones[$index]["pzID"]){
						print " selected";
						$foundzone=TRUE;
					}
					print ">" . $allzones[$index]["pzName"] . "</option>\n";
				}
				if(!$foundzone)print '<option value="0" selected><?php print $yyUndef?></option>';
				print "</select></td>";
			}else
				print "<td>&nbsp;</td>";
			print "</tr>";
		}
	}else{
		while($alldata = mysql_fetch_assoc($result)){
			if($bgcolor=="#E7EAEF") $bgcolor="#FFFFFF"; else $bgcolor="#E7EAEF";
			?><tr align=center bgcolor="<?php print $bgcolor?>">
<td align=left><strong><?php print $alldata["countryName"]?></strong></td>
<td><input type=checkbox name="ena<?php print $alldata["countryID"]?>" <?php if((int)$alldata["countryEnabled"]==1) print "checked"; ?> /></td>
<td><input type=text name="tax<?php print $alldata["countryID"]?>" value="<?php print (double)$alldata["countryTax"]?>" size="4" />%</td>
<?php print '<script type="text/javascript">wposzon(' . $alldata['countryID'] . ',' . $alldata['countryOrder'] . ',' . $alldata['countryZone'] . ',' . $alldata['countryFreeShip'] . ');</script>' ?>
</tr><?php
		}
	}
?>			  <tr> 
                <td width="100%" colspan="6" align="center">
                  <p><input type="submit" value="<?php print $yySubmit?>" />&nbsp;&nbsp;<input type="reset" value="<?php print $yyReset?>" /><br />&nbsp;</p>
                </td>
			  </tr>
			  <tr> 
                <td width="100%" colspan="6" align="center"><br />
                          <a href="admin.php"><strong><?php print $yyAdmHom?></strong></a><br />
				<img src="../images/clearpixel.gif" width="300" height="3" alt="" /></td>
			  </tr>
            </table>
		  </form>
<?php
}
?>
