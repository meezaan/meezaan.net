<?php
// This code is copyright Internet Business Solutions SL.
// Unauthorized copying, use or transmittal without the
// express permission of Internet Business Solutions SL
// is strictly prohibited.
// Author: Vince Reid, vince@virtualred.net
$sVersion='PHP v5.3.3';
?><html>
<head>
<title>Create Ecommerce Plus mySQL database version <?php print $sVersion?></title>
<STYLE type="text/css">
<!--
p {  font: 10pt Verdana, Arial, Helvetica, sans-serif}
TD {  font: 10pt Verdana, Arial, Helvetica, sans-serif}
BODY {  font: 10pt Verdana, Arial, Helvetica, sans-serif}
-->
</STYLE>
</head>
<body>
<?php include "vsadmin/db_conn_open.php" ?>
<?php

$haserrors=FALSE;
function print_sql_error(){
	global $haserrors;
	$haserrors=TRUE;
	print('<font color="#FF0000">' . mysql_error() . "</font><br>");
}

if(@$_POST["posted"]=="1"){

// mysql_query("DROP TABLE address,admin,affiliates,cart,cartoptions,clientlogin,countries,coupons,cpnassign,customerlogin,dropshipper,installedmods,ipblocking,multibuyblock,multisections,optiongroup,options,orders,orderstatus,payprovider,postalzones,pricebreaks,prodoptions,products,relatedprods,sections,states,tmplogin,uspsmethods,zonecharges") or print_sql_error();

$sSQL = "CREATE TABLE address (addID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
$sSQL .= "addCustID INT DEFAULT 0,";
$sSQL .= "addIsDefault TINYINT DEFAULT 0,";
$sSQL .= "addName VARCHAR(255) NULL,";
$sSQL .= "addAddress VARCHAR(255) NULL,";
$sSQL .= "addAddress2 VARCHAR(255) NULL,";
$sSQL .= "addCity VARCHAR(255) NULL,";
$sSQL .= "addState VARCHAR(255) NULL,";
$sSQL .= "addZip VARCHAR(255) NULL,";
$sSQL .= "addCountry VARCHAR(255) NULL,";
$sSQL .= "addPhone VARCHAR(255) NULL,";
$sSQL .= "addShipFlags TINYINT DEFAULT 0,";
$sSQL .= "addExtra1 VARCHAR(255) NULL,";
$sSQL .= "addExtra2 VARCHAR(255) NULL)";
mysql_query($sSQL) or print_sql_error();
mysql_query("ALTER TABLE address ADD INDEX (addCustID)") or print_sql_error();
	
$sSQL = "CREATE TABLE admin (adminID INT NOT NULL PRIMARY KEY,";
$sSQL .= "adminVersion VARCHAR(100),";
$sSQL .= "adminUser VARCHAR(50) NULL,";
$sSQL .= "adminPassword VARCHAR(50) NULL,";
$sSQL .= "adminEmail VARCHAR(75) NULL,";
$sSQL .= "adminStoreURL VARCHAR(255) NULL,";
$sSQL .= "adminProdsPerPage INT DEFAULT 0,";
$sSQL .= "adminShipping INT DEFAULT 0,";
$sSQL .= "adminIntShipping INT DEFAULT 0,";
$sSQL .= "adminCountry INT DEFAULT 0,";
$sSQL .= "adminZipCode VARCHAR(50) NULL,";
$sSQL .= "adminUSPSUser VARCHAR(255) NULL,";
$sSQL .= "adminUSPSpw VARCHAR(255) NULL,";
$sSQL .= "adminUPSUser VARCHAR(255) NULL,";
$sSQL .= "adminUPSpw VARCHAR(255) NULL,";
$sSQL .= "adminUPSAccess VARCHAR(255) NULL,";
$sSQL .= "FedexAccountNo VARCHAR(255) NULL,";
$sSQL .= "FedexMeter VARCHAR(255) NULL,";
$sSQL .= "adminCanPostUser VARCHAR(255) NULL,";
$sSQL .= "adminEmailConfirm TINYINT DEFAULT 0,";
$sSQL .= "adminPacking TINYINT DEFAULT 0,";
$sSQL .= "adminDelUncompleted INT DEFAULT 0,";
$sSQL .= "adminUSZones TINYINT DEFAULT 0,";
$sSQL .= "adminUnits TINYINT DEFAULT 0,";
$sSQL .= "adminStockManage INT DEFAULT 0,";
$sSQL .= "adminHandling DOUBLE DEFAULT 0,";
$sSQL .= "adminTweaks INT DEFAULT 0,";
$sSQL .= "adminCert TEXT NULL,";
$sSQL .= "adminUPSLicense TEXT NULL,";
$sSQL .= "adminDelCC INT DEFAULT 0,";
$sSQL .= "adminClearCart INT DEFAULT 0,";
$sSQL .= "adminlanguages INT DEFAULT 0,";
$sSQL .= "adminlangsettings INT DEFAULT 0,";
$sSQL .= "currRate1 DOUBLE DEFAULT 0,";
$sSQL .= "currSymbol1 VARCHAR(50) NULL,";
$sSQL .= "currRate2 DOUBLE DEFAULT 0,";
$sSQL .= "currSymbol2 VARCHAR(50) NULL,";
$sSQL .= "currRate3 DOUBLE DEFAULT 0,";
$sSQL .= "currSymbol3 VARCHAR(50) NULL,";
$sSQL .= "currConvUser VARCHAR(50) NULL,";
$sSQL .= "currConvPw VARCHAR(50) NULL,";
$sSQL .= "currLastUpdate DATETIME)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE affiliates (affilID VARCHAR(32) NOT NULL PRIMARY KEY,";
$sSQL .= "affilPW VARCHAR(32),";
$sSQL .= "affilEmail VARCHAR(128),";
$sSQL .= "affilName VARCHAR(255),";
$sSQL .= "affilAddress VARCHAR(255),";
$sSQL .= "affilCity VARCHAR(255),";
$sSQL .= "affilState VARCHAR(255),";
$sSQL .= "affilZip VARCHAR(255),";
$sSQL .= "affilCountry VARCHAR(255),";
$sSQL .= "affilInform TINYINT DEFAULT 0,";
$sSQL .= "affilCommision DOUBLE DEFAULT 0)";

mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE cart (cartID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
$sSQL .= "cartSessionID VARCHAR(100),";
$sSQL .= "cartProdID VARCHAR(50),";
$sSQL .= "cartProdName VARCHAR(255),";
$sSQL .= "cartProdPrice DOUBLE,";
$sSQL .= "cartDateAdded DATETIME,";
$sSQL .= "cartQuantity INT DEFAULT 0,";
$sSQL .= "cartOrderID INT DEFAULT 0,";
$sSQL .= "cartClientID INT DEFAULT 0,";
$sSQL .= "cartCompleted TINYINT)";
mysql_query($sSQL) or print_sql_error();
mysql_query("ALTER TABLE cart ADD INDEX (cartClientID)") or print_sql_error();

$sSQL = "CREATE TABLE cartoptions (coID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
$sSQL .= "coCartID INT,";
$sSQL .= "coOptID INT,";
$sSQL .= "coOptGroup VARCHAR(255),";
$sSQL .= "coCartOption VARCHAR(255),";
$sSQL .= "coPriceDiff DOUBLE DEFAULT 0,";
$sSQL .= "coWeightDiff DOUBLE DEFAULT 0)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE countries (countryID INT NOT NULL PRIMARY KEY,";
$sSQL .= "countryName VARCHAR(255),";
$sSQL .= "countryName2 VARCHAR(255),";
$sSQL .= "countryName3 VARCHAR(255),";
$sSQL .= "countryEnabled TINYINT DEFAULT 0,";
$sSQL .= "countryTax DOUBLE DEFAULT 0,";
$sSQL .= "countryOrder INT DEFAULT 0,";
$sSQL .= "countryZone INT DEFAULT 0,";
$sSQL .= "countryLCID VARCHAR(50),";
$sSQL .= "countryCurrency VARCHAR(50),";
$sSQL .= "countryCode VARCHAR(50),";
$sSQL .= "countryFreeShip TINYINT DEFAULT 0)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE coupons (cpnID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
$sSQL .= "cpnName VARCHAR(255) NULL,";
$sSQL .= "cpnName2 VARCHAR(255) NULL,";
$sSQL .= "cpnName3 VARCHAR(255) NULL,";
$sSQL .= "cpnWorkingName VARCHAR(255),";
$sSQL .= "cpnNumber VARCHAR(255),";
$sSQL .= "cpnType INT DEFAULT 0,";
$sSQL .= "cpnEndDate DATETIME,";
$sSQL .= "cpnDiscount DOUBLE DEFAULT 0,";
$sSQL .= "cpnThreshold DOUBLE DEFAULT 0,";
$sSQL .= "cpnThresholdMax DOUBLE DEFAULT 0,";
$sSQL .= "cpnThresholdRepeat DOUBLE DEFAULT 0,";
$sSQL .= "cpnQuantity INT DEFAULT 0,";
$sSQL .= "cpnQuantityMax INT DEFAULT 0,";
$sSQL .= "cpnQuantityRepeat INT DEFAULT 0,";
$sSQL .= "cpnNumAvail INT DEFAULT 0,";
$sSQL .= "cpnCntry TINYINT DEFAULT 0,";
$sSQL .= "cpnIsCoupon TINYINT DEFAULT 0,";
$sSQL .= "cpnSitewide TINYINT DEFAULT 0)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE cpnassign (cpaID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
$sSQL .= "cpaCpnID INT DEFAULT 0,";
$sSQL .= "cpaType TINYINT DEFAULT 0,";
$sSQL .= "cpaAssignment VARCHAR(255))";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE customerlogin (clID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
$sSQL .= "clUserName VARCHAR(50) NULL,";
$sSQL .= "clPW VARCHAR(50) NULL,";
$sSQL .= "clLoginLevel TINYINT DEFAULT 0,";
$sSQL .= "clPercentDiscount DOUBLE DEFAULT 0,";
$sSQL .= "clActions INT DEFAULT 0,";
$sSQL .= "clEmail VARCHAR(255) NULL,";
$sSQL .= "clDateCreated DATETIME)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE dropshipper (dsID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
$sSQL .= "dsName VARCHAR(255) NULL,";
$sSQL .= "dsEmail VARCHAR(255) NULL,";
$sSQL .= "dsAddress VARCHAR(255) NULL,";
$sSQL .= "dsCity VARCHAR(255) NULL,";
$sSQL .= "dsState VARCHAR(255) NULL,";
$sSQL .= "dsZip VARCHAR(255) NULL,";
$sSQL .= "dsCountry VARCHAR(255) NULL,";
$sSQL .= "dsAction INT DEFAULT 0)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE installedmods (modkey VARCHAR(255) PRIMARY KEY,modtitle VARCHAR(255) NOT NULL, modauthor VARCHAR(255) NULL, modauthorlink VARCHAR(255) NULL, modversion VARCHAR(255) NULL, modectversion VARCHAR(255) NULL, modlink VARCHAR(255) NULL, moddate DATETIME NOT NULL, modnotes TEXT NULL)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE ipblocking (dcid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
$sSQL .= "dcip1 INT DEFAULT 0,";
$sSQL .= "dcip2 INT DEFAULT 0)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE mailinglist (email VARCHAR(255) PRIMARY KEY,emailFormat TINYINT DEFAULT 0)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE multibuyblock (ssdenyid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
$sSQL .= "ssdenyip VARCHAR(255) NOT NULL,";
$sSQL .= "sstimesaccess INT DEFAULT 0,";
$sSQL .= "lastaccess DATETIME)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE multisections (pID VARCHAR(128) NOT NULL,";
$sSQL .= "pSection INT DEFAULT 0 NOT NULL,";
$sSQL .= "PRIMARY KEY (pID, pSection))";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE optiongroup (optGrpID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
$sSQL .= "optGrpName VARCHAR(255),";
$sSQL .= "optGrpName2 VARCHAR(255),";
$sSQL .= "optGrpName3 VARCHAR(255),";
$sSQL .= "optGrpWorkingName VARCHAR(255),";
$sSQL .= "optType INT DEFAULT 0,";
$sSQL .= "optFlags INT DEFAULT 0,";
$sSQL .= "optGrpSelect TINYINT(1) DEFAULT 0)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE options (optID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
$sSQL .= "optGroup INT,";
$sSQL .= "optName VARCHAR(255),";
$sSQL .= "optName2 VARCHAR(255),";
$sSQL .= "optName3 VARCHAR(255),";
$sSQL .= "optPriceDiff DOUBLE DEFAULT 0,";
$sSQL .= "optWholesalePriceDiff DOUBLE DEFAULT 0,";
$sSQL .= "optWeightDiff DOUBLE DEFAULT 0,";
$sSQL .= "optStock INT DEFAULT 0,";
$sSQL .= "optRegExp VARCHAR(255),";
$sSQL .= "optDefault TINYINT(1) DEFAULT 0)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE orders (ordID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
$sSQL .= "ordSessionID VARCHAR(255),";
$sSQL .= "ordName VARCHAR(255),";
$sSQL .= "ordAddress VARCHAR(255),";
$sSQL .= "ordAddress2 VARCHAR(255),";
$sSQL .= "ordCity VARCHAR(255),";
$sSQL .= "ordState VARCHAR(255),";
$sSQL .= "ordZip VARCHAR(255),";
$sSQL .= "ordCountry VARCHAR(255),";
$sSQL .= "ordEmail VARCHAR(255),";
$sSQL .= "ordPhone VARCHAR(255),";
$sSQL .= "ordShipName VARCHAR(255),";
$sSQL .= "ordShipAddress VARCHAR(255),";
$sSQL .= "ordShipAddress2 VARCHAR(255),";
$sSQL .= "ordShipCity VARCHAR(255),";
$sSQL .= "ordShipState VARCHAR(255),";
$sSQL .= "ordShipZip VARCHAR(255),";
$sSQL .= "ordShipCountry VARCHAR(255),";
$sSQL .= "ordShipPhone VARCHAR(255),";
$sSQL .= "ordAuthNumber VARCHAR(255),";
$sSQL .= "ordAffiliate VARCHAR(255),";
$sSQL .= "ordPayProvider INT DEFAULT 0,";
$sSQL .= "ordTransID VARCHAR(255) NULL,";
$sSQL .= "ordShipping DOUBLE DEFAULT 0,";
$sSQL .= "ordStateTax DOUBLE DEFAULT 0,";
$sSQL .= "ordCountryTax DOUBLE DEFAULT 0,";
$sSQL .= "ordHSTTax DOUBLE DEFAULT 0,";
$sSQL .= "ordHandling DOUBLE DEFAULT 0,";
$sSQL .= "ordShipType VARCHAR(255),";
$sSQL .= "ordShipCarrier INT DEFAULT 0,";
$sSQL .= "ordClientID INT DEFAULT 0,";
$sSQL .= "ordTotal DOUBLE DEFAULT 0,";
$sSQL .= "ordDate DATETIME,";
$sSQL .= "ordIP VARCHAR(255),";
$sSQL .= "ordDiscount DOUBLE DEFAULT 0,";
$sSQL .= "ordDiscountText VARCHAR(255),";
$sSQL .= "ordExtra1 VARCHAR(255) NULL,";
$sSQL .= "ordExtra2 VARCHAR(255) NULL,";
$sSQL .= "ordShipExtra1 VARCHAR(255) NULL,";
$sSQL .= "ordShipExtra2 VARCHAR(255) NULL,";
$sSQL .= "ordCheckoutExtra1 VARCHAR(255) NULL,";
$sSQL .= "ordCheckoutExtra2 VARCHAR(255) NULL,";
$sSQL .= "ordTrackNum VARCHAR(255) NULL,";
$sSQL .= "ordAVS VARCHAR(255) NULL,";
$sSQL .= "ordCVV VARCHAR(255) NULL,";
$sSQL .= "ordAddInfo TEXT,";
$sSQL .= "ordCNum TEXT NULL,";
$sSQL .= "ordComLoc TINYINT DEFAULT 0,";
$sSQL .= "ordStatus TINYINT DEFAULT 0,";
$sSQL .= "ordStatusDate DATETIME,";
$sSQL .= "ordStatusInfo TEXT NULL,";
$sSQL .= "ordInvoice VARCHAR(255) NULL)";
mysql_query($sSQL) or print_sql_error();
mysql_query("ALTER TABLE orders ADD INDEX (ordClientID)") or print_sql_error();

$sSQL = "CREATE TABLE orderstatus (statID INT PRIMARY KEY,";
$sSQL .= "statPrivate VARCHAR(255) NULL,";
$sSQL .= "statPublic VARCHAR(255) NULL,";
$sSQL .= "statPublic2 VARCHAR(255) NULL,";
$sSQL .= "statPublic3 VARCHAR(255) NULL)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE payprovider (payProvID INT NOT NULL PRIMARY KEY,";
$sSQL .= "payProvName VARCHAR(255),";
$sSQL .= "payProvShow VARCHAR(255),";
$sSQL .= "payProvShow2 VARCHAR(255),";
$sSQL .= "payProvShow3 VARCHAR(255),";
$sSQL .= "payProvEnabled TINYINT,";
$sSQL .= "payProvAvailable TINYINT,";
$sSQL .= "payProvDemo TINYINT,";
$sSQL .= "payProvData1 VARCHAR(255),";
$sSQL .= "payProvData2 VARCHAR(255),";
$sSQL .= "payProvData3 VARCHAR(255),";
$sSQL .= "payProvOrder INT DEFAULT 0,";
$sSQL .= "payProvMethod INT DEFAULT 0,";
$sSQL .= "payProvLevel INT DEFAULT 0)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE postalzones (pzID INT NOT NULL PRIMARY KEY,";
$sSQL .= "pzName VARCHAR(50),";
$sSQL .= "pzMultiShipping TINYINT DEFAULT 0,";
$sSQL .= "pzMethodName1 VARCHAR(255) NULL,";
$sSQL .= "pzMethodName2 VARCHAR(255) NULL,";
$sSQL .= "pzMethodName3 VARCHAR(255) NULL,";
$sSQL .= "pzMethodName4 VARCHAR(255) NULL,";
$sSQL .= "pzMethodName5 VARCHAR(255) NULL,";
$sSQL .= "pzFSA TINYINT DEFAULT 1)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE pricebreaks (pbQuantity INT NOT NULL,";
$sSQL .= "pbProdID VARCHAR(255) NOT NULL,";
$sSQL .= "pPrice DOUBLE DEFAULT 0,";
$sSQL .= "pWholesalePrice DOUBLE DEFAULT 0,";
$sSQL .= "PRIMARY KEY(pbProdID,pbQuantity))";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE prodoptions (poID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
$sSQL .= "poProdID VARCHAR(128),";
$sSQL .= "poOptionGroup INT)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE products (pID VARCHAR(128) NOT NULL PRIMARY KEY,";
$sSQL .= "pName VARCHAR(255),";
$sSQL .= "pName2 VARCHAR(255),";
$sSQL .= "pName3 VARCHAR(255),";
$sSQL .= "pSection INT,";
$sSQL .= "pDescription TEXT,";
$sSQL .= "pDescription2 TEXT,";
$sSQL .= "pDescription3 TEXT,";
$sSQL .= "pLongdescription TEXT,";
$sSQL .= "pLongdescription2 TEXT,";
$sSQL .= "pLongdescription3 TEXT,";
$sSQL .= "pImage VARCHAR(255),";
$sSQL .= "pLargeimage VARCHAR(255),";
$sSQL .= "pDownload VARCHAR(255) NULL,";
$sSQL .= "pPrice DOUBLE DEFAULT 0,";
$sSQL .= "pListPrice DOUBLE DEFAULT 0,";
$sSQL .= "pWholesalePrice DOUBLE DEFAULT 0,";
$sSQL .= "pShipping DOUBLE DEFAULT 0,";
$sSQL .= "pShipping2 DOUBLE DEFAULT 0,";
$sSQL .= "pWeight DOUBLE DEFAULT 0,";
$sSQL .= "pDisplay TINYINT(1) DEFAULT 1,";
$sSQL .= "pSell TINYINT(1) DEFAULT 1,";
$sSQL .= "pStaticPage TINYINT(1) DEFAULT 0,";
$sSQL .= "pStockByOpts TINYINT(1) DEFAULT 0,";
$sSQL .= "pRecommend TINYINT(1) DEFAULT 0,";
$sSQL .= "pExemptions TINYINT DEFAULT 0,";
$sSQL .= "pInStock INT DEFAULT 0,";
$sSQL .= "pDropship INT DEFAULT 0,";
$sSQL .= "pDims VARCHAR(255) NULL,";
$sSQL .= "pTax DOUBLE NULL,";
$sSQL .= "pOrder INT DEFAULT 0,";
$sSQL .= "INDEX (pOrder))";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE relatedprods (rpProdID VARCHAR(128) NOT NULL,";
$sSQL .= "rpRelProdID VARCHAR(128) NOT NULL,";
$sSQL .= "PRIMARY KEY (rpProdID, rpRelProdID))";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE sections (sectionID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
$sSQL .= "sectionName VARCHAR(255),";
$sSQL .= "sectionName2 VARCHAR(255),";
$sSQL .= "sectionName3 VARCHAR(255),";
$sSQL .= "sectionWorkingName VARCHAR(255),";
$sSQL .= "sectionurl VARCHAR(255),";
$sSQL .= "sectionImage VARCHAR(255),";
$sSQL .= "sectionDescription TEXT,";
$sSQL .= "sectionDescription2 TEXT,";
$sSQL .= "sectionDescription3 TEXT,";
$sSQL .= "topSection INT DEFAULT 0,";
$sSQL .= "rootSection INT DEFAULT 0,";
$sSQL .= "sectionOrder INT DEFAULT 0,";
$sSQL .= "sectionDisabled TINYINT DEFAULT 0)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE states (stateID INT NOT NULL PRIMARY KEY,";
$sSQL .= "stateName VARCHAR(50),";
$sSQL .= "stateAbbrev VARCHAR(50),";
$sSQL .= "stateTax DOUBLE DEFAULT 0,";
$sSQL .= "stateEnabled TINYINT,";
$sSQL .= "stateZone INT DEFAULT 0,";
$sSQL .= "stateFreeShip TINYINT DEFAULT 1)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE tmplogin (tmploginid VARCHAR(100) PRIMARY KEY,";
$sSQL .= "tmploginname VARCHAR(50) NULL,";
$sSQL .= "tmploginchk INT DEFAULT 0,";
$sSQL .= "tmplogindate DATETIME)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE uspsmethods (uspsID INT PRIMARY KEY,";
$sSQL .= "uspsMethod VARCHAR(150) NOT NULL,";
$sSQL .= "uspsShowAs VARCHAR(150) NOT NULL,";
$sSQL .= "uspsUseMethod TINYINT DEFAULT 0,";
$sSQL .= "uspsFSA TINYINT DEFAULT 0,";
$sSQL .= "uspsLocal TINYINT DEFAULT 0)";
mysql_query($sSQL) or print_sql_error();

$sSQL = "CREATE TABLE zonecharges (zcID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
$sSQL .= "zcZone INT DEFAULT 0,";
$sSQL .= "zcWeight DOUBLE DEFAULT 0,";
$sSQL .= "zcRate DOUBLE DEFAULT 0,";
$sSQL .= "zcRate2 DOUBLE DEFAULT 0,";
$sSQL .= "zcRate3 DOUBLE DEFAULT 0,";
$sSQL .= "zcRate4 DOUBLE DEFAULT 0,";
$sSQL .= "zcRate5 DOUBLE DEFAULT 0,";
$sSQL .= "zcRatePC TINYINT(1) DEFAULT 0,";
$sSQL .= "zcRatePC2 TINYINT(1) DEFAULT 0,";
$sSQL .= "zcRatePC3 TINYINT(1) DEFAULT 0,";
$sSQL .= "zcRatePC4 TINYINT(1) DEFAULT 0,";
$sSQL .= "zcRatePC5 TINYINT(1) DEFAULT 0)";
mysql_query($sSQL) or print_sql_error();

// Dumping admin table
print('Adding admin table data<br>');
$guessURL = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/';
mysql_query("INSERT INTO admin (adminID,adminVersion,adminUser,adminPassword,adminEmail,adminStoreURL,adminProdsPerPage,adminShipping,adminCountry,adminZipCode,adminUSPSUser,adminUSPSpw,adminEmailConfirm,adminPacking,adminDelUncompleted,adminUSZones,adminUnits,adminStockManage,adminHandling,adminTweaks,adminCert,adminDelCC,adminUPSUser,adminUPSpw,adminUPSAccess,currLastUpdate) VALUES (1,'Ecommerce Plus " . $sVersion . "','mystore','changeme','you@yoursite.com','" . mysql_escape_string($guessURL) . "',8,2,1,'YOURZIP','','',0,0,4,0,1,0,0,0,'',7,'','','','" . date("Y-m-d H:i:s", time()-100000) . "')");
// Dumping cart table
print('Adding cart table data<br>');
mysql_query("INSERT INTO cart (cartID,cartSessionID,cartProdID,cartProdName,cartProdPrice,cartDateAdded,cartQuantity,cartOrderID,cartCompleted) VALUES (1,'935000845','pc001','#1 PC multimedia package',1200,'" . date("Y-m-d H:i:s", time()) . "',1,501,1)");
// Dumping cartoptions table
print('Adding cartoptions table data<br>');
mysql_query("INSERT INTO cartoptions (coID,coCartID,coOptID,coOptGroup,coCartOption,coPriceDiff) VALUES (1,1,23,'Processor','Intel Pentium IV 1.5GHz',25.5)");
mysql_query("INSERT INTO cartoptions (coID,coCartID,coOptID,coOptGroup,coCartOption,coPriceDiff) VALUES (2,1,28,'Hard Disk','60 Gigabytes',34)");
mysql_query("INSERT INTO cartoptions (coID,coCartID,coOptID,coOptGroup,coCartOption,coPriceDiff) VALUES (3,1,30,'Monitor','15\" Standard',0)");
mysql_query("INSERT INTO cartoptions (coID,coCartID,coOptID,coOptGroup,coCartOption,coPriceDiff) VALUES (4,1,35,'Network Card','Yes',15)");
// Dumping countries table
print('Adding countries table data<br>');
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (1,'United States of America',1,0,2,1,'en_US','USD','US')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (2,'Canada',1,0,0,2,'en_CA','CAD','CA')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (3,'Afghanistan',0,0,0,4,'','AFA','AF')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (4,'Albania',0,0,0,4,'','ALL','AL')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (5,'Algeria',0,0,0,4,'','DZD','DZ')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (6,'Andorra',0,0,0,3,'','EUR','AD')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (7,'Angola',0,0,0,4,'','AOA','AO')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (8,'Anguilla',0,0,0,4,'','XCD','AI')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (10,'Antigua and Barbuda',0,0,0,4,'','XCD','AG')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (11,'Argentina',1,0,0,2,'es_AR','ARS','AR')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (12,'Armenia',0,0,0,4,'','AMD','AM')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (13,'Aruba',0,0,0,4,'','AWG','AW')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (14,'Australia',1,0,0,4,'en_AU','AUD','AU')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (15,'Austria',1,0,0,3,'de_AT','EUR','AT')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (16,'Azerbaijan',0,0,0,4,'','AZM','AZ')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (17,'Bahamas',1,0,0,4,'en_US','BSD','BS')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (18,'Bahrain',0,0,0,4,'','BHD','BH')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (19,'Bangladesh',0,0,0,4,'','BDT','BD')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (20,'Barbados',0,0,0,4,'','BBD','BB')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (21,'Belarus',0,0,0,4,'','BYR','BY')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (22,'Belgium',1,0,0,3,'fr_BE','EUR','BE')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (23,'Belize',0,0,0,4,'','BZD','BZ')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (24,'Benin',0,0,0,4,'','XOF','BJ')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (25,'Bermuda',0,0,0,4,'','BMD','BM')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (26,'Bhutan',0,0,0,4,'','BTN','BT')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (27,'Bolivia',0,0,0,2,'','BOB','BO')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (28,'Bosnia-Herzegovina',0,0,0,4,'','BAM','BA')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (29,'Botswana',0,0,0,4,'','BWP','BW')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (30,'Brazil',1,0,0,2,'pt_BR','BRL','BR')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (31,'Brunei Darussalam',0,0,0,4,'','BND','BN')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (32,'Bulgaria',0,0,0,4,'','BGL','BG')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (33,'Burkina Faso',0,0,0,4,'','XOF','BF')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (34,'Burundi',0,0,0,4,'','BIF','BI')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (35,'Cambodia',0,0,0,4,'','KHR','KH')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (36,'Cameroon',0,0,0,4,'','XAF','CM')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (37,'Cape Verde',0,0,0,4,'','CVE','CV')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (38,'Cayman Islands',0,0,0,4,'','KYD','KY')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (39,'Central African Republic',0,0,0,4,'','XAF','CF')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (40,'Chad',0,0,0,4,'','XAF','TD')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (41,'Chile',1,0,0,2,'es_CL','CHL','CL')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (42,'China',0,0,0,4,'','CNY','CN')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (43,'Colombia',0,0,0,2,'','COP','CO')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (44,'Comoros',0,0,0,4,'','KMF','KM')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (45,'Costa Rica',1,0,0,2,'es_CR','CRI','CR')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (46,'Croatia',0,0,0,4,'','HRK','HR')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (47,'Cuba',0,0,0,4,'','CUP','CU')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (48,'Cyprus',0,0,0,4,'','CYP','CY')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (49,'Czech Republic',0,0,0,4,'','CZK','CZ')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (50,'Denmark',1,0,0,3,'da_DK','DKK','DK')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (51,'Djibouti',0,0,0,4,'','DJF','DJ')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (52,'Dominica',0,0,0,4,'','XCD','DM')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (53,'Dominican Republic',1,0,0,4,'','DOP','DO')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (54,'East Timor',0,0,0,4,'','IDR','TP')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (55,'Ecuador',0,0,0,4,'','USD','EC')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (56,'Egypt',0,0,0,4,'','EGP','EG')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (57,'El Salvador',0,0,0,2,'','SVC','SV')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (58,'Equatorial Guinea',0,0,0,4,'','XAF','GQ')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (59,'Estonia',0,0,0,4,'','EEK','EE')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (60,'Ethiopia',0,0,0,4,'','ETB','ET')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (61,'Falkland Islands',0,0,0,4,'','FKP','FK')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (62,'Faroe Islands',0,0,0,4,'','DKK','FO')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (63,'Fiji',0,0,0,4,'','FJD','FJ')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (64,'Finland',1,0,0,3,'su_FI','EUR','FI')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (65,'France',1,0,0,3,'fr_FR','EUR','FR')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (66,'French Guiana',0,0,0,4,'','EUR','GF')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (67,'French Polynesia',0,0,0,4,'','XPF','PF')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (68,'Gabon',0,0,0,4,'','XAF','GA')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (69,'Gambia',0,0,0,4,'','GMD','GM')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (70,'Georgia, Republic of',0,0,0,4,'','GEL','GE')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (71,'Germany',1,0,0,3,'de_DE','EUR','DE')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (72,'Ghana',0,0,0,4,'','GHC','GH')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (73,'Gibraltar',1,0,0,3,'','GBP','GI')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (74,'Greece',1,0,0,3,'el_GR','EUR','GR')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (75,'Greenland',1,0,0,3,'','DKK','GL')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (76,'Grenada',0,0,0,4,'','XCD','GD')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (77,'Guadeloupe',0,0,0,4,'','EUR','GP')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (78,'Guam',0,0,0,1,'','USD','GU')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (79,'Guatemala',1,0,0,2,'es_GT','GTQ','GT')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (80,'Guinea',0,0,0,4,'','GNF','GN')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (81,'Guinea-Bissau',0,0,0,4,'','XOF','GW')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (82,'Guyana',0,0,0,2,'','GYD','GY')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (83,'Haiti',0,0,0,4,'','USD','HT')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (84,'Honduras',0,0,0,2,'','HNL','HN')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (85,'Hong Kong',1,0,0,4,'en_HK','HKD','HK')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (86,'Hungary',0,0,0,4,'hu_HU','HUF','HU')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (87,'Iceland',1,0,0,3,'','ISK','IS')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (88,'India',0,0,0,4,'en_IN','INR','IN')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (89,'Indonesia',0,0,0,4,'','IDR','ID')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (90,'Iraq',0,0,0,4,'','IQD','IQ')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (91,'Ireland',1,0,0,3,'en_IE','EUR','IE')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (92,'Israel',1,0,0,4,'','ILS','IL')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (93,'Italy',1,0,0,3,'it_IT','EUR','IT')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (94,'Jamaica',0,0,0,4,'','JMD','JM')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (95,'Japan',1,0,0,4,'jp_JP','JPY','JP')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (96,'Jordan',0,0,0,4,'','JOD','JO')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (97,'Kazakhstan',0,0,0,4,'','KZT','KZ')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (98,'Kenya',0,0,0,4,'','KES','KE')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (99,'Kiribati',0,0,0,4,'','AUD','KI')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (100,'North Korea',0,0,0,4,'ko_KR','KPW','KP')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (101,'South Korea',0,0,0,4,'','KRW','KR')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (102,'Kuwait',0,0,0,4,'','KWD','KW')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (103,'Latvia',0,0,0,4,'','LVL','LV')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (104,'Lebanon',0,0,0,4,'','LBP','LB')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (105,'Lesotho',0,0,0,4,'','LSL','LS')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (106,'Liberia',0,0,0,4,'','LRD','LR')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (107,'England',0,0,0,3,'','GBP','GB')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (108,'Liechtenstein',0,0,0,4,'','CHF','LI')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (109,'Lithuania',0,0,0,4,'','LTL','LT')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (110,'Luxembourg',1,0,0,3,'','EUR','LU')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (111,'Macao',0,0,0,4,'','MOP','MO')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (112,'Macedonia, Republic of',0,0,0,4,'','MKD','MK')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (113,'Madagascar',0,0,0,4,'','MGF','MG')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (114,'Malawi',0,0,0,4,'','MWK','MW')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (115,'Malaysia',1,0,0,4,'','MYR','MY')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (116,'Maldives',0,0,0,4,'','MVR','MV')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (117,'Mali',0,0,0,4,'','XOF','ML')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (118,'Malta',0,0,0,4,'','MTL','MT')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (119,'Martinique',0,0,0,4,'','EUR','MQ')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (120,'Mauritania',0,0,0,4,'','MRO','MR')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (121,'Mauritius',0,0,0,4,'','MUR','MU')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (122,'Mexico',1,0,0,2,'es_MX','MXN','MX')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (123,'Moldova',0,0,0,4,'','MDL','MD')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (124,'Monaco',1,0,0,3,'','FRF','MC')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (125,'Mongolia',0,0,0,4,'','MNT','MN')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (126,'Montserrat',0,0,0,4,'','XCD','MS')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (127,'Morocco',0,0,0,4,'','MAD','MA')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (128,'Mozambique',0,0,0,4,'','MZM','MZ')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (129,'Myanmar',0,0,0,4,'','MMK','MM')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (130,'Namibia',0,0,0,4,'','NAD','NA')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (131,'Nauru',0,0,0,4,'','AUD','NR')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (132,'Nepal',0,0,0,4,'','NPR','NP')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (133,'Netherlands',1,0,0,3,'nl_NL','EUR','NL')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (134,'Netherlands Antilles',0,0,0,4,'','ANG','AN')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (135,'New Caledonia',0,0,0,4,'','XPF','NC')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (136,'New Zealand',1,0,0,4,'en_NZ','NZD','NZ')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (137,'Nicaragua',0,0,0,2,'','NIO','NI')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (138,'Niger',0,0,0,4,'','XOF','NE')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (139,'Nigeria',0,0,0,4,'','NGN','NG')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (140,'Niue',0,0,0,4,'','NZD','NU')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (141,'Norfolk Island',0,0,0,4,'','AUD','NF')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (142,'Northern Ireland',1,0,0,3,'en_GB','GBP','GB')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (143,'Norway',1,0,0,3,'no_NO','NOK','NO')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (144,'Oman',0,0,0,4,'','OMR','OM')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (145,'Pakistan',0,0,0,4,'','PKR','PK')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (146,'Panama',1,0,0,2,'es_PA','PAB','PA')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (147,'Papua New Guinea',0,0,0,4,'','PGK','PG')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (148,'Paraguay',0,0,0,4,'','PYG','PY')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (149,'Peru',0,0,0,2,'','PEN','PE')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (150,'Philippines',0,0,0,4,'','PHP','PH')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (151,'Pitcairn Island',0,0,0,4,'','NZD','PN')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (152,'Poland',0,0,0,4,'','PLN','PL')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (153,'Portugal',1,0,0,3,'pt_PT','EUR','PT')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (154,'Qatar',0,0,0,4,'','QAR','QA')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (155,'Reunion',0,0,0,4,'','EUR','RE')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (156,'Romania',0,0,0,4,'','RON','RO')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (157,'Russia',0,0,0,4,'','RUB','RU')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (158,'Rwanda',0,0,0,4,'','RWF','RW')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (159,'Saint Kitts',0,0,0,4,'','XCD','KN')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (160,'Saint Lucia',0,0,0,4,'','XCD','LC')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (161,'Saint Vincent and the Grenadines',0,0,0,4,'','XCD','VC')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (162,'Western Samoa',0,0,0,4,'','WST','WS')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (163,'San Marino',0,0,0,4,'','EUR','SM')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (164,'Sao Tome and Principe',0,0,0,4,'','STD','ST')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (165,'Saudi Arabia',0,0,0,4,'','SAR','SA')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (166,'Senegal',0,0,0,4,'','XOF','SN')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (167,'Seychelles',0,0,0,4,'','SCR','SC')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (168,'Sierra Leone',0,0,0,4,'','SLL','SL')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (169,'Singapore',1,0,0,4,'','SGD','SG')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (170,'Slovak Republic',0,0,0,4,'','SKK','SK')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (171,'Slovenia',0,0,0,4,'','SIT','SI')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (172,'Solomon Islands',0,0,0,4,'','SBD','SB')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (173,'Somalia',0,0,0,4,'','SOS','SO')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (174,'South Africa',0,0,0,4,'en_ZA','ZAR','ZA')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (175,'Spain',1,0,0,3,'es_ES','EUR','ES')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (176,'Sri Lanka',0,0,0,4,'','LKR','LK')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (177,'Saint Helena',0,0,0,4,'','SHP','SH')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (178,'Saint Pierre and Miquelon',0,0,0,4,'','EUR','PM')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (179,'Sudan',0,0,0,4,'','SDD','SD')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (180,'Suriname',0,0,0,4,'','SRG','SR')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (181,'Swaziland',0,0,0,4,'','SZL','SZ')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (182,'Sweden',1,0,0,3,'sv_SE','SEK','SE')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (183,'Switzerland',1,0,0,3,'fr_CH','CHF','CH')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (184,'Syrian Arab Republic',0,0,0,4,'','SYP','SY')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (185,'Taiwan',1,0,0,4,'','TWD','TW')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (186,'Tajikistan',0,0,0,4,'','TJS','TJ')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (187,'Tanzania',0,0,0,4,'','TZS','TZ')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (188,'Thailand',1,0,0,4,'','THB','TH')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (189,'Togo',0,0,0,4,'','XOF','TG')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (190,'Tokelau',0,0,0,4,'','NZD','TK')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (191,'Tonga',0,0,0,4,'','TOP','TO')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (192,'Trinidad and Tobago',0,0,0,4,'','TTD','TT')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (193,'Tunisia',0,0,0,4,'','TND','TN')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (194,'Turkey',0,0,0,4,'','TRL','TR')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (195,'Turkmenistan',0,0,0,4,'','TMM','TM')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (196,'Turks and Caicos Islands',0,0,0,4,'','USD','TC')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (197,'Tuvalu',0,0,0,4,'','TVD','TV')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (198,'Uganda',0,0,0,4,'','UGX','UG')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (199,'Ukraine',0,0,0,4,'','UAH','UA')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (200,'United Arab Emirates',0,0,0,4,'','AED','AE')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (201,'Great Britain',1,0,1,3,'en_GB','GBP','GB')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (202,'Uruguay',0,0,0,4,'','UYU','UY')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (203,'Uzbekistan',0,0,0,4,'','UZS','UZ')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (204,'Vanuatu',0,0,0,4,'','VUV','VU')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (205,'Vatican City',1,0,0,3,'','ITL','VA')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (206,'Venezuela',0,0,0,2,'','VEB','VE')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (207,'Vietnam',0,0,0,4,'','VND','VN')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (208,'British Virgin Islands',0,0,0,4,'','USD','VG')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (209,'Wallis and Futuna Islands',0,0,0,4,'','XPF','WF')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (210,'Yemen',0,0,0,4,'','YER','YE')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (211,'Zambia',0,0,0,4,'','ZMK','ZM')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (212,'Zimbabwe',0,0,0,4,'','ZWD','ZW')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (213,'Iran',0,0,0,4,'','IRR','IR')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (214,'Channel Islands',0,0,0,3,'','GBP','GB')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (215,'Puerto Rico',0,0,0,3,'','USD','PR')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (216,'Isle of Man',0,0,0,3,'','GBP','GB')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (217,'Azores',0,0,0,3,'','EUR','PT')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (218,'Corsica',0,0,0,3,'','EUR','FR')");
mysql_query("INSERT INTO countries (countryID,countryName,countryEnabled,countryTax,countryOrder,countryZone,countryLCID,countryCurrency,countryCode) VALUES (219,'Balearic Islands',0,0,0,3,'','EUR','ES')");
// Dumping optionGroup table
print('Adding optionGroup table data<br>');
mysql_query("INSERT INTO optiongroup (optGrpID,optGrpName,optGrpWorkingName,optType,optGrpSelect) VALUES (1,'Color','Color',2,1)");
mysql_query("INSERT INTO optiongroup (optGrpID,optGrpName,optGrpWorkingName,optType,optGrpSelect) VALUES (2,'Size','Size (Jackets)',2,1)");
mysql_query("INSERT INTO optiongroup (optGrpID,optGrpName,optGrpWorkingName,optType,optGrpSelect) VALUES (4,'Size','Size (Socks)',2,1)");
mysql_query("INSERT INTO optiongroup (optGrpID,optGrpName,optGrpWorkingName,optType,optGrpSelect) VALUES (6,'Processor','Processor (Multimedia)',2,1)");
mysql_query("INSERT INTO optiongroup (optGrpID,optGrpName,optGrpWorkingName,optType,optGrpSelect) VALUES (7,'Hard Disk','Hard Disk',2,1)");
mysql_query("INSERT INTO optiongroup (optGrpID,optGrpName,optGrpWorkingName,optType,optGrpSelect) VALUES (8,'Monitor','Monitor',2,1)");
mysql_query("INSERT INTO optiongroup (optGrpID,optGrpName,optGrpWorkingName,optType,optGrpSelect) VALUES (9,'Network Card','Network Card',2,1)");
mysql_query("INSERT INTO optiongroup (optGrpID,optGrpName,optGrpWorkingName,optType,optGrpSelect) VALUES (10,'Processor','Processor (Portables)',2,1)");
// Dumping options table
print('Adding options table data<br>');
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (1,1,'Blue',0,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (2,1,'Red',0,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (3,1,'Green',0,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (4,1,'Yellow',0,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (5,2,'Small',0,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (6,2,'Medium',1,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (7,2,'Large',1.5,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (8,2,'X-Large',2,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (9,2,'XX-Large',2.2,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (12,4,'8',0,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (13,4,'8 1/2',0.1,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (14,4,'9',0.15,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (15,4,'9 1/2',0.2,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (16,4,'10',0.25,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (21,6,'Intel Pentium III 1.3GHz',0,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (22,6,'Intel Pentium III 1.4GHz',15,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (23,6,'Intel Pentium IV 1.5GHz',25.5,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (24,6,'Intel Pentium IV 1.7GHz',45,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (25,6,'Intel Pentium IV 2.0GHz',65,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (26,7,'20 Gigabytes',0,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (27,7,'40 Gigabytes',10,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (28,7,'60 Gigabytes',34,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (29,7,'80 Gigabytes',44.5,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (30,8,'15\" Standard',0,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (31,8,'17\" Trinitron',22,5)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (32,8,'19\" Flatron',75,10)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (33,8,'21\" Supertron',185,20)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (34,9,'No',0,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (35,9,'Yes',15,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (36,10,'Pentium III 1.0 GHz',0,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (37,10,'Pentium III 1.3 GHz',33,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (38,10,'Pentium IV 1.5 GHz',50,0)");
mysql_query("INSERT INTO options (optID,optGroup,optName,optPriceDiff,optWeightDiff) VALUES (39,10,'Pentium IV 1.7 GHz',75,0)");
// Dumping orders table
print('Adding orders table data<br>');
mysql_query("INSERT INTO orders (ordID,ordSessionID,ordName,ordAddress,ordCity,ordState,ordZip,ordCountry,ordEmail,ordPhone,ordShipName,ordShipAddress,ordShipCity,ordShipState,ordShipZip,ordShipCountry,ordPayProvider,ordAuthNumber,ordShipping,ordStateTax,ordCountryTax,ordShipType,ordTotal,ordDate,ordStatusDate,ordIP,ordHandling,ordAddInfo,ordStatus,ordStatusInfo) VALUES (501,'935000845','A Customer','1212 The Street','San Jose','California','90210','United States of America','info@ecommercetemplates.com','1121212121212','','','','','','United States of America',4,'Email Only',2.5,0,0,'',1274.5,'" . date("Y-m-d H:i:s", time()) . "','" . date("Y-m-d H:i:s", time()) . "','192.168.0.1',0,'This is just an example order. It is also here to make sure your order numbers do not start at zero, which just doesn\'t look good.',3,'')");
// Dumping orderstatus table
print('Adding orderstatus table data<br>');
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (0,'Cancelled','Order Cancelled')");
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (1,'Deleted','Order Deleted')");
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (2,'Unauthorized','Awaiting Payment')");
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (3,'Authorized','Payment Received')");
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (4,'Packing','In Packing')");
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (5,'Shipping','In Shipping')");
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (6,'Shipped','Order Shipped')");
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (7,'Completed','Order Completed')");
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (8,'','')");
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (9,'','')");
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (10,'','')");
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (11,'','')");
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (12,'','')");
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (13,'','')");
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (14,'','')");
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (15,'','')");
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (16,'','')");
mysql_query("INSERT INTO orderstatus (statID,statPrivate,statPublic) VALUES (17,'','')");
// Dumping payprovider table
print('Adding payprovider table data<br>');
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (1,'PayPal','PayPal',0,1,0,'','',1)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (2,'2Checkout','Credit Card',0,1,0,'','',2)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (3,'Auth.net SIM','Credit Card',0,1,0,'','',3)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (4,'Email','Email',1,1,0,'','',4)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (5,'World Pay','Credit Card',0,1,0,'','',5)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (6,'NOCHEX','NOCHEX',0,1,0,'','',6)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (7,'Payflow Pro','Credit Card',0,1,0,'','',7)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (8,'Payflow Link','Credit Card',0,1,0,'','',8)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (9,'SECPay','Credit Card',0,1,0,'','',9)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (10,'Capture Card','Credit Card',0,1,0,'XXXXXOOOOOOO','',10)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (11,'PSiGate','Credit Card',0,1,0,'','',11)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (12,'PSiGate SSL','Credit Card',0,1,0,'','',12)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (13,'Auth.net AIM','Credit Card',0,1,0,'','',13)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (14,'Custom','Credit Card',0,1,0,'','',14)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (15,'Netbanx','Credit Card',0,1,0,'','',15)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (16,'Linkpoint','Credit Card',0,1,0,'','',16)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (17,'Email 2','Email 2',0,1,0,'','',17)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (18,'PayPal Direct','Credit Card',0,1,0,'','',18)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (19,'PayPal Express','PayPal Express',0,1,0,'','',19)");
mysql_query("INSERT INTO payprovider (payProvID,payProvName,payProvShow,payProvEnabled,payProvAvailable,payProvDemo,payProvData1,payProvData2,payProvOrder) VALUES (20,'Google Checkout','Google Checkout',0,1,0,'','',20)") or print_sql_error();
// Dumping postalzones table
print('Adding postalzones table data<br>');
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (1,'United States')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (2,'Zone 2')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (3,'Zone 3')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (4,'Zone 4')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (5,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (6,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (7,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (8,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (9,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (10,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (11,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (12,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (13,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (14,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (15,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (16,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (17,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (18,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (19,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (20,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (21,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (22,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (23,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (24,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (101,'All States')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (102,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (103,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (104,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (105,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (106,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (107,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (108,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (109,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (110,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (111,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (112,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (113,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (114,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (115,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (116,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (117,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (118,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (119,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (120,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (121,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (122,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (123,'')");
mysql_query("INSERT INTO postalzones (pzID,pzName) VALUES (124,'')");
mysql_query("UPDATE postalzones SET pzMethodName1='Standard Shipping',pzMethodName2='Express Shipping'");
// Dumping prodoptions table
print('Adding prodoptions table data<br>');
mysql_query("INSERT INTO prodoptions (poID,poProdID,poOptionGroup) VALUES (9,'monitor001',8)");
mysql_query("INSERT INTO prodoptions (poID,poProdID,poOptionGroup) VALUES (21,'palmtop001',6)");
mysql_query("INSERT INTO prodoptions (poID,poProdID,poOptionGroup) VALUES (22,'palmtop001',7)");
mysql_query("INSERT INTO prodoptions (poID,poProdID,poOptionGroup) VALUES (23,'mouse001',1)");
mysql_query("INSERT INTO prodoptions (poID,poProdID,poOptionGroup) VALUES (25,'portable001',10)");
mysql_query("INSERT INTO prodoptions (poID,poProdID,poOptionGroup) VALUES (26,'pc001',8)");
mysql_query("INSERT INTO prodoptions (poID,poProdID,poOptionGroup) VALUES (27,'pc001',6)");
mysql_query("INSERT INTO prodoptions (poID,poProdID,poOptionGroup) VALUES (28,'pc001',7)");
mysql_query("INSERT INTO prodoptions (poID,poProdID,poOptionGroup) VALUES (29,'pc001',9)");
mysql_query("INSERT INTO prodoptions (poID,poProdID,poOptionGroup) VALUES (30,'testproduct',4)");
// Dumping Products table
print('Adding products table data<br>');
mysql_query("INSERT INTO products (pID,pName,pSection,pDescription,pLongdescription,pImage,pLargeimage,pPrice,pShipping,pShipping2,pWeight,pDisplay,pSell,pExemptions,pInStock) VALUES ('fscanner001','Professional Scanner',2,'600 dpi full color quality for professional quality scanning results. Twice the resolution and twice the quality for your scans, but at an incredible low price.','600 dpi full color quality for professional quality scanning results. Twice the resolution and twice the quality for your scans, but at an incredible low price.<br>As well as a larger image, you can use this \"Long Description\" to add extra detail or information about your products.','prodimages/scanner2.gif','prodimages/lscanner2.gif',120,5,0,4.04,1,1,0,4)");
mysql_query("INSERT INTO products (pID,pName,pSection,pDescription,pLongdescription,pImage,pLargeimage,pPrice,pShipping,pShipping2,pWeight,pDisplay,pSell,pExemptions,pInStock) VALUES ('inkjet001','Inkjet Printer',4,'This inkjet printer really packs a punch for the home user. Full color prints at photo quality. Perfect for everything from letters to the bank manager, to printing out your favourite digital family pictures.','This inkjet printer really packs a punch for the home user. Full color prints at photo quality. Perfect for everything from letters to the bank manager, to printing out your favourite digital family pictures.<br>As well as a larger image, you can use this \"Long Description\" to add extra detail or information about your products.','prodimages/inkjetprinter.gif','prodimages/linkjetprinter.gif',95,4,0,2.02,1,1,0,0)");
mysql_query("INSERT INTO products (pID,pName,pSection,pDescription,pLongdescription,pImage,pLargeimage,pPrice,pShipping,pShipping2,pWeight,pDisplay,pSell,pExemptions,pInStock) VALUES ('keyboard001','PC Keyboard',3,'With ergonomic tactile key action, this is a \"must buy\" for all PC users, home and professional alike. Connects via your PC's serial or PS2 port.','With ergonomic tactile key action, this is a \"must buy\" for all PC users, home and professional alike. Connects via your PC's serial or PS2 port.<br>As well as a larger image, you can use this \"Long Description\" to add extra detail or information about your products.','prodimages/keyboard.gif','prodimages/lkeyboard.gif',19,5,0,1,1,0,0,0)");
mysql_query("INSERT INTO products (pID,pName,pSection,pDescription,pLongdescription,pImage,pLargeimage,pPrice,pShipping,pShipping2,pWeight,pDisplay,pSell,pExemptions,pInStock) VALUES ('lprinter001','Laser Printer',4,'For the small or home office, this laser printer is the perfect solution. Up to 15 black and white pages per minute, and a full 600dpi resolution for the quality your business demands.','For the small or home office, this laser printer is the perfect solution. Up to 15 black and white pages per minute, and a full 600dpi resolution for the quality your business demands.<br>As well as a larger image, you can use this \"Long Description\" to add extra detail or information about your products.','prodimages/laserprinter.gif','prodimages/llaserprinter.gif',499,5,0,2,1,1,0,0)");
mysql_query("INSERT INTO products (pID,pName,pSection,pDescription,pLongdescription,pImage,pLargeimage,pPrice,pShipping,pShipping2,pWeight,pDisplay,pSell,pExemptions,pInStock) VALUES ('monitor001','PC Monitor',3,'17\" full color flat screen monitor, with 0.25 dot resolution and 16.25\" viewable area.','17\" full color flat screen monitor, with 0.25 dot resolution and 16.25\" viewable area.<br>As well as a larger image, you can use this \"Long Description\" to add extra detail or information about your products.','prodimages/monitor.gif','prodimages/lmonitor.gif',299,5,0,2.15,1,1,0,0)");
mysql_query("INSERT INTO products (pID,pName,pSection,pDescription,pLongdescription,pImage,pLargeimage,pPrice,pShipping,pShipping2,pWeight,pDisplay,pSell,pExemptions,pInStock) VALUES ('mouse001','PC Mouse',3,'Indispensible for using your PC, this mouse has easyglide action and simple connectivity to get your PC up and surfing the internet in no time.','Indispensible for using your PC, this mouse has easyglide action and simple connectivity to get your PC up and surfing the internet in no time.<br>As well as a larger image, you can use this \"Long Description\" to add extra detail or information about your products.','prodimages/mouse.gif','prodimages/lmouse.gif',7,1,0,0.15,1,1,0,0)");
mysql_query("INSERT INTO products (pID,pName,pSection,pDescription,pLongdescription,pImage,pLargeimage,pPrice,pShipping,pShipping2,pWeight,pDisplay,pSell,pExemptions,pInStock) VALUES ('palmtop001','Palmtop Computer',1,'The very latest in palmtop technology. All the power of a PC in a pocket sized system. Great for the mobile business person.','The very latest in palmtop technology. All the power of a PC in a pocket sized system. Great for the mobile business person.<br>As well as a larger image, you can use this \"Long Description\" to add extra detail or information about your products.','prodimages/palmtop.gif','prodimages/lpalmtop.gif',199,5,0,4.12,1,1,0,0)");
mysql_query("INSERT INTO products (pID,pName,pSection,pDescription,pLongdescription,pImage,pLargeimage,pPrice,pShipping,pShipping2,pWeight,pDisplay,pSell,pExemptions,pInStock) VALUES ('pc001','#1 PC multimedia package',1,'This is an example of how you can use the product options to create advanced product descriptions with automatic price calculations.','Internet ready PC package. Just choose your monitor, hard disk size, processor speed and network card.<br>As well as a larger image, you can use this \"Long Description\" to add extra detail or information about your products. You can also include HTML Markup in the short and long product descriptions.','prodimages/pc.gif','prodimages/lpc.gif',1200,10,0,6,1,1,0,10)");
mysql_query("INSERT INTO products (pID,pName,pSection,pDescription,pLongdescription,pImage,pLargeimage,pPrice,pShipping,pShipping2,pWeight,pDisplay,pSell,pExemptions,pInStock) VALUES ('portable001','Portable PC',1,'For those on the go, this portable PC is just the thing. Your choice of processor, 256mb ram and 4gb harddisk make this the perfect solution for all types of applications. Buy now while stocks last.','For those on the go, this portable PC is just the thing. Your choice of processor, 256mb ram and 4gb harddisk make this the perfect solution for all types of applications. Buy now while stocks last.<br>As well as a larger image, you can use this \"Long Description\" to add extra detail or information about your products.','prodimages/portable.gif','prodimages/lportable.gif',1250,6,0,2,1,1,0,0)");
mysql_query("INSERT INTO products (pID,pName,pSection,pDescription,pLongdescription,pImage,pLargeimage,pPrice,pShipping,pShipping2,pWeight,pDisplay,pSell,pExemptions,pInStock) VALUES ('scanner001','Flatbed scanner',2,'Up to 300 dpi full color resolution and incredible speed make this a top choice for all your scanning needs. Scan professional quality photos, text or artwork in seconds.','Up to 300 dpi full color resolution and incredible speed make this a top choice for all your scanning needs. Scan professional quality photos, text or artwork in seconds.<br>As well as a larger image, you can use this \"Long Description\" to add extra detail or information about your products.','prodimages/scanner.gif','prodimages/lscanner.gif',89,6,0,5.1,1,1,0,0)");
mysql_query("INSERT INTO products (pID,pName,pSection,pDescription,pLongdescription,pImage,pLargeimage,pPrice,pShipping,pShipping2,pWeight,pDisplay,pSell,pExemptions,pInStock) VALUES ('serialcab001','PC Serial Cable',3,'Can be used for connecting PC systems to peripheral devices such as serial printers and scanners.','Can be used for connecting PC systems to peripheral devices such as serial printers and scanners.<br>As well as a larger image, you can use this \"Long Description\" to add extra detail or information about your products.','prodimages/computercable.gif','prodimages/lcomputercable.gif',2.5,0.2,0,0.1,1,1,0,0)");
mysql_query("INSERT INTO products (pID,pName,pSection,pDescription,pLongdescription,pImage,pLargeimage,pPrice,pShipping,pShipping2,pWeight,pDisplay,pSell,pExemptions,pInStock) VALUES ('testproduct','Cheap Test Product',3,'This is a cheap product for testing. Note how you can use HTML Markup in product descriptions.<br>Also note that as you change the product options, the price changes automatically.','This is a cheap product for testing. Note how you can use HTML Markup in product descriptions.<br>In the long description you can go into more detail about products.','prodimages/computercable.gif','prodimages/lcomputercable.gif',0.01,0,0,3,1,1,0,21)");
// Dumping sections table
print('Adding sections table data<br>');
mysql_query("INSERT INTO sections (sectionID,sectionName,sectionWorkingName,sectionImage,sectionDescription,topSection,sectionOrder,rootSection) VALUES (1,'Systems','Systems','','Complete PC systems including tower systems, laptops and palmtop computers. The very best in PC power.',5,3,1)");
mysql_query("INSERT INTO sections (sectionID,sectionName,sectionWorkingName,sectionImage,sectionDescription,topSection,sectionOrder,rootSection) VALUES (2,'Scanners','Scanners','','RGB color scanners and scanner based systems for everything from digital snaps to professional prints.',6,5,1)");
mysql_query("INSERT INTO sections (sectionID,sectionName,sectionWorkingName,sectionImage,sectionDescription,topSection,sectionOrder,rootSection) VALUES (3,'Peripherals','Peripherals','','Keyboards, mice, cables and mousemats and all your other PC peripheral needs.',5,2,1)");
mysql_query("INSERT INTO sections (sectionID,sectionName,sectionWorkingName,sectionImage,sectionDescription,topSection,sectionOrder,rootSection) VALUES (4,'Printers','Printers','','Inkjet and laser printers for the very best in home and small office printing systems.',6,6,1)");
mysql_query("INSERT INTO sections (sectionID,sectionName,sectionWorkingName,sectionImage,sectionDescription,topSection,sectionOrder,rootSection) VALUES (5,'Computer Parts','Computer Parts','','Bits and pieces for your computer',0,1,0)");
mysql_query("INSERT INTO sections (sectionID,sectionName,sectionWorkingName,sectionImage,sectionDescription,topSection,sectionOrder,rootSection) VALUES (6,'Printers and Scanners','Printers and Scanners','','Printers and scanners for your PC',0,4,0)");
// Dumping States table
print('Adding States table data<br>');
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (2,'Alabama','AL',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (3,'Alaska','AK',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (4,'American Samoa','AS',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (5,'Arizona','AZ',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (6,'Arkansas','AR',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (7,'California','CA',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (8,'Colorado','CO',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (9,'Connecticut','CT',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (10,'Delaware','DE',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (11,'District Of Columbia','DC',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (12,'Fdr. States Of Micronesia','FM',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (13,'Florida','FL',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (14,'Georgia','GA',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (15,'Guam','GU',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (16,'Hawaii','HI',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (17,'Idaho','ID',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (18,'Illinois','IL',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (19,'Indiana','IN',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (20,'Iowa','IA',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (21,'Kansas','KS',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (22,'Kentucky','KY',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (23,'Louisiana','LA',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (24,'Maine','ME',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (25,'Marshall Islands','MH',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (26,'Maryland','MD',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (27,'Massachusetts','MA',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (28,'Michigan','MI',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (29,'Minnesota','MN',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (30,'Mississippi','MS',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (31,'Missouri','MO',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (32,'Montana','MT',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (33,'Nebraska','NE',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (34,'Nevada','NV',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (35,'New Hampshire','NH',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (36,'New Jersey','NJ',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (37,'New Mexico','NM',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (38,'New York','NY',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (39,'North Carolina','NC',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (40,'North Dakota','ND',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (41,'Northern Mariana Islands','MP',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (42,'Ohio','OH',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (43,'Oklahoma','OK',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (44,'Oregon','OR',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (45,'Palau','PW',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (46,'Pennsylvania','PA',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (47,'Puerto Rico','PR',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (48,'Rhode Island','RI',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (49,'South Carolina','SC',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (50,'South Dakota','SD',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (51,'Tennessee','TN',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (52,'Texas','TX',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (53,'Utah','UT',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (54,'Vermont','VT',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (55,'Virgin Islands','VI',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (56,'Virginia','VA',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (57,'Washington','WA',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (58,'West Virginia','WV',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (59,'Wisconsin','WI',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (60,'Wyoming','WY',0,1,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (61,'Armed Forces Africa','AE',0,0,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (62,'Armed Forces Americas','AA',0,0,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (63,'Armed Forces Canada','AE',0,0,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (64,'Armed Forces Europe','AE',0,0,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (65,'Armed Forces Middle East','AE',0,0,101)");
mysql_query("INSERT INTO states (stateID,stateName,stateAbbrev,stateTax,stateEnabled,stateZone) VALUES (66,'Armed Forces Pacific','AP',0,0,101)");
// Dumping uspsmethods table
print('Adding uspsmethods table data<br>');
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (1,'EXPRESS','Express Mail',0,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (2,'PRIORITY','Priority Mail',0,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal,uspsFSA) VALUES (3,'PARCEL','Parcel Post',1,1,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (4,'Global Express Guaranteed Document Service','Global Express Guaranteed',0,0)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (5,'Global Express Guaranteed Non-Document Service','Global Express Guaranteed',0,0)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (6,'Global Express Mail (EMS)','Global Express Mail (EMS)',0,0)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (7,'Global Priority Mail - Flat-rate Envelope (Large)','Global Priority Mail',0,0)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (8,'Global Priority Mail - Flat-rate Envelope (Small)','Global Priority Mail',0,0)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (9,'Global Priority Mail - Variable Weight (Single)','Global Priority Mail',0,0)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (10,'Airmail Letter-post','Airmail Letter Post',0,0)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (11,'Airmail Parcel Post','Airmail Parcel Post',1,0)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (12,'Economy (Surface) Letter-post','Economy (Surface) Letter Post',0,0)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (13,'Economy (Surface) Parcel Post','Economy (Surface) Parcel Post',1,0)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (14,'Media','Media Mail',0,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (15,'BPM','Bound Printed Matter',0,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (16,'FIRST CLASS','First-Class Mail',0,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (101,'01','UPS Next Day Air&reg;',1,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (102,'02','UPS 2nd Day Air&reg;',1,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal,uspsFSA) VALUES (103,'03','UPS Ground',1,1,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (104,'07','UPS Worldwide Express',1,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (105,'08','UPS Worldwide Expedited',1,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (106,'11','UPS Standard',1,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (107,'12','UPS 3 Day Select&reg;',1,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (108,'13','UPS Next Day Air Saver&reg;',1,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (109,'14','UPS Next Day Air&reg; Early A.M.&reg;',1,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (110,'54','UPS Worldwide Express Plus',1,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (111,'59','UPS 2nd Day Air A.M.&reg;',1,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (112,'65','UPS Express Saver',1,1)");
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal,uspsFSA) VALUES (201,'1010','Regular',1,1,1)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (202,'1020','Expedited',1,1)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (203,'1030','Xpresspost',1,1)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (204,'1040','Priority Courier',1,1)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (205,'1120','Expedited Evening',1,1)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (206,'1130','XpressPost Evening',1,1)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (207,'1220','Expedited Saturday',1,1)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (208,'1230','XpressPost Saturday',1,1)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (210,'2005','Small Packets Surface',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (211,'2010','Surface USA',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (212,'2015','Small Packets Air USA',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (213,'2020','Air USA',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (214,'2025','Expedited USA Commercial',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (215,'2030','XPressPost USA',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (216,'2040','Purolator USA',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (217,'2050','PuroPak USA',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (218,'3005','Small Packets Surface International',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (221,'3010','Parcel Surface International',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (222,'3015','Small Packets Air International',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (223,'3020','Air International',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (224,'3025','XPressPost International',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (225,'3040','Purolator International',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (226,'3050','PuroPak International',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (301,'PRIORITYOVERNIGHT','FedEx Priority Overnight®',1,1)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (302,'STANDARDOVERNIGHT','FedEx Standard Overnight®',1,1)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (303,'FIRSTOVERNIGHT','FedEx First Overnight®',1,1)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (304,'FEDEX2DAY','FedEx 2Day®',1,1)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (305,'FEDEXEXPRESSSAVER','FedEx Express Saver®',1,1)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (306,'INTERNATIONALPRIORITY','FedEx International Priority® ',1,1)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (307,'INTERNATIONALECONOMY','FedEx International Economy®',1,1)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (308,'INTERNATIONALFIRST','FedEx International Next Flight® ',1,1)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (310,'FEDEX1DAYFREIGHT','FedEx 1Day Freight®',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (311,'FEDEX2DAYFREIGHT','FedEx 2Day Freight®',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (312,'FEDEX3DAYFREIGHT','FedEx 3Day Freight®',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal,uspsFSA) VALUES (313,'FEDEXGROUND','FedEx Ground®',1,0,1)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (314,'GROUNDHOMEDELIVERY','FedEx Home Delivery®',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (315,'INTERNATIONALPRIORITYFREIGHT','FedEx International Priority Freight®',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (316,'INTERNATIONALECONOMYFREIGHT','FedEx International Economy Freight®',1,0)") or print_sql_error();
mysql_query("INSERT INTO uspsmethods (uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal) VALUES (317,'EUROPEFIRSTINTERNATIONALPRIORITY','FedEx Europe First® - Int''l Priority',1,1)") or print_sql_error();

// Dumping zonecharges table
print('Adding zonecharges table data<br>');
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (1,1,0.2,0.3,0.4)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (2,1,0.5,0.5,0.6)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (3,1,1,0.9,1.0)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (4,1,1.5,1.3,1.4)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (5,1,2,1.5,1.6)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (6,1,5,2,2.1)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (7,1,-1,0.5,0.6)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (8,2,0.2,0.4,0.5)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (9,2,0.5,0.7,0.8)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (10,2,1,1.1,1.2)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (11,2,1.5,1.6,1.7)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (12,2,2,2,2.1)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (13,2,5,3,3.1)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (14,2,-1,0.7,0.8)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (15,3,-1.1,0.8,0.9)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (16,3,0.2,0.5,0.6)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (17,3,0.5,0.8,0.9)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (18,3,1,1.2,1.3)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (19,3,1.5,1.7,1.8)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (20,3,2,2.2,2.3)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (21,3,5,3.2,3.3)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (22,4,-1,1,1.1)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (23,4,1,1.5,1.6)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (24,4,2,2.8,2.9)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (25,4,3,3.8,3.9)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (26,4,4,4.8,4.9)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (27,101,-1,1,1.1)");
mysql_query("INSERT INTO zonecharges (zcID,zcZone,zcWeight,zcRate,zcRate2) VALUES (28,101,1,1,1.1)");

mysql_query("UPDATE countries SET countryName2=countryName,countryName3=countryName") or print_sql_error();
mysql_query("UPDATE orderstatus SET statPublic2=statPublic,statPublic3=statPublic") or print_sql_error();
mysql_query("UPDATE payprovider SET payProvShow2=payProvShow,payProvShow3=payProvShow") or print_sql_error();

if($haserrors)
	print('<font color="#FF0000"><b>Completed, but with errors !</b></font><br>');
else
	print('<font color="#FF0000"><b>Everything installed successfully !</b></font><br>');

mysql_close($dbh);

}else{

?>
<form action="createdb.php" method="POST">
<input type="hidden" name="posted" value="1">
<table width="100%">
<tr><td align="center" width="100%">
<p>&nbsp;</p>
<p><?php print "When reporting support issues, please quote your PHP version number which is " . phpversion();?></p>
<?php
$sSQL = "SELECT version() AS theversion";
$result = mysql_query($sSQL) or print(mysql_error());
$rs = mysql_fetch_assoc($result);
print "<p>mySQL Version is " . $rs["theversion"] . "</p>";
?>
<p>&nbsp;</p>
<p>Please click below to start your installation.</p>
<p>&nbsp;</p>
<p>After performing the installation, please delete this file from your web.</p>
<p>&nbsp;</p>
<input type="submit" value="Install Ecommerce Plus version <?php print $sVersion?>">
<p>&nbsp;</p>
<p>&nbsp;</p>
</td></tr>
</table>
</form>
<?php
}
?>
</body>
</html>