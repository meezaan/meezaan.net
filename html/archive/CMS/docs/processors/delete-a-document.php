<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

$UPLOADIRECTORY = "../../uploadedfiles/docs";
// Variables passed
$DOC_ID = $_REQUEST['docid'];

checkEmptyVariable($DOC_ID);
//If it gets this far a page id has been submitted after confirmation

$DOC = getDocInfoReturn($DOC_ID, 'doc_loc');

//Let's get rid of the file first

if (unlink($UPLOADIRECTORY.'/'.$DOC)) //if the file is deleted then remove it from the database 

{

mysql_query("DELETE FROM `documents` WHERE `doc_id` = '".$DOC_ID."'") or die(mysql_error());	

//redirect with success message			
header('location: ../index.php?section=Manage Documents&errormessage='.$MESSAGE_DOC_DELETED_SUCCESSFULLY);

}

else {
header('location: ../index.php?section=Manage Documents&errormessage='.$MESSAGE_DOC_DELETED_FAIL);
}

?>