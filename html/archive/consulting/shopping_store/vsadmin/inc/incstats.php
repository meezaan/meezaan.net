<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$storesessionvalue=="") $storesessionvalue="virtualstore".time();
if($_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE) exit;
$success=TRUE;
$admindatestr="Y-m-d";
if(@$admindateformat=="") $admindateformat=0;
if($admindateformat==1)
	$admindatestr="m/d/Y";
elseif($admindateformat==2)
	$admindatestr="d/m/Y";
$alreadygotadmin = getadminsettings();
$fromdate = trim(@$_POST["fromdate"]);
$todate = trim(@$_POST["todate"]);
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
}else{
	$thefromdate = time()-(60*60*24*365);
	$thetodate = time();
}
?>
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
        <tr>
          <td width="100%" align="center">
			<input type="hidden" name="posted" value="1">
			<input type="hidden" name="act" value="domodify">
            <table width="550" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" align="center"><br /><strong>Sales reports from <?php print date($admindatestr, $thefromdate)?> to <?php print date($admindatestr, $thetodate)?></strong><br />&nbsp;</td>
			  </tr>
			  <tr> 
                <td width="100%" align="center"><strong>Sales results</strong><br />&nbsp;</td>
			  </tr>
<?php
$sSQL = "SELECT COUNT(ordID) AS numorders,SUM(ordTotal) AS theordtot,SUM(ordHandling) AS tothandling,SUM(ordStateTax) AS totstatetax,SUM(ordCountryTax) AS totcountrytax,SUM(ordHSTTax) AS tothsttax,SUM(ordDiscount) AS totdiscount, SUM(ordShipping) AS totshipping FROM orders WHERE ordStatus>=3 AND ordDate BETWEEN '" . date("Y-m-d", $thefromdate) . "' AND '" . date("Y-m-d", $thetodate) . " 23:59:59'";
$result = mysql_query($sSQL) or print(mysql_error());
if(mysql_num_rows($result) > 0){
	print '<tr><td align="center"><table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="left">';
	print '<tr><td><strong>Orders</strong></td><td><strong>Order Total</strong></td><td align="center"><strong>Shipping</strong></td><td align="center"><strong>Handling</strong></td><td align="center"><strong>Discounts</strong></td><td align="center"><strong>State Tax</strong></td><td align="center"><strong>Country Tax</strong></td><td align="center"><strong>Grand Total</strong></td></tr>';
	while($rs = mysql_fetch_assoc($result)){
		print '<tr><td>' . $rs["numorders"] . '</td><td>' . FormatEuroCurrency($rs["theordtot"]) . '</td><td>' . FormatEuroCurrency($rs["totshipping"]) . '</td><td>' . FormatEuroCurrency($rs["tothandling"]) . '</td><td>' . FormatEuroCurrency($rs["totdiscount"]) . '</td><td>' . FormatEuroCurrency($rs["totstatetax"]) . '</td><td>' . FormatEuroCurrency($rs["totcountrytax"]) . '</td><td>' . FormatEuroCurrency(($rs["theordtot"]+$rs["totshipping"]+$rs["tothandling"]+$rs["totstatetax"]+$rs["totcountrytax"]+$rs["tothsttax"])-$rs["totdiscount"]) . '</td></tr>';
	}
	print '</table></td></tr>';
}
?>
			  <tr> 
                <td width="100%" align="center"><strong>Top 100 Sales</strong><br />&nbsp;</td>
			  </tr>
<?php
$sSQL = "SELECT SUM(cartQuantity) AS thecount,cartProdID,cartProdName FROM cart WHERE cartCompleted=1 AND cartDateAdded BETWEEN '" . date("Y-m-d", $thefromdate) . "' AND '" . date("Y-m-d", $thetodate) . " 23:59:59' GROUP BY cartProdID,cartProdName ORDER BY thecount DESC LIMIT 100";
$result = mysql_query($sSQL) or print(mysql_error());
if(mysql_num_rows($result) > 0){
	print '<tr><td align="left"><table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="left">';
	print '<tr><td><strong>Prod ID</strong></td><td><strong>Prod Name</strong></td><td align="center"><strong>Quant sold</strong></td></tr>';
	while($rs = mysql_fetch_assoc($result)){
		print '<tr><td>' . $rs["cartProdID"] . '</td><td>' . $rs["cartProdName"] . '</td><td align="center">' . $rs["thecount"] . '</td></tr>';
	}
	print '</table></td></tr>';
}
?>

			  <tr> 
                <td width="100%" align="center"><strong>Top Countries</strong><br />&nbsp;</td>
			  </tr>
<?php
$sSQL = "SELECT COUNT(ordCountry) AS thecount,ordCountry FROM orders WHERE ordStatus>=3 AND ordDate BETWEEN '" . date("Y-m-d", $thefromdate) . "' AND '" . date("Y-m-d", $thetodate) . " 23:59:59' GROUP BY ordCountry ORDER BY thecount DESC LIMIT 100";
$result = mysql_query($sSQL) or print(mysql_error());
if(mysql_num_rows($result) > 0){
	print '<tr><td align="left"><table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="left">';
	print '<tr><td><strong>Country Name</strong></td><td align="center"><strong>Sales</strong></td></tr>';
	while($rs = mysql_fetch_assoc($result)){
		print '<tr><td>' . $rs["ordCountry"] . '</td><td align="center">' . $rs["thecount"] . '</td></tr>';
	}
	print '</table></td></tr>';
}
?>
			  <tr> 
                <td width="100%" align="center"><br /><a href="admin.php"><strong><?php print $yyAdmHom?></strong></a><br />&nbsp;</td>
			  </tr>
            </table></td>
        </tr>
      </table>