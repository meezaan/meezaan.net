<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protected under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$_SERVER['CONTENT_LENGTH'] != '' && $_SERVER['CONTENT_LENGTH'] > 10000) exit;
$iNumOfPages = 0;
$showcategories=FALSE;
$gotcriteria=FALSE;
$numcats=0;
$catid=0;
$nobox="";
$isrootsection=FALSE;
$topsectionids="0";
if(! @is_numeric($_GET["pg"]))
	$CurPage = 1;
else
	$CurPage = (int)($_GET["pg"]);
if(@$_GET["nobox"]=="true" || @$_POST["nobox"]=="true")
	$nobox='true';
$WSP = "";
$OWSP = "";
$TWSP = "pPrice";
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
$tsID="";
$scat=trim(unstripslashes(@$_REQUEST["scat"]));
$stext=trim(unstripslashes(@$_REQUEST["stext"]));
$stype=trim(unstripslashes(@$_REQUEST["stype"]));
$sprice=trim(unstripslashes(@$_REQUEST["sprice"]));
$minprice=trim(unstripslashes(@$_REQUEST["sminprice"]));
if(substr($scat,0,2)=="ms") $thecat = substr($scat,2); else $thecat=$scat;
$thecat = str_replace("'","",$thecat);
$catarr = split(',', $thecat);
$Count = 0;
function writemenulevel($id,$itlevel){
	global $allcatsa,$numcats,$thecat,$catarr;
	if($itlevel<10){
		for($wmlindex=0; $wmlindex < $numcats; $wmlindex++){
			if($allcatsa[$wmlindex][2]==$id){
				print "<option value='" . $allcatsa[$wmlindex][0] . "'";
				if($catarr[0]==$allcatsa[$wmlindex][0]) print " selected>"; else print ">";
				for($index = 0; $index < $itlevel-1; $index++)
					print '&nbsp;&nbsp;&raquo;&nbsp;';
				print $allcatsa[$wmlindex][1] . "</option>\n";
				if($allcatsa[$wmlindex][3]==0) writemenulevel($allcatsa[$wmlindex][0],$itlevel+1);
			}
		}
	}
}
function writepagebar($CurPage, $iNumPages){
	global $nobox,$scat,$stext,$stype,$sprice,$minprice,$xxNext,$xxPrev;
	$sLink = '<a href="search.php?nobox=' . $nobox . '&scat=' . $scat . '&stext=' . urlencode($stext) . '&stype=' . $stype . '&sprice=' . urlencode($sprice) . ($minprice!=""?"&sminprice=".$minprice:"") . '&pg=';
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
		return $sStr . $sLink . ($CurPage+1) . '">'.$xxNext.'</a> ' . $sLink . $iNumPages . '"><strong><font face="Verdana">&raquo;</font></strong></a>';
	else
		return $sStr . ' '.$xxNext.' <strong><font face="Verdana">&raquo;</font></strong>';
}
$alreadygotadmin = getadminsettings();
if(@$orprodsperpage != '') $adminProdsPerPage=$orprodsperpage;
checkCurrencyRates($currConvUser,$currConvPw,$currLastUpdate,$currRate1,$currSymbol1,$currRate2,$currSymbol2,$currRate3,$currSymbol3);
if(@$_SESSION["clientLoginLevel"] != "") $minloglevel=$_SESSION["clientLoginLevel"]; else $minloglevel=0;
$sSQL = "SELECT sectionID,".getlangid("sectionName",256).",topSection,rootSection FROM sections WHERE sectionDisabled<=" . $minloglevel . " ";
if(@$onlysubcats==TRUE)
	$sSQL .= "AND rootSection=1 ORDER BY ".getlangid("sectionName",256);
else
	$sSQL .= "ORDER BY sectionOrder";
$allcats = mysql_query($sSQL) or print(mysql_error());
if(mysql_num_rows($allcats)==0)
	$success=FALSE;
else
	$success=TRUE;
