<?php
session_start();
include ('../_connections/db_connector.php');
include ('../_includes/_functions/stripslashes.php');

//get login time

$TIME = time();
$GM_TIME = $TIME - date('Z', $TIME);

function getMysqlDate($TIME){return date("Y-m-d", $TIME);}
function getMysqlDatetime($TIME){return date("Y-m-d H:i:s", $TIME);}
function getTimestamp($MYSQL_DATETIME){return strtotime($MYSQL_DATETIME);}

$LOGIN_TIME = getMysqlDatetime($GM_TIME); //GMT TIME ALWAYS WILL GO INTO DATABASE

$USER = stripanyslashes($_REQUEST['username']);
$PASSWORD = md5($_REQUEST['password']);


$CHK = "SELECT users.user_id, users.user_password, users.user_type_id, site_info.license_number
FROM users, site_info
WHERE user_username = '".$USER."'";
$CHK_R = mysql_query($CHK) or die (mysql_error());
$NUM_USERS = mysql_num_rows($CHK_R);

if ($NUM_USERS != 1) {
$ERROR = "The username you have entered does not exist.";
header ('location: ../login.php?errormessage='.$ERROR);
exit;
}


$USERINFO = mysql_fetch_array($CHK_R);

$PWD = $USERINFO['user_password'];

if ($PWD == $PASSWORD) {

        $_SESSION['logged_on'] = true;
		$_SESSION['user_admin'] = 3155;
		$_SESSION['user_id'] = $USERINFO['user_id'];
		$_SESSION['user_type'] = $USERINFO['user_type_id'];
		$_SESSION['license_nomber'] = $USERINFO['license_number'];
		
//sessions set,now update login time

mysql_query("UPDATE `users` SET `user_last_login_time` = '".$LOGIN_TIME."'") or die(mysql_error());		

header('location: ../index.php');
exit;

}

if ($PWD != $PASSWORD) {
$ERROR = "You have entered an incorrect password.";
header ('location: ../login.php?errormessage='.$ERROR);
exit;
} 

?>