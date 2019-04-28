<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$storesessionvalue=="") $storesessionvalue="virtualstore".time();
if($_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE) exit;
$success=TRUE;
if(@$_POST["posted"]=="1"){
	foreach(@$_POST as $objItem => $objValue){
		if(substr($objItem,0,4)=="idxx"){
			$ip1 = ip2long($objValue);
			if(trim(@$_POST[str_replace("xx","yy",$objItem)])<>"")
				$ip2 = ip2long(trim(@$_POST[str_replace("xx","yy",$objItem)]));
			else
				$ip2 = 0;
			if($ip1 != -1 && $ip2 != -1){
				$sSQL = "UPDATE ipblocking SET dcip1=" . $ip1 . ",dcip2=" . $ip2 . " WHERE dcid=" . substr($objItem,4);
				mysql_query($sSQL) or print(mysql_error());
			}
		}elseif(substr($objItem,0,7)=="newidxx"){
			$ip1 = ip2long($objValue);
			if(trim(@$_POST[str_replace("xx","yy",$objItem)])<>"")
				$ip2 = ip2long(trim(@$_POST[str_replace("xx","yy",$objItem)]));
			else
				$ip2 = 0;
			if($ip1 != -1 && $ip2 != -1){
				$sSQL = "INSERT INTO ipblocking (dcip1,dcip2) VALUES (" . $ip1 . "," . $ip2 . ")";
				mysql_query($sSQL) or print(mysql_error());
			}
		}elseif(substr($objItem,0,5)=="delip"){
			$sSQL = "DELETE FROM ipblocking WHERE dcid=" . substr($objItem,5);
			mysql_query($sSQL) or print(mysql_error());
		}elseif(substr($objItem,0,5)=="delss"){
			$sSQL = "DELETE FROM multibuyblock WHERE ssdenyid=" . substr($objItem,5);
			mysql_query($sSQL) or print(mysql_error());
		}
	}
	if($success)
		print '<meta http-equiv="refresh" content="2; url=adminipblock.php">';
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
                        <?php print $yyNoAuto?> <A href="adminipblock.php"><strong><?php print $yyClkHer?></strong></a>.<br />
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
<script language="javascript" type="text/javascript">
<!--

//-->
</script>
        <tr>
		  <form name="mainform" method="post" action="adminipblock.php">
		  <td width="100%">
			<input type="hidden" name="posted" value="1" />
            <table width="100%" border="0" cellspacing="0" cellpadding="1" bgcolor="">
			  <tr> 
                <td width="100%" colspan="5" align="center"><strong><?php print $yyUsIPBl?></strong><br />&nbsp;
				</td>
			  </tr>
			  <tr>
				<td align=center><strong><?php print $yySinIP?></strong></td>
				<td align=center><strong><?php print $yyLasIP?></strong></td>
				<td align=center><strong><?php print $yyDelete?></strong></td>
			  </tr><?php
	$bgcolor="#FFFFFF";
	$sSQL = "SELECT dcid,dcip1,dcip2 FROM ipblocking ORDER BY dcip1";
	$result = mysql_query($sSQL) or print(mysql_error());
	if(mysql_num_rows($result)==0){
		print '<tr><td colspan="3" align="center">' . $yyNoIPBl . '</td></tr>';
	}else{
		while($alldata = mysql_fetch_assoc($result)){
			if($bgcolor=="#E7EAEF") $bgcolor="#FFFFFF"; else $bgcolor="#E7EAEF"; ?>
<tr bgcolor="<?php print $bgcolor?>">
<td align="center"><input type="text" size="15" name="idxx<?php print $alldata["dcid"]?>" value="<?php print long2ip($alldata["dcip1"])?>" /></td>
<td align="center"><input type="text" size="15" name="idyy<?php print $alldata["dcid"]?>" value="<?php if($alldata["dcip2"] != 0) print long2ip($alldata["dcip2"])?>" /></td>
<td align="center"><input type="checkbox" name="delip<?php print $alldata["dcid"]?>"></td>
</tr>
<?php	}
	}
	for($index=0; $index < 5; $index++){
		if($bgcolor=="#E7EAEF") $bgcolor="#FFFFFF"; else $bgcolor="#E7EAEF"; ?>
<tr bgcolor="<?php print $bgcolor?>">
<td align="center"><input type="text" size="15" name="newidxx<?php print $index?>" /></td>
<td align="center"><input type="text" size="15" name="newidyy<?php print $index?>" /></td>
<td align="center">n/a</td>
</tr>
<?php
	}
	if(@$blockmultipurchase != ""){ ?>
<tr bgcolor="#FFFFFF"><td colspan="3" align="center">&nbsp;<br><strong><?php print $yyFolIPB?></strong><br>&nbsp;</td></tr>
<tr><td align="center"><strong>IP Address</strong></td>
<td align="center"><strong>Checkout Attempts</strong></td>
<td align="center"><strong>Delete</strong></td></tr>
<?php	$sSQL = "SELECT ssdenyid,ssdenyip,sstimesaccess,lastaccess FROM multibuyblock WHERE sstimesaccess>=" . $blockmultipurchase . " ORDER BY ssdenyip";
		$result = mysql_query($sSQL) or print(mysql_error());
		if(mysql_num_rows($result)==0){
			print '<tr><td colspan="3" align="center">' . $yyNoIPBl . '</td></tr>';
		}else{
			while($alldata = mysql_fetch_assoc($result)){
				if($bgcolor=="#E7EAEF") $bgcolor="#FFFFFF"; else $bgcolor="#E7EAEF"; ?>
<tr bgcolor="<?php print $bgcolor?>">
<td align="center"><?php print $alldata["ssdenyip"]?></td>
<td align="center"><?php print ($alldata["sstimesaccess"]+1)?></td>
<td align="center"><input type="checkbox" name="delss<?php print $alldata["ssdenyid"]?>"></td>
</tr>
<?php		}
		}
	}
?>			  <tr> 
                <td width="100%" colspan="5" align="center">
                  <p><br /><input type="submit" value="<?php print $yySubmit?>" />&nbsp;&nbsp;<input type="reset" value="<?php print $yyReset?>" /><br />&nbsp;</p>
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