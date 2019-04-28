To initiate a password reset request, please enter the Username and Email address you have forgotten the password for and press the "Submit" button.  We'll email you some information to reset the password for the account.
<br /><br />
<table width="600" align="center" border="0" cellpadding="7">
<form name="reset-password" action="processors/reset-password.php" enctype="multipart/form-data" method="post"> 
	<tr>
    	<td width="300" class="formlabel">
        <img src="<?php getSiteLoc(); ?>/CMS/icons/small/icon_user.gif" alt="Username"> Username
        </td>
        <td width="300" class="form">
        <input type="text" name="username"  size="30"/>
        </td>
    </tr>
    <tr>
    	<td width="300" class="formlabel">
        <img src="<?php getSiteLoc(); ?>/CMS/icons/small/email-blue.gif" alt="Email"> Email
        </td>
        <td width="300" class="form">
        <input type="text" name="email"  size="50"/>
        </td>
    </tr>
    <tr>
    	<td colspan="2" >
        <br />
        <input type="submit" value="Submit" />
        </td>
    </tr>
</form>
</table>
