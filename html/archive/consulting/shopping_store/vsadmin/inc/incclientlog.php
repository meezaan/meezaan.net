<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$storesessionvalue=="") $storesessionvalue="virtualstore".time();
if($_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE) exit;
$success=TRUE;
if(@$dateadjust=="") $dateadjust=0;
if(@$dateformatstr == "") $dateformatstr = "m/d/Y";
$admindatestr="Y-m-d";
if(@$admindateformat=="") $admindateformat=0;
if($admindateformat==1)
	$admindatestr="m/d/Y";
elseif($admindateformat==2)
	$admindatestr="d/m/Y";
function writepagebar($CurPage, $iNumPages){
	global $nobox,$scat,$stext,$stype,$sprice,$minprice,$yyNext,$yyPrev;
	$sLink = '<a href="adminclientlog.php?rid=' . @$_REQUEST['rid'] . '&stock=' . @$_REQUEST['stock'] . '&scat=' . $scat . '&stext=' . urlencode($stext) . '&stype=' . $stype . '&sprice=' . urlencode($sprice) . ($minprice!=""?"&sminprice=".$minprice:"") . '&pg=';
	$startPage = max(1,round(floor((double)$CurPage/10.0)*10));
	$endPage = min($iNumPages,round(floor((double)$CurPage/10.0)*10)+10);
	if($CurPage > 1)
		$sStr = $sLink . '1' . '"><strong><font face="Verdana">&laquo;</font></strong></a> ' . $sLink . ($CurPage-1) . '">'.$yyPrev.'</a> | ';
	else
		$sStr = '<strong><font face="Verdana">&laquo;</font></strong> '.$yyPrev.' | ';
	for($i=$startPage;$i <= $endPage; $i++){
		if($i==$CurPage)
			$sStr .= $i . " | ";
		else{
			$sStr .= $sLink . $i . '">';
			if($i==$startPage && $i > 1) $sStr .= '...';
			$sStr .= $i;
			if($i==$endPage && $i < $iNumPages) $sStr .= '...';
			$sStr .= '</a> | ';
		}
	}
	if($CurPage < $iNumPages)
		return $sStr . $sLink . ($CurPage+1) . '">'.$yyNext.'</a> ' . $sLink . $iNumPages . '"><strong><font face="Verdana">&raquo;</font></strong></a>';
	else
		return $sStr . ' '.$yyNext.' <strong><font face="Verdana">&raquo;</font></strong>';
}
$sSQL = '';
$alldata='';
$dorefresh=FALSE;
if(@$maxloginlevels=="") $maxloginlevels=5;
if(@$_POST["posted"]=="1"){
	if(@$_POST["act"]=="delete"){
		$sSQL = "DELETE FROM customerlogin WHERE clID='" . @$_POST['id'] . "'";
		mysql_query($sSQL) or print(mysql_error());
		$dorefresh=TRUE;
	}elseif(@$_POST["act"]=="domodify"){
		$sSQL = "UPDATE customerlogin SET clUserName='" . mysql_escape_string(@$_POST['clUserName']) . "'";
		$sSQL .= ",clPW='" . mysql_escape_string(@$_POST['clPW']) . "'";
		$sSQL .= ",clLoginLevel=" . @$_POST['clLoginLevel'];
		$cpd = trim(@$_POST['clPercentDiscount']);
		$sSQL .= "," . "clPercentDiscount=" . (is_numeric($cpd) ? $cpd : 0);
		$sSQL .= "," . "clEmail='" . mysql_escape_string(@$_POST['clEmail']) . "'";
		$clActions=0;
		if(is_array(@$_POST['clActions'])){
			foreach(@$_POST['clActions'] as $objValue){
				if(is_array($objValue)) $objValue = $objValue[0];
				$clActions += $objValue;
			}
		}
		$sSQL .= ",clActions=" . $clActions;
		$sSQL .= " WHERE clID='" . @$_POST['id'] . "'";
		mysql_query($sSQL) or print(mysql_error());
		if(@$_POST['clAllowEmail']=='ON')
			mysql_query("INSERT INTO mailinglist (email) VALUES ('" . mysql_escape_string(strtolower(@$_POST['clEmail'])) . "')");
		else
			mysql_query("DELETE FROM mailinglist WHERE email='" . mysql_escape_string(@$_POST['clEmail']) . "'");
		$dorefresh=TRUE;
	}elseif(@$_POST["act"]=="doaddnew"){
		$sSQL = "SELECT clEmail FROM customerlogin WHERE clEmail='" . mysql_escape_string(@$_POST['clEmail']) . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_array($result)){
			$success=FALSE;
			$errmsg=$yyEmReg . '<br />' . @$_POST['clEmail'];
		}
		mysql_free_result($result);
		if($success){
			$sSQL = "INSERT INTO customerlogin (clUserName,clPW,clLoginLevel,clPercentDiscount,clEmail,clDateCreated,clActions) VALUES (";
			$sSQL .= "'" . mysql_escape_string(@$_POST["clUserName"]) . "'";
			$sSQL .= ",'" . mysql_escape_string(@$_POST["clPW"]) . "'";
			$sSQL .= "," . @$_POST["clLoginLevel"];
			$cpd = trim(@$_POST["clPercentDiscount"]);
			$sSQL .= "," . (is_numeric($cpd) ? $cpd : 0);
			$sSQL .= ",'" . mysql_escape_string(@$_POST['clEmail']) . "'";
			$sSQL .= ",'" . date("Y-m-d", time() + ($dateadjust*60*60)) . "'";
			$clActions=0;
			if(is_array(@$_POST["clActions"])){
				foreach(@$_POST["clActions"] as $objValue){
					if(is_array($objValue)) $objValue = $objValue[0];
					$clActions += $objValue;
				}
			}
			$sSQL .= "," . $clActions . ")";
			mysql_query($sSQL) or print(mysql_error());
			if(@$_POST['clAllowEmail']=='ON')
				mysql_query("INSERT INTO mailinglist (email) VALUES ('" . mysql_escape_string(strtolower(@$_POST['clEmail'])) . "')");
			else
				mysql_query("DELETE FROM mailinglist WHERE email='" . mysql_escape_string(@$_POST['clEmail']) . "'");
			$dorefresh=TRUE;
		}
	}
}
if($dorefresh){
	print '<meta http-equiv="refresh" content="1; url=adminclientlog.php';
	print '?stext=' . urlencode(@$_POST['stext']) . '&accdate=' . urlencode(@$_POST['accdate']) . '&slevel=' . urlencode(@$_POST['slevel']) . '&stype=' . urlencode(@$_POST['stype']) . '&daterange=' . urlencode(@$_POST['daterange']) . '&pg=' . urlencode(@$_POST['pg']);
	print '">';
}
?>
<script language="javascript" type="text/javascript">
<!--
function formvalidator(theForm){
if (theForm.clUserName.value == ""){
alert("<?php print $yyPlsEntr?> \"<?php print $yyLiName?>\".");
theForm.clUserName.focus();
return (false);
}
var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_@.-";
var checkStr = theForm.clPW.value;
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
    alert("<?php print $yyOnlyAl?> \"<?php print $yyPass?>\".");
    theForm.clPW.focus();
    return (false);
}
if(document.mainform.elements['clActions[]'].options[3].selected && document.mainform.elements['clActions[]'].options[4].selected){
    alert("<?php print $yyWSDsc?>");
    theForm.elements['clActions[]'].focus();
    return (false);
}
document.mainform.clPercentDiscount.disabled=false;
return (true);
}
function checkperdisc(){
	document.mainform.clPercentDiscount.disabled=!document.mainform.elements['clActions[]'].options[4].selected;
}
//-->
</script>
<?php	if(@$_POST['posted']=='1' && (@$_POST['act']=='modify' || @$_POST['act']=='addnew')){
			if($_POST['act']=='modify'){
				$sSQL = "SELECT clUserName,clPW,clLoginLevel,clActions,clPercentDiscount,clEmail,clDateCreated FROM customerlogin WHERE clID='" . @$_POST["id"] . "'";
				$result = mysql_query($sSQL) or print(mysql_error());
				$rs = mysql_fetch_array($result);
				$clUserName=$rs['clUserName'];
				$clPW=$rs['clPW'];
				$clLoginLevel=$rs['clLoginLevel'];
				$clActions=$rs['clActions'];
				$clPercentDiscount=$rs['clPercentDiscount'];
				$clEmail=$rs['clEmail'];
				$clDateCreated=$rs['clDateCreated'];
				$sSQL = "SELECT email FROM mailinglist WHERE email='" . mysql_escape_string($clEmail) . "'";
				$result = mysql_query($sSQL) or print(mysql_error());
				if(mysql_num_rows($result)>0) $clAllowEmail=1; else $clAllowEmail=0;
			}else{
				$clUserName='';
				$clPW='';
				$clLoginLevel=0;
				$clActions=0;
				$clPercentDiscount=0;
				$clEmail='';
				$clDateCreated=date('Y-m-d');
				$clAllowEmail=0;
			}
?>
	  <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr>
		  <form name="mainform" method="post" action="adminclientlog.php" onsubmit="return formvalidator(this)">
			<td width="100%" align="center">
<?php		writehiddenvar('posted', '1');
			if($_POST['act']=='modify')
				writehiddenvar('act', 'domodify');
			else
				writehiddenvar('act', 'doaddnew');
			writehiddenvar('stext', @$_POST['stext']);
			writehiddenvar('accdate', @$_POST['accdate']);
			writehiddenvar('daterange', @$_POST['daterange']);
			writehiddenvar('slevel', @$_POST['slevel']);
			writehiddenvar('stype', @$_POST['stype']);
			writehiddenvar('pg', @$_POST['pg']);
			writehiddenvar('id', @$_POST['id']); ?>
            <table width="100%" border="0" cellspacing="2" cellpadding="2" bgcolor="">
			  <tr> 
                <td width="100%" colspan="4" align="center"><strong><?php print $yyLiAdm?></strong><br /><br /><?php print '<strong>' . $yyDateCr. ':</strong> ' . date($admindatestr, strtotime($clDateCreated)); ?><br /><br /></td>
			  </tr>
			  <tr>
				<td align="right"><strong><?php print $yyLiName?>:</strong></td>
				<td align="left"><input type="text" name="clUserName" size="20" value="<?php print str_replace('"','&quot;',$clUserName)?>" /></td>
				<td align="right" rowspan="6" valign="top"><strong><?php print $yyActns?>:</strong></td>
				<td rowspan="6" align="left" valign="top"><select name="clActions[]" size="6" onChange="checkperdisc()" multiple>
				<option value="1"<?php if(($clActions & 1) == 1) print " selected" ?>><?php print $yyExStat?></option>
				<option value="2"<?php if(($clActions & 2) == 2) print " selected" ?>><?php print $yyExCoun?></option>
				<option value="4"<?php if(($clActions & 4) == 4) print " selected" ?>><?php print $yyExShip?></option>
				<option value="8"<?php if(($clActions & 8) == 8) print " selected" ?>><?php print $yyWholPr?></option>
				<option value="16"<?php if(($clActions & 16) == 16) print " selected" ?>><?php print $yyPerDis?></option>
				</select></td>
			  </tr>
			  <tr>
				<td align="right"><p><strong><?php print $yyEmail?>:</strong></td>
				<td align="left"><input type="text" name="clEmail" size="20" value="<?php print str_replace('"','&quot;',$clEmail)?>" /></td>
			  </tr>
			  <tr>
				<td align="right"><p><strong><?php print $yyPass?>:</strong></td>
				<td align="left"><input type="text" name="clPW" size="20" value="<?php print str_replace('"','&quot;',$clPW)?>" /></td>
			  </tr>
			  <tr>
				<td align="right"><strong><?php print $yyLiLev?>:</strong></td>
				<td align="left"><select name="clLoginLevel" size="1">
				<?php	for($rowcounter=0; $rowcounter<=$maxloginlevels; $rowcounter++){
							print '<option value="' . $rowcounter . '"';
							if($rowcounter==(int)$clLoginLevel) print " selected";
							print '>&nbsp; ' . $rowcounter . " </option>\r\n";
						} ?>
				</select></td>
			  </tr>
			  <tr>
				<td align="right"><p><strong><?php print $yyPerDis?>:</strong></td>
				<td align="left"><input type="text" name="clPercentDiscount" size="10" value="<?php print $clPercentDiscount?>" /></td>
			  </tr>
			  <tr>
				<td align="right"><strong><?php print $yyAllEml?>:</strong></td>
				<td align="left"><input type="checkbox" name="clAllowEmail" value="ON"<?php if($clAllowEmail!=0) print ' checked'?> /></td>
			  </tr>
			  <tr>
                <td width="100%" colspan="4" align="center"><br /><input type="submit" value="<?php print $yySubmit?>" />&nbsp;<input type="reset" value="<?php print $yyReset?>" /><br />&nbsp;</td>
			  </tr>
			  <tr> 
                <td width="100%" colspan="4" align="center"><br />
                          <a href="admin.php"><strong><?php print $yyAdmHom?></strong></a><br />
                          &nbsp;</td>
			  </tr>
            </table></td>
		  </form>
        </tr>
<script language="javascript" type="text/javascript">
<!--
checkperdisc();
//-->
</script>
<?php	}elseif(@$_POST["posted"]=="1" && $success){ ?>
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr>
          <td width="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyUpdSuc?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
                        <?php print $yyNoAuto?> <A href="adminclientlog.php"><strong><?php print $yyClkHer?></strong></a>.<br />
                        <br />
				<img src="../images/clearpixel.gif" width="300" height="3" alt="" />
                </td>
			  </tr>
			</table></td>
        </tr>
	  </table>
<?php	}elseif(@$_POST["posted"]=="1"){ ?>
	  <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr>
          <td width="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><font color="#FF0000"><strong><?php print $yyOpFai?></strong></font><br /><br /><?php print $errmsg?><br /><br />
				<a href="javascript:history.go(-1)"><strong><?php print $yyClkBac?></strong></a></td>
			  </tr>
			</table></td>
        </tr>
	  </table>
<?php	}else{ ?>
<script language="javascript" type="text/javascript" src="popcalendar.js">
</script>
<script language="javascript" type="text/javascript">
<!--
function mrec(id) {
	document.mainform.id.value = id;
	document.mainform.act.value = "modify";
	document.mainform.submit();
}
function newrec(id) {
	document.mainform.id.value = id;
	document.mainform.act.value = "addnew";
	document.mainform.submit();
}
function drec(id) {
cmsg = "<?php print $yyConDel?>\n"
if (confirm(cmsg)) {
	document.mainform.id.value = id;
	document.mainform.act.value = "delete";
	document.mainform.submit();
}
}
function startsearch(){
	document.mainform.action="adminclientlog.php";
	document.mainform.act.value = "search";
	document.mainform.stock.value = "";
	document.mainform.posted.value = "";
	document.mainform.submit();
}
// -->
</script>
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr>
		<form name="mainform" method="post" action="adminclientlog.php">
		  <td width="100%">
			<input type="hidden" name="posted" value="1" />
			<input type="hidden" name="act" value="xxxxx" />
			<input type="hidden" name="stock" value="" />
			<input type="hidden" name="id" value="xxxxx" />
			<input type="hidden" name="pg" value="<?php print (@$_POST['act']=='search' ? '1' : @$_GET['pg']) ?>" />
<?php	$themask = 'yyyy-mm-dd';
		if($admindateformat==1)
			$themask='mm/dd/yyyy';
		elseif($admindateformat==2)
			$themask='dd/mm/yyyy';
		$thelevel = @$_REQUEST['slevel'];
		if(@$thelevel != '') $thelevel = (int)$thelevel;
?>			<table class="cobtbl" width="100%" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
			  <tr> 
                <td class="cobhl" width="25%" align="right" bgcolor="#EBEBEB"><?php print $yySrchFr?>:</td>
				<td class="cobll" width="25%" bgcolor="#FFFFFF"><input type="text" name="stext" size="20" value="<?php print str_replace("\"","&quot;",@$_REQUEST['stext'])?>" /></td>
				<td class="cobhl" width="20%" align="right" bgcolor="#EBEBEB"><?php print $yyDate?>:</td>
				<td class="cobll" bgcolor="#FFFFFF">
					<select name="daterange" size="1">
					<option value=""><?php print $yySinc?></option>
					<option value="1"<?php if(@$_REQUEST['daterange']=="1") print ' selected'?>><?php print $yyTill?></option>
					<option value="2"<?php if(@$_REQUEST['daterange']=="2") print ' selected'?>><?php print $yyOn?></option>
					</select>
					<input type="text" size="14" name="accdate" value="<?php print @$_REQUEST['accdate']?>" /> <input type=button onclick="popUpCalendar(this, document.forms.mainform.accdate, '<?php print $themask?>', -205)" value='DP' />
				</td>
			  </tr>
			  <tr>
			    <td class="cobhl" align="right" bgcolor="#EBEBEB"><?php print $yySrchTp?>:</td>
				<td class="cobll" bgcolor="#FFFFFF"><select name="stype" size="1">
					<option value=""><?php print $yySrchAl?></option>
					<option value="any" <?php if(@$_REQUEST['stype']=='any') print "selected"?>><?php print $yySrchAn?></option>
					<option value="exact" <?php if(@$_REQUEST['stype']=='exact') print "selected"?>><?php print $yySrchEx?></option>
					</select>
				</td>
				<td class="cobhl" align="right" bgcolor="#EBEBEB"><?php print $yyLiLev?>:</td>
				<td class="cobll" bgcolor="#FFFFFF">
				  <select name="slevel" size="1">
				  <option value=""><?php print $yyAllLev?></option>
