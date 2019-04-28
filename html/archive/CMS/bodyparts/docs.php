<div id="main_text">

<?php  //Main Site Management Section
if 
($SECTION == $MANAGE_DOCS_TRAIL && $SECTFUNCTION == "" && $FUNCTION == "") {
include ('contentpages/docs.php');
}

elseif 
($SECTION == $MANAGE_DOCS_TRAIL && $SECTFUNCTION == "" && $FUNCTION == $FUNCTION_ADD_DOC) {
include ('forms/add-a-document.php');
}

elseif 
($SECTION == $MANAGE_DOCS_TRAIL && $SECTFUNCTION == "" && $FUNCTION == $FUNCTION_DELETE_DOC) {
include ('forms/delete-a-document.php');
}




else  { //Incorrect section, that is, not Website Management
$LOCATIONBASE = getSiteLoc();
header('location:' .$LOCATIONBASE. '/CMS/?errormessage='.$URL_ERROR_MESSAGE);
}

?>

</div>