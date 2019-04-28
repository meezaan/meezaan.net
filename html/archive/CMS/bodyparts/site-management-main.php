<div id="main_text">

<?php  //Main Site Management Section
if 
($SECTION == $SITE_MANAGEMENT_TRAIL && $SECTFUNCTION == "" && $FUNCTION == "") {
include ('contentpages/site-management.php');
}

 //Main Pages Section
elseif ($SECTION == $SITE_MANAGEMENT_TRAIL && $SECTFUNCTION == $SECTFUNCT_PAGES && $FUNCTION == "") {
include ('contentpages/pages.php');
}

elseif  //Add a Page
($SECTION == $SITE_MANAGEMENT_TRAIL && $SECTFUNCTION == $SECTFUNCT_PAGES && $FUNCTION == $FUNCTION_ADD_PAGE) {
include ('forms/add-a-page.php');
}

elseif  //Edit a Page
($SECTION == $SITE_MANAGEMENT_TRAIL && $SECTFUNCTION == $SECTFUNCT_PAGES && $FUNCTION == $FUNCTION_EDIT_PAGE) {
include ('forms/edit-a-page.php');
}

elseif  //Delete a Page
($SECTION == $SITE_MANAGEMENT_TRAIL && $SECTFUNCTION == $SECTFUNCT_PAGES && $FUNCTION == $FUNCTION_DELETE_PAGE) {
include ('forms/delete-a-page.php');
}

elseif  //Static Areas Section
($SECTION == $SITE_MANAGEMENT_TRAIL && $SECTFUNCTION == $SECTFUNCT_AREAS && $FUNCTION == "") {
include ('contentpages/areas.php');
}

elseif  //Edit a Static Area
($SECTION == $SITE_MANAGEMENT_TRAIL && $SECTFUNCTION == $SECTFUNCT_AREAS && $FUNCTION == $FUNCTION_EDIT_AREA) {
include ('forms/edit-an-area.php');
}

elseif  //Sidebars Section
($SECTION == $SITE_MANAGEMENT_TRAIL && $SECTFUNCTION == $SECTFUNCT_SIDEBARS && $FUNCTION == "") {
include ('contentpages/sidebars.php');
}

elseif  //Add a Sidebar
($SECTION == $SITE_MANAGEMENT_TRAIL && $SECTFUNCTION == $SECTFUNCT_SIDEBARS && $FUNCTION == $FUNCTION_ADD_SIDEBAR) {
include ('forms/add-a-sidebar.php');
}

elseif  //Edit a Sidebar
($SECTION == $SITE_MANAGEMENT_TRAIL && $SECTFUNCTION == $SECTFUNCT_SIDEBARS && $FUNCTION == $FUNCTION_EDIT_SIDEBAR) {
include ('forms/edit-a-sidebar.php');
}

elseif  //Delete a Sidebar
($SECTION == $SITE_MANAGEMENT_TRAIL && $SECTFUNCTION == $SECTFUNCT_SIDEBARS && $FUNCTION == $FUNCTION_DELETE_SIDEBAR) {
include ('forms/delete-a-sidebar.php');
}

elseif  //Menus Section
($SECTION == $SITE_MANAGEMENT_TRAIL && $SECTFUNCTION == $SECTFUNCT_MENUS && $FUNCTION == "") {
include ('contentpages/menus.php');
}

elseif  //Edit a Menu
($SECTION == $SITE_MANAGEMENT_TRAIL && $SECTFUNCTION == $SECTFUNCT_MENUS && $FUNCTION == $FUNCTION_EDIT_MENU) {
include ('forms/edit-a-menu.php');
}

elseif  //Add Sub Menu
($SECTION == $SITE_MANAGEMENT_TRAIL && $SECTFUNCTION == $SECTFUNCT_MENUS && $FUNCTION == $FUNCTION_ADD_SUBMENU) {
include ('forms/add-sub-menu.php');
}

elseif  //Google Analytics Section
($SECTION == $SITE_MANAGEMENT_TRAIL && $SECTFUNCTION == $SECTFUNCT_ANALYTICS && $FUNCTION == "") {
include ('forms/google-analytics.php');
}

else  { //Incorrect section, that is, not Website Management
$LOCATIONBASE = getSiteLoc();
header('location:' .$LOCATIONBASE. '/CMS/?errormessage='.$URL_ERROR_MESSAGE);
}

?>

</div>