<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(! is_numeric(@$_GET["id"]))
	$theid = "0";
else
	$theid = (int)(@$_GET["id"]);
if(is_numeric(@$_GET["cat"])) $theid = (int)(@$_GET["cat"]);
if(@$explicitid != "" && is_numeric(@$explicitid)) $theid=@$explicitid;
if(! is_numeric(@$categorycolumns) || $categorycolumns=="") $categorycolumns=1;
$cellwidth = (int)(100/$categorycolumns);
if(@$usecategoryformat==3){
	$afterimage="<br />";
	$beforedesc='';
}elseif(@$usecategoryformat==2){
	$afterimage="";
	$beforedesc='';
}else{
	$usecategoryformat=1;
	$afterimage="";
	$beforedesc='</td></tr><tr><td class="catdesc" colspan="2">';
}
$border=0;
if(! @isset($catseparator)) $catseparator = "<br />&nbsp;";
$alreadygotadmin = getadminsettings();
$_SESSION["frompage"] = @$_SERVER['PHP_SELF'] . (trim(@$_SERVER['QUERY_STRING'])!= "" ? "?" : "") . @$_SERVER['QUERY_STRING'];
$tslist = "";
$thetopts = $theid;
$topsectionids = $theid;
$success = TRUE;
if(@$_SESSION["clientLoginLevel"] != "") $minloglevel=$_SESSION["clientLoginLevel"]; else $minloglevel=0;
$columncount=0;
for($index=0; $index <= 10; $index++){
	if($thetopts==0){
		if($theid=="0")
			$tslist = $xxHome . " " . $tslist;
		else
			$tslist = '<a href="categories.php">' . $xxHome . "</a> " . $tslist;
		break;
	}elseif($index==10){
		$tslist = "<strong>Loop</strong>" . $tslist;
	}else{
		$sSQL = "SELECT sectionID,topSection,".getlangid("sectionName",256).",rootSection,sectionDisabled,sectionurl FROM sections WHERE sectionID=" . $thetopts;
		$result2 = mysql_query($sSQL) or print(mysql_error());
		if(mysql_num_rows($result2) > 0){
			$rs2 = mysql_fetch_assoc($result2);
			if($rs2['sectionDisabled'] > $minloglevel)
				$success=FALSE;
			elseif($rs2['sectionID']==(int)$theid){
				$tslist = ' &raquo; ' . $rs2[getlangid('sectionName',256)] . $tslist;
				if(@$explicitid=='' && trim($rs2['sectionurl']) != '' && @$redirecttostatic==TRUE){
					ob_end_clean();
					header('HTTP/1.1 301 Moved Permanently');
					if($rs2['sectionurl']{0}=='/')$thelocation='http://'.$_SERVER['HTTP_HOST'].$rs2['sectionurl'];elseif(substr(strtolower($rs2['sectionurl']),0,7) == 'http://')$thelocation=$rs2['sectionurl'];else $thelocation='http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'],'/')).'/'.$rs2['sectionurl'];
					header('Location: '.$thelocation);
					exit;
				}
			}elseif(trim($rs2['sectionurl']) !='')
				$tslist = ' &raquo; <a href="' . $rs2['sectionurl'] . '">' . $rs2[getlangid('sectionName',256)] . '</a>' . $tslist;
			elseif($rs2['rootSection']==1)
				$tslist = ' &raquo; <a href="products.php?cat=' . $rs2['sectionID'] . '">' . $rs2[getlangid('sectionName',256)] . '</a>' . $tslist;
			else
				$tslist = ' &raquo; <a href="categories.php?cat=' . $rs2['sectionID'] . '">' . $rs2[getlangid('sectionName',256)] . '</a>' . $tslist;
			$thetopts = $rs2['topSection'];
			$topsectionids .= ',' . $thetopts;
		}else{
			$tslist = 'Top Section Deleted' . $tslist;
			break;
		}
		mysql_free_result($result2);
	}
}
if(@$xxAlProd!='') $tslist .= ' &raquo; <a href="products.php' . ($theid=="0" ? '' : '?cat=' . $theid) . '">' . $xxAlProd . "</a>";
$sSQL = "SELECT sectionID,".getlangid("sectionName",256).",".getlangid("sectionDescription",512).",sectionImage,sectionOrder,rootSection,sectionurl FROM sections WHERE topSection=" . $theid . " AND sectionDisabled<=" . $minloglevel . " ORDER BY sectionOrder";
$result = mysql_query($sSQL) or print(mysql_error());
if(!$success || mysql_num_rows($result)==0){
	$success=false;
	$mess1 = "<p>&nbsp;</p>" . $xxNoCats;
}else{
	$success=true;
	if(@$xxClkPrd != "") $mess1 = $xxClkPrd . "<br />&nbsp;"; else $mess1='';
}
if($usecategoryformat==1 || $usecategoryformat==2) $numcolumns=2*$categorycolumns; else $numcolumns=$categorycolumns;
?>
      <table border="0" cellspacing="<?php print $maintablespacing?>" cellpadding="<?php print $maintablepadding?>" width="<?php print $maintablewidth?>" bgcolor="<?php print $maintablebg?>" align="center">
        <tr> 
          <td width="100%">
            <table width="<?php print $innertablewidth?>" border="<?php print $border?>" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
