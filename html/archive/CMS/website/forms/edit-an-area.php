<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Please make any changes to the area and press the "Confirm Changes" button.  If you're unclear about what a certain section does, please click on the <img src="<?php echo getSiteLoc();?>/CMS/icons/small/icon_info.gif" alt="VAFTA CMS Knowledge Base" /> icon to view more information about the specific section in the VAFTA CMS knowledge base. </span>
<br />
<br />
<?php
//Get variable passed
$AREA_ID = $_REQUEST['areaid'];
?>
 <span class="formcomment"><?php getAreaDesc($AREA_ID); ?></span>
<br /><br />
<form name="edit-an-area" action="processors/edit-an-area.php" enctype="multipart/form-data" method="post">
<input type="hidden" name="areaid" value="<?php echo $AREA_ID; ?>" />
<table width="600" cellpadding="3">
	<tr>
    	<td width="600" class="form" align="left">
        Static Area:  <span class="formtitle"><?php getAreaName($AREA_ID); ?></span>
        </td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td width="600"  align="left" class="form">
       Edit Static Area <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=4&id=6&artlang=en'); ?>
       <br />
       <span class="formcomment">(Please enter the content of the static area)</span>
        </td>
    </tr>
    <tr>
    	<td width="600" align="left" colspan="2" class="form">
        	<?php
			$oFCKeditor = new FCKeditor('areacontent') ;
			$oFCKeditor->BasePath = '../_includes/fckeditor/' ;
			$oFCKeditor->Value = getAreaContent($AREA_ID) ;
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