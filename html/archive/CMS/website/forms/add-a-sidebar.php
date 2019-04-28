<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> In this section you can create sidebars to your VAFTA CMS powered website.  To create a new sidebar, please fill in all the sections below and press the "Create Sidebar" button.  If you're unclear about what a certain section does, please click on the <img src="<?php echo getSiteLoc();?>/CMS/icons/small/icon_info.gif" alt="VAFTA CMS Knowledge Base" /> icon to view more information about the specific section in the VAFTA CMS knowledge base. To add a sidebar to a page, go to the the pages section, choose to edit a page, and choose a sidebar to associate with a specific page.</span>
<br />
<br />


<form name="add-a-sidebar" action="processors/add-a-sidebar.php" enctype="multipart/form-data" method="post">
<table width="600" cellpadding="3">
	<tr>
    	<td width="600" class="form" align="left">
        Please enter a sidebar name (Required) <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=5&id=8&artlang=en'); ?> 
        <br />
        <span class="formcomment">(This is for your information so you can keep track of the sidebars)</span>
        </td>
    </tr>
    <tr>
    	<td width="600" align="left" class="form">
        <input name="sidebarname" type="text" size = "50" />
    	</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
	<tr>
    	<td width="600" align="left" class="form">
        Please enter a sidebar header (Required) <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=5&id=9&artlang=en'); ?>
         <br />
         <span class="formcomment">(This appears on the top of the sidebar as the heading)</span>
        </td>
    </tr>
    	<td width="600" align="left" class="form">
        <input name="sidebarheader" type="text" size = "50" />
    	</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td width="600"  align="left" class="form">
       Please create your sidebar here (Required) <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=5&id=10&artlang=en'); ?>
       <br />
       <span class="formcomment">(The content of your sidebar will appear in your site design / template exactly as it appears below.  Make use of the MS-Word type interface to edit the page)</span>
        </td>
    </tr>
    <tr>
    	<td width="600" align="left" colspan="2" class="form">
        	<?php
			$oFCKeditor = new FCKeditor('sidebarcontent') ;
			$oFCKeditor->BasePath = '../_includes/fckeditor/' ;
			$oFCKeditor->Value = '<p>Please create your sidebar here</p>' ;
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
    	<td width="600" align="center">
        <input type="submit" value="Create Sidebar" />
        </td>
    </tr> 
</table>
</form>