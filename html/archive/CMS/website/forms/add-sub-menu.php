<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Here you can edit/update the pages that comprise pre-defined menus on your website. Please note that each menu sits in a certain section in your website design and adding too many pages to it can render the display of the website incorrect.</span>
<br />
<br />
<?php
//Get variable passed
$MENU_ID = $_REQUEST['menuid'];
$PAGE_ID = $_REQUEST['pageid'];
?>
You are currently adding Sub-Menu Pages to: <strong><?php getPageTitle($PAGE_ID); ?></strong>
<table>
	<tr>
		<td valign="top">
			<table width="300" cellpadding="3">
				<tr class="header" valign="top">
    				<td>
				Pages Available
					</td>
       		 		<td>
        			</td>
   		 		</tr>
                <?php //Get Pages 
				$SQL_totalpages = mysql_query("SELECT page_title, page_id FROM  page WHERE NOT EXISTS (SELECT page_id FROM menu WHERE menu_id = '".$MENU_ID."' AND page.page_id = menu.page_id)");
				while ($PAGES = mysql_fetch_array($SQL_totalpages)) {				
				 ?>
				<tr class="gray">
                	<td>
          				<?php echo $PAGES['page_title']; ?>
                	</td>
                	<td>
                    <a href="processors/add-to-submenu.php?menuid=<?php echo $MENU_ID; ?>&pageid=<? echo $PAGES['page_id']; ?>&pagelevel=1&parentid=<?php echo $PAGE_ID; ?>" title="Add to Sub Menu"><img src="<?php getSiteLoc(); ?>/CMS/icons/small/action_forward.gif" alt="Add to Sub Menu" border="0" /></a>
                    </td>                    
                </tr>    
                <?php
				}	
				?>
			</table>
		</td>
        <td valign="top">
			<table width="300" cellpadding="3">
				<tr class="header">
        			<td>
        			</td>
        			<td>
        				Sub Pages Added
        			</td>
    			</tr> 
                <?php //Get Pages 
				$SQL_pages = mysql_query("SELECT menu.page_id, page.page_title FROM  menu, page WHERE menu.menu_id = '".$MENU_ID."' AND menu.page_parent = '".$PAGE_ID."' AND menu.page_id = page.page_id");
				$NUM_pages = mysql_num_rows($SQL_pages);
				for ($i=0; $i<$NUM_pages; $i++) {
				$PAGE = mysql_fetch_array($SQL_pages);
				?>
				<tr class="gray">
                	<td>
                     <a href="processors/remove-from-sub-menu.php?menuid=<?php echo $MENU_ID; ?>&pageid=<?php echo $PAGE['page_id']; ?>&pagelevel=1&parentid=<?php echo $PAGE_ID; ?>" title="Remove from Menu"><img src="<?php getSiteLoc(); ?>/CMS/icons/small/action_back.gif" alt="Remove from Menu" border="0" /></a>
                    </td>
                	<td>
                <?php echo $PAGE['page_title']; ?>
                	</td>


                </tr>    
				<?php }		?>   
			</table>
        </td>
    </tr>
</table>
