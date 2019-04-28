<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

$UPLOADIRECTORY = "../photo-gallery-images";
// Variables passed
$PHOTO_ID = $_REQUEST['photoid'];
$ALBUM_ID = $_REQUEST['albumid'];

checkEmptyVariable($PHOTO_ID);
checkEmptyVariable($ALBUM_ID);
//If it gets this far a photo and album id has been submitted after confirmation

$PHOTO = getPhotoInfoReturn($PHOTO_ID, 'photo_img');
$PHOTO_SM = getPhotoInfoReturn($PHOTO_ID, 'photo_thumb');

//Let's get rid of the file first

if (unlink($UPLOADIRECTORY.'/'.$PHOTO) && unlink($UPLOADIRECTORY.'/'.$PHOTO_SM))  //if the file is deleted then remove it from the database

{

mysql_query("DELETE FROM `mod_photogallery_photos` WHERE `photo_id` = '".$PHOTO_ID."' AND `album_id` = '".$ALBUM_ID."'") or die(mysql_error());

//redirect with success message			
header('location: ../index.php?section=Modules&sectfunction=Photo Gallery&function=View Photo Album&albumid='.$ALBUM_ID.'&errormessage='.$MESSAGE_PHOTO_DELETED_SUCCESS);

}

else {
header('location: ../index.php?section=Modules&sectfunction=Photo Gallery&function=View Photo Album&albumid='.$ALBUM_ID.'&errormessage='.$MESSAGE_PHOTO_DELETED_FAIL);
}

?>