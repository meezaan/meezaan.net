<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
include "./vsadmin/inc/incemail.php";
if(@$_SERVER['CONTENT_LENGTH'] != '' && $_SERVER['CONTENT_LENGTH'] > 10000) exit;
$success=FALSE;
$errtext="";
$errormsg="";
$thereference="";
$orderText="";
$ordGrandTotal = 0;
$ordTotal=0;
$_SESSION["couponapply"]="";
$ordAuthNumber='';
function order_failed(){
	global $maintablebg,$innertablebg,$maintablewidth,$innertablewidth,$maintablespacing,$innertablespacing,$maintablepadding,$innertablepadding;
	global $xxThkErr,$storeurl,$xxCntShp,$errtext;
?>
      <table border="0" cellspacing="<?php print $maintablespacing?>" cellpadding="<?php print $maintablepadding?>" width="<?php print $maintablewidth?>" bgcolor="<?php print $maintablebg?>" align="center">
        <tr>
          <td width="100%">
            <table width="<?php print $innertablewidth?>" border="0" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
			  <tr> 
                <td width="100%" colspan="2" align="center"><?php print $xxThkErr?>
				<?php if($errtext != "") print "<p><strong>" . $errtext . "</strong></p>" ?>
				<a href="<?php print $storeurl?>"><strong><?php print $xxCntShp?></strong></a><br />
				<img src="images/clearpixel.gif" width="300" height="3" alt="" />
                </td>
			  </tr>
			</table>
		  </td>
        </tr>
      </table>
<?php
}
$alreadygotadmin = getadminsettings();
if(@$_GET['sig'] != '' && @$_GET['tx'] != '' && @$_GET['st'] != ''){
	$ordID='';
	$success = getpayprovdetails(1,$data1,$data2,$data3,$demomode,$ppmethod);
	if($data2 == ''){
		$errtext = "Identity token for PayPal Payment Data Transfer (PDT) not set.";
		order_failed();
	}else{
		$req = 'cmd=_notify-synch';
		$req .= '&tx=' . $_GET['tx'] . '&at=' . $data2;
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		if(@$usecurlforfsock){
			callcurlfunction('http://www' . ($demomode ? '.sandbox' : '') . '.paypal.com', $req, $res, '', $errtext, FALSE);
		}else{
			// $fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
			if($fp = fsockopen ('www' . ($demomode ? '.sandbox' : '') . '.paypal.com', 80, $errno, $errstr, 30)){
				fputs ($fp, $header . $req); // read the body data 
				$res = '';
				$headerdone = false;
				while(!feof($fp)){
					$line = fgets ($fp, 1024);
					if(strcmp($line, "\r\n") == 0)
						$headerdone = true;
					else if ($headerdone) // header has been read. now read the contents
						$res .= $line;
				}
			}
			fclose ($fp);
		}
		$lines = explode("\n", $res);
		$keyarray = array();
		if(strcmp ($lines[0], "SUCCESS") == 0){
			$payment_status='';
			$pending_reason='';
			$txn_id='';
			for ($i=1; $i<(count($lines)-1);$i++){
				list($key,$val) = explode("=", $lines[$i]);
				if($key=='payment_status')
					$payment_status = $val;
				if($key=='pending_reason')
					$pending_reason = $val;
				if($key=='custom')
					$ordID = $val;
				if($key=='txn_id')
					$txn_id = $val;
			}
			$sSQL = "SELECT ordAuthNumber FROM orders WHERE ordPayProvider=1 AND ordStatus>=3 AND ordAuthNumber='" . mysql_escape_string($txn_id) . "' AND ordID='" . mysql_escape_string($ordID) . "'";
			$result = mysql_query($sSQL) or print(mysql_error());
			$success = FALSE;
			if($rs = mysql_fetch_assoc($result))
				$success = (trim($rs["ordAuthNumber"])!="");
			mysql_free_result($result);
			if($success)
				do_order_success($ordID,$emailAddr,FALSE,TRUE,FALSE,FALSE,FALSE);
			else{
				mysql_query("UPDATE cart SET cartCompleted=2 WHERE cartCompleted=0 AND cartOrderID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
				mysql_query("UPDATE orders SET ordAuthNumber='no ipn' WHERE ordAuthNumber='' AND ordPayProvider=1 AND ordID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
				$xxThkErr = '';
				if($payment_status=="Pending")
					$errtext = $xxPPPend;
				else
					$errtext = $xxNoCnf;
				order_failed();
			}
		}else if (strcmp ($lines[0], "FAIL") == 0){
			$errtext = $res;
			order_failed();
		}
	}
}elseif(@$_POST["custom"] != ""){ // PayPal
	$ordID = trim(@$_POST["custom"]);
	$txn_id = trim(@$_POST["txn_id"]);
	$sSQL = "SELECT ordAuthNumber FROM orders WHERE ordPayProvider=1 AND ordStatus>=3 AND ordAuthNumber='" . mysql_escape_string($txn_id) . "' AND ordID='" . mysql_escape_string($ordID) . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	$success = FALSE;
	if($rs = mysql_fetch_assoc($result))
		$success = (trim($rs["ordAuthNumber"])!="");
	mysql_free_result($result);
	if($success)
		do_order_success($ordID,$emailAddr,FALSE,TRUE,FALSE,FALSE,FALSE);
	else{
		mysql_query("UPDATE cart SET cartCompleted=2 WHERE cartCompleted=0 AND cartOrderID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
		mysql_query("UPDATE orders SET ordAuthNumber='no ipn' WHERE ordAuthNumber='' AND ordPayProvider=1 AND ordID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
		$xxThkErr = '';
		if(@$_POST["payment_status"]=="Pending")
			$errtext = $xxPPPend;
		else
			$errtext = $xxNoCnf;
		order_failed();
	}
}elseif(@$_POST["method"] == "paypalexpress" && @$_POST["token"] != ""){ // PayPal Express
	if($success = getpayprovdetails(19,$username,$password,$data3,$demomode,$ppmethod)){
		$data2arr = split("&",$password);
		$password=urldecode(@$data2arr[0]);
		$isthreetoken=(trim(urldecode(@$data2arr[2]))=='1');
		$signature=''; $sslcertpath='';
		if($isthreetoken) $signature=urldecode(@$data2arr[1]); else $sslcertpath=urldecode(@$data2arr[1]);
	}
	$ordID = trim(@$_POST["ordernumber"]);
	$token = trim(@$_POST["token"]);
	$payerid = trim(@$_POST["payerid"]);
	$ordAuthNumber = '';
	$txn_id = $status = $pendingreason = '';
	if($demomode) $sandbox = ".sandbox"; else $sandbox = "";
	$sSQL = "SELECT ordShipping,ordStateTax,ordCountryTax,ordHandling,ordTotal,ordDiscount,ordAuthNumber,ordEmail FROM orders WHERE ordID='" . mysql_escape_string($ordID) . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	if($rs = mysql_fetch_assoc($result)){
		if($rs["ordEmail"]==trim(@$_POST["email"])) $ordAuthNumber = $rs["ordAuthNumber"];
	}else
		$success = FALSE;
	if($success){
		if($ordAuthNumber==''){
			$amount = number_format(($rs["ordShipping"]+$rs["ordStateTax"]+$rs["ordCountryTax"]+$rs["ordTotal"]+$rs["ordHandling"])-$rs["ordDiscount"],2,'.','');
			$sXML = ppsoapheader($username, $password, $signature) .
				'<soap:Body>' .
				'  <DoExpressCheckoutPaymentReq xmlns="urn:ebay:api:PayPalAPI">' .
				'    <DoExpressCheckoutPaymentRequest>' .
				'      <Version xmlns="urn:ebay:apis:eBLBaseComponents">1.00</Version>' .
				'      <DoExpressCheckoutPaymentRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">' .
				'        <PaymentAction>' . ($ppmethod==1?'Authorization':'Sale') . '</PaymentAction>' .
				'        <Token>' . $token . '</Token><PayerID>' . $payerid . '</PayerID>' .
				'        <PaymentDetails>' .
				'          <OrderTotal currencyID="' . $countryCurrency . '">' . $amount . '</OrderTotal>' .
				'          <ButtonSource>ecommercetemplates_Cart_EC_US</ButtonSource>' .
				'        </PaymentDetails>' .
				'      </DoExpressCheckoutPaymentRequestDetails>' .
				'    </DoExpressCheckoutPaymentRequest>' .
				'  </DoExpressCheckoutPaymentReq>' .
				'</soap:Body></soap:Envelope>';
			if(callcurlfunction('https://api-aa' . ($sandbox=='' && $isthreetoken ? '-3t' : '') . $sandbox . '.paypal.com/2.0/', $sXML, $res, $sslcertpath, $errtext, FALSE)){
				$xmlDoc = new vrXMLDoc($res);
				$nodeList = $xmlDoc->nodeList->childNodes[0];
				for($i = 0; $i < $nodeList->length; $i++){
					if($nodeList->nodeName[$i]=="SOAP-ENV:Body"){
						$e = $nodeList->childNodes[$i];
						for($j = 0; $j < $e->length; $j++){
							if($e->nodeName[$j] == "DoExpressCheckoutPaymentResponse"){
								$ee = $e->childNodes[$j];
								for($jj = 0; $jj < $ee->length; $jj++){
									if($ee->nodeName[$jj] == "Token"){
										$token=$ee->nodeValue[$jj];
									}elseif($ee->nodeName[$jj] == "DoExpressCheckoutPaymentResponseDetails"){
										$ff = $ee->childNodes[$jj];
										for($kk = 0; $kk < $ff->length; $kk++){
											if($ff->nodeName[$kk] == "PaymentInfo"){
												$gg = $ff->childNodes[$kk];
												for($ll = 0; $ll < $gg->length; $ll++){
													if($gg->nodeName[$ll] == "PaymentStatus"){
														$status=$gg->nodeValue[$ll];
													}elseif($gg->nodeName[$ll] == "PendingReason"){
														$pendingreason=$gg->nodeValue[$ll];
													}elseif($gg->nodeName[$ll] == "TransactionID"){
														$txn_id=$gg->nodeValue[$ll];
													}
												}
											}
										}
									}elseif($ee->nodeName[$jj] == "Errors"){
										$ff = $ee->childNodes[$jj];
										for($kk = 0; $kk < $ff->length; $kk++){
											if($ff->nodeName[$kk] == "ShortMessage"){
												$errtext=$ff->nodeValue[$kk].'<br>'.$errtext;
											}elseif($ff->nodeName[$kk] == "LongMessage"){
												$errtext.=$ff->nodeValue[$kk];
											}elseif($ff->nodeName[$kk] == "ErrorCode"){
												$errcode=$ff->nodeValue[$kk];
											}
										}
									}
								}
							}
						}
					}
				}
			}else
				$success = FALSE;
		}else{
			$status = "Refresh";
		}
		if($status=="Completed" || $status=="Pending"){
			if($status=="Pending" && $pendingreason != '') $txn_id = $status . ": " . $pendingreason . '<br>' . $txn_id;
			do_stock_management($ordID);
			mysql_query("UPDATE cart SET cartCompleted=1 WHERE cartOrderID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
			mysql_query("UPDATE orders SET ordStatus=3,ordAuthNumber='" . $txn_id . "' WHERE ordPayProvider=19 AND ordID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
			do_order_success($ordID,$emailAddr,$sendEmail,TRUE,TRUE,TRUE,TRUE);
		}elseif($status=="Refresh"){
			do_order_success($ordID,$emailAddr,$sendEmail,FALSE,FALSE,FALSE,FALSE);
		}else{
			order_failed();
		}
	}else{
		order_failed();
	}
}elseif(@$_GET["ncretval"] != "" && @$_GET["ncsessid"] != ""){ // NOCHEX
	$ordID = trim(@$_GET["ncretval"]);
	$ncsessid = trim(@$_GET["ncsessid"]);
	$sSQL = "SELECT ordAuthNumber FROM orders WHERE ordPayProvider=6 AND ordStatus>=3 AND ordSessionID='" . mysql_escape_string($ncsessid) . "' AND ordID='" . mysql_escape_string($ordID) . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	$success = FALSE;
	if($rs = mysql_fetch_assoc($result))
		$success = (trim($rs["ordAuthNumber"])!="");
	mysql_free_result($result);
	if($success)
		do_order_success($ordID,$emailAddr,FALSE,TRUE,FALSE,FALSE,FALSE);
	else{
		mysql_query("UPDATE cart SET cartCompleted=2 WHERE cartCompleted=0 AND cartOrderID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
		mysql_query("UPDATE orders SET ordAuthNumber='no apc' WHERE ordAuthNumber='' AND ordPayProvider=6 AND ordID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
		$errtext = $xxNoCnf;
		$xxThkErr = '';
		order_failed();
	}
}elseif(@$_POST['xxpreauth'] != ''){
	$ordID = trim(@$_POST['xxpreauth']);
	$thesessionid = trim(str_replace("'",'',@$_POST['thesessionid']));
	$themethod = trim(str_replace("'",'',@$_POST['xxpreauthmethod']));
	if($success = getpayprovdetails($themethod,$data1,$data2,$data3,$demomode,$ppmethod)){
		$sSQL = "SELECT ordAuthNumber FROM orders WHERE ordSessionID='" . mysql_escape_string($thesessionid) . "' AND ordID='" . mysql_escape_string($ordID) . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		$success = FALSE;
		if($rs = mysql_fetch_assoc($result))
			$success = (trim($rs['ordAuthNumber'])!='');
		mysql_free_result($result);
	}
	if($success)
		order_success($ordID,$emailAddr,$sendEmail);
	else
		order_failed();
}elseif(@$_POST['cart_order_id'] != '' && @$_POST['order_number'] != ''){ // 2Checkout Transaction
	if(trim(@$_POST['credit_card_processed'])=='Y'){
		$ordID = trim(@$_POST['cart_order_id']);
		$success = getpayprovdetails(2,$acctno,$md5key,$data3,$demomode,$ppmethod);
		$keysmatch=TRUE;
		if($md5key != ''){
			$theirkey = trim(@$_POST['key']);
			$ourkey = trim(strtoupper(md5($md5key . $acctno . ($demomode ? '1' : @$_POST['order_number']) . @$_POST['total'])));
			if($ourkey==$theirkey) $keysmatch=TRUE; else $keysmatch=FALSE;
		}
		if($success && $keysmatch){
			do_stock_management($ordID);
			mysql_query("UPDATE cart SET cartCompleted=1 WHERE cartOrderID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
			mysql_query("UPDATE orders SET ordStatus=3,ordAuthNumber='" . mysql_escape_string(trim(@$_POST["order_number"])) . "' WHERE ordPayProvider=2 AND ordID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
			order_success($ordID,$emailAddr,$sendEmail);
		}else{
			order_failed();
		}
	}else{
		order_failed();
	}
}elseif(@$_POST["CUSTID"] != "" && @$_POST["AUTHCODE"] != ""){ // PayFlow Link
	$success = getpayprovdetails(8,$data1,$data2,$data3,$demomode,$ppmethod);
	if($success && trim(@$_POST["RESULT"])=="0"){
		$ordID = trim(@$_POST["CUSTID"]);
		do_stock_management($ordID);
		mysql_query("UPDATE cart SET cartCompleted=1 WHERE cartOrderID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
		mysql_query("UPDATE orders SET ordStatus=3,ordAVS='" . mysql_escape_string(trim(@$_POST["AVSDATA"])) . "',ordCVV='" . mysql_escape_string(trim(@$_POST["CSCMATCH"])) . "',ordAuthNumber='" . mysql_escape_string(unstripslashes(trim(@$_POST["AUTHCODE"]))) . "' WHERE ordPayProvider=8 AND ordID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
		order_success($ordID,$emailAddr,$sendEmail);
	}else{
		order_failed();
	}
}elseif(@$_POST["emailorder"] != "" || @$_POST["secondemailorder"] != ""){
	$ordGndTot=1;
	if(@$emailorderstatus != "") $ordStatus=$emailorderstatus; else $ordStatus=3;
	if(@$_POST["emailorder"] != ""){
		$ordID = trim(@$_POST["emailorder"]);
		$ppid = 4;
	}else{
		$ordID = trim(@$_POST["secondemailorder"]);
		$ppid = 17;
	}
	$thesessionid = trim(str_replace("'",'',@$_POST['thesessionid']));
	$sSQL = "SELECT ordAuthNumber,((ordShipping+ordStateTax+ordCountryTax+ordTotal+ordHandling)-ordDiscount) AS ordGndTot FROM orders WHERE ordSessionID='" . mysql_escape_string($thesessionid) . "' AND ordID='" . mysql_escape_string($ordID) . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	$success = FALSE;
	if($rs = mysql_fetch_assoc($result)){
		$success = TRUE;
		$ordGndTot=$rs['ordGndTot'];
	}
	mysql_free_result($result);
	$sSQL = "SELECT payProvShow FROM payprovider WHERE (payProvEnabled=1 OR ".$ordGndTot."=0) AND payProvID=" . $ppid;
	$result = mysql_query($sSQL) or print(mysql_error());
	if($rs = mysql_fetch_assoc($result))
		$authnumber = $rs['payProvShow'];
	else
		$success = FALSE;
	mysql_free_result($result);
	if($success){
		if($ordStatus >= 3) do_stock_management($ordID);
		$sSQL="UPDATE cart SET cartCompleted=1 WHERE cartOrderID='" . mysql_escape_string($ordID) . "'";
		mysql_query($sSQL) or print(mysql_error());
		$sSQL="UPDATE orders SET ordStatus=" . $ordStatus . ",ordAuthNumber='" . substr(mysql_escape_string($authnumber),0,48) . "' WHERE (ordPayProvider=" . $ppid . " OR (ordTotal-ordDiscount)<=0) AND ordID='" . mysql_escape_string($ordID) . "'";
		mysql_query($sSQL) or print(mysql_error());
		order_success($ordID,$emailAddr,$sendEmail);
	}else
		order_failed();
}elseif(@$_GET['OrderID'] != '' && @$_GET['TransRefNumber'] != ''){ // PSiGate
	$sSQL = 'SELECT payProvID FROM payprovider WHERE payProvEnabled=1 AND payProvID=11 OR payProvID=12';
	$result = mysql_query($sSQL) or print(mysql_error());
	$success = (mysql_num_rows($result) > 0);
	mysql_free_result($result);
	if(@$_GET['Approved'] != 'APPROVED') $success=FALSE;
	if(@$_GET['CustomerRefNo'] != substr(md5(@$_GET['OrderID'].':'.@$secretword), 0, 24)) $success=FALSE;
	if($success){
		$ordID = trim(@$_GET['OrderID']);
		do_stock_management($ordID);
		mysql_query("UPDATE cart SET cartCompleted=1 WHERE cartOrderID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
		mysql_query("UPDATE orders SET ordStatus=3,ordAVS='" . mysql_escape_string(@$_GET['AVSResult'].'/'.@$_GET['IPResult']) . "',ordCVV='" . mysql_escape_string(@$_GET['CardIDResult']) . "',ordAuthNumber='" . mysql_escape_string(trim(@$_GET['CardAuthNumber'])) . "',ordTransID='" . mysql_escape_string(trim(@$_GET['CardRefNumber'])) . "' WHERE (ordPayProvider=11 OR ordPayProvider=12) AND ordID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
		order_success($ordID,$emailAddr,$sendEmail);
	}else{
		$errtext = @$_GET['ErrMsg'];
		order_failed();
	}
}elseif(@$_POST["ponumber"] != "" && (@$_POST["approval_code"] != "" || @$_POST["failReason"] != "")){ // Linkpoint
	if(getpayprovdetails(16,$data1,$data2,$data3,$demomode,$ppmethod)){
		$ordID=mysql_escape_string(trim(@$_POST["ponumber"]));
		$ordIDa=split(",", $ordID);
		$ordID=$ordIDa[0];
		$theauthcode=mysql_escape_string(trim(@$_POST["approval_code"]));
		$thesuccess=strtolower(trim(@$_POST["status"]));
		if(($thesuccess=="approved" || $thesuccess=="submitted") && $theauthcode != ''){
			$autharr = split(':', $theauthcode);
			if($autharr[0]=='Y' && count($autharr) >= 3){
				$theauthcode = $autharr[1];
				$theavscode = $autharr[2];
				do_stock_management($ordID);
				mysql_query("UPDATE cart SET cartCompleted=1 WHERE cartOrderID='$ordID'") or print(mysql_error());
				mysql_query("UPDATE orders SET ordStatus=3,ordAVS='" . substr($theavscode,0,3) . "',ordCVV='" . substr($theavscode,3) . "',ordAuthNumber='" . substr($theauthcode,0,6) . "',ordTransID='" . substr($theauthcode,6) . "' WHERE ordPayProvider=16 AND ordID='" . $ordID . "'") or print(mysql_error());
				order_success($ordID,$emailAddr,$sendEmail);
			}else{
				$errtext = 'Invalid auth code';
				order_failed();
			}
		}else{
			$errtext = @$_POST["failReason"];
			$errtextarr = split(':', $errtext);
			if(@$errtextarr[1] != '') $errtext = $errtextarr[1];
			order_failed();
		}
	}else
		order_failed();
}elseif(@$_POST["docapture"] == "vsprods"){
	if($success = getpayprovdetails(10,$data1,$data2,$data3,$demomode,$ppmethod)){
		$ordID=trim(@$_POST["ordernumber"]);
		if(@$capturecardorderstatus != "") $ordStatus=$capturecardorderstatus; else $ordStatus=3;
		$encryptmethod = strtolower(@$encryptmethod);
		if($encryptmethod=="none"){
			$enctext = trim(str_replace("'","",@$_POST["ACCT"])) . "&" . trim(str_replace("'","",@$_POST["EXMON"])) . "/" . trim(str_replace("'","",@$_POST["EXYEAR"])) . "&" . trim(str_replace("'","",@$_POST["CVV2"])) . "&" . trim(str_replace("'","",@$_POST["IssNum"]) . "&" . trim(URLEncode(@$_POST["cardname"])));
		}elseif($encryptmethod=="mcrypt"){
			$thekey = @$ccencryptkey;
			if(@$mcryptalg == "") $mcryptalg = MCRYPT_BLOWFISH;
			$td = mcrypt_module_open($mcryptalg, '', 'cbc', '');
			$thekey = substr($thekey, 0, mcrypt_enc_get_key_size($td));
			if(strlen($thekey)<10){
				print "<strong>Warning ! CC Encryption key is too short.</strong>";
				$enctext = "";
			}else{
				$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
				mcrypt_generic_init($td, $thekey, $iv);
				$enctext = bin2hex($iv) . " " . bin2hex(mcrypt_generic($td, trim(str_replace("'","",@$_POST["ACCT"])) . "&" . trim(str_replace("'","",@$_POST["EXMON"])) . "/" . trim(str_replace("'","",@$_POST["EXYEAR"])) . "&" . trim(str_replace("'","",@$_POST["CVV2"])) . "&" . trim(str_replace("'","",@$_POST["IssNum"])) . "&" . trim(URLEncode(@$_POST["cardname"]))));
				mcrypt_generic_deinit($td);
				mcrypt_module_close($td);
			}
		}else{
			print "WARNING: \$encryptmethod is not set. Please see http://www.ecommercetemplates.com/phphelp/ecommplus/parameters.asp#encryption<br />";
		}
		do_stock_management($ordID);
		mysql_query("UPDATE cart SET cartCompleted=1 WHERE cartOrderID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
		mysql_query("UPDATE orders SET ordStatus=".$ordStatus.",ordAuthNumber='Card Capture',ordCNum='" . @$enctext . "' WHERE ordPayProvider=10 AND ordID='" . mysql_escape_string($ordID) . "'") or print(mysql_error());
		order_success($ordID,$emailAddr,$sendEmail);
	}else
		order_failed();
}elseif(@$_GET["OrdNo"] != "" && @$_GET["ErrMsg"] != ""){ // PSiGate Error Reporting
	$errtext = @$_GET['ErrMsg'];
	order_failed();
}else{
	include "./vsadmin/inc/customppreturn.php";
}
?>