<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Please make any changes to the news item and press the "Confirm Changes" button.  If you're unclear about what a certain section does, please click on the <img src="<?php echo getSiteLoc();?>/CMS/icons/small/icon_info.gif" alt="VAFTA CMS Knowledge Base" /> icon to view more information about the specific section in the VAFTA CMS knowledge base. </span>
<br />
<br />
<?php
//Get variable passed
$NEWS_ID = $_REQUEST['newsitem'];
checkEmptyVariable($NEWS_ID);
?>
<br />
<br />
<form name="edit-news" action="processors/edit-news.php" enctype="multipart/form-data" method="post">
<input type="hidden" name="newsitem" value="<?php echo $NEWS_ID; ?>" />
<table width="600" cellpadding="3">
	<tr>
    	<td width="600" class="form" align="left">
        <b>News Title</b><br />
        <span class="formcomment">(Please enter a title for your news item)</span>
        <br />
        <input type="text" name="newstitle" size="55" value="<?php getNewsInfo($NEWS_ID, 'news_title'); ?>" />
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
			$oFCKeditor->Value = getNewsInfoReturn($NEWS_ID, 'news_content') ;
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