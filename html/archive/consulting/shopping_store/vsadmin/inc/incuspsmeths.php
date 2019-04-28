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
$method=trim(@$_REQUEST["method"]);
if($method != '') $shipType=(int)$method;
$shipmet = "USPS";
if($shipType==4) $shipmet = "UPS";
if($shipType==6) $shipmet = $yyCanPos;
if($shipType==7) $shipmet = "FedEx";
if(@$_POST["posted"]=="1"){
	if($shipType==3){
		for($index=1;$index<=25;$index++){
			if(trim(@$_POST["methodshow" . $index]) != ""){
				$sSQL = "UPDATE uspsmethods SET uspsShowAs='" . mysql_escape_string(unstripslashes(trim(@$_POST["methodshow" . $index]))) . "',";
				if(@$_POST["methodfsa" . $index]=="ON")
					$sSQL .= "uspsFSA=1,";
				else
					$sSQL .= "uspsFSA=0,";
				if(@$_POST["methoduse" . $index]=="ON")
					$sSQL .= "uspsUseMethod=1 WHERE uspsID=" . $index;
				else
					$sSQL .= "uspsUseMethod=0 WHERE uspsID=" . $index;
				mysql_query($sSQL) or print(mysql_error());
			}
		}
	}elseif($shipType==4 || $shipType==6 || $shipType==7){
		$indexadd=0;
		if($shipType==6) $indexadd=100; elseif($shipType==7) $indexadd=200;
		for($index=100+$indexadd;$index<=125+$indexadd;$index++){
			if(trim(@$_POST["methodshow" . $index]) != ""){
				$sSQL = "UPDATE uspsmethods SET ";
				if(@$_POST["methodfsa" . $index]=="ON")
					$sSQL .= "uspsFSA=1,";
				else
					$sSQL .= "uspsFSA=0,";
				if(@$_POST["methoduse" . $index]=="ON")
					$sSQL .= "uspsUseMethod=1 WHERE uspsID=" . $index;
				else
					$sSQL .= "uspsUseMethod=0 WHERE uspsID=" . $index;
				mysql_query($sSQL) or print(mysql_error());
			}
		}
	}
	print "<meta http-equiv=\"refresh\" content=\"3; url=admin.php\">";
}
?>
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
<?php if($method==''){ ?>
        <tr>
          <td width="100%" align="center">
            <table width="80%" border="0" cellspacing="1" cellpadding="2" bgcolor="">
			  <tr>
                <td colspan="3" align="center"><strong><?php print $yyUsUpd . " " . $yyShpMet?>.</strong><br />&nbsp;</td>
			  </tr>
			  <tr bgcolor="#E7EAEF">
				<td align="left">&nbsp;&nbsp;<a href="adminuspsmeths.php?method=3"><strong><?php print $yyEdit . " USPS " . $yyShpMet?></strong></a> </td>
				<td>&nbsp; </td>
				<td><input type="button" value="<?php print $yyEdit . " " . $yyShpMet?>" onclick="javascript:document.location='adminuspsmeths.php?method=3'"></td>
			  </tr>
			  <tr bgcolor="#FFFFFF">
				<td align="left">&nbsp;&nbsp;<a href="adminuspsmeths.php?method=4"><strong><?php print $yyEdit . " UPS " . $yyShpMet?></strong></a> </td>
				<td><input type="button" value="<?php print $yyRegUPS?>" onclick="javascript:document.location='adminupslicense.php'"></td>
				<td><input type="button" value="<?php print $yyEdit . " " . $yyShpMet?>" onclick="javascript:document.location='adminuspsmeths.php?method=4'"></td>
			  </tr>
			  <tr bgcolor="#E7EAEF">
				<td align="left">&nbsp;&nbsp;<a href="adminuspsmeths.php?method=6"><strong><?php print $yyEdit . " " . $yyCanPos . " " . $yyShpMet?></strong></a> </td>
				<td>&nbsp; </td>
				<td><input type="button" value="<?php print $yyEdit . " " . $yyShpMet?>" onclick="javascript:document.location='adminuspsmeths.php?method=6'"></td>
			  </tr>
			  <tr bgcolor="#FFFFFF"> 
				<td align="left">&nbsp;&nbsp;<a href="adminuspsmeths.php?method=7"><strong><?php print $yyEdit . " FedEx " . $yyShpMet?></strong></a> </td>
				<td><input type="button" value="<?php print str_replace("UPS","FedEx",$yyRegUPS)?>" onclick="javascript:document.location='adminfedexlicense.php'"></td>
				<td><input type="button" value="<?php print $yyEdit . " " . $yyShpMet?>" onclick="javascript:document.location='adminuspsmeths.php?method=7'"></td>
			  </tr>
			  <tr bgcolor="#FFFFFF">
                <td colspan="3" align="center"><br />&nbsp;<br />&nbsp;</td>
			  </tr>
			</table></td>
        </tr>
<?php }elseif(@$_POST["posted"]=="1" && $success){ ?>
        <tr>
          <td width="100%">
            <table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyUpdSuc?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
                        <?php print $yyNoAuto?> <A href="admin.php"><strong><?php print $yyClkHer?></strong></a>.<br />
                        <br />
				<img src="../images/clearpixel.gif" width="300" height="1" alt="" />
                </td>
			  </tr>
			</table></td>
        </tr>
<?php }else{ ?>
        <tr>
		  <form method="post" action="adminuspsmeths.php">
			<td width="100%">
			<input type="hidden" name="posted" value="1" />
			<input type="hidden" name="method" value="<?php print $method?>" />
            <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="">
			  <tr> 
                <td width="100%" colspan="5" align="center"><br /><strong><?php print $yyUsUpd . " " . $shipmet . " " . $yyShpMet?>.</strong><br />&nbsp;</td>
			  </tr>
<?php	if(! $success){ ?>
			  <tr> 
                <td width="100%" colspan="5" align="center"><br /><font color="#FF0000"><?php print $errmsg?></font>
                </td>
			  </tr>
<?php	}
		$sSQL = "SELECT uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal,uspsFSA FROM uspsmethods ";
		if($shipType==3)
			$sSQL .= "WHERE uspsID<100 ";
		elseif($shipType==4)
			$sSQL .= "WHERE uspsID>100 AND uspsID<200 ";
		elseif($shipType==6)
			$sSQL .= "WHERE uspsID>200 AND uspsID<300 ";
		elseif($shipType==7)
			$sSQL .= "WHERE uspsID>300 AND uspsID<400 ";
		$sSQL .= "ORDER BY uspsLocal DESC, uspsID";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($shipType==3){
?>
			  <tr>
				<td colspan="5"><ul><li><font size="1"><?php print $yyUSS1?></font></li>
				<li><font size="1"><?php print $yyUSS2?> 
				<a href="http://www.usps.com">http://www.usps.com</a>.</font></li></ul></td>
			  </tr>
<?php		while($allmethods=mysql_fetch_assoc($result)){ ?>
			  <tr>
			    <td align="right"><?php print $yyUSPSMe?>:</td>
				<td align="left"><font size="1"><strong><?php print $allmethods["uspsMethod"]?></strong></font></td>
				<td align="center"><?php print $yyUseMet?></td>
				<td align="center"><acronym title="<?php print $yyFSApp?>"><?php print $yyFSA?></acronym></td>
				<td align="center"><?php print $yyType?></td>
			  </tr>
			  <tr>
			    <td align="right"><?php print $yyShwAs?>:</td>
				<td align="left"><input type="text" name="methodshow<?php print $allmethods["uspsID"]?>" value="<?php print $allmethods["uspsShowAs"]?>" size="36" /></td>
				<td align="center"><input type="checkbox" name="methoduse<?php print $allmethods["uspsID"]?>" value="ON" <?php if((int)$allmethods["uspsUseMethod"]==1) print "checked"?> /></td>
				<td align="center"><input type="checkbox" name="methodfsa<?php print $allmethods["uspsID"]?>" value="ON" <?php if((int)$allmethods["uspsFSA"]==1) print "checked"?> /></td>
				<td align="center"><?php if($allmethods["uspsLocal"]==1) print '<font color="#FF0000">Domestic</font>'; else print '<font color="#0000FF">Internat.</font>';?></td>
			  </tr>
			  <tr>
				<td colspan="5" align="center"><hr width="80%"></td>
			  </tr>
<?php		}
		}else{
			if($shipType==4){ ?>
			  <tr>
				<td colspan="5"><ul><li><font size="1"><?php print $yyUSS3?></li>
				<li><font size="1"><?php print str_replace("USPS","UPS",$yyUSS2)?> 
				<a href="http://www.ups.com">http://www.ups.com</a>.</font></li></ul></td>
			  </tr>
<?php		}else{ ?>
			  <tr>
				<td colspan="5"><ul><li><font size="1">You can use this page to set which <?php print $shipmet?> shipping methods qualify for free shipping discounts by checking the FSA (Free Shipping Available) checkbox.</li>
				<li><font size="1"><?php
			print str_replace("USPS",$shipmet,$yyUSS2);
			if($shipType==6){ ?>
				<a href="http://www.canadapost.ca">http://www.canadapost.ca</a>.
<?php		}else{ ?>
				<a href="http://www.fedex.com">http://www.fedex.com</a>.
<?php		} ?>
				</font></li>
				</ul></td>
			  </tr>
<?php		}
			while($allmethods=mysql_fetch_assoc($result)){ ?>
			  <tr>
			    <input type="hidden" name="methodshow<?php print $allmethods["uspsID"]?>" value="1" />
			    <td align="right"><strong><?php print $yyShipMe?>:</strong></td>
				<td align="left"> <?php print $allmethods["uspsShowAs"]?></td>
				<td align="center"><strong><?php print ($shipType==4 || $shipType==7?$yyUseMet:"&nbsp;")?></strong></td>
				<td align="center"><acronym title="<?php print $yyFSApp?>"><?php print $yyFSA?></acronym></td>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td colspan="2">&nbsp;</td>
				<td align="center"><input type="<?php print ($shipType==4 || $shipType==7?"checkbox":"hidden")?>" name="methoduse<?php print $allmethods["uspsID"]?>" value="ON" <?php if((int)$allmethods["uspsUseMethod"]==1) print "checked"?> /></td>
				<td align="center"><input type="checkbox" name="methodfsa<?php print $allmethods["uspsID"]?>" value="ON" <?php if((int)$allmethods["uspsFSA"]==1) print "checked"?> /></td>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td colspan="5" align="center"><hr width="80%"></td>
			  </tr>
<?php		}
		} ?>
			  <tr> 
                <td width="100%" colspan="5" align="center"><br /><input type="submit" value="<?php print $yySubmit?>" /><br />&nbsp;</td>
			  </tr>
            </table></td>
		  </form>
        </tr>
<?php } ?>
      </table>
