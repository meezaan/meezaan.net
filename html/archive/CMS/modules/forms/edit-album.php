<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Please make any changes to the album information and press the "Confirm Changes" button.</span>
<br />
<br />
<?php
//Get variable passed
$ALBUM_ID = $_REQUEST['albumid'];
checkEmptyVariable($ALBUM_ID);
?>
You are currently editing the album: <?php getAlbumName($ALBUM_ID); ?>
<br />
<br />
<form name="edit-photo-album" action="processors/edit-album.php" enctype="multipart/form-data" method="post">
<input type="hidden" name="albumid" value="<?php echo $ALBUM_ID; ?>" />
<table width="600" cellpadding="3">
       <tr>
            <td class="form" width="300">
                 Album Name
            </td>
            <td class="form" width="300">
                <input type="text" size="20" name ="album_name" value="<?php getAlbumInfo($ALBUM_ID, 'album_name'); ?>" />
            </td>
       </tr>
        <tr>
            <td class="form" width="300">
                 Album Description
            </td>
            <td class="form" width="300">
                <input type="text" size="30" name ="album_desc" value="<?php getAlbumInfo($ALBUM_ID, 'album_desc'); ?>" />
            </td>
       </tr>
        <tr>
            <td class="form" width="300">
                 Album Caption <?php getKBRef('http://www.vafta.com/cms/knowledgebase/'); ?>
            </td>
            <td class="form" width="300">
                <input type="text" size="30" name ="album_caption" value="<?php getAlbumInfo($ALBUM_ID, 'album_caption'); ?>" />
            </td>
       </tr>
        <tr>
            <td class="form" width="300">
                 Current Album Photo <br />
            </td>
            <td class="form" width="300">
                <img src="<?php getSiteLoc(); ?>/CMS/modules/photo-gallery-images/<?php getAlbumInfo($ALBUM_ID, 'album_img'); ?>" />
            </td>
       </tr>
        <tr>
            <td class="form" width="300">
                 New Album Photo <br />
                 <span class="formcomment"> Leave Empty if you don't want to change photo </span>
            </td>
            <td class="form" width="300">
                <input type="file" size="30" name ="album_img" />
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