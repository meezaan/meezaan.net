<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protected under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$_SERVER['CONTENT_LENGTH'] != '' && $_SERVER['CONTENT_LENGTH'] > 10000) exit;
$WSP = "";
$OWSP = "";
$TWSP = "pPrice";
$cs = @$csstyleprefix;
$thesessionid = session_id();
if(@$_SESSION["clientUser"] != ""){
	if(($_SESSION["clientActions"] & 8) == 8){
		$WSP = "pWholesalePrice AS ";
		$TWSP = "pWholesalePrice";
		if(@$wholesaleoptionpricediff==TRUE) $OWSP = 'optWholesalePriceDiff AS ';
	}
	if(($_SESSION["clientActions"] & 16) == 16){
		$WSP = $_SESSION["clientPercentDiscount"] . "*pPrice AS ";
		$TWSP = $_SESSION["clientPercentDiscount"] . "*pPrice";
		if(@$wholesaleoptionpricediff==TRUE) $OWSP = $_SESSION["clientPercentDiscount"] . '*optPriceDiff AS ';
	}
}
if(@$crosssellcolumns==''){ if(@$productcolumns=='') $crosssellcolumns=3; else $crosssellcolumns=$productcolumns; }
if(@$crosssellrows=='') $crosssellrows=1;
$numberofproducts = $crosssellcolumns * $crosssellrows;
$productcolumns=$crosssellcolumns;
if(@$csnobuyorcheckout==TRUE) $nobuyorcheckout=TRUE;
if(@$csnoshowdiscounts==TRUE) $noshowdiscounts=TRUE;
if(@$csnoproductoptions==TRUE) $noproductoptions=TRUE;
if(! @isset($forcedetailslink)) $forcedetailslink=TRUE;
$iNumOfPages=1;
$showcategories=FALSE;
$isrootsection=TRUE;
if(! @isset($Count)) $Count=0; else $Count=($Count+$crosssellcolumns)-($Count % $crosssellcolumns);
$catid = '0';
if(@$_SESSION['sortby'] != '') $sortBy=(int)($_SESSION['sortby']);
if(@$sortBy==2)
	$sSortBy = ' ORDER BY products.pId';
elseif(@$sortBy==3)
	$sSortBy = ' ORDER BY '.$TWSP;
elseif(@$sortBy==4)
	$sSortBy = ' ORDER BY '.$TWSP.' DESC';
elseif(@$sortBy==5)
	$sSortBy = '';
elseif(@$sortBy==6)
	$sSortBy = ' ORDER BY pOrder';
elseif(@$sortBy==7)
	$sSortBy = ' ORDER BY pOrder DESC';
else
	$sSortBy = ' ORDER BY '.getlangid('pName',1);
