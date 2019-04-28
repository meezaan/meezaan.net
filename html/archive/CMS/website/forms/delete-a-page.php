<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Are you sure you want to delete the following page?  Please note that, once confirmed, this deletion cannot be undone. </span>
<br />
<br />
<?php
//Get variable passed
$PAGE_ID = $_REQUEST['pageid'];
//Check if its the home page, if it is, don't allow deletion
if ($PAGE_ID == "1") {
header('location: ../website/?section=Website Management&sectfunction=Pages&errormessage=Sorry!  You cannot delete the Home page.');
}

?>
 Would you like to delete the page <span class="formtitle"><?php getPageTitle($PAGE_ID); ?></span>?
<br /><br />
<form name="delete-a-page" action="processors/delete-a-page.php" enctype="multipart/form-data" method="post">
<input type="hidden" name="pageid" value="<?php echo $PAGE_ID; ?>" />
<table width="600" cellpadding="3">
	<tr>
    	<td width="300">
       <input type="submit" value="Yes" />
        </td>
        <td width="300">
       <input type="button" onclick="window.location='../website/?section=Website Management&sectfunction=Pages';" value="Cancel"/>
        </td>
    </tr> 
</table>
</form>