if(@$_POST["posted"]=="1" || @$_GET["pg"] != ""){
	if($thecat != ""){
		$sSQL = "SELECT DISTINCT products.pId FROM products LEFT JOIN multisections ON products.pId=multisections.pId WHERE pDisplay<>0 ";
		$gotcriteria=TRUE;
		$sectionids = getsectionids($thecat, FALSE);
		if($sectionids != "") $sSQL .= "AND (products.pSection IN (" . $sectionids . ") OR multisections.pSection IN (" . $sectionids . ")) ";
	}else
		$sSQL = "SELECT DISTINCT products.pId FROM products WHERE pDisplay<>0 ";
	if(is_numeric($sprice)){
		$gotcriteria=TRUE;
		$sSQL .= "AND ".$TWSP."<='" . mysql_escape_string($sprice) . "' ";
	}
	if(is_numeric($minprice)){
		$gotcriteria=TRUE;
		$sSQL .= "AND ".$TWSP.">='" . mysql_escape_string($minprice) . "' ";
	}
	if(trim($stext) != ""){
		$gotcriteria=TRUE;
		$Xstext = mysql_escape_string($stext);
		$aText = split(" ",$Xstext);
		$aFields[0]="products.pId";
		$aFields[1]=getlangid("pName",1);
		$aFields[2]=getlangid("pDescription",2);
		$aFields[3]=getlangid("pLongDescription",4);
		if($stype=="exact")
			$sSQL .= "AND (products.pId LIKE '%" . $Xstext . "%' OR ".getlangid("pName",1)." LIKE '%" . $Xstext . "%' OR ".getlangid("pDescription",2)." LIKE '%" . $Xstext . "%' OR ".getlangid("pLongDescription",4)." LIKE '%" . $Xstext . "%') ";
		else{
			$sJoin="AND ";
			if($stype=="any") $sJoin="OR ";
			$sSQL .= "AND (";
			for($index=0;$index<=3;$index++){
				$sSQL .= "(";
				$rowcounter=0;
				$arrelms=count($aText);
				foreach($aText as $theopt){
					if(is_array($theopt))$theopt=$theopt[0];
					$sSQL .= $aFields[$index] . " LIKE '%" . $theopt . "%' ";
					if(++$rowcounter < $arrelms) $sSQL .= $sJoin;
				}
				$sSQL .= ") ";
				if($index < 3) $sSQL .= "OR ";
			}
			$sSQL .= ") ";
		}
	}
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
	$disabledsections = "";
	$addcomma="";
	$result2 = mysql_query("SELECT sectionID FROM sections WHERE sectionDisabled>".$minloglevel) or print(mysql_error());
	while($rs2 = mysql_fetch_assoc($result2)){
		$disabledsections .= $addcomma . $rs2["sectionID"];
		$addcomma=",";
	}
	mysql_free_result($result2);
	if($gotcriteria)
		$tmpSQL = preg_replace("/DISTINCT products.pId/","COUNT(DISTINCT products.pId) AS bar",$sSQL, 1);
	else{
		$sSQL = "SELECT products.pId FROM products WHERE pDisplay<>0";
		$tmpSQL = preg_replace("/products.pId/","COUNT(*) AS bar",$sSQL, 1);
	}
	if($disabledsections!="") $extrasql = " AND NOT (products.pSection IN (" . getsectionids($disabledsections, TRUE) . "))"; else $extrasql = "";
	if($useStockManagement && @$noshowoutofstock==TRUE) $extrasql .= ' AND (pInStock>0 OR pStockByOpts<>0)';
	$sSQL .= $extrasql;
	$tmpSQL .= $extrasql;
	$allprods = mysql_query($tmpSQL) or print(mysql_error());
	$iNumOfPages = ceil(mysql_result($allprods,0,"bar")/$adminProdsPerPage);
	mysql_free_result($allprods);
	$sSQL .= $sSortBy . " LIMIT " . ($adminProdsPerPage*($CurPage-1)) . ", $adminProdsPerPage";
	$allprods = mysql_query($sSQL) or print(mysql_error());
	if(mysql_num_rows($allprods) == 0)
		$success=FALSE;
	else{
		$success=TRUE;
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
}
$_SESSION["frompage"] = @$_SERVER['PHP_SELF'] . (trim(@$_SERVER['QUERY_STRING'])!= "" ? "?" : "") . @$_SERVER['QUERY_STRING'];
if($nobox==''){
?>
	  <br />
	  <form method="post" action="search.php">
		  <input type="hidden" name="posted" value="1" />
            <table class="cobtbl" width="<?php print $maintablewidth?>" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
			  <tr> 
                <td class="cobhl" align="center" colspan="4" bgcolor="#EBEBEB" height="30">
                  <strong><?php print $xxSrchPr?></strong>
                </td>
              </tr>
			  <tr> 
                <td class="cobhl" width="25%" align="right" bgcolor="#EBEBEB"><?php print $xxSrchFr?>:</td>
				<td class="cobll" width="25%" bgcolor="#FFFFFF"><input type="text" name="stext" size="20" value="<?php print htmlspecialchars($stext)?>" /></td>
				<td class="cobhl" width="25%" align="right" bgcolor="#EBEBEB"><?php print $xxSrchMx?>:</td>
				<td class="cobll" width="25%" bgcolor="#FFFFFF"><input type="text" name="sprice" size="10" value="<?php print htmlspecialchars($sprice)?>" /></td>
			  </tr>
			  <tr>
			    <td class="cobhl" width="25%" align="right" bgcolor="#EBEBEB"><?php print $xxSrchTp?>:</td>
				<td class="cobll" width="25%" bgcolor="#FFFFFF"><select name="stype" size="1">
					<option value=""><?php print $xxSrchAl?></option>
					<option value="any" <?php if($stype=="any") print "selected"?>><?php print $xxSrchAn?></option>
					<option value="exact" <?php if($stype=="exact") print "selected"?>><?php print $xxSrchEx?></option>
					</select>
				</td>
				<td class="cobhl" width="25%" align="right" bgcolor="#EBEBEB"><?php print $xxSrchCt?>:</td>
				<td class="cobll" width="25%" bgcolor="#FFFFFF">
				  <select name="scat" size="1">
				  <option value=""><?php print $xxSrchAC?></option>
<?php
		$lasttsid = -1;
		while($row = mysql_fetch_row($allcats)){
			$allcatsa[$numcats++]=$row;
		}
		if($numcats > 0) writemenulevel(0,1);
?>
				  </select>
				</td>
              </tr>
			  <tr>
			    <td class="cobhl" bgcolor="#EBEBEB">&nbsp;</td>
			    <td class="cobll" bgcolor="#FFFFFF" colspan="3"><table width="100%" cellspacing="0" cellpadding="0" border="0">
				    <tr>
					  <td class="cobll" bgcolor="#FFFFFF" width="66%" align="center"><input type="submit" value="<?php print $xxSearch?>" /></td>
					  <td class="cobll" bgcolor="#FFFFFF" width="34%" height="26" align="right" valign="bottom"><img src="images/tablebr.gif" alt="" /></td>
					</tr>
				  </table></td>
			  </tr>
			</table>
		</form>
<?php
}
if(@$_POST["posted"]=="1" || @$_GET["pg"] != ""){
?>
		<table border="0" cellspacing="<?php print $maintablespacing?>" cellpadding="<?php print $maintablepadding?>" width="<?php print $maintablewidth?>" bgcolor="<?php print $maintablebg?>" align="center">
<?php
	if(!$success){
?>
		<tr> 
		  <td align="center"> 
		    <p>&nbsp;</p>
		    <p><strong><?php print $xxSrchNM?></strong></p>
		  </td>
		</tr>
<?php
	}else{
?>
        <tr> 
          <td width="100%">
<?php	if($usesearchbodyformat==3)
			include "./vsadmin/inc/incproductbody3.php";
		elseif($usesearchbodyformat==2)
			include "./vsadmin/inc/incproductbody2.php";
		else
			include "./vsadmin/inc/incproductbody.php"; ?>
          </td>
        </tr>
<?php
	}
?>
      </table>
<?php
}
?>
