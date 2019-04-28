<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Are you sure you want to delete the following Photo?  Please note that, once confirmed, this deletion cannot be undone. </span>
<br />
<br />
<?php
//Get variables passed
$ALBUM_ID = $_REQUEST['albumid'];
?>
 Would you like to delete the photo <span class="formtitle">
 <?php getAlbumName($ALBUM_ID,'photo_name'); ?></span>? Please note that deleting the album will also remove all the photos you have added to it.
<br /><br />
<center>
<img src="<?php getSiteLoc(); ?>/CMS/modules/photo-gallery-images/<?php getAlbumInfo($ALBUM_ID,'album_img'); ?>"
alt="<?php getPhotoInfo($PHOTO_ID,'photo_caption'); ?>" />
</center>
<form name="delete-album" action="processors/delete-album.php" enctype="multipart/form-data" method="post">
<input type="hidden" name="albumid" value="<?php echo $ALBUM_ID; ?>" />
<table width="600" cellpadding="3">
	<tr>
    	<td width="300">
       <input type="submit" value="Yes" />
        </td>
        <td width="300">
       <input type="button" onclick="window.location='../modules/?section=Modules&sectfunction=Photo Gallery'" value="Cancel"/>
        </td>
    </tr> 
</table>
</form>