<?php						for($rowcounter=1; $rowcounter <= $maxloginlevels; $rowcounter++){
								print "<option value='" . $rowcounter . "'";
								if($thelevel != ''){
									if($thelevel==$rowcounter) print ' selected';
								}
								print '>&nbsp; ' . $rowcounter . ' </option>';
							} ?>
				  </select>
				</td>
              </tr>
			  <tr>
				    <td class="cobhl" bgcolor="#EBEBEB">&nbsp;</td>
				    <td class="cobll" bgcolor="#FFFFFF" colspan="3"><table width="100%" cellspacing="0" cellpadding="0" border="0">
					    <tr>
						  <td class="cobll" bgcolor="#FFFFFF" align="center"><input type="button" value="<?php print $yyListRe?>" onclick="startsearch();" />
							<input type="button" value="<?php print $yyCLNew?>" onclick="newrec();" />
						  </td>
						  <td class="cobll" bgcolor="#FFFFFF" height="26" width="20%" align="right">&nbsp;</td>
						</tr>
					  </table></td>
				  </tr>
			</table>
            <table width="100%" border="0" cellspacing="0" cellpadding="1" bgcolor="">
<?php	if(@$_POST['act']=='search' || @$_GET['pg'] != ''){
			$bgcolor='#FFFFFF';
			function displayprodrow($xrs){
				global $bgcolor,$yyModify,$yyDelete;
			?><tr bgcolor="<?php print $bgcolor?>"><td><?php print $xrs['clUserName']?></td><td><?php print $xrs['clEmail']?></td><td><?php print $xrs['clPW']?></td><td align="center"><?php print $xrs['clLoginLevel']?></td>
				<td><?php	if(($xrs['clActions'] & 1) == 1) print 'STE ';
							if(($xrs['clActions'] & 2) == 2) print 'CTE ';
							if(($xrs['clActions'] & 4) == 4) print 'SHE ';
							if(($xrs['clActions'] & 8) == 8) print 'WSP ';
							if(($xrs['clActions'] & 16) == 16) print 'PED ';
				?>&nbsp;</td>
				<td><input type="button" value="<?php print $yyModify?>" onclick="mrec('<?php print $xrs['clID']?>',event)" /></td>
				<td><input type="button" value="<?php print $yyDelete?>" onclick="drec('<?php print $xrs['clID']?>')" /></td></tr>
<?php		}
			function displayheaderrow(){
				global $yyLiName,$yyEmail,$yyPass,$yyLiLev,$yyActns,$yyModify,$yyDelete;
?>
			  <tr>
				<td><strong><?php print $yyLiName?></strong></td>
				<td><strong><?php print $yyEmail?></strong></td>
				<td><strong><?php print $yyPass?></strong></td>
				<td align="center"><strong><?php print $yyLiLev?></strong></td>
				<td><strong><?php print $yyActns?></strong></td>
				<td width="5%" align="center"><font size="1"><strong><?php print $yyModify?></strong></font></td>
				<td width="5%" align="center"><font size="1"><strong><?php print $yyDelete?></strong></font></td>
			  </tr>
<?php		}
		$pidlist='';
		$whereand = ' WHERE ';
		$sSQL = "SELECT clID,clUserName,clActions,clLoginLevel,clPercentDiscount,clEmail,clPW FROM customerlogin ";
		if($thelevel != ''){
			$sSQL .= $whereand . " clLoginLevel >=" . $thelevel;
			$whereand=' AND ';
		}
		$accdate = trim(@$_REQUEST['accdate']);
		if($accdate != ''){
			$accdate = parsedate($accdate);
			if(@$_REQUEST['daterange']=='1') // Till
				$sSQL .= $whereand . "clDateCreated <= '" . date("Y-m-d", $accdate) . "' ";
			elseif(@$_REQUEST['daterange']=='2') // On
				$sSQL .= $whereand . "clDateCreated BETWEEN '"  . date("Y-m-d", $accdate) . "' AND '" . date("Y-m-d", $accdate+(60*60*24)) . "' ";
			else // Since
				$sSQL .= $whereand . "clDateCreated >= '" . date("Y-m-d", $accdate) . "' ";
			$whereand=' AND ';
		}
		if(trim(@$_REQUEST['stext']) != ''){
			$Xstext = mysql_escape_string($stext);
			$aText = split(" ",$Xstext);
			$aFields[0]='clUserName';
			$aFields[1]='clPw';
			$aFields[2]='clEmail';
			if($stype=="exact"){
				$sSQL .= $whereand . "(clUserName LIKE '%" . $Xstext . "%' OR clPw LIKE '%" . $Xstext . "%' OR clEmail LIKE '%" . $Xstext . "%') ";
				$whereand=' AND ';
			}else{
				$sJoin="AND ";
				if($stype=="any") $sJoin="OR ";
				$sSQL .= $whereand . "(";
				$whereand=' AND ';
				for($index=0;$index<=2;$index++){
					$sSQL .= "(";
					$rowcounter=0;
					$arrelms=count($aText);
					foreach($aText as $theopt){
						if(is_array($theopt))$theopt=$theopt[0];
						$sSQL .= $aFields[$index] . " LIKE '%" . $theopt . "%' ";
						if(++$rowcounter < $arrelms) $sSQL .= $sJoin;
					}
					$sSQL .= ") ";
					if($index < 2) $sSQL .= "OR ";
				}
				$sSQL .= ") ";
			}
		}
		$sSQL .= ' ORDER BY clUserName';
		if(! @is_numeric($_GET["pg"]))
			$CurPage = 1;
		else
			$CurPage = (int)($_GET["pg"]);
		if(@$adminproductsperpage=='') $adminproductsperpage=200;
		// $tmpSQL = "SELECT COUNT(DISTINCT products.pId) AS bar" . $sSQL;
		$tmpSQL = str_replace('clID,clUserName,clActions,clLoginLevel,clPercentDiscount,clEmail,clPW', 'COUNT(*) AS bar', $sSQL);
		$allprods = mysql_query($tmpSQL) or print(mysql_error());
		$iNumOfPages = ceil(mysql_result($allprods,0,"bar")/$adminproductsperpage);
		mysql_free_result($allprods);
		$sSQL .= ' LIMIT ' . ($adminproductsperpage*($CurPage-1)) . ', ' . $adminproductsperpage;
		$result = mysql_query($sSQL) or print(mysql_error());
		$haveerrprods=FALSE;
		if(mysql_num_rows($result) > 0){
			if($iNumOfPages > 1) print '<tr><td colspan="6" align="center">' . writepagebar($CurPage, $iNumOfPages) . '</td></tr>';
			displayheaderrow();
			$addcomma='';
			while($rs = mysql_fetch_assoc($result)){
				if($bgcolor=='#E7EAEF') $bgcolor='#FFFFFF'; else $bgcolor='#E7EAEF';
				displayprodrow($rs,$rowcounter++);
				$pidlist .= $addcomma . "'" . $rs['clID'] . "'";
				$addcomma=',';
			}
			if($haveerrprods) print '<tr><td width="100%" colspan="6"><br /><strong><font color="#FF0000">* </font></strong>' . $yySeePr . '</td></tr>';
			if($iNumOfPages > 1) print '<tr><td colspan="6" align="center">' . writepagebar($CurPage, $iNumOfPages) . '</td></tr>';
		}else{
			print '<tr><td width="100%" colspan="6" align="center"><br />' . $yyPrNone . '<br />&nbsp;</td></tr>';
		}
	} ?>
			  <tr>
                <td width="100%" colspan="6" align="center"><br /><ul><li><?php print $yyCLTyp?></li></ul>
                          <a href="admin.php"><strong><?php print $yyAdmHom?></strong></a><br />
				<img src="../images/clearpixel.gif" width="300" height="3" alt="" /></td>
			  </tr>
            </table></td>
		  </form>
        </tr>
      </table>
<?php
}
?>