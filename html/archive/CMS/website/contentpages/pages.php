<span class="formtitle"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/comment_yellow.gif" /> This sections lists all the pages that exist on your website.  Here you can edit or delete an existing page, or you can add more pages.</span>
<br />
<br />
<span class="pagesubmenu"><a href="<?php echo getSiteLoc(); ?>/CMS/website/?section=Website Management&sectfunction=Pages&function=Add a Page" title="Add a page"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/add-page-blue.gif" alt="Add a page" border="0" /> Add a page</a></span>
<br />
<br />
<table width="600" border="0" cellspacing="0" cellpadding="4">
<?php
//Collecting information for pagination -- Display 15 items per page

$UPPER_LIMIT = $_REQUEST['UPPER_LIMIT'];
if (empty($UPPER_LIMIT)) {
$UPPER_LIMIT = 15;
}

$LOWER_LIMIT = $_REQUEST['LOWER_LIMIT'];
if (empty($LOWER_LIMIT)) {
$LOWER_LIMIT = 0;
}

$COLOR = "1";  // Set colour for alternating <tr> colours
$PAGE_QUERY = "SELECT `page_id` FROM `page` WHERE `page_id` != '1' ORDER BY page_title ASC";
$RESULT_pages = mysql_query($PAGE_QUERY) or die(mysql_error());
$NUM_pages = mysql_num_rows($RESULT_pages);

//If no pages are loaded, stop right here
		if ($NUM_pages == 0) {
		echo 'There are currently no pages in your website. Please Add a page by clicking the link above.';
		die();
		}
$PAGE_QUERY1 = $PAGE_QUERY." LIMIT $LOWER_LIMIT, $UPPER_LIMIT";

$CHECK_UPPER_LIMIT = $UPPER_LIMIT+15;
$CHECK_LOWER_LIMIT = $LOWER_LIMIT+15;

$PAGE_QUERY2 = $PAGE_QUERY." LIMIT $CHECK_LOWER_LIMIT, $CHECK_UPPER_LIMIT";

                $RESULT_pages1 = mysql_query($PAGE_QUERY1);
		$RESULT_pages2 = mysql_query($PAGE_QUERY2);
		$NUM_pages1 = mysql_num_rows($RESULT_pages1);
		$NUM_pages2 = mysql_num_rows($RESULT_pages2);

                ?>

  <tr>
            <td colspan="7" style="text-align: right; font-size: 80%;">
                Page
                 <?php 
                 $TOTALPAGES = $NUM_pages/15; //Total Pages
		 $CURRENT_PAGE = $UPPER_LIMIT/15;
		echo $CURRENT_PAGE;
		  ?> of <?php  echo ceil($TOTALPAGES); ?>
            </td>
        </tr>

    <tr class="header">
    	<td>No.</td>
        <td>Page Title</td>
        <td>Link ID</td>
        <td>Last Edited</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
        
        
<?php
//All work is now done to display pages using pagination.  However, we need to get the Home Page to always be the first page.
$HOMEPAGE_QUERY = "SELECT `page_id` FROM `page` WHERE `page_id` = '1'";
$RESULT_homepage = mysql_query($HOMEPAGE_QUERY) or die(mysql_error());
$HOMEPAGEINFO = mysql_fetch_array($RESULT_homepage);
//Display Hompeage Line only if this is the first page

    if ($LOWER_LIMIT == 0) { ?>
<tr>
	<td>1.</td>
        <td><?php  getPageTitle($HOMEPAGEINFO['page_id']); ?></td>
        <td>?page=<?php echo $HOMEPAGEINFO['page_id']; ?></td>
        <td><?php  getPageEditedTimeSummary($HOMEPAGEINFO['page_id']); ?></td>
        <td><a href="<?php getSiteLoc(); ?>/?page=<?php echo $HOMEPAGEINFO['page_id']; ?>" target="_blank" title="View this page"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/web-page-blue.gif" alt="View this page" border="0" /></a></td>
        <td><a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_PAGES; ?>&function=<?php echo $FUNCTION_EDIT_PAGE; ?>&pageid=<?php echo $HOMEPAGEINFO['page_id']; ?>" title="Edit this page"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/edit-page-blue.gif" alt="Edit this page" border="0" /></a></td>
        <td><a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_PAGES; ?>&function=<?php echo $FUNCTION_DELETE_PAGE; ?>&pageid=<?php echo $HOMEPAGEINFO['page_id']; ?>" title="Delete this page"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/delete-page-blue.gif" alt="Delete this page" border="0" /></a></td>
	</tr>
	
	<?php }
