<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$storesessionvalue=="") $storesessionvalue="virtualstore".time();
if($_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE) exit;
$success=TRUE;
$warning1=FALSE;
$demomodeavailable=TRUE;
if(@$maxloginlevels=="") $maxloginlevels=5;
$alreadygotadmin = getadminsettings();
if(@$_POST["act"]=="domodify"){
	$isenabled=0;
	$demomode=0;
	if(@$_POST["isenabled"]=="1") $isenabled=1;
	if(@$_POST["demomode"]=="1") $demomode=1;
	$sSQL = "UPDATE payprovider SET payProvShow='" . trim(mysql_escape_string(@$_POST["showas"])) . "',payProvEnabled=" . $isenabled . ",payProvDemo=" . $demomode . ",payProvLevel=" . @$_POST["payProvLevel"] . ",";
	if(@$_POST["id"]=="5") // WorldPay
		$sSQL .= "payProvData1='" . mysql_escape_string(@$_POST["data1"]) . "',payProvData2='" . mysql_escape_string(@$_POST["data2"]) . "&" . mysql_escape_string(@$_POST["data3"]) . "'";
	elseif(@$_POST["id"]=="7") // VeriSign
		$sSQL .= "payProvData1='" . mysql_escape_string(@$_POST["data1"]) . "&" . mysql_escape_string(@$_POST["data2"]) . "&" . mysql_escape_string(@$_POST["data3"]) . "&" . mysql_escape_string(@$_POST["data4"]) . "'";
	elseif(@$_POST["id"]=="9") // SECPay
		$sSQL .= "payProvData1='" . mysql_escape_string(@$_POST["data1"]) . "',payProvData2='" . mysql_escape_string(@$_POST["data2"] . "&" . urlencode(@$_POST["data3"])) . "'";
	elseif(@$_POST["id"]=="10"){ // Capture Card
		$data1 = "";
		for($index=1;$index<=20;$index++){
			if(@$_POST["cardtype" . $index]=="X")
				$data1 .= "X";
			else
				$data1 .= "O";
		}
		$sSQL .= "payProvData1='" . $data1 . "'";
	}elseif(@$_POST['id']=='18' || @$_POST['id']=='19'){ // PayPal Payment Pro
		$sSQL .= "payProvData1='" . mysql_escape_string(@$_POST["data1"]) . "',payProvData2='" . mysql_escape_string(urlencode(trim(@$_POST["data2"]))) . "&" . mysql_escape_string(urlencode(trim(unstripslashes(@$_POST["data3"])))) . '&' . trim($_POST['apimethod']) . "'";
	}else{
		$thedata1 = trim(@$_POST["data1"]);
		$thedata2 = trim(@$_POST["data2"]);
		if(@$secretword != "" && (@$_POST["id"]=="3" || @$_POST["id"]=="13")){
			$thedata1 = upsencode($thedata1, $secretword);
			$thedata2 = upsencode($thedata2, $secretword);
		}
		$sSQL .= "payProvData1='" . mysql_escape_string($thedata1) . "',payProvData2='" . mysql_escape_string($thedata2) . "'";
	}
	for($index=2; $index <= $adminlanguages+1; $index++){
		if(($adminlangsettings & 128)==128) $sSQL .= ",payProvShow" . $index . "='" . trim(mysql_escape_string(@$_POST["showas" . $index])) . "'";
	}
	if(trim(@$_POST["transtype"]) != "") $sSQL .= ",payProvMethod=" . trim(@$_POST["transtype"]);
	$sSQL .= " WHERE payProvID=" . @$_POST["id"];
	mysql_query($sSQL) or print(mysql_error());
	if(@$_POST['id']=='18' || @$_POST['id']=='19'){
		$sSQL = "UPDATE payprovider SET payProvDemo=" . $demomode . ",payProvData1='" . mysql_escape_string(@$_POST["data1"]) . "',payProvData2='" . mysql_escape_string(urlencode(trim(@$_POST["data2"]))) . "&" . mysql_escape_string(urlencode(trim(unstripslashes(@$_POST["data3"])))) . '&' . trim($_POST['apimethod']) . "'";
		if(@$_POST['id']=='18'){
			if($isenabled==1) $sSQL .= ',payProvEnabled=1';
			$sSQL .= ' WHERE payProvID=19';
		}
		if(@$_POST['id']=='19') $sSQL .= ' WHERE payProvID=18';
		mysql_query($sSQL) or print(mysql_error());
	}
	print '<meta http-equiv="refresh" content="2; url=adminpayprov.php">';
}elseif(@$_POST['act']=='changepos'){
	$currentorder = (int)@$_POST["selectedq"];
	$neworder = (int)@$_POST["newval"];
	$sSQL = "SELECT payProvID FROM payprovider ORDER BY payProvEnabled DESC,payProvOrder";
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
		$sSQL="UPDATE payprovider SET payProvOrder=" . $theorder . " WHERE payProvID=" . $rs["payProvID"];
		mysql_query($sSQL) or print(mysql_error());
		$rowcounter++;
	}
	print '<meta http-equiv="refresh" content="2; url=adminpayprov.php">';
}
?>
<script language="javascript" type="text/javascript">
<!--
function modrec(id) {
	document.mainform.id.value = id;
	document.mainform.act.value = "modify";
	document.mainform.submit();
}
function validate_index(currindex)
{
	var i = eval("document.mainform.newpos"+currindex+".selectedIndex")+1;
	document.mainform.newval.value = i;
	document.mainform.selectedq.value = currindex;
	document.mainform.act.value = "changepos";
	if(i==document.mainform.selectedq.value){
		alert("No change in position");
		return (false);
	}
	document.mainform.submit();
}
// -->
</script>
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
<?php if(@$_POST["act"]=="domodify" && $success){ ?>
        <tr>
          <td width="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyUpdSuc?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
				<?php print $yyNoAuto?> <A href="adminpayprov.php"><strong><?php print $yyClkHer?></strong></a>.<br /><br />
				<img src="../images/clearpixel.gif" width="300" height="3" alt="" />
                </td>
			  </tr>
			</table></td>
        </tr>
<?php
}elseif(@$_POST["act"]=="domodify"){ ?>
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
}elseif(@$_POST["act"]=="modify"){
		$sSQL = "SELECT payProvID,payProvName,payProvShow,payProvDemo,payProvEnabled,payProvData1,payProvData2,payProvMethod,payProvShow2,payProvShow3,payProvLevel FROM payprovider WHERE payProvAvailable=1";
		if(@$_POST["id"] != "") $sSQL .= " AND payProvID=" . @$_POST["id"];
		$result = mysql_query($sSQL) or print(mysql_error());
		$alldata = mysql_fetch_array($result);
		$data2name='';
		$signuppage='';
		if($alldata['payProvID']==1){ // PayPal
			$signuppage='http://altfarm.mediaplex.com/ad/ck/3484-23890-3840-61';
			$data1name=$yyEmail;
			$data2name="Identity Token<br><font size='1'>(Only when using PDT)</font>";
			$demomodeavailable=TRUE;
		}elseif($alldata['payProvID']==2){ // 2Checkout
			$signuppage='http://www.2checkout.com/cgi-bin/aff.2c?affid=16632';
			$data1name=$yyAccNum;
			$data2name=$yyMD5H;
			$warning1=TRUE;
		}elseif($alldata['payProvID']==3 || $alldata['payProvID']==13){ // Authorize.net
			$signuppage='https://www.e-onlinedata.com/ecommercetemplates/';
			$data1name=$yyMercLID;
			$data2name=$yyTrnKey;
			if(@$secretword != ''){
				$alldata['payProvData1'] = upsdecode($alldata['payProvData1'], $secretword);
				$alldata['payProvData2'] = upsdecode($alldata['payProvData2'], $secretword);
			}
		}elseif($alldata['payProvID']==4 || $alldata['payProvID']==17){ // Email
			$data1name=$yyEAOrd;
			$demomodeavailable=FALSE;
		}elseif($alldata['payProvID']==5){ // World Pay
			$signuppage='https://secure.worldpay.com/app/application.pl?brand=templatestore';
			$data1name=$yyAccNum;
			$data2name=$yyMD5H;
			$warning1=TRUE;
		}elseif($alldata['payProvID']==6){ // NOCHEX
			$data1name=$yyEmail;
		}elseif($alldata['payProvID']==8){ // Payflow Link
			$data1name=$yyLogin;
			$data2name=$yyPartner;
		}elseif($alldata['payProvID']==9){ // SECPay
			$data1name=$yyMercID;
			$data2name=$yyMD5H;
			$warning1=TRUE;
		}elseif($alldata['payProvID']==10) // Capture Card
			$demomodeavailable=FALSE;
		elseif($alldata['payProvID']==11 || $alldata['payProvID']==12){ // PSiGate
			$data1name=$yyMercID;
		}elseif($alldata['payProvID']==14){ // Custom Payment Processor
			$data1name='Data 1';
			$data2name='Data 2';
		}elseif($alldata['payProvID']==15){ // Netbanx
			$data1name=$yyMercID;
			$demomodeavailable=FALSE;
		}elseif($alldata['payProvID']==16){ // Linkpoint
			$signuppage='http://www.shareasale.com/r.cfm?B=21469&U=112830&M=4776';
			$data1name=$yyNumSto;
			$data2name=$yyOwnSit;
		}elseif($alldata['payProvID']==18 || $alldata['payProvID']==19){ // PayPal Payment Pro
			$signuppage='http://altfarm.mediaplex.com/ad/ck/3484-23890-3840-61';
			$data1name='API Account Name';
			$data2name='API Password.<br>(NOT PayPal account password)';
		}elseif($alldata['payProvID']==20){ // Google Checkout
			$signuppage='http://checkout.google.com/sell?promo=sectem';
			$data1name='Merchant ID';
			$data2name='Merchant Key';
		}else
			$data1name='Data 1';
?>
        <tr>
		  <form name="mainform" method="post" action="adminpayprov.php">
          <td width="100%">
			<input type="hidden" name="posted" value="1" />
			<input type="hidden" name="act" value="domodify" />
			<input type="hidden" name="id" value="<?php print $alldata['payProvID']?>" />
            <table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><strong><?php print $yyPPAdm?></strong><br />&nbsp;</td>
			  </tr>
<?php	if($signuppage!=''){ ?>
			  <tr> 
                <td width="100%" colspan="2" align="center"><?php print $yySignUp?> <a href="<?php print $signuppage?>" target="_blank"><strong><?php print $yyClkHer?></strong></a><br />&nbsp;</td>
			  </tr>
<?php	} ?>
			  <tr>
				<td width="50%" align="right" valign="top"><strong><?php print $yyPPName?> : </strong></td>
				<td width="50%" align="left" valign="top"><strong><?php print $alldata['payProvName']?></strong></td>
			  </tr>
			  <tr>
				<td width="50%" align="right" valign="top"><strong><?php print $yyShwAs?> : </strong></td>
				<td width="50%" align="left" valign="top"><input type="text" name="showas" value="<?php print $alldata['payProvShow']?>" size="25" /></td>
			  </tr>
<?php	for($index=2; $index <= $adminlanguages+1; $index++){
			if(($adminlangsettings & 128)==128){ ?>
			  <tr>
				<td width="50%" align="right" valign="top"><strong><?php print $yyShwAs . " " . $index?> : </strong></td>
				<td width="50%" align="left" valign="top"><input type="text" name="showas<?php print $index?>" value="<?php print $alldata[6 + $index]?>" size="25" /></td>
			  </tr>
<?php		}
		} ?>
			  <tr>
				<td width="50%" align="right" valign="top"><strong><?php print $yyEnable?> : </strong></td>
				<td width="50%" align="left" valign="top"><input type="checkbox" name="isenabled" value="1" <?php if($alldata['payProvEnabled']==1) print "checked"?> /></td>
			  </tr>
<?php if($demomodeavailable){ ?>
			  <tr>
				<td width="50%" align="right" valign="top"><strong><?php print $yyDemoMo?> : </strong></td>
				<td width="50%" align="left" valign="top"><input type="checkbox" name="demomode" value="1" <?php if($alldata['payProvDemo']==1) print "checked"?> /></td>
			  </tr>
<?php }
	  if($alldata['payProvID']==7){ // VeriSign PayFlo Pro
		$vsdetails = split("&",$alldata['payProvData1']);
		$vs1=@$vsdetails[0];
		$vs2=@$vsdetails[1];
		$vs3=@$vsdetails[2];
		$vs4=@$vsdetails[3];
?>
			  <tr>
				<td width="50%" align="right" valign="top"><strong><?php print $yyUserID?> : </strong></td>
				<td width="50%" align="left" valign="top"><input type="text" name="data1" value="<?php print $vs1?>" size="25" /></td>
			  </tr>
			  <tr>
				<td width="50%" align="right" valign="top"><strong><?php print $yyVendor?> : </strong></td>
				<td width="50%" align="left" valign="top"><input type="text" name="data2" value="<?php print $vs2?>" size="25" /></td>
			  </tr>
			  <tr>
				<td width="50%" align="right" valign="top"><strong><?php print $yyPartner?> : </strong></td>
				<td width="50%" align="left" valign="top"><input type="text" name="data3" value="<?php print $vs3?>" size="25" /></td>
			  </tr>
			  <tr>
				<td width="50%" align="right" valign="top"><strong><?php print $yyPass?> : </strong></td>
				<td width="50%" align="left" valign="top"><input type="text" name="data4" value="<?php print $vs4?>" size="25" /></td>
			  </tr>
<?php }elseif($alldata['payProvID']==10){ ?>
			  <tr>
				<td align="center" valign="top" colspan="2"><hr width="50%"><strong><?php print $yyAccCar?></strong><br />&nbsp;</td>
			  </tr>
			  <tr>
				<td align="right" valign="top"><strong>Visa : </strong></td>
				<td align="left" valign="top"><input type="checkbox" name="cardtype1" value="X" <?php if(substr($alldata['payProvData1'],0,1)=="X") print "checked" ?> /></td>
			  </tr>
			  <tr>
				<td align="right" valign="top"><strong>Mastercard : </strong></td>
				<td align="left" valign="top"><input type="checkbox" name="cardtype2" value="X" <?php if(substr($alldata['payProvData1'],1,1)=="X") print "checked" ?> /></td>
			  </tr>
			  <tr>
				<td align="right" valign="top"><strong>American Express : </strong></td>
				<td align="left" valign="top"><input type="checkbox" name="cardtype3" value="X" <?php if(substr($alldata['payProvData1'],2,1)=="X") print "checked" ?> /></td>
			  </tr>
			  <tr>
				<td align="right" valign="top"><strong>Diners Club : </strong></td>
				<td align="left" valign="top"><input type="checkbox" name="cardtype4" value="X" <?php if(substr($alldata['payProvData1'],3,1)=="X") print "checked" ?> /></td>
			  </tr>
			  <tr>
				<td align="right" valign="top"><strong>Discover : </strong></td>
				<td align="left" valign="top"><input type="checkbox" name="cardtype5" value="X" <?php if(substr($alldata['payProvData1'],4,1)=="X") print "checked" ?> /></td>
			  </tr>
			  <tr>
				<td align="right" valign="top"><strong>En Route : </strong></td>
				<td align="left" valign="top"><input type="checkbox" name="cardtype6" value="X" <?php if(substr($alldata['payProvData1'],5,1)=="X") print "checked" ?> /></td>
			  </tr>
			  <tr>
				<td align="right" valign="top"><strong>JCB : </strong></td>
				<td align="left" valign="top"><input type="checkbox" name="cardtype7" value="X" <?php if(substr($alldata['payProvData1'],6,1)=="X") print "checked" ?> /></td>
			  </tr>
			  <tr>
				<td align="right" valign="top"><strong>Switch/Solo : </strong></td>
				<td align="left" valign="top"><input type="checkbox" name="cardtype8" value="X" <?php if(substr($alldata['payProvData1'],7,1)=="X") print "checked" ?> /></td>
			  </tr>
			  <tr>
				<td align="right" valign="top"><strong>Bankcard (AUS / NZ) : </strong></td>
				<td align="left" valign="top"><input type="checkbox" name="cardtype9" value="X" <?php if(substr($alldata['payProvData1'],8,1)=="X") print "checked" ?> /></td>
			  </tr>
		<?php if(false){ ?>
			  <tr>
				<td align="center" valign="top" colspan="2"><hr width="50%"><strong><?php print $yyNewCer?></strong><br />&nbsp;</td>
			  </tr>
			  <tr>
				<td colspan="2" align="center" valign="top"><textarea name="data2" rows="10" cols="82"></textarea></td>
			  </tr>
		<?php } ?>
<?php }else{ ?>
			  <tr>
				<td width="50%" align="right" valign="top"><strong><?php print $data1name?> : </strong></td>
				<td width="50%" align="left" valign="top"><input type="text" name="data1" value="<?php print $alldata['payProvData1']?>" size="25" /></td>
			  </tr>
<?php } ?>
<?php if($alldata['payProvID']==5){
		$data2arr = split("&",trim($alldata['payProvData2']));
		$data2md5=@$data2arr[0];
		$data2cbp=@$data2arr[1];
		
?>
			  <tr>
				<td width="50%" align="right"><strong>MD5 Secret (Optional) : </strong></td>
				<td width="50%" align="left"><input type="text" name="data2" value="<?php print $data2md5?>" size="25" /></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong>Callback password (Optional) : </strong></td>
				<td width="50%" align="left"><input type="text" name="data3" value="<?php print $data2cbp?>" size="25" /></td>
			  </tr>
<?php }elseif($alldata['payProvID']==9){
		$data2arr = split("&",trim($alldata['payProvData2']));
		$data2md5=@$data2arr[0];
		$data2template=urldecode(@$data2arr[1]);
		
?>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyMD5H?> : </strong></td>
				<td width="50%" align="left"><input type="text" name="data2" value="<?php print $data2md5?>" size="25" /></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong>Payment Template (Optional) : </strong></td>
				<td width="50%" align="left"><input type="text" name="data3" value="<?php print $data2template?>" size="25" /></td>
			  </tr>
<?php }elseif($alldata['payProvID']==16){ ?>
			  <tr>
				<td width="50%" align="right"><strong><?php print $data2name?> : </strong></td>
				<td width="50%" align="left"><select name="data2" size="1"><option value="0"><?php print $yyLPSit?></option><option value="1" <?php if($alldata['payProvData2']=="1") print "selected"?>><?php print $yyYesOS?></option></select></td>
			  </tr>
<?php }elseif($alldata['payProvID']==18 || $alldata['payProvID']==19){
		$data2arr = split("&",trim($alldata['payProvData2']));
		$data2pw=urldecode(@$data2arr[0]);
		$data2path=urldecode(@$data2arr[1]);
		$isthreetoken=(trim(@$data2arr[2])=='1');
		$yyPAtoCE="Path to certificate";
		$yySigHas="Signature Hash";
?>
			  <tr>
				<td width="50%" align="right"><strong><?php print $data2name?> : </strong></td>
				<td width="50%" align="left"><input type="text" name="data2" value="<?php print $data2pw?>" size="25" /></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><span id="apimethspan"><?php if($isthreetoken) print $yySigHas; else print $yyPAtoCE; ?></span> : </strong></td>
				<td width="50%" align="left"><input type="text" name="data3" value="<?php print $data2path?>" size="35" /></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong>API Method : </strong></td>
				<td width="50%" align="left"><select name="apimethod" size="1" onchange="document.getElementById('apimethspan').innerHTML=(document.getElementById('apimethod').selectedIndex==1 ? '<?php print str_replace("'","\'",$yySigHas)?>' : '<?php print str_replace("'","\'",$yyPAtoCE)?>')"><option value="">API Certificate</option><option value="1" <?php if($isthreetoken) print 'selected';?>>API Signature</option></select></td>
			  </tr>
<?php }elseif($data2name != ""){ ?>
			  <tr>
				<td width="50%" align="right" valign="top"><strong><?php print $data2name?> : </strong></td>
				<td width="50%" align="left" valign="top"><input type="text" name="data2" value="<?php print $alldata['payProvData2']?>" size="25" /></td>
			  </tr>
<?php }
	  if($alldata['payProvID']==3 || $alldata['payProvID']==5 || $alldata['payProvID']==7 || $alldata['payProvID']==9 || $alldata['payProvID']==11 || $alldata['payProvID']==12 || $alldata['payProvID']==13 || $alldata['payProvID']==14 || $alldata['payProvID']==16 || $alldata['payProvID']==18){ // Pay Providers we can set authorization type ?>
			  <tr>
				<td width="50%" align="right" valign="top"><strong><?php print $yyTrnTyp?> : </strong></td>
				<td width="50%" align="left" valign="top"><select name="transtype" size="1"><option value="0"><?php print $yyAuthCp?></option><option value="1" <?php if($alldata['payProvMethod']=="1") print "selected" ?>><?php print $yyAuthOn?></option></select></td>
			  </tr>
<?php } ?>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyLiLev ?> : </strong></td>
				<td width="50%" align="left"><select name="payProvLevel" size="1">
				<option value="0"><?php print $yyNoRes?></option>
