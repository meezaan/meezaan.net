<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(trim(@$explicitid) != "") $prodid=trim($explicitid); else $prodid=trim(@$_GET["prod"]);
$prodlist = "'" . mysql_escape_string($prodid) . "'";
$WSP = "";
$OWSP = "";
if(@$_SESSION["clientUser"] != ""){
	if(($_SESSION["clientActions"] & 8) == 8){
		$WSP = "pWholesalePrice AS ";
		if(@$wholesaleoptionpricediff==TRUE) $OWSP = 'optWholesalePriceDiff AS ';
	}
	if(($_SESSION["clientActions"] & 16) == 16){
		$WSP = $_SESSION["clientPercentDiscount"] . "*pPrice AS ";
		if(@$wholesaleoptionpricediff==TRUE) $OWSP = $_SESSION["clientPercentDiscount"] . '*optPriceDiff AS ';
	}
}
$Count=0;
$previousid="";
$nextid="";
function writepreviousnextlinks(){
	global $xxPrev,$previousid,$previousidname,$previousidstatic,$xxNext,$nextid,$nextidname,$nextidstatic;
	if(trim(@$_GET['cat']) != '' && is_numeric(@$_GET['cat']) && trim(@$_GET['cat']) != '0') $thecatid = $_GET['cat']; else $thecatid='';
	if($previousid != ""){
		if($previousidstatic)
			print '<a href="' . cleanforurl($previousidname) . '.php' . ($thecatid != '' ? '?cat=' . $thecatid : '') . '">';
		else
			print '<a href="proddetail.php?prod=' . $previousid . ($thecatid != '' ? '&cat=' . $thecatid : '') . '">';
	}
	print '<strong>&laquo; ' . $xxPrev . '</strong>';
	if($previousid != "") print '</a>';
	print ' | ';
	if($nextid != ""){
		if($nextidstatic)
			print '<a href="' . cleanforurl($nextidname) . '.php' . ($thecatid != '' ? '?cat=' . $thecatid : '') . '">';
		else
			print '<a href="proddetail.php?prod=' . $nextid . ($thecatid != '' ? '&cat=' . $thecatid : '') . '">';
	}
	print '<strong>' . $xxNext . ' &raquo;</strong>';
	if($nextid != "") print '</a>';
}
$alreadygotadmin = getadminsettings();
checkCurrencyRates($currConvUser,$currConvPw,$currLastUpdate,$currRate1,$currSymbol1,$currRate2,$currSymbol2,$currRate3,$currSymbol3);
$_SESSION["frompage"] = @$_SERVER['PHP_SELF'] . (trim(@$_SERVER['QUERY_STRING'])!= "" ? "?" : "") . @$_SERVER['QUERY_STRING'];
$sSQL = "SELECT pId,".getlangid("pName",1).",".getlangid("pDescription",2).",pImage,".$WSP."pPrice,pSection,pListPrice,pSell,pStockByOpts,pStaticPage,pInStock,pExemptions,".(@$detailslink != "" ? "'' AS " : "")."pLargeImage,pTax,".getlangid("pLongDescription",4)." FROM products WHERE pDisplay<>0 AND pId='" . mysql_escape_string($prodid) . "'";
$result = mysql_query($sSQL) or print(mysql_error());
if(! ($rs = mysql_fetch_array($result))){
	print '<p align="center">&nbsp;<br />Sorry, this product is not currently available.<br />&nbsp;</p>';
}else{
if(trim(@$_GET['prod']) != '' && $rs['pStaticPage'] != 0 && @$redirecttostatic==TRUE){
	ob_end_clean();
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'],'/')).'/'. cleanforurl($rs[getlangid('pName',1)]) . '.php');
	exit;
}
$tslist = '';
$catid = $rs['pSection'];
if(trim(@$_GET['cat']) != '' && is_numeric(@$_GET['cat']) && trim(@$_GET['cat']) != '0') $catid = $_GET['cat'];
$thetopts = $catid;
$topsectionids = $catid;
$isrootsection=FALSE;
for($index=0; $index <= 10; $index++){
	if($thetopts==0){
		if($catid=="0")
			$tslist = $xxHome . " " . $tslist;
		else
			$tslist = '<a href="categories.php">' . $xxHome . "</a> " . $tslist;
		break;
	}elseif($index==10){
		$tslist = "<strong>Loop</strong>" . $tslist;
	}else{
		$sSQL = "SELECT sectionID,topSection,".getlangid("sectionName",256).",rootSection,sectionurl FROM sections WHERE sectionID=" . $thetopts;
		$result2 = mysql_query($sSQL) or print(mysql_error());
		if(mysql_num_rows($result2) > 0){
			$rs2 = mysql_fetch_assoc($result2);
			if($rs2["sectionurl"] != "")
				$tslist = ' &raquo; <a href="' . $rs2["sectionurl"] . '">' . $rs2[getlangid("sectionName",256)] . "</a>" . $tslist;
			elseif($rs2["rootSection"]==1)
				$tslist = ' &raquo; <a href="products.php?cat=' . $rs2["sectionID"] . '">' . $rs2[getlangid("sectionName",256)] . "</a>" . $tslist;
			else
				$tslist = ' &raquo; <a href="categories.php?cat=' . $rs2["sectionID"] . '">' . $rs2[getlangid("sectionName",256)] . "</a>" . $tslist;
			$thetopts = $rs2["topSection"];
			$topsectionids .= "," . $thetopts;
		}else{
			$tslist = "Top Section Deleted" . $tslist;
			break;
		}
		mysql_free_result($result2);
	}
}
$nextid="";
$previousid="";
$sectionids = getsectionids($catid, FALSE);
$sSQL = "SELECT products.pId,".getlangid('pName',1).",pStaticPage FROM products LEFT JOIN multisections ON products.pId=multisections.pId WHERE (products.pSection IN (" . $sectionids . ") OR multisections.pSection IN (" . $sectionids . "))" . (($useStockManagement && @$noshowoutofstock==TRUE) ? ' AND (pInStock>0 OR pStockByOpts<>0)' : '') . " AND pDisplay<>0 AND products.pId > '" . mysql_escape_string($prodid) . "' ORDER BY products.pId ASC LIMIT 1";
$result2 = mysql_query($sSQL) or print(mysql_error());
if($rs2=mysql_fetch_assoc($result2)){
	$nextid = urlencode($rs2['pId']);
	$nextidname = $rs2[getlangid('pName',1)];
	$nextidstatic = $rs2['pStaticPage'];
}
mysql_free_result($result2);
$sSQL = "SELECT products.pId,pName,pStaticPage FROM products LEFT JOIN multisections ON products.pId=multisections.pId WHERE (products.pSection IN (" . $sectionids . ") OR multisections.pSection IN (" . $sectionids . "))" . (($useStockManagement && @$noshowoutofstock==TRUE) ? ' AND (pInStock>0 OR pStockByOpts<>0)' : '') . " AND pDisplay<>0 AND products.pId < '" . mysql_escape_string($prodid) . "' ORDER BY products.pId DESC LIMIT 1";
$result2 = mysql_query($sSQL) or print(mysql_error());
if($rs2=mysql_fetch_assoc($result2)){
	$previousid = urlencode($rs2['pId']);
	$previousidname = $rs2[getlangid('pName',1)];
	$previousidstatic = $rs2['pStaticPage'];
}
mysql_free_result($result2);
$prodoptions="";
productdisplayscript(TRUE);
if(@$currencyseparator=="") $currencyseparator=" ";
if(@$perproducttaxrate==TRUE && ! is_null($rs['pTax'])) $thetax = $rs['pTax']; else $thetax = $countryTaxRate;
updatepricescript(TRUE,$thetax);
?>
      <table border="0" cellspacing="<?php print $maintablespacing?>" cellpadding="<?php print $maintablepadding?>" width="<?php print $maintablewidth?>" bgcolor="<?php print $maintablebg?>" align="center">
        <tr> 
          <td width="100%">
		    <form method="post" name="tForm<?php print $Count?>" action="cart.php" onsubmit="return formvalidator<?php print $Count?>(this)">
<?php if(! (@isset($showcategories) && @$showcategories==FALSE)){ ?>
			<table width="<?php print $innertablewidth?>" border="0" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
              <tr> 
                <td class="prodnavigation" colspan="3" align="left" valign="top"><strong><p class="prodnavigation"><?php print $tslist ?><br />
                  <img src="images/clearpixel.gif" width="300" height="8" alt="" /></p></strong></td>
                <td align="right" valign="top">&nbsp;<?php if(@$nobuyorcheckout != TRUE){ ?><a href="cart.php"><img src="images/checkout.gif" border="0" alt="<?php print $xxCOTxt?>" /></a><?php }else print '&nbsp;' ?></td>
              </tr>
			</table>
<?php }
		$alldiscounts = "";
		if(@$nowholesalediscounts==TRUE && @$_SESSION["clientUser"]!="")
			if((($_SESSION["clientActions"] & 8) == 8) || (($_SESSION["clientActions"] & 16) == 16)) $noshowdiscounts=TRUE;
		if(@$noshowdiscounts != TRUE){
			$sSQL = "SELECT DISTINCT ".getlangid("cpnName",1024)." FROM coupons LEFT OUTER JOIN cpnassign ON coupons.cpnID=cpnassign.cpaCpnID WHERE cpnNumAvail>0 AND cpnEndDate>='" . date("Y-m-d",time()) ."' AND cpnIsCoupon=0 AND ";
			$sSQL .= "((cpnSitewide=1 OR cpnSitewide=2) ";
			$sSQL .= "OR (cpnSitewide=0 AND cpaType=2 AND cpaAssignment='" . $rs["pId"] . "') ";
			$sSQL .= "OR ((cpnSitewide=0 OR cpnSitewide=3) AND cpaType=1 AND cpaAssignment IN ('" . str_replace(",","','",$topsectionids) . "')))";
			$result2 = mysql_query($sSQL) or print(mysql_error());
			while($rs2=mysql_fetch_assoc($result2))
				$alldiscounts .= $rs2[getlangid("cpnName",1024)] . "<br />";
			mysql_free_result($result2);
		}
		if(@$usedetailbodyformat==1 || @$usedetailbodyformat==""){ ?>
            <table width="<?php print $innertablewidth?>" border="0" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
              <tr> 
                <td width="100%" colspan="4"> 
<?php					if(@$showproductid==TRUE) print '<div class="detailid"><strong>' . $xxPrId . ':</strong> ' . $rs["pId"] . '</div>' ?><strong><div class="detailname"><?php print $rs[getlangid("pName",1)] . $xxDot;
						if($alldiscounts != "") print ' <font color="#FF0000"><span class="discountsapply">' . $xxDsApp . '</span></div></strong><font size="1"><div class="detaildiscounts">' . $alldiscounts . '</div></font></font>'; else print '</div></strong>';
						if(@$showinstock==TRUE){ if((int)$rs["pStockByOpts"]==0) print '<div class="detailinstock"><strong>' . $xxInStoc . ':</strong> ' . $rs["pInStock"] . '</div>'; } ?>
                </td>
              </tr>
              <tr> 
                <td width="100%" colspan="4" align="center" class="detailimage"> <?php
					if(! (trim($rs["pLargeImage"])=="" || is_null($rs["pLargeImage"]) || trim($rs["pLargeImage"])=="prodimages/")){ ?> 
						<img class="prodimage" src="<?php print $rs["pLargeImage"]?>" border="0" alt="<?php print str_replace('"','&nbsp;',strip_tags($rs[getlangid("pName",1)])) ?>" /> <?php
					}elseif(! (trim($rs["pImage"])=="" || is_null($rs["pImage"]) || trim($rs["pImage"])=="prodimages/")) { ?> 
						<img class="prodimage" src="<?php print $rs["pImage"]?>" border="0" alt="<?php print str_replace('"','&nbsp;',strip_tags($rs[getlangid("pName",1)])) ?>" /> <?php
					}else
						print "&nbsp;"; ?> 
                </td>
              </tr>
              <tr> 
                <td width="100%" colspan="4"> 
                  <p><?php $longdesc = trim($rs[getlangid("pLongDescription",4)]);
				if($longdesc != "")
					print '<div class="detaildescription">' . $longdesc . '</div>';
				elseif(trim($rs[getlangid("pDescription",2)]) != "")
					print '<div class="detaildescription">' . $rs[getlangid("pDescription",2)] . '</div>';
				else
					print "&nbsp;"; ?></p>
<?php
$optionshavestock=true;
if(is_array($prodoptions)){
	print '<div class="detailoptions" align="center"><table border="0" cellspacing="1" cellpadding="1">';
	$rowcounter=0;
	print displayproductoptions('<strong><span class="detailoption">','</span></strong>',$optdiff);
	$rs['pPrice'] += $optdiff;
	print "</table></div>";
}
?>              </td>
              </tr>
              <tr>
			    <td width="20%"><?php if(@$useemailfriend){ ?>
<p align="center"><a href="javascript:openEFWindow('<?php print urlencode($prodid)?>')"><strong><?php print $xxEmFrnd?></strong></a></p>
<?php } else { ?>
&nbsp;
<?php } ?></td><td width="60%" align="center" colspan="2">
			<?php	if(@$noprice==TRUE){
						print '&nbsp;';
					}else{
						if((double)$rs['pListPrice']!=0.0) print '<div class="detaillistprice">' . str_replace('%s', FormatEuroCurrency($rs['pListPrice']), $xxListPrice) . '</div>';
						if($rs['pPrice']==0 && @$pricezeromessage != '')
							print '<div class="detailprice">' . $pricezeromessage . '</div>';
						else{
							print '<div class="detailprice"><strong>' . $xxPrice . ':</strong> <span class="price" id="pricediv' . $Count . '" name="pricediv' . $Count . '">' . FormatEuroCurrency($rs["pPrice"]) . '</span> ';
							if(@$showtaxinclusive && ($rs["pExemptions"] & 2)!=2) printf($ssIncTax,'<span id="pricedivti' . $Count . '" name="pricedivti' . $Count . '">' . FormatEuroCurrency($rs["pPrice"]+($rs["pPrice"]*$thetax/100.0)) . '</span> ');
							print "</div>";
							$extracurr = "";
							if($currRate1!=0 && $currSymbol1!="") $extracurr = str_replace("%s",number_format($rs["pPrice"]*$currRate1,checkDPs($currSymbol1),".",","),$currFormat1) . $currencyseparator;
							if($currRate2!=0 && $currSymbol2!="") $extracurr .= str_replace("%s",number_format($rs["pPrice"]*$currRate2,checkDPs($currSymbol2),".",","),$currFormat2) . $currencyseparator;
							if($currRate3!=0 && $currSymbol3!="") $extracurr .= str_replace("%s",number_format($rs["pPrice"]*$currRate3,checkDPs($currSymbol3),".",","),$currFormat3) . "</strong>";
							if($extracurr!='') print '<div class="detailcurrency"><span class="extracurr" id="pricedivec' . $Count . '" name="pricedivec' . $Count . '">' . $extracurr . "</strong></span></div>";
						}
					} ?>
				</td><td width="20%" align="right">
<?php
if(@$nobuyorcheckout == TRUE)
	print "&nbsp;";
else{
	if($useStockManagement)
		if($rs["pStockByOpts"]!=0) $isInStock = $optionshavestock; else $isInStock = ((int)($rs["pInStock"]) > 0);
	else
		$isInStock = ($rs["pSell"] != 0);
	if($isInStock){
?>
<input type="hidden" name="id" value="<?php print $rs["pId"]?>" />
<input type="hidden" name="mode" value="add" />
<input type="hidden" name="frompage" value="<?php print @$_SERVER['PHP_SELF'] . (trim(@$_SERVER['QUERY_STRING'])!= "" ? "?" : "") . @$_SERVER['QUERY_STRING']?>" />
<?php	if(@$showquantondetail==TRUE) print '<input type="text" name="quant" size="2" maxlength="5" value="1" />&nbsp;';
		if(@$custombuybutton != "") print $custombuybutton; else print '<input align="middle" type="image" src="images/buy.gif" border="0" alt="'.$xxAddToC.'" />';
	}else{
		print "<strong>" . $xxOutStok . "</strong>";
	}
}			?></td>
            </tr>
<?php
if($previousid != "" || $nextid != ""){
	print '<tr><td align="center" colspan="4" class="pagenums"><p class="pagenums">&nbsp;<br />';
	writepreviousnextlinks();
	print '</p></td></tr>';
} ?>
            </table>
<?php }else{ // if($usedetailbodyformat==2) ?>
			<table width="<?php print $innertablewidth?>" border="0" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
              <tr> 
                <td width="30%" align="center" class="detailimage"> <?php
					if(! (trim($rs["pLargeImage"])=="" || is_null($rs["pLargeImage"]) || trim($rs["pLargeImage"])=="prodimages/")){ ?> 
						<img class="prodimage" src="<?php print $rs["pLargeImage"]?>" border="0" alt="<?php print str_replace('"','&nbsp;',strip_tags($rs[getlangid("pName",1)])) ?>" /> <?php
					}elseif(! (trim($rs["pImage"])=="" || is_null($rs["pImage"]) || trim($rs["pImage"])=="prodimages/")) { ?> 
						<img class="prodimage" src="<?php print $rs["pImage"]?>" border="0" alt="<?php print str_replace('"','&nbsp;',strip_tags($rs[getlangid("pName",1)])) ?>" /> <?php
					}else
						print "&nbsp;"; ?> 
                </td>
				<td>&nbsp;</td>
				<td width="70%" valign="top"> 
<?php			$optionshavestock=true;
				if(is_array($prodoptions)){
					$optionshtml = displayproductoptions('<span class="detailoption">','</span>',$optdiff);
					$rs['pPrice'] += $optdiff;
				}
				if(@$showproductid==TRUE) print '<div class="detailid"><strong>' . $xxPrId . ':</strong> ' . $rs["pId"] . '</div>' ?><strong><div class="detailname"><?php print $rs[getlangid("pName",1)] . $xxDot;
				if($alldiscounts != "") print ' <font color="#FF0000"><span class="discountsapply">' . $xxDsApp . '</span></font></div></strong><font size="1" color="#FF0000"><div class="detaildiscounts">' . $alldiscounts . '</div></font>'; else print '</div></strong>';
				if(@$showinstock==TRUE){ if((int)$rs["pStockByOpts"]==0) print '<div class="detailinstock"><strong>' . $xxInStoc . ':</strong> ' . $rs["pInStock"] . '</div>'; }
				print '<br />';
				$longdesc = trim($rs[getlangid("pLongDescription",4)]);
				if($longdesc != "")
					print '<div class="detaildescription">' . $longdesc . '</div>';
				elseif(trim($rs[getlangid("pDescription",2)]) != "")
					print '<div class="detaildescription">' . $rs[getlangid("pDescription",2)] . '</div>';
				if(@$noprice==TRUE){
					print '&nbsp;';
				}else{
					if((double)$rs['pListPrice']!=0.0) print '<div class="detaillistprice">' . str_replace('%s', FormatEuroCurrency($rs['pListPrice']), $xxListPrice) . '</div>';
					if($rs['pPrice']==0 && @$pricezeromessage != '')
						print '<div class="detailprice">' . $pricezeromessage . '</div>';
					else{
						print '<div class="detailprice"><strong>' . $xxPrice . ':</strong> <span class="price" id="pricediv' . $Count . '" name="pricediv' . $Count . '">' . FormatEuroCurrency($rs["pPrice"]) . '</span> ';
						if(@$showtaxinclusive && ($rs["pExemptions"] & 2)!=2) printf($ssIncTax,'<span id="pricedivti' . $Count . '" name="pricedivti' . $Count . '">' . FormatEuroCurrency($rs["pPrice"]+($rs["pPrice"]*$thetax/100.0)) . '</span> ');
						print "</div>";
						$extracurr = "";
						if($currRate1!=0 && $currSymbol1!="") $extracurr = str_replace("%s",number_format($rs["pPrice"]*$currRate1,checkDPs($currSymbol1),".",","),$currFormat1) . $currencyseparator;
						if($currRate2!=0 && $currSymbol2!="") $extracurr .= str_replace("%s",number_format($rs["pPrice"]*$currRate2,checkDPs($currSymbol2),".",","),$currFormat2) . $currencyseparator;
						if($currRate3!=0 && $currSymbol3!="") $extracurr .= str_replace("%s",number_format($rs["pPrice"]*$currRate3,checkDPs($currSymbol3),".",","),$currFormat3) . "</strong>";
						if($extracurr!='') print '<div class="detailcurrency"><span class="extracurr" id="pricedivec' . $Count . '" name="pricedivec' . $Count . '">' . $extracurr . "</strong></span></div>";
					}
					print '<hr width="80%">';
				}
if(is_array($prodoptions)){
	print '<div class="detailoptions" align="center"><table border="0" cellspacing="1" cellpadding="1" width="100%">';
	$rowcounter=0;
	print $optionshtml;
	if(@$nobuyorcheckout != true && (@$showquantondetail==TRUE || ! @isset($showquantondetail))){
?>
	<tr><td align="right"><?php print $xxQuant?>:</td><td align="left"><input type="text" name="quant" size="4" maxlength="5" value="1" /></td></tr>
<?php
	}
	print "</table></div>";
}else{
	if(@$nobuyorcheckout != true && (@$showquantondetail==TRUE || ! @isset($showquantondetail))){
?>
	<table border='0' cellspacing='1' cellpadding='1' width='100%'>
	<tr><td align="right"><?php print $xxQuant?>:</td><td><input type="text" name="quant" size="4" maxlength="5" value="1" /></td></tr>
	</table>
<?php
	}
}
?>
<p align="center">
<?php
if(@$nobuyorcheckout == TRUE)
	print "&nbsp;";
else{
	if($useStockManagement)
		if($rs["pStockByOpts"]!=0) $isInStock = $optionshavestock; else $isInStock = ((int)($rs["pInStock"]) > 0);
	else
		$isInStock = ($rs["pSell"] != 0);
	if($isInStock){
?>
<input type="hidden" name="id" value="<?php print $rs["pId"]?>" />
<input type="hidden" name="mode" value="add" />
<input type="hidden" name="frompage" value="<?php print @$_SERVER['PHP_SELF'] . (trim(@$_SERVER['QUERY_STRING'])!= "" ? "?" : "") . @$_SERVER['QUERY_STRING']?>" />
<?php	if(@$custombuybutton != "") print $custombuybutton; else print '<input type="image" src="images/buy.gif" border="0" alt="'.$xxAddToC.'" /><br />';
	}else{
		print "<strong>" . $xxOutStok . "</strong><br />";
	}
}
if($previousid != "" || $nextid != ""){
	print '</p><p class="pagenums" align="center">';
	writepreviousnextlinks();
	print '<br />';
} ?>
<hr width="80%"></p>
<?php if(@$useemailfriend){ ?>
<p align="center"><a href="javascript:openEFWindow('<?php print urlencode($prodid)?>')"><strong><?php print $xxEmFrnd?></strong></a></p>
<?php } ?>
</td>
            </tr>
            </table>
<?php } ?>
			</form>
          </td>
        </tr>
      </table>
<?php } // EOF ?>