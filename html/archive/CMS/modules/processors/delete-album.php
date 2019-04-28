<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

$UPLOADIRECTORY = "../photo-gallery-images";
// Variables passed
$ALBUM_ID = $_REQUEST['albumid'];

checkEmptyVariable($ALBUM_ID);
//If it gets this far a photo and album id has been submitted after confirmation


//First we must delete all the images and pictures below this album.
//Step 1, let's delete all the pictures that have been uploaded for this album.

$PHOTOS_FOR_THIS_ALBUM = mysql_query("SELECT *  FROM `mod_photogallery_photos` WHERE `album_id` = '".$ALBUM_ID."'") or die(mysql_error());
$NUM_PHOTOS = mysql_num_rows($PHOTOS_FOR_THIS_ALBUM);

for ($i=0; $i<$NUM_PHOTOS; $i++) {
$PHOTO_FIELD = mysql_fetch_array($PHOTOS_FOR_THIS_ALBUM);

if (unlink($UPLOADIRECTORY.'/'.$PHOTO_FIELD['photo_img']) && unlink($UPLOADIRECTORY.'/'.$PHOTO_FIELD['photo_thumb'])) { 
/*if the photo has been removed, then delete the database entry.  Note that we won't run this set in batches simply because we want consistency
between photos and deleted and the database records. If we ran the photo deletion and database update in batches, there can be inconsistencies*/

mysql_query("DELETE FROM  `mod_photogallery_photos` WHERE `album_id` = '".$ALBUM_ID."' AND `photo_id` = '".$PHOTO_FIELD['photo_id']."'") or die(mysql_error());

}
//Once we get to this part we have deleted all the photo files in the album as well as the relevant database entries in the photos table
echo 'All Photos Deleted. Now deleting album information.';
//Let's get rid of the photgraph for the album
}
$ALBUM_PHOTO = getAlbumInfoReturn($ALBUM_ID, 'album_img');

if (unlink($UPLOADIRECTORY.'/'.$ALBUM_PHOTO))  //Remove the Photo Album Image
//If the photo album image has been removed, let's delete the entry in the database

{

mysql_query("DELETE FROM `mod_photogallery_album` WHERE `album_id` = '".$ALBUM_ID."'") or die(mysql_error());

//redirect with success message			
header('location: ../index.php?section=Modules&sectfunction=Photo Gallery&errormessage='.$MESSAGE_ALBUM_DELETED_SUCCESS);
exit;
}

else {
header('location: ../index.php?section=Modules&sectfunction=Photo Gallery&errormessage='.$MESSAGE_ALBUM_DELETED_FAIL);
}

?>