<?php
include ('../_connections/db_connector.php');
include ('../_includes/_functions/stripslashes.php');
include ('../_includes/_functions/functions.php');

//look for id and key for resetting passwords
$RESETCONFIRMATION = stripanyslashes($_REQUEST['idandkey']);
if ($RESETCONFIRMATION == "submitted") 
{
 //if this is coming from the reset password form 

$ID = stripanyslashes($_REQUEST['id']);
$KEY = stripanyslashes($_REQUEST['key']);
$PWD = stripanyslashes($_REQUEST['_password_']);
$CONFPWD = stripanyslashes($_REQUEST['_confirmpassword_']);

checkEmptyVariable($ID);
checkEmptyVariable($KEY);
checkEmptyVariable($PWD);
checkEmptyVariable($CONFPWD);

checkPasswordMatch($PWD,$CONFPWD);

//The two entered passwords match and none of the passed fields is empty

//Verification:  Pull id keys table
$VERIFY = mysql_query("SELECT `user_id` FROM `pwd_keys` WHERE `key` = '".$KEY."'") or die(mysql_error());
$R_verify = mysql_fetch_row($VERIFY);

if (md5($R_verify['0']) == $ID) { //If the md5 hash for the table userid equals the ID passed, update the password

mysql_query("UPDATE `users` SET `user_password` = '".md5($PWD)."' WHERE `user_id` = '".$R_verify['0']."'"); //Update Password
mysql_query("DELETE FROM `pwd_keys` WHERE `key` = '".$KEY."' AND `user_id` = '".$R_verify['0']."'"); //Delete password reset key

header('location: ../login.php?errormessage=Your password has been reset.  Please login using your new password.'); //redirect to login page
}
else 
{
header('location: ../login.php?inquiry=resetpassword&errormessage=Oops!  Something went wrong.  Please enter your ID and Key and try again.');
} 
}//End resetting password rotuine when id and key are passed

else {

//Start routine where idandkey not submitted
//get request time

$TIME = time();
$GM_TIME = $TIME - date('Z', $TIME);

function getMysqlDate($TIME){return date("Y-m-d", $TIME);}
function getMysqlDatetime($TIME){return date("Y-m-d H:i:s", $TIME);}
function getTimestamp($MYSQL_DATETIME){return strtotime($MYSQL_DATETIME);}

$REQUEST_TIME = getMysqlDatetime($GM_TIME); //GMT TIME ALWAYS WILL GO INTO DATABASE

$USER = stripanyslashes($_REQUEST['username']);
$EMAIL = stripanyslashes($_REQUEST['email']);

checkEmptyVariable($EMAIL);
checkEmptyVariable($USER);

if (CheckEmailFormat($EMAIL)) {

$CHK = "SELECT `user_id` FROM `users` WHERE `user_username` = '".$USER."' AND `user_email` = '".$EMAIL."'";
$CHK_R = mysql_query($CHK) or die (mysql_error());
$NUM_USERS = mysql_num_rows($CHK_R);

if ($NUM_USERS != 1) {
$ERROR = "The username or email address you have entered does not exist.  Please try again.";
header ('location: ../login.php?errormessage='.$ERROR);
exit;
}
//Get user id
$RESULT = mysql_fetch_row($CHK_R);
$USER_ID = $RESULT['0'];

if ($NUM_USERS == 1) {

//Now let's check if a request has already been made in the past that's still in the system
$REQ = mysql_query("SELECT * FROM `pwd_keys` WHERE `user_id` = '".$USER_ID."'");
$NUM_REQS = mysql_num_rows($REQ);

if ($NUM_REQS >= 1) { //if the request already exists, Delete it
mysql_query("DELETE FROM `pwd_keys` WHERE `user_id` = '".$USER_ID."'");
}

//get key
$KEY = md5(rand(1000, 9999999999));
if (mysql_query("INSERT INTO pwd_keys
			( `user_id`, `time`, `key`)
			VALUES
			('".$USER_ID."', '".$REQUEST_TIME."', '".$KEY."')") or die(mysql_error())); 

//email
//Get variables to go in email
$USER_NAME = getUserInfoReturn($USER_ID, 'user_name');
$USERNAME = getUserInfoReturn($USER_ID, 'user_username');
$SITENAME =  getSiteNameReturn();
$SITEADDRESS =  getSiteLocReturn();


$MAIL_MESSAGE = 
	'<html>
	<body>
	Dear '.$USER_NAME.',
	<br />
	<br />
	A request has been made to reset the password associated with the the following username on the Plum CMS installation at '.$SITENAME.':
	<br />
	<br /> 
	'.$USERNAME.'
	<br />
	<br />
	To confirm this reset request and update the password, please click on the following link:  
	<br />
	<br />
	<a href="'.$SITEADDRESS.'/CMS/login.php?inquiry=resetpassword&submission=idandkey&id='.md5($USER_ID).'&key='.$KEY.'">'.$SITEADDRESS.'/CMS/login.php?inquiry=resetpassword&submission=idandkey&id='.md5($USER_ID).'&key='.$KEY.'</a>.
	<br />
	<br />
	If you are unable to click on the above link, or cannot paste it into the address bar of your browser, please visit <a href="'.$SITEADDRESS.'/CMS/login.php?inquiry=resetpassword">'.$SITEADDRESS.'/CMS/login.php?inquiry=resetpassword</a> and enter the following:
	<br />
	<br />
	ID:  '.md5($USER_ID).'
	<br />
	Key: '.$KEY.'
	<br />
	<br />
	If you did not initiate a request to change your password, please ignore this email.
	<br />
	<br />
	For more questions, please contact the Plum CMS Administrator for '.$SITENAME.'.
	<br /><br />
	This is an automated email from an unmonitored email address. Please DO NOT REPLY to it.
	<br />
	<br />
	Thank You.
	<br />
	Plum CMS
	<br />
	Easy as Plum Website Management
	</body>
	</html>'; //Email message complete
	
if (mail("$EMAIL", "Plum CMS - Forgot/Reset Password", "$MAIL_MESSAGE",
	"To: $USER_NAME <$EMAIL>\n" .
    "From: Plum CMS <noreply.plumcms@vafta.com>\n" .
    "MIME-Version: 1.0\n" .
    "Content-type: text/html; charset=iso-8859-1"))
	{ //If email is sent

header('location: ../login.php?errormessage=An email has been sent to the email address you specified.  Please follow the instructions in this email to reset your password.');
exit;

} //End email sent

} //End if No. Users == 1

}  //End Check Email format
else {

header('location: ../login.php?inquiry=forgotpassword&errormessage=Please enter a valid email address.');
exit;
}

}



?>