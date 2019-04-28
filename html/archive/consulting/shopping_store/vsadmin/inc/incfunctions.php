<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
$incfunctionsdefined=TRUE;
@set_magic_quotes_runtime(0);
$magicq = (get_magic_quotes_gpc()==1);
if(@$emailencoding=='') $emailencoding='iso-8859-1';
if(@$adminencoding=='') $adminencoding='iso-8859-1';
if(@$_SESSION["languageid"] != "") $languageid=$_SESSION["languageid"];
function getadminsettings(){
	global $alreadygotadmin,$splitUSZones,$adminLocale,$countryCurrency,$orcurrencyisosymbol,$useEuro,$storeurl,$stockManage,$useStockManagement,$adminProdsPerPage,$countryTax,$countryTaxRate,$delccafter,$handling,$adminCanPostUser,$packtogether,$origZip,$shipType,$adminIntShipping,$origCountry,$origCountryCode,$uspsUser,$uspsPw,$upsUser,$upsPw,$upsAccess,$fedexaccount,$fedexmeter,$adminUnits,$emailAddr,$sendEmail,$adminTweaks,$adminlanguages,$adminlangsettings,$currRate1,$currSymbol1,$currRate2,$currSymbol2,$currRate3,$currSymbol3,$currConvUser,$currConvPw,$currLastUpdate;
	if(! @$alreadygotadmin){
		$sSQL = "SELECT adminEmail,adminEmailConfirm,adminTweaks,adminProdsPerPage,adminStoreURL,adminHandling,adminPacking,adminDelCC,adminUSZones,adminStockManage,adminShipping,adminIntShipping,adminCanPostUser,adminZipCode,adminUnits,adminUSPSUser,adminUSPSpw,adminUPSUser,adminUPSpw,adminUPSAccess,FedexAccountNo,FedexMeter,adminlanguages,adminlangsettings,currRate1,currSymbol1,currRate2,currSymbol2,currRate3,currSymbol3,currConvUser,currConvPw,currLastUpdate,countryLCID,countryCurrency,countryName,countryCode,countryTax FROM admin LEFT JOIN countries ON admin.adminCountry=countries.countryID WHERE adminID=1";
		$result = mysql_query($sSQL) or print(mysql_error());
		$rs = mysql_fetch_array($result);
		$splitUSZones = ((int)$rs['adminUSZones']==1);
		$adminLocale = $rs['countryLCID'];
		$countryCurrency = $rs['countryCurrency'];
		if(@$orcurrencyisosymbol != '') $countryCurrency=$orcurrencyisosymbol;
		$useEuro = ($rs['countryCurrency']=='EUR');
		$storeurl = $rs['adminStoreURL'];
		$stockManage = (int)$rs['adminStockManage'];
		$useStockManagement = ($stockManage != 0);
		$adminProdsPerPage = $rs['adminProdsPerPage'];
		$countryTax=(double)$rs['countryTax'];
		$countryTaxRate=(double)$rs['countryTax'];
		$delccafter = (int)$rs['adminDelCC'];
		$handling=(double)$rs['adminHandling'];
		$adminCanPostUser=trim($rs['adminCanPostUser']);
		$packtogether = ((int)$rs['adminPacking']==1);
		$origZip = $rs['adminZipCode'];
		$shipType=(int)$rs['adminShipping'];
		$adminIntShipping=(int)$rs['adminIntShipping'];
		$origCountry = $rs['countryName'];
		$origCountryCode = $rs['countryCode'];
		$uspsUser = $rs['adminUSPSUser'];
		$uspsPw = $rs['adminUSPSpw'];
		$upsUser = upsdecode($rs['adminUPSUser'], '');
		$upsPw = upsdecode($rs['adminUPSpw'], '');
		$upsAccess = $rs['adminUPSAccess'];
		$fedexaccount = $rs['FedexAccountNo'];
		$fedexmeter = $rs['FedexMeter'];
		$adminUnits = (int)$rs['adminUnits'];
		$emailAddr = $rs['adminEmail'];
		$sendEmail = ((int)$rs['adminEmailConfirm']==1);
		$adminTweaks = (int)$rs['adminTweaks'];
		$adminlanguages = (int)$rs['adminlanguages'];
		$adminlangsettings = (int)$rs['adminlangsettings'];
		$currRate1=(double)$rs['currRate1'];
		$currSymbol1=trim($rs['currSymbol1']);
		$currRate2=(double)$rs['currRate2'];
		$currSymbol2=trim($rs['currSymbol2']);
		$currRate3=(double)$rs['currRate3'];
		$currSymbol3=trim($rs['currSymbol3']);
		$currConvUser=$rs['currConvUser'];
		$currConvPw=$rs['currConvPw'];
		$currLastUpdate=$rs['currLastUpdate'];
		mysql_free_result($result);
	}
	// Overrides
	global $orstoreurl,$oremailaddr;
	if(@$orstoreurl != '') $storeurl=$orstoreurl;
	if((substr(strtolower($storeurl),0,7) != "http://") && (substr(strtolower($storeurl),0,8) != "https://"))
		$storeurl = "http://" . $storeurl;
	if(substr($storeurl,-1) != "/") $storeurl .= "/";
	if(@$oremailaddr != "") $emailAddr=$oremailaddr;
	return(TRUE);
}
function cleanforurl($surl){
global $urlfillerchar;
if(! @isset($urlfillerchar)) $urlfillerchar = '_';
$surl = str_replace(' ',$urlfillerchar,strtolower(strip_tags($surl)));
return(preg_replace('/[^a-z\\'.$urlfillerchar.'0-9]/','',$surl));
}
function vrxmlencode($xmlstr){
	return str_replace(array('&','"',"'",'<','>','’'),array('&amp;','&quot;','&apos;','&lt;','&gt;','&apos;'),$xmlstr);
}
function xmlencodecharref($xmlstr){
	$xmlstr = str_replace(array('&reg;','&','<','>','®'),array('','&#x26;','&#x3c;','&#x3e;',''),$xmlstr);
	$tmp_str="";
	for($i=0; $i < strlen($xmlstr); $i++){
		$ch_code=ord(substr($xmlstr,$i,1));
		if($ch_code<=130) $tmp_str .= substr($xmlstr,$i,1);
	}
	return($tmp_str);
}
function getlangid($col, $bfield){
	global $languageid, $adminlangsettings;
	if(@$languageid=="" || @$languageid==1){
		return($col);
	}else{
		if(($adminlangsettings & $bfield) != $bfield) return($col);
	}
	return($col . $languageid);
}
function parsedate($tdat){
	global $admindateformat;
	if($admindateformat==0)
		list($year, $month, $day) = sscanf($tdat, "%d-%d-%d");
	elseif($admindateformat==1)
		list($month, $day, $year) = sscanf($tdat, "%d/%d/%d");
	elseif($admindateformat==2)
		list($day, $month, $year) = sscanf($tdat, "%d/%d/%d");
	if(! is_numeric($year))
		$year = date("Y");
	elseif((int)$year < 39)
		$year = (int)$year + 2000;
	elseif((int)$year < 100)
		$year = (int)$year + 1900;
	if($year < 1970 || $year > 2038) $year = date("Y");
	if(! is_numeric($month))
		$month = date("m");
	if(! is_numeric($day))
		$day = date("d");
	return(mktime(0, 0, 0, $month, $day, $year));
}
function unstripslashes($slashedText){
	global $magicq;
	if($magicq)
		return stripslashes($slashedText);
	else
		return $slashedText;
}
function getattributes($attlist,$attid){
	$pos = strpos($attlist, $attid.'=');
	if($pos === false)
		return '';
	$pos += strlen($attid) + 1;
	$quote = $attlist[$pos];
	$pos2 = strpos($attlist, $quote, $pos + 1);
	$retstr = substr($attlist, $pos + 1, $pos2 - ($pos + 1));
	return($retstr); 
}
class vrNodeList{
	var $length;
	var $childNodes;
	var $nodeName;
	var $nodeValue;
	var $attributes;

