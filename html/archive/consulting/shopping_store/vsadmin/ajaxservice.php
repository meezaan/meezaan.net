<SCRIPT language="php">
session_cache_limiter('none');
session_start();
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property
//of Internet Business Solutions SL. Any use, reproduction, disclosure or copying
//of any kind without the express and written permission of Internet Business 
//Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
include "db_conn_open.php";
include "includes.php";
include "inc/languageadmin.php";
include "inc/languagefile.php";
include "inc/incemail.php";
include "inc/incfunctions.php";
if(@$storesessionvalue=="") $storesessionvalue="virtualstore";
if(@$_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE){
	if(@$_SERVER["HTTPS"] == "on" || @$_SERVER["SERVER_PORT"] == "443")$prot='https://';else $prot='http://';
	header('Location: '.$prot.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/login.php');
	exit;
}
function sendmessagewithbasicauth($themessage){
	global $googledata1,$googledata2,$googledemomode,$curlproxy,$success;
	$cfurl='https://' . ($googledemomode ? 'sandbox' : 'checkout') . '.google.com' . ($googledemomode ? '/checkout' : '') . '/cws/v2/Merchant/' . $googledata1 . '/request';
	$success = TRUE;
	if(@$pathtocurl != ''){
		exec($pathtocurl . ' -H \'Authorization: Basic ' . base64_encode($googledata1 . ":" . $googledata2) . '\' -H \'Content-Type: application/xml\' -H \'Accept: application/xml\' --data-binary \'' . str_replace("'","\'", '<?xml version="1.0" encoding="UTF-8"?>' . $themessage) . '\' ' . $cfurl, $cfres, $retvar);
		$cfres = implode("\n",$cfres);
	}else{
		if (!$ch = curl_init()) {
			print "cURL package not installed in PHP. Set \$pathtocurl parameter.";
			$success=FALSE;
		}else{
			curl_setopt($ch, CURLOPT_URL, $cfurl);
			$headers = array('Authorization: Basic ' . base64_encode($googledata1 . ":" . $googledata2), 'Content-Type: application/xml', 'Accept: application/xml');
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_POSTFIELDS, '<?xml version="1.0" encoding="UTF-8"?>' . $themessage);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			if(@$curlproxy!=''){
				curl_setopt($ch, CURLOPT_PROXY, $curlproxy);
			}
			$cfres = curl_exec($ch);
			// print str_replace("<","<br />&lt;",str_replace('<'.'/','&lt;/',$cfres)) . "<br />\n";
			if(curl_error($ch) != ""){
				print 'cURL error: ' . curl_error($ch) . '<br />';
				$success=FALSE;
			}else{
				curl_close($ch);
			}
		}
	}
	return($cfres);
}
if(@$_GET['gid'] != ''){
	$ordID = str_replace("'",'',@$_GET['gid']);
	$sSQL = "SELECT ordPayProvider,ordAuthNumber,payProvData1,payProvData2,payProvDemo FROM orders INNER JOIN payprovider ON orders.ordPayProvider=payprovider.payProvID WHERE ordID='" . mysql_escape_string($ordID) . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	if($rs = mysql_fetch_assoc($result)){
		$authcode=$rs['ordAuthNumber'];
		$googledata1=$rs['payProvData1'];
		$googledata2=$rs['payProvData2'];
		$googledemomode=$rs['payProvDemo'];
	}
	if(@$_GET['act']=='charge'){
		// First set the status to process-order
		sendmessagewithbasicauth('<process-order xmlns="http://checkout.google.com/schema/2" google-order-number="' . $authcode . '"/>');

		$acttext = '<charge-order xmlns="http://checkout.google.com/schema/2" google-order-number="' . $authcode . '"></charge-order>';
	}elseif(@$_GET['act']=='cancel')
		$acttext = '<cancel-order xmlns="http://checkout.google.com/schema/2" google-order-number="' . $authcode . '"><reason>Cancelled by store admin on ' . date('F d Y H:i:s') . '.</reason></cancel-order>';
	elseif(@$_GET['act']=='refund')
		$acttext = '<refund-order xmlns="http://checkout.google.com/schema/2" google-order-number="' . $authcode . '"><reason>Refunded by store admin on ' . date('F d Y H:i:s') . '.</reason></refund-order>';
	elseif(@$_GET['act']=='ship'){
		// First set the status to process-order
		sendmessagewithbasicauth('<process-order xmlns="http://checkout.google.com/schema/2" google-order-number="' . $authcode . '"/>');

		$acttext = '<deliver-order xmlns="http://checkout.google.com/schema/2" google-order-number="' . $authcode . '">';
		if(@$_GET['carrier'] != '' && @$_GET['trackno'] != ''){
			$sSQL = "UPDATE orders SET ordTrackNum='" . mysql_escape_string($_GET['trackno']) . "',ordShipCarrier=" . mysql_escape_string(@$_GET['carrier']) . " WHERE ordID='" . mysql_escape_string($ordID) . "'";
			mysql_query($sSQL) or print(mysql_error());
			$acttext .= '<tracking-data><carrier>';
			switch($_GET['carrier']){
				case "3":
					$acttext .= "USPS";
				break;
				case "4":
					$acttext .= "UPS";
				break;
				case "7":
					$acttext .= "FedEx";
				break;
				case "8":
					$acttext .= "DHL";
				break;
				default:
					$acttext .= "Other";
			}
			$acttext .= '</carrier><tracking-number>' . trim($_GET['trackno']) . '</tracking-number></tracking-data>';
		}
		$acttext .= '</deliver-order>';
	}elseif(@$_GET['act']=='message'){
		// First set the status to process-order
		sendmessagewithbasicauth('<process-order xmlns="http://checkout.google.com/schema/2" google-order-number="' . $authcode . '"/>');
		
		$acttext = '<send-buyer-message xmlns="http://checkout.google.com/schema/2" google-order-number="' . $authcode . '"><message>' . @$_POST['googlemessage'] . '</message><send-email>true</send-email></send-buyer-message>';
	}
	
	$cfres = sendmessagewithbasicauth($acttext);
	
	if(! $success){
		print '<font color="#FF0000">' . "Error, couldn't update order " . $ordID . '</font><br/>';
	}else{
		$xmlDoc = new vrXMLDoc($cfres);
		$nodeList = $xmlDoc->nodeList->childNodes[0];
		if(($errmsg = $nodeList->getValueByTagName('error-message')) != null)
			print '<font color="#FF0000">' . $errmsg . '</font><br/>';
		else
			print 'Finished updating order ' . $ordID;
	}
}
?>