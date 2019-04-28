<div id="main_text">

<?php  //Main Site Management Section
if 
($SECTION == $USERS_TRAIL && $SECTFUNCTION == "" && $FUNCTION == "") {
include ('contentpages/users.php');
}

elseif 
($SECTION == $USERS_TRAIL && $SECTFUNCTION == "" && $FUNCTION == $FUNCTION_EDIT_USER) {
include ('forms/edit-a-user.php');
}

elseif 
($SECTION == $USERS_TRAIL && $SECTFUNCTION == "" && $FUNCTION == $FUNCTION_ADD_USER) {
include ('forms/add-a-user.php');
}

elseif 
($SECTION == $USERS_TRAIL && $SECTFUNCTION == "" && $FUNCTION == $FUNCTION_DELETE_USER) {
include ('forms/delete-a-user.php');
}


else  { //Incorrect section, that is, not Website Management
$LOCATIONBASE = getSiteLoc();
header('location:' .$LOCATIONBASE. '/CMS/?errormessage='.$URL_ERROR_MESSAGE);
}

?>

</div>