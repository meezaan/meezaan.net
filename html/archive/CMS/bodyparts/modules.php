<div id="main_text">

<?php  //Main Modules Section
if 
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == "" && $FUNCTION == "") {
include ('contentpages/modules.php');
}
//news Module
elseif 
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_NEWS && $FUNCTION == "") {
include ('contentpages/news.php');
}

elseif 
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_NEWS && $FUNCTION == $FUNCTION_EDIT_NEWS) {
include ('forms/edit-news.php');
}

elseif 
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_NEWS && $FUNCTION == $FUNCTION_DELETE_NEWS) {
include ('forms/delete-news.php');
}

elseif 
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_NEWS && $FUNCTION == $FUNCTION_ADD_NEWS) {
include ('forms/add-news.php');
}

//Photo gallery Module

elseif 
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_PHOTOGALLERY && $FUNCTION == "") {
include ('contentpages/photo-gallery.php');
}


elseif 
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_PHOTOGALLERY && $FUNCTION == $FUNCTION_ADD_PHOTOALBUM) {
include ('forms/add-photo-album.php');
}

elseif
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_PHOTOGALLERY && $FUNCTION == $FUNCTION_VIEW_PHOTOALBUM) {
include ('contentpages/photo-album.php');
}

elseif
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_PHOTOGALLERY && $FUNCTION == $FUNCTION_EDIT_PHOTOALBUM) {
include ('forms/edit-album.php');
}


elseif 
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_PHOTOGALLERY && $FUNCTION == $FUNCTION_ADD_PHOTO) {
include ('forms/add-photo.php');
}

elseif
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_PHOTOGALLERY && $FUNCTION == $FUNCTION_DELETE_PHOTO) {
include ('forms/delete-photo.php');
}

elseif
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_PHOTOGALLERY && $FUNCTION == $FUNCTION_DELETE_PHOTOALBUM) {
include ('forms/delete-album.php');
}

elseif
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_PHOTOGALLERY && $FUNCTION == $FUNCTION_EDIT_PHOTO) {
include ('forms/edit-photo.php');
}

elseif 
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_PHOTOGALLERY && $FUNCTION == $FUNCTION_CONFIGURE_PHOTOGALLERY) {
include ('forms/configure-photo-gallery.php');
}

//CRM Module
elseif
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_MAILINGLIST && $FUNCTION == "") {
include ('contentpages/mailing-list.php');
}

//Newsletter Module
elseif
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_NEWSLETTER && $FUNCTION == "") {
include ('contentpages/newsletter.php');
}

//Blog Module  / RSS Feed
elseif
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_BLOG && $FUNCTION == "") {
include ('contentpages/blog.php');
}

//eCommerce Module
elseif
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_ECOMMERCE && $FUNCTION == "") {
include ('contentpages/store.php');
}

//Portfolio Module
elseif
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_PORTFOLIO && $FUNCTION == "") {
include ('contentpages/portfolio.php');
}

//Forum Module
elseif
($SECTION == $MODULES_TRAIL && $SECTFUNCTION == $SECTFUNCT_MODULE_FORUM && $FUNCTION == "") {
include ('contentpages/forum.php');
}

else  { //Incorrect section, that is, not Website Management
$LOCATIONBASE = getSiteLoc();
header('location:' .$LOCATIONBASE. '/CMS/?errormessage='.$URL_ERROR_MESSAGE);
}

?>

</div>