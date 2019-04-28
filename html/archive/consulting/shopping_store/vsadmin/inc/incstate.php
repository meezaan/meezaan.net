<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$storesessionvalue=="") $storesessionvalue="virtualstore".time();
if($_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE) exit;
$success=TRUE;
$alreadygotadmin = getadminsettings();
$simpleShipping = (($adminTweaks & 1)==1);
if(@$_POST["posted"]=="1"){
	for($index=1; $index <= 150; $index++){
		if(@$_POST["id" . $index] != ""){
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
				if($splitUSZones)
					$sSQL = "UPDATE states SET stateEnabled=" . $cena . ",stateTax=" . $tax . ",stateFreeShip=" . $fsa . ",stateZone=" . @$_POST["zon" . $index] . " WHERE stateID=" . $index;
				else
					$sSQL = "UPDATE states SET stateEnabled=" . $cena . ",stateTax=" . $tax . ",stateFreeShip=" . $fsa . " WHERE stateID=" . $index;
				mysql_query($sSQL) or print(mysql_error());
			}
		}
	}
	if($success)
		print '<meta http-equiv="refresh" content="3; url=admin.php">';
}
?>
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
<?php
if(@$_POST["posted"]=="1" && $success){ ?>
        <tr>
          <td width="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyUpdSuc?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
                        <?php print $yyNoAuto?> <A href="admin.php"><strong><?php print $yyClkHer?></strong></a>.<br />
                        <br />
				<img src="../images/clearpixel.gif" width="300" height="3" alt="" />
                </td>
			  </tr>
			</table></td>
        </tr>
<?php
}elseif(@$_POST["posted"]=="1"){ ?>
        <tr>
          <td width="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><font color="#FF0000"><strong>Some records could not be updated.</strong></font><br /><br /><?php print $errmsg?><br /><br />
				<a href="javascript:history.go(-1)"><strong><?php print $yyClkBac?></strong></a></td>
			  </tr>
			</table></td>
        </tr>
<?php
}else{ ?>
<script language="javascript" type="text/javascript">
<!--
function writezone(id,zone) {
var foundzone=false;
document.write('<select name="zon'+id+'" size="1">');
<?php
	$sSQL = "SELECT pzID,pzName FROM postalzones WHERE pzName<>'' AND pzID>100";
	$result = mysql_query($sSQL) or print(mysql_error());
	$maxzones=0;
	while($allzones = mysql_fetch_assoc($result)){
		print "document.write('<option value=\"" . $allzones["pzID"] . "\"');\n";
		print "if(zone==" . $allzones["pzID"] . "){document.write(' selected');foundzone=true;}\n";
		print "document.write('>" . $allzones["pzName"] . "</option>');\n";
		$az[$maxzones++] = $allzones;
	}
	mysql_free_result($result);
?>
if(!foundzone)document.write('<option value="0" selected><?php print $yyUndef?></option>');
document.write('</select>');
}
//-->
</script>
        <tr>
		  <form name="mainform" method="post" action="adminstate.php">
		  <td width="100%">
			<input type="hidden" name="posted" value="1" />
            <table width="100%" border="0" cellspacing="0" cellpadding="1" bgcolor="">
			  <tr> 
                <td width="100%" colspan="5" align="center"><strong><?php print $yyStaAdm?></strong><br /><br />
				<font size="1"><?php print $yyFSANot?><br />&nbsp;</font>
				</td>
			  </tr>
			  <tr>
				<td><strong><?php print $yyStaNam?></strong></td>
				<td align=center><strong><?php print $yyEnable?></strong></td>
				<td align=center><strong><?php print $yyTax?></strong></td>
				<td align=center><strong><acronym title="<?php print $yyFSApp?>"><?php print $yyFSA?></acronym></strong></td>
				<td align=center><strong><?php	if($splitUSZones)
												print $yyPZone;
											else
												print "&nbsp;"; ?></strong></td>
			  </tr><?php
	$bgcolor="#FFFFFF";
	$sSQL = "SELECT stateID,stateName,stateEnabled,stateTax,stateZone,stateFreeShip FROM states ORDER BY stateName";
	$result = mysql_query($sSQL) or print(mysql_error());
	if($simpleShipping){
		while($alldata = mysql_fetch_assoc($result)){
			if($bgcolor=="#E7EAEF") $bgcolor="#FFFFFF"; else $bgcolor="#E7EAEF";
			?><tr align=center bgcolor="<?php print $bgcolor?>">
<td align=left><strong><?php print $alldata["stateName"]?></strong><input type="hidden" name="id<?php print $alldata["stateID"]?>" value="1" /></td>
<td><input type=checkbox name="ena<?php print $alldata["stateID"]?>" <?php if((int)$alldata["stateEnabled"]==1) print "checked" ?> /></td>
<td><input type=text name="tax<?php print $alldata["stateID"]?>" value="<?php print (double)$alldata["stateTax"]?>" size="4" />%</td>
<td><input type=checkbox name="fsa<?php print $alldata["stateID"]?>"<?php if((int)$alldata["stateFreeShip"]==1) print " checked"?> /></td>
	<td><?php	if($splitUSZones){
					$index=0;
					$foundzone=FALSE;
					print '<select name="zon' . $alldata["stateID"] . '" size="1">';
					while($index < $maxzones){
						print '<option value="' . $az[$index]["pzID"] . '"';
						if($alldata["stateZone"]==$az[$index]["pzID"]){
							print " selected";
							$foundzone=TRUE;
						}
						print ">" . $az[$index]["pzName"] . "</option>";
						$index++;
					}
					if(!$foundzone)print '<option value="0" selected>' . $yyUndef . '</option>';
					print "</select>";
				}else
					print "&nbsp;"; ?></td></tr>
<?php
		}
	}else{
		while($alldata = mysql_fetch_assoc($result)){
			if($bgcolor=="#E7EAEF") $bgcolor="#FFFFFF"; else $bgcolor="#E7EAEF";
				?><tr align=center bgcolor="<?php print $bgcolor?>">
<td align=left><strong><?php print $alldata["stateName"]?></strong><input type="hidden" name="id<?php print $alldata["stateID"]?>" value="1" /></td>
<td><input type=checkbox name="ena<?php print $alldata["stateID"]?>" <?php if((int)$alldata["stateEnabled"]==1) print "checked" ?> /></td>
<td><input type=text name="tax<?php print $alldata["stateID"]?>" value="<?php print (double)$alldata["stateTax"]?>" size="4" />%</td>
<td><input type=checkbox name="fsa<?php print $alldata["stateID"]?>"<?php if((int)$alldata["stateFreeShip"]==1) print " checked"?> /></td>
<td><?php	if($splitUSZones)
				print '<script type="text/javascript">writezone(' . $alldata['stateID'] . ',' . $alldata['stateZone'] . ');</script>';
			else
				print "&nbsp;"; ?></td>
	</tr><?php
		}
	}
?>			  <tr> 
                <td width="100%" colspan="5" align="center">
                  <p><input type="submit" value="<?php print $yySubmit?>" />&nbsp;&nbsp;<input type="reset" value="<?php print $yyReset?>" /><br />&nbsp;</p>
                </td>
			  </tr>
			  <tr> 
                <td width="100%" colspan="5" align="center"><br />
                          <a href="admin.php"><strong><?php print $yyAdmHom?></strong></a><br />
				<img src="../images/clearpixel.gif" width="300" height="3" alt="" /></td>
			  </tr>
            </table>
		  </td>
		  </form>
        </tr>
<?php
}
?>
      </table>