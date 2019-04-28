<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

// Variables passed
$USERID = stripanyslashes($_REQUEST['userid']);
$NAME = stripanyslashes($_REQUEST['name']);
$USERNAME = stripanyslashes($_REQUEST['username']);
$USERTYPE = stripanyslashes($_REQUEST['user_type_id']);
$EMAIL = stripanyslashes($_REQUEST['email']);
$TEL = stripanyslashes($_REQUEST['tel']);

checkEmptyVariable($USERID);
checkEmptyVariable($NAME);
checkEmptyVariable($USERNAME);
checkEmptyVariable($USERTYPE);
checkEmptyVariable($EMAIL);


//If it gets this far all the fields are filled in properly.  Enter data in database

//Let's check password and confirm password field

checkDuplicateUserEdit($USERNAME,$USERID);

if (CheckEmailFormat($EMAIL)) { //if email format is correct create user

mysql_query("UPDATE `users` SET
				 `user_type_id` = '".$USERTYPE."',
				 `user_name` = '".$NAME."',
				 `user_username` = '".$USERNAME."',
				 `user_email` = '".$EMAIL."',
				 `user_tel` = '".$TEL."' WHERE `user_id` = '".$USERID."'")
				or die(mysql_error());	
			
header('location: ../index.php?section=Users&errormessage='.$MESSAGE_USER_EDITED_SUCCESSFULLY);
}
else {
header('location: ../index.php?section=Users&function=Add User&errormessage=Please enter a valid email address.');
}

?>