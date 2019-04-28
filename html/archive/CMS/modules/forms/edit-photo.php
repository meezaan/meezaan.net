<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Please fill in the following form to add a Photo and press the "Submit" button.  If you are unclear about what a certain section does, please click on the <img src="<?php echo getSiteLoc();?>/CMS/icons/small/icon_info.gif" alt="VAFTA CMS Knowledge Base" /> icon to view more information about the specific section in the Plum CMS knowledge base. </span>
<br />
<br />
<?php $ALBUM_ID = $_REQUEST['albumid']; ?>
<?php $PHOTO_ID = $_REQUEST['photoid']; ?>
You are currently editing the Photo: <strong><?php getPhotoInfo($PHOTO_ID, 'photo_name'); ?></strong>
<br />
<br />
<center>
<a target="_blank" href="<?php getSiteLoc(); ?>/CMS/modules/photo-gallery-images/<?php getPhotoInfo($PHOTO_ID, 'photo_img'); ?>"><img src="<?php getSiteLoc(); ?>/CMS/modules/photo-gallery-images/<?php getPhotoInfo($PHOTO_ID, 'photo_thumb'); ?>" border="0" /></a>
</center>
<br />
<br />
<form name="edit-photo" action="processors/edit-photo.php" enctype="multipart/form-data" method="post">
<table width="600" cellpadding="3">
<input type="hidden" value="<?php echo $ALBUM_ID; ?>" name="albumid" />
<input type="hidden" value="<?php echo $PHOTO_ID; ?>" name="photoid" />
       <tr>
            <td class="form" width="300">
                 Photo Name
            </td>
            <td class="form" width="300">
                <input type="text" size="20" name ="photo_name" value="<?php getPhotoInfo($PHOTO_ID, 'photo_name'); ?>" />
            </td>
       </tr>
        <tr>
            <td class="form" width="300">
                 Photo Description
            </td>
            <td class="form" width="300">
                <input type="text" size="30" name ="photo_desc" value="<?php getPhotoInfo($PHOTO_ID, 'photo_desc'); ?>" />
            </td>
       </tr>
        <tr>
            <td class="form" width="300">
                Photo Caption <?php getKBRef('http://www.vafta.com/cms/knowledgebase/'); ?>
            </td>
            <td class="form" width="300">
                <input type="text" size="30" name ="photo_caption" value="<?php getPhotoInfo($PHOTO_ID, 'photo_caption'); ?>" />
            </td>
       </tr>
       <tr>
            <td align="center" width="600" colspan="2">
                <br />
            <input type="submit" value="Submit">
            </td>
       </tr>
</table>
</form>