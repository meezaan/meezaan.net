<span class="formtitle"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/comment_yellow.gif" /> This sections lists all the users who have access to your website. Here you can edit or delete an user, or you can add new users.</span>
<br />
<br />
<span class="pagesubmenu"><a href="<?php echo getSiteLoc(); ?>/CMS/users/?section=Users&function=<?php echo $FUNCTION_ADD_USER; ?>" title="Add a User"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/page_user.gif" alt="Add a User" border="0" /> Add a User</a></span>
<br />
<br />
<table width="600" border="0" cellspacing="0" cellpadding="4">
	<tr class="header">
    	<td>No.</td>
        <td>Name</td>
        <td>Username</td>
        <td>Last Login Date</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
<?php
$COLOR = "1";  // For alternating <tr> colours
$RESULT_users = mysql_query("SELECT `user_id` FROM `users`") or die(mysql_error());
$NUM_users = mysql_num_rows($RESULT_users);
for ($i=0; $i<$NUM_users; $i++) {
$USERINFO = mysql_fetch_array($RESULT_users);
if ($COLOR == "1") {
?>
	<tr class="gray"> 
		<td><?php echo $i+1; ?>.</td>
        <td><?php getUserInfo($USERINFO['user_id'], 'user_name'); ?></td>
        <td><?php getUserInfo($USERINFO['user_id'], 'user_username'); ?></td>
        <td><?php getUserLoginTime($USERINFO['user_id']); ?></td>
        <td><a href="<?php getSiteLoc(); ?>/CMS/users/?section=<?php echo $USERS_TRAIL; ?>&sectfunction=&function=<?php echo $FUNCTION_EDIT_USER; ?>&userid=<?php echo $USERINFO['user_id']; ?>" title="Edit this User"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/user-edit-blue.gif" alt="Edit this User" border="0" /></a></td>
        <td><a href="<?php getSiteLoc(); ?>/CMS/users/?section=<?php echo $USERS_TRAIL; ?>&sectfunction=&function=<?php echo $FUNCTION_DELETE_USER; ?>&userid=<?php echo $USERINFO['user_id']; ?>" title="Delete this User"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/delete_action.gif" alt="Delete this User" border="0" /></a></td>
	</tr>
<?php 
$COLOR = "2";
}
else {
?>
	<tr> 
		<td><?php echo $i+1; ?>.</td>
        <td><?php getUserInfo($USERINFO['user_id'], 'user_name'); ?></td>
        <td><?php getUserInfo($USERINFO['user_id'], 'user_username'); ?></td>
        <td><?php getUserLoginTime($USERINFO['user_id']); ?></td>
        <td><a href="<?php getSiteLoc(); ?>/CMS/users/?section=<?php echo $USERS_TRAIL; ?>&sectfunction=&function=<?php echo $FUNCTION_EDIT_USER; ?>&userid=<?php echo $USERINFO['user_id']; ?>" title="Edit this User"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/user-edit-blue.gif" alt="Edit this User" border="0" /></a></td>
        <td><a href="<?php getSiteLoc(); ?>/CMS/users/?section=<?php echo $USERS_TRAIL; ?>&sectfunction=&function=<?php echo $FUNCTION_DELETE_USER; ?>&userid=<?php echo $USERINFO['user_id']; ?>" title="Delete this User"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/delete_action.gif" alt="Delete this User" border="0" /></a></td>
	</tr>
<?php 
$COLOR = "1";
			}

					}
?>
</table>