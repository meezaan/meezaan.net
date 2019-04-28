<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Are you sure you want to delete the following user?  Please note that, once confirmed, this deletion cannot be undone. </span>
<br />
<br />
<?php
//Get variable passed
$USER_ID = $_REQUEST['userid'];
//Check if its the home page, if it is, don't allow deletion
if ($USER_ID == "1") {
header('location: ../users/?section=Users&errormessage=Sorry!  You cannot delete the User admin.');
exit;
}
if ($USER_ID == $_SESSION['user_id']) {
header('location: ../users/?section=Users&errormessage=Sorry!  You cannot delete your own username.  Please ask another administrator to delete your username.');
exit;
}

?>
 Would you like to delete the User <span class="formtitle"><?php getUserInfo($USER_ID, 'user_name'); ?></span>?
<br /><br />
<form name="delete-a-user" action="processors/delete-a-user.php" enctype="multipart/form-data" method="post">
<input type="hidden" name="userid" value="<?php echo $USER_ID; ?>" />
<table width="600" cellpadding="3">
	<tr>
    	<td width="300">
       <input type="submit" value="Yes" />
        </td>
        <td width="300">
       <input type="button" onclick="window.location='../users/?section=Users';" value="Cancel"/>
        </td>
    </tr> 
</table>
</form>