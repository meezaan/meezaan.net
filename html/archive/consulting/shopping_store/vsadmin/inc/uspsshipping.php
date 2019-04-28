<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
function sortshippingarray(){
	global $intShipping,$maxshipoptions;
	for($ssaindex2=0; $ssaindex2 < $maxshipoptions; $ssaindex2++){
		$intShipping[$ssaindex2][2] = (double)$intShipping[$ssaindex2][2];
		for($ssaindex=1; $ssaindex < 20; $ssaindex++){
			if($intShipping[$ssaindex][3] && (double)$intShipping[$ssaindex][2] < (double)$intShipping[$ssaindex-1][2]){
				$tt = $intShipping[$ssaindex];
				$intShipping[$ssaindex] = $intShipping[$ssaindex-1];
				$intShipping[$ssaindex-1] = $tt;
			}
		}
	}
//	for($ssaindex=0; $ssaindex < $maxshipoptions; $ssaindex++)
//		print $intShipping[$ssaindex][0] . ":" . $intShipping[$ssaindex][1] . ":" . $intShipping[$ssaindex][2] . ":" . $intShipping[$ssaindex][3] . "<br>";
}
function ParseUSPSXMLOutput($sXML, $international, &$totalCost, &$errormsg, &$intShipping){
	global $iTotItems,$xxDay,$xxDays,$debugmode;
	$noError = TRUE;
	$totalCost = 0;
	$packCost = 0;
	$errormsg = "";
	$xmlDoc = new vrXMLDoc($sXML);

	if($xmlDoc->nodeList->nodeName[0] == "Error"){ // Top-level Error
		$noError = FALSE;
		$nodeList = $xmlDoc->nodeList->childNodes[0];
		for($i = 0; $i < $nodeList->length; $i++){
			if($nodeList->nodeName[$i]=="Description"){
				$errormsg = $nodeList->nodeValue[$i];
			}
		}
	}else{ // no Top-level Error
		$nodeList = $xmlDoc->nodeList->childNodes[0];
		for($i = 0; $i < $nodeList->length; $i++){
			if($nodeList->nodeName[$i]=="Package"){
				$tmpArr = split('x', getattributes($nodeList->attributes[$i], 'ID'));
				$quantity = (int)$tmpArr[1];
				$e = $nodeList->childNodes[$i];
				for($j = 0; $j < $nodeList->childNodes[$i]->length; $j++){
					if($e->nodeName[$j] == "Error"){ // Lower-level error
						$t = $e->childNodes[$j];
						for($k = 0; $k < $t->length; $k++){
							if($t->nodeName[$k] == "Description")
								if(@$debugmode) print 'USPS warning: ' . $t->nodeValue[$k] . '<br>';
						}
					}else{
						if($e->nodeName[$j] == "Postage"){
							if($international == ""){
								$l = 0;
								while($intShipping[$l][0] != $thisService && $intShipping[$l][0] != "")
									$l++;
								$intShipping[$l][0] = $thisService;
								if($thisService=="PARCEL")
									$intShipping[$l][1] = "2-7 " . $xxDays;
								elseif($thisService=="EXPRESS")
									$intShipping[$l][1] = "Overnight to most areas";
								elseif($thisService=="PRIORITY")
									$intShipping[$l][1] = "1-2 " . $xxDays;
								elseif($thisService=="BPM")
									$intShipping[$l][1] = "2-7 " . $xxDays;
								elseif($thisService=="Media")
									$intShipping[$l][1] = "2-7 " . $xxDays;
								elseif($thisService=="FIRST CLASS")
									$intShipping[$l][1] = "1-3 " . $xxDays;
								$intShipping[$l][2] = $intShipping[$l][2] + ($e->nodeValue[$j] * $quantity);
								$intShipping[$l][3] = $intShipping[$l][3] + 1;
							}
						}elseif($e->nodeName[$j] == "Service"){
							if($international != ""){
								$t = $e->childNodes[$j];
								for($k = 0; $k < $t->length; $k++){
									if($t->nodeName[$k] == "SvcDescription")
										$SvcDescription = $t->nodeValue[$k];
									elseif($t->nodeName[$k] == "SvcCommitments")
										$SvcCommitments = $t->nodeValue[$k];
									elseif($t->nodeName[$k] == "Postage")
										$Postage = $t->nodeValue[$k];
								}
								$l = 0;
								while($intShipping[$l][0] != "" && $intShipping[$l][0] != $SvcDescription)
									$l++;
								$intShipping[$l][0] = $SvcDescription;
								$intShipping[$l][1] = $SvcCommitments;
								$intShipping[$l][2] += ($Postage * $quantity);
								$intShipping[$l][3]++;
							}
							else
								$thisService = $e->nodeValue[$j];
						}
					}
				}
				$totalCost += $packCost;
				$packCost = 0;
			}
		}
	}
	return $noError;
}
function checkUPSShippingMeth($method, &$discountsApply, &$showAs){
	global $numuspsmeths, $uspsmethods;
	for($index=0; $index < $numuspsmeths; $index++){
		if($method==$uspsmethods[$index][0]){
			$discountsApply = $uspsmethods[$index][1];
			$showAs = $uspsmethods[$index][2];
			return(TRUE);
		}
	}
	return(FALSE);
}
function ParseUPSXMLOutput($sXML, $international, &$totalCost, &$errormsg, &$errorcode, &$intShipping){
	global $xxDay,$xxDays;
	$noError = TRUE;
	$totalCost = 0;
	$errormsg = "";
	$l = 0;
	$discntsApp = "";
	$xmlDoc = new vrXMLDoc($sXML);
	$nodeList = $xmlDoc->nodeList->childNodes[0];
	for($i = 0; $i < $nodeList->length; $i++){
		if($nodeList->nodeName[$i]=="Response"){
			$e = $nodeList->childNodes[$i];
			for($j = 0; $j < $e->length; $j++){
				if($e->nodeName[$j]=="ResponseStatusCode"){
					$noError = ((int)$e->nodeValue[$j])==1;
				}
				if($e->nodeName[$j]=="Error"){
					$errormsg = "";
					$t = $e->childNodes[$j];
					for($k = 0; $k < $t->length; $k++){
						if($t->nodeName[$k]=="ErrorCode"){
							$errorcode = $t->nodeValue[$k];
						}elseif($t->nodeName[$k]=="ErrorSeverity"){
							if($t->nodeValue[$k]=="Transient")
								$errormsg = "This is a temporary error. Please wait a few moments then refresh this page.<br />" . $errormsg;
						}elseif($t->nodeName[$k]=="ErrorDescription"){
							$errormsg .= $t->nodeValue[$k];
						}
					}
				}
				// print "The Nodename is : " . e.nodeName . ":" . e.firstChild.nodeValue . "<br />";
			}
		}elseif($nodeList->nodeName[$i]=="RatedShipment"){ // no Top-level Error
			$wantthismethod=TRUE;
			$nodeList = $xmlDoc->nodeList->childNodes[0];
			$e = $nodeList->childNodes[$i];
			for($j = 0; $j < $e->length; $j++){
				if($e->nodeName[$j] == "Service"){ // Lower-level error
					$t = $e->childNodes[$j];
					for($k = 0; $k < $t->length; $k++){
						if($t->nodeName[$k]=="Code"){
							if($t->nodeValue[$k]=="01")
								$intShipping[$l][0] = "UPS Next Day Air&reg;";
							elseif($t->nodeValue[$k]=="02")
								$intShipping[$l][0] = "UPS 2nd Day Air&reg;";
							elseif($t->nodeValue[$k]=="03")
								$intShipping[$l][0] = "UPS Ground";
							elseif($t->nodeValue[$k]=="07")
								$intShipping[$l][0] = "UPS Worldwide Express";
							elseif($t->nodeValue[$k]=="08")
								$intShipping[$l][0] = "UPS Worldwide Expedited";
							elseif($t->nodeValue[$k]=="11")
								$intShipping[$l][0] = "UPS Standard";
							elseif($t->nodeValue[$k]=="12")
								$intShipping[$l][0] = "UPS 3 Day Select&reg;";
							elseif($t->nodeValue[$k]=="13")
								$intShipping[$l][0] = "UPS Next Day Air Saver&reg;";
							elseif($t->nodeValue[$k]=="14")
								$intShipping[$l][0] = "UPS Next Day Air&reg; Early A.M.&reg;";
							elseif($t->nodeValue[$k]=="54")
								$intShipping[$l][0] = "UPS Worldwide Express Plus";
							elseif($t->nodeValue[$k]=="59")
								$intShipping[$l][0] = "UPS 2nd Day Air A.M.&reg;";
							elseif($t->nodeValue[$k]=="65")
								$intShipping[$l][0] = "UPS Express Saver";
							$wantthismethod = checkUPSShippingMeth($t->nodeValue[$k], $discntsApp, $notUsed);
							$intShipping[$l][4] = $discntsApp;
						}
					}
				}elseif($e->nodeName[$j] == "TotalCharges"){
					$t = $e->childNodes[$j];
					for($k = 0; $k < $t->length; $k++){
						if($t->nodeName[$k]=="MonetaryValue"){
							$intShipping[$l][2] = (double)$t->nodeValue[$k];
						}
					}
				}elseif($e->nodeName[$j] == "GuaranteedDaysToDelivery"){
					if(strlen($e->nodeValue[$j]) > 0){
						if($e->nodeValue[$j]=="1")
							$intShipping[$l][1] = "1 " . $xxDay . $intShipping[$l][1];
						else
							$intShipping[$l][1] = $e->nodeValue[$j] . " " . $xxDays . $intShipping[$l][1];
					}
				}elseif($e->nodeName[$j] == "ScheduledDeliveryTime"){
					if(strlen($e->nodeValue[$j]) > 0){
						$intShipping[$l][1] .= " by " . $e->nodeValue[$j];
					}
				}
			}
			if($wantthismethod){
				$intShipping[$l][3] = TRUE;
				$l++;
			}else
				$intShipping[$l][1] = "";
			$wantthismethod=TRUE;
		}
	}
	return $noError;
}
function ParseCanadaPostXMLOutput($sXML, $international, &$totalCost, &$errormsg, &$errorcode, &$intShipping){
	global $xxDay,$xxDays;
	$noError = TRUE;
	$totalCost = 0;
	$errormsg = "";
	$discntsApp = "";
	$l = strpos($sXML, ']>');
	if($l > 0) $sXML = substr($sXML, $l+2);
	$l = 0;
	$cphandlingcharge=0;
	$xmlDoc = new vrXMLDoc($sXML);
	$nodeList = $xmlDoc->nodeList->childNodes[0];
	for($i = 0; $i < $nodeList->length; $i++){
		if($nodeList->nodeName[$i]=="error"){
			$noError = FALSE;
			$e = $nodeList->childNodes[$i];
			for($j = 0; $j < $e->length; $j++){
				if($e->nodeName[$j]=="statusCode"){
					$errorcode = $e->nodeValue[$j];
				}elseif($e->nodeName[$j]=="statusMessage"){
					$errormsg = $e->nodeValue[$j];
				}
			}
		}elseif($nodeList->nodeName[$i]=="ratesAndServicesResponse"){ // no Top-level Error
			$wantthismethod=TRUE;
			$nodeList = $xmlDoc->nodeList->childNodes[0];
			$e = $nodeList->childNodes[$i];
			for($j = 0; $j < $e->length; $j++){
				if($e->nodeName[$j] == 'handling')
					$cphandlingcharge = $e->nodeValue[$j];
			}
			for($j = 0; $j < $e->length; $j++){
				if($e->nodeName[$j] == "product"){
					$wantthismethod = checkUPSShippingMeth(getattributes($e->attributes[$j], 'id'), $discntsApp, $notUsed);
					$intShipping[$l][4] = $discntsApp;
					$wantthismethod=TRUE;
					$t = $e->childNodes[$j];
					for($k = 0; $k < $t->length; $k++){
						if($t->nodeName[$k]=="name"){
							$intShipping[$l][0] = $t->nodeValue[$k];
						}elseif($t->nodeName[$k]=="rate"){
							$intShipping[$l][2] = (double)$t->nodeValue[$k] + (double)$cphandlingcharge;
						}elseif($t->nodeName[$k]=="deliveryDate"){
							$today = getdate();
							$daytoday = $today["yday"];
							if(($ttimeval = strtotime($t->nodeValue[$k])) < 0){
								$intShipping[$l][1] = $t->nodeValue[$k] . $intShipping[$l][1];
							}else{
								$deldate = getdate($ttimeval);
								$daydeliv = $deldate["yday"];
								if($daydeliv < $daytoday) $daydeliv+=365;
								$intShipping[$l][1] = ($daydeliv - $daytoday) . " " . ($daydeliv - $daytoday < 2?$xxDay:$xxDays) . $intShipping[$l][1];
							}
						}elseif($t->nodeName[$k]=="nextDayAM"){
							if($t->nodeValue[$k]=="true")
								$intShipping[$l][1] = $intShipping[$l][1] . " AM";
						}
					}
					if($wantthismethod){
						$intShipping[$l][3] = TRUE;
						$l++;
					}else
						$intShipping[$l][1] = "";
					$wantthismethod=TRUE;
				}
			}
		}
	}
	return $noError;
}
function addUSPSDomestic($id,$service,$orig,$dest,$iWeight,$quantity,$container,$size,$machinable){
	global $numuspsmeths,$uspsmethods;
	$sXML="";
	$pounds = (int)$iWeight;
	$ounces = round(($iWeight-$pounds)*16.0);
	if($pounds==0 && $ounces==0) $ounces=1;
	for($index=0;$index<$numuspsmeths;$index++){
		$sXML .= '<Package ID="' . $uspsmethods[$index][0] . $id . 'x' . $quantity . '">';
		$sXML .= '<Service>' . $uspsmethods[$index][0] . '</Service>';
		$sXML .= '<ZipOrigination>' . $orig . '</ZipOrigination><ZipDestination>' . substr($dest, 0, 5) . '</ZipDestination>';
		$sXML .= '<Pounds>' . $pounds . '</Pounds><Ounces>' . $ounces . '</Ounces>';
		$sXML .= '<Container>' . $container . '</Container><Size>' . $size . '</Size>';
		$sXML .= '<Machinable>' . $machinable . '</Machinable></Package>';
	}
	return $sXML;
}
function addUSPSInternational($id,$iWeight,$quantity,$mailtype,$country){
	$pounds = (int)$iWeight;
	$ounces = round(($iWeight-$pounds)*16.0);
	if($pounds==0 && $ounces==0) $ounces=1;
	$sXML = '<Package ID="' . $id . 'x' . $quantity . '"><Pounds>' . $pounds . '</Pounds><Ounces>' . $ounces . '</Ounces><MailType>' . $mailtype . '</MailType><Country>' . $country . '</Country>';
	return $sXML . '</Package>';
}
function addUPSInternational($iWeight,$adminUnits,$packTypeCode,$country,$packcost,&$dimens){
	global $addshippinginsurance, $countryCurrency, $oversize, $adminUnits, $payproviderpost, $wantinsurancepost;
	if($iWeight<0.1) $iWeight=0.1;
	$sXML = '<Package><PackagingType><Code>' . $packTypeCode . '</Code><Description>Package</Description></PackagingType>';
	if($oversize != 0) $sXML .= '<OversizePackage>' . $oversize . '</OversizePackage>';
	$oversize = 0;
	if($dimens[0] > 0 && $dimens[1] > 0 && $dimens[2] > 0) $sXML .= '<Dimensions><Length>' . round($dimens[0],0) . '</Length><Width>' . round($dimens[1],0) . '</Width><Height>' . round($dimens[2],0) . '</Height><UnitOfMeasurement><Code>' . (($adminUnits & 12)==4 ? 'IN' : 'CM') . '</Code></UnitOfMeasurement></Dimensions>';
	$dimens[0]=0; $dimens[1]=0; $dimens[2]=0; $dimens[3]=0;
	$sXML .= '<Description>Rate Shopping</Description><PackageWeight><UnitOfMeasurement><Code>' . (($adminUnits & 1)==1 ? 'LBS' : 'KGS') . '</Code></UnitOfMeasurement><Weight>' . $iWeight . '</Weight></PackageWeight><PackageServiceOptions>';
	if(abs(@$addshippinginsurance)==1 || (abs(@$addshippinginsurance)==2 && $wantinsurancepost=='Y')){
		if($packcost > 50000) $packcost=50000;
		$sXML .= '<InsuredValue><CurrencyCode>' . $countryCurrency . '</CurrencyCode><MonetaryValue>' . number_format($packcost,2,'.','') . '</MonetaryValue></InsuredValue>';
	}
	if($payproviderpost != ''){
		if((int)$payproviderpost==@$codpaymentprovider) $sXML .= '<COD><CODFundsCode>0</CODFundsCode><CODCode>3</CODCode><CODAmount><CurrencyCode>'. $countryCurrency . '</CurrencyCode><MonetaryValue>' . number_format($packcost,2,'.','') . '</MonetaryValue></CODAmount></COD>';
	}
	if(@$signatureoption=='indirect')
		$sXML .= '<DeliveryConfirmation><DCISType>1</DCISType></DeliveryConfirmation>';
	elseif(@$signatureoption=='direct')
		$sXML .= '<DeliveryConfirmation><DCISType>2</DCISType></DeliveryConfirmation>';
	elseif(@$signatureoption=='adult')
		$sXML .= '<DeliveryConfirmation><DCISType>3</DCISType></DeliveryConfirmation>';
	return $sXML . '</PackageServiceOptions></Package>';
}
function addCanadaPostPackage($iWeight,$adminUnits,$packTypeCode,$country,$packcost,&$dimens){
	global $addshippinginsurance, $countryCurrency, $packtogether, $productdimensions;
	if($iWeight<0.1) $iWeight=0.1;
	if($packtogether) $thesize = 1; else $thesize = 19;
	if($dimens[0]=0) $dimens[0] = $thesize;
	if($dimens[1]=0) $dimens[1] = $thesize;
	if($dimens[2]=0) $dimens[2] = $thesize;
	$tmpXML = '<item><quantity> 1 </quantity><weight> ' . $iWeight . ' </weight><length> '.$dimens[0].' </length><width> '.$dimens[1].' </width><height> '.$dimens[2].' </height><description> Goods for shipping rates selection </description></item>';
	$dimens[0]=0; $dimens[1]=0; $dimens[2]=0; $dimens[3]=0;
	return $tmpXML;
}
function addFedexPackage($iWeight,$packages,$packcost,&$dimens){
	global $adminUnits;
	$tmpXML = '';
	if($iWeight < 0.1) $iWeight=0.1;
	if($dimens[0] > 0 && $dimens[1] > 0 && $dimens[2] > 0) $tmpXML = '<Dimensions><Length>' . round($dimens[0],0) . '</Length><Width>' . round($dimens[1],0) . '</Width><Height>' . round($dimens[2],0) . '</Height><Units>' . (($adminUnits & 12)==4 ? 'IN' : 'CM') . '</Units></Dimensions>';
	$dimens[0]=0; $dimens[1]=0; $dimens[2]=0; $dimens[3]=0;
	return $tmpXML . '<DeclaredValue>' . $packcost . '</DeclaredValue><PackageCount>' . $packages . '</PackageCount><Weight>' . number_format($iWeight,2,'.','') . '</Weight>';
}
function USPSCalculate($sXML,$international,&$totalCost,&$errormsg,&$intShipping){
	global $usecurlforfsock,$pathtocurl,$curlproxy,$destZip,$xxPlsZip;
	$success = TRUE;
	if($destZip==''){
		$errormsg=$xxPlsZip;
		return(FALSE);
	}
	$sXML = "API=" . $international . "Rate&XML=" . $sXML;
	if(@$usecurlforfsock){
		$success = callcurlfunction('http://production.shippingapis.com/ShippingAPI.dll', $sXML, $res, '', $errormsg, FALSE);
	}else{
		$header = "POST /ShippingAPI.dll HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= 'Content-Length: ' . strlen($sXML) . "\r\n\r\n";
		$fp = fsockopen ('production.shippingapis.com', 80, $errno, $errstr, 30);
		if (!$fp){
			echo "$errstr ($errno)"; // HTTP error handling
			return FALSE;
		}else{
			$res = "";
			fputs ($fp, $header . $sXML);
			while (!feof($fp)) {
				$res .= fgets ($fp, 1024);
			}
			fclose ($fp);
		}
	}
	if($success){
		// print str_replace("<","<br />&lt;",$res) . "<br />\n";
		$success = ParseUSPSXMLOutput($res, $international, $totalCost, $errormsg, $intShipping);
		sortshippingarray();
	}
	return $success;
}
function UPSCalculate($sXML,$international,&$totalCost, &$errormsg, &$intShipping){
	global $pathtocurl,$curlproxy,$destZip,$xxPlsZip;
	if($destZip==''){
		$errormsg=$xxPlsZip;
		return(FALSE);
	}
	if($success = callcurlfunction('https://www.ups.com/ups.app/xml/Rate', $sXML, $res, '', $errormsg, FALSE)){
		// print str_replace("<","<br />&lt;",$res) . "<br />\n";
		$success = ParseUPSXMLOutput($res, $international, $totalCost, $errormsg, $errorcode, $intShipping);
		sortshippingarray();
		if($errorcode == 111210) $errormsg = 'The destination zip / postal code is invalid.';
		if($errorcode == 110971) $errormsg = ''; // May differ from published rates.
		if($errorcode == 119070) $errormsg = ''; // Large package surcharge.
	}
	return $success;
}

