<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
$prodoptions="";
productdisplayscript(@$noproductoptions!=TRUE); ?>
		<table class="products" width="<?php print $innertablewidth;?>" border="0" cellspacing="<?php print $innertablespacing;?>" cellpadding="<?php print $innertablepadding;?>" bgcolor="<?php print $innertablebg;?>">
<?php	if(! (@isset($showcategories) && @$showcategories==FALSE)){ ?>
			  <tr>
				<td class="prodnavigation" colspan="2" align="left"><strong><p class="prodnavigation"><?php print $tslist ?></p></strong></td>
				<td align="right">&nbsp;<?php if(@$nobuyorcheckout != TRUE){ ?><a href="cart.php"><img src="images/checkout.gif" border="0" alt="<?php print $xxCOTxt?>" /></a><?php }else print '&nbsp;' ?></td>
			  </tr>
<?php	}
if(@$nowholesalediscounts==TRUE && @$_SESSION["clientUser"]!="")
	if((($_SESSION["clientActions"] & 8) == 8) || (($_SESSION["clientActions"] & 16) == 16)) $noshowdiscounts=TRUE;
if(@$noshowdiscounts != TRUE){
	$sSQL = "SELECT DISTINCT ".getlangid("cpnName",1024)." FROM coupons LEFT OUTER JOIN cpnassign ON coupons.cpnID=cpnassign.cpaCpnID WHERE (";
	$addor = "";
	if($catid != "0"){
		$sSQL .= $addor . "((cpnSitewide=0 OR cpnSitewide=3) AND cpaType=1 AND cpaAssignment IN ('" . str_replace(",","','",$topsectionids) . "'))";
		$addor = " OR ";
	}
	$sSQL .= $addor . "(cpnSitewide=1 OR cpnSitewide=2)) AND cpnNumAvail>0 AND cpnEndDate>='" . date("Y-m-d",time()) ."' AND cpnIsCoupon=0 ORDER BY cpnID";
	$result2 = mysql_query($sSQL) or print(mysql_error());
	if(mysql_num_rows($result2) > 0){ ?>
			  <tr>
				<td align="left" colspan="3">
				  <p><strong><?php print $xxDsProd?></strong><br /><font color="#FF0000" size="1">
				  <?php	while($rs2=mysql_fetch_row($result2)){
							print $rs2[0] . "<br />";
						} ?></font></p>
				</td>
			  </tr>
<?php
	}
	mysql_free_result($result2);
}
?>
			  <tr>
				<td colspan="3" align="center" class="pagenums"><p class="pagenums"><?php
					If($iNumOfPages > 1 && @$pagebarattop==1) print writepagebar($CurPage, $iNumOfPages) . "<br />"; ?>
				  <img src="images/clearpixel.gif" width="300" height="8" alt="" /></p></td>
			  </tr>
