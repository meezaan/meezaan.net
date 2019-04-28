<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$storesessionvalue=="") $storesessionvalue="virtualstore".time();
if($_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE) exit;
$success=TRUE;
$maxcatsperpage = 100;
if(@$maxloginlevels=="") $maxloginlevels=5;
$sSQL = "";
$alldata = "";
$alreadygotadmin = getadminsettings();
if(@$_POST["act"]=="changepos"){
	$currentorder = (int)@$_POST["selectedq"];
	$neworder = (int)@$_POST["newval"];
	$sSQL = "SELECT sectionID FROM sections ORDER BY sectionOrder";
	$result = mysql_query($sSQL) or print(mysql_error());
	$rowcounter=1;
	while($rs = mysql_fetch_assoc($result)){
		$theorder = $rowcounter;
		if($currentorder == $theorder)
			$theorder = $neworder;
		elseif(($currentorder > $theorder) && ($neworder <= $theorder))
			$theorder++;
		elseif(($currentorder < $theorder) && ($neworder >= $theorder))
			$theorder--;
		$sSQL="UPDATE sections SET sectionOrder=" . $theorder . " WHERE sectionID=" . $rs["sectionID"];
		mysql_query($sSQL) or print(mysql_error());
		$rowcounter++;
	}
	print '<meta http-equiv="refresh" content="1; url=admincats.php?pg=' . @$_POST["pg"] . '">';
}elseif(@$_POST["posted"]=="1"){
	if(@$_POST["act"]=="delete"){
		$sSQL = "DELETE FROM cpnassign WHERE cpaType=1 AND cpaAssignment='" . @$_POST["id"] . "'";
		mysql_query($sSQL) or print(mysql_error());
		$sSQL = "DELETE FROM sections WHERE sectionID=" . @$_POST["id"];
		mysql_query($sSQL) or print(mysql_error());
		$sSQL = "DELETE FROM multisections WHERE pSection=" . @$_POST["id"];
		mysql_query($sSQL) or print(mysql_error());
		print '<meta http-equiv="refresh" content="2; url=admincats.php?pg=' . @$_POST["pg"] . '">';
	}elseif(@$_POST["act"]=="domodify"){
		$sSQL = "UPDATE sections SET sectionName='" . mysql_escape_string(unstripslashes(trim(@$_POST["secname"]))) . "',sectionDescription='" . mysql_escape_string(unstripslashes(@$_POST["secdesc"])) . "',sectionImage='" . mysql_escape_string(unstripslashes(@$_POST["secimage"])) . "',topSection=" . @$_POST["tsTopSection"] . ",rootSection=" . @$_POST["catfunction"];
		$workname = mysql_escape_string(unstripslashes(trim(@$_POST["secworkname"])));
		if($workname != "")
			$sSQL .= ",sectionWorkingName='" . $workname . "'";
		else
			$sSQL .= ",sectionWorkingName='" . mysql_escape_string(unstripslashes(trim(@$_POST["secname"]))) . "'";
		for($index=2; $index <= $adminlanguages+1; $index++){
			if(($adminlangsettings & 256)==256) $sSQL .= ",sectionName" . $index . "='" . mysql_escape_string(unstripslashes(trim(@$_POST["secname" . $index]))) . "'";
			if(($adminlangsettings & 512)==512) $sSQL .= ",sectionDescription" . $index . "='" . mysql_escape_string(unstripslashes(trim(@$_POST["secdesc" . $index]))) . "'";
		}
		$sSQL .= ",sectionDisabled=" . trim(@$_POST["sectionDisabled"]);
		$sSQL .= ",sectionurl='" . mysql_escape_string(unstripslashes(trim(@$_POST["sectionurl"]))) . "'";
		$sSQL .= " WHERE sectionID=" . @$_POST["id"];
		mysql_query($sSQL) or print(mysql_error());
		print '<meta http-equiv="refresh" content="2; url=admincats.php?pg=' . @$_POST["pg"] . '">';
	}elseif(@$_POST["act"]=="doaddnew"){
		$sSQL = "SELECT MAX(sectionOrder) AS mxOrder FROM sections";
		$result = mysql_query($sSQL) or print(mysql_error());
		$rs = mysql_fetch_assoc($result);
		$mxOrder = $rs["mxOrder"];
		if(is_null($mxOrder) || $mxOrder=="") $mxOrder=1; else $mxOrder++;
		mysql_free_result($result);
		$sSQL = "INSERT INTO sections (sectionName,sectionDescription,sectionImage,sectionOrder,topSection,rootSection,sectionWorkingName";
		for($index=2; $index <= $adminlanguages+1; $index++){
			if(($adminlangsettings & 256)==256) $sSQL .= ",sectionName" . $index;
			if(($adminlangsettings & 512)==512) $sSQL .= ",sectionDescription" . $index;
		}
		$sSQL .= ",sectionDisabled,sectionurl) VALUES ('" . mysql_escape_string(unstripslashes(@$_POST["secname"])) . "','" . mysql_escape_string(unstripslashes(@$_POST["secdesc"])) . "','" . mysql_escape_string(unstripslashes(@$_POST["secimage"])) . "'," . $mxOrder . "," . @$_POST["tsTopSection"] . "," . @$_POST["catfunction"];
		$workname = mysql_escape_string(unstripslashes(trim(@$_POST["secworkname"])));
		if($workname != "")
			$sSQL .= ",'" . $workname . "'";
		else
			$sSQL .= ",'" . mysql_escape_string(unstripslashes(trim(@$_POST["secname"]))) . "'";
		for($index=2; $index <= $adminlanguages+1; $index++){
			if(($adminlangsettings & 256)==256) $sSQL .= ",'" . mysql_escape_string(unstripslashes(trim(@$_POST["secname" . $index]))) . "'";
			if(($adminlangsettings & 512)==512) $sSQL .= ",'" . mysql_escape_string(unstripslashes(trim(@$_POST["secdesc" . $index]))) . "'";
		}
		$sSQL .= "," . trim(@$_POST["sectionDisabled"]);
		$sSQL .= ",'" . mysql_escape_string(unstripslashes(trim(@$_POST["sectionurl"]))) . "')";
		mysql_query($sSQL) or print(mysql_error());
		print '<meta http-equiv="refresh" content="2; url=admincats.php?pg=' . @$_POST["pg"] . '">';
	}elseif(@$_POST["act"]=="dodiscounts"){
		$sSQL = "INSERT INTO cpnassign (cpaCpnID,cpaType,cpaAssignment) VALUES (" . @$_POST["assdisc"] . ",1,'" . @$_POST["id"] . "')";
		mysql_query($sSQL) or print(mysql_error());
		print '<meta http-equiv="refresh" content="2; url=admincats.php?pg=' . @$_POST["pg"] . '">';
	}elseif(@$_POST["act"]=="deletedisc"){
		$sSQL = "DELETE FROM cpnassign WHERE cpaID=" . @$_POST["id"];
		mysql_query($sSQL) or print(mysql_error());
		print '<meta http-equiv="refresh" content="2; url=admincats.php?pg=' . @$_POST["pg"] . '">';
	}
}
?>
<script language="javascript" type="text/javascript">
<!--
function formvalidator(theForm)
{
  if (theForm.secname.value == "")
  {
    alert("<?php print $yyPlsEntr?> \"<?php print $yyCatNam?>\".");
    theForm.secname.focus();
    return (false);
  }
  if (theForm.tsTopSection[theForm.tsTopSection.selectedIndex].value == "")
  {
    alert("<?php print $yyPlsSel?> \"<?php print $yyCatSub?>\".");
    theForm.tsTopSection.focus();
    return (false);
  }
  return (true);
}
//-->
</script>
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
<?php
if(@$_POST["posted"]=="1" && (@$_POST["act"]=="modify" || @$_POST["act"]=="addnew")){
		$ntopsections=0;
		$sSQL = "SELECT sectionID, sectionWorkingName FROM sections WHERE rootSection=0 ORDER BY sectionWorkingName";
		$result = mysql_query($sSQL) or print(mysql_error());
		while($rs = mysql_fetch_assoc($result))
			$alltopsections[$ntopsections++] = $rs;
		mysql_free_result($result);
		if(@$_POST["act"]=="modify"){
			$sSQL = "SELECT sectionID,sectionName,sectionName2,sectionName3,sectionDescription,sectionDescription2,sectionDescription3,sectionImage,sectionWorkingName,topSection,sectionDisabled,rootSection,sectionurl FROM sections WHERE sectionID=" . @$_POST["id"];
			$result = mysql_query($sSQL) or print(mysql_error());
			$rs = mysql_fetch_assoc($result);
			$sectionID = $rs["sectionID"];
			$sectionName = $rs["sectionName"];
			$sectionDescription = $rs["sectionDescription"];
			for($index=2; $index <= $adminlanguages+1; $index++){
				$sectionNames[$index] = $rs["sectionName" . $index];
				$sectionDescriptions[$index] = $rs["sectionDescription" . $index];
			}
			$sectionImage = $rs["sectionImage"];
			$sectionWorkingName = $rs["sectionWorkingName"];
			$topSection = $rs["topSection"];
			$sectionDisabled = $rs["sectionDisabled"];
			$rootSection = $rs["rootSection"];
			$sectionurl = $rs["sectionurl"];
			mysql_free_result($result);
		}else{
			$sectionID = "";
			$sectionName = "";
			$sectionDescription = "";
			for($index=2; $index <= $adminlanguages+1; $index++){
				$sectionNames[$index] = "";
				$sectionDescriptions[$index] = "";
			}
			$sectionImage = "";
			$sectionWorkingName = "";
			$topSection = 0;
			$sectionDisabled = 0;
			$rootSection = 1;
			$sectionurl = "";
		}
?>
        <tr>
		<form name="mainform" method="post" action="admincats.php" onsubmit="return formvalidator(this)">
		  <td width="100%">
			<input type="hidden" name="posted" value="1" />
			<?php if(@$_POST["act"]=="modify"){ ?>
			<input type="hidden" name="act" value="domodify" />
			<?php }else{ ?>
			<input type="hidden" name="act" value="doaddnew" />
			<?php } ?>
			<input type="hidden" name="id" value="<?php print @$_POST["id"]?>" />
			<input type="hidden" name="pg" value="<?php print @$_POST["pg"]?>" />
            <table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><strong><?php print $yyCatAdm?></strong><br />&nbsp;</td>
			  </tr>
			  <tr>
				<td width="40%" align="center" valign="top"><strong><?php print $yyCatNam?></strong><br /><input type="text" name="secname" size="30" value="<?php print str_replace("\"","&quot;",$sectionName)?>" /><br />
