<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Are you sure you want to delete the following file / document?  Please note that, once confirmed, this deletion cannot be undone. </span>
<br />
<br />
<?php
//Get variable passed
$DOC_ID = $_REQUEST['docid'];
?>

 Would you like to delete the file <span class="formtitle"><?php getDocInfo($DOC_ID, 'doc_loc'); ?></span>?
<br /><br />
<form name="delete-a-document" action="processors/delete-a-document.php" enctype="multipart/form-data" method="post">
<input type="hidden" name="docid" value="<?php echo $DOC_ID; ?>" />
<table width="600" cellpadding="3">
	<tr>
    	<td width="300">
       <input type="submit" value="Yes" />
        </td>
        <td width="300">
       <input type="button" onclick="window.location='../docs/?section=Manage Documents';" value="Cancel"/>
        </td>
    </tr> 
</table>
</form>