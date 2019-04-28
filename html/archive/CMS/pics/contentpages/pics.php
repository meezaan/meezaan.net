<span class="formtitle"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/comment_yellow.gif" /> This sections lists all the images that have been uploaded and are available for use on the website. Here you can add new images, see the direct URL to an existing image, or delete images.</span>
<br />
<br />
<span class="pagesubmenu"><a href="<?php echo getSiteLoc(); ?>/CMS/pics/?section=Manage Images&function=Add an Image" title="Add an image"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/image_new.gif" alt="Add an image" border="0" /> Add an Image</a></span>
<br />
<br />
<table width="600" border="0" cellspacing="0" cellpadding="4">
<?php
$COLOR = "1";  // For alternating <tr> colours
$RESULT_images= mysql_query("SELECT `image_id` FROM `images`") or die(mysql_error());
$NUM_images = mysql_num_rows($RESULT_images);
for ($i=0; $i<$NUM_images; $i++) {
$IMGINFO = mysql_fetch_array($RESULT_images);
if ($COLOR == "1") {
?>
	<tr class="gray"> 
		<td width="50"><?php echo $i+1; ?>.</td>
      <td class="form"><img src="<?php echo getSiteLoc(); ?>/CMS/uploadedfiles/pics/<?php getImgInfo($IMGINFO['image_id'],'image_loc_sm'); ?>" /> 
      </td>
      <td>
      <a href="<?php getSiteLoc(); ?>/CMS/pics/?section=<?php echo $MANAGE_IMAGES_TRAIL; ?>&function=<?php echo $FUNCTION_DELETE_IMAGE; ?>&imageid=<?php echo $IMGINFO['image_id']; ?>" title="Delete this Image"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/delete_action.gif" alt="Delete this Image" border="0" /></a>
      </td>
  </tr>
  <tr class="gray">
  	<td></td>
  	<td class="form" colspan="2">
	<?php getImgInfo($IMGINFO['image_id'],'image_name'); ?> 
    </td>
  </tr>
    <tr class="gray">
    	<td class="form">
        <b>Direct Link:</b> 
        </td>
        <td colspan="2" class="form"><a href="<?php getSiteLoc(); ?>/CMS/uploadedfiles/pics/<?php getImgInfo($IMGINFO['image_id'], 'image_loc'); ?>" target="_blank"><?php getSiteLoc(); ?>/CMS/uploadedfiles/pics/<?php getImgInfo($IMGINFO['image_id'], 'image_loc'); ?></a></td>
    </tr>
    <tr class="gray">
    	<td class="form">
        <b>Description:</b> 
        </td>
        <td colspan="2" class="form"><?php getImgInfo($IMGINFO['image_id'], 'image_description'); ?></td>
    </tr>

<?php 
$COLOR = "2";
}
else {
?>
	<tr> 
	<td width="50"><?php echo $i+1; ?>.</td>
     <td class="form"><img src="<?php echo getSiteLoc(); ?>/CMS/uploadedfiles/pics/<?php getImgInfo($IMGINFO['image_id'],'image_loc_sm'); ?>" /> </td>
     <td colspan="2">
     <a href="<?php getSiteLoc(); ?>/CMS/pics/?section=<?php echo $MANAGE_IMAGES_TRAIL; ?>&function=<?php echo $FUNCTION_DELETE_IMAGE; ?>&imageid=<?php echo $IMGINFO['image_id']; ?>" title="Delete this Image"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/delete_action.gif" alt="Delete this Image" border="0" /></a>
     </td>
  </tr>
  <tr>
  	<td>	</td>
  	<td class="form" colspan="2">
	<?php getImgInfo($IMGINFO['image_id'],'image_name'); ?> &nbsp;&nbsp; &nbsp; 
    </td>

  </tr>
    <tr>
    	<td class="form">
        <b>Direct Link:</b> 
        </td>
        <td colspan="2" class="form"><a href="<?php getSiteLoc(); ?>/CMS/uploadedfiles/pics/<?php getImgInfo($IMGINFO['image_id'], 'image_loc'); ?>" target="_blank"><?php getSiteLoc(); ?>/CMS/uploadedfiles/pics/<?php getImgInfo($IMGINFO['image_id'], 'image_loc'); ?></a></td>
    </tr>
    <tr>
    	<td class="form">
        <b>Description:</b> 
        </td>
        <td colspan="2" class="form"><?php getImgInfo($IMGINFO['image_id'], 'image_description'); ?></td>
    </tr>
	

<?php 
$COLOR = "1";
			}

					}
?>
</table>