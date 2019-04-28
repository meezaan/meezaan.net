<div id="main_text">

<?php  //Current user sections
if 
($SECTION == $CURRENTUSER_TRAIL && $SECTFUNCTION == $SECTFUNCT_PWD && $FUNCTION == "") {
include ('forms/edit-password.php');
}

elseif 
($SECTION == $CURRENTUSER_TRAIL && $SECTFUNCTION == $SECTFUNCT_PROFILE && $FUNCTION == "") {
include ('forms/edit-profile.php');
}

else  { //Incorrect section, that is, not Website Management
$LOCATIONBASE = getSiteLoc();
header('location:' .$LOCATIONBASE. '/CMS/?errormessage='.$URL_ERROR_MESSAGE);
}

?>

</div>