<?php	for($index=2; $index <= $adminlanguages+1; $index++){
			if(($adminlangsettings & 256)==256){ ?>
				<strong><?php print $yyCatNam . " " . $index ?></strong><br />
				<input type="text" name="secname<?php print $index?>" size="30" value="<?php print str_replace('"','&quot;',$sectionNames[$index])?>" /><br />
<?php		}
		} ?>
                </td>
				<td width="60%" rowspan="9" align="center" valign="top"><strong><?php print $yyCatDes?></strong><br /><textarea name="secdesc" cols="38" rows="8" wrap=virtual><?php print $sectionDescription?></textarea><br />
<?php	for($index=2; $index <= $adminlanguages+1; $index++){
			if(($adminlangsettings & 512)==512){ ?>
				<strong><?php print $yyCatDes . " " . $index ?></strong><br />
				<textarea name="secdesc<?php print $index?>" cols="38" rows="8" wrap=virtual><?php print $sectionDescriptions[$index]?></textarea><br />
<?php		}
		} ?>
				&nbsp;<br /><select name="sectionDisabled" size="1">
				<option value="0"><?php print $yyNoRes?></option>
			<?php	for($index=1; $index<= $maxloginlevels; $index++){
						print '<option value="' . $index . '"';
						if($sectionDisabled==$index) print ' selected';
						print '>' . $yyLiLev . ' ' . $index . '</option>';
					} ?>
				<option value="127"<?php if($sectionDisabled==127) print ' selected'?>><?php print $yyDisCat?></option>
				</select><br />
				&nbsp;<br /><strong>Category URL (Optional)</strong><br />
				<input type="text" name="sectionurl" size="40" value="<?php print str_replace('"','&quot;',$sectionurl)?>" />
                </td>
			  </tr>
			  <tr>
				<td align="center" valign="top"><strong><?php print $yyCatWrNa?></strong></td>
			  </tr>
			  <tr>
				<td align="center" valign="top"><input type="text" name="secworkname" size="30" value="<?php print str_replace("\"","&quot;",$sectionWorkingName)?>" /></td>
			  </tr>
			  <tr>
				<td align="center" valign="top"><strong><?php print $yyCatSub?></strong></td>
			  </tr>
			  <tr>
				<td align="center" valign="top"><select name="tsTopSection" size="1"><option value="0"><?php print $yyCatHom?></option>
				<?php	$foundcat=($topSection==0);
						for($index=0;$index<$ntopsections; $index++){
							if($alltopsections[$index]["sectionID"] != $sectionID){
								print '<option value="' . $alltopsections[$index]["sectionID"] . '"';
								if($topSection==$alltopsections[$index]["sectionID"]){
									print " selected";
									$foundcat=TRUE;
								}
								print ">" . $alltopsections[$index]["sectionWorkingName"] . "</option>\n";
							}
						}
						if(! $foundcat) print '<option value="" selected>**undefined**</option>';
					?></select>
                </td>
			  </tr>
			  <tr>
				<td align="center" valign="top"><strong><?php print $yyCatFn?></strong></td>
			  </tr>
			  <tr>
				<td align="center" valign="top"><select name="catfunction" size="1">
				  <option value="1"><?php print $yyCatPrd?></option>
				  <option value="0" <?php if($rootSection==0) print "selected"?>><?php print $yyCatCat?></option>
				  </select></td>
			  </tr>
			  <tr>
				<td align="center" valign="top"><strong><?php print $yyCatImg?></strong></td>
			  </tr>
			  <tr>
				<td align="center" valign="top"><input type="text" name="secimage" size="30" value="<?php print str_replace("\"","&quot;",$sectionImage)?>" /></td>
			  </tr>
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><input type="submit" value="<?php print $yySubmit?>" /></td>
			  </tr>
			  <tr> 
                <td width="100%" colspan="2"><br /><ul>
				  <li><?php print $yyCatEx1?></li>
				  <li><?php print $yyCatEx2?></li>
				  </ul></td>
			  </tr>
			  <tr> 
                <td width="100%" colspan="2" align="center"><br />
                          <a href="admin.php"><strong><?php print $yyAdmHom?></strong></a><br />
                          &nbsp;</td>
			  </tr>
            </table></td>
		  </form>
        </tr>
