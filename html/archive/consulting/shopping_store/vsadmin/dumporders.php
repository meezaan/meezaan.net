<?php
ob_start();
session_cache_limiter('none');
session_start();
//=========================================
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protected under law as the intellectual property
//of Internet Business Solutions SL. Any use, reproduction, disclosure or copying
//of any kind without the express and written permission of Internet Business 
//Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
include "db_conn_open.php";
include "includes.php";
include "inc/incfunctions.php";
function twodp($theval){
	return(number_format($theval,2,'.',''));
}
function xmlstrip($name2){
	$name2 = str_replace(
		array('&','’','–','-',"'",'€','£','é','è','™','ú','á','ñ','ü','ö','®','"','“','”','©','å'),
		array('chr(11)','chr(146)','chr(150)','chr(150)','chr(39)chr(39)','chr(128)','chr(163)','chr(130)','chr(138)','','u','a','n','chr(129)','chr(148)','','','','','','a'),
		$name2);
	$tmp_str="";
	for($i=0; $i < strlen($name2); $i++){
		$ch_code=ord(substr($name2,$i,1));
		if($ch_code>130) $tmp_str .= 'chr(' . $ch_code . ')'; else $tmp_str .= substr($name2,$i,1);
	}
	return($tmp_str);
}
function getsearchparams(){
	global $sd, $ed;
	$tmpsql = '';
	if(@$_POST["powersearch"]=="1"){
		$fromdate = trim(@$_POST["fromdate"]);
		$todate = trim(@$_POST["todate"]);
		$ordid = trim(str_replace('"','',str_replace("'","",@$_POST["ordid"])));
		$searchtext = trim(mysql_escape_string(unstripslashes(@$_POST["searchtext"])));
		$ordstatus = "";
		$addcomma = "";
		if(is_array(@$_POST["ordstatus"])){
			foreach($_POST["ordstatus"] as $objValue){
				$ordstatus .= $addcomma . $objValue;
				$addcomma = ",";
			}
		}else
			$ordstatus = trim(@$_POST["ordstatus"]);
		$tmpsql .= " WHERE 1=1";
		if($ordid != ""){
			if(is_numeric($ordid)){
				$tmpsql .= " AND ordID=" . $ordid;
			}else{
				$success=FALSE;
				$errmsg="The order id you specified seems to be invalid - " . $ordid;
				$tmpsql .= " AND ordID=0";
			}
		}else{
			if($fromdate != ""){
				if(is_numeric($fromdate))
					$thefromdate = time()-($fromdate*60*60*24);
				else
					$thefromdate = parsedate($fromdate);
				if($todate=="")
					$thetodate = $thefromdate;
				elseif(is_numeric($todate))
					$thetodate = time()-($todate*60*60*24);
				else
					$thetodate = parsedate($todate);
				if($thefromdate > $thetodate){
					$tmpdate = $thetodate;
					$thetodate = $thefromdate;
					$thefromdate = $tmpdate;
				}
				$sd = $thefromdate;
				$ed = $thetodate;
				$tmpsql .= " AND ordDate BETWEEN '" . date("Y-m-d", $sd) . "' AND '" . date("Y-m-d", $ed) . " 23:59:59'";
			}
			if($ordstatus != "" && strpos($ordstatus,"9999")===FALSE) $tmpsql .= " AND ordStatus IN (" . $ordstatus . ")";
			if($searchtext != "") $tmpsql .= " AND (ordAuthNumber LIKE '%" . $searchtext . "%' OR ordName LIKE '%" . $searchtext . "%' OR ordEmail LIKE '%" . $searchtext . "%' OR ordAddress LIKE '%" . $searchtext . "%' OR ordCity LIKE '%" . $searchtext . "%' OR ordState LIKE '%" . $searchtext . "%' OR ordZip LIKE '%" . $searchtext . "%' OR ordPhone LIKE '%" . $searchtext . "%')";
		}
		$tmpsql .= " ORDER BY ordID";
	}else{
		$tmpsql .= " WHERE ordDate BETWEEN '" . date("Y-m-d", $sd) . "' AND '" . date("Y-m-d", $ed) . " 23:59:59' ORDER BY ordID";
	}
	return($tmpsql);
}
if(@$storesessionvalue=="") $storesessionvalue="virtualstore";
if(@$_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE){
	if(@$_SERVER["HTTPS"] == "on" || @$_SERVER["SERVER_PORT"] == "443")$prot='https://';else $prot='http://';
	header('Location: '.$prot.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/login.php');
	exit;
}
$hasdetails = (@$_POST['act']=='dumpdetails');
header('Content-type: unknown/exe');
if(@$_POST['act']=='stockinventory')
	header('Content-Disposition: attachment;filename=stockinventory.csv');
elseif(@$_POST['act']=='dump2COinventory')
	header('Content-Disposition: attachment;filename=inventory2co.csv');
elseif(@$_POST['act']=='fullinventory')
	header('Content-Disposition: attachment;filename=inventory.csv');
elseif(@$_POST['act']=='dumpaffiliate')
	header('Content-Disposition: attachment;filename=affilreport.csv');
elseif(@$_POST['act']=='quickbooks'){
}elseif(@$_POST['act']=='ouresolutionsxmldump'){
	header('Content-Disposition: attachment;filename=oes_ordersdata.xml');
}elseif($hasdetails)
	header('Content-Disposition: attachment;filename=orderdetails.csv');
else
	header('Content-Disposition: attachment;filename=dumporders.csv');
$alreadygotadmin = getadminsettings();
$admindatestr='Y-m-d';
if(@$admindateformat=='') $admindateformat=0;
if($admindateformat==1)
	$admindatestr='m/d/Y';
elseif($admindateformat==2)
	$admindatestr='d/m/Y';
if(@$_POST['sd'] != '')
	$sd = @$_POST['sd'];
elseif(@$_GET['sd'] != '')
	$sd = @$_GET['sd'];
else
	$sd = date($admindatestr);
if(@$_POST['ed'] != '')
	$ed = @$_POST['ed'];
elseif(@$_GET['ed'] != '')
	$ed = @$_GET['ed'];
else
	$ed = date($admindatestr);
$sd = parsedate($sd);
$ed = parsedate($ed);
$sslok=TRUE;
if(@$_SERVER["HTTPS"] != "on" && (@$_SERVER["SERVER_PORT"] != "443") && @$nochecksslserver != TRUE) $sslok = FALSE;
if(@$_POST["act"]=="dumpaffiliate"){
	print "Affiliate report for " . date($admindatestr, $sd) . " to " . date($admindatestr, $ed) . "\r\n";
	print '"ID","Name","Address","City","State","Zip","Country","Email","Total"' . "\r\n";
	$sSQL = "SELECT affilID,affilName,affilAddress,affilCity,affilState,affilZip,affilCountry,affilEmail FROM affiliates ORDER BY affilID";
	$result = mysql_query($sSQL) or print(mysql_error());
	while($rs = mysql_fetch_assoc($result)){
		print '"' . str_replace('"','""',$rs["affilID"]) . '",';
		print '"' . str_replace('"','""',$rs["affilName"]) . '",';
		print '"' . str_replace('"','""',$rs["affilAddress"]) . '",';
		print '"' . str_replace('"','""',$rs["affilCity"]) . '",';
		print '"' . str_replace('"','""',$rs["affilState"]) . '",';
		print '"' . str_replace('"','""',$rs["affilZip"]) . '",';
		print '"' . str_replace('"','""',$rs["affilCountry"]) . '",';
		print '"' . str_replace('"','""',$rs["affilEmail"]) . '",';
		$sSQL2 = "SELECT SUM(ordTotal-ordDiscount) FROM affiliates LEFT JOIN orders ON affiliates.affilID=orders.ordAffiliate WHERE affilID='" . $rs["affilID"] . "' AND ordStatus>=3 AND ordDate BETWEEN '" . date("Y-m-d", $sd) . "' AND '" . date("Y-m-d", $ed) . " 23:59:59'";
		$alldata2 = mysql_query($sSQL2) or print(mysql_error());
		$rs2=mysql_fetch_array($alldata2);
		print $rs2[0] . "\r\n";
		mysql_free_result($alldata2);
	}
	mysql_free_result($result);
}elseif(@$_POST["act"]=="stockinventory"){
	$sSQL2 = "SELECT pID,pName,pPrice,pInStock,pStockByOpts FROM products";
	$result = mysql_query($sSQL2) or print(mysql_error());
	print "pID,pName,pPrice,pInStock,optID,OptionGroup,Option\r\n";
	while($rs = mysql_fetch_assoc($result)){
		if((int)$rs['pStockByOpts'] != 0){
			$result2 = mysql_query("SELECT optID,optGrpName,optName,optStock FROM optiongroup INNER JOIN options ON optiongroup.optGrpID=options.optGroup INNER JOIN prodoptions ON options.optGroup=prodoptions.poOptionGroup WHERE prodoptions.poProdID='" . mysql_escape_string($rs["pID"]) . "'") or print(mysql_error());
			while($rs2 = mysql_fetch_assoc($result2)){
				print '"' . str_replace('"','""',$rs['pID']) . '",';
				print '"' . str_replace('"','""',$rs['pName']) . '",';
				print '"' . $rs['pPrice'] . '",';
				print $rs2['optStock'] . ",";
				print $rs2['optID'] . ",";
				print '"' . str_replace('"','""',$rs2['optGrpName']) . '",';
				print '"' . str_replace('"','""',$rs2['optName']) . '"' . "\r\n";
			}
		}else{
			print '"' . str_replace('"','""',$rs['pID']) . '",';
			print '"' . str_replace('"','""',$rs['pName']) . '",';
			print '"' . $rs['pPrice'] . '",';
			print $rs['pInStock'] . ",,,\r\n";
		}
	}
	mysql_free_result($result);
}elseif(@$_POST["act"]=="fullinventory"){
	$fieldlist = "pID,pName";
	for($index=2; $index <= $adminlanguages+1; $index++){
		if(($adminlangsettings & 1)==1) $fieldlist .= ",pName" . $index;
	}
	$fieldlist .= ",pSection,pImage,pLargeimage,pPrice,pWholesalePrice,pListPrice,pShipping,pShipping2,pWeight,pDisplay,pSell,pExemptions,pInStock,pDims,pTax,pDropship";
	if(@$digidownloads==TRUE) $fieldlist .= ",pDownload";
	$fieldlist .= ",pStaticPage,pStockByOpts,pDescription";
	for($index=2; $index <= $adminlanguages+1; $index++){
		if(($adminlangsettings & 2)==2) $sSQL2 .= ",pDescription" . $index;
	}
	$fieldlist .= ",pLongDescription";
	for($index=2; $index <= $adminlanguages+1; $index++){
		if(($adminlangsettings & 4)==4) $sSQL2 .= ",pLongDescription" . $index;
	}
	$result = mysql_query("SELECT " . $fieldlist . " FROM products") or print(mysql_error());
	$fieldlistarr = split(",", $fieldlist);
	$addcomma = '';
	foreach($fieldlistarr as $flarrval){
		print $addcomma;
		print '"' . $flarrval . '"';
		$addcomma = ',';
	}
	print "\r\n";
	while($rs = mysql_fetch_assoc($result)){
		$addcomma = '';
		foreach($fieldlistarr as $flarrval){
			print $addcomma;
			print '"' . str_replace('"','""',$rs[$flarrval]). '"';
			$addcomma = ',';
		}
		print "\r\n";
	}
}elseif(@$_POST["act"]=="dump2COinventory"){
	$sSQL2 = "SELECT payProvData1 FROM payprovider WHERE payProvID=2";
	$result = mysql_query($sSQL2) or print(mysql_error());
	$rs = mysql_fetch_assoc($result);
	print $rs["payProvData1"] . "\r\n";
	mysql_free_result($result);
	$sSQL2 = "SELECT pID,pName,pPrice," . (@$digidownloads==TRUE ? "pDownload," : "") . "pDescription FROM products";
	$result = mysql_query($sSQL2) or print(mysql_error());
	while($rs = mysql_fetch_assoc($result)){
		print str_replace(',', '&#44;', $rs["pID"]) . ",";
		print preg_replace("(\r\n|\n|\r)",' ',str_replace(',', '&#44;',strip_tags($rs["pName"]))) . ",";
		print ",";
		print $rs["pPrice"] . ",";
		print ",,";
		if(@$digidownloads==TRUE)
			print (trim($rs["pDownload"]) != "" ? "N" : "Y") . ",";
		else
			print 'Y,';
		print preg_replace("(\r\n|\n|\r)",'\\n',str_replace(',','&#44;',strip_tags($rs["pDescription"]))) . "\r\n";
	}
	mysql_free_result($result);
}elseif(@$_POST["act"]=="quickbooks"){
}elseif(@$_POST["act"]=="ouresolutionsxmldump"){
	print '<?xml version="1.0"?>' . "\r\n";
	print '<DATABASE NAME="DataBaseCopy.mdb" >' . "\r\n";
	$sSQL = "SELECT ordID,cartProdId,cartProdName,cartProdPrice,cartQuantity,cartID FROM cart INNER JOIN orders ON cart.cartOrderId=orders.ordID";
	$sSQL .= getsearchparams();
	$result = mysql_query($sSQL) or print(mysql_error());
	while($rs = mysql_fetch_assoc($result)){
		$theoptionspricediff=0;
		$sSQL = "SELECT coPriceDiff,coOptGroup,coCartOption FROM cartoptions WHERE coCartID=" . $rs['cartID'];
		$result2 = mysql_query($sSQL) or print(mysql_error());
		while($rs2 = mysql_fetch_assoc($result2)){
			$theoptionspricediff += $rs2['coPriceDiff'];
		}
		$theunitprice = $rs['cartProdPrice']+$theoptionspricediff;
		$sSQL = "SELECT pName,pDescription,pDropShip FROM products WHERE pID='" . $rs['cartProdId'] . "'";
		$result2 = mysql_query($sSQL) or print(mysql_error());
		if($rs2 = mysql_fetch_assoc($result2)){
			$prodname = strip_tags($rs2['pName']);
			$proddesc = strip_tags($rs2['pDescription']);
			$supplier = $rs2['pDropShip'];
		}else{
			$prodname = '';
			$proddesc = '';
			$supplier = 0;
		}
		if($ouresolutionsxml==1)
			$itemname = strip_tags($rs['cartProdId']) . 'chr(60)brchr(62)' . $proddesc;
		elseif($ouresolutionsxml==3)
			$itemname = strip_tags($rs['cartProdId']);
		elseif($ouresolutionsxml==4)
			$itemname = $prodname;
		elseif($ouresolutionsxml==5)
			$itemname = strip_tags($rs['cartProdId']) . 'chr(60)brchr(62)' . $prodname;
		else // default to "2"
			$itemname = $prodname . 'chr(60)brchr(62)' . $proddesc;
		print "<DATA TABLE='oitems' ORDERITEMID='" . $rs['cartID'] . "' ORDERID='" . $rs['ordID'] . "' CATALOGID='" . $rs['cartID'] . "' NUMITEMS='" . $rs['cartQuantity'] . "' ITEMNAME='" . xmlstrip($itemname) . "' UNITPRICE='" . twodp($theunitprice) . "' DUALPRICE='0' SUPPLIERID='" . $supplier . "' ADDRESS='' />" . "\r\n";
	}
	$sSQL = "SELECT ordID,ordName,ordAddress,ordAddress2,ordCity,ordState,ordZip,ordCountry,ordEmail,ordPhone,ordExtra1,ordExtra2,ordShipExtra1,ordShipExtra2,ordCheckoutExtra1,ordCheckoutExtra2,ordShipName,ordShipAddress,ordShipAddress2,ordShipCity,ordShipState,ordShipZip,ordShipCountry,ordShipPhone,ordPayProvider,ordAuthNumber,ordTotal,ordDate,ordStateTax,ordCountryTax,ordHSTTax,ordShipping,ordHandling,ordShipType,ordDiscount,ordAffiliate,ordDiscountText,ordStatus,statPrivate,ordAddInfo FROM orders INNER JOIN orderstatus ON orders.ordStatus=orderstatus.statID";
	$sSQL .= getsearchparams();
	$result = mysql_query($sSQL) or print(mysql_error());
	while($rs = mysql_fetch_assoc($result)){
		$ordGrandTotal = ($rs['ordTotal']+$rs['ordStateTax']+$rs['ordCountryTax']+$rs['ordHSTTax']+$rs['ordShipping']+$rs['ordHandling'])-$rs['ordDiscount'];
		$thename = xmlstrip(trim($rs['ordName']));
		if($thename != ''){
			if(strstr($thename,' ')){
				$namearr = split(' ',$thename,2);
				$firstname = $namearr[0];
				$lastname = $namearr[1];
			}else{
				$firstname = '';
				$lastname = $thename;
			}
		}
		print "<DATA TABLE='orders' ORDERID='" . $rs['ordID'] . "' OCUSTOMERID='" . $rs['ordID'] . "' ODATE='" . date($admindatestr, strtotime($rs['ordDate'])) . "' ORDERAMOUNT='" . twodp($ordGrandTotal) . "' OFIRSTNAME='" . $firstname . "' OLASTNAME='" . $lastname . "' OEMAIL='" . xmlstrip($rs['ordEmail']) . "' OADDRESS='" . xmlstrip($rs['ordAddress'] . (trim($rs['ordAddress2']) != '' ? ', ' . $rs['ordAddress2'] : '')) . "' OCITY='" . xmlstrip($rs['ordCity']) . "' OPOSTCODE='" . xmlstrip($rs['ordZip']) . "' OSTATE='" . xmlstrip($rs['ordState']) . "' OCOUNTRY='" . xmlstrip($rs['ordCountry']) . "' OPHONE='" . substr(xmlstrip(str_replace(array(' ','.','-',')','('), '', $rs['ordPhone'])), -10) . "' OFAX='' OCOMPANY='" . (@$extra1iscompany==TRUE ? xmlstrip($rs['ordExtra1']) : '') . "' OCARDTYPE='' ";
		if(@$dumpccnumber){
			if($sslok==FALSE){
				print "OCARDNO='No SSL' OCARDNAME='No SSL' OCARDEXPIRES='No SSL' OCARDADDRESS='No SSL' ";
			}else{
				$result2 = mysql_query("SELECT ordCNum FROM orders WHERE ordID=" . $rs["ordID"]) or print(mysql_error());
				$rs2 = mysql_fetch_array($result2);
				$ordCNum = $rs2["ordCNum"];
				$encryptmethod = strtolower(@$encryptmethod);
				if(trim($ordCNum)=="" || is_null($ordCNum)){
					print "OCARDNO='' OCARDNAME='' OCARDEXPIRES='' OCARDADDRESS='' ";
				}elseif($encryptmethod=="mcrypt"){
					if(@$mcryptalg == "") $mcryptalg = MCRYPT_BLOWFISH;
					$td = mcrypt_module_open($mcryptalg, '', 'cbc', '');
					$thekey = @$ccencryptkey;
					$thekey = substr($thekey, 0, mcrypt_enc_get_key_size($td));
					$cnumarr = explode(" ", $ordCNum);
					$iv = @$cnumarr[0];
					$iv = @pack("H" . strlen($iv), $iv);
					$ordCNum = @pack("H" . strlen(@$cnumarr[1]), @$cnumarr[1]);
					mcrypt_generic_init($td, $thekey, $iv);
					$cnumarr = explode("&", mdecrypt_generic($td, $ordCNum));
					mcrypt_generic_deinit($td);
					mcrypt_module_close($td);
					if(is_array($cnumarr)){
						print "OCARDNO='" . $cnumarr[0] . "' OCARDNAME='" . $cnumarr[3] . "' OCARDEXPIRES='" . $cnumarr[1] . "' OCARDADDRESS='" . $rs["ordAddress"] . (trim($rs["ordAddress2"]) != '' ? ', ' . $rs["ordAddress2"] : '') . "' ";
					}else
						print "OCARDNO='' OCARDNAME='' OCARDEXPIRES='' OCARDADDRESS='' ";
				}elseif($encryptmethod=="none"){
					$cnumarr = explode("&",$ordCNum);
					if(is_array($cnumarr)){
						print "OCARDNO='" . $cnumarr[0] . "' OCARDNAME='" . $cnumarr[3] . "' OCARDEXPIRES='" . $cnumarr[1] . "' OCARDADDRESS='" . $rs["ordAddress"] . (trim($rs["ordAddress2"]) != '' ? ', ' . $rs["ordAddress2"] : '') . "' ";
					}else
						print "OCARDNO='' OCARDNAME='' OCARDEXPIRES='' OCARDADDRESS='' ";
				}
				mysql_free_result($result2);
			}
		}
		print "OPROCESSED='' OCOMMENT='" . xmlstrip($rs['ordAddInfo']) . "' OTAX='" . twodp($rs['ordStateTax']+$rs['ordCountryTax']+$rs['ordHSTTax']) . "' OPROMISEDSHIPDATE='' OSHIPPEDDATE='' OSHIPMETHOD='0' OSHIPCOST='" . twodp($rs['ordShipping']) . "' ";
		print "OSHIPNAME='" . xmlstrip($rs['ordShipName']) . "' OSHIPCOMPANY='' OSHIPEMAIL='' OSHIPMETHODTYPE='" . xmlstrip($rs['ordShipType']) . "' OSHIPADDRESS='" . xmlstrip($rs['ordShipAddress'] . (trim($rs['ordShipAddress2']) != '' ? ', ' . $rs['ordShipAddress2'] : '')) . "' OSHIPTOWN='" . xmlstrip($rs['ordShipCity']) . "' OSHIPZIP='" . xmlstrip($rs['ordShipZip']) . "' OSHIPCOUNTRY='" . xmlstrip($rs['ordShipCountry']) . "' OSHIPSTATE='" . xmlstrip($rs['ordShipState']) . "' ";
		print "OPAYMETHOD='" . $rs['ordPayProvider'] . "' OTHER1='" . (@$extra1iscompany==TRUE ? '' : xmlstrip($rs['ordExtra1'])) . "' OTHER2='" . xmlstrip($rs['ordExtra2']) . "' OTIME='' OAUTHORIZATION='' OERRORS='' ODISCOUNT='" . twodp($rs['ordDiscount']) . "' OSTATUS='" . xmlstrip($rs['statPrivate']) . "' OAFFID='' ODUALTOTAL='0' ODUALTAXES='0' ODUALSHIPPING='0' ODUALDISCOUNT='0' OHANDLING='" . twodp($rs['ordHandling']) . "' COUPON='" . xmlstrip(strip_tags($rs['ordDiscountText'])) . "' COUPONDISCOUNT='0' COUPONDISCOUNTDUAL='0' GIFTCERTIFICATE='' GIFTAMOUNTUSED='0' GIFTAMOUNTUSEDDUAL='0' CANCELED='" . ($rs['ordStatus']<2 ? "True" : "False") . "' />\r\n";
	}
	print "</DATABASE>\r\n";
}else{
	$sSQL2="SELECT statID,statPrivate FROM orderstatus";
	$result = mysql_query($sSQL2) or print(mysql_error());
	while($rs = mysql_fetch_assoc($result)){
		$allstatus[$rs['statID']]=$rs['statPrivate'];
	}
	if($hasdetails)
		$sSQL2 = "SELECT ordID,ordName,ordAddress,ordAddress2,ordCity,ordState,ordZip,ordCountry,ordEmail,ordPhone,ordExtra1,ordExtra2,ordShipExtra1,ordShipExtra2,ordCheckoutExtra1,ordCheckoutExtra2,ordShipName,ordShipAddress,ordShipAddress2,ordShipCity,ordShipState,ordShipZip,ordShipCountry,ordShipPhone,payProvName,ordAuthNumber,ordTotal,ordDate,ordStateTax,ordCountryTax,ordHSTTax,ordShipping,ordHandling,ordDiscount,ordAddInfo,ordShipType,ordStatus,cartProdId,cartProdName,cartProdPrice,cartQuantity,cartID FROM cart LEFT JOIN orders ON cart.cartOrderId=orders.ordID LEFT JOIN payprovider ON payprovider.payProvID=orders.ordPayProvider";
	else
		$sSQL2 = "SELECT ordID,ordName,ordAddress,ordAddress2,ordCity,ordState,ordZip,ordCountry,ordEmail,ordPhone,ordExtra1,ordExtra2,ordShipExtra1,ordShipExtra2,ordCheckoutExtra1,ordCheckoutExtra2,ordShipName,ordShipAddress,ordShipAddress2,ordShipCity,ordShipState,ordShipZip,ordShipCountry,ordShipPhone,payProvName,ordAuthNumber,ordTotal,ordDate,ordStateTax,ordCountryTax,ordHSTTax,ordShipping,ordHandling,ordDiscount,ordAddInfo,ordShipType,ordStatus FROM orders LEFT JOIN payprovider ON payprovider.payProvID=orders.ordPayProvider";
	$sSQL2 .= getsearchparams();
	$result = mysql_query($sSQL2) or print(mysql_error());
	print '"OrderID",';
	if(@$extraorderfield1 != '') print '"' . str_replace('"','""',$extraorderfield1) . '",';
	print '"CustomerName","Address",';
	if(@$useaddressline2==TRUE) print '"Address2",';
	print '"City","State","Zip","Country","Email","Phone",';
	if(@$extraorderfield2 != '') print '"' . str_replace('"','""',$extraorderfield2) . '",';
	if(@$extraorderfield1 != '') print '"' . str_replace('"','""',$extraorderfield1) . '",';
	print '"ShipName","ShipAddress",';
	if(@$useaddressline2==TRUE) print '"ShipAddress2",';
	print '"ShipCity","ShipState","ShipZip","ShipCountry","ShipPhone",';
	if(@$extraorderfield2 != '') print '"' . str_replace('"','""',$extraorderfield2) . '",';
	print '"PaymentMethod","AuthNumber","Total","Date","StateTax","CountryTax",';
	if(@$canadataxsystem==true) print '"HST",';
	print '"Shipping","Handling","Discounts","AddInfo","ShippingMethod","Status"';
	if(@$dumpccnumber) print ',"Card Number","Expiry Date","CVV Code","Issue Number"';
	if($hasdetails) print ',"ProductID","ProductName","ProductPrice","Quantity","Options"';
	print "\r\n";
	while($rs = mysql_fetch_assoc($result)){
			print $rs["ordID"] . ",";
			if(@$extraorderfield1 != '') print '"' . str_replace('"','""',$rs["ordExtra1"]) . '",';
			print '"' . str_replace('"','""',$rs["ordName"]) . '",';
			print '"' . str_replace('"','""',$rs["ordAddress"]) . '",';
			if(@$useaddressline2==TRUE) print '"' . str_replace('"','""',$rs["ordAddress2"]) . '",';
			print '"' . str_replace('"','""',$rs["ordCity"]) . '",';
			print '"' . str_replace('"','""',$rs["ordState"]) . '",';
			print '"' . str_replace('"','""',$rs["ordZip"]) . '",';
			print '"' . str_replace('"','""',$rs["ordCountry"]) . '",';
			print '"' . str_replace('"','""',$rs["ordEmail"]) . '",';
			print '"' . str_replace('"','""',$rs["ordPhone"]) . '",';
			if(@$extraorderfield2 != '') print '"' . str_replace('"','""',$rs["ordExtra2"]) . '",';
			if(@$extraorderfield1 != '') print '"' . str_replace('"','""',$rs["ordShipExtra1"]) . '",';
			print '"' . str_replace('"','""',$rs["ordShipName"]) . '",';
			print '"' . str_replace('"','""',$rs["ordShipAddress"]) . '",';
			if(@$useaddressline2==TRUE) print '"' . str_replace('"','""',$rs["ordShipAddress2"]) . '",';
			print '"' . str_replace('"','""',$rs["ordShipCity"]) . '",';
			print '"' . str_replace('"','""',$rs["ordShipState"]) . '",';
			print '"' . str_replace('"','""',$rs["ordShipZip"]) . '",';
			print '"' . str_replace('"','""',$rs["ordShipCountry"]) . '",';
			print '"' . str_replace('"','""',$rs["ordShipPhone"]) . '",';
			if(@$extraorderfield2 != '') print '"' . str_replace('"','""',$rs["ordShipExtra2"]) . '",';
			print '"' . str_replace('"','""',$rs["payProvName"]) . '",';
			print '"' . str_replace('"','""',$rs["ordAuthNumber"]) . '",';
			print '"' . $rs["ordTotal"] . '",';
			print '"' . $rs["ordDate"] . '",';
			print '"' . $rs["ordStateTax"] . '",';
			print '"' . $rs["ordCountryTax"] . '",';
			if(@$canadataxsystem==true) print '"' . $rs["ordHSTTax"] . '",';
			print '"' . $rs["ordShipping"] . '",';
			print '"' . $rs["ordHandling"] . '",';
			print '"' . $rs["ordDiscount"] . '",';
			print '"' . str_replace('"','""',$rs["ordAddInfo"]) . '",';
			print '"' . str_replace('"','""',$rs["ordShipType"]) . '",';
			print '"' . str_replace('"','""',@$allstatus[$rs["ordStatus"]]) . '"';
			if(@$dumpccnumber){
				if($sslok==FALSE){
					print ",No SSL,No SSL,No SSL,No SSL";
				}else{
					$result2 = mysql_query("SELECT ordCNum FROM orders WHERE ordID=" . $rs["ordID"]) or print(mysql_error());
					$rs2 = mysql_fetch_array($result2);
					$ordCNum = $rs2["ordCNum"];
					$encryptmethod = strtolower(@$encryptmethod);
					if(trim($ordCNum)=="" || is_null($ordCNum)){
						print ',"(no data)","","",""';
					}elseif($encryptmethod=="mcrypt"){
						if(@$mcryptalg == "") $mcryptalg = MCRYPT_BLOWFISH;
						$td = mcrypt_module_open($mcryptalg, '', 'cbc', '');
						$thekey = @$ccencryptkey;
						$thekey = substr($thekey, 0, mcrypt_enc_get_key_size($td));
						$cnumarr = explode(" ", $ordCNum);
						$iv = @$cnumarr[0];
						$iv = @pack("H" . strlen($iv), $iv);
						$ordCNum = @pack("H" . strlen(@$cnumarr[1]), @$cnumarr[1]);
						mcrypt_generic_init($td, $thekey, $iv);
						$cnumarr = explode("&", mdecrypt_generic($td, $ordCNum));
						mcrypt_generic_deinit($td);
						mcrypt_module_close($td);
						if(is_array($cnumarr)){
							print ',"""' . $cnumarr[0] . '"""';
							print ',"""' . @$cnumarr[1] . '"""';
							print ',"' . @$cnumarr[2] . '"';
							print ',"' . @$cnumarr[3] . '"';
						}else
							print ',"(no data)","","",""';
					}elseif($encryptmethod=="none"){
						$cnumarr = explode("&",$ordCNum);
						if(is_array($cnumarr)){
							print ',"""' . $cnumarr[0] . '"""';
							print ',"""' . @$cnumarr[1] . '"""';
							print ',"' . @$cnumarr[2] . '"';
							print ',"' . @$cnumarr[3] . '"';
						}else
							print ',"(no data)","","",""';
					}
					mysql_free_result($result2);
				}
			}
			if($hasdetails){
				$theOptions = "";
				$thePriceDiff = 0;
				$result2 = mysql_query("SELECT coPriceDiff,coOptGroup,coCartOption FROM cartoptions WHERE coCartID=" . $rs["cartID"]) or print(mysql_error());
				while($rs2 = mysql_fetch_assoc($result2)){
					$theOptions .= "," . '"' . str_replace('"','""',$rs2["coOptGroup"]) . " - " . str_replace('"','""',$rs2["coCartOption"]) . '"';
					$thePriceDiff += $rs2["coPriceDiff"];
				}
				print ',"' . str_replace('"','""',$rs["cartProdId"]) . '"';
				print ',"' . str_replace('"','""',$rs["cartProdName"]) . '"';
				print ',' . ($rs["cartProdPrice"] + $thePriceDiff);
				print ',' . $rs["cartQuantity"];
				print $theOptions;
				mysql_free_result($result2);
			}
			print "\r\n";
	}
}
?>