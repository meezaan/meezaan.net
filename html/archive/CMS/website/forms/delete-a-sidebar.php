<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Are you sure you want to delete the following sidebar?  Please note that, once confirmed, this deletion cannot be undone. </span>
<br />
<br />
<?php
//Get variable passed
$SIDEBAR_ID = $_REQUEST['sidebarid'];
?>
 Would you like to delete the sidebar <span class="formtitle"><?php getSidebarName($SIDEBAR_ID); ?></span>?
<br /><br />
<form name="delete-a-sidebar" action="processors/delete-a-sidebar.php" enctype="multipart/form-data" method="post">
<input type="hidden" name="sidebarid" value="<?php echo $SIDEBAR_ID; ?>" />
<table width="600" cellpadding="3">
	<tr>
    	<td width="300">
       <input type="submit" value="Yes" />
        </td>
        <td width="300">
       <input type="button" onclick="window.location='../website/?section=Website Management&sectfunction=Sidebars';" value="Cancel"/>
        </td>
    </tr> 
</table>
</form>