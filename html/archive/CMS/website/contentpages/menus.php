<span class="formtitle"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/comment_yellow.gif" /> This sections lists all the Predefined menus for your website.  Here you can edit these menus.  Menus are pre-defined based on the design of your website.  You can choose to edit the pages and links in the menu.  Please note that you must check if the menu displays properly after updating, as adding more links than the template can handle may throw off the page display of your website.</span>
<br />
<br />
<table width="600" border="0" cellspacing="0" cellpadding="4">
	<tr class="header">
    	<td>No.</td>
        <td>Menu Name</td>
        <td>Pages</td>
        <td>&nbsp;</td>
    </tr>
<?php
$COLOR = "1";
$RESULT_menus = mysql_query("SELECT * FROM `menu_types`") or die(mysql_error());
$NUM_menus = mysql_num_rows($RESULT_menus);
for ($i=0; $i<$NUM_menus; $i++) {
$MENUSINFO = mysql_fetch_array($RESULT_menus);
if ($COLOR == "1") { ?>
	<tr class="gray"> 
		<td><?php echo $i+1; ?>.</td>
        <td><?php echo $MENUSINFO['menu_name']; ?></td>
        <td><?php getMenuPages($MENUSINFO['menu_id']); ?></td>
        <td><a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_MENUS; ?>&function=<?php echo $FUNCTION_EDIT_MENU; ?>&menuid=<?php echo $MENUSINFO['menu_id']; ?>" title="Edit this menu"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/edit-blue.gif" alt="Edit this menu" border="0" /></a></td>
	</tr>
<?php 
$COLOR = "2";
}	
else { ?>

	<tr> 
		<td><?php echo $i+1; ?>.</td>
        <td><?php echo $MENUSINFO['menu_name']; ?></td>
        <td><?php getMenuPages($MENUSINFO['menu_id']); ?></td>
        <td><a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_MENUS; ?>&function=<?php echo $FUNCTION_EDIT_MENU; ?>&menuid=<?php echo $MENUSINFO['menu_id']; ?>" title="Edit this menu"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/edit-blue.gif" alt="Edit this menu" border="0" /></a></td>
	</tr>			
<?php 
$COLOR = "1";
} 
}
?>
</table>