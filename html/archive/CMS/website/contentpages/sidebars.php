<span class="formtitle"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/comment_yellow.gif" /> This sections lists all the sidebars that exist on your website.  Here you can edit or delete an existing sidebar, or you can add more sidebars.</span>
<br />
<br />
<span class="pagesubmenu"><a href="<?php echo getSiteLoc(); ?>/CMS/website/?section=Website Management&sectfunction=Sidebars&function=Add a Sidebar" title="Add a Sidebar"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/add-page-blue.gif" alt="Add a page" border="0" /> Add a Sidebar</a></span>
<br />
<br />

<table width="600" border="0" cellspacing="0" cellpadding="4">
	<tr class="header">
    	<td>No.</td>
        <td>Sidebar</td>
        <td>Last Edited</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
<?php
$COLOR = "1";  // For alternating <tr> colours
$RESULT_sidebars = mysql_query("SELECT `sidebar_id` FROM `sidebars`") or die(mysql_error());
$NUM_sidebars = mysql_num_rows($RESULT_sidebars);
for ($i=0; $i<$NUM_sidebars; $i++) {
$SIDEBARINFO = mysql_fetch_array($RESULT_sidebars);
if ($COLOR == "1") {
?>
	<tr class="gray"> 
		<td><?php echo $i+1; ?>.</td>
        <td><?php getSidebarName($SIDEBARINFO['sidebar_id']); ?></td>
        <td><?php getSidebarEditedTimeSummary($SIDEBARINFO['sidebar_id']); ?></td>
        <td><a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_SIDEBARS; ?>&function=<?php echo $FUNCTION_EDIT_SIDEBAR; ?>&sidebarid=<?php echo $SIDEBARINFO['sidebar_id']; ?>" title="Edit this sidebar"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/edit-page-blue.gif" alt="Edit this sidebar" border="0" /></a></td>
        <td><a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_SIDEBARS; ?>&function=<?php echo $FUNCTION_DELETE_SIDEBAR; ?>&sidebarid=<?php echo $SIDEBARINFO['sidebar_id']; ?>" title="Delete this sidebar"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/delete-page-blue.gif" alt="Delete this sidebar" border="0" /></a></td>
	</tr>
<?php 
$COLOR = "2";
}
else {
?>
	<tr>
		<td><?php echo $i+1; ?>.</td>
        <td><?php getSidebarName($SIDEBARINFO['sidebar_id']); ?></td>
        <td><?php getSidebarEditedTimeSummary($SIDEBARINFO['sidebar_id']); ?></td>
        <td><a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_SIDEBARS; ?>&function=<?php echo $FUNCTION_EDIT_SIDEBAR; ?>&sidebarid=<?php echo $SIDEBARINFO['sidebar_id']; ?>" title="Edit this sidebar"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/edit-page-blue.gif" alt="Edit this sidebar" border="0" /></a></td>
        <td><a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_SIDEBARS; ?>&function=<?php echo $FUNCTION_DELETE_SIDEBAR; ?>&sidebarid=<?php echo $SIDEBARINFO['sidebar_id']; ?>" title="Delete this sidebar"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/delete-page-blue.gif" alt="Delete this sidebar" border="0" /></a></td>
	</tr>
<?php 
$COLOR = "1";
			}

					}
?>
</table>