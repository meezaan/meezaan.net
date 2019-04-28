<?php

include ('_connections/db_connector.php');
require ('_includes/check_signin.php');
require ('_includes/_functions/functions.php');


unset($_SESSION['logged_on']);
unset($_SESSION['user_admin']);
unset($_SESSION['user_id']);
unset($_SESSION['user_type']);
// kill session variables
$_SESSION = array(); // reset session array
session_destroy();   // destroy session.
header('Location: login.php?errormessage=You have logged out successfully.');
// redirect login page

?>