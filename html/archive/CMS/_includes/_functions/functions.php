<?php
/*
This code is copyright (c) VAFTA Solutions Limited, all rights reserved. The contents of this file are protected under law as the intellectual property of VAFTA Solutions Limited. Any use, reproduction, disclosure or copying of any kind without the express and written permission of VAFTA Solutions Limited is forbidden.
Author:  Asif Nawaz, asif@vafta.com
*/
// All PHP functions reside in this file

//This function gets the name of the site
function getSiteName() {
$SQL_sitename = mysql_query("SELECT `site_name` FROM `site_info`") or die(mysql_error());
$RESULT_sitename = mysql_fetch_row($SQL_sitename);
echo $RESULT_sitename['0'];
}

function getSiteNameReturn() {
$SQL_sitename = mysql_query("SELECT `site_name` FROM `site_info`") or die(mysql_error());
$RESULT_sitename = mysql_fetch_row($SQL_sitename);
return $RESULT_sitename['0'];
}

function getUserInfo($user_id, $field) {
$SQL_username = mysql_query("SELECT `".$field."` FROM `users` WHERE user_id = '".$user_id."'") or die(mysql_error());
$RESULT_username = mysql_fetch_row($SQL_username);
echo $RESULT_username['0'];
}

function getUserInfoReturn($user_id, $field) {
$SQL_username = mysql_query("SELECT `".$field."` FROM `users` WHERE user_id = '".$user_id."'") or die(mysql_error());
$RESULT_username = mysql_fetch_row($SQL_username);
return $RESULT_username['0'];
}

//This function gets the URL for the site stored in the database
function getSiteLoc() {
$SQL_siteloc = mysql_query("SELECT `site_url` FROM `site_info`") or die(mysql_error());
$RESULT_siteloc = mysql_fetch_row($SQL_siteloc);
echo $RESULT_siteloc['0'];
}

function getSiteLocReturn() {
$SQL_siteloc = mysql_query("SELECT `site_url` FROM `site_info`") or die(mysql_error());
$RESULT_siteloc = mysql_fetch_row($SQL_siteloc);
return $RESULT_siteloc['0'];
}

//Section verification functions.  The text checked comes from the site section variables passed in variables.php
function SectionVerify($SECTIONPASSED) { 
if (($SECTIONPASSED == "Website Management") || ($SECTIONPASSED == "Meta and SEO") || ($SECTIONPASSED == "Manage Documents") || ($SECTIONPASSED == "Manage Images") || ($SECTIONPASSED == "Users") || ($SECTIONPASSED == "Licensing and Site Info") || ($SECTIONPASSED == "Current User") || ($SECTIONPASSED == "Modules"))
{
//Do nothing and proceed
}
else {
header ('location: ../');
}
}

function getKBRef($phpMyFAQLink) { //VAFTA CMS Knowlede Base Link generated on Question / FAQ Number
echo "<a href=\"".$phpMyFAQLink."\" title=\"See this item in our knowledge base\" target=\"blank\" ><img src=\"../icons/small/icon_info.gif\" alt=\"See this item in our knowledge base\" border=\"0\"></a>";
}

function getSidebarChooser() {  // Choose a sidebar to associate with a page on the add a page page
$SQL_sidebarchooser = mysql_query("SELECT * FROM `sidebars`") or die(mysql_error());
while($RESULT_sidebarchooser = mysql_fetch_array($SQL_sidebarchooser)) 
{
$SIDEBAR_OPTION .= '<option value="'.$RESULT_sidebarchooser['sidebar_id'].'">'.$RESULT_sidebarchooser['sidebar_name'].'</option>';
}
echo '<select name="sidebar_id">
		<option value="0">No Sidebar</option>'
		.$SIDEBAR_OPTION.
		'</select>';		
}

