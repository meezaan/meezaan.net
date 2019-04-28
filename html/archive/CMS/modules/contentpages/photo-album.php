<?php //check status
checkModEnabled('2');
?>
<?php $ALBUM_ID = $_REQUEST['albumid']; ?>
<br />
<br />
<span class="formtitle">You are currently viewing: <?php getAlbumName($ALBUM_ID); ?>. </span>
<br />
<br />
<?php
$LOWER_LIMIT = $_REQUEST['LOWER_LIMIT'];
if (empty($LOWER_LIMIT)) {
$LOWER_LIMIT = 0;
}
//Album Total numbers
$TOTAL_ALBUM_SQL = mysql_query("SELECT * FROM `mod_photogallery_photos` WHERE `album_id` = '".$ALBUM_ID."'");
$TOTAL_photos = mysql_num_rows($TOTAL_ALBUM_SQL);
$TOTAL_pages = $TOTAL_photos/9;
$CURRENT_page = ($LOWER_LIMIT+9)/9;
?>
<table width="600">
                <tr>
                <td colspan="3" style="width: 100%; text-align: right; font-size: 80%;">
                   Page <?php echo $CURRENT_page; ?>  of <?php echo ceil($TOTAL_pages); ?>
                   </td>
               </tr>
</table>

<table width="600">
             <tr>
<?php
$ALBUM_SQL = mysql_query("SELECT * FROM `mod_photogallery_photos` WHERE `album_id` = '".$ALBUM_ID."' LIMIT ".$LOWER_LIMIT.", 3");
$NUM_photos = mysql_num_rows($ALBUM_SQL);
for ($i=0; $i<$NUM_photos; $i++) {
 $PHOTOLINE1 = mysql_fetch_array($ALBUM_SQL);
?>
                 <td>
                 <a href="<?php getSiteLoc(); ?>/CMS/modules/photo-gallery-images/<?php echo $PHOTOLINE1['photo_img']; ?>" target="_blank"><img src="<?php getSiteLoc(); ?>/CMS/modules/photo-gallery-images/<?php echo $PHOTOLINE1['photo_thumb']; ?>" alt="<?php echo $PHOTOLINE1['photo_caption']; ?>" border="0" /></a>
                 <br />
                 <?php echo $PHOTOLINE1['photo_name']; ?> &nbsp; <a href="<?php getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=Photo Gallery&function=Delete Photo&albumid=<?php echo $ALBUM_ID; ?>&photoid=<?php echo $PHOTOLINE1['photo_id']; ?>" title="Delete this Photo"><img src="<?php getSiteLoc(); ?>/CMS/icons/small/delete_action.gif" alt="Delete Photo" border="0" /></a>  <a href="<?php getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=Photo Gallery&function=Edit Photo&albumid=<?php echo $ALBUM_ID; ?>&photoid=<?php echo $PHOTOLINE1['photo_id']; ?>" title="Edit this Photo"><img src="<?php getSiteLoc(); ?>/CMS/icons/small/edit-blue.gif" alt="Edit Photo" border="0" /></a>
                 <br /><br />
                 </td>
<?php
}
?>
</tr>
<?php
  //Increase lower limit by 3
  $LOWER_LIMIT = $LOWER_LIMIT+3;
?>
             <tr>
            <?php //run query again
