<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> In this section you can add additional pages to your VAFTA CMS powered website.  To create a new page, please fill in all the sections below and press the "Create Page" button.  If you're unclear about what a certain section does, please click on the <img src="<?php echo getSiteLoc();?>/CMS/icons/small/icon_info.gif" alt="VAFTA CMS Knowledge Base" /> icon to view more information about the specific section in the VAFTA CMS knowledge base. </span>
<br />
<br />


<form name="add-a-page" action="processors/add-a-page.php" enctype="multipart/form-data" method="post">
<table width="600" cellpadding="3">
	<tr>
    	<td width="600" class="form" align="left">
        Please enter a page title (Required) <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=3&id=2&artlang=en'); ?> 
        <br />
        <span class="formcomment">(This appears in the titlebar of the window)</span>
        </td>
    </tr>
    <tr>
    	<td width="600" align="left" class="form">
        <input name="pagetitle" type="text" size = "50" />
    	</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
	<tr>
    	<td width="600" align="left" class="form">
        Please enter a page header (Required) <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=3&id=3&artlang=en'); ?>
         <br />
         <span class="formcomment">(This appears on the top of the page as the heading)</span>
        </td>
    </tr>
    	<td width="600" align="left" class="form">
        <input name="pageheader" type="text" size = "50" />
    	</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td width="600"  align="left" class="form">
       Please create your page here (Required) <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=3&id=4&artlang=en'); ?>
       <br />
       <span class="formcomment">(The content of your page will appear in your site design / template exactly as it appears below.  Make use of the MS-Word type interface to edit the page)</span>
        </td>
    </tr>
    <tr>
    	<td width="600" align="left" colspan="2" class="form">
        	<?php
			$oFCKeditor = new FCKeditor('pagecontent') ;
			$oFCKeditor->BasePath = '../_includes/fckeditor/' ;
			$oFCKeditor->Value = '<p>Please create your web page here</p>' ;
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
       <?php getSidebarChooser(); ?>
        </td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td width="600" align="center">
        <input type="submit" value="Create Page" />
        </td>
    </tr> 
</table>
</form>