<?php

include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');
include ('../../_includes/_functions/img-generator.php');

$UPLOADIRECTORY = "../../uploadedfiles/pics"; //This is where all the uploaded files and doc (non image) go
//Important: Make sure this folder's permission is 0777!

// Variables passed
$IMGNAME = stripanyslashes($_REQUEST['imgname']);
$IMGDESC = stripanyslashes($_REQUEST['imgdescription']);
$WIDTH = stripanyslashes($_REQUEST['width']);
$UPLOADED = $_FILES['uploadedimg'];

checkEmptyVariable($IMGNAME);
checkEmptyVariable($IMGDESC);
checkEmptyVariable($UPLOADED);

//If it gets this far all the fields are filled in properly.  Let's start the fun part


$RAND = rand(100,99999);  //Random number assigned to imagename to avoid duplication

//Now Let's upload the Document

$allowed_ext = "jpg, jpeg, gif, psd, tif, tiff, bmp, png";
// These are the allowed extensions of the files that are uploaded


$max_size = "5242880";
// 5 MB

// Check Entension
$extension = pathinfo($_FILES['uploadedimg']['name']); //The second part here always has to be name
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
		header('Location: ../index.php?section=Manage Images&function=Add an Image&errormessage='.$UPLOAD_IMG_EXT_ERROR);
		exit;
}
elseif ($ok == "1")	
// Check File Size
{
	if($_FILES['uploadedimg']['size'] > $max_size)
	{
    		header('Location: ../index.php?section=Manage Images&function=Add an Image&errormessage='.$UPLOAD_IMG_SIZE_ERROR);
        exit;
	}

// The Upload Part
if(is_uploaded_file($_FILES['uploadedimg']['tmp_name']))
	{
$file_ext_attach1 = strrchr($_FILES['uploadedimg']['name'], '.');   // Get The File Extention In The Format Of , For Instance, .jpg, .gif or .php 
move_uploaded_file($_FILES['uploadedimg']['tmp_name'],$UPLOADIRECTORY.'/'.$IMGNAME.'_'.$RAND.$file_ext_attach1);
$uploadsuccess = "yes";
	}
}

$FULL = $UPLOADIRECTORY."/".$IMGNAME."_".$RAND.$file_ext_attach1;
$THUMB = $UPLOADIRECTORY."/".$IMGNAME."_".$RAND.'_sm'.$file_ext_attach1;
//File Uploaded

if ($uploadsuccess == "yes") {
//Okay, now, if the file has already been uploaded once, let's resize it and overrride the original file with a 600 x 600 or otherwise specified sizeimage to save space
if ($HEIGHT != "" && $WIDTH != "") {
//Function:  createthumb(originalfile, renamedfile, height, width)
createthumb($FULL,$FULL,$HEIGHT,$WIDTH);  //File created & overwritten per $HEIGHT AND $WIDTH
}
//Now let's create the thumbnail to display the imge
createthumb($FULL,$THUMB,100,100);



//Now Enter information in the Database

$INSERT = "INSERT INTO `images`
          (`image_name`, `image_description`, `image_loc`, `image_loc_sm`)
          VALUES
          ('".$IMGNAME."', '".$IMGDESC."', '".$IMGNAME."_".$RAND.$file_ext_attach1."', '".$IMGNAME."_".$RAND."_sm".$file_ext_attach1."' )";
          $INSERT_RESULT = mysql_query($INSERT) or die(mysql_error());

          //if this doesn't choke
  	header('Location: ../index.php?section=Manage Images&errormessage='.$UPLOAD_IMG_SUCCESS);
        exit; 
		
		}

?>