function getSidebarChooserEdit($page) {
$SQL = mysql_query("SELECT `sidebar_id` from `page` WHERE page_id = '".$page."'");
$RESULT = mysql_fetch_row($SQL);
$SIDEBARNAME = mysql_fetch_row(mysql_query("SELECT sidebar_name FROM sidebars WHERE sidebar_id = '".$RESULT['0']."'"));
  // Choose a sidebar to associate with a page on the add a page page
$SQL_sidebarchooser = mysql_query("SELECT * FROM `sidebars` WHERE `sidebar_id` != '".$RESULT['0']."'") or die(mysql_error());
while($RESULT_sidebarchooser = mysql_fetch_array($SQL_sidebarchooser)) 
{
$SIDEBAR_OPTION .= '<option value="'.$RESULT_sidebarchooser['sidebar_id'].'">'.$RESULT_sidebarchooser['sidebar_name'].'</option>';
}
echo '<select name="sidebar_id">';
			if ($RESULT['0'] != "0") {
		echo '<option value="'.$RESULT['0'].'">'.$SIDEBARNAME['0'].'</option>';
						}
		echo '<option value="0">No Sidebar</option>'
		.$SIDEBAR_OPTION.
		'</select>';		
}



function checkEmptyVariable($VARIABLE) {
if($VARIABLE == "") {
$REFERER = $_SERVER['HTTP_REFERER'];
header('location: '.$REFERER.'&errormessage=Please fill in all the required fields.');
exit;
}
}


function getPageTitle($page_id) {
$RESULT_pageinfo = mysql_query("SELECT `page_title` FROM `page` WHERE `page_id` = '".$page_id."'") or die(mysql_error());
$FIELD_SELECTED = mysql_fetch_row($RESULT_pageinfo);
echo $FIELD_SELECTED['0'];
}

function getPageHeader($page_id) {
$RESULT_pageinfo = mysql_query("SELECT `page_header` FROM `page` WHERE `page_id` = '".$page_id."'") or die(mysql_error());
$FIELD_SELECTED = mysql_fetch_row($RESULT_pageinfo);
echo $FIELD_SELECTED['0'];
}

function getPageContent($page_id) {
$RESULT_pageinfo = mysql_query("SELECT `page_content` FROM `page` WHERE `page_id` = '".$page_id."'") or die(mysql_error());
$FIELD_SELECTED = mysql_fetch_row($RESULT_pageinfo);
$FIELD = $FIELD_SELECTED['0'];
return $FIELD;
}

function getPageCreator($page_id) {
$RESULT_pagecreator = mysql_query("SELECT `page_creator` FROM `page` WHERE `page_id` = '".$page_id."'") or die(mysql_error());
$PAGE_CREATOR = mysql_fetch_row($RESULT_pagecreator);
$RESULT_pagecreatorname = mysql_query("SELECT `user_name` FROM `users` WHERE `user_id` = '".$PAGE_CREATOR['0']."'") or die(mysql_error());
$PAGE_CREATORNAME = mysql_fetch_row($RESULT_pagecreatorname);
echo $PAGE_CREATORNAME['0'];
}

function getSidebarCreator($sidebar_id) {
$RESULT_sidebarcreator = mysql_query("SELECT `sidebar_creator` FROM `sidebars` WHERE `sidebar_id` = '".$sidebar_id."'") or die(mysql_error());
$SIDEBAR_CREATOR = mysql_fetch_row($RESULT_sidebarcreator);
$RESULT_sidebarcreatorname = mysql_query("SELECT `user_name` FROM `users` WHERE `user_id` = '".$SIDEBAR_CREATOR['0']."'") or die(mysql_error());
$SIDEBAR_CREATORNAME = mysql_fetch_row($RESULT_sidebarcreatorname);
echo $SIDEBAR_CREATORNAME['0'];
}

function getPageEditor($page_id) {
$RESULT_pageeditor = mysql_query("SELECT `page_last_editor` FROM `page` WHERE `page_id` = '".$page_id."'") or die(mysql_error());
$PAGE_EDITOR = mysql_fetch_row($RESULT_pageeditor);
$RESULT_pageeditorname = mysql_query("SELECT `user_name` FROM `users` WHERE `user_id` = '".$PAGE_EDITOR['0']."'") or die(mysql_error());
$PAGE_EDITORNAME = mysql_fetch_row($RESULT_pageeditorname);
echo $PAGE_EDITORNAME['0'];
}

function getSidebarEditor($sidebar_id) {
$RESULT_sidebareditor = mysql_query("SELECT `sidebar_last_editor` FROM `sidebars` WHERE `sidebar_id` = '".$sidebar_id."'") or die(mysql_error());
$SIDEBAR_EDITOR = mysql_fetch_row($RESULT_sidebareditor);
$RESULT_sidebareditorname = mysql_query("SELECT `user_name` FROM `users` WHERE `user_id` = '".$SIDEBAR_EDITOR['0']."'") or die(mysql_error());
$SIDEBAR_EDITORNAME = mysql_fetch_row($RESULT_sidebareditorname);
echo $SIDEBAR_EDITORNAME['0'];
}

