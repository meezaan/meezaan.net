<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

$UPLOADIRECTORY = "../../uploadedfiles/pics";
// Variables passed
$IMAGE_ID = $_REQUEST['imageid'];

checkEmptyVariable($IMAGE_ID);
//If it gets this far a image id has been submitted after confirmation

$IMAGE = getImgInfoReturn($IMAGE_ID, 'image_loc');
$IMAGE_SM = getImgInfoReturn($IMAGE_ID, 'image_loc_sm');

//Let's get rid of the file first

if (unlink($UPLOADIRECTORY.'/'.$IMAGE) && unlink($UPLOADIRECTORY.'/'.$IMAGE_SM))  //if the file is deleted then remove it from the database 

{

mysql_query("DELETE FROM `images` WHERE `image_id` = '".$IMAGE_ID."'") or die(mysql_error());	

//redirect with success message			
header('location: ../index.php?section=Manage Images&errormessage='.$MESSAGE_IMG_DELETED_SUCCESSFULLY);

}

else {
header('location: ../index.php?section=Manage Documents&errormessage='.$MESSAGE_IMG_DELETED_FAIL);
}

?>