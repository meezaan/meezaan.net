<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Please update your profile information as needed and press the "Confirm Changes" button. Please make sure you have a valid email address here as that may be used to communicate with you from time to time.</span>
<br />
<br />
<form name="edit-profile" action="processors/edit-profile.php" enctype="multipart/form-data" method="post">
<table width="600" cellpadding="2">
	<tr>
    	<td  class="formlabel">
    	Name
    	</td>
    	<td class="form">

    <input type="text" name="name" size="30" value="<?php getUserInfo($_SESSION['user_id'], 'user_name'); ?>" />

    	</td>
    </tr>
	<tr>
    	<td  class="formlabel">
    		Username
    	</td>
    	<td class="form">
                <?php if ($_SESSION['user_id'] == "1") {
		getUserInfo($_SESSION['user_id'], 'user_username'); ?>
		<input type="hidden" name="username" size="30" value="<?php getUserInfo($_SESSION['user_id'], 'user_username'); ?>" />
		<?php } 
		else { ?>
    <input type="text" name="username" size="30" value="<?php getUserInfo($_SESSION['user_id'], 'user_username'); ?>" />
        <?php } ?>
    	</td>
    </tr>
    <tr>
    	<td class="formlabel">
    		Email
    	</td>
    	<td class="form">
    <input type="text" name="email" size="40" value="<?php getUserInfo($_SESSION['user_id'], 'user_email'); ?>" />
    	</td>
    </tr>
    <tr>
    	<td class="formlabel">
    Telephone
    	</td>
    	<td class="form">
   <input type="text" name="tel" size="30" value="<?php getUserInfo($_SESSION['user_id'], 'user_tel'); ?>" />
    	</td>
    </tr>
    <tr>
    	<td colspan="2">
        <br />
        <input type="submit" value="Confirm Changes" />
        </td>
    </tr>
</table>
</form>