function getPageCreatedTime($page_id) {
$RESULT_pagecreatedtime = mysql_query("SELECT `page_created_time` FROM `page` WHERE `page_id` = '".$page_id."'") or die(mysql_error());
$PAGE_CREATED_TIME = mysql_fetch_row($RESULT_pagecreatedtime);
$DATE_TIME = strtotime($PAGE_CREATED_TIME['0']);
echo date("l\, jS \of F\, Y \@ h:i:s A", $DATE_TIME);
}

function getUserLoginTime($user_id) {
$RESULT = mysql_query("SELECT `user_last_login_time` FROM `users` WHERE `user_id` = '".$user_id."'") or die(mysql_error());
$USER_TIME = mysql_fetch_row($RESULT);
$DATE_TIME = strtotime($USER_TIME['0']);
echo date("jS F\, Y", $DATE_TIME);
}

function getSidebarCreatedTime($sidebar_id) {
$RESULT_sidebarcreatedtime = mysql_query("SELECT `sidebar_created_time` FROM `sidebars` WHERE `sidebar_id` = '".$sidebar_id."'") or die(mysql_error());
$SIDEBAR_CREATED_TIME = mysql_fetch_row($RESULT_sidebarcreatedtime);
$DATE_TIME = strtotime($SIDEBAR_CREATED_TIME['0']);
echo date("l\, jS \of F\, Y \@ h:i:s A", $DATE_TIME);
}

function getPageEditedTime($page_id) {
$RESULT_pageeditedtime = mysql_query("SELECT `page_last_edited_time` FROM `page` WHERE `page_id` = '".$page_id."'") or die(mysql_error());
$PAGE_EDITED_TIME = mysql_fetch_row($RESULT_pageeditedtime);
$DATE_TIME = strtotime($PAGE_EDITED_TIME['0']);
echo date("l\, jS \of F\, Y \@ h:i:s A", $DATE_TIME);
}

function getSidebarEditedTime($sidebar_id) {
$RESULT_sidebareditedtime = mysql_query("SELECT `sidebar_last_edit_time` FROM `sidebars` WHERE `sidebar_id` = '".$sidebar_id."'") or die(mysql_error());
$SIDEBAR_EDITED_TIME = mysql_fetch_row($RESULT_sidebareditedtime);
$DATE_TIME = strtotime($SIDEBAR_EDITED_TIME['0']);
echo date("l\, jS \of F\, Y \@ h:i:s A", $DATE_TIME);
}

function getPageEditedTimeSummary($page_id) {
$RESULT_pageeditedtime = mysql_query("SELECT `page_last_edited_time` FROM `page` WHERE `page_id` = '".$page_id."'") or die(mysql_error());
$PAGE_EDITED_TIME = mysql_fetch_row($RESULT_pageeditedtime);
$DATE_TIME = strtotime($PAGE_EDITED_TIME['0']);
echo date("jS F\, Y", $DATE_TIME);
}


function getAreaName($area_id) {
$RESULT_areainfo = mysql_query("SELECT `area_name` FROM `static_areas` WHERE `area_id` = '".$area_id."'") or die(mysql_error());
$FIELD_SELECTED = mysql_fetch_row($RESULT_areainfo);
$FIELD = $FIELD_SELECTED['0'];
echo $FIELD;
}

function getAreaDesc($area_id) {
$RESULT_areainfo = mysql_query("SELECT `area_description` FROM `static_areas` WHERE `area_id` = '".$area_id."'") or die(mysql_error());
$FIELD_SELECTED = mysql_fetch_row($RESULT_areainfo);
$FIELD = $FIELD_SELECTED['0'];
echo $FIELD;
}

function getAreaContent($area_id) {
$RESULT_areainfo = mysql_query("SELECT `area_content` FROM `static_areas` WHERE `area_id` = '".$area_id."'") or die(mysql_error());
$FIELD_SELECTED = mysql_fetch_row($RESULT_areainfo);
$FIELD = $FIELD_SELECTED['0'];
return $FIELD;
}

