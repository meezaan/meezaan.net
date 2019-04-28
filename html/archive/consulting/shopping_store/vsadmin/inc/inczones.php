<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$storesessionvalue=="") $storesessionvalue="virtualstore".time();
if($_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE) exit;
$success=TRUE;
$maxshippingmethods=5;
$alldata="";
$numrows = 0;
if(@$_POST["posted"]=="1"){
	for($index=1; $index <= 200; $index++){
		if(@$_POST["id" . $index]=="1"){
			$sSQL = "UPDATE postalzones SET pzName='" . @$_POST["zon" . $index] . "' WHERE pzID=" . $index;
			mysql_query($sSQL) or print(mysql_error());
		}
	}
	print '<meta http-equiv="refresh" content="1; url=adminzones.php">';
}elseif(@$_POST["posted"]=="2"){
	$numshipmethods=$_POST["numshipmethods"];
	$zone = $_POST["zone"];
	mysql_query("DELETE FROM zonecharges WHERE zcZone=" . $zone) or print(mysql_error());
	if(is_numeric(@$_POST["highweight"]) && (double)@$_POST["highweight"] > 0){
		$sSQL = "INSERT INTO zonecharges (zcZone,zcWeight,zcRate,zcRate2,zcRate3,zcRate4,zcRate5) VALUES (" . $zone . "," . (0.0-(double)@$_POST["highweight"]);
		for($index=0; $index < $maxshippingmethods; $index++){
			if(is_numeric(trim(@$_POST["highvalue" . $index])))
				$sSQL .= "," . $_POST["highvalue" . $index];
			else
				$sSQL .= ",0";
		}
		mysql_query($sSQL . ')') or print(mysql_error());
	}
	for($index=0; $index <= 59; $index++){
		if(is_numeric(@$_POST['weight' . $index]) && (double)@$_POST['weight' . $index] > 0){
			$sSQL = "INSERT INTO zonecharges (zcZone,zcWeight,zcRate,zcRatePC,zcRate2,zcRatePC2,zcRate3,zcRatePC3,zcRate4,zcRatePC4,zcRate5,zcRatePC5) VALUES (" . $zone . ',' . @$_POST['weight' . $index];
			for($index2=0; $index2 < $maxshippingmethods; $index2++){
				$thecharge = trim(@$_POST['charge' . $index2 . 'x' . $index]);
				if(is_numeric(str_replace('%','',$thecharge)))
					$sSQL .= ',' . str_replace('%','',$thecharge);
				elseif(strtolower($thecharge)=='x')
					$sSQL .= ',-99999.0';
				else
					$sSQL .= ',0';
				if(substr_count($thecharge, '%') > 0) $sSQL .= ',1'; else $sSQL .= ',0';
			}
			mysql_query($sSQL . ')') or print(mysql_error());
		}
	}
	$sSQL = "UPDATE postalzones SET ";
	$addcomma="";
	$pzFSA = 0;
	for($index=0; $index < $maxshippingmethods; $index++){
		$sSQL .= $addcomma . "pzMethodName" . ($index+1) . "='" . trim(mysql_escape_string(@$_POST["methodname" . $index])) . "'";
		if(trim(@$_POST["methodfsa" . $index])=="ON") $pzFSA = ($pzFSA | pow(2, $index));
		$addcomma=",";
	}
	$sSQL .= ',pzFSA=' . $pzFSA;
	mysql_query($sSQL . " WHERE pzID=" . $zone);
	print '<meta http-equiv="refresh" content="1; url=adminzones.php">';
}elseif(@$_GET["id"] != ""){
	if(trim(@$_GET["shippingmethods"]) != ""){
		$sSQL = "UPDATE postalzones SET pzMultiShipping=" . @$_GET["shippingmethods"] . " WHERE pzID=" . @$_GET["id"];
		mysql_query($sSQL) or print(mysql_error());
	}
	$sSQL = "SELECT pzName,pzMultiShipping,pzFSA,pzMethodName1,pzMethodName2,pzMethodName3,pzMethodName4,pzMethodName5 FROM postalzones WHERE pzID=" . @$_GET["id"];
	$result = mysql_query($sSQL) or print(mysql_error());
	$zoneName="";
	if($rs=mysql_fetch_assoc($result)){
		$zoneName = $rs["pzName"];
		$hasMultiShip=$rs["pzMultiShipping"];
		$pzFSA=$rs["pzFSA"];
		for($rowcounter=1; $rowcounter<=$maxshippingmethods; $rowcounter++){
			$methodnames[$rowcounter-1]=$rs["pzMethodName".$rowcounter];
		}
	}
	mysql_free_result($result);
	$sSQL = "SELECT zcID,zcWeight,zcRate,zcRate2,zcRate3,zcRate4,zcRate5,zcRatePC,zcRatePC2,zcRatePC3,zcRatePC4,zcRatePC5 FROM zonecharges WHERE zcZone=" . @$_GET["id"] . " ORDER BY zcWeight";
	$result = mysql_query($sSQL) or print(mysql_error());
	while($rs = mysql_fetch_row($result))
		$alldata[$numrows++]=$rs;
	mysql_free_result($result);
}else{
	if(@$_GET["oneuszone"]=="yes"){
		$sSQL = "UPDATE admin SET adminUSZones=0";
		mysql_query($sSQL) or print(mysql_error());
	}
	if(@$_GET["oneuszone"]=="no"){
		$sSQL = "UPDATE admin SET adminUSZones=1";
		mysql_query($sSQL) or print(mysql_error());
	}
	$sSQL = "SELECT pzID,pzName FROM postalzones ORDER BY pzID";
	$result = mysql_query($sSQL) or print(mysql_error());
	while($rs = mysql_fetch_row($result))
		$alldata[$numrows++]=$rs;
	mysql_free_result($result);
}
$alreadygotadmin = getadminsettings();
$isWeightBased = ($shipType==2 || $shipType==5);
?>
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
<?php
if(@$_POST["posted"]=="2" && $success){ ?>
        <tr>
          <td width="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyUpdSuc?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
                        <?php print $yyNoAuto?> <A href="adminzones.php"><strong><?php print $yyClkHer?></strong></a>.<br />
                        <br />
				<img src="../images/clearpixel.gif" width="300" height="3" alt="" />
                </td>
			  </tr>
			</table></td>
        </tr>
<?php
}elseif(@$_POST["posted"]=="2"){ ?>
        <tr>
          <td width="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><font color="#FF0000"><strong><?php print $yyErrUpd?></strong></font><br /><br /><?php print $errmsg?><br /><br />
				<a href="javascript:history.go(-1)"><strong><?php print $yyClkBac?></strong></a></td>
			  </tr>
			</table></td>
        </tr>
<?php
}elseif(@$_GET["id"] != ""){ ?>
<script language="javascript" type="text/javascript">
<!--
function formvalidator(theForm)
{
	var emptyentries=false;
<?php for($index=0; $index<= $hasMultiShip; $index++){ ?>
	if (theForm.methodname<?php print $index?>.value == ""){
		alert("<?php print $yyAllShp?>");
		theForm.methodname<?php print $index?>.focus();
		return (false);
	}
<?php } ?>
	var checkOK = "0123456789.";
	var checkStr = theForm.highweight.value;
	var allValid = true;
	for (i = 0;  i < checkStr.length;  i++){
		ch = checkStr.charAt(i);
		for (j = 0;  j < checkOK.length;  j++)
			if (ch == checkOK.charAt(j))
				break;
		if (j == checkOK.length){
			allValid = false;
				break;
		}
	}
	if (!allValid){
		alert("<?php print $yyDecFld?>");
		theForm.highweight.focus();
		return (false);
	}
	for(index=0; index<<?php print $maxshippingmethods?>;index++){
		var theobj = eval("theForm.highvalue"+index);
		var checkStr = theobj.value;
		var allValid = true;
		for (i = 0;  i < checkStr.length;  i++){
			ch = checkStr.charAt(i);
			for (j = 0;  j < checkOK.length;  j++)
				if (ch == checkOK.charAt(j))
					break;
			if (j == checkOK.length){
				allValid = false;
					break;
			}
		}
		if (!allValid){
			alert("<?php print $yyDecFld?>");
			theobj.focus();
			return (false);
		}
	}
	for(index=0;index<60;index++){
		var theobj = eval("theForm.weight"+index);
		var checkStr = theobj.value;
		var allValid = true;
		var hasweight = (theobj.value != "");
		for (i = 0;  i < checkStr.length;  i++){
			ch = checkStr.charAt(i);
			for (j = 0;  j < checkOK.length;  j++)
			  if (ch == checkOK.charAt(j))
				break;
			if (j == checkOK.length){
				allValid = false;
				break;
			}
		}
		if (!allValid){
			alert("<?php print $yyDecFld?>");
			theobj.focus();
			return (false);
		}
		for(index2=0; index2<=<?php print $hasMultiShip?>;index2++){
			var theobj = eval("theForm.charge"+index2+"x"+index);
			var checkOK = "0123456789.%";
			var checkStr = theobj.value;
			var allValid = true;
			if(hasweight && checkStr==""){
				emptyentries=true;
				emptyobj=theobj;
			}
			for (i = 0;  i < checkStr.length;  i++){
				ch = checkStr.charAt(i);
				for (j = 0;  j < checkOK.length;  j++)
					if (ch == checkOK.charAt(j))
						break;
				if (j == checkOK.length && checkStr.toLowerCase()!="x"){
					allValid = false;
					break;
				}
			}
			if (!allValid){
				alert("<?php print $yyDecFld?>");
				theobj.focus();
				return (false);
			}
		}
	}
	if(emptyentries){
		if(!confirm("<?php print $yyNoMeth?> <?php if($shipType==5) print $yyMaxPri; else print $yyMaxWei;?><?php print $yyNoMet2?> <?php print $yyNoInt?>\n\n<?php print $yyOkCan?>")){
			emptyobj.focus();
			return(false);
		}
	}
	return (true);
}
function setnummethods(){
setto=document.forms.mainform.numshipmethods.selectedIndex;
document.location="adminzones.php?shippingmethods="+setto+"&id=<?php print @$_GET["id"]?>";
}
//-->
</script>
        <tr>
		  <form name="mainform" method="post" action="adminzones.php" onsubmit="return formvalidator(this)">
			<td width="100%" align="center">
			<input type="hidden" name="posted" value="2" />
			<input type="hidden" name="zone" value="<?php print @$_GET["id"]?>" />
            <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><strong><?php print $yyModRul?> <?php
				if($zoneName != "")
					print '"' . $zoneName . '"';
				else
					print "(unnamed)"; ?>.</strong><br />&nbsp;</td>
			  </tr>
			  <tr> 
                <td width="100%" align="center">
					<font size="1"><?php print $yyZonUse?> 
					<select name="numshipmethods" size="1" onChange="setnummethods()"><?php
						for($rowcounter=1; $rowcounter <= 5; $rowcounter++){
							print '<option value="' . $rowcounter . '"';
							if($rowcounter == ($hasMultiShip+1)) print ' selected';
							print '>' . $rowcounter . '</option>';
						} ?></select> <?php print $yyZonUs2?></font>
				</td>
			  </tr>
			  <tr> 
                <td width="100%" align="center">
				<table width="80%" cellspacing="2" cellpadding="0">
				  <tr>
					<td align="right" width="45%"><?php print $yyForEv?></td>
					<td width="10%"><input type=text name="highweight" value="<?php
				$foundmatch=0;
				for($rowcounter=0; $rowcounter < $numrows; $rowcounter++){
					if($alldata[$rowcounter][1] < 0){
						$foundmatch = abs($alldata[$rowcounter][1]);
						for($index=0; $index < $maxshippingmethods; $index++)
							$hishipvals[$index]=$alldata[$rowcounter][2+$index];
					}
				}
				print $foundmatch;
				?>" size="5" /></td>
					<td width="45%" align="left"><?php print $yyAbvHg?> <?php if($shipType==5) print $yyPrice; else print $yyWeigh;?>...</td>
				  </tr>
			<?php	for($index=0; $index<=$hasMultiShip;$index++){ ?>
				  <tr>
					<td align="right"><?php print $yyAddExt?></td>
					<td><input type=text name="highvalue<?php print $index?>" value="<?php print @$hishipvals[$index] ?>" size="5" /></td><td align="left"><?php print $yyFor?> <strong><?php if($methodnames[$index] != "") print $methodnames[$index]; else print $yyShipMe . " " . ($index+1)?></strong></td>
				  </tr>
			<?php	}
					for($index=$hasMultiShip+1; $index < $maxshippingmethods; $index++){ ?>
				  <input type="hidden" name="highvalue<?php print $index?>" value="<?php print @$hishipvals[$index] ?>" />
			<?php	} ?>
				</table>
				</td>
			  </tr>
			  <tr> 
                <td width="100%" align="center">
                  <p><input type="submit" value="<?php print $yySubmit?>" />&nbsp;&nbsp;<input type="reset" value="<?php print $yyReset?>" /><br />&nbsp;</p>
                </td>
			  </tr>
			</table>
			<table width="120" border="0" cellspacing="0" cellpadding="1" bgcolor="">
			  <tr>
				<td width="<?php print (int)(100/(2+$hasMultiShip))?>%" align="center">&nbsp;</td>
			<?php	for($index=0; $index<=$hasMultiShip;$index++){
						print '<td width="' . (int)(100/(2+$hasMultiShip)) . '%" align="center"><acronym title="'. $yyFSApp . '"><strong>' . $yyFSA . '</strong></acronym>: <input type="checkbox" value="ON" name="methodfsa' . $index . '" ' . (($pzFSA & pow(2, $index)) != 0 ? "checked" : "") . ' /></td>' . "\r\n";
					}
					for($index=$hasMultiShip+1; $index < $maxshippingmethods; $index++){
						print '<input type="hidden" name="methodfsa' . $index . '" value="' . (($pzFSA & pow(2, $index)) != 0 ? "ON" : "") . '" />' . "\r\n";
					} ?>
			  </tr>
			  <tr>
				<td align="center"><strong><?php if($shipType==5) print $yyMaxPri; else print $yyMaxWgt;?></strong></td>
			<?php	for($index=0; $index<=$hasMultiShip;$index++)
						print '<td align="center"><input class="darkborder" type="text" name="methodname' . $index . '" value="' . str_replace('"','&quot;',$methodnames[$index]) . '" size="14" /></td>' . "\r\n";
					for($index=$hasMultiShip+1; $index < $maxshippingmethods; $index++)
						print '<input type="hidden" name="methodname' . $index . '" value="' . str_replace('"','&quot;',$methodnames[$index]) . '" />' . "\r\n";
					?>
			  </tr>
<?php
	$rowcounter=0;
	$index=0;
	if($numrows > 0)
		$upperbound = $numrows;
	else
		$upperbound = -1;
	while($index < 60){
		if($rowcounter < $upperbound){
			if($alldata[$rowcounter][1] > 0){
?>
			  <tr>
				<td align="center"><input class="darkborder" type=text name="weight<?php print $index?>" value="<?php print (double)$alldata[$rowcounter][1]?>" size="10" /></td>
			<?php	for($index2=0; $index2<$maxshippingmethods; $index2++){
						if($index2 <= $hasMultiShip)
							print '<td align="center"><input type="text" name="charge'. $index2 . "x" . $index . '" value="' . ($alldata[$rowcounter][2+$index2]!=-99999.0?$alldata[$rowcounter][2+$index2] . ($alldata[$rowcounter][7+$index2] != 0 ? '%' : '') :'x') . '" size="14" /></td>' . "\r\n";
						else
							print '<input type="hidden" name="charge' . $index2 . "x" . $index . '" value="' . $alldata[$rowcounter][2+$index2] . '" />';
					} ?>
			  </tr>
<?php
				$index++;
			}
		}else{
?>
			  <tr>
				<td align="center"><input class="darkborder" type=text name="weight<?php print $index?>" value="" size="10" /></td>
			<?php	for($index2=0; $index2<$maxshippingmethods; $index2++){
						if($index2 <= $hasMultiShip)
							print '<td align="center"><input type="text" name="charge' . $index2 . "x" . $index . '" size="14" /></td>' . "\r\n";
					} ?>
			  </tr>
<?php
			$index++;
		}
		$rowcounter++;
	}
?>
			  <tr> 
                <td width="100%" colspan="<?php print 2+$hasMultiShip?>" align="center">
                  <p><input type="submit" value="<?php print $yySubmit?>" />&nbsp;&nbsp;<input type="reset" value="<?php print $yyReset?>" /><br />&nbsp;</p>
                </td>
			  </tr>
			  <tr> 
                <td width="100%" colspan="<?php print 2+$hasMultiShip?>" align="center"><br />
                          <a href="admin.php"><strong><?php print $yyAdmHom?></strong></a><br />
				<img src="../images/clearpixel.gif" width="300" height="3" alt="" /></td>
			  </tr>
            </table>
		  </td>
		  </form>
        </tr>
<?php
}elseif(@$_POST["posted"]=="1" && $success){ ?>
        <tr>
          <td width="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyUpdSuc?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
                        <?php print $yyNoAuto?> <A href="adminzones.php"><strong><?php print $yyClkHer?></strong></a>.<br />
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
                <td width="100%" colspan="2" align="center"><br /><font color="#FF0000"><strong><?php print $yyErrUpd?></strong></font><br /><br /><?php print $errmsg?><br /><br />
				<a href="javascript:history.go(-1)"><strong><?php print $yyClkBac?></strong></a></td>
			  </tr>
			</table></td>
        </tr>
<?php
}else{ ?>
        <tr>
		  <form name="mainform" method="post" action="adminzones.php">
		  <td width="100%">
			<input type="hidden" name="posted" value="1" />
            <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" <?php if($splitUSZones) print "colspan='2'";?> align="center"><strong><?php print $yyModPZo?></strong><br />&nbsp;</td>
			  </tr>
			  <tr> 
                <td width="100%" <?php if($splitUSZones) print "colspan='2'";?> align="left">
				  <ul>
				  <?php if(! $isWeightBased){ ?>
					<li><font size="1"><?php print $yyPZEx1?> <a href="adminmain.php"><strong><?php print $yyClkHer?></strong></a>.</font></li>
				  <?php } ?>
				  <?php if($splitUSZones){ ?>
					<li><font size="1"><?php print $yyPZEx2?> <a href="adminzones.php?oneuszone=yes"><strong><?php print $yyClkHer?></strong></a>.</font></li>
				  <?php }else{ ?>
				    <li><font size="1"><?php print $yyPZEx3?> <a href="adminzones.php?oneuszone=no"><strong><?php print $yyClkHer?></strong></a>.</font></li>
				  <?php } ?>
					<li><font size="1"><?php print $yyPZEx4?></font></li>
				  </ul>
				</td>
			  </tr>
			  <tr>
				<td valign="top">
				  <table width="100%" cellspacing="1" cellpadding="1" border="0">
					<tr> 
					  <td width="100%" colspan="3" align="center"><strong><?php print $yyPZWor?></strong><br /><hr width="70%"></td>
					</tr>
					 <tr>
					  <td width="40%" align=right>&nbsp;</td>
					  <td width="20%" align=center><strong><?php print $yyPZNam?></strong></td>
					  <td width="40%" align=left><strong><?php print $yyPZRul?></strong></td>
					</tr>
<?php
	for($rowcounter=0;$rowcounter < $numrows;$rowcounter++){
		if($alldata[$rowcounter][0] <= 100){ // First 100 are for world zones
?>
					<tr>
					  <td align=right><strong><?php print $alldata[$rowcounter][0]?> : <input type="hidden" name="id<?php print $alldata[$rowcounter][0]?>" value="1" /></strong></td>
					  <td align=center><input type=text name="zon<?php print $alldata[$rowcounter][0]?>" value="<?php print $alldata[$rowcounter][1]?>" size="20" /></td>
					  <td align=left><?php if(trim($alldata[$rowcounter][1]) <> ""){ ?><a href="adminzones.php?id=<?php print $alldata[$rowcounter][0]?>"><strong><?php print $yyEdRul?></strong></a><?php }else{ ?>&nbsp;<?php } ?></td>
					</tr>
<?php
		}
	}
?>
				  </table>
				</td>

<?php
	if($splitUSZones){
?>
				<td width="50%" valign="top">
				  <table width="100%" cellspacing="1" cellpadding="1" border="0">
					<tr> 
					  <td width="100%" colspan="3" align="center"><strong><?php print $yyPZSta?></strong><br /><hr width="70%"></td>
					</tr>
					 <tr>
					  <td width="40%" align=right>&nbsp;</td>
					  <td width="20%" align=center><strong><?php print $yyPZNam?></strong></td>
					  <td width="40%" align=left><strong><?php print $yyPZRul?></strong></td>
					</tr>
<?php
		$index = 0;
		for($rowcounter=0;$rowcounter < $numrows;$rowcounter++){
			if($alldata[$rowcounter][0] > 100){ // First 100 are for world zones
?>
					<tr>
					  <td align=right><strong><?php print $alldata[$rowcounter][0]-100?> : <input type="hidden" name="id<?php print $alldata[$rowcounter][0]?>" value="1" /></strong></td>
					  <td align=center><input type=text name="zon<?php print $alldata[$rowcounter][0]?>" value="<?php print $alldata[$rowcounter][1]?>" size="20" /></td>
					  <td align=left><?php if(trim($alldata[$rowcounter][1]) != ""){ ?><a href="adminzones.php?id=<?php print $alldata[$rowcounter][0]?>"><strong><?php print $yyEdRul?></strong></a><?php }else{ ?>&nbsp;<?php } ?></td>
					</tr>
<?php
			}
		}
?>
				  </table>
				</td>
<?php
	}
?>			  </tr>
			  <tr> 
                <td width="100%" <?php if($splitUSZones) print "colspan='2'";?> align="center">
                  <p><input type="submit" value="<?php print $yySubmit?>" />&nbsp;&nbsp;<input type="reset" value="<?php print $yyReset?>" /><br />&nbsp;</p>
                </td>
			  </tr>
			  <tr> 
                <td width="100%" <?php if($splitUSZones) print "colspan='2'";?> align="center"><br />
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