$ALBUM_SQL = mysql_query("SELECT * FROM `mod_photogallery_photos` WHERE `album_id` = '".$ALBUM_ID."' LIMIT ".$LOWER_LIMIT.", 3");
$NUM_photos = mysql_num_rows($ALBUM_SQL);
for ($i=0; $i<$NUM_photos; $i++) {
 $PHOTOLINE1 = mysql_fetch_array($ALBUM_SQL);
?>
                 <td>
                 <a href="<?php getSiteLoc(); ?>/CMS/modules/photo-gallery-images/<?php echo $PHOTOLINE1['photo_img']; ?>" target="_blank"><img src="<?php getSiteLoc(); ?>/CMS/modules/photo-gallery-images/<?php echo $PHOTOLINE1['photo_thumb']; ?>" alt="<?php echo $PHOTOLINE1['photo_caption']; ?>" border="0" /></a>
                 <br />
                 <?php echo $PHOTOLINE1['photo_name']; ?> &nbsp; <a href="<?php getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=Photo Gallery&function=Delete Photo&albumid=<?php echo $ALBUM_ID; ?>&photoid=<?php echo $PHOTOLINE1['photo_id']; ?>" title="Delete this Photo"><img src="<?php getSiteLoc(); ?>/CMS/icons/small/delete_action.gif" alt="Delete Photo" border="0" /></a>  <a href="<?php getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=Photo Gallery&function=Edit Photo&albumid=<?php echo $ALBUM_ID; ?>&photoid=<?php echo $PHOTOLINE1['photo_id']; ?>" title="Edit this Photo"><img src="<?php getSiteLoc(); ?>/CMS/icons/small/edit-blue.gif" alt="Edit Photo" border="0" /></a>
                 <br /><br />
                 </td>
<?php
}
?>
</tr>

<?php
  //Increase lower limit by 3
  $LOWER_LIMIT = $LOWER_LIMIT+3;
?>
             <tr>
            <?php //run query again
$ALBUM_SQL = mysql_query("SELECT * FROM `mod_photogallery_photos` WHERE `album_id` = '".$ALBUM_ID."' LIMIT ".$LOWER_LIMIT.", 3");
$NUM_photos = mysql_num_rows($ALBUM_SQL);
for ($i=0; $i<$NUM_photos; $i++) {
 $PHOTOLINE1 = mysql_fetch_array($ALBUM_SQL);
?>
                 <td>
                 <a href="<?php getSiteLoc(); ?>/CMS/modules/photo-gallery-images/<?php echo $PHOTOLINE1['photo_img']; ?>" target="_blank"><img src="<?php getSiteLoc(); ?>/CMS/modules/photo-gallery-images/<?php echo $PHOTOLINE1['photo_thumb']; ?>" alt="<?php echo $PHOTOLINE1['photo_caption']; ?>" border="0" /></a>
                 <br />
                 <?php echo $PHOTOLINE1['photo_name']; ?> &nbsp; <a href="<?php getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=Photo Gallery&function=Delete Photo&albumid=<?php echo $ALBUM_ID; ?>&photoid=<?php echo $PHOTOLINE1['photo_id']; ?>" title="Delete this Photo"><img src="<?php getSiteLoc(); ?>/CMS/icons/small/delete_action.gif" alt="Delete Photo" border="0" /></a>  <a href="<?php getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=Photo Gallery&function=Edit Photo&albumid=<?php echo $ALBUM_ID; ?>&photoid=<?php echo $PHOTOLINE1['photo_id']; ?>" title="Edit this Photo"><img src="<?php getSiteLoc(); ?>/CMS/icons/small/edit-blue.gif" alt="Edit Photo" border="0" /></a>
                 <br /><br />
                 </td>
<?php
}
?>
</tr>
</table>

<table width="600">
                <tr>
                <td colspan="3" style="width: 100%; text-align: right; font-size: 80%;">
                  <?php
                  if ($CURRENT_page != 1) {  ?>

                  <a href="<?php getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=Photo Gallery&function=View Photo Album&albumid=1&LOWER_LIMIT=<?php echo $LOWER_LIMIT-15; ?>"><< Previous</a> &nbsp;&nbsp;
                  <?php
                    }

                    if ($CURRENT_page < $TOTAL_pages) {  ?>

                  <a href="<?php getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=Photo Gallery&function=View Photo Album&albumid=1&LOWER_LIMIT=<?php echo $LOWER_LIMIT+3; ?>">Next >></a>
                  <?php
                    }
                  ?>
                   </td>
               </tr>
</table>