function CanadaPostCalculate($sXML,$international,&$totalCost, &$errormsg, &$intShipping){
	global $pathtocurl,$usecurlforfsock,$curlproxy,$destZip,$xxPlsZip;
	$success = true;
	if($destZip==''){
		$errormsg=$xxPlsZip;
		return(FALSE);
	}
	if(@$usecurlforfsock){
		$success = callcurlfunction('sellonline.canadapost.ca:30000', $sXML, $res, '', $errormsg, FALSE);
	}else{
		$header = "POST / HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= 'Content-Length: ' . strlen($sXML) . "\r\n\r\n";
		$fp = fsockopen('sellonline.canadapost.ca', 30000, $errno, $errstr, 30);
		if (!$fp){
			echo "$errstr ($errno)"; // HTTP error handling
			return FALSE;
		}else{
			$res = "";
			fputs ($fp, $header . $sXML);
			while (!feof($fp)) {
				$res .= fgets ($fp, 1024);
			}
			fclose ($fp);
		}
	}
	if($success){
		// print str_replace("<","<br />&lt;",$res) . "<br />\n";
		$success = ParseCanadaPostXMLOutput($res, $international, $totalCost, $errormsg, $errorcode, $intShipping);
		sortshippingarray();
	}
	return $success;
}
function parsefedexXMLoutput($sXML, $international, &$errormsg, &$errorcode, &$intShipping){
	global $xxDay,$xxDays,$uselistshippingrates,$commerciallocpost;
	$noError = TRUE;
	$errormsg = "";
	$discntsApp = "";
	$l = strpos($sXML, ']>');
	if($l > 0) $sXML = substr($sXML, $l+2);
	$l = 0;
	$xmlDoc = new vrXMLDoc($sXML);
	$nodeList = $xmlDoc->nodeList->childNodes[0];
	for($i = 0; $i < $nodeList->length; $i++){
		if($nodeList->nodeName[$i]=="Error"){
			$noError = FALSE;
			$e = $nodeList->childNodes[$i];
			for($j = 0; $j < $e->length; $j++){
				if($e->nodeName[$j]=="Message"){
					$errormsg = $e->nodeValue[$j];
				}elseif($e->nodeName[$j]=="Code"){
					$errorcode = $e->nodeValue[$j];
				}
			}
		}elseif($nodeList->nodeName[$i]=="Entry"){
			$wantthismethod=FALSE;
			$e = $nodeList->childNodes[$i];
			$entryweight = $e->getValueByTagName('BilledWeight');
			for($j = 0; $j < $e->length; $j++){
				if($e->nodeName[$j] == "Service"){
					$wantthismethod = checkUPSShippingMeth($e->nodeValue[$j], $discntsApp, $showAs);
					if($e->nodeValue[$j]=='FEDEXGROUND' && $commerciallocpost != 'Y' && $entryweight<=70.0) $wantthismethod=FALSE;
					if($wantthismethod){
						$intShipping[$l][0] = $showAs;
						$intShipping[$l][4] = $discntsApp;
					}
				}elseif($e->nodeName[$j] == "EstimatedCharges"){
					$t = $e->childNodes[$j];
					for($k = 0; $k < $t->length; $k++){
						if($t->nodeName[$k]=="DiscountedCharges"){
							$intShipping[$l][2] = 0;
							$u = $t->childNodes[$k];
							for($kk = 0; $kk < $u->length; $kk++){
								if($u->nodeName[$kk]=="NetCharge"){
									$intShipping[$l][2] += (double)$u->nodeValue[$kk];
								}elseif($u->nodeName[$kk]=="TotalDiscount"){
									if(@$uselistshippingrates==TRUE) $intShipping[$l][2] += (double)$u->nodeValue[$kk];
								}
							}
						}
					}
				}elseif($e->nodeName[$j] == "DeliveryDate"){
					$today = getdate();
					$daytoday = $today["yday"];
					if(($ttimeval = strtotime($e->nodeValue[$j])) < 0){
						$intShipping[$l][1] = $e->nodeValue[$j] . $intShipping[$l][1];
					}else{
						$deldate = getdate($ttimeval);
						$daydeliv = $deldate["yday"];
						if($daydeliv < $daytoday) $daydeliv+=365;
						$intShipping[$l][1] = ($daydeliv - $daytoday) . " " . ($daydeliv - $daytoday < 2?$xxDay:$xxDays) . $intShipping[$l][1];
					}
				}
			}
			if($wantthismethod){
				$intShipping[$l][3] = TRUE;
				$l++;
			}else
				$intShipping[$l][1] = "";
		}
	}
	return $noError;
}
function fedexcalculate($sXML,$international, &$errormsg, &$intShipping){
	global $destZip,$xxPlsZip,$payproviderpost;
	if($destZip==''){
		$errormsg=$xxPlsZip;
		return(FALSE);
	}
	if($payproviderpost != ''){
		if((int)$payproviderpost==@$codpaymentprovider) $sXML = str_replace("XXXFILLCODAMTHEREYYY", number_format($totalgoods,2,'.',''), $sXML);
	}
	// print str_replace("<","<br />&lt;",str_replace('</','&lt;/',$sXML)) . "<br />\n";
	if($success = callcurlfunction('https://gateway.fedex.com:443/GatewayDC', $sXML, $xmlres, '', $errormsg, FALSE))
		$success = parsefedexXMLoutput($xmlres, $international, $errormsg, $errorcode, $intShipping);
	// print str_replace("<","<br />&lt;",str_replace('</','&lt;/',$xmlres)) . "<br />\n";
	if($success) sortshippingarray();
	return $success;
}
?>
