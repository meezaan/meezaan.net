<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$digidownloadsecret=="") $digidownloadsecret="this is some secwet text";
function vrhmac($key, $text){
	$idatastr = "                                                                ";
	$odatastr = "                                                                ";
	$hkey = (string)$key;
	$idatastr .= $text;
	for($i=0; $i<64; $i++){
		$idata[$i] = $ipad[$i] = 0x36;
		$odata[$i] = $opad[$i] = 0x5C;
	}
	for($i=0; $i< strlen($hkey); $i++){
		$ipad[$i] ^= ord($hkey{$i});
		$opad[$i] ^= ord($hkey{$i});
		$idata[$i] = ($ipad[$i] & 0xFF);
		$odata[$i] = ($opad[$i] & 0xFF);
	}
	for($i=0; $i< strlen($text); $i++){
		$idata[64+$i] = ord($text{$i}) & 0xFF;
	}
	for($i=0; $i< strlen($idatastr); $i++){
		$idatastr{$i} = chr($idata[$i] & 0xFF);
	}
	for($i=0; $i< strlen($odatastr); $i++){
		$odatastr{$i} = chr($odata[$i] & 0xFF);
	}
	$innerhashout = md5($idatastr);
	for($i=0; $i<16; $i++)
		$odatastr .= chr(hexdec(substr($innerhashout,$i*2,2)));
	return md5($odatastr);
}
function order_success($sorderid,$sEmail,$sendstoreemail){
	do_order_success($sorderid,$sEmail,$sendstoreemail,TRUE,TRUE,TRUE,TRUE);
}
function do_order_success($sorderid,$sEmail,$sendstoreemail,$doshowhtml,$sendcustemail,$sendaffilemail,$sendmanufemail){
	global $maintablebg,$innertablebg,$maintablewidth,$innertablewidth,$maintablespacing,$innertablespacing,$maintablepadding,$innertablepadding,$thereference,$emlNl,$htmlemails,$extraorderfield1,$extraorderfield2,$extraorderfield3,$shipType,$emailheader,$emailfooter,$emailencoding,$hideoptpricediffs,$xxWtIns,$ordGrandTotal,$ordID,$digidownloads,$dropshipfooter,$dropshipheader,$digidownloademail,$xxPrint,$dropshipsubject,$xxHST;
	global $xxHndlg,$xxDscnts,$xxOrdId,$xxCusDet,$xxEmail,$xxPhone,$xxShpDet,$xxShpMet,$xxAddInf,$xxPrId,$xxPrNm,$xxQuant,$xxUnitPr,$xxOrdTot,$xxStaTax,$xxCntTax,$xxShippg,$xxGndTot,$xxOrdStr,$xxTnxOrd,$xxTouSoo,$xxAff1,$xxAff2,$xxAff3,$xxThnks,$xxThkYou,$xxRecEml,$storeurl,$xxHomeURL,$xxCntShp,$success,$ordAuthNumber,$orderText,$ordTotal,$customheaders,$digidownloadsecret,$useaddressline2,$combineshippinghandling,$xxShipHa,$extracheckoutfield1,$extracheckoutfield2;
	if(@$htmlemails==TRUE) $emlNl = "<br />"; else $emlNl="\n";
	if(@$customheaders == ""){
		$customheaders = "MIME-Version: 1.0\n";
		$customheaders .= "From: %from% <%from%>\n";
		//$customheaders .= "To: " . $custEmail . " <" . $custEmail . ">\n";
		if(@$htmlemails==TRUE)
			$customheaders .= "Content-type: text/html; charset=".$emailencoding."\n";
		else
			$customheaders .= "Content-type: text/plain; charset=".$emailencoding."\n";
	}
	$affilID = "";
	$saveHeader = "";
	$ordID = $sorderid;
	$hasdownload=FALSE;
	$ndropshippers=0;
	$sSQL = "SELECT ordID,ordName,ordAddress,ordAddress2,ordCity,ordState,ordZip,ordCountry,ordEmail,ordPhone,ordShipName,ordShipAddress,ordShipAddress2,ordShipCity,ordShipState,ordShipZip,ordShipCountry,ordShipPhone,ordPayProvider,ordAuthNumber,ordTotal,ordDate,ordStateTax,ordCountryTax,ordHSTTax,ordHandling,ordShipping,ordAffiliate,ordDiscount,ordDiscountText,ordComLoc,ordExtra1,ordExtra2,ordShipExtra1,ordShipExtra2,ordCheckoutExtra1,ordCheckoutExtra2,ordSessionID,ordAddInfo,ordShipType,payProvID FROM orders LEFT JOIN payprovider ON payprovider.payProvID=orders.ordPayProvider WHERE ordAuthNumber<>'' AND ordID='" . mysql_escape_string($sorderid) . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	if(mysql_num_rows($result) > 0){
		$rs = mysql_fetch_assoc($result);
		$orderText = '';
		$success=TRUE;
		$ordAuthNumber = $rs['ordAuthNumber'];
		$ordSessionID = $rs['ordSessionID'];
		$payprovid = $rs['payProvID'];
		$ordName = $rs['ordName'];
		if(@$emailheader != '') $saveHeader .= $emailheader;
		eval('global $emailheader' . $payprovid . ';$emailheader = @$emailheader' . $payprovid . ';');
		if(@$emailheader != "") $saveHeader .= $emailheader;
		$saveHeader = str_replace('%ordername%', $ordName, $saveHeader);
		$orderText .= $xxOrdId . ': ' . $rs['ordID'] . $emlNl;
		if($thereference != '') $orderText .= 'Transaction Ref' . ': ' . $thereference . $emlNl;
		$orderText .= $xxCusDet . ': ' . $emlNl;
		if(trim(@$extraorderfield1)!='') $orderText .= $extraorderfield1 . ': ' . $rs['ordExtra1'] . $emlNl;
		$orderText .= $ordName . $emlNl;
		$orderText .= $rs['ordAddress'] . $emlNl;
		if(trim($rs['ordAddress2']) != '') $orderText .= $rs['ordAddress2'] . $emlNl;
		$orderText .= $rs['ordCity'] . ', ' . $rs['ordState'] . $emlNl;
		$orderText .= $rs['ordZip'] . $emlNl;
		$orderText .= $rs['ordCountry'] . $emlNl;
		$orderText .= $xxEmail . ': ' . $rs['ordEmail'] . $emlNl;
		$custEmail = $rs['ordEmail'];
		$orderText .= $xxPhone . ': ' . $rs['ordPhone'] . $emlNl;
		if(trim(@$extraorderfield2)!='') $orderText .= $extraorderfield2 . ': ' . $rs['ordExtra2'] . $emlNl;
		if(trim($rs['ordShipName']) != '' || trim($rs['ordShipAddress']) != ''){
			$orderText .= $xxShpDet . ': ' . $emlNl;
			if(trim(@$extraorderfield1)!='' && trim($rs['ordShipExtra1'])!='') $orderText .= $extraorderfield1 . ': ' . $rs['ordShipExtra1'] . $emlNl;
			$orderText .= $rs['ordShipName'] . $emlNl;
			$orderText .= $rs['ordShipAddress'] . $emlNl;
			if(trim($rs['ordShipAddress2']) != '') $orderText .= $rs['ordShipAddress2'] . $emlNl;
			$orderText .= $rs['ordShipCity'] . ', ' . $rs['ordShipState'] . $emlNl;
			$orderText .= $rs['ordShipZip'] . $emlNl;
			$orderText .= $rs['ordShipCountry'] . $emlNl;
			if(trim($rs['ordShipPhone']!='')) $orderText .= $xxPhone . ': ' . $rs['ordShipPhone'] . $emlNl;
			if(trim(@$extraorderfield2)!='' && trim($rs['ordShipExtra2'])!='') $orderText .= $extraorderfield2 . ': ' . $rs['ordShipExtra2'] . $emlNl;
		}
		$ordShipType = $rs['ordShipType'];
		if($ordShipType != ''){
			$orderText .= $emlNl . $xxShpMet . ': ' . $ordShipType;
			if(($rs['ordComLoc'] & 2)==2) $orderText .= $xxWtIns;
			$orderText .= $emlNl;
			if(($rs['ordComLoc'] & 1)==1) $orderText .= $xxCerCLo . $emlNl;
			if(($rs['ordComLoc'] & 4)==4) $orderText .= $xxSatDeR . $emlNl;
		}
		if(trim(@$extracheckoutfield1)!='' && trim($rs['ordCheckoutExtra1'])!='') $orderText .= $extracheckoutfield1 . ': ' . $rs['ordCheckoutExtra1'] . $emlNl;
		if(trim(@$extracheckoutfield2)!='' && trim($rs['ordCheckoutExtra2'])!='') $orderText .= $extracheckoutfield2 . ': ' . $rs['ordCheckoutExtra2'] . $emlNl;
		$ordAddInfo = trim($rs['ordAddInfo']);
		if($ordAddInfo != ''){
			$orderText .= $emlNl . $xxAddInf . ': ' . $emlNl;
			$orderText .= $ordAddInfo . $emlNl;
		}
		$ordTotal = $rs['ordTotal'];
		$ordDate = $rs['ordDate'];
		$ordStateTax = $rs['ordStateTax'];
		$ordDiscount = $rs['ordDiscount'];
		$ordDiscountText = $rs['ordDiscountText'];
		$ordCountryTax = $rs['ordCountryTax'];
		$ordHSTTax = $rs['ordHSTTax'];
		$ordShipping = $rs['ordShipping'];
		$ordHandling = $rs['ordHandling'];
		$affilID = trim($rs['ordAffiliate']);
	}else{
		$orderText = 'Cannot find customer details for order id: ' . $sorderid . $emlNl;
		$sendstoreemail=FALSE;
		$sendcustemail=FALSE;
		$sendaffilemail=FALSE;
		$sendmanufemail=FALSE;
	}
	mysql_free_result($result);
	$saveCustomerDetails=$orderText;
	$orderText = $saveHeader . '%digidownloadplaceholder%' . $orderText;
	$sSQL = "SELECT cartProdId,cartProdName,cartProdPrice,cartQuantity,cartID,pDropship".(@$digidownloads==TRUE?',pDownload':'')." FROM cart INNER JOIN products ON cart.cartProdId=products.pID WHERE cartOrderID='" . mysql_escape_string($sorderid) . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	if(mysql_num_rows($result) > 0){
		while($rs = mysql_fetch_assoc($result)){
			$localhasdownload=FALSE;
			if(@$digidownloads==TRUE)
				if(trim($rs["pDownload"]) != "") $localhasdownload=TRUE;
			$saveCartItems = "--------------------------" . $emlNl;
			$saveCartItems .= $xxPrId . ": " . $rs["cartProdId"] . $emlNl;
			$saveCartItems .= $xxPrNm . ": " . $rs["cartProdName"] . $emlNl;
			$saveCartItems .= $xxQuant . ": " . $rs["cartQuantity"] . $emlNl;
			$orderText .= $saveCartItems;
			$theoptions = "";
			$theoptionspricediff=0;
			$sSQL = "SELECT coOptGroup,coCartOption,coPriceDiff,optRegExp FROM cartoptions INNER JOIN options ON cartoptions.coOptID=options.optID WHERE coCartID=" . $rs["cartID"] . " ORDER BY coID";
			$result2 = mysql_query($sSQL) or print(mysql_error());
			while($rs2 = mysql_fetch_assoc($result2)){
				$theoptionspricediff += $rs2["coPriceDiff"];
				$optionline = (@$htmlemails==true?"&nbsp;&nbsp;&nbsp;&nbsp;>&nbsp;":"> > > ") . $rs2["coOptGroup"] . " : " . str_replace(array("\r\n","\n"),array($emlNl,$emlNl),$rs2["coCartOption"]);
				$theoptions .= $optionline;
				$saveCartItems .= $optionline . $emlNl;
				if($rs2["coPriceDiff"]==0 || @$hideoptpricediffs==TRUE)
					$theoptions .= $emlNl;
				else{
					$theoptions .= " (";
					if($rs2["coPriceDiff"] > 0) $theoptions .= "+";
					$theoptions .= FormatEmailEuroCurrency($rs2["coPriceDiff"]) . ")" . $emlNl;
				}
				if($rs2["optRegExp"] == "!!") $localhasdownload=FALSE;
			}
			$orderText .= $xxUnitPr . ": " . (@$hideoptpricediffs==TRUE ? FormatEmailEuroCurrency($rs["cartProdPrice"] + $theoptionspricediff) : FormatEmailEuroCurrency($rs["cartProdPrice"])) . $emlNl;
			$orderText .= $theoptions;
			if($rs["pDropship"] != 0){
				$index=0;
				for($index=0; $index<$ndropshippers; $index++){
					if($dropShippers[$index][0]==$rs["pDropship"]) break;
				}
				if($index>=$ndropshippers){
					$ndropshippers=$index+1;
					$dropShippers[$index][1]="";
				}
				$dropShippers[$index][0] = $rs["pDropship"];
				$dropShippers[$index][1] .= $saveCartItems;
			}
			if($localhasdownload==TRUE) $hasdownload=TRUE;
			mysql_free_result($result2);
		}
		$orderText .= "--------------------------" . $emlNl;

		$orderText .= $xxOrdTot . " : " . FormatEmailEuroCurrency($ordTotal) . $emlNl;
		if(@$combineshippinghandling==TRUE){
			$orderText .= $xxShipHa . " : " . FormatEmailEuroCurrency($ordShipping+$ordHandling) . $emlNl;
		}else{
			if($shipType != 0) $orderText .= $xxShippg . " : " . FormatEmailEuroCurrency($ordShipping) . $emlNl;
			if((double)$ordHandling!=0.0) $orderText .= $xxHndlg . " : " . FormatEmailEuroCurrency($ordHandling) . $emlNl;
		}
		if((double)$ordDiscount!=0.0) $orderText .= $xxDscnts . " : " . FormatEmailEuroCurrency($ordDiscount) . $emlNl;
		if((double)$ordStateTax!=0.0) $orderText .= $xxStaTax . " : " . FormatEmailEuroCurrency($ordStateTax) . $emlNl;
		if((double)$ordCountryTax!=0.0) $orderText .= $xxCntTax . " : " . FormatEmailEuroCurrency($ordCountryTax) . $emlNl;
		if((double)$ordHSTTax!=0.0) $orderText .= $xxHST . " : " . FormatEmailEuroCurrency($ordHSTTax) . $emlNl;
		$ordGrandTotal = ($ordTotal+$ordStateTax+$ordCountryTax+$ordHSTTax+$ordShipping+$ordHandling)-$ordDiscount;
		$orderText .= $xxGndTot . " : " . FormatEmailEuroCurrency($ordGrandTotal) . $emlNl;

		eval('global $emailfooter' . $payprovid . ';$emailheader = @$emailfooter' . $payprovid . ';');
		if(@$emailheader != "") $orderText .= $emailheader;
		if(@$emailfooter != "") $orderText .= $emailfooter;
	}else{
		$orderText .= "Cannot find order details for order id: " . $sorderid . $emlNl;
	}
	mysql_free_result($result);
	if($hasdownload==TRUE && @$digidownloademail != ""){
		$fingerprint = vrhmac($digidownloadsecret, $sorderid . $ordAuthNumber . $ordSessionID);
		$fingerprint = substr($fingerprint, 0, 14);
		$digidownloademail = str_replace('%orderid%',$ordID,$digidownloademail);
		$digidownloademail = str_replace('%password%',$fingerprint,$digidownloademail);
		$digidownloademail = str_replace('%nl%',$emlNl,$digidownloademail);
		$orderEmailText = str_replace('%digidownloadplaceholder%',$digidownloademail,$orderText);
	}else
		$orderEmailText = str_replace('%digidownloadplaceholder%',"",$orderText);
	$orderText = str_replace('%digidownloadplaceholder%',"",$orderText);
	if($sendstoreemail){
		$headers = str_replace('%from%',$sEmail,$customheaders);
		$headers = str_replace('%to%',$sEmail,$headers);
		mail($sEmail, str_replace('%orderid%', $sorderid, $xxOrdStr), $orderEmailText, $headers);
	}
	// And one for the customer
	if($sendcustemail){
		$headers = str_replace('%from%',$sEmail,$customheaders);
		$headers = str_replace('%to%',$custEmail,$headers);
		$thesubject = str_replace('%ordername%', $ordName, $xxTnxOrd);
		if(@$encodecustomeremailsubject==TRUE) $thesubject=encodeemailsubject($thesubject, $emailencoding);
		mail($custEmail, $thesubject, $xxTouSoo . $emlNl . $emlNl . $orderEmailText, $headers);
	}
	// Drop Shippers
	if($sendmanufemail){
		for($index=0; $index < $ndropshippers; $index++){
			if(@$dropshipsubject=="") $dropshipsubject="We have received the following order";
			$sSQL = "SELECT dsEmail,dsAction FROM dropshipper WHERE dsID=" . $dropShippers[$index][0];
			$result = mysql_query($sSQL) or print(mysql_error());
			if($rs = mysql_fetch_assoc($result)){
				if(($rs['dsAction'] & 1)==1 || (int)$sendmanufemail==2){
					$saveHeader = '';
					$saveFooter = '';
					$saveHeader .= @$dropshipheader;
					eval('global $dropshipheader' . $payprovid . ';$emailheader = @$dropshipheader' . $payprovid . ';');
					if($emailheader != '') $saveHeader .= $emailheader;
					eval('global $dropshipfooter' . $payprovid . ';$saveFooter = @$dropshipfooter' . $payprovid . ';');
					$saveFooter .= @$dropshipfooter;
					$headers = str_replace('%from%',$sEmail,$customheaders);
					$headers = str_replace('%to%',$rs['dsEmail'],$headers);
					mail($rs['dsEmail'], $dropshipsubject, $saveHeader . $saveCustomerDetails . $dropShippers[$index][1] . $saveFooter, $headers);
				}
			}
		}
	}
	if($sendaffilemail){
		if($affilID != ''){
			$sSQL = "SELECT affilEmail,affilInform FROM affiliates WHERE affilID='" . mysql_escape_string($affilID) . "'";
			$result = mysql_query($sSQL) or print(mysql_error());
			if($rs = mysql_fetch_assoc($result)){
				if((int)$rs["affilInform"]==1){
					$affiltext = $xxAff1 . " " . FormatEmailEuroCurrency($ordTotal-$ordDiscount) . ".".$emlNl.$emlNl.$xxAff2.$emlNl.$emlNl.$xxThnks.$emlNl;
					$headers = str_replace('%from%',$sEmail,$customheaders);
					$headers = str_replace('%to%',trim($rs["affilEmail"]),$headers);
					mail(trim($rs["affilEmail"]), $xxAff3, $emlNl . $affiltext, $headers);
				}
			}
			mysql_free_result($result);
		}
	}
	if($doshowhtml){
?>
<script language="javascript" type="text/javascript">
<!--
function doprintcontent()
{
	var prnttext = '<html><body>\n';
	prnttext += document.getElementById('printcontent').innerHTML;
	prnttext += '</body></html>';
	var newwin = window.open("","printit",'menubar=no, scrollbars=yes, width=600, height=450, directories=no,location=no,resizable=yes,status=no,toolbar=no');
	newwin.document.open();
	newwin.document.write(prnttext);
	newwin.document.close();
	newwin.print();
}
//-->
</script>
      <table border="0" cellspacing="<?php print $maintablespacing?>" cellpadding="<?php print $maintablepadding?>" width="<?php print $maintablewidth?>" bgcolor="<?php print $maintablebg?>" align="center">
        <tr>
          <td width="100%">
            <table width="<?php print $innertablewidth?>" border="0" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
			  <tr> 
                <td width="100%" align="center"><?php print $xxThkYou?>
                </td>
			  </tr>
		<?php	if(@$digidownloads!=TRUE){ ?>
			  <tr> 
                <td width="100%" align="left">
				  <span name="printcontent" id="printcontent">
					<?php print str_replace(array("\r\n","\n"),array("<br />","<br />"),$orderText)?>
				  </span>
                </td>
			  </tr>
			  <tr> 
                <td width="100%" align="center"><br />
				<?php if(trim($xxRecEml)!='')print $xxRecEml . '<br /><br />'?>
				<input type="button" value="&nbsp;<?php print $xxCntShp?>&nbsp;" onclick="document.location='<?php print $storeurl?>';" />
				<input type="button" value="&nbsp;<?php print $xxPrint?>&nbsp;" onclick="doprintcontent();" /><br />
				<img src="images/clearpixel.gif" width="300" height="3" alt="" />
                </td>
			  </tr>
		<?php	} ?>
			</table>
		  </td>
        </tr>
      </table>
<?php
	}
}
?>