<?php
	if(mysql_num_rows($allprods) == 0)
		print '<tr><td colspan="3" align="center"><p>'.$xxNoPrds.'</p></td></tr>';
	else while($rs = mysql_fetch_array($allprods)){
		if(@$forcedetailslink==TRUE || trim($rs[getlangid('pLongDescription',4)])!='' || ! (trim($rs['pLargeImage'])=='' || trim($rs['pLargeImage'])=='prodimages/')){
			if($rs['pStaticPage'] != 0){
				$startlink='<a href="' . cleanforurl($rs[getlangid('pName',1)]) . '.php' . (@$catid != '' && @$catid != '0' && $catid != $rs['pSection'] && @$nocatid != TRUE ? '?cat=' . $catid : '') . '">';
				$endlink='</a>';
			}elseif(@$detailslink != ''){
				$startlink=str_replace('%pid%', $rs['pId'], str_replace('%largeimage%', $rs['pLargeImage'], $detailslink));
				$endlink=@$detailsendlink;
			}else{
				$startlink='<a href="proddetail.php?prod=' . urlencode($rs['pId']) . (@$catid != '' && @$catid != '0' && $catid != $rs['pSection'] && @$nocatid != TRUE ? '&amp;cat=' . $catid : '') . '">';
				$endlink='</a>';
			}
		}else{
			$startlink='';
			$endlink='';
		}
		for($cpnindex=0; $cpnindex < $adminProdsPerPage; $cpnindex++) $aDiscSection[$cpnindex][0] = "";
		if(! $isrootsection){
			$thetopts = $rs["pSection"];
			$gotdiscsection = FALSE;
			for($cpnindex=0; $cpnindex < $adminProdsPerPage; $cpnindex++){
				if($aDiscSection[$cpnindex][0]==$thetopts){
					$gotdiscsection = TRUE;
					break;
				}elseif($aDiscSection[$cpnindex][0]=="")
					break;
			}
			$aDiscSection[$cpnindex][0] = $thetopts;
			if(! $gotdiscsection){
				$topcpnids = $thetopts;
				for($index=0; $index<= 10; $index++){
					if($thetopts==0)
						break;
					else{
						$sSQL = "SELECT topSection FROM sections WHERE sectionID=" . $thetopts;
						$result2 = mysql_query($sSQL) or print(mysql_error());
						if(mysql_num_rows($result2) > 0){
							$rs2 = mysql_fetch_assoc($result2);
							$thetopts = $rs2["topSection"];
							$topcpnids .= "," . $thetopts;
						}else
							break;
					}
				}
				$aDiscSection[$cpnindex][1] = $topcpnids;
			}else
				$topcpnids = $aDiscSection[$cpnindex][1];
		}
		$alldiscounts = "";
		if(@$noshowdiscounts != TRUE){
			$sSQL = "SELECT DISTINCT ".getlangid("cpnName",1024)." FROM coupons LEFT OUTER JOIN cpnassign ON coupons.cpnID=cpnassign.cpaCpnID WHERE (cpnSitewide=0 OR cpnSitewide=3) AND cpnNumAvail>0 AND cpnEndDate>='" . date("Y-m-d",time()) ."' AND cpnIsCoupon=0 AND ((cpaType=2 AND cpaAssignment='" . $rs["pId"] . "')";
			if(! $isrootsection) $sSQL .= " OR (cpaType=1 AND cpaAssignment IN ('" . str_replace(",","','",$topcpnids) . "') AND NOT cpaAssignment IN ('" . str_replace(",","','",$topsectionids) . "'))";
			$sSQL .= ") ORDER BY cpnID";
			$result2 = mysql_query($sSQL) or print(mysql_error());
			while($rs2=mysql_fetch_row($result2))
				$alldiscounts .= $rs2[0] . "<br />";
			mysql_free_result($result2);
		} ?>
              <tr> 
                <td width="26%" rowspan="3" align="center" class="prodimage">
				<?php
					if(trim($rs["pImage"])=="" || is_null($rs["pImage"]) || trim($rs["pImage"])=="prodimages/"){
						print "&nbsp;";
					}else{
						print $startlink . '<img class="prodimage" src="' . $rs["pImage"] . '" border="0" alt="' . str_replace('"','&quot;',strip_tags($rs[getlangid("pName",1)])) . '" />' . $endlink;
					}
				?>
                </td>
				<td width="59%">
<?php				if(@$showproductid==TRUE) print '<div class="prodid"><strong>' . $xxPrId . ':</strong> ' . $rs["pId"] . '</div>' ?><strong><div class="prodname"><?php print $rs[getlangid("pName",1)] . $xxDot;
					if($alldiscounts != '') print ' <font color="#FF0000"><span class="discountsapply">' . $xxDsApp . '</span></font></div></strong><font size="1" color="#FF0000"><div class="proddiscounts">' . $alldiscounts . '</div></font>'; else print '</div></strong>';
					if(@$showinstock==TRUE){ if((int)$rs["pStockByOpts"]==0) print '<div class="prodinstock"><strong>' . $xxInStoc . ':</strong> ' . $rs["pInStock"] . '</div>'; } ?>
                </td>
				<td width="15%" align="right" valign="top"><?php
            		if($startlink != "")
                		print "<p>" . $startlink . "<strong>".$xxPrDets."</strong></a>&nbsp;</p>";
                	else
                		print "&nbsp;";
              ?></td>
			  </tr><?php
	if(@$currencyseparator=="") $currencyseparator=" ";
	if(@$perproducttaxrate==TRUE && ! is_null($rs['pTax'])) $thetax = $rs['pTax']; else $thetax = $countryTaxRate;
	updatepricescript(@$noproductoptions!=TRUE,$thetax); ?>
	<form method="post" name="tForm<?php print $Count; ?>" action="cart.php" onsubmit="return formvalidator<?php print $Count; ?>(this)">
			  <tr>
			    <td colspan="2"><?php
	print '<div class="proddescription">';
	$shortdesc = $rs[getlangid('pDescription',2)];
	if(@$shortdescriptionlimit=='') print $shortdesc; else print substr($shortdesc, 0, $shortdescriptionlimit) . (strlen($shortdesc)>$shortdescriptionlimit ? '...' : '');
	print '</div>';
	$optionshavestock=true;
	if(is_array($prodoptions)){
		print '<div class="prodoptions"><table border="0" cellspacing="1" cellpadding="1" width="100%">';
		$rowcounter=0;
		print displayproductoptions('<strong><span class="prodoption">','</span></strong>',$optdiff);
		$rs['pPrice'] += $optdiff;
		print "</table></div>";
	}		  ?></td>
			  </tr>
			  <tr>
				<td width="59%" align="center"><?php
					if(@$noprice==TRUE){
						print '&nbsp;';
					}else{
						if((double)$rs['pListPrice']!=0.0) print '<div class="listprice">' . str_replace('%s', FormatEuroCurrency($rs['pListPrice']), $xxListPrice) . '</div>';
						if($rs["pPrice"]==0 && @$pricezeromessage != "")
							print '<div class="prodprice">' . $pricezeromessage . '</div>';
						else{
							print '<div class="prodprice"><strong>' . $xxPrice . ':</strong> <span class="price" id="pricediv' . $Count . '" name="pricediv' . $Count . '">' . FormatEuroCurrency($rs["pPrice"]) . '</span> ';
							if(@$showtaxinclusive && ($rs["pExemptions"] & 2)!=2) printf($ssIncTax,'<span id="pricedivti' . $Count . '" name="pricedivti' . $Count . '">' . FormatEuroCurrency($rs["pPrice"]+($rs["pPrice"]*$thetax/100.0)) . '</span> ');
							print "</div>";
							$extracurr = "";
							if($currRate1!=0 && $currSymbol1!="") $extracurr = str_replace("%s",number_format($rs["pPrice"]*$currRate1,checkDPs($currSymbol1),".",","),$currFormat1) . $currencyseparator;
							if($currRate2!=0 && $currSymbol2!="") $extracurr .= str_replace("%s",number_format($rs["pPrice"]*$currRate2,checkDPs($currSymbol2),".",","),$currFormat2) . $currencyseparator;
							if($currRate3!=0 && $currSymbol3!="") $extracurr .= str_replace("%s",number_format($rs["pPrice"]*$currRate3,checkDPs($currSymbol3),".",","),$currFormat3) . "</strong>";
							if($extracurr!='') print '<div class="prodcurrency"><span class="extracurr" id="pricedivec' . $Count . '" name="pricedivec' . $Count . '">' . $extracurr . "</strong></span></div>";
						}
					} ?>
                </td>
			    <td align="right" valign="bottom"><?php
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
<?php	if(@$showquantonproduct==TRUE) print '<input type="text" name="quant" size="2" maxlength="5" value="1" />&nbsp;';
		if(@$custombuybutton != "") print $custombuybutton; else print '<input align="middle" type="image" src="images/buy.gif" border="0" alt="'.$xxAddToC.'" />';
	}else{
		print "<strong>".$xxOutStok."</strong>";
	}
}			  ?></td>
			  </tr>
			</form>
<?php	if(@$noproductseparator!=TRUE){
			print '<tr><td colspan="3" class="prodseparator">' . (@$prodseparator != '' ? $prodseparator : '<hr class="prodseparator" width="70%" align="center">') . '</td></tr>';
		}
		$Count++;
	}
?>			  <tr>
				<td colspan="3" align="center" class="pagenums"><p class="pagenums"><?php
					if($iNumOfPages > 1 && @$nobottompagebar<>TRUE) print writepagebar($CurPage, $iNumOfPages); ?><br />
				  <img src="images/clearpixel.gif" width="300" height="1" alt="" /></p></td>
			  </tr>
			</table>