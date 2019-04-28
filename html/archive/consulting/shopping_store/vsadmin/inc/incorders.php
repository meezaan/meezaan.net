<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
$lisuccess=0;
if(@$dateadjust=="") $dateadjust=0;
if(@$dateformatstr == "") $dateformatstr = "m/d/Y";
$admindatestr="Y-m-d";
if(@$admindateformat=="") $admindateformat=0;
if($admindateformat==1)
	$admindatestr="m/d/Y";
elseif($admindateformat==2)
	$admindatestr="d/m/Y";
if(@$storesessionvalue=="") $storesessionvalue="virtualstore".time();
if(@$_GET["doedit"]=="true") $doedit=TRUE; else $doedit=FALSE;
$isinvoice=(@$_GET['invoice']=='true');
function editfunc($data,$col,$size){
	global $doedit;
	if($doedit) return('<input type="text" id="' . $col . '" name="' . $col . '" value="' . str_replace('"','&quot;',htmlspecialchars($data)) . '" size="' . $size . '">'); else return(htmlspecialchars($data));
}
function editnumeric($data,$col,$size){
	global $doedit;
	if($doedit) return('<input type="text" id="' . $col . '" name="' . $col . '" value="' . number_format(strip_tags($data),2,'.','') . '" size="' . $size . '">'); else return(FormatEuroCurrency(strip_tags($data)));
}
function decodehtmlentities($thestr){
	return(str_replace(array('&quot;','&nbsp;'), array('"', ' '), $thestr));
}
if(@$_SESSION["loggedon"] != $storesessionvalue && trim(@$_COOKIE["WRITECKL"])!=""){
	$sSQL="SELECT adminID FROM admin WHERE adminUser='" . mysql_escape_string(unstripslashes(trim(@$_COOKIE["WRITECKL"]))) . "' AND adminPassword='" . mysql_escape_string(unstripslashes(trim(@$_COOKIE["WRITECKP"]))) . "' AND adminID=1";
	$result = mysql_query($sSQL) or print(mysql_error());
	if(mysql_num_rows($result)>0)
		@$_SESSION["loggedon"] = $storesessionvalue;
	else
		$lisuccess=2;
	mysql_free_result($result);
}
if(($_SESSION["loggedon"] != $storesessionvalue && $lisuccess!=2) || @$disallowlogin==TRUE) exit;
if(@$htmlemails==TRUE) $emlNl = "<br />"; else $emlNl="\n";
function release_stock($smOrdId){
	global $stockManage;
	if($stockManage != 0){
		$sSQL="SELECT cartID,cartProdID,cartQuantity,pStockByOpts FROM cart INNER JOIN products ON cart.cartProdID=products.pID WHERE cartCompleted=1 AND cartOrderID=" . $smOrdId;
		$result = mysql_query($sSQL) or print(mysql_error());
		while($rs = mysql_fetch_array($result)){
			if(((int)$rs['pStockByOpts'] <> 0)){
				$sSQL = "SELECT coOptID FROM cartoptions INNER JOIN options ON cartoptions.coOptID=options.optID INNER JOIN optiongroup ON options.optGroup=optiongroup.optGrpID WHERE optType IN (-2,-1,1,2) AND coCartID=" . $rs["cartID"];
				$result2 = mysql_query($sSQL) or print(mysql_error());
				while($rs2 = mysql_fetch_array($result2)){
					$sSQL = "UPDATE options SET optStock=optStock+" . $rs["cartQuantity"] . " WHERE optID=" . $rs2["coOptID"];
					mysql_query($sSQL) or print(mysql_error());
				}
				mysql_free_result($result2);
			}else{
				$sSQL = "UPDATE products SET pInStock=pInStock+" . $rs["cartQuantity"] . " WHERE pID='" . $rs["cartProdID"] . "'";
				mysql_query($sSQL) or print(mysql_error());
			}
		}
		mysql_free_result($result);
	}
}
if($lisuccess==2){
?>
	  <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr>
          <td width="100%">
            <table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="">
			  <tr> 
                <td width="100%" colspan="4" align="center"><p>&nbsp;</p><p>&nbsp;</p>
				  <p><strong><?php print $yyOpFai?></strong></p><p>&nbsp;</p>
				  <p><?php print $yyCorCoo?> <?php print $yyCorLI?> <a href="login.php"><?php print $yyClkHer?></a>.</p>
				</td>
			  </tr>
			</table>
		  </td>
		</tr>
	  </table>
<?php
}else{
$success=true;
$alreadygotadmin = getadminsettings();
if(@$_POST["updatestatus"]=="1"){
	mysql_query("UPDATE orders SET ordTrackNum='" . mysql_escape_string(unstripslashes(trim(@$_POST["ordTrackNum"]))) . "',ordStatusInfo='" . mysql_escape_string(unstripslashes(trim(@$_POST["ordStatusInfo"]))) . "',ordInvoice='" . mysql_escape_string(unstripslashes(trim(@$_POST["ordInvoice"]))) . "'" . (trim(@$_POST['shipcarrier']) != '' ? ',ordShipCarrier=' . trim(@$_POST['shipcarrier']) : '') . " WHERE ordID=" . @$_POST["orderid"]) or print(mysql_error());
}elseif(@$_GET["id"] != ""){
	if(@$_POST["delccdets"] != "")
		mysql_query("UPDATE orders SET ordCNum='' WHERE ordID=" . @$_GET["id"]) or print(mysql_error());
	$sSQL = "SELECT cartProdId,cartProdName,cartProdPrice,cartQuantity,cartID FROM cart WHERE cartOrderID=" . $_GET["id"];
	$allorders = mysql_query($sSQL) or print(mysql_error());
}else{
	if($delccafter != 0) mysql_query("UPDATE orders SET ordCNum='' WHERE ordDate<'" . date("Y-m-d H:i:s", time()-($delccafter*60*60*24)) . "'") or print(mysql_error());
	if(@$_SESSION['hasdeletedoldcart'] != '1'){ trimoldcartitems(time()-(3*60*60*24)); $_SESSION['hasdeletedoldcart']='1'; }
	$numstatus=0;
	$sSQL = "SELECT statID,statPrivate FROM orderstatus WHERE statPrivate<>'' ORDER BY statID";
	$result = mysql_query($sSQL) or print(mysql_error());
	while($rs = mysql_fetch_assoc($result)){
		$allstatus[$numstatus++]=$rs;
	}
	mysql_free_result($result);
}
if(@$_POST["updatestatus"]=="1"){
?>
<script language="javascript" type="text/javascript">
<!--
setTimeout("history.go(-2);",1100);
// -->
</script>
	  <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr>
          <td width="100%">
            <table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="">
			  <tr> 
                <td width="100%" colspan="4" align="center"><br /><strong><?php print $yyUpdSuc?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
                        <?php print $yyNoAuto?> <a href="javascript:history.go(-2)"><strong><?php print $yyClkHer?></strong></a>.<br /><br />
						<img src="../images/clearpixel.gif" width="300" height="3" alt="" /></td>
			  </tr>
			</table>
		  </td>
		</tr>
	  </table>
<?php
}elseif(@$_POST["doedit"] == "true"){
	$OWSP = "";
	$sSQL = "SELECT ordSessionID,ordClientID FROM orders WHERE ordID='" . $_POST["orderid"] . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	$rs = mysql_fetch_array($result);
	$thesessionid = $rs['ordSessionID'];
	$thecustomerid = $rs['ordClientID'];
	mysql_free_result($result);
	$sSQL = "UPDATE orders SET ";
	$sSQL .= "ordName='" . mysql_escape_string(trim(unstripslashes(@$_POST["name"]))) . "',";
	$sSQL .= "ordAddress='" . mysql_escape_string(trim(unstripslashes(@$_POST["address"]))) . "',";
	$sSQL .= "ordAddress2='" . mysql_escape_string(trim(unstripslashes(@$_POST['address2']))) . "',";
	$sSQL .= "ordCity='" . mysql_escape_string(trim(unstripslashes(@$_POST["city"]))) . "',";
	$sSQL .= "ordState='" . mysql_escape_string(unstripslashes(trim(@$_POST["state"]))) . "',";
	$sSQL .= "ordZip='" . mysql_escape_string(unstripslashes(trim(@$_POST["zip"]))) . "',";
	$sSQL .= "ordCountry='" . mysql_escape_string(unstripslashes(trim(@$_POST["country"]))) . "',";
	$sSQL .= "ordEmail='" . mysql_escape_string(unstripslashes(trim(@$_POST["email"]))) . "',";
	$sSQL .= "ordPhone='" . mysql_escape_string(unstripslashes(trim(@$_POST["phone"]))) . "',";
	$sSQL .= "ordShipName='" . mysql_escape_string(trim(unstripslashes(@$_POST["sname"]))) . "',";
	$sSQL .= "ordShipAddress='" . mysql_escape_string(trim(unstripslashes(@$_POST["saddress"]))) . "',";
	$sSQL .= "ordShipAddress2='" . mysql_escape_string(trim(unstripslashes(@$_POST['saddress2']))) . "',";
	$sSQL .= "ordShipCity='" . mysql_escape_string(trim(unstripslashes(@$_POST["scity"]))) . "',";
	$sSQL .= "ordShipState='" . mysql_escape_string(unstripslashes(trim(@$_POST["sstate"]))) . "',";
	$sSQL .= "ordShipZip='" . mysql_escape_string(unstripslashes(trim(@$_POST["szip"]))) . "',";
	$sSQL .= "ordShipCountry='" . mysql_escape_string(unstripslashes(trim(@$_POST["scountry"]))) . "',";
	$sSQL .= "ordShipPhone='" . mysql_escape_string(unstripslashes(trim(@$_POST["sphone"]))) . "',";
	$sSQL .= "ordShipType='" . mysql_escape_string(unstripslashes(trim(@$_POST["shipmethod"]))) . "',";
	$sSQL .= "ordShipCarrier='" . mysql_escape_string(unstripslashes(trim(@$_POST["shipcarrier"]))) . "',";
	$sSQL .= "ordIP='" . mysql_escape_string(unstripslashes(trim(@$_POST["ipaddress"]))) . "',";
	$ordComLoc = 0;
	if(trim(@$_POST["commercialloc"])=="Y") $ordComLoc = 1;
	if(trim(@$_POST["wantinsurance"])=="Y") $ordComLoc += 2;
	if(trim(@$_POST["saturdaydelivery"])=="Y") $ordComLoc += 4;
	if(trim(@$_POST["signaturerelease"])=="Y") $ordComLoc += 8;
	if(trim(@$_POST["insidedelivery"])=="Y") $ordComLoc += 16;
	$sSQL .= "ordComLoc=" . $ordComLoc . ",";
	$sSQL .= "ordAffiliate='" . trim(@$_POST["PARTNER"]) . "',";
	$sSQL .= "ordAddInfo='" . mysql_escape_string(trim(unstripslashes(@$_POST["ordAddInfo"]))) . "',";
	$sSQL .= "ordStatusInfo='" . mysql_escape_string(trim(unstripslashes(@$_POST["ordStatusInfo"]))) . "',";
	$sSQL .= "ordTrackNum='" . mysql_escape_string(trim(unstripslashes(@$_POST["ordTrackNum"]))) . "',";
	$sSQL .= "ordDiscountText='" . mysql_escape_string(str_replace(array("\r\n","\n","\r"),array('<br />','<br />','<br />'),trim(unstripslashes(@$_POST["discounttext"])))) . "',";
	$sSQL .= "ordInvoice='" . mysql_escape_string(unstripslashes(trim(@$_POST["ordInvoice"]))) . "',";
	$sSQL .= "ordExtra1='" . mysql_escape_string(unstripslashes(trim(@$_POST["ordextra1"]))) . "',";
	$sSQL .= "ordExtra2='" . mysql_escape_string(unstripslashes(trim(@$_POST["ordextra2"]))) . "',";
	$sSQL .= "ordShipExtra1='" . mysql_escape_string(unstripslashes(trim(@$_POST["ordshipextra1"]))) . "',";
	$sSQL .= "ordShipExtra2='" . mysql_escape_string(unstripslashes(trim(@$_POST["ordshipextra2"]))) . "',";
	$sSQL .= "ordCheckoutExtra1='" . mysql_escape_string(unstripslashes(trim(@$_POST["ordcheckoutextra1"]))) . "',";
	$sSQL .= "ordCheckoutExtra2='" . mysql_escape_string(unstripslashes(trim(@$_POST["ordcheckoutextra2"]))) . "',";
	$sSQL .= "ordShipping='" . mysql_escape_string(trim(@$_POST["ordShipping"])) . "',";
	$sSQL .= "ordStateTax='" . mysql_escape_string(trim(@$_POST["ordStateTax"])) . "',";
	$sSQL .= "ordCountryTax='" . mysql_escape_string(trim(@$_POST["ordCountryTax"])) . "',";
	if(@$canadataxsystem==TRUE) $sSQL .= "ordHSTTax='" . mysql_escape_string(trim(@$_POST["ordHSTTax"])) . "',";
	$sSQL .= "ordDiscount='" . mysql_escape_string(trim(@$_POST["ordDiscount"])) . "',";
	$sSQL .= "ordHandling='" . mysql_escape_string(trim(@$_POST["ordHandling"])) . "',";
	$sSQL .= "ordAuthNumber='" . mysql_escape_string(trim(@$_POST["ordAuthNumber"])) . "',";
	$sSQL .= "ordTransID='" . mysql_escape_string(trim(@$_POST["ordTransID"])) . "',";
	$sSQL .= "ordTotal='" . mysql_escape_string(trim(@$_POST["ordtotal"])) . "'";
	$sSQL .= " WHERE ordID='" . $_POST["orderid"] . "'";
	mysql_query($sSQL) or print(mysql_error());

	foreach($_POST as $objItem => $objValue){
		//print $objItem . " : " . $objValue . "<br>";
		if(substr($objItem,0,6)=="prodid"){
			$idno = (int)substr($objItem, 6);
			$cartid = trim(@$_POST["cartid" . $idno]);
			$prodid = trim(@$_POST["prodid" . $idno]);
			$quant = trim(@$_POST["quant" . $idno]);
			$theprice = trim(@$_POST["price" . $idno]);
			$prodname = trim(@$_POST["prodname" . $idno]);
			$delitem = trim(@$_POST["del_" . $idno]);
			if($delitem=="yes"){
				mysql_query("DELETE FROM cart WHERE cartID=" . $cartid) or print(mysql_error());
				mysql_query("DELETE FROM cartoptions WHERE coCartID=" . $cartid) or print(mysql_error());
				$cartid = "";
			}elseif($cartid != ""){
				$sSQL = "UPDATE cart SET cartProdID='" . mysql_escape_string(trim(unstripslashes($prodid))) . "',cartProdPrice=" . $theprice . ",cartProdName='" . mysql_escape_string(trim(unstripslashes($prodname))) . "',cartQuantity=" . $quant . " WHERE cartID=" . $cartid;
				mysql_query($sSQL) or print(mysql_error());
				mysql_query("DELETE FROM cartoptions WHERE coCartID=" . $cartid) or print(mysql_error());
			}else{
				$sSQL = "INSERT INTO cart (cartSessionID,cartClientID,cartProdID,cartQuantity,cartCompleted,cartProdName,cartProdPrice,cartOrderID,cartDateAdded) VALUES (";
				$sSQL .= "'" . $thesessionid . "',";
				$sSQL .= "'" . $thecustomerid . "',";
				$sSQL .= "'" . mysql_escape_string(trim(unstripslashes($prodid))) . "',";
				$sSQL .= $quant . ",";
				$sSQL .= "1,";
				$sSQL .= "'" . mysql_escape_string(trim(unstripslashes($prodname))) . "',";
				$sSQL .= "'" . $theprice . "',";
				$sSQL .= @$_POST["orderid"] . ",";
				$sSQL .= "'" . date("Y-m-d H:i:s", time() + ($dateadjust*60*60)) . "')";
				mysql_query($sSQL) or print(mysql_error());
				$cartid = mysql_insert_id();
			}
			if($cartid != ""){
				$optprefix = "optn" . $idno . '_';
				$prefixlen = strlen($optprefix);
				foreach($_POST as $kk => $kkval){
					if(substr($kk,0,$prefixlen)==$optprefix && trim($kkval) != ''){
						$optidarr = split('\|', $kkval);
						$optid = $optidarr[0];
						if(@$_POST["v" . $kk] == ""){
							$sSQL="SELECT optID,".getlangid("optGrpName",16).",".getlangid("optName",32)."," . $OWSP . "optPriceDiff,optWeightDiff,optType,optFlags FROM options LEFT JOIN optiongroup ON options.optGroup=optiongroup.optGrpID WHERE optID='" . mysql_escape_string($kkval) . "'";
							$result = mysql_query($sSQL) or print(mysql_error());
							if($rs = mysql_fetch_array($result)){
								if(abs($rs["optType"]) != 3){
									$sSQL = "INSERT INTO cartoptions (coCartID,coOptID,coOptGroup,coCartOption,coPriceDiff,coWeightDiff) VALUES (" . $cartid . "," . $rs["optID"] . ",'" . mysql_escape_string($rs[getlangid("optGrpName",16)]) . "','" . mysql_escape_string($rs[getlangid("optName",32)]) . "',";
									$sSQL .= $optidarr[1] . ",0)";
								}else
									$sSQL = "INSERT INTO cartoptions (coCartID,coOptID,coOptGroup,coCartOption,coPriceDiff,coWeightDiff) VALUES (" . $cartid . "," . $rs["optID"] . ",'" . mysql_escape_string($rs[getlangid("optGrpName",16)]) . "','',0,0)";
								mysql_query($sSQL) or print(mysql_error());
							}
							mysql_free_result($result);
						}else{
							$sSQL="SELECT optID,".getlangid("optGrpName",16).",".getlangid("optName",32)." FROM options LEFT JOIN optiongroup ON options.optGroup=optiongroup.optGrpID WHERE optID='" . mysql_escape_string($kkval) . "'";
							$result = mysql_query($sSQL) or print(mysql_error());
							$rs = mysql_fetch_array($result);
							$sSQL = "INSERT INTO cartoptions (coCartID,coOptID,coOptGroup,coCartOption,coPriceDiff,coWeightDiff) VALUES (" . $cartid . "," . $rs["optID"] . ",'" . mysql_escape_string($rs[getlangid("optGrpName",16)]) . "','" . mysql_escape_string(unstripslashes(trim(@$_POST["v" . $kk]))) . "',0,0)";
							mysql_query($sSQL) or print(mysql_error());
							mysql_free_result($result);
						}
					}
				}
			}
		}
	}
?>
<script language="javascript" type="text/javascript">
<!--
setTimeout("history.go(-2);",1100);
// -->
</script>
	  <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr>
          <td width="100%">
            <table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="">
			  <tr> 
                <td width="100%" colspan="4" align="center"><br /><strong><?php print $yyUpdSuc?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
                        <?php print $yyNoAuto?> <a href="javascript:history.go(-2)"><strong><?php print $yyClkHer?></strong></a>.<br /><br />
						<img src="../images/clearpixel.gif" width="300" height="3" alt="" /></td>
			  </tr>
			</table>
		  </td>
		</tr>
	  </table>
<?php
}elseif(@$_GET["id"] != ""){
	$statetaxrate=0;
	$countrytaxrate=0;
	$hsttaxrate=0;
	$countryorder=0;
	$sSQL = "SELECT ordID,ordName,ordAddress,ordAddress2,ordCity,ordState,ordZip,ordCountry,ordEmail,ordPhone,ordShipName,ordShipAddress,ordShipAddress2,ordShipCity,ordShipState,ordShipZip,ordShipCountry,ordShipPhone,ordPayProvider,ordAuthNumber,ordTransID,ordTotal,ordDate,ordStateTax,ordCountryTax,ordHSTTax,ordShipping,ordShipType,ordShipCarrier,ordIP,ordAffiliate,ordDiscount,ordHandling,ordDiscountText,ordComLoc,ordExtra1,ordExtra2,ordShipExtra1,ordShipExtra2,ordCheckoutExtra1,ordCheckoutExtra2,ordAddInfo,ordCNum,ordTrackNum,ordInvoice,ordStatusInfo FROM orders LEFT JOIN payprovider ON payprovider.payProvID=orders.ordPayProvider WHERE ordID='" . $_GET["id"] . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	$alldata = mysql_fetch_array($result);
	$alldata["ordDate"] = strtotime($alldata["ordDate"]);
	mysql_free_result($result);
	if($doedit){
		print '<form method="post" name="editform" action="adminorders.php" onsubmit="return confirmedit()"><input type="hidden" name="orderid" value="' . $_GET["id"] . '" /><input type="hidden" name="doedit" value="true" />';
		$overridecurrency=TRUE;
		$orcsymbol="";
		$orcdecplaces=2;
		$orcpreamount=TRUE;
		$orcdecimals=".";
		$orcthousands="";
	}
?>
<script language="javascript" type="text/javascript">
<!--
var newwin="";
var plinecnt=0;
function openemailpopup(id) {
  popupWin = window.open('popupemail.php?'+id,'emailpopup','menubar=no, scrollbars=no, width=300, height=250, directories=no,location=no,resizable=yes,status=no,toolbar=no')
}
function updateoptions(id){
	prodid = document.getElementById('prodid'+id).value;
	if(prodid != ''){
		newwin = window.open('popupemail.php?prod='+prodid+'&index='+id,'updateopts','menubar=no, scrollbars=no, width=50, height=40, directories=no,location=no,resizable=yes,status=no,toolbar=no');
	}
	return(false);
}
function extraproduct(plusminus){
var productspan=document.getElementById('productspan');
if(plusminus=='+'){
productspan.innerHTML=productspan.innerHTML.replace(/<!--NEXTPRODUCTCOMMENT-->/,'<!--PLINE'+plinecnt+'--><tr><td valign="top"><input type="button" value="..." onclick="updateoptions('+(plinecnt+1000)+')">&nbsp;<input name="prodid'+(plinecnt+1000)+'" size="18" id="prodid'+(plinecnt+1000)+'"></td><td valign="top"><input type="text" id="prodname'+(plinecnt+1000)+'" name="prodname'+(plinecnt+1000)+'" size="24"></td><td><span id="optionsspan'+(plinecnt+1000)+'">-</span></td><td valign="top"><input type="text" id="quant'+(plinecnt+1000)+'" name="quant'+(plinecnt+1000)+'" size="5" value="1"></td><td valign="top"><input type="text" id="price'+(plinecnt+1000)+'" name="price'+(plinecnt+1000)+'" value="0" size="7"><br /><input type="hidden" id="optdiffspan'+(plinecnt+1000)+'" value="0"></td><td>&nbsp;</td></tr><!--PLINEEND'+plinecnt+'--><!--NEXTPRODUCTCOMMENT-->');
plinecnt++;
}else{
if(plinecnt>0){
plinecnt--;
var restr = '<!--PLINE'+plinecnt+'-->(.|\\n)+<!--PLINEEND'+plinecnt+'-->';
//alert(restr);
var re = new RegExp(restr);
productspan.innerHTML=productspan.innerHTML.replace(re,'');
}
}
}
function confirmedit(){
if(confirm('<?php print str_replace("'","\'",$yyChkRec)?>'))
	return(true);
return(false);
}
function dorecalc(onlytotal){
var thetotal=0,totoptdiff=0;
for(var i in document.forms.editform){
if(i.substr(0,5)=="quant"){
	theid = i.substr(5);
	totopts=0;
	delbutton = document.getElementById("del_"+theid);
	if(delbutton==null)
		isdeleted=false;
	else
		isdeleted=delbutton.checked;
	if(! isdeleted){
	for(var ii in document.forms.editform){
		var opttext="optn"+theid+"_";
		if(ii.substr(0,opttext.length)==opttext){
			theitem = document.getElementById(ii);
			if(document.getElementById('v'+ii)==null){
				thevalue = theitem[theitem.selectedIndex].value;
				if(thevalue.indexOf('|')>0){
					totopts += parseFloat(thevalue.substr(thevalue.indexOf('|')+1));
				}
			}
		}
	}
	thequant = parseInt(document.getElementById(i).value);
	if(isNaN(thequant)) thequant=0;
	theprice = parseFloat(document.getElementById("price"+theid).value);
	if(isNaN(theprice)) theprice=0;
	document.getElementById("optdiffspan"+theid).value=totopts;
	optdiff = parseFloat(document.getElementById("optdiffspan"+theid).value);
	if(isNaN(optdiff)) optdiff=0;
	thetotal += thequant * (theprice + optdiff);
	totoptdiff += thequant * optdiff;
	}
}
}
document.getElementById("optdiffspan").innerHTML=totoptdiff.toFixed(2);
document.getElementById("ordtotal").value = thetotal.toFixed(2);
if(onlytotal==true) return;
statetaxrate = parseFloat(document.getElementById("staterate").value);
if(isNaN(statetaxrate)) statetaxrate=0;
countrytaxrate = parseFloat(document.getElementById("countryrate").value);
if(isNaN(countrytaxrate)) countrytaxrate=0;
discount = parseFloat(document.getElementById("ordDiscount").value);
if(isNaN(discount)){
	discount=0;
	document.getElementById("ordDiscount").value=0;
}
statetaxtotal = (statetaxrate * (thetotal-discount)) / 100.0;
countrytaxtotal = (countrytaxrate * (thetotal-discount)) / 100.0;
shipping = parseFloat(document.getElementById("ordShipping").value);
if(isNaN(shipping)){
	shipping=0;
	document.getElementById("ordShipping").value=0;
}
handling = parseFloat(document.getElementById("ordHandling").value);
if(isNaN(handling)){
	handling=0;
	document.getElementById("ordHandling").value=0;
}
<?php	if(@$taxShipping==2){ ?>
statetaxtotal += (statetaxrate * shipping) / 100.0;
countrytaxtotal += (countrytaxrate * shipping) / 100.0;
<?php	}
		if(@$taxHandling==2){ ?>
statetaxtotal += (statetaxrate * handling) / 100.0;
countrytaxtotal += (countrytaxrate * handling) / 100.0;
<?php	} ?>
document.getElementById("ordStateTax").value = statetaxtotal.toFixed(2);
document.getElementById("ordCountryTax").value = countrytaxtotal.toFixed(2);
hstobj = document.getElementById("ordHSTTax");
hsttax=0;
if(! (hstobj==null)){
	hsttax = parseFloat(hstobj.value);
}
grandtotal = (thetotal + shipping + handling + statetaxtotal + countrytaxtotal + hsttax) - discount;
document.getElementById("grandtotalspan").innerHTML = grandtotal.toFixed(2);
}
function ajaxcallback() {
	if(ajaxobj.readyState==4){
		document.getElementById("googleupdatespan").innerHTML = ajaxobj.responseText;
	}
}
function updategoogleorder(theact,ordid){
	if(confirm('Inform Google of change to order id ' + ordid + "?")){
		document.getElementById("googleupdatespan").innerHTML = '';
		if(window.XMLHttpRequest){
			ajaxobj = new XMLHttpRequest();
		}else{
			ajaxobj = new ActiveXObject("MSXML2.XMLHTTP");
		}
		ajaxobj.onreadystatechange = ajaxcallback;
		extraparams='';
		if(theact=='ship'){
			shipcar = document.getElementById("shipcarrier");
			if(shipcar!= null){
				trackno=document.getElementById("ordTrackNum").value
				if(trackno!='' && confirm('Include tracking and carrier info?')){
					extraparams='&carrier='+(shipcar.options[shipcar.selectedIndex].value)+'&trackno='+document.getElementById("ordTrackNum").value;
				}
			}
		}
		document.getElementById("googleupdatespan").innerHTML = 'Connecting...';
		ajaxobj.open("GET", "ajaxservice.php?gid="+ordid+"&act="+theact+extraparams, true);
		ajaxobj.send(null);
	}
}
function updategooglestatus(theact,ordid){
	if(confirm('Update Google account status and inform customer of this status change?')){
		document.getElementById("googleupdatespan").innerHTML = '';
		if(window.XMLHttpRequest){
			ajaxobj = new XMLHttpRequest();
		}else{
			ajaxobj = new ActiveXObject("MSXML2.XMLHTTP");
		}
		ajaxobj.onreadystatechange = ajaxcallback;
		themessage="googlemessage=" + encodeURI(document.getElementById("ordStatusInfo").value);
		document.getElementById("googleupdatespan").innerHTML = 'Connecting...';
		ajaxobj.open("POST", "ajaxservice.php?gid="+ordid+"&act="+theact, true);
		ajaxobj.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		ajaxobj.setRequestHeader('Content-Length', themessage.length);
		ajaxobj.send(themessage);
	}
}
//-->
</script>
	  <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr>
          <td width="100%">
            <table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="">
<?php		if($isprinter && ! @isset($packingslipheader)) $packingslipheader=$invoiceheader;
			if($isinvoice && @$invoiceheader != ""){ ?>
			  <tr><td width="100%" colspan="4"><?php print $invoiceheader?></td></tr>
<?php		}elseif($isprinter && @$packingslipheader != ""){ ?>
			  <tr><td width="100%" colspan="4"><?php print $packingslipheader?></td></tr>
<?php		} ?>
			  <tr><td width="100%" colspan="4" align="center"><strong><?php print $xxOrdNum . " " . $alldata["ordID"] . "<br /><br />" . date($dateformatstr, $alldata["ordDate"]) . " " . date("H:i", $alldata["ordDate"])?></strong></td></tr>
<?php		if($isprinter && ! @isset($packingslipaddress)) $packingslipaddress=$invoiceaddress;
			if($isinvoice && @$invoiceaddress != ""){ ?>
			  <tr><td width="100%" colspan="4"><?php print $invoiceaddress?></td></tr>
<?php		}elseif($isprinter && @$packingslipaddress != ""){ ?>
			  <tr><td width="100%" colspan="4"><?php print $packingslipaddress?></td></tr>
<?php		} ?>
<?php		if(trim(@$extraorderfield1)!=""){ ?>
			<tr>
			  <td width="20%" align="right"><strong><?php print $extraorderfield1 ?>:</strong></td>
			  <td align="left" colspan="3"><?php print editfunc($alldata["ordExtra1"],"ordextra1",25)?></td>
			</tr>
<?php		} ?>
			<tr>
			  <td width="20%" align="right"><strong><?php print $xxName?>:</strong></td>
			  <td width="30%" align="left"><?php print editfunc($alldata["ordName"],"name",25)?></td>
			  <td width="20%" align="right"><?php if(! $isprinter && $alldata["ordAuthNumber"] != "" && ! $doedit) print '<input type="button" value="Resend" onclick="javascript:openemailpopup(\'id=' . $alldata["ordID"] . '\')" />' ?>
			  <strong><?php print $xxEmail?>:</strong></td>
			  <td width="30%" align="left"><?php
				if($isprinter || $doedit) print editfunc($alldata["ordEmail"],"email",25); else print '<a href="mailto:' . $alldata["ordEmail"] . '">' . $alldata["ordEmail"] . '</a>';?></td>
			</tr>
			<tr>
			  <td align="right"><strong><?php print $xxAddress?>:</strong></td>
			  <td align="left"<?php if(@$useaddressline2==TRUE || trim($alldata['ordAddress2']) != '') print ' colspan="3"'?>><?php print editfunc($alldata['ordAddress'],"address",25)?></td>
<?php	if(@$useaddressline2==TRUE || trim($alldata['ordAddress2']) != ''){ ?>
			</tr>
			<tr>
			  <td align="right"><strong><?php print $xxAddress2?>:</strong></td>
			  <td align="left"><?php print editfunc($alldata["ordAddress2"],"address2",25)?></td>
<?php	} ?>
			  <td align="right"><strong><?php print $xxCity?>:</strong></td>
			  <td align="left"><?php print editfunc($alldata["ordCity"],"city",25)?></td>
			</tr>
			<tr>
			  <td align="right"><strong><?php print $xxAllSta?>:</strong></td>
			  <td align="left"><?php print editfunc($alldata["ordState"],"state",25)?></td>
			  <td align="right"><strong><?php print $xxCountry?>:</strong></td>
			  <td align="left"><?php
			if($doedit){
				$foundmatch=FALSE;
				print '<select name="country" size="1">';
				$sSQL = "SELECT countryName,countryTax,countryOrder FROM countries ORDER BY countryOrder DESC, countryName";
				$result = mysql_query($sSQL) or print(mysql_error());
				while($rs2 = mysql_fetch_array($result)){
					print '<option value="' . str_replace('"','&quot;',$rs2["countryName"]) . '"';
					if($alldata["ordCountry"]==$rs2["countryName"]){
						print ' selected';
						$foundmatch=TRUE;
						$countrytaxrate=$rs2["countryTax"];
						$countryorder=$rs2["countryOrder"];
					}
					print '>' . $rs2["countryName"] . "</option>\r\n";			}
				mysql_free_result($result);
				if(! $foundmatch) print '<option value="' . str_replace('"','&quot;',$alldata["ordCountry"]) . '" selected>' . $alldata["ordCountry"] . "</option>\r\n";
				print '</select>';
				if($countryorder==2){
					$sSQL = "SELECT stateTax FROM states WHERE stateName='" . mysql_escape_string($alldata["ordState"]) . "'";
					$result = mysql_query($sSQL) or print(mysql_error());
					if($rs2 = mysql_fetch_array($result))
						$statetaxrate = $rs2["stateTax"];
				}
			}else
				print $alldata["ordCountry"];?></td>
			</tr>
			<tr>
			  <td align="right"><strong><?php print $xxZip?>:</strong></td>
			  <td align="left"><?php print editfunc($alldata["ordZip"],"zip",15)?></td>
			  <td align="right"><strong><?php print $xxPhone?>:</strong></td>
			  <td align="left"><?php print editfunc($alldata["ordPhone"],"phone",25)?></td>
			</tr>
<?php	if(trim(@$extraorderfield2)!=""){ ?>
			<tr>
			  <td align="right"><strong><?php print @$extraorderfield2 ?>:</strong></td>
			  <td align="left" colspan="3"><?php print editfunc($alldata["ordExtra2"],"ordextra2",25)?></td>
			</tr>
<?php	}
		if(! $isprinter){ ?>
			<tr>
			  <td align="right"><strong>IP Address:</strong></td>
			  <td align="left"><?php print editfunc($alldata["ordIP"],"ipaddress",15)?></td>
			  <td align="right"><strong><?php print $yyAffili?>:</strong></td>
			  <td align="left"><?php print editfunc($alldata["ordAffiliate"],"PARTNER",15)?></td>
			</tr>
<?php	}
		if((trim($alldata['ordDiscountText'])!='' && (! $isprinter || $isinvoice)) || $doedit){ ?>
			<tr>
			  <td align="right" valign="top"><strong><?php print $xxAppDs?>:</strong></td>
			  <td align="left" colspan="3"><?php if($doedit) print '<textarea name="discounttext" cols="50" rows="2" wrap=virtual>' . str_replace('<br />', "\r\n", $alldata["ordDiscountText"]) . '</textarea>'; else print $alldata["ordDiscountText"]; ?></td>
			</tr>
<?php	}
	  if(trim($alldata['ordShipName']) != '' || trim($alldata['ordShipAddress']) != '' || trim($alldata['ordShipCity']) != '' || trim($alldata['ordShipExtra1'])!='' || $doedit){ ?>
			<tr>
			  <td width="100%" align="center" colspan="4"><strong><?php print $xxShpDet?>.</strong></td>
			</tr>
<?php		if(trim(@$extraorderfield1)!=''){ ?>
			<tr>
			  <td align="right"><strong><?php print @$extraorderfield1 ?>:</strong></td>
			  <td align="left" colspan="3"><?php print editfunc($alldata["ordShipExtra1"],"ordshipextra1",25)?></td>
			</tr>
<?php		} ?>
			<tr>
			  <td align="right"><strong><?php print $xxName?>:</strong></td>
			  <td align="left" colspan="3"><?php print editfunc($alldata["ordShipName"],"sname",25)?></td>
			</tr>
			<tr>
			  <td align="right"><strong><?php print $xxAddress?>:</strong></td>
			  <td align="left"<?php if(@$useaddressline2==TRUE || trim($alldata['ordShipAddress2']) != '') print ' colspan="3"'?>><?php print editfunc($alldata["ordShipAddress"],"saddress",25)?></td>
<?php	if(@$useaddressline2==TRUE || trim($alldata['ordShipAddress2']) != ''){ ?>
			</tr>
			<tr>
			  <td align="right"><strong><?php print $xxAddress2?>:</strong></td>
			  <td align="left"><?php print editfunc($alldata["ordShipAddress2"],"saddress2",25)?></td>
<?php	} ?>
			  <td align="right"><strong><?php print $xxCity?>:</strong></td>
			  <td align="left"><?php print editfunc($alldata["ordShipCity"],"scity",25)?></td>
			</tr>
			<tr>
			  <td align="right"><strong><?php print $xxAllSta?>:</strong></td>
			  <td align="left"><?php print editfunc($alldata["ordShipState"],"sstate",25)?></td>
			  <td align="right"><strong><?php print $xxCountry?>:</strong></td>
			  <td align="left"><?php
			if($doedit){
				if(trim($alldata["ordShipName"]) != "" || trim($alldata["ordShipAddress"]) != "") $usingshipcountry=TRUE; else $usingshipcountry=FALSE;
				$foundmatch=FALSE;
				print '<select name="scountry" size="1">';
				$sSQL = "SELECT countryName,countryTax,countryOrder FROM countries ORDER BY countryOrder DESC, countryName";
				$result = mysql_query($sSQL) or print(mysql_error());
				while($rs2 = mysql_fetch_array($result)){
					print '<option value="' . str_replace('"','&quot;',$rs2["countryName"]) . '"';
					if($alldata["ordShipCountry"]==$rs2["countryName"]){
						print ' selected';
						$foundmatch=TRUE;
						if($usingshipcountry) $countrytaxrate=$rs2["countryTax"];
						$countryorder=$rs2["countryOrder"];
					}
					print '>' . $rs2["countryName"] . "</option>\r\n";			}
				mysql_free_result($result);
				if(! $foundmatch) print '<option value="' . str_replace('"','&quot;',$alldata["ordShipCountry"]) . '" selected>' . $alldata["ordShipCountry"] . "</option>\r\n";
				print '</select>';
				if($countryorder==2 && $usingshipcountry){
					$sSQL = "SELECT stateTax FROM states WHERE stateName='" . mysql_escape_string($alldata["ordShipState"]) . "'";
					$result = mysql_query($sSQL) or print(mysql_error());
					if($rs2 = mysql_fetch_array($result))
						$statetaxrate = $rs2["stateTax"];
				}
			}else
				print $alldata['ordShipCountry']?></td>
			</tr>
			<tr>
			  <td align="right"><strong><?php print $xxZip?>:</strong></td>
			  <td><?php print editfunc($alldata['ordShipZip'],'szip',15)?></td>
			  <td align="right"><strong><?php print $xxPhone?>:</strong></td>
			  <td><?php print editfunc($alldata['ordShipPhone'],'sphone',25)?></td>
			</tr>
			<?php if(trim(@$extraorderfield2) != ''){ ?>
			<tr>
			  <td align="right"><strong><?php print $extraorderfield2 ?>:</strong></td>
			  <td align="left" colspan="3"><?php print editfunc($alldata['ordShipExtra2'],'ordshipextra2',25)?></td>
			</tr>
			<?php } ?>
<?php }
	if(! $isprinter && ! $doedit) print '<form method="post" action="adminorders.php"><input type="hidden" name="updatestatus" value="1" /><input type="hidden" name="orderid" value="' . @$_GET["id"] . '" />';
	if($alldata['ordShipCarrier'] != 0 || $alldata['ordShipType'] != '' || $doedit){ ?>
			<tr>
			  <td align="right"><strong><?php print $xxShpMet?>:</strong></td>
			  <td align="left"><?php	if(! $isprinter){ ?>
					<select name="shipcarrier" id="shipcarrier" size="1">
					<option value="0"><?php print $yyOther?></option>
					<option value="3" <?php if((int)$alldata['ordShipCarrier']==3) print 'selected'?>>USPS</option>
					<option value="4" <?php if((int)$alldata['ordShipCarrier']==4) print 'selected'?>>UPS</option>
					<option value="6" <?php if((int)$alldata['ordShipCarrier']==6) print 'selected'?>>CanPos</option>
					<option value="7" <?php if((int)$alldata['ordShipCarrier']==7) print 'selected'?>>FedEx</option>
					<option value="8" <?php if((int)$alldata['ordShipCarrier']==8) print 'selected'?>>DHL</option>
					</select> <?php		}
										print editfunc($alldata['ordShipType'],'shipmethod',25); ?></td>
			  <td align="right"><strong><?php if($doedit) print $xxCLoc . ':'?></strong></td>
			  <td align="left"><?php	if($doedit){
											print '<select name="commercialloc" size="1">';
											print '<option value="N">' . $yyNo . '</option>';
											print '<option value="Y"' . (($alldata["ordComLoc"]&1)==1 ? ' selected' : '') . '>' . $yyYes . '</option>';
											print '</select>';
										}?></td>
			</tr>
<?php	if($doedit){ ?>
			<tr>
			  <td align="right"><strong><?php print $xxShpIns?>:</strong></td>
			  <td align="left"><?php	print '<select name="wantinsurance" size="1">';
										print '<option value="N">' . $yyNo . '</option>';
										print '<option value="Y"' . (($alldata["ordComLoc"] & 2)==2 ? ' selected' : '') . '>' . $yyYes . '</option>';
										print '</select>'; ?></td>
			  <td align="right"><strong><?php print $xxSatDe2?>:</strong></td>
			  <td align="left"><?php	print '<select name="saturdaydelivery" size="1">';
										print '<option value="N">' . $yyNo . '</option>';
										print '<option value="Y"' . (($alldata["ordComLoc"] & 4)==4 ? ' selected' : '') . '>' . $yyYes . '</option>';
										print '</select>' ?></td>
			</tr>
			<tr>
			  <td align="right"><strong><?php print $xxSigRe2?>:</strong></td>
			  <td align="left"><?php	print '<select name="signaturerelease" size="1">';
										print '<option value="N">' . $yyNo . '</option>';
										print '<option value="Y"' . (($alldata["ordComLoc"] & 8)==8 ? ' selected' : '') . '>' . $yyYes . '</option>';
										print '</select>' ?></td>
			  <td align="right"><strong><?php print $xxInsDe2?>:</strong></td>
			  <td align="left"><?php	print '<select name="insidedelivery" size="1">';
										print '<option value="N">' . $yyNo . '</option>';
										print '<option value="Y"' . (($alldata["ordComLoc"] & 16)==16 ? ' selected' : '') . '>' . $yyYes . '</option>';
										print '</select>' ?></td>
			</tr>
<?php	}elseif($alldata["ordComLoc"] > 0){
			$shipopts='<strong>Shipping options:</strong>';
			if(($alldata["ordComLoc"] & 1)==1){ print '<tr><td align="right">' . $shipopts.'</td><td align="left" colspan="3">' . $xxCerCLo . '</td></tr>'; $shipopts='';}
			if(($alldata["ordComLoc"] & 2)==2){ print '<tr><td align="right">' . $shipopts.'</td><td align="left" colspan="3">' . $xxShiInI . '</td></tr>'; $shipopts='';}
			if(($alldata["ordComLoc"] & 4)==4){ print '<tr><td align="right">' . $shipopts.'</td><td align="left" colspan="3">' . $xxSatDeR . '</td></tr>'; $shipopts='';}
			if(($alldata["ordComLoc"] & 8)==8){ print '<tr><td align="right">' . $shipopts.'</td><td align="left" colspan="3">' . $xxSigRe2 . '</td></tr>'; $shipopts='';}
			if(($alldata["ordComLoc"] & 16)==16){ print '<tr><td align="right">' . $shipopts.'</td><td align="left" colspan="3">' . $xxInsDe2 . '</td></tr>'; $shipopts='';}
		}
	}
		$ordAuthNumber = trim($alldata["ordAuthNumber"]);
		$ordTransID = trim($alldata["ordTransID"]);
		if(! $isprinter && ($ordAuthNumber != "" || $ordTransID != "" || $doedit)){ ?>
			<tr>
			  <td align="right"><strong><?php print $yyAutCod?>:</strong></td>
			  <td align="left"><?php print editfunc($ordAuthNumber,"ordAuthNumber",15) ?></td>
			  <td align="right"><strong><?php print $yyTranID?>:</strong></td>
			  <td align="left"><?php print editfunc($ordTransID,"ordTransID",15) ?></td>
			</tr>
<?php	}
		$ordAddInfo = Trim($alldata['ordAddInfo']);
		if($ordAddInfo != '' || $doedit){ ?>
			<tr>
			  <td align="right" valign="top"><strong><?php print $xxAddInf?>:</strong></td>
			  <td align="left" colspan="3"><?php
			if($doedit)
				print '<textarea name="ordAddInfo" cols="50" rows="4" wrap=virtual>' . htmlspecialchars($ordAddInfo) . '</textarea>';
			else
				print str_replace(array("\r\n","\n"),array('<br />','<br />'),htmlspecialchars($ordAddInfo)); ?></td>
			</tr>
<?php	}
		if(trim(@$extracheckoutfield1) != ''){
			$checkoutfield1 = '<strong>' . $extracheckoutfield1 . '</strong>';
			$checkoutfield2 = editfunc($alldata['ordCheckoutExtra1'],'ordcheckoutextra1',25)
?>			<tr>
			  <td width="20%" align="right"><?php if(@$extracheckoutfield1reverse) print $checkoutfield2; else print $checkoutfield1 . '<strong>:</strong>' ?></td>
			  <td align="left" colspan="3"><?php if(@$extracheckoutfield1reverse) print $checkoutfield1; else print $checkoutfield2 ?></td>
			</tr>
<?php	}
		if(trim(@$extracheckoutfield2) != ''){
			$checkoutfield1 = '<strong>' . $extracheckoutfield2 . '</strong>';
			$checkoutfield2 = editfunc($alldata['ordCheckoutExtra2'],'ordcheckoutextra2',25)
?>			<tr>
			  <td width="20%" align="right"><?php if(@$extracheckoutfield2reverse) print $checkoutfield2; else print $checkoutfield1 . '<strong>:</strong>' ?></td>
			  <td align="left" colspan="3"><?php if(@$extracheckoutfield2reverse) print $checkoutfield1; else print $checkoutfield2 ?></td>
			</tr>
<?php	}
if(! $isprinter){
		if($alldata['ordPayProvider']==20){
			$ordCNum = $alldata['ordCNum'];
			if($ordCNum != ''){ ?>
					<tr>
					  <td align="right"><strong>Partial CC Number:</strong></td>
					  <td align="left" colspan="3">-<?php print htmlspecialchars($ordCNum) ?></td>
					</tr>
<?php		}
		}
?>			<tr>
			  <td align="right" valign="top"><strong><?php print $yyTraNum?>:</strong></td>
			  <td align="left"><input type="text" name="ordTrackNum" id="ordTrackNum" size="25" value="<?php print htmlspecialchars($alldata['ordTrackNum'])?>"></td>
			  <td align="right" valign="top"><strong><?php print $yyInvNum?>:</strong></td>
			  <td align="left"><input type="text" name="ordInvoice" size="25" value="<?php print htmlspecialchars($alldata['ordInvoice'])?>"></td>
			</tr>
			<tr>
			  <td align="right" valign="top"><strong><?php print $yyStaInf?>:</strong></td>
			  <td align="left" colspan="3"><textarea name="ordStatusInfo" id="ordStatusInfo" cols="50" rows="4" wrap=virtual><?php print htmlspecialchars($alldata['ordStatusInfo'])?></textarea> <?php if(! $doedit) print '<input type="submit" value="' . $yyUpdate . '" ' . ($alldata['ordPayProvider']==20 ? 'onclick="updategooglestatus(\'message\',' . $_GET['id'] . ')" ' : '') . '/>'?></td>
			</tr>
<?php	if(($alldata['ordPayProvider']==3 || $alldata['ordPayProvider']==13 || $alldata['ordPayProvider']==20) && $alldata['ordAuthNumber'] != ''){
			if($alldata['ordPayProvider']==20){ ?>
			<tr>
			  <td width="50%" align="center" colspan="4">
				<strong>Update Google Account Status:</strong> <span id="googleupdatespan"></span>
			  </td>
			</tr>
			<tr>
			  <td width="50%" align="center" colspan="4">
				<input type="button" value="Charge Order" onclick="updategoogleorder('charge',<?php print $alldata['ordID']?>)" />
				<input type="button" value="Cancel Order" onclick="updategoogleorder('cancel',<?php print $alldata['ordID']?>)" />
				<input type="button" value="Refund Order" onclick="updategoogleorder('refund',<?php print $alldata['ordID']?>)" />
				<input type="button" value="Ship Order" onclick="updategoogleorder('ship',<?php print $alldata['ordID']?>)" />
			  </td>
			</tr>
<?php		}else{ ?>
			<tr><td width="50%" align="center" colspan="4"><input type="button" value="Capture Funds" onclick="javascript:openemailpopup('oid=<?php print $alldata['ordID']?>')" /></td></tr>
<?php		}
		}
		if(! $doedit) print '</form>';
	if((int)$alldata["ordPayProvider"]==10){ ?>
			<tr>
			  <td width="50%" align="center" colspan="4"><hr width="50%">
			  </td>
			</tr>
<?php	if(@$_SERVER["HTTPS"] != "on" && (@$_SERVER["SERVER_PORT"] != "443") && @$nochecksslserver != TRUE){ ?>
			<tr>
			  <td width="50%" align="center" colspan="4"><strong><font color="#FF0000">You do not appear to be viewing this page on a secure (https) connection. Credit card information cannot be shown.</strong></td>
			</tr>
<?php	}else{
			$ordCNum = $alldata["ordCNum"];
			if($ordCNum != ""){
				$cnumarr = "";
				$encryptmethod = strtolower(@$encryptmethod);
				if($encryptmethod=="none"){
					$cnumarr = explode("&",$ordCNum);
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
				}else{
					print '<tr><td colspan="4">WARNING: $encryptmethod is not set. Please see http://www.ecommercetemplates.com/phphelp/ecommplus/parameters.asp#encryption</td></tr>';
				}
			} ?>
			<tr>
			  <td width="50%" align="right" colspan="2"><strong><?php print $xxCCName?>:</strong></td>
			  <td width="50%" align="left" colspan="2"><?php
			if(@$encryptmethod!=""){
					if(is_array(@$cnumarr)) print htmlspecialchars(URLDecode(@$cnumarr[4]));
			} ?></td>
			</tr>
			<tr>
			  <td width="50%" align="right" colspan="2"><strong><?php print $yyCarNum?>:</strong></td>
			  <td width="50%" align="left" colspan="2"><?php
			if($ordCNum != ""){
				if(is_array($cnumarr)) print htmlspecialchars($cnumarr[0]);
			}else{
				print "(no data)";
			} ?></td>
			</tr>
			<tr>
			  <td width="50%" align="right" colspan="2"><strong><?php print $yyExpDat?>:</strong></td>
			  <td width="50%" align="left" colspan="2"><?php
			if(@$encryptmethod!=""){
					if(is_array(@$cnumarr)) print htmlspecialchars(@$cnumarr[1]);
			} ?></td>
			</tr>
			<tr>
			  <td width="50%" align="right" colspan="2"><strong>CVV Code:</strong></td>
			  <td width="50%" align="left" colspan="2"><?php
			if(@$encryptmethod!=""){
					if(is_array(@$cnumarr)) print htmlspecialchars(@$cnumarr[2]);
			} ?></td>
			</tr>
			<tr>
			  <td width="50%" align="right" colspan="2"><strong>Issue Number:</strong></td>
			  <td width="50%" align="left" colspan="2"><?php
			if(@$encryptmethod!=""){
					if(is_array(@$cnumarr)) print htmlspecialchars(@$cnumarr[3]);
			} ?></td>
			</tr>
<?php		if($ordCNum != "" && !$doedit){ ?>
		  <form method=POST action="adminorders.php?id=<?php print $_GET["id"]?>">
			<input type="hidden" name="delccdets" value="<?php print $_GET["id"]?>" />
			<tr>
			  <td width="100%" align="center" colspan="4"><input type=submit value="<?php print $yyDelCC?>" /></td>
			</tr>
		  </form>
<?php		}
		}
	}
}elseif($isinvoice && trim($alldata['ordInvoice']) != ''){ ?>
			<tr>
			  <td align="right" valign="top"><strong><?php print $yyInvNum?>:</strong></td>
			  <td align="left" colspan="3"><?php print editfunc($alldata['ordInvoice'],'ordInvoice',15)?></td>
			</tr>
<?php
} // isprinter ?>
			<tr>
			  <td width="100%" align="center" colspan="4">&nbsp;<br /></td>
			</tr>
		  </table>
<span id="productspan">
		  <table width="100%" border="1" cellspacing="0" cellpadding="4" bordercolor="#E7EAEF" bgcolor="">
			<tr>
			  <td><strong><?php print $xxPrId?></strong></td>
			  <td><strong><?php print $xxPrNm?></strong></td>
			  <td><strong><?php print $xxPrOpts?></strong></td>
			  <td><strong><?php print $xxQuant?></strong></td>
<?php	if(! $isprinter || $isinvoice) print '<td><strong>' . ($doedit ? $xxUnitPr : $xxPrice) . '</strong></td>';
		if($doedit) print '<td align="center"><strong>DEL</strong></td>' ?>
			</tr>
<?php
	$totoptpricediff = 0;
	if(mysql_num_rows($allorders)>0){
		$totoptpricediff = 0;
		$rowcounter=0;
		while($rsOrders = mysql_fetch_assoc($allorders)){
			$optpricediff = 0;
?>
			<tr>
			  <td valign="top" nowrap><?php if($doedit) print '<input type="button" value="..." onclick="updateoptions(' . $rowcounter . ')">&nbsp;<input type="hidden" name="cartid' . $rowcounter . '" value="' . str_replace('"','&quot;',$rsOrders["cartID"]) . '" />'?><strong><?php print editfunc($rsOrders["cartProdId"],'prodid' . $rowcounter,18)?></strong></td>
			  <td valign="top"><?php print editfunc(decodehtmlentities($rsOrders['cartProdName']),'prodname' . $rowcounter,24)?></td>
			  <td valign="top"><?php
			if($doedit) print '<span id="optionsspan' . $rowcounter . '">';
			$sSQL = "SELECT coOptGroup,coCartOption,coPriceDiff,coOptID,optGroup FROM cartoptions LEFT JOIN options ON cartoptions.coOptID=options.optID WHERE coCartID=" . $rsOrders["cartID"] . " ORDER BY coID";
			$result = mysql_query($sSQL) or print(mysql_error());
			if(mysql_num_rows($result) > 0){
				if($doedit) print '<table border="0" cellspacing="0" cellpadding="1" width="100%">';
				while($rs2 = mysql_fetch_array($result)){
					if($doedit){
						print '<tr><td align="right"><strong>' . $rs2["coOptGroup"] . ':</strong></td><td>';
						if(is_null($rs2["optGroup"])){
							print 'xxxxxx';
						}else{
							$sSQL="SELECT optID," . getlangid("optName",32) . ",optPriceDiff,optType,optFlags,optStock,optPriceDiff AS optDims FROM options INNER JOIN optiongroup ON options.optGroup=optiongroup.optGrpID WHERE optGroup=" . $rs2["optGroup"] . ' ORDER BY optID';
							$result2 = mysql_query($sSQL) or print(mysql_error());
							if($rsl = mysql_fetch_assoc($result2)){
								if(abs($rsl['optType'])==1 || abs($rsl['optType'])==2){
									print '<select onchange="dorecalc(true)" name="optn' . $rowcounter . '_' . $rs2["coOptID"] . '" id="optn' . $rowcounter . '_' . $rs2["coOptID"] . '" size="1">';
									do {
										print '<option value="' . $rsl["optID"] . "|" . (($rsl["optFlags"] & 1) == 1 ? ($rsOrders["cartProdPrice"]*$rsl["optPriceDiff"])/100.0 : $rsl["optPriceDiff"]) . '"';
										if($rsl["optID"]==$rs2["coOptID"]) print ' selected';
										print '>' . $rsl[getlangid("optName",32)];
										if((double)$rsl["optPriceDiff"] != 0){
											print ' ';
											if((double)$rsl["optPriceDiff"] > 0) print '+';
											if(($rsl["optFlags"] & 1) == 1)
												print number_format(($rsOrders["cartProdPrice"]*$rsl["optPriceDiff"])/100.0,2,'.','');
											else
												print number_format($rsl["optPriceDiff"],2,'.','');
										}
										print '</option>';
									} while($rsl = mysql_fetch_array($result2));
									print '</select>';
								}else{
									print "<input type='hidden' name='optn" . $rowcounter . '_' . $rs2["coOptID"] . "' value='" . $rsl["optID"] . "' /><textarea wrap='virtual' name='voptn" . $rowcounter . '_' . $rs2["coOptID"] . "' id='voptn". $rowcounter. '_' . $rs2["coOptID"] . "' cols='30' rows='3'>";
									print htmlspecialchars($rs2['coCartOption']) . '</textarea>';
								}
							}
						}
						print "</td></tr>";
					}else{
						print '<strong>' . $rs2["coOptGroup"] . ':</strong> ' . str_replace(array("\r\n","\n"),array('<br />','<br />'),htmlspecialchars($rs2['coCartOption'])) . '<br />';
					}
					if($doedit)
						$optpricediff += $rs2["coPriceDiff"];
					else
						$rsOrders["cartProdPrice"] += $rs2["coPriceDiff"];
				}
				if($doedit) print '</table>';
			}else{
				print ' - ';
			}
			mysql_free_result($result);
			if($doedit) print '</span>' ?></td>
			  <td valign="top"><?php print editfunc($rsOrders["cartQuantity"],'quant' . $rowcounter . '" onchange="dorecalc(true)',5)?></td>
<?php		if(! $isprinter || $isinvoice){ ?>
			  <td valign="top"><?php if($doedit) print editnumeric($rsOrders["cartProdPrice"],'price' . $rowcounter . '" onchange="dorecalc(true)',7); else print FormatEuroCurrency($rsOrders["cartProdPrice"]*$rsOrders["cartQuantity"])?>
<?php				if($doedit){
						print '<input type="hidden" id="optdiffspan' . $rowcounter . '" value="' . $optpricediff . '">';
						$totoptpricediff += ($optpricediff*$rsOrders["cartQuantity"]);
					}
			?></td>
<?php		}
			if($doedit) print '<td align="center"><input type="checkbox" name="del_' . $rowcounter . '" id="del_' . $rowcounter . '" value="yes" /></td>' ?>
			</tr>
<?php		$rowcounter++;
		}
	}
?>
<!--NEXTPRODUCTCOMMENT-->
<?php	if($doedit){ ?>
			<tr>
			  <td align="right" colspan="4">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<td align="center"><?php if($doedit) print '<input style="width:30px;" type="button" value="-" onclick="extraproduct(\'-\')"> ' . $yyMoProd . ' <input style="width:30px;" type="button" value="+" onclick="extraproduct(\'+\')"> &nbsp; <input type="button" value="' . $yyRecal . '" onclick="dorecalc(false)">'?></td>
					<td align="right"><strong>Options Total:</strong></td>
				  </tr>
				</table></td>
			  <td align="left" colspan="2"><span id="optdiffspan"><?php print number_format($totoptpricediff, 2, '.', '')?></span></td>
			</tr>
<?php	}
		if(! $isprinter || $isinvoice){
?>
			<tr>
			  <td align="right" colspan="4"><strong><?php print $xxOrdTot?>:</strong></td>
			  <td align="left"><?php print editnumeric($alldata["ordTotal"],"ordtotal",7)?></td>
<?php		if($doedit) print '<td align="center">&nbsp;</td>' ?>
			</tr>
<?php	if($isprinter && @$combineshippinghandling==TRUE){ ?>
			<tr>
			  <td align="right" colspan="4"><strong><?php print $xxShipHa?>:</strong></td>
			  <td align="left"><?php print FormatEuroCurrency($alldata['ordShipping']+$alldata['ordHandling'])?></td>
			</tr>
<?php	}else{
			if((double)$alldata["ordShipping"]!=0.0 || $doedit){ ?>
			<tr>
			  <td align="right" colspan="4"><strong><?php print $xxShippg?>:</strong></td>
			  <td align="left"><?php print editnumeric($alldata["ordShipping"],"ordShipping",7)?></td>
<?php			if($doedit) print '<td align="center">&nbsp;</td>' ?>
			</tr>
<?php		}
			if((double)$alldata["ordHandling"]!=0.0 || $doedit){ ?>
			<tr>
			  <td align="right" colspan="4"><strong><?php print $xxHndlg?>:</strong></td>
			  <td align="left"><?php print editnumeric($alldata["ordHandling"],"ordHandling",7)?></td>
<?php			if($doedit) print '<td align="center">&nbsp;</td>' ?>
			</tr>
<?php		}
		}
		if((double)$alldata["ordDiscount"]!=0.0 || $doedit){ ?>
			<tr>
			  <td align="right" colspan="4"><strong><?php print $xxDscnts?>:</strong></td>
			  <td align="left"><font color="#FF0000"><?php print editnumeric($alldata["ordDiscount"],"ordDiscount",7)?></font></td>
<?php		if($doedit) print '<td align="center">&nbsp;</td>' ?>
			</tr>
<?php	}
		if((double)$alldata["ordStateTax"]!=0.0 || $doedit){ ?>
			<tr>
			  <td align="right" colspan="4"><strong><?php print $xxStaTax?>:</strong></td>
			  <td align="left"><?php print editnumeric($alldata["ordStateTax"],"ordStateTax",7)?></td>
<?php		if($doedit) print '<td align="center" nowrap><input type="text" name="staterate" id="staterate" size="1" value="' . $statetaxrate . '">%</td>' ?>
			</tr>
<?php	}
		if((double)$alldata["ordCountryTax"]!=0.0 || $doedit){ ?>
			<tr>
			  <td align="right" colspan="4"><strong><?php print $xxCntTax?>:</strong></td>
			  <td align="left"><?php print editnumeric($alldata["ordCountryTax"],"ordCountryTax",7)?></td>
<?php		if($doedit) print '<td align="center" nowrap><input type="text" name="countryrate" id="countryrate" size="1" value="' . $countrytaxrate . '">%</td>' ?>
			</tr>
<?php	}
		if((double)$alldata["ordHSTTax"]!=0.0 || ($doedit && @$canadataxsystem)){ ?>
			<tr>
			  <td align="right" colspan="4"><strong><?php print $xxHST?>:</strong></td>
			  <td align="left"><?php print editnumeric($alldata["ordHSTTax"],"ordHSTTax",7)?></td>
<?php		if($doedit) print '<td align="center" nowrap><input type="text" name="hstrate" id="hstrate" size="1" value="' . $hsttaxrate . '">%</td>' ?>
			</tr>
<?php	} ?>
			<tr>
			  <td align="right" colspan="4"><strong><?php print $xxGndTot?>:</strong></td>
			  <td align="left"><span id="grandtotalspan"><?php print FormatEuroCurrency(($alldata["ordTotal"]+$alldata["ordStateTax"]+$alldata["ordCountryTax"]+$alldata["ordHSTTax"]+$alldata["ordShipping"]+$alldata["ordHandling"])-$alldata["ordDiscount"])?></span></td>
<?php		if($doedit) print '<td align="center">&nbsp;</td>' ?>
			</tr>
<?php	} // ! $isprinter || $isinvoice ?>
			</table>
</span>
		  </td>
		</tr>
<?php	if($isprinter && ! @isset($packingslipfooter)) $packingslipfooter=$invoicefooter;
		if($isinvoice && @$invoicefooter != ""){ ?>
		<tr><td width="100%"><?php print $invoicefooter?></td></tr>
<?php	}elseif($isprinter && @$packingslipfooter != ""){ ?>
		<tr><td width="100%"><?php print $packingslipfooter?></td></tr>
<?php	}elseif($doedit){ ?>
		<tr> 
          <td align="center" width="100%">&nbsp;<br /><input type="submit" value="<?php print $yyUpdate?>" /><br />&nbsp;</td>
		</tr>
<?php	} ?>
	  </table>
<?php
	if($doedit) print '</form>';
}else{
	$sSQL = "SELECT ordID FROM orders WHERE ordStatus=1";
	if(@$_POST["act"] != "purge") $sSQL .= " AND ordStatusDate<'" . date("Y-m-d H:i:s", time()-(3*60*60*24)) . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	while($rs = mysql_fetch_assoc($result)){
		$theid = $rs["ordID"];
		$delOptions = "";
		$addcomma = "";
		$result2 = mysql_query("SELECT cartID FROM cart WHERE cartOrderID=" . $theid) or print(mysql_error());
		while($rs2 = mysql_fetch_assoc($result2)){
			$delOptions .= $addcomma . $rs2["cartID"];
			$addcomma = ",";
		}
		if($delOptions != ""){
			$sSQL = "DELETE FROM cartoptions WHERE coCartID IN (" . $delOptions . ")";
			mysql_query($sSQL) or print(mysql_error());
		}
		mysql_query("DELETE FROM cart WHERE cartOrderID=" . $theid) or print(mysql_error());
		mysql_query("DELETE FROM orders WHERE ordID=" . $theid) or print(mysql_error());
	}
	if(@$_POST["act"]=="authorize"){
		do_stock_management(trim($_POST["id"]));
		if(trim($_POST["authcode"]) != "")
			$sSQL = "UPDATE orders set ordAuthNumber='" . mysql_escape_string(trim($_POST["authcode"])) . "',ordStatus=3 WHERE ordID=" . $_POST["id"];
		else
			$sSQL = "UPDATE orders set ordAuthNumber='" . mysql_escape_string($yyManAut) . "',ordStatus=3 WHERE ordID=" . $_POST["id"];
		mysql_query($sSQL) or print(mysql_error());
		mysql_query("UPDATE cart SET cartCompleted=1 WHERE cartOrderID=" . $_POST["id"]) or print(mysql_error());
	}elseif(@$_POST["act"]=="status"){
		$maxitems=(int)($_POST["maxitems"]);
		for($index=0; $index < $maxitems; $index++){
			$iordid = trim(@$_POST['ordid' . $index]);
			$ordstatus = trim(@$_POST['ordstatus' . $index]);
			$ordauthno = "";
			$oldordstatus=999;
			$payprovider=0;
			if($iordid != ''){
				$result = mysql_query("SELECT ordStatus,ordAuthNumber,ordEmail,ordDate,".getlangid("statPublic",64).",ordStatusInfo,ordName,ordTrackNum,ordPayProvider FROM orders INNER JOIN orderstatus ON orders.ordStatus=orderstatus.statID WHERE ordID=" . $iordid) or print(mysql_error());
				if($rs = mysql_fetch_assoc($result)){
					$oldordstatus=$rs["ordStatus"];
					$ordauthno=$rs["ordAuthNumber"];
					$ordemail=$rs["ordEmail"];
					$orddate=strtotime($rs["ordDate"]);
					$oldstattext=$rs[getlangid("statPublic",64)];
					$ordstatinfo=$rs["ordStatusInfo"];
					$ordername=$rs["ordName"];
					if(@$trackingnumtext == '') $trackingnumtext=$yyTrackT;
					if(trim($rs["ordTrackNum"]) != '') $trackingnum=str_replace('%s', $rs["ordTrackNum"], $trackingnumtext); else $trackingnum='';
					$payprovider=$rs['ordPayProvider'];
				}
			}
			if($payprovider != 20){
				if(! ($oldordstatus==999) && ($oldordstatus < 3 && $ordstatus >=3)){
					// This is to force stock management
					mysql_query("UPDATE cart SET cartCompleted=0 WHERE cartOrderID=" . $iordid) or print(mysql_error());
					do_stock_management($iordid);
					mysql_query("UPDATE cart SET cartCompleted=1 WHERE cartOrderID=" . $iordid) or print(mysql_error());
					if($ordauthno=="") mysql_query("UPDATE orders SET ordAuthNumber='". mysql_escape_string($yyManAut) . "' WHERE ordID=" . $iordid) or print(mysql_error());
				}
				if(! ($oldordstatus==999) && ($oldordstatus >=3 && $ordstatus < 3)) release_stock($iordid);
				if($iordid != "" && $ordstatus != ""){
					if($oldordstatus != (int)$ordstatus && @$_POST["emailstat"]=="1"){
						$result = mysql_query("SELECT ".getlangid("statPublic",64)." FROM orderstatus WHERE statID=" . $ordstatus);
						if($rs = mysql_fetch_assoc($result))
							$newstattext = $rs[getlangid('statPublic',64)];
						if(@$orderstatussubject != '') $emailsubject=$orderstatussubject; else $emailsubject = 'Order status updated';
						$ose = $orderstatusemail;
						$ose = str_replace('%orderid%', $iordid, $ose);
						$ose = str_replace('%orderdate%', date($dateformatstr, $orddate) . ' ' . date('H:i', $orddate), $ose);
						$ose = str_replace('%oldstatus%', $oldstattext, $ose);
						$ose = str_replace('%newstatus%', $newstattext, $ose);
						$thetime = time() + ($dateadjust*60*60);
						$ose = str_replace('%date%', date($dateformatstr, $thetime) . ' ' . date('H:i', $thetime), $ose);
						$ose = str_replace('%statusinfo%', $ordstatinfo, $ose);
						$ose = str_replace('%ordername%', $ordername, $ose);
						$ose = str_replace('%trackingnum%', $trackingnum, $ose);
						$ose = str_replace('%nl%', $emlNl, $ose);
						if(@$customheaders == ''){
							$customheaders = "MIME-Version: 1.0\n";
							$customheaders .= "From: %from% <%from%>\n";
							if(@$htmlemails==TRUE)
								$customheaders .= "Content-type: text/html; charset=".$emailencoding."\n";
							else
								$customheaders .= "Content-type: text/plain; charset=".$emailencoding."\n";
						}
						$headers = str_replace('%from%',$emailAddr,$customheaders);
						$headers = str_replace('%to%',$ordemail,$headers);
						mail($ordemail, $emailsubject, $ose, $headers);
					}
					if($oldordstatus != (int)$ordstatus) mysql_query("UPDATE orders SET ordStatus=" . $ordstatus . ",ordStatusDate='" . date("Y-m-d H:i:s", time() + ($dateadjust*60*60)) . "' WHERE ordID=" . $iordid) or print(mysql_error());
				}
			}
		}
	}
	if(@$_POST["sd"] != "")
		$sd = @$_POST["sd"];
	elseif(@$_GET["sd"] != "")
		$sd = @$_GET["sd"];
	else
		$sd = date($admindatestr, time() + ($dateadjust*60*60));
	if(@$_POST["ed"] != "")
		$ed = @$_POST["ed"];
	elseif(@$_GET["ed"] != "")
		$ed = @$_GET["ed"];
	else
		$ed = date($admindatestr, time() + ($dateadjust*60*60));
	$sd = parsedate($sd);
	$ed = parsedate($ed);
	if($sd > $ed) $ed = $sd;
	$fromdate = trim(@$_POST["fromdate"]);
	$todate = trim(@$_POST["todate"]);
	$ordid = trim(str_replace('"',"",str_replace("'","",@$_POST["ordid"])));
	$origsearchtext = trim(unstripslashes(@$_POST["searchtext"]));
	$searchtext = trim(mysql_escape_string(unstripslashes(@$_POST["searchtext"])));
	$ordstatus = "";
	if(@$_POST["powersearch"]=="1"){
		$sSQL = "SELECT ordID,ordName,payProvName,ordAuthNumber,ordDate,ordStatus,ordTotal-ordDiscount AS ordTot,ordTransID,ordAVS,ordCVV,ordPayProvider FROM orders INNER JOIN payprovider ON payprovider.payProvID=orders.ordPayProvider WHERE ordStatus>=0 ";
		$addcomma = "";
		if(is_array(@$_POST["ordstatus"])){
			foreach($_POST["ordstatus"] as $objValue){
				if(is_array($objValue))$objValue=$objValue[0];
				$ordstatus .= $addcomma . $objValue;
				$addcomma = ",";
			}
		}else
			$ordstatus = trim((string)@$_POST["ordstatus"]);
		if($ordid != ""){
			if(is_numeric($ordid)){
				$sSQL .= " AND ordID=" . $ordid;
			}else{
				$success=FALSE;
				$errmsg="The order id you specified seems to be invalid - " . $ordid;
				$sSQL .= " AND ordID=0";
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
				$sSQL .= " AND ordDate BETWEEN '" . date("Y-m-d", $sd) . "' AND '" . date("Y-m-d", $ed) . " 23:59:59'";
			}
			if($ordstatus != "" && strpos($ordstatus,"9999")===FALSE) $sSQL .= " AND ordStatus IN (" . $ordstatus . ")";
			if($searchtext != "") $sSQL .= " AND (ordTransID LIKE '%" . $searchtext . "%' OR ordAuthNumber LIKE '%" . $searchtext . "%' OR ordName LIKE '%" . $searchtext . "%' OR ordEmail LIKE '%" . $searchtext . "%' OR ordAddress LIKE '%" . $searchtext . "%' OR ordCity LIKE '%" . $searchtext . "%' OR ordState LIKE '%" . $searchtext . "%' OR ordZip LIKE '%" . $searchtext . "%' OR ordPhone LIKE '%" . $searchtext . "%' OR ordInvoice LIKE '%" . $searchtext . "%' OR ordAffiliate='" . $searchtext . "')";
		}
		$sSQL .= " ORDER BY ordID";
	}else{
		$sSQL = "SELECT ordID,ordName,payProvName,ordAuthNumber,ordDate,ordStatus,ordTotal-ordDiscount AS ordTot,ordTransID,ordAVS,ordCVV,ordPayProvider FROM orders LEFT JOIN payprovider ON payprovider.payProvID=orders.ordPayProvider WHERE ordStatus<>1 AND ordDate BETWEEN '" . date("Y-m-d", $sd) . "' AND '" . date("Y-m-d", $ed) . " 23:59:59' ORDER BY ordID";
	}
	$alldata = mysql_query($sSQL) or print(mysql_error());
	$hasdeleted=false;
	$sSQL = "SELECT COUNT(*) AS NumDeleted FROM orders WHERE ordStatus=1";
	$result = mysql_query($sSQL) or print(mysql_error());
	$rs = mysql_fetch_assoc($result);
	if($rs["NumDeleted"] > 0) $hasdeleted=true;
	mysql_free_result($result);
?>
<script language="javascript" type="text/javascript" src="popcalendar.js">
</script>
<script language="javascript" type="text/javascript">
<!--
function delrec(id) {
cmsg = "<?php print $yyConDel?>\n"
if (confirm(cmsg)) {
	document.mainform.id.value = id;
	document.mainform.act.value = "delete";
	document.mainform.sd.value="<?php print date($admindatestr, $sd)?>";
	document.mainform.ed.value="<?php print date($admindatestr, $ed)?>";
	document.mainform.submit();
}
}
function authrec(id) {
var aucode;
cmsg = "<?php print $yyEntAuth?>"
if ((aucode=prompt(cmsg,'<?php print $yyManAut?>'))!=null) {
	document.mainform.id.value = id;
	document.mainform.act.value = "authorize";
	document.mainform.authcode.value = aucode;
	document.mainform.sd.value="<?php print date($admindatestr, $sd)?>";
	document.mainform.ed.value="<?php print date($admindatestr, $ed)?>";
	document.mainform.submit();
}
}
function checkcontrol(tt,evt){
<?php if(strstr(@$_SERVER['HTTP_USER_AGENT'], 'Gecko')){ ?>
theevnt = evt;
return;
<?php }else{ ?>
theevnt=window.event;
<?php } ?>
if(theevnt.ctrlKey){
	maxitems=document.mainform.maxitems.value;
	for(index=0;index<maxitems;index++){
		isdisabled = eval('document.mainform.ordstatus'+index+'.disabled');
		if(! isdisabled){
			if(eval('document.mainform.ordstatus'+index+'.length') > tt.selectedIndex){
				eval('document.mainform.ordstatus'+index+'.selectedIndex='+tt.selectedIndex);
				eval('document.mainform.ordstatus'+index+'.options['+tt.selectedIndex+'].selected=true');
			}
		}
	}
}
}
function displaysearch(){
thestyle = document.getElementById('searchspan').style;
if(thestyle.display=='none')
	thestyle.display = 'block';
else
	thestyle.display = 'none';
}
function checkprinter(tt,evt){
<?php if(strstr(@$_SERVER['HTTP_USER_AGENT'], 'Gecko')){ ?>
if(evt.ctrlKey || evt.altKey || document.mainform.ctrlmod[document.mainform.ctrlmod.selectedIndex].value=="1"){
	tt.href += "&printer=true";
	window.location.href = tt.href;
}else if(document.mainform.ctrlmod[document.mainform.ctrlmod.selectedIndex].value=="3"){
	tt.href += "&invoice=true";
	window.location.href = tt.href;
}else if(document.mainform.ctrlmod[document.mainform.ctrlmod.selectedIndex].value=="2"){
	tt.href += "&doedit=true";
	window.location.href = tt.href;
}
<?php }else{ ?>
theevnt=window.event;
if(theevnt.ctrlKey || document.mainform.ctrlmod[document.mainform.ctrlmod.selectedIndex].value=="1")tt.href += "&printer=true";
if(document.mainform.ctrlmod[document.mainform.ctrlmod.selectedIndex].value=="3")tt.href += "&invoice=true";
if(document.mainform.ctrlmod[document.mainform.ctrlmod.selectedIndex].value=="2")tt.href += "&doedit=true";
<?php } ?>
return(true);
}
function setdumpformat(){
formatindex = document.forms.dumpform.filedump[document.forms.dumpform.filedump.selectedIndex].value;
if(formatindex==1)
	document.dumpform.act.value='dumporders';
else if(formatindex==2)
	document.dumpform.act.value='dumpdetails';
else if(formatindex==3)
	document.dumpform.act.value='quickbooks';
else if(formatindex==4)
	document.dumpform.act.value='ouresolutionsxmldump';
}
// -->
</script>
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="">
        <tr>
          <td width="100%" align="center">
<?php	$themask = 'yyyy-mm-dd';
		if($admindateformat==1)
			$themask='mm/dd/yyyy';
		elseif($admindateformat==2)
			$themask='dd/mm/yyyy';
		if(! $success) print "<p><font color='#FF0000'>" . $errmsg . "</font></p>"; ?>
			<span name="searchspan" id="searchspan" <?php if($usepowersearch) print 'style="display:block"'; else print 'style="display:none"'?>>
            <table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="">
			  <form method="post" action="adminorders.php" name="psearchform">
			  <input type="hidden" name="powersearch" value="1" />
			  <tr bgcolor="#030133"><td colspan="4"><strong><font color="#E7EAEF">&nbsp;<?php print $yyPowSea?></font></strong></td></tr>
			  <tr bgcolor="#E7EAEF"> 
                <td align="right" width="25%"><strong><?php print $yyOrdFro?>:</strong></td>
				<td align="left" width="25%">&nbsp;<input type="text" size="14" name="fromdate" value="<?php print $fromdate?>" /> <input type=button onclick="popUpCalendar(this, document.forms.psearchform.fromdate, '<?php print $themask?>', 0)" value='DP' /></td>
				<td align="right" width="25%"><strong><?php print $yyOrdTil?>:</strong></td>
				<td align="left" width="25%">&nbsp;<input type="text" size="14" name="todate" value="<?php print $todate?>" /> <input type=button onclick="popUpCalendar(this, document.forms.psearchform.todate, '<?php print $themask?>', -205)" value='DP' /></td>
			  </tr>
			  <tr bgcolor="#EAECEB">
				<td align="right"><strong><?php print $yyOrdId?>:</strong></td>
				<td align="left">&nbsp;<input type="text" size="14" name="ordid" value="<?php print $ordid?>" /></td>
				<td align="right"><strong><?php print $yySeaTxt?>:</strong></td>
				<td align="left">&nbsp;<input type="text" size="24" name="searchtext" value="<?php print $origsearchtext?>" /></td>
			  </tr>
			  <tr bgcolor="#E7EAEF">
				<td align="right"><strong><?php print $yyOrdSta?>:</strong></td>
				<td align="left">&nbsp;<select name="ordstatus[]" size="5" multiple><option value="9999" <?php if(strpos($ordstatus,"9999") !== FALSE) print "selected"?>><?php print $yyAllSta?></option><?php
						$ordstatus="";
						$addcomma = "";
						if(is_array(@$_POST["ordstatus"])){
							foreach($_POST["ordstatus"] as $objValue){
								if(is_array($objValue))$objValue=$objValue[0];
								$ordstatus .= $addcomma . $objValue;
								$addcomma = ",";
							}
						}else
							$ordstatus = trim(@$_POST["ordstatus"]);
						$ordstatusarr = explode(",", $ordstatus);
						for($index=0; $index < $numstatus; $index++){
							print '<option value="' . $allstatus[$index]["statID"] . '"';
							if(is_array($ordstatusarr)){
								foreach($ordstatusarr as $objValue)
									if($objValue==$allstatus[$index]["statID"]) print " selected";
							}
							print ">" . $allstatus[$index]["statPrivate"] . "</option>";
						} ?></select></td>
				<td colspan="2" align="center"><input type="checkbox" name="startwith" value="1" <?php if($usepowersearch) print "checked"?> /> <strong><?php print $yyStaPow?></strong><br /><br />
				  <input type="submit" value="<?php print $yySearch?>" /> <input type="button" value="Stats" onclick="document.forms.psearchform.action='adminstats.php';document.forms.psearchform.submit();" /></td>
			  </tr>
			  <tr><td colspan="4">&nbsp;</td></tr>
			  </form>
			</table>
			</span>
            <table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="">
			  <form method="post" action="adminorders.php">
			  <tr>
			    <td align="center"><input type="button" value="<?php print $yyPowSea?>" onclick="displaysearch()" /></td>
                <td align="center"><p><strong><?php print $yyShoFrm?>:</strong> <select name="sd" size="1"><?php
					$gotmatch=FALSE;
					$thetime = time() + ($dateadjust*60*60);
					$dayToday = date("d",$thetime);
					$monthToday = date("m",$thetime);
					$yearToday = date("Y",$thetime);
					for($index=$dayToday; $index > 0; $index--){
						$thedate = mktime(0, 0, 0, $monthToday, $index, $yearToday);
						$thedatestr = date($admindatestr, $thedate);
						print "<option value='" . $thedatestr . "'";
						if($thedate==$sd){
							print " selected";
							$gotmatch=TRUE;
						}
						print ">" . $thedatestr . "</option>\n";
					}
					for($index=1; $index<=12; $index++){
						$thedatestr = date($admindatestr, $thedate = mktime(0,0,0,date("m",$thetime)-$index,1,date("Y",$thetime)));
						if(! $gotmatch && $thedate < $sd){
							print "<option value='" . date($admindatestr, $sd) . "' selected>" . date($admindatestr, $sd) . "</option>";
							$gotmatch=TRUE;
						}
						print "<option value='" . $thedatestr . "'";
						if($thedate==$sd){
							print " selected";
							$gotmatch=TRUE;
						}
						print ">" . $thedatestr . "</option>\n";
					}
					if(!$gotmatch) print "<option value='" . date($admindatestr, $sd) . "' selected>" . date($admindatestr, $sd) . "</option>";
				?></select> <strong><?php print $yyTo?>:</strong> <select name="ed" size="1"><?php
					$gotmatch=FALSE;
					$dayToday = date("d",$thetime);
					$monthToday = date("m",$thetime);
					$yearToday = date("Y",$thetime);
					for($index=$dayToday; $index > 0; $index--){
						$thedate = mktime(0, 0, 0, $monthToday, $index, $yearToday);
						$thedatestr = date($admindatestr, $thedate);
						print "<option value='" . $thedatestr . "'";
						if($thedate==$ed){
							print " selected";
							$gotmatch=TRUE;
						}
						print ">" . $thedatestr . "</option>\n";
					}
					for($index=1; $index<=12; $index++){
						if(! $gotmatch && $thedate < $ed){
							print "<option value='" . date($admindatestr, $ed) . "' selected>" . date($admindatestr, $ed) . "</option>";
							$gotmatch=TRUE;
						}
						$thedatestr = date($admindatestr, $thedate = mktime(0,0,0,date("m",$thetime)-$index,1,date("Y",$thetime)));
						print "<option value='" . $thedatestr . "'";
						if($thedate==$ed){
							print " selected";
							$gotmatch=TRUE;
						}
						print ">" . $thedatestr . "</option>\n";
					}
					if(!$gotmatch) print "<option value='" . date($admindatestr, $sd) . "' selected>" . date($admindatestr, $sd) . "</option>";
				?></select> <input type="submit" value="Go" /></td>
			  </tr>
			  <tr><td colspan="2">&nbsp;</td></tr>
			  </form>
			</table>
			<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="">
			  <tr bgcolor="#030133"> 
                <td align="center"><strong><font color="#E7EAEF"><?php print $yyOrdId?></font></strong></td>
				<td align="center"><strong><font color="#E7EAEF"><?php print $yyName?></font></strong></td>
				<td align="center"><strong><font color="#E7EAEF"><?php print $yyMethod?></font></strong></td>
				<td width="1%"><strong><font color="#E7EAEF">AVS</font></strong></td>
				<td width="1%"><strong><font color="#E7EAEF">CVV</font></strong></td>
				<td align="center"><strong><font color="#E7EAEF"><?php print $yyAutCod?></font></strong></td>
				<td align="center"><strong><font color="#E7EAEF"><?php print $yyDate?></font></strong></td>
				<td align="center"><strong><font color="#E7EAEF"><?php print $yyStatus?></font></strong></td>
			  </tr>
			  <form method="post" name="mainform" action="adminorders.php">
			  <?php if(@$_POST["powersearch"]=="1"){ ?>
			  <input type="hidden" name="powersearch" value="1" />
			  <input type="hidden" name="fromdate" value="<?php print trim(@$_POST["fromdate"])?>" />
			  <input type="hidden" name="todate" value="<?php print trim(@$_POST["todate"])?>" />
			  <input type="hidden" name="ordid" value="<?php print trim(str_replace('"','',str_replace("'",'',@$_POST["ordid"])))?>" />
			  <input type="hidden" name="origsearchtext" value="<?php print trim(str_replace('"','&quot;',@$_POST["searchtext"]))?>" />
			  <input type="hidden" name="searchtext" value="<?php print trim(str_replace('"',"&quot;",@$_POST["searchtext"]))?>" />
			  <input type="hidden" name="ordstatus[]" value="<?php print $ordstatus?>" />
			  <input type="hidden" name="startwith" value="<?php if($usepowersearch) print "1"?>" />
			  <?php } ?>
			  <input type="hidden" name="act" value="xxx" />
			  <input type="hidden" name="id" value="xxx" />
			  <input type="hidden" name="authcode" value="xxx" />
			  <input type="hidden" name="ed" value="<?php print date($admindatestr, $ed)?>" />
			  <input type="hidden" name="sd" value="<?php print date($admindatestr, $sd)?>" />
<?php
	if(mysql_num_rows($alldata) > 0){
		$rowcounter=0;
		$ordTot=0;
		while($rs = mysql_fetch_assoc($alldata)){
			if($rs['ordStatus']>=3) $ordTot += $rs['ordTot'];
			if($rs['ordAuthNumber']=='' || is_null($rs['ordAuthNumber'])){
				$startfont='<font color="#FF0000">';
				$endfont='</font>';
			}else{
				$startfont='';
				$endfont='';
			}
			if(@$bgcolor=='#E7EAEF') $bgcolor='#EAECEB'; else $bgcolor='#E7EAEF';
?>			  <tr bgcolor="<?php print $bgcolor?>">
				<td align="center"><a onclick="return(checkprinter(this,event));" href="adminorders.php?id=<?php print $rs['ordID']?>"><?php print '<strong>' . $startfont . $rs['ordID'] . $endfont . '</strong>'?></a></td>
				<td align="center"><a onclick="return(checkprinter(this,event));" href="adminorders.php?id=<?php print $rs['ordID']?>"><?php print $startfont . htmlspecialchars($rs['ordName']) . $endfont?></a></td>
				<td align="center"><?php print $startfont . htmlspecialchars($rs['payProvName']) . ($rs['payProvName']=='PayPal' && trim($rs['ordTransID']) != '' ? ' CC' : '') . $endfont?></td>
				<td width="1%"><?php if(trim($rs['ordAVS']) != '') print htmlspecialchars($rs['ordAVS']); else print '&nbsp;' ?></td>
				<td width="1%"><?php if(trim($rs['ordCVV']) != '') print htmlspecialchars($rs['ordCVV']); else print '&nbsp;' ?></td>
				<td align="center"><?php
					if($rs['ordAuthNumber']=='' || is_null($rs['ordAuthNumber'])){
						$isauthorized=FALSE;
						print '<input type="button" name="auth" value="' . $yyAuthor . '" onclick="authrec(\'' . $rs['ordID'] . '\')" />';
					}else{
						print '<a href="#" title="' . FormatEuroCurrency($rs['ordTot']) . '" onclick="authrec(\'' . $rs['ordID'] . '\');return(false);">' . $startfont . $rs['ordAuthNumber'] . $endfont . '</a>';
						$isauthorized=TRUE;
					}
				?></td>
				<td align="center"><font size="1"><?php print $startfont . date($admindatestr . "\<\\b\\r\>H:i:s", strtotime($rs["ordDate"])) . $endfont?></font></td>
				<td align="center"><input type="hidden" name="ordid<?php print $rowcounter?>" value="<?php print $rs["ordID"]?>" /><select name="ordstatus<?php print $rowcounter?>" size="1" onchange="checkcontrol(this,event)"<?php if($rs['ordPayProvider']==20) print ' disabled'?>><?php
						$gotitem=FALSE;
						for($index=0; $index<$numstatus; $index++){
							if(! $isauthorized && $allstatus[$index]["statID"]>2) break;
							if(! ($rs["ordStatus"] != 2 && $allstatus[$index]["statID"]==2)){
								print '<option value="' . $allstatus[$index]["statID"] . '"';
								if($rs["ordStatus"]==$allstatus[$index]["statID"]){
									print " selected";
									$gotitem=TRUE;
								}
								print ">" . $allstatus[$index]["statPrivate"] . "</option>";
							}
						}
						if(! $gotitem) print '<option value="" selected>' . $yyUndef . '</option>' ?></select></td>
			  </tr>
<?php		$rowcounter++;
			if($rowcounter>=250){
				print "<tr><td colspan='8' align='center'><strong>Limit of " . $rowcounter . " orders reached. Please refine your search.</strong></td></tr>";
				break;
			}
		} ?>
			  <tr>
				<td align="center"><?php print FormatEuroCurrency($ordTot)?></td>
				<td align="center"><?php if($hasdeleted){ ?><input type="submit" value="<?php print $yyPurDel?>" onclick="document.mainform.act.value='purge';" /><?php } ?></td><td colspan="5"><select name="ctrlmod" size="1"><option value="0"><?php print $yyVieDet?></option><option value="1"><?php print $yyPPSlip?></option><option value="3"><?php print $yyPPInv?></option><option value="2"><?php print $yyEdOrd?></option></select>
				&nbsp;&nbsp;&nbsp;<?php if(@$orderstatusemail != ""){ ?><input type="checkbox" name="emailstat" value="1" <?php if(@$_POST["emailstat"]=="1" || @$alwaysemailstatus==TRUE) print "checked"?>/> <?php print $yyEStat?><?php } ?></td>
				<td align="center"><input type="hidden" name="maxitems" value="<?php print $rowcounter?>" /><input type="submit" value="<?php print $yyUpdate?>" onclick="document.mainform.act.value='status';" /> <input type="reset" value="<?php print $yyReset?>" /></td>
			  </tr>
			  </form>
			  <form method="post" action="dumporders.php" name="dumpform">
<?php	if(@$_POST["powersearch"]=="1"){ ?>
			  <input type="hidden" name="powersearch" value="1" />
			  <input type="hidden" name="fromdate" value="<?php print trim(@$_POST["fromdate"])?>" />
			  <input type="hidden" name="todate" value="<?php print trim(@$_POST["todate"])?>" />
			  <input type="hidden" name="ordid" value="<?php print trim(str_replace('"','',str_replace("'",'',@$_POST["ordid"])))?>" />
			  <input type="hidden" name="origsearchtext" value="<?php print trim(str_replace('"','&quot;',@$_POST["searchtext"]))?>" />
			  <input type="hidden" name="searchtext" value="<?php print trim(str_replace('"',"&quot;",@$_POST["searchtext"]))?>" />
			  <input type="hidden" name="ordstatus[]" value="<?php print $ordstatus?>" />
			  <input type="hidden" name="startwith" value="<?php if($usepowersearch) print "1"?>" />
<?php	} ?>
			  <input type="hidden" name="sd" value="<?php print date($admindatestr, $sd)?>" />
			  <input type="hidden" name="ed" value="<?php print date($admindatestr, $ed)?>" />
			  <input type="hidden" name="act" value="" />
			  <tr> 
                <td colspan="8" align="center"><select name="filedump" size="1">
					<option value="1"><?php print $yyDmpOrd?></option>
					<option value="2"><?php print $yyDmpDet?></option>
<?php	if(@$ouresolutionsxml != '') print '<option value="4">OurESolutions XML format</option>'; ?>
					</select> <input type="submit" value="<?php print $yySubmit?>" onclick="setdumpformat()" /></td>
			  </tr>
			  </form>
<?php
	}else{
?>
			  <tr> 
                <td width="100%" colspan="8" align="center">
					<p><?php
					if(@$_POST['powersearch']=='1')
						print $yyNoMat1;
					elseif($sd==$ed)
						print $yyNoMat2 . ' ' . date($admindatestr, $sd) . '.';
					else
						print $yyNoMat3 . ' ' . date($admindatestr, $sd) . ' '.$yyAnd.' ' . date($admindatestr, $ed) . '.';
					?></p>
				</td>
			  </tr>
			  <?php if($hasdeleted){ ?>
			  <tr> 
				<td colspan="8"><input type="submit" value="<?php print $yyPurDel?>" onclick="document.mainform.act.value='purge';" /></td>
			  </tr>
			  <?php } ?>
			  </form>
<?php
	} ?>
			  <tr> 
                <td width="100%" colspan="8" align="center">
				  <p><br />
					<a href="adminorders.php?sd=<?php print date($admindatestr,mktime(0,0,0,date("m",$sd)-1,date("d",$sd),date("Y",$sd)))?>&ed=<?php print date($admindatestr,mktime(0,0,0,date("m",$ed)-1,date("d",$ed),date("Y",$ed)))?>"><strong>- <?php print $yyMonth?></strong></a> | 
					<a href="adminorders.php?sd=<?php print date($admindatestr,mktime(0,0,0,date("m",$sd),date("d",$sd)-7,date("Y",$sd)))?>&ed=<?php print date($admindatestr,mktime(0,0,0,date("m",$ed),date("d",$ed)-7,date("Y",$ed)))?>"><strong>- <?php print $yyWeek?></strong></a> | 
					<a href="adminorders.php?sd=<?php print date($admindatestr,mktime(0,0,0,date("m",$sd),date("d",$sd)-1,date("Y",$sd)))?>&ed=<?php print date($admindatestr,mktime(0,0,0,date("m",$ed),date("d",$ed)-1,date("Y",$ed)))?>"><strong>- <?php print $yyDay?></strong></a> | 
					<a href="adminorders.php"><strong><?php print $yyToday?></strong></a> | 
					<a href="adminorders.php?sd=<?php print date($admindatestr,mktime(0,0,0,date("m",$sd),date("d",$sd)+1,date("Y",$sd)))?>&ed=<?php print date($admindatestr,mktime(0,0,0,date("m",$ed),date("d",$ed)+1,date("Y",$ed)))?>"><strong><?php print $yyDay?> +</strong></a> | 
					<a href="adminorders.php?sd=<?php print date($admindatestr,mktime(0,0,0,date("m",$sd),date("d",$sd)+7,date("Y",$sd)))?>&ed=<?php print date($admindatestr,mktime(0,0,0,date("m",$ed),date("d",$ed)+7,date("Y",$ed)))?>"><strong><?php print $yyWeek?> +</strong></a> | 
					<a href="adminorders.php?sd=<?php print date($admindatestr,mktime(0,0,0,date("m",$sd)+1,date("d",$sd),date("Y",$sd)))?>&ed=<?php print date($admindatestr,mktime(0,0,0,date("m",$ed),date("d",$ed)+1,date("Y",$ed)))?>"><strong><?php print $yyMonth?> +</strong></a>
				  </p>
				</td>
			  </tr>
			</table>
		  </td>
		</tr>
      </table>
<?php
}
}
?>