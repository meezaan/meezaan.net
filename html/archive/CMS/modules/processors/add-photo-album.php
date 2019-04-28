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

$CREATION_TIME = getMysqlDatetime($GM_TIME); //GMT TIME ALWAYS WILL GO INTO DATABASE

$UPLOADIRECTORY = "../photo-gallery-images"; //This is where all the uploaded files and doc (non image) go
//Important: Make sure this folder's permission is 0777!

// Variables passed
$ALBUM_NAME = stripanyslashes($_REQUEST['album_name']);
$ALBUM_DESC = stripanyslashes($_REQUEST['album_desc']);
$ALBUM_CAPTION = stripanyslashes($_REQUEST['album_caption']);
$UPLOADED = $_FILES['album_img'];

checkEmptyVariable($ALBUM_NAME);
checkEmptyVariable($UPLOADED);

//If it gets this far all the fields are filled in properly.  Let's start the fun part


$RAND = rand(100,99999);  //Random number assigned to imagename to avoid duplication

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
		header('Location: ../index.php?section=Modules&sectfunction=Photogallery&function=Add Photo Album&errormessage='.$UPLOAD_IMG_EXT_ERROR);
		exit;
}
elseif ($ok == "1")	
// Check File Size
{
	if($_FILES['uploadedimg']['size'] > $max_size)
	{
    		header('Location: ../index.php?section=Modules&sectfunction=Photogallery&function=Add Photo Album&errormessage='.$UPLOAD_IMG_SIZE_ERROR);
        exit;
	}

// The Upload Part
if(is_uploaded_file($_FILES['album_img']['tmp_name']))
	{
$file_ext_attach1 = strrchr($_FILES['album_img']['name'], '.');   // Get The File Extention In The Format Of , For Instance, .jpg, .gif or .php
move_uploaded_file($_FILES['album_img']['tmp_name'],$UPLOADIRECTORY.'/'.$ALBUM_NAME.'_'.$RAND.$file_ext_attach1);
$uploadsuccess = "yes";
	}
}

$ALBUM_THUMB = $UPLOADIRECTORY."/".$ALBUM_NAME."_".$RAND.$file_ext_attach1;
//File Uploaded

if ($uploadsuccess == "yes") {
//Okay, now, if the file has already been uploaded once, let's resize it and overrride the original file with a 120 x 120
createthumb($ALBUM_THUMB,$ALBUM_THUMB,120,120);



//Now Enter information in the Database

$INSERT = "INSERT INTO `mod_photogallery_album`
          (`album_name`, 
          `album_desc`, 
          `album_caption`, 
          `album_img`, 
          `album_creator`, 
          `album_created_time`, 
          `album_last_editor`, 
          `album_last_edited`)
          VALUES
          ('".$ALBUM_NAME."', 
          '".$ALBUM_DESC."', 
          '".$ALBUM_CAPTION."',
          '".$ALBUM_NAME."_".$RAND.$file_ext_attach1."', 
          '".$_SESSION['user_id']."',
          '".$CREATION_TIME."',
          '".$_SESSION['user_id']."',
          '".$CREATION_TIME."'
            )";
          $INSERT_RESULT = mysql_query($INSERT) or die(mysql_error());

          //if this doesn't choke
  	header('Location: ../index.php?section=Modules&sectfunction=Photo Gallery&errormessage='.$MESSAGE_ALBUM_CREATED_SUCCESS);
        exit; 
		
		}

?>


