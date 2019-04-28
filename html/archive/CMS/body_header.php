  		<div id="header">
        	<img src="<?php getSiteLoc(); ?>/CMS/images/vafta_cms_logo.gif" alt="VAFTA CMS" border="0" />
        </div>
		
        <div id="gap">
        <?php
		if (isset($_SESSION['user_id'])) {
		// In this gap goes the username and logout button 
		?>
		Welcome, <b><?php getUserInfo($_SESSION['user_id'], 'user_name'); ?></b>  |  <a href="<?php getSiteLoc(); ?>/CMS/curruser/?section=<?php echo $CURRENTUSER_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_PROFILE; ?>" class="bg" title="Edit Profile">Edit Profile</a>  |  <a href="<?php getSiteLoc(); ?>/CMS/curruser/?section=<?php echo $CURRENTUSER_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_PWD; ?>" class="bg" title="Change Password">Change Password</a>  |  <a href="<?php getSiteLoc(); ?>/CMS/logout.php" class="bg" title="Logout">Logout</a>
		<?php  } ?>
	    </div>
        