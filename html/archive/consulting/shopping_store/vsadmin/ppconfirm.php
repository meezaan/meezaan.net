<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protected under law as the intellectual property
//of Internet Business Solutions SL. Any use, reproduction, disclosure or copying
//of any kind without the express and written permission of Internet Business 
//Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
include "db_conn_open.php";
include "includes.php";
include "inc/incemail.php";
include "inc/languagefile.php";
include "inc/incfunctions.php";
$sSQL = "SELECT payProvDemo,payProvData1,payProvData2,payProvMethod FROM payprovider WHERE payProvID=1";
$result = mysql_query($sSQL) or print(mysql_error());
if($rs = mysql_fetch_assoc($result)){
	$demomode = ((int)$rs["payProvDemo"]==1);
	$ppmethod = (int)$rs["payProvMethod"];
}
mysql_free_result($result);
if($demomode) $sandbox = ".sandbox"; else $sandbox = "";
// read post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) {
  $value = urlencode(stripslashes($value));
  $req .= "&$key=$value";
}
// post back to PayPal system to validate
$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= 'Content-Length: ' . strlen($req) . "\r\n\r\n";
// assign posted variables to local variables
$Receiver_email = @$_POST['receiver_email'];
$Item_number = @$_POST['item_number'];
$Invoice = @$_POST['invoice'];
$Payment_status = @$_POST['payment_status'];
$Payment_gross = @$_POST['payment_gross'];
$Txn_id = @$_POST['txn_id'];
$Payer_email = @$_POST['payer_email'];
$ordID = trim(@$_POST['custom']);
$receipt_id = trim(@$_POST['receipt_id']);
$address_status = strtolower(trim(@$_POST['address_status']));
if($address_status=='confirmed')
	$avs = 'Y';
elseif($address_status=='unconfirmed')
	$avs = 'N';
else
	$avs = 'U';
$payer_status = strtolower(trim(@$_POST['payer_status']));
if($payer_status=='verified')
	$cvv = 'Y';
elseif($payer_status=='unverified')
	$cvv = 'N';
else
	$cvv = 'U';
$success = FALSE;
$res = '';
if(strpos($ordID,':')===FALSE){ // Otherwise PayPal Express Payment
	// Check notification validation
	if(@$usecurlforfsock){
		if(@$pathtocurl != ''){
			exec($pathtocurl . ' --data-binary \'' . str_replace("'","\'",$req) . '\' http://www' . $sandbox . '.paypal.com/cgi-bin/webscr', $res, $retvar);
			$res = trim(implode("",$res));
		}else{
			if (!$ch = curl_init()) {
				$success = false;
				$errormsg = "cURL package not installed in PHP";
			}else{
				curl_setopt($ch, CURLOPT_URL,'http://www' . $sandbox . '.paypal.com/cgi-bin/webscr'); 
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				if(@$curlproxy!=''){ 
					curl_setopt($ch, CURLOPT_PROXY, $curlproxy);
				}
				$res = curl_exec($ch);
				if(curl_error($ch) != "") print "Error with cURL installation: " . curl_error($ch) . "<br />";
				curl_close($ch);
			}
		}
		if(strcmp ($res, "VERIFIED") == 0 && ($ordID != "")){
			$success = TRUE;
		}elseif(strcmp ($res, "INVALID") == 0){
			; // log for manual investigation
		}else{
			if(@$debugmode==TRUE) print $res; // error
		}
	}else{
		$fp = fsockopen ('www' . $sandbox . '.paypal.com', 80, $errno, $errstr, 30);
		if (!$fp){
			echo "$errstr ($errno)"; // HTTP error handling
		}else{
			fputs ($fp, $header . $req);
			while (!feof($fp)) {
				$res = fgets ($fp, 1024);
				if(strcmp ($res, "VERIFIED") == 0 && ($ordID != "")){
					$success = TRUE;
				}elseif(strcmp ($res, "INVALID") == 0){
					; // log for manual investigation
				}else{
					if(@$debugmode==TRUE) print $res; // error
				}
			}
			fclose ($fp);
		}
	}
}
$alreadygotadmin = getadminsettings();
if($success){
	// check the payment_status is Completed
	// check that txn_id has not been previously processed
	// check that receiver_email is an email address in your PayPal account process payment
	if($Payment_status=="Completed"){
		do_stock_management($ordID);
		mysql_query("UPDATE cart SET cartCompleted=1 WHERE cartOrderID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
		mysql_query("UPDATE orders SET ordAVS='". $avs . "',ordCVV='" . $cvv . "',ordStatus=3,ordAuthNumber='" . $Txn_id . "',ordTransID='" . $receipt_id . "' WHERE ordID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
		do_order_success($ordID,$emailAddr,$sendEmail,FALSE,TRUE,TRUE,TRUE);
	}elseif($Payment_status=="Pending"){
		do_stock_management($ordID);
		mysql_query("UPDATE cart SET cartCompleted=2 WHERE cartCompleted=0 AND cartOrderID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
		mysql_query("UPDATE orders SET ordAVS='". $avs . "',ordCVV='" . $cvv . "',ordAuthNumber='Pending: " . mysql_escape_string(@$_POST['pending_reason']) . "' WHERE ordPayProvider=1 AND ordID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
	}
}
if(@$debugmode==TRUE){
	if(@$htmlemails==TRUE) $emlNl = "<br>"; else $emlNl="\n";
	$headers = "MIME-Version: 1.0\n";
	$headers .= "From: ".$emailAddr." <".$emailAddr.">\n";
	if(@$htmlemails==TRUE)
		$headers .= "Content-type: text/html; charset=".$emailencoding."\n";
	else
		$headers .= "Content-type: text/plain; charset=".$emailencoding."\n";
	$emailtxt = "Status: " . $Payment_status . $emlNl . "Txn ID: " . $Txn_id . $emlNl . "Response: " . $res . $emlNl . "Ord ID: " . $ordID . $emlNl . "Pending Reason: " . @$_POST["pending_reason"] . $emlNl;
	mail($emailAddr, "ppconfirm.php debug", $emailtxt, $headers);
}
?>