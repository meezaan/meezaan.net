<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> To change your password, please enter your existing password in the"Current Password" field, followed by the new password in the "New Password" and "Confirm New Password" fields and press the "Change Password" button.</span>
<br />
<br />
<form name="edit-password" action="processors/edit-password.php" enctype="multipart/form-data" method="post">
<table width="600" cellpadding="2">
	<tr>
    	<td  class="formlabel">
    Current Password
    	</td>
    	<td>
    <input type="password" name="currentpassword" size="30" />
    	</td>
    </tr>
	<tr>
    	<td  class="formlabel">
    New Password
    	</td>
    	<td>
    <input type="password" name="newpassword" size="30" />
    	</td>
    </tr>
    <tr>
    	<td  class="formlabel">
    Confirm New Password
    	</td>
    	<td>
    <input type="password" name="confirmnewpassword" size="30" />
    	</td>
    </tr>
    <tr>
    	<td colspan="2">
        <br />
        <input type="submit" value="Change Password" />
        </td>
    </tr>
</table>
</form>