<?php	if($mess1 != ""){ ?>
			  <tr>
				<td align="center"<?php if($numcolumns>1) print ' colspan="' . $numcolumns . '"'?>>
				  <p><strong><?php print $mess1?></strong></p>
				</td>
			  </tr>
<?php
		}
	if(@$nowholesalediscounts==TRUE && @$_SESSION["clientUser"]!="")
		if((($_SESSION["clientActions"] & 8) == 8) || (($_SESSION["clientActions"] & 16) == 16)) $noshowdiscounts=TRUE;
	if($success){
		if(@$noshowdiscounts != TRUE){
			if($theid=="0")
				$sSQL = "SELECT DISTINCT ".getlangid("cpnName",1024)." FROM coupons WHERE (cpnSitewide=1 OR cpnSitewide=2) AND cpnNumAvail>0 AND cpnEndDate>='" . date("Y-m-d",time()) ."' AND cpnIsCoupon=0";
			else
				$sSQL = "SELECT DISTINCT ".getlangid("cpnName",1024)." FROM coupons LEFT OUTER JOIN cpnassign ON coupons.cpnID=cpnassign.cpaCpnID WHERE (((cpnSitewide=0 OR cpnSitewide=3) AND cpaType=1 AND cpaAssignment IN ('" . str_replace(",","','",$topsectionids) . "')) OR cpnSitewide=1 OR cpnSitewide=2) AND cpnNumAvail>0 AND cpnEndDate>='" . date("Y-m-d",time()) ."' AND cpnIsCoupon=0";
			$result2 = mysql_query($sSQL) or print(mysql_error());
			if(mysql_num_rows($result2) > 0){ ?>
			  <tr>
				<td align="left"<?php if($numcolumns>1) print ' colspan="' . $numcolumns . '"'?>>
				  <p><strong><?php print $xxDsCat?></strong><br /><font color="#FF0000" size="1">
				  <?php	while($rs=mysql_fetch_row($result2)){
							print $rs[0] . "<br />";
						} ?>&nbsp;</font></p>
				</td>
			  </tr>
<?php		}
			mysql_free_result($result2);
		}
		print '</table>';
		if(! (@isset($showcategories) && @$showcategories==FALSE)){
			print '<table width="' . @$innertablewidth . '" border="' . $border . '" cellspacing="' . @$innertablespacing . '" cellpadding="' . @$innertablepadding . '" bgcolor="' . @$innertablebg . '"><tr>';
			if(@$allproductsimage != "") print '<td class="catimage" width="5%" align="right"><a href="products.php"><img class="catimage" src="' . @$allproductsimage . '" border="0" alt="' . $xxAlProd . '" /></a>' . $afterimage . '</td>';
			print '<td class="catnavigation">';
			print '<p class="catnavigation"><strong>' . $tslist . '</strong></p>';
			print '<p class="navdesc">' . $xxAlPrCa . @$catseparator . '</p>';
			print "</td></tr>\r\n";
			print '</table>';
		}
		print '<table width="' . @$innertablewidth . '" border="' . $border . '" cellspacing="' . ($usecategoryformat==1 && $categorycolumns>1 ? 0 : @$innertablespacing) . '" cellpadding="' . ($usecategoryformat==1 && $categorycolumns>1 ? 0 : $innertablepadding) . '" bgcolor="' . $innertablebg . '">';
		while($rs=mysql_fetch_row($result)){
			if(trim($rs[6])!="")
				$startlink="<a href='" . $rs[6] . "'>";
			elseif($rs[5]==0)
				$startlink="<a href='categories.php?cat=" . $rs[0] . "'>";
			else
				$startlink="<a href='products.php?cat=" . $rs[0] . "'>";
			$sSQL = "SELECT DISTINCT ".getlangid("cpnName",1024)." FROM coupons LEFT OUTER JOIN cpnassign ON coupons.cpnID=cpnassign.cpaCpnID WHERE (cpnSitewide=0 OR cpnSitewide=3) AND cpnNumAvail>0 AND cpnEndDate>='" . date("Y-m-d",time()) ."' AND cpnIsCoupon=0 AND cpaType=1 AND cpaAssignment='" . $rs[0] . "'";
			$alldiscounts = "";
			if(@$noshowdiscounts != TRUE){
				$result2 = mysql_query($sSQL) or print(mysql_error());
				while($rs2=mysql_fetch_row($result2))
					$alldiscounts .= $rs2[0] . "<br />";
				mysql_free_result($result2);
			}
			$secdesc = trim($rs[2]);
			$noimage = (trim($rs[3]) == "");
			if($columncount==0) print "<tr>";
			if($usecategoryformat==1 && $categorycolumns>1) print '<td width="' . $cellwidth . '%" valign="top"><table width="100%" border="' . $border . '" cellspacing="' . @$innertablespacing . '" cellpadding="' . $innertablepadding . '"><tr>';
			if(($usecategoryformat==1 || $usecategoryformat==2) && ! $noimage){
				$cellwidth -= 5;
				print '<td class="catimage" width="5%" align="right">' . $startlink . '<img alt="' . str_replace('"','',$rs[1]) . '" class="catimage" src="' . $rs[3] . '" border="0" /></a>' . $afterimage . '</td>';
			}
			print '<td class="catname" width="' . ($usecategoryformat==1 && $categorycolumns>1 ? 95 : $cellwidth) . '%"' . (($usecategoryformat==1 || $usecategoryformat==2) && $noimage ? ' colspan="2"' : "") . '>';
			if(($usecategoryformat==1 || $usecategoryformat==2) && ! $noimage) $cellwidth += 5;
			if($usecategoryformat != 1 && $usecategoryformat != 2 && ! $noimage) print $startlink . '<img alt="' . str_replace('"','',$rs[1]) . '" class="catimage" src="' . $rs[3] . '" border="0" /></a>' . $afterimage;
			print '<p class="catname"><strong>' . $startlink . $rs[1] . '</a>' . $xxDot . '</strong>';
			if($alldiscounts!= "") print ' <font color="#FF0000"><strong>' . $xxDsApp . '</strong><br /><font size="1"><div class="catdiscounts">' . $alldiscounts . '</div></font></font>';
			if($secdesc != "") print '</p>'; else print @$catseparator . '</p>';
			if($secdesc != "") print $beforedesc . '<p class="catdesc">' . $secdesc . $catseparator . '</p>';
			print "</td>\r\n";
			if($usecategoryformat==1 && $categorycolumns>1) print '</tr></table></td>';
			$columncount++;
			if($columncount==$categorycolumns){
				print '</tr>';
				$columncount=0;
			}
		}
	}
	if($columncount<$categorycolumns && $columncount != 0){
		while($columncount<$categorycolumns){
			print '<td ' . ($usecategoryformat==2 ? ' colspan="2"' : '') . '>&nbsp;</td>';
			$columncount++;
		}
		print "</tr>";
	}
print '</table><table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="">';
print '<tr><td><img src="images/clearpixel.gif" width="300" height="1" alt="" /></td></tr>';
?>
            </table>
          </td>
        </tr>
      </table>