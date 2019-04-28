<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Are you sure you want to delete the following Image?  Please note that, once confirmed, this deletion cannot be undone. </span>
<br />
<br />
<?php
//Get variable passed
$IMAGE_ID = $_REQUEST['imageid'];
?>

 Would you like to delete the image <span class="formtitle"><?php getImgInfo($IMAGE_ID, 'image_loc'); ?></span>?
<br /><br />
<form name="delete-a-pic" action="processors/delete-a-pic.php" enctype="multipart/form-data" method="post">
<input type="hidden" name="imageid" value="<?php echo $IMAGE_ID; ?>" />
<table width="600" cellpadding="3">
	<tr>
    	<td width="300">
       <input type="submit" value="Yes" />
        </td>
        <td width="300">
       <input type="button" onclick="window.location='../pics/?section=Manage Images';" value="Cancel"/>
        </td>
    </tr> 
</table>
</form>