<?php				for($index=1; $index<= $maxloginlevels; $index++){
						print '<option value="' . $index . '"';
						if($alldata['payProvLevel']==$index) print ' selected';
						print '>' . $yyLiLev . ' ' . $index . '</option>';
					} ?></select></td>
			  </tr>
			  <tr>
				<td colspan="2">&nbsp;</td>
			  </tr>
			  <tr>
				<td width="50%" align="right" valign="top"><input type="submit" value="<?php print $yySubmit?>" /></td>
				<td width="50%" align="left" valign="top"><input type="reset" value="<?php print $yyReset?>" /></td>
			  </tr>
<?php if($warning1==TRUE){ ?>
			  <tr>
				<td colspan="2">&nbsp;<br /><font size="1">Setting MD5 hash and callback password security features is optional. But if set, they will be checked so you must make sure they match with your payment processor.</font></td>
			  </tr>
<?php } ?>
			  <tr>
				<td colspan="2">&nbsp;</td>
			  </tr>
			</table>
		  </td>
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
			<p><?php print $yyNoFor?> <a href="adminpayprov.php"><?php print $yyClkHer?></a>.</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
		  </td>
		</tr>
<?php
}else{ ?>
        <tr>
		  <form name="mainform" method="post" action="adminpayprov.php">
          <td width="100%" align="center">
			<input type="hidden" name="posted" value="1" />
			<input type="hidden" name="act" value="modify" />
			<input type="hidden" name="id" value="1" />
			<input type="hidden" name="selectedq" value="1" />
			<input type="hidden" name="newval" value="1" />
            <table width="80%" border="0" cellspacing="0" cellpadding="2" bgcolor="">
			  <tr> 
                <td width="100%" colspan="4" align="center"><strong><?php print $yyPPAdm?></strong><br />&nbsp;</td>
			  </tr>
			  <tr>
				<td width="8%" align="center" valign="top"><strong>ID</strong></td>
				<td width="8%" align="center" valign="top"><strong><?php print $yyOrder?></strong></td>
				<td width="42%" align="center" valign="top"><strong><?php print $yyPPName?></strong></td>
				<td width="42%" align="center" valign="top"><strong><?php print $yyConf?></strong></td>
			  </tr>
<?php
		function writeposition($currpos,$maxpos){
			$reqtext="<select name='newpos" . $currpos . "' size='1' onChange='javascript:validate_index(".$currpos.");'>";
			for($i = 1; $i <= $maxpos; $i++){
				$reqtext .= "<option value='".$i."'";
				if($currpos==$i) $reqtext .= " selected";
				$reqtext .= ">" . $i . "</option>";
			}
			return($reqtext . "</select>");
		};
		$sSQL = "SELECT COUNT(payProvID) AS enabledProv FROM payprovider WHERE payProvEnabled=1";
		$result = mysql_query($sSQL) or print(mysql_error());
		$rs = mysql_fetch_assoc($result);
		$enabledProv = $rs["enabledProv"];
		mysql_free_result($result);
		$showenabled=TRUE;
		for($index=0; $index<2; $index++){
			$sSQL = "SELECT payProvID,payProvName,payProvShow,payProvDemo,payProvEnabled,payProvData1,payProvData2 FROM payprovider WHERE payProvAvailable=1";
			if($showenabled)
				$sSQL .= " AND payProvEnabled=1 ORDER BY payProvOrder";
			else
				$sSQL .= " AND payProvEnabled=0 ORDER BY payProvName";
			$result = mysql_query($sSQL) or print(mysql_error());
			$rowcounter=1;
			while($alldata = mysql_fetch_row($result)){ ?>
			  <tr>
				<td align="center"><?php print $alldata[0] ?></td>
				<td align="center"><?php if($alldata[4]==1) print writeposition($rowcounter,$enabledProv); else print "-"; ?></td>
				<td align="center"><?php if($alldata[3]==1) print "<font color='#FF0000'>"; ?><?php if($alldata[4]==1) print "<strong>"; ?><?php print $alldata[1];?><?php if($alldata[4]==1) print "</strong>"; ?><?php if($alldata[3]==1) print "</font>"; ?></td>
				<td align="center"><input type=button name="modify" value="<?php print $yyModify?>" onclick="modrec('<?php print $alldata[0];?>')" /></td>
			  </tr>
<?php			$rowcounter++;
			}
			$showenabled=FALSE;
		} ?>
			  <tr> 
                <td width="100%" colspan="4" align="center"><br /><?php print $yyPPEx1?><br />
				  <?php print $yyPPEx2?>&nbsp;</td>
			  </tr>
			  <tr> 
                <td width="100%" colspan="4" align="center"><br /><a href="admin.php"><strong><?php print $yyAdmHom?></strong></a><br />&nbsp;</td>
			  </tr>
            </table></td>
		  </form>
        </tr>

<?php
}
?>
      </table>