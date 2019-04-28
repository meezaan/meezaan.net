<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

// Variables passed
$NAME = stripanyslashes($_REQUEST['name']);
$USERNAME = stripanyslashes($_REQUEST['username']);
$PASSWORD = stripanyslashes($_REQUEST['password']);
$CONFPASSWORD = stripanyslashes($_REQUEST['confirmpassword']);
$USERTYPE = stripanyslashes($_REQUEST['user_type_id']);
$EMAIL = stripanyslashes($_REQUEST['email']);
$TEL = stripanyslashes($_REQUEST['tel']);

checkEmptyVariable($NAME);
checkEmptyVariable($USERNAME);
checkEmptyVariable($PASSWORD);
checkEmptyVariable($CONFPASSWORD);
checkEmptyVariable($USERTYPE);
checkEmptyVariable($EMAIL);


//If it gets this far all the fields are filled in properly.  Enter data in database

//Let's check password and confirm password field
checkPasswordMatch($PASSWORD, $CONFPASSWORD);

checkDuplicateUser($USERNAME);

if (CheckEmailFormat($EMAIL)) { //if email format is correct create user

mysql_query("INSERT INTO `users` 
				(`user_type_id`, `user_name`,`user_username`, `user_password`, `user_email`, `user_tel`)
				VALUES 
				('".$USERTYPE."', '".$NAME."', '".$USERNAME."', '".md5($PASSWORD)."', '".$EMAIL."', '".$TEL."')")
				or die(mysql_error());	
			
header('location: ../index.php?section=Users&errormessage='.$MESSAGE_USER_ADDED_SUCCESSFULLY);
}
else {
header('location: ../index.php?section=Users&function=Add User&errormessage=Please enter a valid email address.');
}

?>