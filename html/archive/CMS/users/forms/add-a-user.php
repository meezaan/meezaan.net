<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> To add a new user, please fill in the following form and press the "Submit" button.</span>
<br />
<br />
<form name="add-a-user" action="processors/add-a-user.php" enctype="multipart/form-data" method="post">
<table width="600" cellpadding="2">
	<tr>
    	<td  class="formlabel">
    Name
    	</td>
    	<td class="form">
    <input type="text" name="name" size="30" />
    	</td>
    </tr>
	<tr>
    	<td  class="formlabel">
    Username
    	</td>
    	<td class="form">
    <input type="text" name="username" size="30" />
    	</td>
    </tr>
    <tr>
    	<td  class="formlabel">
    Password
    	</td>
    	<td class="form">
    <input type="password" name="password" size="30" />
    	</td>
    </tr>
    <tr>
    	<td  class="formlabel">
    Confirm Password
    	</td>
    	<td class="form">
    <input type="password" name="confirmpassword" size="30" />
    	</td>
    </tr>
    <tr>
    	<td  class="formlabel">
    User Type
    	</td>
    	<td class="form">
			<?php getUserTypeChooser(); ?>
    	</td>
    </tr>
    <tr>
    	<td  class="formlabel">
    Email
    	</td>
    	<td class="form">
    <input type="text" name="email" size="30" />
    	</td>
    </tr>
    <tr>
    	<td  class="formlabel">
    Telephone
    	</td>
    	<td class="form">
    <input type="text" name="tel" size="30" />
    	</td>
    </tr>
    <tr>
    	<td colspan="2">&nbsp;</td>
    </tr>
    <tr>
    	<td colspan="2">
        <input type="submit" value="Submit" />
        </td>
    </tr>
    
</table>
</form>