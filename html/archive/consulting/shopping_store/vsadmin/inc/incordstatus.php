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
if(@$_POST["act"]=="domodify"){
	for($index=0; $index < 30; $index++){
		$statusid=trim(@$_POST["statusid" . $index]);
		if($statusid != ""){
			$statPrivate = trim(mysql_escape_string(unstripslashes(@$_POST["privstatus" . $index])));
			$statPublic = trim(mysql_escape_string(unstripslashes(@$_POST["pubstatus" . $index])));
			if($statPublic=="") $statPublic = $statPrivate;
			$sSQL = "UPDATE orderstatus SET statPrivate='" . $statPrivate . "',statPublic='" . $statPublic . "'";
			for($index2=2; $index2 <= $adminlanguages+1; $index2++){
				if(($adminlangsettings & 64)==64) $sSQL .= ",statPublic" . $index2 . "='" . trim(mysql_escape_string(unstripslashes(@$_POST["pubstatus" . $index . "x" . $index2]))) . "'";
			}
			$sSQL .= " WHERE statID=" . $statusid;
			mysql_query($sSQL) or print(mysql_error());
		}
	}
	print '<meta http-equiv="refresh" content="3; url=admin.php">';
}
?>
<script language="javascript" type="text/javascript">
<!--
function formvalidator(theForm){
for(index=0;index<=3;index++){
theelm=eval('theForm.privstatus'+index);
if(theelm.value == ""){
alert("Please enter a value in the field \"Private Text (Status " + (index+1) + ")\".");
theelm.focus();
return (false);
}
}
return (true);
}
//-->
</script>
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
<?php	if(@$_POST["act"]=="domodify" && $success){ ?>
        <tr>
          <td width="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyUpdSuc?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
				<?php print $yyNoAuto?> <A href="admin.php"><strong><?php print $yyClkHer?></strong></a>.<br /><br />
				<img src="../images/clearpixel.gif" width="300" height="3" alt="" />
                </td>
			  </tr>
			</table></td>
        </tr>
<?php	}elseif(@$_POST["act"]=="domodify"){ ?>
        <tr>
          <td width="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><font color="#FF0000"><strong><?php print $yyOpFai?></strong></font><br /><br /><?php print $errmsg?><br /><br />
				<a href="javascript:history.go(-1)"><strong><?php print $yyClkBac?></strong></a></td>
			  </tr>
			</table></td>
        </tr>
<?php	}else{
			if(($adminlangsettings & 64) != 64) $numcols=5; else $numcols=5+$adminlanguages; ?>
        <tr>
		  <form name="mainform" method="post" action="adminordstatus.php" onsubmit="return formvalidator(this)">
          <td width="100%" align="center">
			<input type="hidden" name="posted" value="1">
			<input type="hidden" name="act" value="domodify">
            <table width="500" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="<?php print $numcols?>" align="center"><br /><strong><?php print $yyOSAdm?></strong><br />&nbsp;</td>
			  </tr>
			  <tr>
				<td align="center" valign="top" width="50"><strong>&nbsp;</strong></td>
				<td align="center" valign="top"><strong>&nbsp;</strong></td>
				<td align="center" valign="top"><strong><?php print $yyPrTxt?></strong></td>
				<td align="center" valign="top"><strong><?php print $yyPubTxt?></strong></td>
<?php		for($index=2; $index <= $adminlanguages+1; $index++){
				if(($adminlangsettings & 64)==64) print '<td align="center" valign="top"><strong>' . $yyPubTxt . " " . $index . '</strong></td>';
			} ?>
				<td align="center" valign="top" width="50"><strong>&nbsp;</strong></td>
			  </tr>
<?php
	$sSQL = "SELECT statID,statPrivate,statPublic,statPublic2,statPublic3 FROM orderstatus ORDER BY statID";
	$result = mysql_query($sSQL) or print(mysql_error());
	$rowcounter=0;
	while($rs = mysql_fetch_assoc($result)){
		if($rs["statID"]==4){ ?>
			  <tr> 
                <td width="100%" colspan="<?php print $numcols?>" align="center"><font size="1"><?php print $yyOSExp1?></font></td>
			  </tr>
<?php	} ?>
			  <tr>
				<td align="center" valign="top"><strong>&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
				<td align="right"><input type="hidden" name="statusid<?php print $rowcounter?>" value="<?php print $rs["statID"] ?>"><?php print $yyStatus?>&nbsp;<?php print $rowcounter?>:</td>
				<td align="center"><input type="text" size="20" name="privstatus<?php print $rowcounter?>" value="<?php print str_replace('"','&quot;',trim($rs["statPrivate"])) ?>"></td>
				<td align="center"><input type="text" size="20" name="pubstatus<?php print $rowcounter?>" value="<?php print str_replace('"','&quot;',trim($rs["statPublic"])) ?>"></td>
<?php		for($index=2; $index <= $adminlanguages+1; $index++){
				if(($adminlangsettings & 64)==64) print '<td align="center"><input type="text" size="20" name="pubstatus' . $rowcounter . "x" . $index . '" value="' . str_replace('"','&quot;',trim($rs["statPublic" . $index])) . '"></td>';
			} ?>
				<td align="center" valign="top"><strong>&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
			  </tr>
<?php	$rowcounter++;
	} ?>
			  <tr> 
                <td width="100%" colspan="<?php print $numcols?>" align="center"><input type="submit" value="<?php print $yySubmit?>"></td>
			  </tr>
			  <tr> 
                <td width="100%" colspan="<?php print $numcols?>" align="center"><br /><a href="admin.php"><strong><?php print $yyAdmHom?></strong></a><br />&nbsp;</td>
			  </tr>
            </table></td>
		  </form>
        </tr>

<?php	} ?>
      </table>