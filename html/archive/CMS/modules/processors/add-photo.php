<?php

include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');
include ('../../_includes/_functions/img-generator.php');

$UPLOADIRECTORY = "../photo-gallery-images"; //This is where all the uploaded files and doc (non image) go
//Important: Make sure this folder's permission is 0777!

// Variables passed
$ALBUM_ID = stripanyslashes($_REQUEST['albumid']);
$PHOTO_NAME = stripanyslashes($_REQUEST['photo_name']);
$PHOTO_DESC = stripanyslashes($_REQUEST['photo_desc']);
$PHOTO_CAPTION = stripanyslashes($_REQUEST['photo_caption']);
$UPLOADED = $_FILES['photo_img'];
$THUMB_WIDTH = returnGalleryPhotoThumbDimension('thumb_width');
$THUMB_HEIGHT = returnGalleryPhotoThumbDimension('thumb_height');
$PHOTO_WIDTH =  returnGalleryPhotoDimension('photo_height');
$PHOTO_HEIGHT = returnGalleryPhotoDimension('photo_height');
$PHOTO_RESIZE =  returnGalleryInfo('photo_resize');
$THUMB_RESIZE =  returnGalleryInfo('thumb_resize');

checkEmptyVariable($ALBUM_ID);
checkEmptyVariable($PHOTO_NAME);
checkEmptyVariable($UPLOADED);

//If it gets this far all the fields are filled in properly.  Let's start the fun part


$RAND = rand(1000,9999999);  //Random number assigned to imagename to avoid duplication

//Now Let's upload the Document

$allowed_ext = "jpg, jpeg, gif, psd, tif, tiff, bmp, png";
// These are the allowed extensions of the files that are uploaded


$max_size = "5242880";
// 5 MB

// Check Entension
$extension = pathinfo($_FILES['photo_img']['name']); //The second part here always has to be name
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
		header('Location: ../index.php?section=Modules&sectfunction=Photogallery&function=Add Photo&albumid='.$ALBUM_ID.'&errormessage='.$UPLOAD_IMG_EXT_ERROR);
		exit;
}
elseif ($ok == "1")	
// Check File Size
{
	if($_FILES['uploadedimg']['size'] > $max_size)
	{
    		header('Location: ../index.php?section=Modules&sectfunction=Photogallery&function=Add Photo&albumid='.$ALBUM_ID.'&errormessage='.$UPLOAD_IMG_SIZE_ERROR);
        exit;
	}

// The Upload Part
if(is_uploaded_file($_FILES['photo_img']['tmp_name']))
	{
$file_ext_attach1 = strrchr($_FILES['photo_img']['name'], '.');   // Get The File Extention In The Format Of , For Instance, .jpg, .gif or .php
move_uploaded_file($_FILES['photo_img']['tmp_name'],$UPLOADIRECTORY.'/'.$PHOTO_NAME.'_'.$RAND.$file_ext_attach1);
$uploadsuccess = "yes";
	}
}

$PHOTO_THUMB = $UPLOADIRECTORY."/".$PHOTO_NAME."_".$RAND."_sm".$file_ext_attach1;
$PHOTO_FULL = $UPLOADIRECTORY."/".$PHOTO_NAME."_".$RAND.$file_ext_attach1;
//File Uploaded

if ($uploadsuccess == "yes") {
//Okay, now, if the file has already been uploaded once, let's resize it and overrride the original file with a 120 x 120
if ($THUMB_RESIZE == "YES") {
createthumb($PHOTO_FULL,$PHOTO_THUMB,$THUMB_WIDTH,$THUMB_HEIGHT);
}

if ($THUMB_RESIZE == "NO" || $THUMB_RESIZE == "") {
createthumb($PHOTO_FULL,$PHOTO_THUMB,120,120);
}

//If Photo Resize option is set, resize photo too
if ($PHOTO_RESIZE == "YES") {
createthumb($PHOTO_FULL,$PHOTO_FULL,$PHOTO_WIDTH,$PHOTO_HEIGHT);
}
//If photo resize is set to know, do nothing, file has already been moved to the right directory with the right name.

//Now Just Enter information in the Database

$INSERT = "INSERT INTO `mod_photogallery_photos`
          (`photo_id`,
          `album_id`,
          `photo_name`,
          `photo_desc`,
          `photo_caption`,
          `photo_img`,
          `photo_thumb`)
          VALUES
          ('',
          '".$ALBUM_ID."',
          '".$PHOTO_NAME."',
          '".$PHOTO_DESC."',
          '".$PHOTO_CAPTION."',
          '".$PHOTO_NAME."_".$RAND.$file_ext_attach1."',
           '".$PHOTO_NAME."_".$RAND."_sm".$file_ext_attach1."'
            )";
          $INSERT_RESULT = mysql_query($INSERT) or die(mysql_error());

          //if this doesn't choke
  	header('Location: ../index.php?section=Modules&sectfunction=Photo Gallery&errormessage='.$MESSAGE_PHOTO_UPLOADED_SUCCESS);
        exit; 
		
		}

?>