function getSidebarName($sidebar_id) {
$RESULT_sidebarinfo = mysql_query("SELECT `sidebar_name` FROM `sidebars` WHERE `sidebar_id` = '".$sidebar_id."'") or die(mysql_error());
$FIELD_SELECTED = mysql_fetch_row($RESULT_sidebarinfo);
$FIELD = $FIELD_SELECTED['0'];
echo $FIELD;
}

function getSidebarHeader($sidebar_id) {
$RESULT_sidebarinfo = mysql_query("SELECT `sidebar_header` FROM `sidebars` WHERE `sidebar_id` = '".$sidebar_id."'") or die(mysql_error());
$FIELD_SELECTED = mysql_fetch_row($RESULT_sidebarinfo);
$FIELD = $FIELD_SELECTED['0'];
echo $FIELD;
}

function getSidebarContent($sidebar_id) {
$RESULT_sidebarinfo = mysql_query("SELECT `sidebar_content` FROM `sidebars` WHERE `sidebar_id` = '".$sidebar_id."'") or die(mysql_error());
$FIELD_SELECTED = mysql_fetch_row($RESULT_sidebarinfo);
$FIELD = $FIELD_SELECTED['0'];
return $FIELD;
}

function getSidebarEditedTimeSummary($sidebar_id) {
$RESULT_sidebarinfo = mysql_query("SELECT `sidebar_last_edit_time` FROM `sidebars` WHERE `sidebar_id` = '".$sidebar_id."'") or die(mysql_error());
$SIDEBAR_EDITED_TIME = mysql_fetch_row($RESULT_sidebarinfo);
$DATE_TIME = strtotime($SIDEBAR_EDITED_TIME['0']);
echo date("jS F\, Y", $DATE_TIME);
}

function getMenuPages($menu_id) {
$SQL_menupages = mysql_query("SELECT `page_id` FROM `menu` WHERE `menu_id` = '".$menu_id."'") or die(mysql_error());
$NUM_pages = mysql_num_rows($SQL_menupages);
for ($i=0;$i<$NUM_pages; $i++) {
$MENUPAGE = mysql_fetch_array($SQL_menupages);
$SQL_pagename = mysql_query("SELECT `page_title` FROM `page` WHERE `page_id` = '".$MENUPAGE['page_id']."'") or die(mysql_error());
$PAGENAME = mysql_fetch_array($SQL_pagename);
echo $PAGENAME['page_title'];
echo '<br />';
}
}

function CheckDuplicateMenus($menu,$page,$pagelevel,$pageparent) {
$SQL_menu = mysql_query("SELECT * FROM `menu` WHERE page_id = '".$page."' AND menu_id = '".$menu."' AND page_level = '".$pagelevel."' AND page_parent = '".$pageparent."'" );
$MENU_CHECK = mysql_num_rows($SQL_menu);
if ($MENU_CHECK >= 1) {
header('Location: ../index.php?section=Website Management&sectfunction=Menus&function=Edit a Menu&menuid='.$menu.'&errormessage=The page you chose already exists in this menu.
');
}
}

function getMeta($parameter) {
$SQL_meta = mysql_query("SELECT ".$parameter." FROM meta") or die(mysql_error());
$RESULT = mysql_fetch_row($SQL_meta);
echo $RESULT['0'];
}

function getSiteInfo($parameter) {
$SQL_license = mysql_query("SELECT ".$parameter." FROM site_info") or die(mysql_error());
$RESULT = mysql_fetch_row($SQL_license);
echo $RESULT['0'];
}

function checkCurrentPassword($password,$userid) {
$SQL = mysql_query("SELECT `user_password` FROM `users` WHERE `user_id` = '".$userid."'") or die(mysql_error()); 
$RESULT = mysql_fetch_row($SQL);
$PWD = $RESULT['0'];
if ($PWD != $password) {
header('location: ../index.php?section=Current User&sectfunction=Change Password&errormessage=The current password you entered is incorrect.  Please try again.');
} 
}

function checkPasswordMatch($a,$b) {
if ($a != $b) {
$REFERER = $_SERVER['HTTP_REFERER'];
header('location: '.$REFERER.'&errormessage=The new password and confirm new password fields do not match.  Please try again.');
exit;
}
}


function checkEmailFormat($email) {
    // First, we check that there's one @ symbol, and that the lengths are right
    if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
    // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
    return false;
    }
    // Split it into sections to make life easier
    $email_array = explode("@", $email);
    $local_array = explode(".", $email_array[0]);
   for ($i = 0; $i < sizeof($local_array); $i++) {
   if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
   return false;
   }
   }
   if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
   $domain_array = explode(".", $email_array[1]);
   if (sizeof($domain_array) < 2) {
   return false; // Not enough parts to domain
   }
   for ($i = 0; $i < sizeof($domain_array); $i++) {
   if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
   return false;
   }
   }
   }
   return true;
   }


