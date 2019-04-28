<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Please make any changes to the Sidebar and press "Confirm Changes".  If you're unclear about what a certain section does, please click on the <img src="<?php echo getSiteLoc();?>/CMS/icons/small/icon_info.gif" alt="VAFTA CMS Knowledge Base" /> icon to view more information about the specific section in the VAFTA CMS knowledge base.</span>
<br />
<br />
<?php
$SIDEBARID =$_REQUEST['sidebarid'];
?>
<span class="formcomment">This sidebar was created by <?php getSidebarCreator($SIDEBARID); ?> on  <?php getSidebarCreatedTime($SIDEBARID); ?>.  It was last edited by <?php getSidebarEditor($SIDEBARID); ?> on <?php getSidebarEditedTime($SIDEBARID); ?>.</span>
<br />
<br />
<form name="edit-a-sidebar" action="processors/edit-a-sidebar.php" enctype="multipart/form-data" method="post">
<input name = "sidebarid" type="hidden" value="<?php echo $SIDEBARID; ?>" />
<table width="600" cellpadding="3">
	<tr>
    	<td width="600" class="form" align="left">
        Sidebar Name (Required) <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=5&id=8&artlang=en'); ?> 
        <br />
        <span class="formcomment">(This is for your information so you can keep track of the sidebars)</span>
        </td>
    </tr>
    <tr>
    	<td width="600" align="left" class="form">
        <input name="sidebarname" type="text" size = "50" value="<?php getSidebarName($SIDEBARID); ?>"/>
    	</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
	<tr>
    	<td width="600" align="left" class="form">
       	Sidebar Header (Required) <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=5&id=9&artlang=en'); ?>
         <br />
         <span class="formcomment">(This appears on the top of the sidebar as the heading)</span>
        </td>
    </tr>
    	<td width="600" align="left" class="form">
        <input name="sidebarheader" type="text" size = "50" value="<?php getSidebarHeader($SIDEBARID); ?>" />
    	</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td width="600"  align="left" class="form">
       Sidebar Content (Required) <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=5&id=10&artlang=en'); ?>
       <br />
       <span class="formcomment">(The content of your sidebar will appear in your site design / template exactly as it appears below.  Make use of the MS-Word type interface to edit the page)</span>
        </td>
    </tr>
    <tr>
    	<td width="600" align="left" colspan="2" class="form">
        	<?php
			$oFCKeditor = new FCKeditor('sidebarcontent') ;
			$oFCKeditor->BasePath = '../_includes/fckeditor/' ;
			$oFCKeditor->Value = getSidebarContent($SIDEBARID);
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
        <input type="submit" value="Confirm Changes" />
        </td>
    </tr> 
</table>
</form>