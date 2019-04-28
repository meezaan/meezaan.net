<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Are you sure you want to delete the following news item?  Please note that, once confirmed, this deletion cannot be undone. </span>
<br />
<br />
<?php
//Get variable passed
$NEWS_ID = $_REQUEST['newsitem'];
checkEmptyVariable($NEWS_ID);
?>
 Would you like to delete the news item <span class="formtitle"><?php getNewsinfo($NEWS_ID,'news_title'); ?></span>?
<br /><br />
<form name="delete-news" action="processors/delete-news.php" enctype="multipart/form-data" method="post">
<input type="hidden" name="newsitem" value="<?php echo $NEWS_ID; ?>" />
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