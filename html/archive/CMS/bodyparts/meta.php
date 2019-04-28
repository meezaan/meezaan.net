<div id="main_text">

<?php  //Main Site Management Section
if 
($SECTION == $META_SEO_TRAIL && $SECTFUNCTION == "" && $FUNCTION == "") {
include ('forms/meta.php');
}

else  { //Incorrect section, that is, not Website Management
$LOCATIONBASE = getSiteLoc();
header('location:' .$LOCATIONBASE. '/CMS/?errormessage='.$URL_ERROR_MESSAGE);
}

?>

</div>