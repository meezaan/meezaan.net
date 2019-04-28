<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Please make any changes and press the "Confirm Changes" button.</span>
<br />
<br />
<?php
$USERID = $_REQUEST['userid'];
?>
<form name="edit-a-user" action="processors/edit-a-user.php" enctype="multipart/form-data" method="post">
<input type="hidden" name="userid" value="<?php echo $USERID; ?>"
<table width="600" cellpadding="2">
	<tr>
    	<td  class="formlabel">
    Name
    	</td>
    	<td class="form">
    <input type="text" name="name" size="30" value="<?php getUserInfo($USERID, 'user_name'); ?>" />
      	</td>
    </tr>
	<tr>
    	<td  class="formlabel">
    Username
    	</td>
    	<td class="form">
         <?php if ($USERID == "1") { //User is Admin, cannot change username
				echo 'admin';			
			?>
            <input type="hidden" name ="username" value="<?php getUserInfo($USERID, 'user_username'); ?>"  />
            <?php }
			else { ?>
    <input type="text" name="username" size="30" value ="<?php getUserInfo($USERID, 'user_username'); ?>"/>
    <?php } ?>
    	</td>
    </tr>
    <tr>
    	<td  class="formlabel">
    User Type
    	</td>
    	<td class="form">
        	<?php if ($USERID == "1") { //User is Admin, cannot change type
				echo 'Administrator';			
			?>
            <input type="hidden" name ="user_type_id" value="2"  />
            <?php }
		else {
		 getUserTypeChooserEditor($USERID); 
		 }
		 ?>
    	</td>
    </tr>
    <tr>
    	<td  class="formlabel">
    Email
    	</td>
    	<td class="form">
    <input type="text" name="email" size="30" value="<?php getUserInfo($USERID, 'user_email'); ?>" />
    	</td>
    </tr>
    <tr>
    	<td  class="formlabel">
    Telephone
    	</td>
    	<td class="form">
    <input type="text" name="tel" size="30" value="<?php getUserInfo($USERID, 'user_tel'); ?>"/>
    	</td>
    </tr>
    <tr>
    	<td colspan="2">&nbsp;</td>
    </tr>
    <tr>
    	<td colspan="2">
        <input type="submit" value="Confirm Changes" />
        </td>
    </tr>
    
</table>
</form>