function getUserTypeChooser() {
$SQL = mysql_query("SELECT * FROM `user_types`");
while($RESULT = mysql_fetch_array($SQL)) 
{
$USERTYPE_OPTION .= '<option value="'.$RESULT['user_type_id'].'">'.$RESULT['user_type'].'</option>';
}
echo '<select name="user_type_id">'
		.$USERTYPE_OPTION.
		'</select>';
}

function getUserTypeChooserEditor($userid) {
$ID = mysql_fetch_row(mysql_query("SELECT `user_type_id` from users WHERE `user_id`= '".$userid."'")) or die(mysql_error());
$IDNAME = mysql_fetch_row(mysql_query("SELECT `user_type` from user_types WHERE `user_type_id`= '".$ID['0']."'")) or die(mysql_error());

$SQL = mysql_query("SELECT * FROM `user_types` WHERE `user_type_id` != '".$ID['0']."'");
while($RESULT = mysql_fetch_array($SQL)) 
{
$USERTYPE_OPTION .= '<option value="'.$RESULT['user_type_id'].'">'.$RESULT['user_type'].'</option>';
}
echo '<select name="user_type_id">
		<option value = "'.$ID['0'].'">'.$IDNAME['0'].'</option>'
		.$USERTYPE_OPTION.
		'</select>';
}

function checkDuplicateUser($username) {
$SQL = mysql_query("SELECT * FROM `users` WHERE `user_username` = '".$username."'");
$NUM = mysql_num_rows($SQL);
if ($NUM >= 1) {
$REFERER = $_SERVER['HTTP_REFERER'];
header('location: '.$REFERER.'&errormessage=This username is already taken.  Please try again.');
exit;
}
}

function checkDuplicateUserEdit($username,$userid) {
$SQL = mysql_query("SELECT * FROM `users` WHERE `user_id` != '".$userid."' AND `user_username` = '".$username."'");
$NUM = mysql_num_rows($SQL);
if ($NUM >= 1) {
header('location: ../index.php?section=Users&function=Edit User&userid='.$userid.'&errormessage=This username is already taken.  Please try again.');
exit;
}
}

function checkIfAdmin($usertype) {
if ($usertype != 2) {
header('location: ../index.php?errormessage=Easy tiger.  Only an Administrator can access the User Management Area.  You are currently logged in as a Moderator.');
}
}

function getDocInfo($docid, $field) {
$RESULT = mysql_fetch_row(mysql_query("SELECT `".$field."` FROM `documents` WHERE `doc_id` = '".$docid."'"));
if ($field == "doc_size") {
echo number_format($RESULT['0']/1000, 2, '.', '');
}
else {
echo $RESULT['0'];
}
}

function getDocInfoReturn($docid, $field) {
$RESULT = mysql_fetch_row(mysql_query("SELECT `".$field."` FROM `documents` WHERE `doc_id` = '".$docid."'"));
if ($field == "doc_size") {
return number_format($RESULT['0']/1000, 2, '.', '');
}
else {
return $RESULT['0'];
}
}

function getImgInfoReturn($imgid, $field) {
$RESULT = mysql_fetch_row(mysql_query("SELECT `".$field."` FROM `images` WHERE `image_id` = '".$imgid."'"));
return $RESULT['0'];
}

function getImgInfo($imgid, $field) {
$RESULT = mysql_fetch_row(mysql_query("SELECT `".$field."` FROM `images` WHERE `image_id` = '".$imgid."'"));
echo $RESULT['0'];
}

function getModInfo($modid, $field) {
$RESULT = mysql_fetch_row(mysql_query("SELECT `".$field."` FROM `modules` WHERE `mod_id` = '".$modid."'"));
echo $RESULT['0'];
}

