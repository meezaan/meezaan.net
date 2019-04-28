<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> These are the default settings for the Plum CMS Photo Gallery Module.  All Albums and Photos uploaded using this module will use the following configuration. Please make any changes as you see fit and press the "Submit" button. </span>
<br />
<br />
<br />
<br />
<form name="configure-photo-gallery" action="processors/configure-photo-gallery.php" enctype="multipart/form-data" method="post">
<table width="600" cellpadding="3">
	<tr>
    	<td width="600" class="form" align="left">
        <b>Resize Photos?</b>
        &nbsp;&nbsp;
			<?php getGalleryPhotoSizeSelector(); ?>
			<br />
        <span class="formcomment">(If you choose "Yes" all photographs will be resized to the size specified below. We suggest you use 800 x 800 for the sake of efficient storage.)</span>	
        </td>
    </tr>
    <tr>
    	<td width="600" class="form" align="left">
		<input type="text" name="photo-width" value="<?php getGalleryInfo('photo_width'); ?>" /> pixes wide by <input type="text" name="photo-height" value="<?php getGalleryInfo('photo_height'); ?>" /> pixels high
		</td>
    </tr> 
	<tr>
    	<td width="600" class="form" align="left">
        <b>Resize Thumbnails?</b>
        &nbsp;&nbsp;
			<?php getGalleryThumbSizeSelector(); ?>
			<br />
        <span class="formcomment">(If you choose "Yes" all thumbnails will be resized to the size specified below. We suggest you use 120 x 120.)</span>	
        </td>
    </tr>
    <tr>
    	<td width="600" class="form" align="left">
		<input type="text" name="thumb-width" value="<?php getGalleryInfo('thumb_width'); ?>" /> pixes wide by <input type="text" name="thumb-height" value="<?php getGalleryInfo('thumb_height'); ?>" /> pixels high
		</td>
    </tr> 
    <tr>
    	<td width="600" align="center">
    	<br />
		<input type="submit" value="Submit" />
		</td>
    
    </tr>
</table>
</form>