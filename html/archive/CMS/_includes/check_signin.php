<?php
session_start();

//get Site License No
$LICENSE_NOMBER = mysql_fetch_array(mysql_query("SELECT license_number from site_info"));


// is the one accessing this page logged in or not?
if (!isset($_SESSION['logged_on']) || ($_SESSION['logged_on'] != true) || (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != 3155)  ||  (!isset($_SESSION['user_id']) || $_SESSION['license_nomber'] != $LICENSE_NOMBER['license_number']  || (!isset($_SESSION['user_type']))))) {   
// not logged in, move to login page
$SITE = getSiteLoc();
$LOCATION = "/CMS/login.php?errormessage=Please login to access the CMS.";
header("location: .$SITE.$LOCATION");
   exit; 
}