//Module Status
function CheckModEnabled($mod_id) {
//News = 1
$RESULT = mysql_fetch_row(mysql_query("Select `mod_status_id` FROM `modules` WHERE `mod_id` = '".$mod_id."'"));
if ($RESULT['0'] == 1) {

}
else {
header('location: index.php?section=Modules&errormessage=The module you are trying to use is disabled.  Please enable it first.');
}
}

//News Module
function getNewsInfo($newsid, $field) {
$RESULT = mysql_fetch_row(mysql_query("SELECT `".$field."` FROM `mod_news` WHERE `news_id` = '".$newsid."'"));
echo $RESULT['0'];
}

function getNewsInfoReturn($newsid, $field) {
$RESULT = mysql_fetch_row(mysql_query("SELECT `".$field."` FROM `mod_news` WHERE `news_id` = '".$newsid."'"));
return $RESULT['0'];
}
//News Module End


//Gallery Module
function getGalleryInfo($field) {
$RESULT = mysql_fetch_row(mysql_query("SELECT `".$field."` FROM `mod_photogallery_options`"));
echo $RESULT['0'];
}

function returnGalleryPhotoDimension($field) {
$RESULT = mysql_fetch_row(mysql_query("SELECT `".$field."` FROM `mod_photogallery_options`"));
if ($RESULT['0'] < 1 || $RESULT['0'] == "" || $RESULT['0'] == 0) {
 return 800;
} 
 else {
   return $RESULT['0'];
}
}

function returnGalleryPhotoThumbDimension($field) {
$RESULT = mysql_fetch_row(mysql_query("SELECT `".$field."` FROM `mod_photogallery_options`"));
if ($RESULT['0'] < 1 || $RESULT['0'] == "" || $RESULT['0'] == 0) {
 return 120;
}
 else {
   return $RESULT['0'];
}
}

function returnGalleryInfo($field) {
$RESULT = mysql_fetch_row(mysql_query("SELECT `".$field."` FROM `mod_photogallery_options`"));
return $RESULT['0'];
}

function getGalleryPhotoSizeSelector() {
$RESULT = mysql_fetch_row(mysql_query("SELECT `photo_resize` FROM `mod_photogallery_options`"));
if ($RESULT['0'] == "YES") {
		echo '	<select name="resize-photos">
				<option value="YES">Yes</option>
				<option value="NO">No</option>
			</select>';
	
} 
elseif ($RESULT['0'] == "NO") {
		echo '	<select name="resize-photos">
				<option value="NO">No</option>
				<option value="YES">Yes</option>
			</select>';
}
}

function getGalleryThumbSizeSelector() {
$RESULT = mysql_fetch_row(mysql_query("SELECT `thumb_resize` FROM `mod_photogallery_options`"));
if ($RESULT['0'] == "YES") {
		echo '	<select name="resize-thmb">
				<option value="YES">Yes</option>
				<option value="NO">No</option>
			</select>';
	
} 
elseif ($RESULT['0'] == "NO") {
		echo '	<select name="resize-thumb">
				<option value="NO">No</option>
				<option value="YES">Yes</option>
			</select>';
}
}

function getAlbumName($albumid) {
$RESULT = mysql_fetch_row(mysql_query("SELECT `album_name` FROM `mod_photogallery_album` WHERE `album_id` ='".$albumid."'"));
echo $RESULT['0'];
}

function getAlbumInfo($albumid, $field) {
$RESULT = mysql_fetch_row(mysql_query("SELECT `".$field."` FROM `mod_photogallery_album` WHERE `album_id` ='".$albumid."'"));
echo $RESULT['0'];
}

function getAlbumInfoReturn($albumid, $field) {
$RESULT = mysql_fetch_row(mysql_query("SELECT `".$field."` FROM `mod_photogallery_album` WHERE `album_id` ='".$albumid."'"));
return $RESULT['0'];
}

function getPhotoInfo($photoid,$field) {
$RESULT = mysql_fetch_row(mysql_query("SELECT `".$field."` FROM `mod_photogallery_photos` WHERE `photo_id` ='".$photoid."'"));
echo $RESULT['0'];
}

function getPhotoInfoReturn($photoid,$field) {
$RESULT = mysql_fetch_row(mysql_query("SELECT `".$field."` FROM `mod_photogallery_photos` WHERE `photo_id` ='".$photoid."'"));
return $RESULT['0'];
}
?>