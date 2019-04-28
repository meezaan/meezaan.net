<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
$iNumOfPages = 0;
if(! is_numeric(@$_GET["pg"]))
	$CurPage = 1;
else
	$CurPage = (int)(@$_GET["pg"]);
if(is_numeric(@$_GET["id"])) $catid = (int)(@$_GET["id"]); else $catid = "0";
if(is_numeric(@$_GET["cat"])) $catid = (int)(@$_GET["cat"]);
if(@$explicitid != "" && is_numeric(@$explicitid)) $catid=@$explicitid;
$WSP = "";
$OWSP = "";
$TWSP = "pPrice";
$sectionurl='products.php';
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
function writepagebar($CurPage, $iNumPages){
	global $catid,$tid,$xxNext,$xxPrev,$sectionurl,$explicitid;
	$sLink = '<a href="'.$sectionurl.'?';
	foreach(@$_GET as $objQS => $objValue)
		if($objQS!='cat' && $objQS!='id' AND $objQS!='pg') $sLink .= $objQS . '=' . $objValue . '&';
	if($catid != '0' && @$explicitid=='') $sLink .= 'cat=' . $catid . '&pg='; else $sLink .= 'pg=';
	$startPage = max(1,round(floor((double)$CurPage/10.0)*10));
	$endPage = min($iNumPages,round(floor((double)$CurPage/10.0)*10)+10);
	if($CurPage > 1)
		$sStr = $sLink . '1' . '"><strong><font face="Verdana">&laquo;</font></strong></a> ' . $sLink . ($CurPage-1) . '">'.$xxPrev.'</a> | ';
	else
		$sStr = '<strong><font face="Verdana">&laquo;</font></strong> '.$xxPrev.' | ';
	for($i=$startPage;$i <= $endPage; $i++){
		if($i==$CurPage)
			$sStr .= '<span class="currpage">' . $i . '</span> | ';
		else{
			$sStr .= $sLink . $i . '">';
			if($i==$startPage && $i > 1) $sStr .= '...';
			$sStr .= $i;
			if($i==$endPage && $i < $iNumPages) $sStr .= '...';
			$sStr .= '</a> | ';
		}
	}
	if($CurPage < $iNumPages)
		$sStr .= $sLink . ($CurPage+1) . '">'.$xxNext.'</a> ' . $sLink . $iNumPages . '"><strong><font face="Verdana">&raquo;</font></strong></a>';
	else
		$sStr .= ' '.$xxNext.' <strong><font face="Verdana">&raquo;</font></strong>';
	return(str_replace(array('&pg=1"','?pg=1"'),'"',$sStr));
}
$alreadygotadmin = getadminsettings();
if(@$orprodsperpage != '') $adminProdsPerPage=$orprodsperpage;
checkCurrencyRates($currConvUser,$currConvPw,$currLastUpdate,$currRate1,$currSymbol1,$currRate2,$currSymbol2,$currRate3,$currSymbol3);
$tslist = "";
$thetopts = $catid;
$topsectionids = $catid;
$isrootsection=FALSE;
$sectiondisabled=FALSE;
if(@$_SESSION["clientLoginLevel"] != "") $minloglevel=$_SESSION["clientLoginLevel"]; else $minloglevel=0;
for($index=0; $index <= 10; $index++){
	if($thetopts==0){
		$tslist = '<a href="categories.php">' . $xxHome . "</a> " . $tslist;
		break;
	}elseif($index==10){
		$tslist = "<strong>Loop</strong>" . $tslist;
	}else{
		$sSQL = "SELECT sectionID,topSection,".getlangid("sectionName",256).",rootSection,sectionDisabled,sectionurl FROM sections WHERE sectionID=" . $thetopts;
		$result2 = mysql_query($sSQL) or print(mysql_error());
		if(mysql_num_rows($result2) > 0){
			$rs2 = mysql_fetch_assoc($result2);
			if($rs2['sectionID']==(int)$catid)$isrootsection = ($rs2['rootSection']==1);
			if($rs2['sectionDisabled']>$minloglevel) $catid=-1;
			if($rs2['sectionID']==(int)$catid && $isrootsection){
				$tslist = ' &raquo; ' . $rs2[getlangid('sectionName',256)] . $tslist;
				if(@$explicitid != '' && trim($rs2['sectionurl']) != '') $sectionurl = trim($rs2['sectionurl']);
				if(@$explicitid=='' && trim($rs2['sectionurl']) != '' && @$redirecttostatic==TRUE){
					ob_end_clean();
					header('HTTP/1.1 301 Moved Permanently');
					if($rs2['sectionurl']{0}=='/')$thelocation='http://'.$_SERVER['HTTP_HOST'].$rs2['sectionurl'];elseif(substr(strtolower($rs2['sectionurl']),0,7) == 'http://')$thelocation=$rs2['sectionurl'];else $thelocation='http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'],'/')).'/'.$rs2['sectionurl'];
					header('Location: '.$thelocation);
					exit;
				}
			}elseif(trim($rs2['sectionurl']) != ''){
				$tslist = ' &raquo; <a href="' . $rs2['sectionurl'] . '">' . $rs2[getlangid('sectionName',256)] . '</a>' . $tslist;
				if(@$explicitid != '' && $rs2['sectionID']==(int)$catid) $sectionurl = trim($rs2['sectionurl']);
			}elseif($rs2['rootSection']==1)
				$tslist = ' &raquo; <a href="products.php?cat=' . $rs2['sectionID'] . '">' . $rs2[getlangid('sectionName',256)] . '</a>' . $tslist;
			else
				$tslist = ' &raquo; <a href="categories.php?cat=' . $rs2['sectionID'] . '">' . $rs2[getlangid('sectionName',256)] . '</a>' . $tslist;
			$thetopts = $rs2['topSection'];
			$topsectionids .= ',' . $thetopts;
		}else{
			$tslist = 'Top Section Deleted ' . $tslist;
			break;
		}
		mysql_free_result($result2);
	}
}
if(! $isrootsection && @$xxAlProd != '') $tslist .= ' &raquo; ' . $xxAlProd;
if($catid=="0"){
	$disabledsections = "";
	$addcomma="";
	$result2 = mysql_query("SELECT sectionID FROM sections WHERE sectionDisabled>".$minloglevel) or print(mysql_error());
	while($rs2 = mysql_fetch_assoc($result2)){
		$disabledsections .= $addcomma . $rs2["sectionID"];
		$addcomma=",";
	}
	mysql_free_result($result2);
	$sSQL = "SELECT products.pId FROM products WHERE pDisplay<>0";
	if($disabledsections != "")
		$sSQL .= " AND NOT (products.pSection IN (" . getsectionids($disabledsections, TRUE) . "))";
}else{
	$sectionids = getsectionids($catid, FALSE);
	$sSQL = "SELECT DISTINCT products.pId FROM products LEFT JOIN multisections ON products.pId=multisections.pId WHERE pDisplay<>0 AND (products.pSection IN (" . $sectionids . ") OR multisections.pSection IN (" . $sectionids . "))";
}
if($useStockManagement && @$noshowoutofstock==TRUE) $sSQL .= ' AND (pInStock>0 OR pStockByOpts<>0)';
if(@$_POST['sortby'] != '') $_SESSION['sortby']=(int)$_POST['sortby'];
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
if(strpos($sSQL,"DISTINCT"))
	$tmpSQL = preg_replace("/DISTINCT products.pId/","COUNT(DISTINCT products.pId) AS bar",$sSQL, 1);
