<?php 
$REQ = $_REQUEST['submission'];
if ($REQ == "idandkey") {  //id and key have been submitted
$ID = stripanyslashes($_REQUEST['id']);
$KEY = stripanyslashes($_REQUEST['key']);

$VERIFY = mysql_query("SELECT * FROM `pwd_keys` WHERE `key` = '".$KEY."'") or die(mysql_error());
$NUM_ROWS = mysql_num_rows($VERIFY);

if ($NUM_ROWS != 1) {
header('Location: login.php?inquiry=resetpassword&errormessage=The ID or Key you entered is not valid. Please try again');
}

if ($NUM_ROWS == 1) {
?>
Please enter and confirm a new password and press the "Change Password" button.
<br /><br />
<table width="600" align="center" border="0" cellpadding="7">
<form name="reset-password2" action="processors/reset-password.php?idandkey=submitted" enctype="multipart/form-data" method="post"> 
<input type="hidden" name="id" value="<?php echo $ID; ?>" />
<input type="hidden" name="key" value="<?php echo $KEY; ?>" />
	<tr>
    	<td class="formlabel" width="225">
        <img src="<?php getSiteLoc(); ?>/CMS/icons/small/icon_padlock.gif" alt="password">
        </td>
        <td class="formlabel">
        Password
        </td>
        <td width="300" class="form">
        <input type="password" name="_password_"  size="50"/>
        </td>
    </tr>
    <tr>
    	<td class="formlabel" width="225">
        <img src="<?php getSiteLoc(); ?>/CMS/icons/small/icon_padlock.gif" alt="password">
        </td>
        <td class="formlabel">
        Confirm Password
        </td>
        <td width="300" class="form">
        <input type="password" name="_confirmpassword_"  size="50"/>
        </td>
    </tr>
    <tr>
    	<td colspan="3">
        <br />
        <input type="submit" value="Change Password" />
        </td>
    </tr>
</form>
</table>


<?php } 
 }
  
else {  //if no id and key are submitted
?>
Please enter an ID and Key and press the "Submit" button.
<br /><br />

<table width="600" align="center" border="0" cellpadding="7">
<form name="reset-password1" action="login.php?inquiry=resetpassword&submission=idandkey" enctype="multipart/form-data" method="post"> 
	<tr>
    	<td class="formlabel" width="225">
        <img src="<?php getSiteLoc(); ?>/CMS/icons/small/icon_security.gif" alt="ID">
        </td>
        <td class="formlabel">
        ID
        </td>
        <td width="300" class="form">
        <input type="text" name="id"  size="50"/>
        </td>
    </tr>
    <tr>
    	<td class="formlabel" width="225">
        <img src="<?php getSiteLoc(); ?>/CMS/icons/small/icon_key.gif" alt="Email">
        </td>
        <td class="formlabel">
        Key
        </td>
        <td width="300" class="form">
        <input type="text" name="key"  size="50"/>
        </td>
    </tr>
    <tr>
    	<td colspan="3">
        <br />
        <input type="submit" value="Submit" />
        </td>
    </tr>
</form>
</table>

<?php } ?>