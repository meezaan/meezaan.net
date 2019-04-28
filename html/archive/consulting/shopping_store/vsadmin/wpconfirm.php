<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protected under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
include "db_conn_open.php";
include "includes.php";
include "inc/incemail.php";
include "inc/languagefile.php";
include "inc/incfunctions.php";
		if(@$wpconfirmnoheaders != TRUE){ ?>
<html>
<head>
<title>Thanks for shopping with us</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php print $adminencoding ?>">
<style type="text/css">
<!--
A:link {
	COLOR: #FFFFFF; TEXT-DECORATION: none
}
A:visited {
	COLOR: #FFFFFF; TEXT-DECORATION: none
}
A:active {
	COLOR: #FFFFFF; TEXT-DECORATION: none
}
A:hover {
	COLOR: #f39000; TEXT-DECORATION: underline
}
TD {
	FONT-FAMILY: Verdana; FONT-SIZE: 13px
}
P {
	FONT-FAMILY: Verdana; FONT-SIZE: 13px
}
-->
</style>
</head>
<?php
		} // wpconfirmnoheaders
$success=FALSE;
$worldpaycallbackerror = FALSE;
$errtext="";
$errormsg="";
$thereference="";
$orderText="";
$ordGrandTotal = 0;
$_SESSION["couponapply"]="";
$alreadygotadmin = getadminsettings();
$success = FALSE;
$isworldpay = FALSE;
$isauthnet = FALSE;
$isnetbanx = FALSE;
$issecpay = FALSE;
if(trim(@$_POST['transStatus']) != ''){ // WorldPay
	$isworldpay = TRUE;
	$transstatus = trim(@$_POST['transStatus']);
	$data2cbp = '';
	if(getpayprovdetails(5,$acctno,$data2,$data3,$demomode,$ppmethod)){
		$data2arr = split("&",$data2,2);
		$data2md5 = @$data2arr[0];
		$data2cbp = @$data2arr[1];
		if($data2cbp != ''){
			if($data2cbp != @$_POST['callbackPW']){
				$transstatus='';
				$errormsg = 'Callback password incorrect';
				$worldpaycallbackerror = TRUE;
			}
		}
		if($transstatus=='Y'){
			$ordID = trim(@$_POST['cartId']);
			$avscode = trim(@$_POST['AVS']);
			if(trim(@$_POST['wafMerchMessage']) != '') $avscode = trim(@$_POST['wafMerchMessage']) . "\r\n" . $avscode;
			do_stock_management($ordID);
			mysql_query("UPDATE cart SET cartCompleted=1 WHERE cartOrderID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
			mysql_query("UPDATE orders SET ordStatus=3,ordAVS='" . mysql_escape_string($avscode) . "',ordAuthNumber='" . mysql_escape_string(trim(@$_POST['transId'])) . "' WHERE ordPayProvider=5 AND ordID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
			do_order_success($ordID,$emailAddr,$sendEmail,FALSE,TRUE,TRUE,TRUE);
			$success = TRUE;
		}
	}
}elseif(trim(@$_POST['x_response_code']) != ''){ // Authorize.net
	if(getpayprovdetails(3,$data1,$data2,$data3,$demomode,$ppmethod)){
		$isauthnet = TRUE;
		$ordID = trim(@$_POST['x_ect_ordid']);
		if(trim(@$_POST['x_response_code'])=='1' && $ordID != ''){
			do_stock_management($ordID);
			mysql_query("UPDATE cart SET cartCompleted=1 WHERE cartOrderID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
			mysql_query("UPDATE orders SET ordStatus=3,ordAVS='" . mysql_escape_string(trim(@$_POST['x_avs_code'])) . "',ordCVV='" . mysql_escape_string(trim(@$_POST['x_cvv2_resp_code'])) . "',ordAuthNumber='" . mysql_escape_string(trim(@$_POST['x_auth_code'])) . "',ordTransID='" . mysql_escape_string(trim(@$_POST['x_trans_id'])) . "' WHERE ordPayProvider=3 AND ordID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
			do_order_success($ordID,$emailAddr,$sendEmail,FALSE,TRUE,TRUE,TRUE);
			$success = TRUE;
		}else
			$errormsg = trim(@$_POST['x_response_reason_text']);
	}
}elseif(trim(@$_POST["trans_id"]) != ""){ // Secpay
	if(getpayprovdetails(9,$data1,$data2,$data3,$demomode,$ppmethod)){
		$issecpay = TRUE;
		$data2arr = split("&",trim($data2),2);
		$data2md5=@$data2arr[0];
		$callbacksuccess = TRUE;
		if(trim(@$_POST["valid"])=="true" && trim(@$_POST["auth_code"])!=""){
			$ordID = trim(@$_POST["trans_id"]);
			if($data2md5 != ""){
				$thehash = md5("trans_id=" . $ordID . "&amount=" . trim(@$_POST["amount"]) . "&callback=" . $storeurl . "vsadmin/wpconfirm.php&" . $data2md5);
				if(@$_POST["hash"] != $thehash) $callbacksuccess=FALSE;
			}
			if(! $callbacksuccess){
				$errormsg = 'Callback password incorrect';
			}else{
				do_stock_management($ordID);
				mysql_query("UPDATE cart SET cartCompleted=1 WHERE cartOrderID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
				mysql_query("UPDATE orders SET ordStatus=3,ordAVS='" . mysql_escape_string(trim(@$_POST["cv2avs"])) . "',ordAuthNumber='" . mysql_escape_string(trim(@$_POST["auth_code"])) . "' WHERE ordPayProvider=9 AND ordID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
				do_order_success($ordID,$emailAddr,$sendEmail,FALSE,TRUE,TRUE,TRUE);
				$success = TRUE;
			}
		}else
			$errormsg = trim(@$_POST["message"]);
	}
}elseif(trim(@$_POST['netbanx_reference']) != ''){ // Netbanx
	if(getpayprovdetails(15,$data1,$data2,$data3,$demomode,$ppmethod)){
		$isnetbanx = TRUE;
		$thereference = trim(@$_POST['netbanx_reference']);
		if(trim(@$_SERVER['REMOTE_ADDR']) != '195.224.77.2' && trim(@$_SERVER['REMOTE_ADDR']) != '80.65.254.6')
			$errormsg = 'Error: This transaction does not appear to have been initiated by Netbanx';
		elseif($thereference!='0' && trim(@$_POST['order_id'])!=''){
			$ordID = trim(@$_POST['order_id']);
			do_stock_management($ordID);
			mysql_query("UPDATE cart SET cartCompleted=1 WHERE cartOrderID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
			$allchecks = 'X';
			if(trim(@$_POST['houseno_auth'])=='Matched')
				$allchecks = 'Y';
			elseif(trim(@$_POST['houseno_auth'])=='Not matched')
				$allchecks = 'N';
			if(trim(@$_POST['postcode_auth'])=='Matched')
				$allchecks .= 'Y';
			elseif(trim(@$_POST['postcode_auth'])=='Not matched')
				$allchecks .= 'N';
			else
				$allchecks .= 'X';
			$cvv = 'X';
			if(trim(@$_POST['CV2_auth'])=='Matched')
				$cvv = 'Y';
			elseif(trim(@$_POST['CV2_auth'])=='Not matched')
				$cvv = 'N';
			mysql_query("UPDATE orders SET ordStatus=3,ordAVS='" . $allchecks . "',ordCVV='" . $cvv . "',ordAuthNumber='" . $thereference . "' WHERE ordPayProvider=15 AND ordID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
			do_order_success($ordID,$emailAddr,$sendEmail,FALSE,TRUE,TRUE,TRUE);
			$success = TRUE;
		}else
			$errormsg = 'Transaction Declined';
	}
}
		if(@$wpconfirmnoheaders != TRUE){
?>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F39900">
  <tr>
    <td>
      <table width="100%" border="1" cellspacing="1" cellpadding="3">
        <tr> 
          <td rowspan="4" bgcolor="#333333">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td width="100%" bgcolor="#333333" align="center"><font color="#FFFFFF" face="Arial, Helvetica, sans-serif"><strong><?php print $xxInAssc . "&nbsp;";
		if($isworldpay)
			print "WorldPay";
		elseif($isauthnet)
			print "Authorize.Net";
		elseif($isnetbanx)
			print "Netbanx";
		elseif($issecpay)
			print "SECPay";
		else
			print '<a href="http://www.ecommercetemplates.com">EcommerceTemplates.com</a>' ?></strong></font></td>
          <td rowspan="4" bgcolor="#333333">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr> 
          <td width="100%" bgcolor="#637BAD" align="center"><font color="#FFFFFF"><strong><font face="Verdana, Arial, Helvetica, sans-serif" size="3"><?php print $xxTnkStr?></font></strong></font></td>
        </tr>
        <tr> 
          <td width="100%" align="center" bgcolor="#F5F5F5"> 
<?php	} // wpconfirmnoheaders
		if($isworldpay){ ?>
			<p>&nbsp;</p>
			<p align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><strong><?php print $xxTnkWit?> <WPDISPLAY ITEM=compName></strong></font></p>
<?php		if($worldpaycallbackerror){ ?>
			<table width="<?php print $innertablewidth?>" border="0" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
			  <tr> 
				<td width="100%" colspan="2" align="center"><?php print $xxThkErr?>
				<p>The error report returned by the server was:<br /><strong><?php print $errormsg?></strong></p>
				<a href="<?php print $storeurl?>"><font color="#637BAD"><strong><?php print $xxCntShp?></strong></font></a><br />
				<p>&nbsp;</p>
				</td>
			  </tr>
			</table>
<?php		} ?>
            <p><wpdisplay item="banner"></p>
<?php		if(! $worldpaycallbackerror){
				if(@$digidownloads==TRUE){
					print '<table width=95% cellpadding=3 cellspacing=0 border=0><tr><td><table width=100% cellspacing=0 cellpadding=3 border=0><tr><td>';
					$noshowdigiordertext = TRUE;
					include "inc/digidownload.php";
					print '</td></tr></table></td></tr></table>';
				} ?>
			<table width=95% cellpadding=3 cellspacing=0 border=0>
			<tr><td>
			<table width=100% cellspacing=0 cellpadding=3 border=0>
			<tr><td>
			<p align="left"><?php print str_replace(array("\r\n","\n"),array("<br />","<br />"),$orderText)?></p>
			</td></tr></table>
			</td></tr></table>
<?php		} ?>
			<p><font size="1"><strong><?php print $xxPlsNt1 . " " . $xxMerRef . " " . $xxPlsNt2?></strong></font></p>
			<p>&nbsp;</p>
<?php	}elseif($success){ ?>
		  <table border="0" cellspacing="<?php print $maintablespacing?>" cellpadding="<?php print $maintablepadding?>" width="<?php print $maintablewidth?>" bgcolor="<?php print $maintablebg?>" align="center">
			<tr>
			  <td width="100%" align="center">
				<table width="80%" border="0" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
				  <tr> 
					<td width="100%" align="center"><?php print $xxThkYou?>
					</td>
				  </tr>
<?php		if(@$digidownloads==TRUE){
				print '</table>';
				$noshowdigiordertext = TRUE;
				include "inc/digidownload.php";
				print '<table width="80%" border="0" cellspacing="' . $innertablespacing . '" cellpadding="' . $innertablepadding . '" bgcolor="' . $innertablebg . '">';
			} ?>
				  <tr> 
					<td width="100%"><?php print str_replace(array("\r\n","\n"),array("<br />","<br />"),$orderText)?>
					</td>
				  </tr>
				  <tr> 
					<td width="100%" align="center"><br /><br />
					<?php print $xxRecEml?><br /><br />
					<a href="<?php print $storeurl?>"><font color="#637BAD"><strong><?php print $xxCntShp?></strong></font></a><br />
					<p>&nbsp;</p>
					</td>
				  </tr>
				</table>
			  </td>
			</tr>
		  </table>
<?php	}else{ ?>
		  <p>&nbsp;</p>
		  <table border="0" cellspacing="<?php print $maintablespacing?>" cellpadding="<?php print $maintablepadding?>" width="<?php print $maintablewidth?>" bgcolor="<?php print $maintablebg?>" align="center">
			<tr>
			  <td width="100%">
				<table width="<?php print $innertablewidth?>" border="0" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
				  <tr> 
					<td width="100%" colspan="2" align="center"><?php print $xxThkErr?>
					<p>The error report returned by the server was:<br /><strong><?php print $errormsg?></strong></p>
					<a href="<?php print $storeurl?>"><font color="#637BAD"><strong><?php print $xxCntShp?></strong></font></a><br />
					<p>&nbsp;</p>
					</td>
				  </tr>
				</table>
			  </td>
			</tr>
		  </table>
<?php	}
		if(@$wpconfirmnoheaders != TRUE){ ?>
          </td>
        </tr>
        <tr> 
          <td width="100%" bgcolor="#333333" align="center"><font color="#FFFFFF"><strong><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href="<?php print $storeurl?>"><?php print $xxClkBck?></a></font></strong></font></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
<?php	} ?>