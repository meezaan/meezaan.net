<?php

include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');
include ('../../_includes/_functions/img-generator.php');

//GET MESSAGE  CREATION TIME
$TIME = time();
$GM_TIME = $TIME - date('Z', $TIME);

function getMysqlDate($TIME){return date("Y-m-d", $TIME);}
function getMysqlDatetime($TIME){return date("Y-m-d H:i:s", $TIME);}
function getTimestamp($MYSQL_DATETIME){return strtotime($MYSQL_DATETIME);}

$EDIT_TIME = getMysqlDatetime($GM_TIME); //GMT TIME ALWAYS WILL GO INTO DATABASE

$UPLOADIRECTORY = "../photo-gallery-images"; //This is where all the uploaded files and doc (non image) go
//Important: Make sure this folder's permission is 0777!
// Variables passed
$ALBUM_ID = stripanyslashes($_REQUEST['albumid']);
$ALBUM_NAME = stripanyslashes($_REQUEST['album_name']);
$ALBUM_DESC = stripanyslashes($_REQUEST['album_desc']);
$ALBUM_CAPTION = stripanyslashes($_REQUEST['album_caption']);
$UPLOADED = $_FILES['album_img'];

checkEmptyVariable($ALBUM_NAME);
checkEmptyVariable($ALBUM_ID);

//If it gets this far all the fields are filled in properly.  Let's start the fun part

//Let's deal with the picture first. If a replace photo has in-fact been uploaded, we need to replace what we have with the new one.

if (!empty($UPLOADED)) {

//First let's get the name of the image
$PHOTO_NAME = getAlbumInfoReturn($ALBUM_ID, 'album_img');

//Now Let's upload the Document

$allowed_ext = "jpg, jpeg, gif, psd, tif, tiff, bmp, png";
// These are the allowed extensions of the files that are uploaded


$max_size = "5242880";
// 5 MB

// Check Entension
$extension = pathinfo($_FILES['album_img']['name']); //The second part here always has to be name
$extension = $extension['extension'];
$allowed_paths = explode(", ", $allowed_ext);
for($i=0; $i<count($allowed_paths); $i++) 
{
	if ($allowed_paths[$i] == "$extension") 
	{
		$ok = "1";
	}
}	
	if ($ok != "1") 
{
		header('Location: ../index.php?section=Modules&sectfunction=Photogallery&errormessage='.$UPLOAD_IMG_EXT_ERROR);
		exit;
}
elseif ($ok == "1")	
// Check File Size
{
	if($_FILES['uploadedimg']['size'] > $max_size)
	{
    		header('Location: ../index.php?section=Modules&sectfunction=Photogallery&errormessage='.$UPLOAD_IMG_SIZE_ERROR);
        exit;
	}

// The Upload Part
if(is_uploaded_file($_FILES['album_img']['tmp_name']))
	{
$file_ext_attach1 = strrchr($_FILES['album_img']['name'], '.');   // Get The File Extention In The Format Of , For Instance, .jpg, .gif or .php
move_uploaded_file($_FILES['album_img']['tmp_name'],$UPLOADIRECTORY.'/'.$PHOTO_NAME);
$uploadsuccess = "yes";
	}
}   //The new photo would have been replaced by the uploaded photo at full size

$ALBUM_THUMB = $UPLOADIRECTORY."/".$PHOTO_NAME;
//File Uploaded

if ($uploadsuccess == "yes") {
//Okay, now, if the file has already been uploaded once, let's resize it and overrride the original file with a 120 x 120
createthumb($ALBUM_THUMB,$ALBUM_THUMB,120,120); //resize the new photo

//Database does not change for photo we have used the same photo name as before.
}
}

//Enter all non-photo information in the Database

$INSERT = "UPDATE `mod_photogallery_album` SET
          `album_name` = '".$ALBUM_NAME."',
          `album_desc` = '".$ALBUM_DESC."',
          `album_caption` = '".$ALBUM_CAPTION."',
          `album_last_editor` = '".$_SESSION['user_id']."',
          `album_last_edited` = '".$EDIT_TIME."'
          WHERE album_id = '".$ALBUM_ID."'";
          $INSERT_RESULT = mysql_query($INSERT) or die(mysql_error());

          //if this doesn't choke
  	header('Location: ../index.php?section=Modules&sectfunction=Photo Gallery&errormessage='.$MESSAGE_ALBUM_UPDATED_SUCCESS);
        exit; 
		
?>

