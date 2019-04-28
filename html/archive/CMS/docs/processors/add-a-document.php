<?php

include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

$UPLOADIRECTORY = "../../uploadedfiles/docs"; //This is where all the uploaded files and doc (non image) go
//Important: Make sure this folders permissions is 0777!

// Variables passed
$DOCNAME = stripanyslashes($_REQUEST['docname']);
$DOCDESC = stripanyslashes($_REQUEST['docdescription']);
$UPLOADED = $_FILES['uploadeddoc'];

checkEmptyVariable($DOCNAME);
checkEmptyVariable($DOCDESC);
checkEmptyVariable($UPLOADED);

//If it gets this far all the fields are filled in properly.  Let's start the fun part


$RAND = rand(100,99999);  //Random number assigned to file to avoid duplication

//Now Let's upload the Document

$allowed_ext = "doc, docx, xls, pdf, xlsx, 7z, avi, csv, fla, flv, gz, gzip, mid, mov, mp3, mp4, mpc, mpeg, mpg, ods, odt, ppt, pptx, qt, ram, rar, rm, rmi, rmvb, rtf, sdc, sitd, swf, sxc, sxw, tar, tgz, txt, vsd, wav, wma, wmv, xml, zip";
// These are the allowed extensions of the files that are uploaded


$max_size = "5242880";
// 5 MB

// Check Entension
$extension = pathinfo($_FILES['uploadeddoc']['name']); //The second part here always has to be name
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
		header('Location: ../index.php?section=Manage Documents&function=Add a Document&errormessage='.$UPLOAD_DOC_EXT_ERROR);
		exit;
}
elseif ($ok == "1")	
// Check File Size
{
	if($_FILES['uploadeddoc']['size'] > $max_size)
	{
    		header('Location: ../index.php?section=Manage Documents&function=Add a Document&errormessage='.$UPLOAD_DOC_SIZE_ERROR);
        exit;
	}

// The Upload Part
if(is_uploaded_file($_FILES['uploadeddoc']['tmp_name']))
	{
$file_ext_attach1 = strrchr($_FILES['uploadeddoc']['name'], '.');   // Get The File Extention In The Format Of , For Instance, .jpg, .gif or .php 
move_uploaded_file($_FILES['uploadeddoc']['tmp_name'],$UPLOADIRECTORY.'/'.$DOCNAME.'_'.$RAND.$file_ext_attach1);
$uploadsuccess = "yes";
	}
}

//File Uploaded

//Now Enter information in the Database
if ($uploadsuccess == "yes") {
$INSERT = "INSERT INTO `documents`
          (`doc_name`, `doc_desc`, `doc_loc`, `doc_size`)
          VALUES
          ('".$DOCNAME."', '".$DOCDESC."', '".$DOCNAME."_".$RAND.$file_ext_attach1."', '".$_FILES['uploadeddoc']['size']."')";
          $INSERT_RESULT = mysql_query($INSERT) or die(mysql_error());

          //if this doesn't choke
  	header('Location: ../index.php?section=Manage Documents&errormessage='.$UPLOAD_DOC_SUCCESS);
        exit;
		}

?>


