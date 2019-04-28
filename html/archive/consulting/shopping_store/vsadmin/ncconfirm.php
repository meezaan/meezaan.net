<?php include "db_conn_open.php" ?>
<?php include "includes.php" ?>
<?php include "inc/incemail.php" ?>
<?php include "inc/languagefile.php" ?>
<?php include "inc/incfunctions.php" ?>
<?php
$req="";
$success = true;
foreach ($_POST as $key => $value) {
  $value = urlencode(stripslashes($value));
  if($req != "") $req .= '&';
  $req .= "$key=$value";
}
// assign posted variables to local variables
$Receiver_email = str_replace("'","",@$_POST['to_email']);
$Payment_gross = str_replace("'","",@$_POST['amount']);
$Payer_email = str_replace("'","",@$_POST['from_email']);
$Txn_id = str_replace("'","",@$_POST['transaction_id']);
$ordID = str_replace("'","",@$_POST['order_id']);

if(@$pathtocurl != ""){
	exec($pathtocurl . ' --data-binary \'' . str_replace("'","\'",$req) . '\' https://www.nochex.com/nochex.dll/apc/apc', $res, $retvar);
	$res = implode("\n",$res);
}else{
	if (!$ch = curl_init()) {
		$success = false;
		$errormsg = "cURL package not installed in PHP";
	}else{
		curl_setopt($ch, CURLOPT_URL,'https://www.nochex.com/nochex.dll/apc/apc'); 
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
$alreadygotadmin = getadminsettings();
if($success){
	// print str_replace("<","<br />&lt;",$res) . "<br />\n";
	if(strcmp ($res, "AUTHORISED") == 0){
		// check the payment_status is Completed
		// check that txn_id has not been previously processed
		// check that receiver_email is an email address in your PayPal account process payment
		do_stock_management($ordID);
		$sSQL="UPDATE cart SET cartCompleted=1 WHERE cartOrderID='" . mysql_escape_string($ordID) . "'";
		mysql_query($sSQL) or print(mysql_error());
		$sSQL="UPDATE orders SET ordStatus=3,ordAuthNumber='" . $Txn_id . "' WHERE ordID='" . mysql_escape_string($ordID) . "'";
		mysql_query($sSQL) or print(mysql_error());
		do_order_success($ordID,$emailAddr,$sendEmail,FALSE,TRUE,TRUE,TRUE);
	}elseif(strcmp ($res, "DECLINED") == 0){
		; // log for manual investigation
	}else{
		if(@$debugmode==TRUE) print $res; // error
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
	$emailtxt = "Txn ID: " . $Txn_id . $emlNl . "Response: " . $res . $emlNl . "Ord ID: " . $ordID . $emlNl . $emlNl;
	mail($emailAddr, "ppconfirm.php debug", $emailtxt, $headers);
}
?>
