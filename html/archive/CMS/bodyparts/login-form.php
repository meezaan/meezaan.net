<table width="600" align="center" border="0" cellpadding="5">
<form name="login" action="processors/login.php" enctype="multipart/form-data" method="post"> 
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
        <img src="<?php getSiteLoc(); ?>/CMS/icons/small/icon_padlock.gif" alt="Password"> Password 
        </td>
        <td width="300" class="form">
        <input type="password" name="password" size="30" />
        </td>
    </tr>
    <tr>
    	<td></td>
    	<td class="passwordlink">
        	<a href="login.php?inquiry=forgotpassword" class="passwordlink" title="Forgot your Password?">Forgot your password?</a>
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
