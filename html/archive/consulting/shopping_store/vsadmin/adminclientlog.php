<SCRIPT language="php">
session_cache_limiter('none');
session_start();
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property
//of Internet Business Solutions SL. Any use, reproduction, disclosure or copying
//of any kind without the express and written permission of Internet Business 
//Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
include "db_conn_open.php";
include "includes.php";
include "inc/languageadmin.php";
include "inc/incfunctions.php";
if(@$storesessionvalue=="") $storesessionvalue="virtualstore";
if(@$_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE){
	if(@$_SERVER["HTTPS"] == "on" || @$_SERVER["SERVER_PORT"] == "443")$prot='https://';else $prot='http://';
	header('Location: '.$prot.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/login.php');
	exit;
}
$isprinter=FALSE;
</SCRIPT>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><!-- InstanceBegin template="/Templates/admintemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<!-- InstanceBeginEditable name="doctitle" -->
<title>Admin Client Login</title>
<!-- InstanceEndEditable --><link rel="stylesheet" type="text/css" href="adminstyle.css"/>
<meta http-equiv="Content-Type" content="text/html; charset=<?php print $adminencoding ?>"/>
</head>
<body <?php if($isprinter) print 'class="printbody"'?>>
<?php if(! $isprinter){ ?>

<!-- Header section -->
<div id="header1" align="right"><a class="topbar" href="http://www.ecommercetemplates.com/help.asp" target="_blank"><?php print $yyLLHelp;?></a> &middot;
  <a href="http://www.ecommercetemplates.com/support/default.asp" target="_blank" class="topbar"><?php print $yyLLForu;?></a> &middot; <a href="http://www.ecommercetemplates.com/support/search.asp" target="_blank" class="topbar"><?php print $yyLLForS;?></a> &middot; <a href="http://www.ecommercetemplates.com/updaters.asp" target="_blank" class="topbar"><?php print $yyLLUpda;?></a> &middot; <a class="topbar" href="logout.php"><?php print $yyLLLogO;?></a>&nbsp;&nbsp;</div>
<div id="header"><img src="adminimages/ecommerce_templates.gif" width="278" height="53" alt=""/></div>

<!-- Left menus -->
<div id="left1">
<img src="adminimages/administration.jpg" width="150" height="31" alt=""/><br />
  &nbsp;&middot; <a class="topbar" href="admin.php"><?php print $yyLLHome;?></a><img src="adminimages/hr.gif" alt=""/><br />
  &nbsp;&middot; <a class="topbar" href="adminmain.php"><?php print $yyLLMain;?></a><img src="adminimages/hr.gif" alt=""/><br />
  &nbsp;&middot; <a class="topbar" href="adminorders.php"><?php print $yyLLOrds;?></a><img src="adminimages/hr.gif" alt=""/><br />
  &nbsp;&middot; <a class="topbar" href="adminlogin.php"><?php print $yyLLPass;?></a><img src="adminimages/hr.gif" alt=""/><br />
  &nbsp;&middot; <a class="topbar" href="adminpayprov.php"><?php print $yyLLPayP;?></a><img src="adminimages/hr.gif" alt=""/><br />
&nbsp;&middot; <a class="topbar" href="adminaffil.php"><?php print $yyLLAffl;?></a><img src="adminimages/hr.gif" alt=""/><br />
&nbsp;&middot; <a class="topbar" href="adminclientlog.php"><?php print $yyLLClLo;?></a><img src="adminimages/hr.gif" alt=""/><br />
&nbsp;&middot; <a class="topbar" href="adminordstatus.php"><?php print $yyLLOrSt;?></a></div>

<div id="left2">
<img src="adminimages/product_admin.jpg" width="150" height="31" alt=""/><br />
  &nbsp;&middot; <a class="topbar" href="adminprods.php"><?php print $yyLLProA;?></a><img src="adminimages/hr.gif" alt=""/><br />
  &nbsp;&middot; <a class="topbar" href="adminprodopts.php"><?php print $yyLLProO;?></a><img src="adminimages/hr.gif" alt=""/><br />
  &nbsp;&middot; <a class="topbar" href="admincats.php"><?php print $yyLLCats;?></a><img src="adminimages/hr.gif" alt=""/><br />
  &nbsp;&middot; <a class="topbar" href="admindiscounts.php"><?php print $yyLLDisc;?></a><img src="adminimages/hr.gif" alt=""/><br />
&nbsp;&middot; <a class="topbar" href="adminpricebreak.php"><?php print $yyLLQuan;?></a></div>

<div id="left3"><img src="adminimages/shipping_admin.jpg" width="150" height="31" alt=""/><br />
  &nbsp;&middot; <a class="topbar" href="adminstate.php"><?php print $yyLLStat;?></a><img src="adminimages/hr.gif" alt=""/><br />
  &nbsp;&middot; <a class="topbar" href="admincountry.php"><?php print $yyLLCoun;?></a><img src="adminimages/hr.gif" alt=""/><br />
&nbsp;&middot; <a class="topbar" href="adminzones.php"><?php print $yyLLZone;?></a><img src="adminimages/hr.gif" alt=""/><br />
&nbsp;&middot; <a class="topbar" href="adminuspsmeths.php"><?php print $yyLLShpM;?></a></div>

<div id="left4"><img src="adminimages/extras.jpg" width="150" height="31" alt=""/><br />
  &nbsp;&middot; <a href="http://www.ecommercetemplates.com/affiliateinfo.asp" target="_blank" class="topbar"><?php print $yyLLAffP;?></a><img src="adminimages/hr.gif" alt=""/><br />
  &nbsp;&middot; <a href="http://www.ecommercetemplates.com/addsite.asp" target="_blank" class="topbar"><?php print $yyLLSubm;?></a><img src="adminimages/hr.gif" alt=""/><br />
&nbsp;&middot; <a class="topbar" href="http://www.ecommercetemplates.com/support/default.asp"><?php print $yyLLForu;?></a></div>
<?php } ?>
<!-- main content -->
<!-- InstanceBeginEditable name="Body" -->
<div id="main">
<?php include "inc/incclientlog.php"; ?></div>
<!-- InstanceEndEditable -->


</body>
<!-- InstanceEnd --></html>
