<span class="formtitle"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/comment_yellow.gif" /> This sections lists all the Predefined static areas for your website.  Here you can edit these static areas.</span>
<br />
<br />
<table width="600" border="0" cellspacing="0" cellpadding="4">
	<tr class="header">
    	<td>No.</td>
        <td>Area Name</td>
        <td>&nbsp;</td>
    </tr>
<?php
$COLOR = "1";  // For alternating <tr> colours
$RESULT_areas = mysql_query("SELECT `area_id` FROM `static_areas`") or die(mysql_error());
$NUM_areas = mysql_num_rows($RESULT_areas);
for ($i=0; $i<$NUM_areas; $i++) {
$AREAINFO = mysql_fetch_array($RESULT_areas);
if ($COLOR == "1") {
?>
	<tr class="gray"> 
		<td><?php echo $i+1; ?>.</td>
        <td><?php getAreaName($AREAINFO['area_id']); ?></td>
        <td><a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_AREAS; ?>&function=<?php echo $FUNCTION_EDIT_AREA; ?>&areaid=<?php echo $AREAINFO['area_id']; ?>" title="Edit this area"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/edit-blue.gif" alt="Edit this page" border="0" /></a></td>
	</tr>
<?php 
$COLOR = "2";
}
else {
?>
	<tr>
		<td><?php echo $i+1; ?>.</td>
        <td><?php getAreaName($AREAINFO['area_id']); ?></td>
       <td><a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_AREAS; ?>&function=<?php echo $FUNCTION_EDIT_AREA; ?>&areaid=<?php echo $AREAINFO['area_id']; ?>" title="Edit this area"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/edit-blue.gif" alt="Edit this page" border="0" /></a></td>
	</tr>
<?php 
$COLOR = "1";
			}

					}
?>
</table>