if(@$prodlist == '') $prodlist='';
if(@$_POST['mode'] != 'checkout' && @$_POST['mode'] != 'add' && @$_POST['mode'] != 'go' && @$_POST['mode'] != 'paypalexpress1'){
	$alreadygotadmin = getadminsettings();
	$crosssellactionarr = split(',', @$crosssellaction);
	for($csindex=0; $csindex < count($crosssellactionarr); $csindex++){
		$crosssellaction=trim($crosssellactionarr[$csindex]);
		$addcomma=''; $relatedlist='';
		if($crosssellaction=='alsobought'){ // Those who bought what's in your cart also bought.
			if(@$csalsoboughttitle=='') $crossselltitle='Customers who bought these products also bought.'; else $crossselltitle=$csalsoboughttitle;
			if($prodlist==''){
				$addcomma='';
				$sSQL = "SELECT cartProdID FROM cart WHERE cartCompleted=0 AND cartSessionID='" . mysql_escape_string($thesessionid) . "'";
				$result = mysql_query($sSQL) or print(mysql_error());
				while($rs = mysql_fetch_array($result)){
					$prodlist .= $addcomma . "'" . mysql_escape_string($rs['cartProdID']) . "'";
					$addcomma=',';
				}
			}
			$addcomma=''; $sessionlist='';
			if($prodlist != ''){
				$sSQL = "SELECT cartSessionID,COUNT(cartSessionID),MAX(cartDateAdded) as maxdateadded FROM cart WHERE cartProdID IN (" . $prodlist . ") GROUP BY cartSessionID HAVING COUNT(cartSessionID) > 1 ORDER BY maxdateadded DESC LIMIT 0,100";
				// print sSQL . "<br>"
				$result = mysql_query($sSQL) or print(mysql_error());
				while($rs = mysql_fetch_array($result)){
					$sessionlist .= $addcomma . "'" . mysql_escape_string($rs['cartSessionID']) . "'";
					$addcomma=',';
				}
			}
			if($prodlist != '' && $sessionlist != ''){
				$sSQL = "SELECT cartProdID FROM cart INNER JOIN products ON cart.cartProdId=products.pID WHERE pDisplay<>0 AND cartSessionID IN (" . $sessionlist . ") AND NOT (cartProdID IN (" . $prodlist . ")) ORDER BY cartDateAdded DESC LIMIT 0," . $numberofproducts;
				// print sSQL . "<br>"
				$addcomma='';
				$relatedlist='';
				$result = mysql_query($sSQL) or print(mysql_error());
				while($rs = mysql_fetch_array($result)){
					$relatedlist .= $addcomma . "'" . mysql_escape_string($rs['cartProdID']) . "'";
					$addcomma=',';
				}
			}
		}elseif($crosssellaction=='recommended'){ // Recommended products (Needs v5.1)
			if(@$csrecommendedtitle=='') $crossselltitle='These products are our current recommendations for you.'; else $crossselltitle=$csrecommendedtitle;
			if($prodlist==''){
				$sSQL = "SELECT cartProdID FROM cart WHERE cartCompleted=0 AND cartSessionID='" . mysql_escape_string($thesessionid) . "'";
				$result = mysql_query($sSQL) or print(mysql_error());
				$addcomma='';
				while($rs = mysql_fetch_array($result)){
					$prodlist .= $addcomma . "'" . mysql_escape_string($rs['cartProdID']) . "'";
					$addcomma=',';
				}
			}
			$sSQL = 'SELECT pID FROM products WHERE pDisplay<>0 AND pRecommend<>0';
			if($prodlist != '') $sSQL .= ' AND NOT (pID IN (' . $prodlist . '))';
			$addcomma=''; $relatedlist='';
			$result = mysql_query($sSQL) or print(mysql_error());
			while($rs = mysql_fetch_array($result)){
				$relatedlist .= $addcomma . "'" . mysql_escape_string($rs['pID']) . "'";
				$addcomma=',';
			}
		}elseif($crosssellaction=='related'){ // Products recommended with this product (Would need v5.1)
			if(@$csrelatedtitle=='') $crossselltitle='These products are recommended with items in your cart.'; else $crossselltitle=$csrelatedtitle;
			if($prodlist==''){
				$addcomma='';
				$sSQL = "SELECT cartProdID FROM cart WHERE cartCompleted=0 AND cartSessionID='" . mysql_escape_string($thesessionid) . "'";
				$result = mysql_query($sSQL) or print(mysql_error());
				while($rs = mysql_fetch_array($result)){
					$prodlist .= $addcomma . "'" . mysql_escape_string($rs['cartProdID']) . "'";
					$addcomma=',';
				}
			}
			if($prodlist != ''){
				$sSQL = "SELECT rpRelProdID FROM relatedprods WHERE rpProdID IN (" . $prodlist . ") AND NOT (rpRelProdID IN (" . $prodlist . "))";
				$addcomma=''; $relatedlist='';
				$result = mysql_query($sSQL) or print(mysql_error());
				while($rs = mysql_fetch_array($result)){
						$relatedlist .= $addcomma . "'" . mysql_escape_string($rs['rpRelProdID']) . "'";
						$addcomma=',';
				}
			}
		}elseif($crosssellaction=='bestsellers'){ // Top X best sellers
			if(@$csbestsellerstitle=='') $crossselltitle='These are our current best sellers.'; else $crossselltitle=$csbestsellerstitle;
			$sSQL = "SELECT cartProdID,COUNT(cartProdID) AS pidcount FROM cart INNER JOIN products ON cart.cartProdID=products.pID WHERE pDisplay<>0 " . (@$crosssellsection != "" ? " AND pSection IN (" . $crosssellsection . ")" : '') . (@$crosssellnotsection != "" ? " AND NOT (pSection IN (" . $crosssellnotsection . "))" : '') . " GROUP BY cartProdID ORDER BY pidcount DESC LIMIT 0," . $numberofproducts;
			$relatedlist='';
			$result = mysql_query($sSQL) or print(mysql_error());
			while($rs = mysql_fetch_array($result)){
				$relatedlist .= $addcomma . "'" . mysql_escape_string($rs['cartProdID']) . "'";
				$addcomma=',';
			}
		}else
			if($crosssellaction != '') print '<p>Unrecognized crosssell action ' . $crosssellaction . '</p>';
		if($relatedlist != ''){
			$saveprodlist=$prodlist;
			$prodlist=$relatedlist;
			$sSQL = "SELECT pId," . getlangid('pName',1) . ",pImage," . $WSP . "pPrice,pListPrice,pSection,pSell,pStockByOpts,pStaticPage,pInStock,pExemptions,pLargeImage,pTax,'' AS " . getlangid('pDescription',2) . "," . getlangid('pLongDescription',4) . " FROM products WHERE pDisplay<>0 AND pId IN (" . $prodlist . ")";
			if($useStockManagement && @$noshowoutofstock==TRUE) $sSQL .= ' AND (pInStock>0 OR pStockByOpts<>0)';
			$sSQL .= $sSortBy;
			$allprods = mysql_query($sSQL) or print(mysql_error());
			if(mysql_num_rows($allprods) > 0){
				print '<p class="cstitle"><strong>' . $crossselltitle . '</strong></p>';
				include './vsadmin/inc/incproductbody2.php';
			}
			$prodlist=$saveprodlist;
		}
	}
}
?>