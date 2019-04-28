<div id="main_text">

<?php  //Main Site Management Section
if 
($SECTION == $MANAGE_IMAGES_TRAIL && $SECTFUNCTION == "" && $FUNCTION == "") {
include ('contentpages/pics.php');
}

elseif 
($SECTION == $MANAGE_IMAGES_TRAIL && $SECTFUNCTION == "" && $FUNCTION == $FUNCTION_ADD_IMAGE) {
include ('forms/add-a-pic.php');
}

elseif 
($SECTION == $MANAGE_IMAGES_TRAIL && $SECTFUNCTION == "" && $FUNCTION == $FUNCTION_DELETE_IMAGE) {
include ('forms/delete-a-pic.php');
}




else  { //Incorrect section, that is, not Website Management
$LOCATIONBASE = getSiteLoc();
header('location:' .$LOCATIONBASE. '/CMS/?errormessage='.$URL_ERROR_MESSAGE);
}

?>

</div>