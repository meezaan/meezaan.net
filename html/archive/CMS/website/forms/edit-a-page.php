<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Please make any changes to the page and press the "Confirm Changes" button.  If you're unclear about what a certain section does, please click on the <img src="<?php echo getSiteLoc();?>/CMS/icons/small/icon_info.gif" alt="VAFTA CMS Knowledge Base" /> icon to view more information about the specific section in the VAFTA CMS knowledge base. </span>
<br />
<br />
<?php
//Get variable passed
$PAGE_ID = $_REQUEST['pageid'];
?>
 <span class="formcomment">This page was created by <?php getPageCreator($PAGE_ID); ?> on  <?php getPageCreatedTime($PAGE_ID); ?>.  It was last edited by <?php getPageEditor($PAGE_ID); ?> on <?php getPageEditedTime($PAGE_ID); ?>.</span>
<br /><br />
<form name="edit-a-page" action="processors/edit-a-page.php" enctype="multipart/form-data" method="post">
<input type="hidden" name="pageid" value="<?php echo $PAGE_ID; ?>" />
<table width="600" cellpadding="3">
	<tr>
    	<td width="600" class="form" align="left">
        Page Title <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=3&id=2&artlang=en'); ?> 
        <br />
        <span class="formcomment">(This appears in the titlebar of the window)</span>
        </td>
    </tr>
    <tr>
    	<td width="600" align="left" class="form">
        <?php if ($PAGE_ID == "1") { //if home page, don't allow title change
		getPageTitle($PAGE_ID); ?>
        <input name="pagetitle" type="hidden" size = "50" value="<?php getPageTitle($PAGE_ID); ?>" />
		<?php }
		else { ?>
        <input name="pagetitle" type="text" size = "50" value="<?php getPageTitle($PAGE_ID); ?>" />
    	<?php } ?>
        </td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
	<tr>
    	<td width="600" align="left" class="form">
        Page Header<?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=3&id=3&artlang=en'); ?>
         <br />
         <span class="formcomment">(This appears on the top of the page as the heading)</span>
        </td>
    </tr>
    	<td width="600" align="left" class="form">
        <input name="pageheader" type="text" size = "50" value="<?php getPageHeader($PAGE_ID); ?>" />
    	</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td width="600"  align="left" class="form">
       Page Content <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=3&id=4&artlang=en'); ?>
       <br />
       <span class="formcomment">(The content of your page will appear in your site design / template exactly as it appears below.  Please make use of the MS-Word type interface to edit the page)</span>
        </td>
    </tr>
    <tr>
    	<td width="600" align="left" colspan="2" class="form">
        	<?php
			$oFCKeditor = new FCKeditor('pagecontent') ;
			$oFCKeditor->BasePath = '../_includes/fckeditor/' ;
			$oFCKeditor->Value = getPageContent($PAGE_ID) ;
			$oFCKeditor->Height= '400' ;
			$oFCKeditor->ToolbarSet = 'Vafta';
			$oFCKeditor->Create() ;
			?>
    	</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td width="600" align="left" class="form">
        Please choose a sidebar for this page (Optional) <?php getKBRef($SECTION,4); ?>
        <br />
    	<span class="formcomment">(Choose a sidebar to associate with this page)</span>
        </td>
    </tr> 
    <tr>
    	<td width="600" align="left" class="form">
       <?php getSidebarChooserEdit($PAGE_ID); ?>
        </td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td width="600" align="center">
        <input type="submit" value="Confirm Changes" />
        </td>
    </tr> 
</table>
</form>