<?php
}elseif(@$_POST["act"]=="discounts"){
		$sSQL = "SELECT sectionName FROM sections WHERE sectionID=" . @$_POST["id"];
		$result = mysql_query($sSQL) or print(mysql_error());
		$rs = mysql_fetch_assoc($result);
		$thisname=$rs["sectionName"];
		mysql_free_result($result);
		$numassigns=0;
		$sSQL = "SELECT cpaID,cpaCpnID,cpnWorkingName,cpnSitewide,cpnEndDate,cpnType FROM cpnassign LEFT JOIN coupons ON cpnassign.cpaCpnID=coupons.cpnID WHERE cpaType=1 AND cpaAssignment='" . @$_POST["id"] . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		while($rs=mysql_fetch_assoc($result))
			$alldata[$numassigns++]=$rs;
		mysql_free_result($result);
		$numcoupons=0;
		$sSQL = "SELECT cpnID,cpnWorkingName,cpnSitewide FROM coupons WHERE (cpnSitewide=0 OR cpnSitewide=3) AND cpnEndDate >='" . date("Y-m-d",time()) ."'";
		$result = mysql_query($sSQL) or print(mysql_error());
		while($rs=mysql_fetch_assoc($result))
			$alldata2[$numcoupons++]=$rs;
		mysql_free_result($result);
?>
<script language="javascript" type="text/javascript">
<!--
function delrec(id) {
cmsg = "<?php print $yyConAss?>\n"
if (confirm(cmsg)) {
	document.mainform.id.value = id;
	document.mainform.act.value = "deletedisc";
	document.mainform.submit();
}
}
// -->
</script>
        <tr>
		<form name="mainform" method="post" action="admincats.php">
		  <td width="100%">
			<input type="hidden" name="posted" value="1" />
			<input type="hidden" name="act" value="dodiscounts" />
			<input type="hidden" name="id" value="<?php print @$_POST["id"]?>" />
			<input type="hidden" name="pg" value="<?php print @$_POST["pg"]?>" />
            <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="4" align="center"><strong><?php print $yyAssDis?> &quot;<?php print $thisname?>&quot;.</strong><br />&nbsp;</td>
			  </tr>
<?php
	$gotone=FALSE;
	if($numcoupons>0){
		$thestr = '<tr><td colspan="4" align="center">' . $yyAsDsCp . ': <select name="assdisc" size="1">';
		for($index=0;$index < $numcoupons;$index++){
			$alreadyassign=FALSE;
			if($numassigns>0){
				for($index2=0;$index2<$numassigns;$index2++){
					if($alldata2[$index]["cpnID"]==$alldata[$index2]["cpaCpnID"]) $alreadyassign=TRUE;
				}
			}
			if(! $alreadyassign){
				$thestr .= "<option value='" . $alldata2[$index]["cpnID"] . "'>" . $alldata2[$index]["cpnWorkingName"] . "</option>\n";
				$gotone=TRUE;
			}
		}
		$thestr .= "</select> <input type='submit' value='Go' /></td></tr>";
	}
	if($gotone){
		print $thestr;
	}else{
?>
			  <tr> 
                <td width="100%" colspan="4" align="center"><br /><strong><?php print $yyNoDis?></td>
			  </tr>
<?php
	}
	if($numassigns>0){
?>
			  <tr> 
                <td width="100%" colspan="4" align="center"><br /><strong><?php print $yyCurDis?> &quot;<?php print $thisname?>&quot;.</strong><br />&nbsp;</td>
			  </tr>
			  <tr> 
                <td><strong><?php print $yyWrkNam?></strong></td>
				<td><strong><?php print $yyDisTyp?></strong></td>
				<td><strong><?php print $yyExpire?></strong></td>
				<td align="center"><strong><?php print $yyDelete?></strong></td>
			  </tr>
<?php
		for($index=0;$index<$numassigns;$index++){
			$prefont = "";
			$postfont = "";
			if((int)$alldata[$index]["cpnSitewide"]==1 || ($alldata[$index]["cpnEndDate"] != '3000-01-01 00:00:00' && strtotime($alldata[$index]["cpnEndDate"])-time() < 0)){
				$prefont = '<font color="#FF0000">';
				$postfont = "</font>";
			}
?>
			  <tr> 
                <td><?php	print $prefont . $alldata[$index]["cpnWorkingName"] . $postfont ?></td>
				<td><?php	if($alldata[$index]["cpnType"]==0)
								print $prefont . $yyFrSShp . $postfont;
							elseif($alldata[$index]["cpnType"]==1)
								print $prefont . $yyFlatDs . $postfont;
							elseif($alldata[$index]["cpnType"]==2)
								print $prefont . $yyPerDis . $postfont; ?></td>
				<td><?php	print $prefont;
							if($alldata[$index]["cpnEndDate"] == '3000-01-01 00:00:00')
								print $yyNever;
							elseif(strtotime($alldata[$index]["cpnEndDate"])-time() < 0)
								print $yyExpird;
							else
								print date("Y-m-d",strtotime($alldata[$index]["cpnEndDate"]));
							print $postfont; ?></td>
				<td align="center"><input type="button" name="discount" value="Delete Assignment" onclick="delrec('<?php print $alldata[$index]["cpaID"]?>')" /></td>
			  </tr>
<?php
		}
	}else{
?>
			  <tr> 
                <td width="100%" colspan="4" align="center"><br /><strong><?php print $yyNoAss?></td>
			  </tr>
<?php
	}
?>
			  <tr>
                <td width="100%" colspan="4" align="center"><br />&nbsp;</td>
			  </tr>
			  <tr> 
                <td width="100%" colspan="4" align="center"><br />
                          <a href="admin.php"><strong><?php print $yyAdmHom?></strong></a><br />
                          &nbsp;</td>
			  </tr>
            </table></td>
		  </form>
        </tr>
<?php
}elseif(@$_POST["act"]=="changepos"){ ?>
        <tr>
          <td width="100%" align="center">
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p><strong><?php print $yyUpdat?> . . . . . . . </strong></font></p>
			<p>&nbsp;</p>
			<p><?php print $yyNoFor?> <a href="admincats.php"><?php print $yyClkHer?></a>.</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
		  </td>
		</tr>
<?php
}elseif(@$_POST["posted"]=="1" && $success){ ?>
        <tr>
          <td width="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyUpdSuc?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
                        <?php print $yyNoAuto?> <A href="admincats.php"><strong><?php print $yyClkHer?></strong></a>.<br />
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
			<table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><font color="#FF0000"><strong><?php print $yyOpFai?></strong></font><br /><br /><?php print $errmsg?><br /><br />
				<a href="javascript:history.go(-1)"><strong><?php print $yyClkBac?></strong></a></td>
			  </tr>
			</table></td>
        </tr>
<?php
}else{
function writeposition($currpos,$maxpos){
	$reqtext="<select name='newpos" . $currpos . "' onChange='chi(" . $currpos . ");'>";
	for($i = 1; $i <= $maxpos; $i++){
		$reqtext .= '<option'; // value='" . $i . "'";
		if($currpos==$i) $reqtext .= " selected";
		$reqtext .= ">" . $i; // . "</option>";
		if($i >= 10 && $i < ($maxpos-15) && abs($currpos-$i) > 40) $i += 9;
	}
	return($reqtext . "</select>");
}
	$allcoupon="";
	$numcoupons=0;
	$sSQL = "SELECT DISTINCT cpaAssignment FROM cpnassign WHERE cpaType=1";
	$result = mysql_query($sSQL) or print(mysql_error());
	while($rs=mysql_fetch_array($result))
		$allcoupon[$numcoupons++]=$rs;
	mysql_free_result($result);
?>
<script language="javascript" type="text/javascript">
<!--
function chi(currindex){
	var i = eval("document.mainform.newpos"+currindex+".selectedIndex");
	document.mainform.newval.value = eval("document.mainform.newpos"+currindex+".options[i].text");
	document.mainform.selectedq.value = currindex;
	document.mainform.act.value = "changepos";
	document.mainform.submit();
}
function mrk(id) {
	document.mainform.id.value = id;
	document.mainform.act.value = "modify";
	document.mainform.submit();
}
function newrec(id) {
	document.mainform.id.value = id;
	document.mainform.act.value = "addnew";
	document.mainform.submit();
}
function dsk(id) {
	document.mainform.id.value = id;
	document.mainform.act.value = "discounts";
	document.mainform.submit();
}
function drk(id) {
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
		<form name="mainform" method="post" action="admincats.php">
		  <td width="100%">
			<input type="hidden" name="posted" value="1" />
			<input type="hidden" name="act" value="xxxxx" />
			<input type="hidden" name="id" value="xxxxx" />
			<input type="hidden" name="pg" value="<?php print @$_GET["pg"]?>" />
			<input type="hidden" name="selectedq" value="1" />
			<input type="hidden" name="newval" value="1" />
            <table width="100%" border="0" cellspacing="0" cellpadding="1" bgcolor="">
			  <tr> 
                <td width="100%" colspan="6" align="center"><strong><?php print $yyCatAdm?></strong><br />&nbsp;</td>
			  </tr>
<?php
function writepagebar($CurPage, $iNumPages){
	$sLink = "<a href='admincats.php?pg=";
	$startPage = max(1,round(floor((double)$CurPage/10.0)*10));
	$endPage = min($iNumPages,round(floor((double)$CurPage/10.0)*10)+10);
	if($CurPage > 1)
		$sStr = $sLink . "1" . "'><strong><font face='Verdana'>&laquo;</font></strong></a> " . $sLink . ($CurPage-1) . "'>Previous</a> | ";
	else
		$sStr = "<strong><font face='Verdana'>&laquo;</font></strong> Previous | ";
	for($i=$startPage;$i <= $endPage; $i++){
		if($i==$CurPage)
			$sStr .= $i . " | ";
		else{
			$sStr .= $sLink . $i . "'>";
			if($i==$startPage && $i > 1) $sStr .= "...";
			$sStr .= $i;
			if($i==$endPage && $i < $iNumPages) $sStr .= "...";
			$sStr .= "</a> | ";
		}
	}
	if($CurPage < $iNumPages)
		return $sStr . $sLink . ($CurPage+1) . "'>Next</a> " . $sLink . $iNumPages . "'><strong><font face='Verdana'>&raquo;</font></strong></a>";
	else
		return $sStr . " Next <strong><font face='Verdana'>&raquo;</font></strong>";
}
	if(! is_numeric(@$_GET["pg"]))
		$CurPage = 1;
	else
		$CurPage = (int)(@$_GET["pg"]);
	$sSQL = "SELECT COUNT(*) AS bar FROM sections";
	$result = mysql_query($sSQL) or print(mysql_error());
	$numids = mysql_result($result,0,"bar");
	$iNumOfPages = ceil($numids/$maxcatsperpage);
	mysql_free_result($result);
	$sSQL = "SELECT sectionID,sectionWorkingName,sectionDescription,topSection,rootSection,sectionDisabled FROM sections ORDER BY sectionOrder LIMIT " . ($maxcatsperpage*($CurPage-1)) . ", $maxcatsperpage";
	$result = mysql_query($sSQL) or print(mysql_error());
	if($numids > 0){
		$islooping=FALSE;
		$noproducts=FALSE;
		$hascatinprodsection=FALSE;
		$rowcounter=0;
		$bgcolor="";
		if($iNumOfPages > 1) print '<tr><td align="center" colspan="6">' . writepagebar($CurPage, $iNumOfPages) . '<br /><br /></td></tr>';
?>
			  <tr>
				<td width="5%"><strong><?php print $yyOrder?></strong></td>
				<td align="left"><strong><?php print $yyCatPat?></strong></td>
				<td align="left"><strong><?php print $yyCatNam?></strong></td>
				<td width="5%" align="center"><font size="1"><strong><?php print $yyDiscnt?></strong></font></td>
				<td width="5%" align="center"><font size="1"><strong><?php print $yyModify?></strong></font></td>
				<td width="5%" align="center"><font size="1"><strong><?php print $yyDelete?></strong></font></td>
			  </tr>
<?php	while($rs = mysql_fetch_assoc($result)){
			if($bgcolor=="#E7EAEF") $bgcolor="#FFFFFF"; else $bgcolor="#E7EAEF"; ?>
<tr bgcolor="<?php print $bgcolor?>">
<td><?php print writeposition(($maxcatsperpage*($CurPage-1))+$rowcounter+1,$numids);?></td>
<td><?php
$tslist = "";
$thetopts = $rs["topSection"];
for($index=0; $index <= 10; $index++){
	if($thetopts==0){
		$tslist = $yyHome . $tslist;
		break;
	}elseif($index==10){
		$tslist = '<strong><font color="#FF0000">' . $yyLoop . '</font></strong>' . $tslist;
		$islooping=TRUE;
	}else{
		$sSQL = "SELECT sectionID,topSection,sectionWorkingName,rootSection FROM sections WHERE sectionID=" . $thetopts;
		$result2 = mysql_query($sSQL) or print(mysql_error());
		if(mysql_num_rows($result2) > 0){
			$rs2 = mysql_fetch_assoc($result2);
			$errstart = "";
			$errend = "";
			if($rs2["rootSection"]==1){
				$errstart = "<strong><font color='#FF0000'>";
				$errend = "</font></strong>";
				$hascatinprodsection=TRUE;
			}
			$tslist = " &raquo; " . $errstart . $rs2["sectionWorkingName"] . $errend . $tslist;
			$thetopts = $rs2["topSection"];
		}else{
			$tslist = '<strong><font color="#FF0000">' . $yyTopDel . '</font></strong>' . $tslist;
			break;
		}
		mysql_free_result($result2);
	}
}
print '<font size="1">' . $tslist . '</font></td><td>';
if($rs["rootSection"]==1) print "<strong>";
if($rs["sectionDisabled"]==127) print '<strike><font color="#FF0000">';
print $rs["sectionWorkingName"] . " (" . $rs["sectionID"] . ")";
if($rs["sectionDisabled"]==127) print '</font></strike>';
if($rs["rootSection"]==1) print "</strong>";
print '</td><td><input';
	for($index=0;$index<$numcoupons;$index++){
		if((int)$allcoupon[$index][0]==$rs["sectionID"]){
			print ' style="color: #FF0000" ';
			break;
		}
	}
?> type="button" value="<?php print $yyAssign?>" onclick="dsk('<?php print $rs["sectionID"]?>')"></td>
<td><input type="button" value="<?php print $yyModify?>" onclick="mrk('<?php print $rs["sectionID"]?>')" /></td>
<td><input type="button" value="<?php print $yyDelete?>" onclick="drk('<?php print $rs["sectionID"]?>')" /></td>
</tr><?php	$rowcounter++;
		}
		if($iNumOfPages > 1) print '<tr><td align="center" colspan="6"><br />' . writepagebar($CurPage, $iNumOfPages) . '</td></tr>';
		if($islooping){
?>
			  <tr><td width="100%" colspan="6"><br /><strong><font color='#FF0000'>** </font></strong><?php print $yyCatEx3?></td></tr>
<?php
		}
		if($hascatinprodsection){
?>
			  <tr><td width="100%" colspan="6"><br /><ul><li><?php print $yyCPErr?></li></ul></td></tr>
<?php
		}
?>
			  <tr><td width="100%" colspan="6"><br /><ul><li><?php print $yyCatEx4?></li></ul></td></tr>
<?php
	}else{
?>
			  <tr><td width="100%" colspan="6" align="center"><br /><strong><?php print $yyCatEx5?><br />&nbsp;</td></tr>
<?php
	}
?>
			  <tr> 
                <td width="100%" colspan="6" align="center"><br /><strong><?php print $yyCatNew?></strong>&nbsp;&nbsp;<input type="button" value="<?php print $yyNewCat?>" onclick="newrec()" /><br />&nbsp;</td>
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