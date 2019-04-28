<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Are you sure you want to delete the following Photo?  Please note that, once confirmed, this deletion cannot be undone. </span>
<br />
<br />
<?php
//Get variables passed
$ALBUM_ID = $_REQUEST['albumid'];
$PHOTO_ID = $_REQUEST['photoid'];
?>
 Would you like to delete the photo <span class="formtitle">
 <?php getPhotoInfo($PHOTO_ID,'photo_name'); ?></span> 
 from the album <?php getAlbumName($ALBUM_ID); ?>?
<br /><br />
<center>
<img src="<?php getSiteLoc(); ?>/CMS/modules/photo-gallery-images/<?php getPhotoInfo($PHOTO_ID,'photo_thumb'); ?>"
alt="<?php getPhotoInfo($PHOTO_ID,'photo_caption'); ?>" />
</center>
<form name="delete-photo" action="processors/delete-photo.php" enctype="multipart/form-data" method="post">
<input type="hidden" name="albumid" value="<?php echo $ALBUM_ID; ?>" />
<input type="hidden" name="photoid" value="<?php echo $PHOTO_ID; ?>" />
<table width="600" cellpadding="3">
	<tr>
    	<td width="300">
       <input type="submit" value="Yes" />
        </td>
        <td width="300">
       <input type="button" onclick="window.location='../modules/?section=Modules&sectfunction=Photo Gallery&function=View Photo Album&albumid=<?php echo $ALBUM_ID; ?>'" value="Cancel"/>
        </td>
    </tr> 
</table>
</form>