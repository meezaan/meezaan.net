<?php //check status
checkModEnabled('2');
?>
<span class="formtitle">Welcome to the Plum CMS Photo Gallery Module.</span>
<br />
<br />
Using this module, you can add photo albums and photos to your website.  To visit the Photo Gallery page on your website, simply visit  <?php getSiteLoc(); ?>/gallery.php.  You can also link to your news page from anywhere within your site by pointing to <?php getSiteLoc(); ?>/gallery.php. To see what this page looks like on your website, <a href="<?php getSiteLoc(); ?>/gallery.php" target="_blank">click here</a>.
<br />
<br />
<span class="pagesubmenu"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/icon_settings.gif" alt="Configure Configure Gallery Module" border="0" /> <a href="<?php echo getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=Photo Gallery&function=Configure" title="Configure Configure Gallery Module">EDIT PHOTO GALLERY CONFIGURATION</a></span>
<br />
<span class="pagesubmenu"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/nav/album.gif" alt="Add a Photo Album" border="0" /> <a href="<?php echo getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=Photo Gallery&function=Add Photo Album" title="Add a Photo Album">Add a Photo Album</a></span>
<br />
<br />
<table width="600" border="0" cellspacing="0" cellpadding="4">
<?php
$RESULT_albums = mysql_query("SELECT * FROM `mod_photogallery_album`") or die(mysql_error());
$NUM_albums = mysql_num_rows($RESULT_albums);
for ($i=0; $i<$NUM_albums; $i++) {
$ALBUMINFO = mysql_fetch_array($RESULT_albums);
?>
    <tr>
    <td class="form" width="450" style="border-bottom: 1px black solid;"><?php echo $i+1; ?>. <b><?php echo $ALBUMINFO['album_name']; ?></b>
    <p>
       <a href="<?php echo getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=Photo Gallery&function=View Photo Album&albumid=<?php echo $ALBUMINFO['album_id']; ?>">View Album</a>&nbsp; | &nbsp;<a href="<?php echo getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=Photo Gallery&function=Edit Photo Album&albumid=<?php echo $ALBUMINFO['album_id']; ?>">Edit Album Info</a>&nbsp; | &nbsp;<a href="<?php echo getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=Photo Gallery&function=Add Photo&albumid=<?php echo $ALBUMINFO['album_id']; ?>">Add Photos to Album</a></p>
       <p>&raquo; <a href="<?php echo getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=Photo Gallery&function=Delete Photo Album&albumid=<?php echo $ALBUMINFO['album_id']; ?>" style="font-size: 80%;">Delete this Album</a>
       </p>
    </td>
    <td width="150" style="border-bottom: 1px black solid;">
    <img src="<?php getSiteLoc(); ?>/CMS/modules/photo-gallery-images/<?php echo $ALBUMINFO['album_img']; ?>" alt ="<?php echo $ALBUMINFO['album_caption']; ?>" border="0" />
    </td>
    </tr>

<?php } ?>
</table>