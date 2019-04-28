<?php

include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');
include ('../../_includes/_functions/img-generator.php');

// Variables passed
$ALBUM_ID = stripanyslashes($_REQUEST['albumid']);
$PHOTO_ID = stripanyslashes($_REQUEST['photoid']);
$PHOTO_NAME = stripanyslashes($_REQUEST['photo_name']);
$PHOTO_DESC = stripanyslashes($_REQUEST['photo_desc']);
$PHOTO_CAPTION = stripanyslashes($_REQUEST['photo_caption']);


checkEmptyVariable($ALBUM_ID);
checkEmptyVariable($PHOTO_ID);
checkEmptyVariable($PHOTO_NAME);


//If it gets this far all the fields are filled in properly.  Let's start the fun part


//Now Just Update information in the Database

$INSERT = "UPDATE `mod_photogallery_photos` SET
          `photo_name` = '".$PHOTO_NAME."',
          `photo_desc` = '".$PHOTO_DESC."',
          `photo_caption` = '".$PHOTO_CAPTION."'
          WHERE
          `album_id` = '".$ALBUM_ID."' AND `photo_id` = '".$PHOTO_ID."'";
          $INSERT_RESULT = mysql_query($INSERT) or die(mysql_error());

          //if this doesn't choke
  	header('Location: ../index.php?section=Modules&sectfunction=Photo Gallery&function=View Photo Album&albumid='.$ALBUM_ID.'&errormessage='.$MESSAGE_PHOTO_EDITED_SUCCESS);
        exit; 
		

?>