else
	$tmpSQL = preg_replace("/products.pId/","COUNT(*) AS bar",$sSQL, 1);
$allprods = mysql_query($tmpSQL) or print(mysql_error());
$iNumOfPages = ceil(mysql_result($allprods,0,"bar")/$adminProdsPerPage);
mysql_free_result($allprods);
$sSQL .=  $sSortBy . " LIMIT " . ($adminProdsPerPage*($CurPage-1)) . ", $adminProdsPerPage";
$allprods = mysql_query($sSQL) or print(mysql_error());
if(mysql_num_rows($allprods) > 0){
	$prodlist = "";
	$addcomma="";
	while($rs = mysql_fetch_array($allprods)){
		$prodlist .= $addcomma . "'" . $rs["pId"] . "'";
		$addcomma=",";
	}
	mysql_free_result($allprods);
	$sSQL = "SELECT pId,".getlangid("pName",1).",pImage,".$WSP."pPrice,pListPrice,pSection,pSell,pStockByOpts,pStaticPage,pInStock,pExemptions,pLargeImage,pTax,".getlangid("pDescription",2).",".getlangid("pLongDescription",4)." FROM products WHERE pId IN (" . $prodlist . ")" . $sSortBy;
	$allprods = mysql_query($sSQL) or print(mysql_error());
}
$Count = 0;
$_SESSION["frompage"] = @$_SERVER['PHP_SELF'] . (trim(@$_SERVER['QUERY_STRING'])!= "" ? "?" : "") . @$_SERVER['QUERY_STRING'];
?>
      <table border="0" cellspacing="<?php print $maintablespacing; ?>" cellpadding="<?php print $maintablepadding; ?>" width="<?php print $maintablewidth; ?>" bgcolor="<?php print $maintablebg; ?>" align="center">
        <tr> 
          <td colspan="3" width="100%">
<?php
if(@$useproductbodyformat==3)
	include "./vsadmin/inc/incproductbody3.php";
elseif(@$useproductbodyformat==2)
	include "./vsadmin/inc/incproductbody2.php";
else
	include "./vsadmin/inc/incproductbody.php"; ?>
		  </td>
        </tr>
      </table>