//Homepage line ends

//Now display all other pages

for ($i=1; $i<$NUM_pages1; $i++) {
$PAGEINFO = mysql_fetch_array($RESULT_pages1);
if ($COLOR == "1") {
?>
	<tr class="gray"> 
		<td><?php echo $i+$LOWER_LIMIT+1; ?>.</td>
        <td><?php getPageTitle($PAGEINFO['page_id']); ?></td>
        <td>?page=<?php echo $PAGEINFO['page_id']; ?></td>
        <td><?php getPageEditedTimeSummary($PAGEINFO['page_id']); ?></td>
        <td><a href="<?php getSiteLoc(); ?>/?page=<?php echo $PAGEINFO['page_id']; ?>" target="_blank" title="View this page"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/web-page-blue.gif" alt="View this page" border="0" /></a></td>
        <td><a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_PAGES; ?>&function=<?php echo $FUNCTION_EDIT_PAGE; ?>&pageid=<?php echo $PAGEINFO['page_id']; ?>" title="Edit this page"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/edit-page-blue.gif" alt="Edit this page" border="0" /></a></td>
        <td><a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_PAGES; ?>&function=<?php echo $FUNCTION_DELETE_PAGE; ?>&pageid=<?php echo $PAGEINFO['page_id']; ?>" title="Delete this page"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/delete-page-blue.gif" alt="Delete this page" border="0" /></a></td>
	</tr>
<?php 
$COLOR = "2";
}
else {
?>
	<tr>
		<td><?php echo $i+$LOWER_LIMIT+1; ?>.</td>
        <td><?php  getPageTitle($PAGEINFO['page_id']); ?></td>
        <td>?page=<?php echo $PAGEINFO['page_id']; ?></td>
        <td><?php  getPageEditedTimeSummary($PAGEINFO['page_id']); ?></td>
        <td><a href="<?php getSiteLoc(); ?>/?page=<?php echo $PAGEINFO['page_id']; ?>" target="_blank" title="View this page"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/web-page-blue.gif" alt="View this page" border="0" /></a></td>
        <td><a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_PAGES; ?>&function=<?php echo $FUNCTION_EDIT_PAGE; ?>&pageid=<?php echo $PAGEINFO['page_id']; ?>" title="Edit this page"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/edit-page-blue.gif" alt="Edit this page" border="0" /></a></td>
        <td><a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_PAGES; ?>&function=<?php echo $FUNCTION_DELETE_PAGE; ?>&pageid=<?php echo $PAGEINFO['page_id']; ?>" title="Delete this page"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/delete-page-blue.gif" alt="Delete this page" border="0" /></a></td>
	</tr>
<?php 
$COLOR = "1";
		}
					}
?>

           <tr>
            <td colspan="7" style="text-align: right; font-size: 80%;">
                <?php
		if ($LOWER_LIMIT >= 1) {
		?>

                  <a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_PAGES; ?>&LOWER_LIMIT=<?php echo $LOWER_LIMIT-15; ?>&UPPER_LIMIT=<?php echo $UPPER_LIMIT-15; ?>" /> << Previous </a>
                    &nbsp; &nbsp;
                 <?php }


                 if  ($NUM_pages2 >= 1) { ?>


                 <a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_PAGES; ?>&LOWER_LIMIT=<?php echo $LOWER_LIMIT+15; ?>&UPPER_LIMIT=<?php echo $UPPER_LIMIT+15; ?>" /> Next >> </a>
        <?php
				}

		?>

        </td>
        </tr>


</table>