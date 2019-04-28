<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Please fill in the following form to add a  news item and press the "Submit" button.  If you're unclear about what a certain section does, please click on the <img src="<?php echo getSiteLoc();?>/CMS/icons/small/icon_info.gif" alt="VAFTA CMS Knowledge Base" /> icon to view more information about the specific section in the VAFTA CMS knowledge base. </span>
<br />
<br />
<br />
<br />
<form name="add-news" action="processors/add-news.php" enctype="multipart/form-data" method="post">
<table width="600" cellpadding="3">
	<tr>
    	<td width="600" class="form" align="left">
        <b>News Title</b><br />
        <span class="formcomment">(Please enter a title for your news item)</span>
        <br />
        <input type="text" name="newstitle" size="55" />
        </td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td width="600"  align="left" class="form">
       <b>News Content</b> <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=10&id=34&artlang=en'); ?>
       <br />
       <span class="formcomment">(Please enter your news here)</span>
        </td>
    </tr>
    <tr>
    	<td width="600" align="left" colspan="2" class="form">
        	<?php
			$oFCKeditor = new FCKeditor('newscontent') ;
			$oFCKeditor->BasePath = '../_includes/fckeditor/' ;
			$oFCKeditor->Value = 'Enter your news here...' ;
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
        <input type="submit" value="Submit" />
        </td>
    </tr> 
</table>
</form>