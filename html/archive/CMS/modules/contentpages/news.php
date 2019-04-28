<?php //check status
checkModEnabled('1');
?>
<span class="formtitle">Welcome to the Plum CMS News Module.</span>
<br />
<br />
Using this module, you can add news items to your website.  To visit your latest news page on your website, simply visit  <?php getSiteLoc(); ?>/news.php.  You can also link to your news page from anywhere within your site by pointing to <?php getSiteLoc(); ?>/news.php. To see what this page looks like on your website, <a href="<?php getSiteLoc(); ?>/news.php" target="_blank">click here</a>.
<br />
<br />
<span class="pagesubmenu"><a href="<?php echo getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=News&function=Add a News Item" title="Add a News Item"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/add-news.gif" alt="Add a News Item" border="0" /> Add a News Item</a></span>
<br />
<br />
<table width="600" border="0" cellspacing="0" cellpadding="4">
<tr class="header">
<td>No.</td>
<td>News Title</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<?php
$COLOR = "1";  // For alternating <tr> colours
$RESULT_news = mysql_query("SELECT * FROM `mod_news`") or die(mysql_error());
$NUM_news = mysql_num_rows($RESULT_news);
for ($i=0; $i<$NUM_news; $i++) {
$NEWSINFO = mysql_fetch_array($RESULT_news);
if ($COLOR == "1") {
?>
	<tr class="gray"> 
    <td class="form" width="20"><?php echo $i+1; ?>. </td>
    <td width="500"><?php echo $NEWSINFO['news_title']; ?></td>
   	<td width="50"><a href="<?php getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=News&function=<?php echo $FUNCTION_EDIT_NEWS; ?>&newsitem=<?php echo $NEWSINFO['news_id']; ?>" title="Edit News Item"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/edit-blue.gif" alt="Edit News Item" border="0" /></td>
    <td width="50"><a href="<?php getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=News&function=<?php echo $FUNCTION_DELETE_NEWS; ?>&newsitem=<?php echo $NEWSINFO['news_id']; ?>" title="Delete News Item"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/delete_action.gif" alt="Edit News Item" border="0" /></td>
    </tr>


<?php 
$COLOR = "2";
}
else {
?>
	<tr> 
    <td class="form" width="20"><?php echo $i+1; ?>. </td>
    <td width="500"><?php echo $NEWSINFO['news_title']; ?></td>
   	<td width="50"><a href="<?php getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=News&function=<?php echo $FUNCTION_EDIT_NEWS; ?>&newsitem=<?php echo $NEWSINFO['news_id']; ?>" title="Edit News Item"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/edit-blue.gif" alt="Edit News Item" border="0" /></td>
    <td width="50"><a href="<?php getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=News&function=<?php echo $FUNCTION_DELETE_NEWS; ?>&newsitem=<?php echo $NEWSINFO['news_id']; ?>" title="Delete News Item"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/delete_action.gif" alt="Edit News Item" border="0" /></td>
    </tr>


<?php 
$COLOR = "1";
			}

					}
?>
</table>