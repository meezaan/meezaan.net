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
$alldata="";
$alreadygotadmin = getadminsettings();
if(@$_POST["posted"]=="1"){
	if(@$_POST["act"]=="delete"){
		$sSQL = "SELECT poID,poProdID FROM prodoptions WHERE poOptionGroup=" . @$_POST["id"];
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_assoc($result)){
			$success=FALSE;
			$errmsg = $yyPOErr . '<br />';
			$errmsg .= $yyPOUse . '<br />(' . $rs['poProdID'] . ')';
		}
		mysql_free_result($result);
		if($success){
			$sSQL = "DELETE FROM options WHERE optGroup=" . @$_POST["id"];
			mysql_query($sSQL) or print(mysql_error());
			$sSQL = "DELETE FROM optiongroup WHERE optGrpID=" . @$_POST["id"];
			mysql_query($sSQL) or print(mysql_error());
			print '<meta http-equiv="refresh" content="1; url=adminprodopts.php">';
		}
	}elseif(@$_POST['act']=='domodify' || @$_POST['act']=='doaddnew'){
		$sSQL = "";
		$bOption=FALSE;
		$optFlags = 0;
		if(@$_POST['pricepercent']=='1') $optFlags=1;
		if(@$_POST['weightpercent']=='1') $optFlags += 2;
		if(@$_POST['singleline']=='1') $optFlags += 4;
		if(@$_POST['optdefault']!='') $optDefault=(int)@$_POST['optdefault']; else $optDefault=-1;
		for($rowcounter=0; $rowcounter < maxprodopts; $rowcounter++){
			if(trim(@$_POST["opt" . $rowcounter]) != "") $bOption=TRUE;
			$aOption[$rowcounter][0]=mysql_escape_string(unstripslashes(trim(@$_POST["opt" . $rowcounter])));
			for($index=2; $index <= $adminlanguages+1; $index++){
				if(($adminlangsettings & 32)==32)
					$aOption[$rowcounter][5+$index]=mysql_escape_string(unstripslashes(trim(@$_POST["opl" . $index . "x" . $rowcounter])));
			}
			if(is_numeric(trim(@$_POST["pri" . $rowcounter])))
				$aOption[$rowcounter][1]=trim(@$_POST["pri" . $rowcounter]);
			else
				$aOption[$rowcounter][1]=0;
			if(is_numeric(trim(@$_POST["wsp" . $rowcounter])))
				$aOption[$rowcounter][4]=trim(@$_POST["wsp" . $rowcounter]);
			else
				$aOption[$rowcounter][4]=0;
			if(is_numeric(trim(@$_POST["wei" . $rowcounter])))
				$aOption[$rowcounter][2]=trim(@$_POST["wei" . $rowcounter]);
			else
				$aOption[$rowcounter][2]=0;
			if(is_numeric(trim(@$_POST["optStock" . $rowcounter])))
				$aOption[$rowcounter][3]=trim(@$_POST["optStock" . $rowcounter]);
			else
				$aOption[$rowcounter][3]=0;
			$aOption[$rowcounter][5]=mysql_escape_string(unstripslashes(trim(@$_POST["regexp" . $rowcounter])));
			$aOption[$rowcounter][6]=trim(@$_POST["orig" . $rowcounter]);
		}
		if((trim(@$_POST["secname"])=="" || ! $bOption) && @$_POST["optType"] != "3"){
			$success=FALSE;
			$errmsg = $yyPOErr . "<br />";
			$errmsg .= $yyPOOne;
		}else{
			if(@$_POST["optType"]=="3"){ // Text option
				$fieldDims = trim(@$_POST["pri0"]) . ".";
				if((int)@$_POST["fieldheight"] < 10) $fieldDims .= "0";
				$fieldDims .= trim(@$_POST["fieldheight"]);
				if(@$_POST["act"]=="doaddnew"){
					$sSQL = "INSERT INTO optiongroup (optGrpName,";
					for($index=2; $index <= $adminlanguages+1; $index++){
						if(($adminlangsettings & 16)==16)
							$sSQL .= "optGrpName" . $index . ",";
					}
					$sSQL .= "optType,optGrpWorkingName,optFlags) VALUES (";
					$sSQL .= "'" . mysql_escape_string(unstripslashes(trim(@$_POST["secname"]))) . "',";
					for($index=2; $index <= $adminlanguages+1; $index++){
						if(($adminlangsettings & 16)==16)
							$sSQL .= "'" . mysql_escape_string(unstripslashes(trim(@$_POST["secname" . $index]))) . "',";
					}
					if(trim(@$_POST["forceselec"])=="ON") $sSQL .= "'3',"; else $sSQL .= "'-3',";
					if(trim(@$_POST["workingname"])=="")
						$sSQL .= "'" . mysql_escape_string(unstripslashes(trim(@$_POST["secname"])));
					else
						$sSQL .= "'" . mysql_escape_string(unstripslashes(trim(@$_POST["workingname"])));
					$sSQL .= "'," . $optFlags . ")";
					mysql_query($sSQL) or print(mysql_error());
					$iID  = mysql_insert_id();
					$sSQL = "INSERT INTO options (optGroup,optName,optPriceDiff";
					for($index=2; $index <= $adminlanguages+1; $index++){
						if(($adminlangsettings & 16)==16)
							$sSQL .= ",optName" . $index;
					}
					$sSQL .= ",optWeightDiff) VALUES (" . $iID . ",'" . mysql_escape_string(unstripslashes(trim(@$_POST["opt0"]))) . "'," . $fieldDims;
					for($index=2; $index <= $adminlanguages+1; $index++){
						if(($adminlangsettings & 16)==16)
							$sSQL .= ",'" . mysql_escape_string(unstripslashes(trim(@$_POST["opl" . $index . "x0"]))) . "'";
					}
					$sSQL .= ",0)";
					mysql_query($sSQL) or print(mysql_error());
				}else{
					$iID = @$_POST["id"];
					$sSQL = "UPDATE optiongroup SET optGrpName='" . mysql_escape_string(unstripslashes(trim(@$_POST["secname"]))) . "'";
					for($index=2; $index <= $adminlanguages+1; $index++){
						if(($adminlangsettings & 16)==16)
							$sSQL .= ",optGrpName" . $index . "='" . mysql_escape_string(unstripslashes(trim(@$_POST["secname" . $index]))) . "'";
					}
					if(trim(@$_POST["forceselec"])=="ON")
						$sSQL .= ",optType='3'";
					else
						$sSQL .= ",optType='-3'";
					if(trim(@$_POST["workingname"])=="")
						$sSQL .= ",optGrpWorkingName='" . mysql_escape_string(unstripslashes(trim(@$_POST["secname"]))) . "',";
					else
						$sSQL .= ",optGrpWorkingName='" . mysql_escape_string(unstripslashes(trim(@$_POST["workingname"]))) . "',";
					$sSQL .= "optFlags=" . $optFlags;
					$sSQL .= " WHERE optGrpID=" . $iID;
					mysql_query($sSQL) or print(mysql_error());
					$sSQL = "UPDATE options SET optName='" . mysql_escape_string(unstripslashes(trim(@$_POST["opt0"]))) . "',optPriceDiff=" . $fieldDims;
					for($index=2; $index <= $adminlanguages+1; $index++){
						if(($adminlangsettings & 16)==16)
							$sSQL .= ",optName" . $index . "='" . mysql_escape_string(unstripslashes(trim(@$_POST["opl" . $index . "x0"]))) . "'";
					}
					$sSQL .= " WHERE optGroup=" . $iID;
					mysql_query($sSQL) or print(mysql_error());
				}
			}else{ // Non-text Option
				if(@$_POST['act']=='doaddnew'){
					$sSQL = 'INSERT INTO optiongroup (optGrpName';
					for($index=2; $index <= $adminlanguages+1; $index++){
						if(($adminlangsettings & 16)==16)
							$sSQL .= ',optGrpName' . $index;
					}
					$sSQL .= ',optType,optGrpWorkingName,optFlags,optGrpSelect) VALUES (';
					$sSQL .= "'" . mysql_escape_string(unstripslashes(trim(@$_POST['secname']))) . "',";
					for($index=2; $index <= $adminlanguages+1; $index++){
						if(($adminlangsettings & 16)==16)
							$sSQL .= "'" . mysql_escape_string(unstripslashes(trim(@$_POST['secname' . $index]))) . "',";
					}
					if(trim(@$_POST['forceselec'])=='ON')
						$sSQL .= "'".@$_POST['optType']."',";
					else
						$sSQL .= "'".(0-(int)@$_POST['optType'])."',";
					if(trim(@$_POST['workingname'])=='')
						$sSQL .= "'" . mysql_escape_string(unstripslashes(trim(@$_POST['secname'])));
					else
						$sSQL .= "'" . mysql_escape_string(unstripslashes(trim(@$_POST['workingname'])));
					$sSQL .= "'," . $optFlags . ',' . (trim(@$_POST['optgrpselect'])=='1' ? 1 : 0) . ')';
					mysql_query($sSQL) or print(mysql_error());
					$iID  = mysql_insert_id();
				}else{
					$iID = @$_POST["id"];
					$sSQL = "UPDATE optiongroup SET optGrpName='" . mysql_escape_string(unstripslashes(trim(@$_POST["secname"]))) . "'";
					for($index=2; $index <= $adminlanguages+1; $index++){
						if(($adminlangsettings & 16)==16)
							$sSQL .= ",optGrpName" . $index . "='" . mysql_escape_string(unstripslashes(trim(@$_POST["secname" . $index]))) . "'";
					}
					if(trim(@$_POST["forceselec"])=="ON")
						$sSQL .= ",optType='".@$_POST['optType']."'";
					else
						$sSQL .= ",optType='".(0-(int)@$_POST['optType'])."'";
					if(trim(@$_POST["workingname"])=="")
						$sSQL .= ",optGrpWorkingName='" . mysql_escape_string(unstripslashes(trim(@$_POST["secname"]))) . "',";
					else
						$sSQL .= ",optGrpWorkingName='" . mysql_escape_string(unstripslashes(trim(@$_POST["workingname"]))) . "',";
					$sSQL .= "optFlags=" . $optFlags;
					$sSQL .= ",optGrpSelect=" . (trim(@$_POST['optgrpselect'])=='1' ? 1 : 0);
					$sSQL .= " WHERE optGrpID=" . $iID;
					mysql_query($sSQL) or print(mysql_error());
				}
				for($rowcounter=0; $rowcounter < maxprodopts; $rowcounter++){
					if(trim($aOption[$rowcounter][0]) != ""){
						if($aOption[$rowcounter][6] != ''){
							$sSQL = "UPDATE options SET optName='" . $aOption[$rowcounter][0] . "',optRegExp='" . $aOption[$rowcounter][5] . "',optPriceDiff=" . $aOption[$rowcounter][1] . ",optWeightDiff=" . $aOption[$rowcounter][2] . ",optStock=" . $aOption[$rowcounter][3];
							if(@$wholesaleoptionpricediff==TRUE) $sSQL .= ",optWholesalePriceDiff=" . $aOption[$rowcounter][4];
							for($index=2; $index <= $adminlanguages+1; $index++){
								if(($adminlangsettings & 32)==32)
									$sSQL .= ",optName" . $index . "='" . $aOption[$rowcounter][5+$index] . "'";
							}
							$sSQL .= ',optDefault=' . ($rowcounter==$optDefault ? '1' : '0');
							$sSQL .= " WHERE optID=" . $aOption[$rowcounter][6];
							mysql_query($sSQL) or print(mysql_error());
						}else{
							$sSQL = "INSERT INTO options (optGroup,optName,optRegExp,optPriceDiff,optWeightDiff,optStock,optDefault";
							if(@$wholesaleoptionpricediff==TRUE) $sSQL .= ",optWholesalePriceDiff";
							for($index=2; $index <= $adminlanguages+1; $index++){
								if(($adminlangsettings & 32)==32) $sSQL .= ",optName" . $index;
							}
							$sSQL .= ") VALUES (" . $iID . ",'" . $aOption[$rowcounter][0] . "','" . $aOption[$rowcounter][5] . "'," . $aOption[$rowcounter][1] . "," . $aOption[$rowcounter][2] . "," . $aOption[$rowcounter][3] . ',' . ($rowcounter==$optDefault ? '1' : '0');
							if(@$wholesaleoptionpricediff==TRUE) $sSQL .= "," . $aOption[$rowcounter][4];
							for($index=2; $index <= $adminlanguages+1; $index++){
								if(($adminlangsettings & 32)==32) $sSQL .= ",'" . $aOption[$rowcounter][5+$index] ."'";
							}
							$sSQL .= ")";
							mysql_query($sSQL) or print(mysql_error());
						}
					}else{
						if($aOption[$rowcounter][6] != ''){
							$sSQL = "DELETE FROM options WHERE optID='" . $aOption[$rowcounter][6] . "'";
							mysql_query($sSQL) or print(mysql_error());
						}
					}
				}
			}
		}
		if($success)
			print '<meta http-equiv="refresh" content="1; url=adminprodopts.php">';
	}
}
?>
<script language="javascript" type="text/javascript">
<!--
function formvalidator(theForm){
  if (theForm.secname.value == "")
  {
    alert("<?php print $yyPlsEntr?> \"<?php print $yyPOName?>\".");
    theForm.secname.focus();
    return (false);
  }
  return (true);
}
function changeunits(){
	var nopercentchar="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	for(index=0;index<<?php print maxprodopts?>;index++){
		wel = document.getElementById("wunitspan" + index);
		pel = document.getElementById("punitspan" + index);
<?php if(@$wholesaleoptionpricediff==TRUE){ ?>
		wspel = document.getElementById("pwspunitspan" + index);
		if(document.forms.mainform.pricepercent.checked){
			wspel.innerHTML='&nbsp;%&nbsp;';
		}else{
			wspel.innerHTML='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		}
<?php } ?>
		if(document.forms.mainform.weightpercent.checked){
			wel.innerHTML='&nbsp;%&nbsp;';
		}else{
			wel.innerHTML=nopercentchar;
		}
		if(document.forms.mainform.pricepercent.checked){
			pel.innerHTML='&nbsp;%&nbsp;';
		}else{
			pel.innerHTML='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		}
	}
}
//-->
</script>
      <table border="0" cellspacing="<?php print $maintablespacing?>" cellpadding="<?php print $maintablepadding?>" width="<?php print $maintablewidth?>" bgcolor="<?php print $maintablebg?>" align="center">
<?php
if(@$_POST["posted"]=="1" && (@$_POST["act"]=="modify" || @$_POST["act"]=="clone" || @$_POST["act"]=="addnew")){
	$noptions=0;
	$iscloning = (@$_POST["act"]=="clone");
	if(@$_POST["act"]=="modify" || @$_POST["act"]=="clone"){
		$doaddnew = false;
		$sSQL = "SELECT optID,optName,optName2,optName3,optGrpName,optGrpName2,optGrpName3,optGrpWorkingName,optPriceDiff,optType,optWeightDiff,optFlags,optStock,optWholesalePriceDiff,optRegExp,optDefault,optGrpSelect FROM options LEFT JOIN optiongroup ON optiongroup.optGrpID=options.optGroup WHERE optGroup=" . @$_POST["id"] . " ORDER BY optID";
		$result = mysql_query($sSQL) or print(mysql_error());
		while($rs = mysql_fetch_assoc($result)){
			$alldata[$noptions++] = $rs;
		}
		$optName = $alldata[0]['optName'];
		$optGrpName = $alldata[0]['optGrpName'];
		for($index=2; $index <= $adminlanguages+1; $index++){
			$optNames[$index] = $alldata[0]['optName' . $index];
			$optGrpNames[$index] = $alldata[0]['optGrpName' . $index];
		}
		$optGrpWorkingName = $alldata[0]['optGrpWorkingName'];
		$optPriceDiff = $alldata[0]['optPriceDiff'];
		$optType = $alldata[0]['optType'];
		$optWeightDiff = $alldata[0]['optWeightDiff'];
		$optFlags = $alldata[0]['optFlags'];
		$optStock = $alldata[0]['optStock'];
		$optWholesalePriceDiff = $alldata[0]['optWholesalePriceDiff'];
		$optDefault = $alldata[0]['optDefault'];
		$optGrpSelect = $alldata[0]['optGrpSelect'];
	}else{
		$doaddnew = true;
		$optName = "";
		$optGrpName = "";
		for($index=2; $index <= $adminlanguages+1; $index++){
			$optNames[$index] = "";
			$optGrpNames[$index] = "";
		}
		$optGrpWorkingName = "";
		$optPriceDiff = 15;
		$optType = (int)@$_POST["optType"];
		$optWeightDiff = "";
		$optFlags = 0;
		$optStock = "";
		$optWholesalePriceDiff = "";
		$optName2 = "";
		$optName3 = "";
		$optGrpName2 = "";
		$optGrpName3 = "";
		$optDefault = '';
		$optGrpSelect = 1;
	}
?>
        <tr>
		  <form name="mainform" method="post" action="adminprodopts.php" onsubmit="return formvalidator(this)">
			<td width="100%" align="center">
			<input type="hidden" name="posted" value="1" />
			<?php	if($iscloning || @$_POST["act"]=="addnew"){ ?>
			<input type="hidden" name="act" value="doaddnew" />
			<?php	}else{ ?>
			<input type="hidden" name="act" value="domodify" />
			<input type="hidden" name="id" value="<?php print @$_POST["id"]?>" />
			<?php	}
					if(abs($optType)==3) print '<input type="hidden" name="optType" value="3" />'; ?>
            <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
<?php	if(abs((int)$optType)==3){ ?>
			  <tr> 
                <td width="100%" colspan="3" align="center"><strong><?php print $yyPOAdm?></strong><br />&nbsp;</td>
			  </tr>
			  <tr>
				<td width="50%" align="center"><p><strong><?php print $yyPOName?></strong><br />
				  <input type="text" name="secname" size="30" value="<?php print str_replace('"',"&quot;",$optGrpName)?>" /><br /><?php
				for($index=2; $index <= $adminlanguages+1; $index++){
					if(($adminlangsettings & 16)==16){
						?><strong><?php print $yyPOName . " " . $index?></strong><br />
						<input type="text" name="secname<?php print $index?>" size="30" value="<?php print str_replace('"',"&quot;",$optGrpNames[$index])?>" /><br /><?php
					}
				} ?></p>
				  <p><strong><?php print $yyWrkNam?></strong><br />
				  <input type="text" name="workingname" size="30" value="<?php print str_replace('"',"&quot;",$optGrpWorkingName)?>" /></p>
                </td>
				<td width="30%" align="center"><p><strong><?php print $yyDefTxt?></strong><br />
				<input type="text" name="opt0" size="25" value="<?php print str_replace('"',"&quot;",$optName)?>" /><br /><?php
				for($index=2; $index <= $adminlanguages+1; $index++){
					if(($adminlangsettings & 16)==16){
						?><strong><?php print $yyDefTxt . " " . $index?></strong><br />
						<input type="text" name="opl<?php print $index?>x0" size="25" value="<?php print str_replace('"',"&quot;",$optNames[$index])?>" /><br /><?php
					}
				} ?></p>
				<p>&nbsp;<br /><input type="checkbox" name="forceselec" value="ON" <?php if($optType>0) print "checked"?> /> <strong><?php print $yyForSel?></strong></p>
                </td>
				<td width="20%" align="center"><p><strong><?php print $yyFldWdt?></strong><br />
				<select name="pri0" size="1">
				<?php
					for($rowcounter=1; $rowcounter <= 35; $rowcounter++){
						print "<option value='" . $rowcounter . "'";
						if($rowcounter==(int)$optPriceDiff) print " selected";
						print ">&nbsp; " . $rowcounter . " </option>\n";
					}
				?>
				</select></p>
				<p><strong><?php print $yyFldHgt?></strong><br />
				<select name="fieldheight" size="1">
				<?php
					$fieldHeight = round(((double)($optPriceDiff)-floor($optPriceDiff))*100.0);
					for($rowcounter=1; $rowcounter <= 15; $rowcounter++){
						print "<option value='" . $rowcounter . "'";
						if($rowcounter==$fieldHeight) print " selected";
						print ">&nbsp; " . $rowcounter . " </option>\n";
					}
				?>
				</select></p>
				</td>
			  </tr>
			  <tr>
				<td colspan="3" align="left">
				  <ul>
				  <li><font size="1"><?php print $yyPOEx1?></li>
				  <li><font size="1"><?php print $yyPOEx2?></li>
				  <li><font size="1"><?php print $yyPOEx3?></li>
				  </ul>
                </td>
			  </tr>
<?php	}else{ ?>
			  <tr>
				<td width="30%" align="center">
				  <table border="0" cellspacing="0" cellpadding="3" bgcolor="">
				  <tr><td align="right"><strong><?php print str_replace(' ','&nbsp;',$yyPOName)?></strong></td><td align="left" colspan="3">
				  <input type="text" name="secname" size="30" value="<?php print str_replace('"','&quot;',$optGrpName)?>" /></td></tr>
<?php			for($index=2; $index <= $adminlanguages+1; $index++){
					if(($adminlangsettings & 16)==16){
						?><strong><?php print $yyPOName . ' ' . $index?></strong><br />
						<input type="text" name="secname<?php print $index?>" size="30" value="<?php print str_replace('"','&quot;',$optGrpNames[$index])?>" /><br /><?php
					}
				} ?>
				  <tr><td align="right"><strong><?php print str_replace(' ','&nbsp;',$yyWrkNam)?></strong></td><td align="left" colspan="3"><input type="text" name="workingname" size="30" value="<?php print str_replace('"','&quot;',$optGrpWorkingName)?>" /></td></tr>
				  <tr><td align="right"><strong><?php print str_replace(' ','&nbsp;',$yyOptSty)?></strong></td><td align="left" colspan="3"><select name="optType" size="1"><option value="2">Drop down menu</option><option value="1"<?php if(abs($optType)==1) print ' selected'?>>Radio Buttons</option></select></td></tr>
				  <tr><td align="right"><strong><?php print str_replace(' ','&nbsp;',$yyForSel)?></strong></td><td align="left"><input type="checkbox" name="forceselec" value="ON" <?php if($optType > 0) print 'checked'?> />&nbsp;</td><td align="right">&nbsp;<input type="radio" name="optdefault" value="" /></td></td><td align="left"><strong><?php print str_replace(' ','&nbsp;',$yyNoDefa)?></strong></tr>
				  <tr><td align="right"><strong><?php print str_replace(' ','&nbsp;',$yySinLin)?></strong></td><td align="left"><input type="checkbox" name="singleline" value="1" <?php if(($optFlags & 4) == 4) print 'checked'?> /></td><td align="right"><input type="checkbox" name="optgrpselect" value="1" <?php if((int)$optGrpSelect!=0) print 'checked'?> /></td><td align="left"><strong><?php print str_replace(' ','&nbsp;',$yyPlsSLi)?></strong></td></tr>
				  </table>
                </td>
				<td colspan="2" align="left">
				  <p align="center"><strong><?php print $yyPOAdm?></strong></p>
				  <ul>
				  <li><font size="1"><?php print $yyPOEx1?></font></li>
				  <li><font size="1"><?php print $yyPOEx4?></font></li>
				  <li><font size="1"><?php print $yyPOEx5?></font></li>
				  <?php if($useStockManagement){ ?>
				  <li><font size="1"><?php print $yyPOEx6?></font></li>
				  <?php } ?>
				  <ul>
                </td>
			  </tr>
			</table>
			<table width="500" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr>
			  	<td><strong><?php print $yyDefaul?></strong></td>
				<td width="3%" align="center">&nbsp;</td>
				<td align="center"><strong><?php print $yyPOOpts?></strong></td>
				<td width="3%" align="center">&nbsp;</td>
<?php			for($index=2; $index <= $adminlanguages+1; $index++){
					if(($adminlangsettings & 32)==32){
						?><td align="center"><strong><?php print $yyPOOpts . " " . $index?></strong></td>
				<td width="3%" align="center">&nbsp;</td><?php
					}
				} ?>
				<td align="center" nowrap><strong><?php print $yyPOPrDf?>&nbsp;%<input class="noborder" type="checkbox" name="pricepercent" value="1" onclick="javascript:changeunits();" <?php if(($optFlags & 1) == 1) print "checked"?> /></strong></td>
				<td width="3%" align="center">&nbsp;</td>
				<?php if(@$wholesaleoptionpricediff==TRUE){ ?>
				<td align="center" nowrap><strong><?php print $yyWhoPri?></strong></td>
				<td width="3%" align="center">&nbsp;</td>
				<?php } ?>
				<td align="center" nowrap><strong><?php print $yyPOWtDf?>&nbsp;%<input class="noborder" type="checkbox" name="weightpercent" value="1" onclick="javascript:changeunits();" <?php if(($optFlags & 2) == 2) print "checked"?> /></strong></td>
				<?php if($useStockManagement){ ?>
				<td width="3%" align="center">&nbsp;</td>
				<td align="center" nowrap><strong><?php print $yyStkLvl?></strong></td>
				<?php } ?>
				<td width="3%" align="center">&nbsp;</td>
				<td align="center" nowrap><strong>Alt Prod ID</strong></td>
			  </tr>
<?php		if(($optFlags & 1) == 1) $pdUnits="&nbsp;%&nbsp;"; else $pdUnits="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			if(($optFlags & 2) == 2) $wdUnits="&nbsp;%&nbsp;"; else $wdUnits="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			for($rowcounter=0; $rowcounter < maxprodopts; $rowcounter++){ ?>
			  <tr>
				<td><input type="radio" name="optdefault" value="<?php print $rowcounter?>" <?php if($rowcounter < $noptions) if($alldata[$rowcounter]['optDefault']!=0) print 'checked'?> /></td>
				<td align="center"><strong>&raquo;</strong></td>
				<td align="center"><?php
					if($rowcounter < $noptions && ! $iscloning) print '<input type="hidden" name="orig' . $rowcounter . '" value="' . $alldata[$rowcounter]['optID'] . '">';
					print '<input type="text" name="opt' . $rowcounter . '" size="20" value="';
					if($rowcounter < $noptions) print str_replace('"', '&quot;',$alldata[$rowcounter]["optName"]);
					print "\" /><br />\n";
				?></td>
				<td align="center"><strong>&raquo;</strong></td>
<?php			for($index=2; $index <= $adminlanguages+1; $index++){
					if(($adminlangsettings & 32)==32){
						?><td align="center"><?php
					print '<input type="text" name="opl' . $index . 'x' . $rowcounter. '" size="20" value="';
					if($rowcounter < $noptions) print str_replace('"', '&quot;',$alldata[$rowcounter]["optName" . $index]);
					print '" /><br />' . "\r\n";
				?></td>
				<td align="center"><strong>&raquo;</strong></td><?php
					}
				} ?>
				<td align="center"><?php
					print "&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' name='pri" . $rowcounter . "' size='5' value='";
					if($rowcounter < $noptions) print (double)$alldata[$rowcounter]["optPriceDiff"];
					print "' /><span name='punitspan" . $rowcounter . "' id='punitspan" . $rowcounter . "'>" . $pdUnits . "</span><br />\n";
				?></td>
				<td align="center"><strong>&raquo;</strong></td>
				<?php if(@$wholesaleoptionpricediff==TRUE){?>
				<td align="center"><?php
					print "&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' name='wsp" . $rowcounter . "' size='5' value='";
					if($rowcounter < $noptions) print (double)$alldata[$rowcounter]["optWholesalePriceDiff"];
					print "' /><span name='pwspunitspan" . $rowcounter . "' id='pwspunitspan" . $rowcounter . "'>" . $pdUnits . "</span><br />\n";
				?></td>
				<td align="center"><strong>&raquo;</strong></td>
				<?php } ?>
				<td align="center" nowrap><?php
					print "&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' name='wei" . $rowcounter . "' size='5' value='";
					if($rowcounter < $noptions) print $alldata[$rowcounter]["optWeightDiff"];
					print "' /><span name='wunitspan" . $rowcounter . "' id='wunitspan" . $rowcounter . "'>" . $wdUnits . "</span><br />\n";
				?></td>
				<?php	if($useStockManagement){ ?>
				<td align="center"><strong>&raquo;</strong></td>
				<td align="center"><input type="text" name="optStock<?php print $rowcounter?>" size="4" value="<?php if($rowcounter < $noptions) print $alldata[$rowcounter]["optStock"]?>" /></td>
				<?php	}else{
							if($rowcounter < $noptions){ ?>
								<input type="hidden" name="optStock<?php print $rowcounter?>" value="<?php print $alldata[$rowcounter]["optStock"]?>" />
				<?php		}
						} ?>
				<td align="center"><strong>&raquo;</strong></td>
				<td align="center"><input type="text" name="regexp<?php print $rowcounter?>" size="12" value="<?php if($rowcounter < $noptions) print $alldata[$rowcounter]["optRegExp"]?>" /></td>
			  </tr>
<?php		} ?>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
<?php	} ?>
			  <tr>
                <td width="100%" colspan="3" align="center"><br /><input type="submit" value="<?php print $yySubmit?>" /><br />&nbsp;</td>
			  </tr>
			  <tr> 
                <td width="100%" colspan="3" align="center"><br />
                          <a href="admin.php"><strong><?php print $yyAdmHom?></strong></a><br />
                          &nbsp;</td>
			  </tr>
            </table></td>
		  </form>
        </tr>
<?php
}elseif(@$_POST["posted"]=="1" && $success){ ?>
        <tr>
          <td width="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyUpdSuc?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
                        <?php print $yyNoAuto?> <A href="adminprodopts.php"><strong><?php print $yyClkHer?></strong></a>.<br />
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
                <td width="100%" colspan="2" align="center"><br /><font color="#FF0000"><strong><?php print $yyOpFai?></strong></font><br /><br /><?php print $errmsg?><br /><br />
				<a href="javascript:history.go(-1)"><strong><?php print $yyClkBac?></strong></a></td>
			  </tr>
			</table></td>
        </tr>
<?php
}else{
?>
<script language="javascript" type="text/javascript">
<!--
function modrec(id) {
	document.mainform.id.value = id;
	document.mainform.act.value = "modify";
	document.mainform.submit();
}
function clone(id) {
	document.mainform.id.value = id;
	document.mainform.act.value = "clone";
	document.mainform.submit();
}
function newtextrec(id) {
	document.mainform.id.value = id;
	document.mainform.act.value = "addnew";
	document.mainform.optType.value = "3";
	document.mainform.submit();
}
function newrec(id) {
	document.mainform.id.value = id;
	document.mainform.act.value = "addnew";
	document.mainform.optType.value = "2";
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
		<form name="mainform" method="post" action="adminprodopts.php">
		  <td width="100%">
			<input type="hidden" name="posted" value="1" />
			<input type="hidden" name="act" value="xxxxx" />
			<input type="hidden" name="id" value="xxxxx" />
			<input type="hidden" name="optType" value="xxxxx" />
            <table width="100%" border="0" cellspacing="0" cellpadding="1" bgcolor="">
			  <tr> 
                <td width="100%" colspan="5" align="center"><strong><?php print $yyPOAdm?></strong><br />&nbsp;</td>
			  </tr>
			  <tr>
				<td width="32%"><strong><?php print $yyPOName?></strong></td>
				<td width="50%"><strong><?php print $yyWrkNam?></strong></td>
				<td width="6%" align="center"><strong><?php print $yyClone?></strong></td>
				<td width="6%" align="center"><strong><?php print $yyModify?></strong></td>
				<td width="6%" align="center"><strong><?php print $yyDelete?></strong></td>
			  </tr>
<?php
	$sSQL = "SELECT optGrpID,optGrpName,optGrpWorkingName FROM optiongroup ORDER BY optGrpName,optGrpWorkingName";
	$result = mysql_query($sSQL) or print(mysql_error());
	if(mysql_num_rows($result) > 0){
		$bgcolor="";
		while($rs = mysql_fetch_assoc($result)){
			if($bgcolor=="#E7EAEF") $bgcolor="#FFFFFF"; else $bgcolor="#E7EAEF"; ?>
			  <tr bgcolor="<?php print $bgcolor?>">
				<td><?php print $rs["optGrpName"]?></td>
				<td><?php print $rs["optGrpWorkingName"]?></td>
				<td align="center"><input type=button value="<?php print $yyClone?>" onclick="clone('<?php print $rs["optGrpID"]?>')" /></td>
				<td align="center"><input type=button value="<?php print $yyModify?>" onclick="modrec('<?php print $rs["optGrpID"]?>')" /></td>
				<td align="center"><input type=button value="<?php print $yyDelete?>" onclick="delrec('<?php print $rs["optGrpID"]?>')" /></td>
			  </tr>
<?php	}
	}else{
?>
			  <tr>
                <td width="100%" colspan="5" align="center"><br /><?php print $yyPONon?><br />&nbsp;</td>
			  </tr>
<?php
	}
?>
			  <tr>
                <td width="100%" colspan="5" align="center"><br /><strong><?php print $yyPOClk?> </strong>&nbsp;&nbsp;<input type="button" value="<?php print $yyPONew?>" onclick="newrec()" />&nbsp;<strong><?php print $yyOr?></strong>&nbsp;<input type="button" value="<?php print $yyPONewT?>" onclick="newtextrec()" /><br />&nbsp;</td>
			  </tr>
<?php
	if($useStockManagement){
		$sSQL = "SELECT DISTINCT optGrpID,optGrpName,optGrpWorkingName FROM optiongroup INNER JOIN options ON optiongroup.optGrpID=options.optGroup INNER JOIN prodoptions ON options.optGroup=prodoptions.poOptionGroup INNER JOIN products ON prodoptions.poProdID=products.pID WHERE options.optStock<=0 AND products.pStockByOpts<>0 AND optType IN (-2,-1,1,2) ORDER BY optGrpName,optGrpWorkingName";
		$result = mysql_query($sSQL) or print(mysql_error());
		if(mysql_num_rows($result)>0) print '<tr><td colspan="5" align="center"><strong>The following options contain at least 1 item that is out of stock</strong></td></tr>';
		while($rs = mysql_fetch_array($result)){
			if($bgcolor=="#E7EAEF") $bgcolor="#FFFFFF"; else $bgcolor="#E7EAEF"; ?>
			  <tr bgcolor="<?php print $bgcolor?>">
				<td><?php print $rs["optGrpName"]?></td>
				<td><?php print $rs["optGrpWorkingName"]?></td>
				<td align="center">&nbsp;</td>
				<td align="center"><input type=button value="<?php print $yyModify?>" onclick="modrec('<?php print $rs["optGrpID"]?>')" /></td>
				<td align="center"><input type=button value="<?php print $yyDelete?>" onclick="delrec('<?php print $rs["optGrpID"]?>')" /></td>
			  </tr><?php
		}
		mysql_free_result($result);
	} ?>
			  <tr>
                <td width="100%" colspan="5" align="center"><br />
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