	function createNodeList($xmlStr){
		$xLen = strlen($xmlStr);
		for($i=0; $i < $xLen; $i++){
			if(substr($xmlStr, $i, 1)=='<' && substr($xmlStr, $i+1, 1) != '/' && substr($xmlStr, $i+1, 1) != '?'){ // Got a tag
				$j = strpos($xmlStr,'>',$i);
				$l = strpos($xmlStr,' ',$i);
				if(is_integer($l) && $l < $j){
					$this->nodeName[$this->length]=substr($xmlStr,$i+1,$l-($i+1));
					$this->attributes[$this->length] = substr($xmlStr,$l+1,($j-$l)-1);
				}else
					$this->nodeName[$this->length]=substr($xmlStr,$i+1,$j-($i+1));
				// print 'Got Node: ' . $this->nodeName[$this->length] . "<br />\n";
				$k = $i+1;
				$nodeNameLen=strlen($this->nodeName[$this->length]);
				if(substr($xmlStr, $j-1, 1)=='/'){
					$this->nodeValue[$this->length]=null;
				}else{
					$currLev=0;
					while($k < $xLen && $currLev >= 0){
						if(substr($xmlStr, $k, 2)=='</'){
							if($currLev==0 && substr($xmlStr, $k+2, $nodeNameLen)==$this->nodeName[$this->length])
								break;
							$currLev--;
						}elseif(substr($xmlStr, $k, 1)=='<')
							$currLev++;
						elseif(substr($xmlStr, $k, 2)=='/>')
							$currLev--;
						$k++;
					}
					$this->nodeValue[$this->length]=substr($xmlStr,$j+1,$k-($j+1));
				}
				// print 'Got Value: xxx' . str_replace('<','<br />&lt;',$this->nodeValue[$this->length]) . "xxx<br />\n";
				$this->childNodes[$this->length] = new vrNodeList($this->nodeValue[$this->length]);
				$this->length++;
				$i = $k;
			}
		}
	}
	function vrNodeList($xmlStr){
		$this->length=0;
		$this->childNodes='';
		$this->createNodeList($xmlStr);
	}
	function getValueByTagName($tagname){
		for($i=0; $i < $this->length; $i++){
			//print "name: " . $this->nodeName[$i] . ", " . $this->nodeValue[$i] . "<br>";
			if($this->nodeName[$i]==$tagname){
				return($this->nodeValue[$i]);
			}else{
				if($this->childNodes!=''){
					if(($retval = $this->childNodes[$i]->getValueByTagName($tagname)) != null)
						return($retval);
				}
			}
		}
		return(null);
	}
	function getAttributeByTagName($tagname, $attrib){
		for($i=0; $i < $this->length; $i++){
			if($this->nodeName[$i]==$tagname){
				return(getattributes($this->attributes[$i], $attrib));
			}else{
				if($this->childNodes!=''){
					if(($retval = $this->childNodes[$i]->getAttributeByTagName($tagname, $attrib)) != null)
						return($retval);
				}
			}
		}
		return(null);
	}
}
class vrXMLDoc{
	var $tXMLStr;
	var $nodeList;
	function vrXMLDoc($xmlStr){
		$this->tXMLStr = $xmlStr;
		$this->nodeList = new vrNodeList($xmlStr);
	}
	function getElementsByTagName($tagname){
		$currlevel=0;
		$taglen = strlen($tagname);
	}
}
$codestr="2952710692840328509902143349209039553396765";
function upsencode($thestr, $propcodestr){
	global $codestr;
	if($propcodestr=="") $localcodestr=$codestr; else $localcodestr=$propcodestr;
	$newstr="";
	for($index=0; $index < strlen($localcodestr); $index++){
		$thechar = substr($localcodestr,$index,1);
		if(! is_numeric($thechar)){
			$thechar = ord($thechar) % 10;
		}
		$newstr .= $thechar;
	}
	$localcodestr = $newstr;
	while(strlen($localcodestr) < 40)
		$localcodestr .= $localcodestr;
	$newstr="";
	for($index=0; $index < strlen($thestr); $index++){
		$thechar = substr($thestr,$index,1);
		$newstr .= chr(ord($thechar)+(int)substr($localcodestr,$index,1));
	}
	return $newstr;
}
function upsdecode($thestr, $propcodestr){
	global $codestr;
	if($propcodestr=="") $localcodestr=$codestr; else $localcodestr=$propcodestr;
	$newstr="";
	for($index=0; $index < strlen($localcodestr); $index++){
		$thechar = substr($localcodestr,$index,1);
		if(! is_numeric($thechar)){
			$thechar = ord($thechar) % 10;
		}
		$newstr .= $thechar;
	}
	$localcodestr = $newstr;
	while(strlen($localcodestr) < 40)
		$localcodestr .= $localcodestr;
	if(is_null($thestr)){
		return "";
	}else{
		$newstr="";
		for($index=0; $index < strlen($thestr); $index++){
			$thechar = substr($thestr,$index,1);
			$newstr .= chr(ord($thechar)-(int)substr($localcodestr,$index,1));
		}
		return($newstr);
	}
}
$locale_info = "";
function FormatEuroCurrency($amount){
	global $useEuro, $adminLocale, $locale_info, $overridecurrency, $orcsymbol, $orcdecplaces, $orcdecimals, $orcthousands, $orcpreamount;
	if(@$overridecurrency==TRUE){
		if($orcpreamount)
			return $orcsymbol . number_format($amount,$orcdecplaces,$orcdecimals,$orcthousands);
		else
			return number_format($amount,$orcdecplaces,$orcdecimals,$orcthousands) . $orcsymbol;
	}else{
		if(! is_array($locale_info)){
			setlocale(LC_MONETARY,$adminLocale);
			$locale_info = localeconv();
			setlocale(LC_MONETARY,"en_US");
		}
		if($useEuro)
			return number_format($amount,2,$locale_info["decimal_point"],$locale_info["thousands_sep"]) . " &euro;";
		else
			return $locale_info["currency_symbol"] . number_format($amount,2,$locale_info["decimal_point"],$locale_info["thousands_sep"]);
	}
}
function FormatEmailEuroCurrency($amount){
	global $useEuro, $adminLocale, $locale_info, $overridecurrency, $orcemailsymbol, $orcdecplaces, $orcdecimals, $orcthousands, $orcpreamount;
	if(@$overridecurrency==TRUE){
		if($orcpreamount)
			return $orcemailsymbol . number_format($amount,$orcdecplaces,$orcdecimals,$orcthousands);
		else
			return number_format($amount,$orcdecplaces,$orcdecimals,$orcthousands) . $orcemailsymbol;
	}else{
		if(! is_array($locale_info)){
			setlocale(LC_ALL,$adminLocale);
			$locale_info = localeconv();
			setlocale(LC_ALL,"en_US");
		}
		if($useEuro)
			return number_format($amount,2,$locale_info["decimal_point"],$locale_info["thousands_sep"]) . " Euro";
		else
			return $locale_info["currency_symbol"] . number_format($amount,2,$locale_info["decimal_point"],$locale_info["thousands_sep"]);
	}
}
if(trim(@$_GET["PARTNER"]) != "" || trim(@$_GET["REFERER"]) != ""){
	if(@$expireaffiliate == "") $expireaffiliate=30;
	if(trim(@$_GET["PARTNER"])!="") $thereferer=trim(@$_GET["PARTNER"]); else $thereferer=trim(@$_GET["REFERER"]);
	print "<script src='vsadmin/savecookie.php?PARTNER=" . $thereferer . "&EXPIRES=" . $expireaffiliate . "'></script>";
}
$stockManage=0;
function do_stock_management($smOrdId){
	global $stockManage;
	if($stockManage != 0){
		$sSQL="SELECT cartID,cartProdID,cartQuantity,pStockByOpts FROM cart INNER JOIN products ON cart.cartProdID=products.pID WHERE (cartCompleted=0 OR cartCompleted=2) AND cartOrderID='" . mysql_escape_string(unstripslashes($smOrdId)) . "'";
		$result1 = mysql_query($sSQL) or print(mysql_error());
		while($rs1 = mysql_fetch_array($result1)){
			if((int)$rs1["pStockByOpts"] != 0){
				$sSQL = "SELECT coOptID FROM cartoptions INNER JOIN options ON cartoptions.coOptID=options.optID INNER JOIN optiongroup ON options.optGroup=optiongroup.optGrpID WHERE optType IN (-2,-1,1,2) AND coCartID=" . $rs1["cartID"];
				$result2 = mysql_query($sSQL) or print(mysql_error());
				while($rs2 = mysql_fetch_array($result2)){
					$sSQL = "UPDATE options SET optStock=optStock-" . $rs1["cartQuantity"] . " WHERE optID=" . $rs2["coOptID"];
					mysql_query($sSQL) or print(mysql_error());
				}
				mysql_free_result($result2);
			}else{
				$sSQL = "UPDATE products SET pInStock=pInStock-" . $rs1["cartQuantity"] . " WHERE pID='" . $rs1["cartProdID"] . "'";
				mysql_query($sSQL) or print(mysql_error());
			}
		}
		mysql_free_result($result1);
	}
}
function productdisplayscript($doaddprodoptions){
global $prodoptions, $countryTaxRate, $xxPrdEnt, $xxPrdChs, $xxPrd255, $xxOptOOS, $useStockManagement, $prodlist, $OWSP;
global $currSymbol1,$currFormat1,$currSymbol2,$currFormat2,$currSymbol3,$currFormat3,$pricecheckerisincluded;
if($currSymbol1!="" && $currFormat1=="") $currFormat1='%s <strong>' . $currSymbol1 . '</strong>';
if($currSymbol2!="" && $currFormat2=="") $currFormat2='%s <strong>' . $currSymbol2 . '</strong>';
if($currSymbol3!="" && $currFormat3=="") $currFormat3='%s <strong>' . $currSymbol3 . '</strong>';
?>
<script language="javascript" type="text/javascript">
<!--
<?php	if(! (@$pricecheckerisincluded==TRUE)){ ?>
var aPC = new Array();<?php
			if($useStockManagement){ ?>
var aPS = new Array();
function checkStock(x,i){
if(i!='' && aPS[i] > 0)return(true);
alert('<?php print str_replace("'","\'",$xxOptOOS)?>');
x.focus();return(false);
}<?php		} ?>
var isW3 = (document.getElementById&&true);
var tax=<?php print $countryTaxRate ?>;
function dummyfunc(){};
function pricechecker(i){
if(i!='')return(aPC[i]);return(0);}
function enterValue(x){
alert('<?php print str_replace("'","\'",$xxPrdEnt)?>');
x.focus();return(false);}
function chooseOption(x){
alert('<?php print str_replace("'","\'",$xxPrdChs)?>');
x.focus();return(false);}
function dataLimit(x){
alert('<?php print str_replace("'","\'",$xxPrd255)?>');
x.focus();return(false);}
function formatprice(i, currcode, currformat){
<?php
	$tempStr = FormatEuroCurrency(0);
	$tempStr2 = number_format(0,2,".",",");
	print "var pTemplate='" . $tempStr . "';\n";
	print "if(currcode!='') pTemplate=' " . $tempStr2 . "' + (currcode!=' '?'<strong>'+currcode+'<\/strong>':'');";
	if(strstr($tempStr,",") || strstr($tempStr,".")){ ?>
if(currcode==' JPY')i = Math.round(i).toString();
else if(i==Math.round(i))i=i.toString()+".00";
else if(i*10.0==Math.round(i*10.0))i=i.toString()+"0";
else if(i*100.0==Math.round(i*100.0))i=i.toString();
<?php }
	print 'if(currcode!="")pTemplate = currformat.toString().replace(/%s/,i.toString());';
	print 'else pTemplate = pTemplate.toString().replace(/\d[,.]*\d*/,i.toString());';
	if(strstr($tempStr,","))
		print "return(pTemplate.replace(/\./,','));";
	else
		print "return(pTemplate);";
?>}
function openEFWindow(id) {
window.open('emailfriend.php?id='+id,'email_friend','menubar=no, scrollbars=no, width=400, height=400, directories=no,location=no,resizable=yes,status=no,toolbar=no')
}
<?php		$pricecheckerisincluded=TRUE;
		}
$prodoptions='';
if($doaddprodoptions && $prodlist != ''){
	$sSQL = "SELECT DISTINCT optID," . $OWSP . "optPriceDiff,optStock FROM options INNER JOIN prodoptions ON options.optGroup=prodoptions.poOptionGroup WHERE prodoptions.poProdID IN (" . $prodlist . ")";
	$result = mysql_query($sSQL) or print(mysql_error());
	$rowcounter=0;
	while($row = mysql_fetch_array($result)){
		if($useStockManagement) print 'aPS[' . $row['optID'] . ']=' . $row['optStock'] . ';';
		print 'aPC['. $row['optID'] . ']=' . $row['optPriceDiff'] . ';';
		if(($rowcounter % 10)==9) print "\r\n";
		$rowcounter++;
	}
	print "\r\n";
}
?>
//-->
</script><?php
}
function updatepricescript($doaddprodoptions,$thetax){
global $prodoptions,$Count,$rs,$noprice,$pricezeromessage,$showtaxinclusive,$currRate1,$currRate2,$currRate3,$currSymbol1,$currSymbol2,$currSymbol3,$currFormat1,$currFormat2,$currFormat3,$useStockManagement,$currencyseparator,$noupdateprice; ?>
<script language="javascript" type="text/javascript">
<!--
function formvalidator<?php print $Count?>(theForm){
<?php
$prodoptions="";
$hasonepriceoption=FALSE;
if($doaddprodoptions){
	$sSQL = "SELECT poOptionGroup,optType,optFlags FROM prodoptions LEFT JOIN optiongroup ON optiongroup.optGrpID=prodoptions.poOptionGroup WHERE poProdID='" . $rs["pId"] . "' ORDER BY poID";
	$result = mysql_query($sSQL) or print(mysql_error());
	for($rowcounter=0;$rowcounter<mysql_num_rows($result);$rowcounter++){
		$prodoptions[$rowcounter] = mysql_fetch_array($result);
	}
	if(is_array($prodoptions)){
		foreach($prodoptions as $rowcounter => $theopt){
			if($theopt['optType']==3){
				print 'if(theForm.voptn' . $rowcounter . ".value=='')return(enterValue(theForm.voptn" . $rowcounter . "));\n";
				print 'if(theForm.voptn' . $rowcounter . ".value.length>255)return(dataLimit(theForm.voptn" . $rowcounter . "));\n";
			}elseif(abs($theopt['optType'])==2){
				$hasonepriceoption=TRUE;
				if($theopt['optType']==2)
					print 'if(theForm.optn' . $rowcounter . '.selectedIndex==0)return(chooseOption(theForm.optn' . $rowcounter . "));\n";
				if($useStockManagement && (int)$rs['pStockByOpts'] != 0) print 'if(!checkStock(theForm.optn' . $rowcounter . ',theForm.optn' . $rowcounter . '.options[theForm.optn' . $rowcounter . '.selectedIndex].value))return(false);' . "\r\n";
			}elseif(abs($theopt['optType'])==1){
				$hasonepriceoption=TRUE;
				print "havefound='';";
				if($theopt['optType']==1)
					print 'for(var i=0; i<theForm.optn' . $rowcounter . '.length; i++) if(theForm.optn' . $rowcounter . '[i].checked)havefound=theForm.optn' . $rowcounter . '[i].value;if(havefound=="")return(chooseOption(theForm.optn' . $rowcounter . '[0]));' . "\r\n";
				if($useStockManagement && (int)$rs['pStockByOpts'] != 0) print 'if(havefound!=""){if(!checkStock(theForm.optn' . $rowcounter . '[0],havefound))return(false);}' . "\r\n";
			}
		}
	}
}
if(@$customvalidator != '') print $customvalidator;
?>return (true);
}
<?php
if(@$noprice!=TRUE && ! ($rs["pPrice"]==0 && @$pricezeromessage != "") && $hasonepriceoption){
	print 'function updateprice' . $Count . "(){\r\n";
	print 'var totAdd=' . $rs['pPrice'] . ";\r\n";
	print 'if(!isW3) return;';
	foreach($prodoptions as $rowcounter => $theopt){
		if(abs($theopt['optType'])==2){
			if(($theopt['optFlags']&1)==1)
				print 'totAdd=totAdd+((' . $rs['pPrice'] . '*pricechecker(document.forms.tForm' . $Count . '.optn' . $rowcounter . '.options[document.forms.tForm' . $Count . '.optn' . $rowcounter . '.selectedIndex].value))/100.0);' . "\r\n";
			else
				print 'totAdd=totAdd+pricechecker(document.forms.tForm' . $Count . '.optn' . $rowcounter . '.options[document.forms.tForm' . $Count . '.optn' . $rowcounter . '.selectedIndex].value);' . "\r\n";
		}elseif(abs($theopt['optType'])==1)
			if(($theopt['optFlags']&1)==1)
				print 'for(var i=0; i<document.forms.tForm' . $Count . '.optn' . $rowcounter . '.length; i++) if (document.forms.tForm' . $Count . '.optn' . $rowcounter . '[i].checked) totAdd=totAdd+((' . $rs['pPrice'] . '*pricechecker(document.forms.tForm' . $Count . '.optn' . $rowcounter . '[i].value))/100.0);' . "\r\n";
			else
				print 'for(var i=0; i<document.forms.tForm' . $Count . '.optn' . $rowcounter . '.length; i++) if (document.forms.tForm' . $Count . '.optn' . $rowcounter . '[i].checked) totAdd=totAdd+pricechecker(document.forms.tForm' . $Count . '.optn' . $rowcounter . '[i].value);' . "\r\n";
	}
	if(@$noupdateprice != TRUE) print "document.getElementById('pricediv" . $Count . "').innerHTML=formatprice(Math.round(totAdd*100.0)/100.0, '', '');\r\n";
	if(@$showtaxinclusive && ($rs["pExemptions"] & 2)!=2) print "document.getElementById('pricedivti" . $Count . "').innerHTML=formatprice(Math.round((totAdd+(totAdd*".$thetax."/100.0))*100.0)/100.0, '', '');\n";
	$extracurr = "";
	if($currRate1!=0 && $currSymbol1!="") $extracurr = "+formatprice(Math.round((totAdd*" . $currRate1 . ")*100.0)/100.0, ' " . $currSymbol1 . "','" . str_replace("'","\'",$currFormat1) . "')+'".str_replace("'","\'",$currencyseparator)."'\n";
	if($currRate2!=0 && $currSymbol2!="") $extracurr .= "+formatprice(Math.round((totAdd*" . $currRate2 . ")*100.0)/100.0, ' " . $currSymbol2 . "','" . str_replace("'","\'",$currFormat2) . "')+'".str_replace("'","\'",$currencyseparator)."'\n";
	if($currRate3!=0 && $currSymbol3!="") $extracurr .= "+formatprice(Math.round((totAdd*" . $currRate3 . ")*100.0)/100.0, ' " . $currSymbol3 . "','" . str_replace("'","\'",$currFormat3) . "');\n";
	if($extracurr!="") print "document.getElementById('pricedivec" . $Count . "').innerHTML=''" . $extracurr . "\r\n";
	print "}";
}
?>//-->
</script><?php
}
function checkDPs($currcode){
	if($currcode=="JPY") return(0); else return(2);
}
function checkCurrencyRates($currConvUser,$currConvPw,$currLastUpdate,&$currRate1,$currSymbol1,&$currRate2,$currSymbol2,&$currRate3,$currSymbol3){
	global $countryCurrency,$usecurlforfsock,$pathtocurl,$curlproxy;
	$ccsuccess = true;
	if($currConvUser!="" && $currConvPw!="" && (strtotime($currLastUpdate) < time()-(60*60*24))){
		$str = "";
		if($currSymbol1!="") $str .= "&curr=" . $currSymbol1;
		if($currSymbol2!="") $str .= "&curr=" . $currSymbol2;
		if($currSymbol3!="") $str .= "&curr=" . $currSymbol3;
		if($str==""){
			mysql_query("UPDATE admin SET currLastUpdate='" . date("Y-m-d H:i:s", time()) . "'") or print(mysql_error());
			return;
		}
		$str = "?source=" . $countryCurrency . "&user=" . $currConvUser . "&pw=" . $currConvPw . $str;
		if(@$usecurlforfsock){
			if(@$pathtocurl != ""){
				exec($pathtocurl . ' --data-binary \'' . str_replace("'","\'","X") . '\' http://www.ecommercetemplates.com/currencyxml.asp' . $str, $res, $retvar);
				$sXML = implode("\n",$res);
			}else{
				if (!$ch = curl_init()) {
					$success = false;
					$errormsg = "cURL package not installed in PHP";
					$ccsuccess = FALSE;
				}else{
					curl_setopt($ch, CURLOPT_URL,'http://www.ecommercetemplates.com/currencyxml.asp' . $str); 
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					curl_setopt($ch, CURLOPT_POSTFIELDS, "X");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					if(@$curlproxy!=''){
						curl_setopt($ch, CURLOPT_PROXY, $curlproxy);
					}
					$sXML = curl_exec($ch);
					if(curl_error($ch) != "") print "Error with cURL installation: " . curl_error($ch) . "<br />";
					curl_close($ch);
				}
			}
		}else{
			$header = "POST /currencyxml.asp" . $str . " HTTP/1.0\r\n";
			$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$header .= "Content-Length: 1\r\n\r\n";
			$fp = fsockopen ('www.ecommercetemplates.com', 80, $errno, $errstr, 30);
			if (!$fp){
				echo "$errstr ($errno)"; // HTTP error handling
				$ccsuccess = FALSE;
			}else{
				fputs ($fp, $header . "X");
				$sXML="";
				while (!feof($fp))
					$sXML .= fgets ($fp, 1024);
			}
		}
		if($ccsuccess){
			// print str_replace("<","<br />&lt;",$sXML) . "<br />\n";
			$xmlDoc = new vrXMLDoc($sXML);
			$nodeList = $xmlDoc->nodeList->childNodes[0];
			for($j = 0; $j < $nodeList->length; $j++){
				if($nodeList->nodeName[$j]=="currError"){
					print $nodeList->nodeValue[$j];
					$ccsuccess = false;
				}elseif($nodeList->nodeName[$j]=="selectedCurrency"){
					$e = $nodeList->childNodes[$j];
					$currRate = 0;
					for($i = 0; $i < $e->length; $i++){
						if($e->nodeName[$i]=="currSymbol")
							$currSymbol = $e->nodeValue[$i];
						elseif($e->nodeName[$i]=="currRate")
							$currRate = $e->nodeValue[$i];
					}
					if($currSymbol1 == $currSymbol){
						$currRate1 = $currRate;
						mysql_query("UPDATE admin SET currRate1=" . $currRate . " WHERE adminID=1") or print(mysql_error());
					}
					if($currSymbol2 == $currSymbol){
						$currRate2 = $currRate;
						mysql_query("UPDATE admin SET currRate2=" . $currRate . " WHERE adminID=1") or print(mysql_error());
					}
					if($currSymbol3 == $currSymbol){
						$currRate3 = $currRate;
						mysql_query("UPDATE admin SET currRate3=" . $currRate . " WHERE adminID=1") or print(mysql_error());
					}
				}
			}
			if($ccsuccess) mysql_query("UPDATE admin SET currLastUpdate='" . date("Y-m-d H:i:s", time()) . "'");
		}
	}
}
function getsectionids($thesecid, $delsections){
	$secarr = split(',', $thesecid);
	$secid = ''; $addcomma = '';
	foreach($secarr as $sect){
		if(is_numeric(trim($sect))) $secid .= $addcomma . $sect; $addcomma = ',';
	}
	if($secid == '') $secid='0';
	$iterations = 0;
	$iteratemore = TRUE;
	if(@$_SESSION['clientLoginLevel'] != '') $minloglevel=$_SESSION['clientLoginLevel']; else $minloglevel=0;
	if($delsections) $nodel = ''; else $nodel = 'sectionDisabled<=' . $minloglevel . ' AND ';
	while($iteratemore && $iterations<10){
		$sSQL2 = "SELECT DISTINCT sectionID,rootSection FROM sections WHERE " . $nodel . "(topSection IN (" . $secid . ") OR (sectionID IN (" . $secid . ") AND rootSection=1))";
		$secid = '';
		$iteratemore = FALSE;
		$result2 = mysql_query($sSQL2) or print(mysql_error());
		$addcomma = '';
		while($rs2 = mysql_fetch_assoc($result2)){
			if($rs2['rootSection']==0) $iteratemore = TRUE;
			$secid .= $addcomma . $rs2['sectionID'];
			$addcomma = ',';
		}
		$iterations++;
	}
	if($secid=='') $secid = '0';
	return($secid);
}
function callcurlfunction($cfurl, $cfxml, &$cfres, $cfcert, &$cferrmsg, $settimeouts){
	global $curlproxy,$pathtocurl;
	$cfsuccess=TRUE;
	// print str_replace("<","<br />&lt;",str_replace("</","&lt;/",$cfxml)) . "<br />\n";
	if(@$pathtocurl != ""){
		exec($pathtocurl . ($cfcert != '' ? ' -E \'' . $cfcert . '\'' : '') . ' --data-binary \'' . str_replace("'","\'",$cfxml) . '\' ' . $cfurl, $cfres, $retvar);
		$cfres = implode("\n",$cfres);
	}else{
		if (!$ch = curl_init()) {
			$cferrmsg = "cURL package not installed in PHP. Set \$pathtocurl parameter.";
			$cfsuccess=FALSE;
		}else{
			curl_setopt($ch, CURLOPT_URL, $cfurl);
			if($cfcert != '') curl_setopt($ch, CURLOPT_SSLCERT, $cfcert); 
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $cfxml);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			if($settimeouts) curl_setopt($ch, CURLOPT_TIMEOUT, 120);
			if(@$curlproxy!=''){
				curl_setopt($ch, CURLOPT_PROXY, $curlproxy);
			}
			$cfres = curl_exec($ch);
			// print str_replace("<","<br />&lt;",str_replace("</","&lt;/",$cfres)) . "<br />\n";
			if(curl_error($ch) != ""){
				if($cfcert != '' && ! is_file($cfcert)){
					$cferrmsg='Certificate file not found: ' . $cfcert . '<br />';
				}else
					$cferrmsg='cURL error: ' . curl_error($ch) . '<br />';
				$cfsuccess=FALSE;
			}else{
				curl_close($ch);
			}
		}
	}
	return($cfsuccess);
}
function getpayprovdetails($ppid,&$ppdata1,&$ppdata2,&$ppdata3,&$ppdemo,&$ppmethod){
	$sSQL = "SELECT payProvData1,payProvData2,payProvData3,payProvDemo,payProvMethod FROM payprovider WHERE payProvEnabled=1 AND payProvID='" . mysql_escape_string($ppid) . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	if($rs = mysql_fetch_assoc($result)){
		$ppdata1 = trim($rs['payProvData1']);
		$ppdata2 = trim($rs['payProvData2']);
		$ppdata3 = trim($rs['payProvData3']);
		$ppdemo = ((int)$rs['payProvDemo']==1);
		$ppmethod = (int)$rs['payProvMethod'];
	}else
		return(FALSE);
	return(TRUE);
}
function writehiddenvar($hvname,$hvval){
print '<input type="hidden" name="' . $hvname . '" value="' . str_replace('"','&quot;',$hvval) . '" />' . "\r\n";
}
function ppsoapheader($username, $password, $signature){
return '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Header><RequesterCredentials xmlns="urn:ebay:api:PayPalAPI"><Credentials xmlns="urn:ebay:apis:eBLBaseComponents"><Username>' . $username . '</Username><Password>' . $password . '</Password>' . ($signature != '' ? '<Signature>' . $signature . '</Signature>' : '') . '</Credentials></RequesterCredentials></soap:Header>';
}
function displayproductoptions($grpnmstyle,$grpnmstyleend,&$optpricediff){
	global $rs,$prodoptions,$useStockManagement,$hideoptpricediffs,$pricezeromessage,$noprice,$OWSP,$xxPlsSel,$Count,$optionshavestock,$xxOpSkTx,$noshowoptionsinstock,$showinstock;
	$optpricediff = 0;
	$optionshtml = '';
	foreach($prodoptions as $rowcounter => $theopt){
		$opthasstock=false;
		$sSQL='SELECT optID,'.getlangid('optName',32).','.getlangid('optGrpName',16).',' . $OWSP . 'optPriceDiff,optType,optFlags,optGrpSelect,optStock,optPriceDiff AS optDims,optDefault FROM options LEFT JOIN optiongroup ON options.optGroup=optiongroup.optGrpID WHERE optGroup=' . $theopt['poOptionGroup'] . ' ORDER BY optID';
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs2=mysql_fetch_array($result)){
			if(abs((int)$rs2['optType'])==3){
				$opthasstock=true;
				$fieldHeight = round(((double)($rs2['optDims'])-(int)($rs2['optDims']))*100.0);
				$optionshtml .= '<tr><td align="right" width="30%">' . $grpnmstyle . $rs2[getlangid('optGrpName',16)] . ':' . $grpnmstyleend . '</td><td align="left"> <input type="hidden" name="optn' . $rowcounter . '" value="' . $rs2["optID"] . '" />';
				if($fieldHeight != 1){
					$optionshtml .= '<textarea wrap="virtual" name="voptn' . $rowcounter . '" cols="' . (int)$rs2["optDims"] . '" rows="' . $fieldHeight . '">';
					$optionshtml .= $rs2[getlangid('optName',32)] . '</textarea>';
				}else
					$optionshtml .= '<input maxlength="255" type="text" name="voptn' . $rowcounter . '" size="' . $rs2['optDims'] . '" value="' . str_replace('"','&quot;',$rs2[getlangid('optName',32)]) . '" />';
				$optionshtml .= '</td></tr>';
			}elseif(abs((int)$rs2['optType'])==1){
				$optionshtml .= '<tr><td align="right" valign="baseline" width="30%">' . $grpnmstyle . $rs2[getlangid('optGrpName',16)] . ':' . $grpnmstyleend . '</td><td align="left"> ';
				do {
					$optionshtml .= '<input type="radio" style="vertical-align:middle" onclick="' . (($rs['pPrice']==0 && @$pricezeromessage != '') || @$noprice==TRUE ?'dummyfunc':'updateprice' . $Count) . '();" name="optn' . $rowcounter . '" ';
					if((int)$rs2['optDefault']!=0) $optionshtml .= 'checked ';
					$optionshtml .= 'value="' . $rs2['optID'] . '" /><span ';
					if($useStockManagement && $rs['pStockByOpts']!=0 && $rs2['optStock'] <= 0) $optionshtml .= ' class="oostock" '; else $opthasstock=true;
					$optionshtml .= '>' . $rs2[getlangid('optName',32)];
					if(@$hideoptpricediffs != TRUE && (double)($rs2['optPriceDiff']) != 0){
						$optionshtml .= ' (';
						if((double)($rs2['optPriceDiff']) > 0) $optionshtml .= '+';
						if(($rs2['optFlags']&1)==1)$pricediff = ($rs['pPrice']*$rs2['optPriceDiff'])/100.0;else$pricediff = $rs2['optPriceDiff'];
						$optionshtml .= FormatEuroCurrency($pricediff) . ')';
						if($rs2['optDefault']!=0) $optpricediff += $pricediff;
					}
					if($useStockManagement && @$showinstock==TRUE && @$noshowoptionsinstock != TRUE && (int)$rs["pStockByOpts"] != 0) $optionshtml .= str_replace('%s', $rs2['optStock'], $xxOpSkTx);
					$optionshtml .= '</span>';
					if(($rs2['optFlags'] & 4) != 4) $optionshtml .= "<br />\r\n";
				} while($rs2=mysql_fetch_array($result));
				$optionshtml .= '</td></tr>';
			}else{
				$optionshtml .= '<tr><td align="right" width="30%">' . $grpnmstyle . $rs2[getlangid('optGrpName',16)] . ':' . $grpnmstyleend . '</td><td align="left"> <select class="prodoption" onChange="' . (($rs['pPrice']==0 && @$pricezeromessage != '') || @$noprice==TRUE ?'dummyfunc':'updateprice' . $Count) . '();" name="optn' . $rowcounter . '" size="1">';
				$gotdefaultdiff = FALSE;
				$firstpricediff = 0;
				if((int)$rs2['optGrpSelect']!=0)
					$optionshtml .= '<option value="">' . $xxPlsSel . '</option>';
				else
					if(($rs2['optFlags']&1)==1)$firstpricediff = ($rs['pPrice']*$rs2['optPriceDiff'])/100.0;else $firstpricediff = $rs2['optPriceDiff'];
				do {
					$optionshtml .= '<option ';
					if($useStockManagement && $rs['pStockByOpts']!=0 && $rs2['optStock'] <= 0) $optionshtml .= 'class="oostock" '; else $opthasstock=true;
					if((int)$rs2['optDefault']!=0) $optionshtml .= 'selected ';
					$optionshtml .= 'value="' . $rs2['optID'] . '">' . $rs2[getlangid('optName',32)];
					if(@$hideoptpricediffs != TRUE){
						if((double)($rs2['optPriceDiff']) != 0){
							$optionshtml .= ' (';
							if((double)($rs2['optPriceDiff']) > 0) $optionshtml .= '+';
							if(($rs2['optFlags']&1)==1)$pricediff = ($rs['pPrice']*$rs2['optPriceDiff'])/100.0;else $pricediff = $rs2['optPriceDiff'];
							$optionshtml .= FormatEuroCurrency($pricediff) . ')';
							if($rs2['optDefault']!=0)$optpricediff += $pricediff;
						}
						if($rs2['optDefault']!=0)$gotdefaultdiff=TRUE;
					}
					if($useStockManagement && @$showinstock==TRUE && @$noshowoptionsinstock != TRUE && (int)$rs["pStockByOpts"] != 0) $optionshtml .= str_replace('%s', $rs2['optStock'], $xxOpSkTx);
					$optionshtml .= "</option>\n";
				} while($rs2=mysql_fetch_array($result));
				if(@$hideoptpricediffs != TRUE && ! $gotdefaultdiff) $optpricediff += $firstpricediff;
				$optionshtml .= '</select></td></tr>';
			}
		}
		$optionshavestock = ($optionshavestock && $opthasstock);
	}
	return($optionshtml);
}
function CalcHmacSha1($data, $key){
    $blocksize = 64;
    $hashfunc = 'sha1';
    if (strlen($key) > $blocksize){
        $key = pack('H*', $hashfunc($key));
    }
    $key = str_pad($key, $blocksize, chr(0x00));
    $ipad = str_repeat(chr(0x36), $blocksize);
    $opad = str_repeat(chr(0x5c), $blocksize);
    $hmac = pack('H*', $hashfunc(($key^$opad).pack('H*', $hashfunc(($key^$ipad).$data))));
    return $hmac;
}
function encodeemailsubject($in_str, $charset){
	$out_str = $in_str;
	if($out_str && $charset){
		// define start delimimter, end delimiter and spacer
		$end = "?=";
		$start = "=?" . $charset . "?B?";
		$spacer = $end . "\r\n " . $start;
		// determine length of encoded text within chunks and ensure length is even
		$length = 75 - strlen($start) - strlen($end);
		$length = floor($length/2) * 2;
		// encode the string and split it into chunks with spacers after each chunk
		$out_str = base64_encode($out_str);
		$out_str = chunk_split($out_str, $length, $spacer);
		// remove trailing spacer and add start and end delimiters
		$spacer = preg_quote($spacer);
		$out_str = preg_replace("/" . $spacer . "$/", "", $out_str);
		$out_str = $start . $out_str . $end;
	}
	return $out_str;
}
if(@$enableclientlogin==TRUE || @$forceclientlogin==TRUE){
	if(@$_SESSION['clientUser'] != ''){
	}elseif(@$_POST['checktmplogin']=='1' && @$_POST['sessionid'] != ''){
		$sSQL = "SELECT tmploginname FROM tmplogin WHERE tmploginid='" . mysql_escape_string(trim(@$_POST['sessionid'])) . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_array($result)){
			$_SESSION['clientID']=$rs['tmploginname'];
			mysql_free_result($result);
			mysql_query("DELETE FROM tmplogin WHERE tmploginid='" . mysql_escape_string(trim(@$_POST['sessionid'])) . "'") or print(mysql_error());
			$sSQL = "SELECT clUserName,clActions,clLoginLevel,clPercentDiscount FROM customerlogin WHERE clID='" . mysql_escape_string($_SESSION['clientID']) . "'";
			$result = mysql_query($sSQL) or print(mysql_error());
			if($rs = mysql_fetch_array($result)){
				$_SESSION['clientUser']=$rs['clUserName'];
				$_SESSION['clientActions']=$rs['clActions'];
				$_SESSION['clientLoginLevel']=$rs['clLoginLevel'];
				$_SESSION['clientPercentDiscount']=(100.0-(double)$rs['clPercentDiscount'])/100.0;
			}
		}
		mysql_free_result($result);
	}elseif(@$_COOKIE['WRITECLL'] != ''){
		$clientEmail = str_replace("'",'',@$_COOKIE['WRITECLL']);
		$clientPW = str_replace("'",'',@$_COOKIE['WRITECLP']);
		$sSQL = "SELECT clID,clUserName,clActions,clLoginLevel,clPercentDiscount FROM customerlogin WHERE (clEmail<>'' AND clEmail='" . mysql_escape_string($clientEmail) . "' AND clPW='" . mysql_escape_string($clientPW) . "') OR (clEmail='' AND clUserName='" . mysql_escape_string($clientEmail) . "' AND clPW='" . mysql_escape_string($clientPW) . "')";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_array($result)){
			$_SESSION['clientID']=$rs['clID'];
			$_SESSION['clientUser']=$rs['clUserName'];
			$_SESSION['clientActions']=$rs['clActions'];
			$_SESSION['clientLoginLevel']=$rs['clLoginLevel'];
			$_SESSION['clientPercentDiscount']=(100.0-(double)$rs['clPercentDiscount'])/100.0;
		}
		mysql_free_result($result);
	}
	if(@$requiredloginlevel != ""){
		if((int)$requiredloginlevel > @$_SESSION["clientLoginLevel"]){
			ob_end_clean();
			if(@$_SERVER["HTTPS"] == "on" || @$_SERVER["SERVER_PORT"] == "443")$prot='https://';else $prot='http://';
			header('Location: '.$prot.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/cart.php?mode=login&refurl=' . urlencode(@$_SERVER["PHP_SELF"] . (@$_SERVER["QUERY_STRING"] !="" ? "?" . @$_SERVER["QUERY_STRING"] : "")));
			exit;
		}
	}
}
function getsessionsql(){
	global $thesessionid;
	return (@$_SESSION['clientID'] != '' ? 'cartClientID=' . $_SESSION['clientID'] : "(cartClientID=0 AND cartSessionID='" . $thesessionid . "')");
}
function getordersessionsql(){
	global $thesessionid;
	return (@$_SESSION['clientID'] != '' ? 'ordClientID=' . $_SESSION['clientID'] : "(ordClientID=0 AND ordSessionID='" . $thesessionid . "')");
}
function trimoldcartitems($cartitemsdel){
	global $dateadjust;
	if(@$dateadjust=='') $dateadjust=0;
	$thetocdate = time() + ($dateadjust*60*60);
	$sSQL = "SELECT adminDelUncompleted,adminClearCart FROM admin WHERE adminID=1";
	$result = mysql_query($sSQL) or print(mysql_error());
	$rs = mysql_fetch_assoc($result);
	$delAfter=$rs['adminDelUncompleted'];
	$delSavedCartAfter=$rs['adminClearCart'];
	$sSQL = 'SELECT cartID FROM cart WHERE cartCompleted=0 AND ';
	$sSQL .= "((cartOrderID=0 AND cartClientID=0 AND cartDateAdded<'" . date("Y-m-d H:i:s", $cartitemsdel) . "') ";
	if($delAfter != 0) $sSQL .= "OR (cartClientID=0 AND cartDateAdded<'" . date("Y-m-d H:i:s", $thetocdate-($delAfter*60*60*24)) . "') ";
	if($delSavedCartAfter != 0) $sSQL .= "OR (cartDateAdded<'" . date("Y-m-d H:i:s", $thetocdate-($delSavedCartAfter*60*60*24)) . "') ";
	$sSQL .= ')';
	$addcomma='';
	$result = mysql_query($sSQL) or print(mysql_error());
	if(mysql_num_rows($result) > 0){
		$delOptions='';
		while($rs = mysql_fetch_assoc($result)){
			$delOptions .= $addcomma . $rs['cartID'];
			$addcomma = ',';
		}
		mysql_query("DELETE FROM cartoptions WHERE coCartID IN (" . $delOptions . ')');
		mysql_query("DELETE FROM cart WHERE cartID IN (" . $delOptions . ')');
	}
	if($delAfter != 0) mysql_query("DELETE FROM orders WHERE ordAuthNumber='' AND ordDate<'" . date("Y-m-d H:i:s", $thetocdate-($delAfter*60*60*24)) . "' AND ordStatus=2");
}
?>