<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> In this section you can the meta characters of your VAFTA CMS powered website.  Meta Characters play a very important part in how search engines (Google, Yahoo!, MSN etc.) view your website.  Please make changes to the section where applicable and press the "Confirm Changes" button.  If you're unclear about what a certain section does, please click on the <img src="<?php echo getSiteLoc();?>/CMS/icons/small/icon_info.gif" alt="VAFTA CMS Knowledge Base" /> icon to view more information about the specific section in the VAFTA CMS knowledge base. </span>
<br />
<br />
<form name="edit-meta" action="processors/edit-meta.php" enctype="multipart/form-data" method="post">
<table width="600" cellpadding="2">
	<tr>
    	<td  class="formlabel">
    Meta Title <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=6&id=11&artlang=en'); ?>
    	</td>
    	<td>
    <input type="text" name="metatitle" size="70" value="<?php getMeta('meta_title'); ?>" />
    	</td>
    </tr>
	<tr>
    	<td  class="formlabel">
    Meta Subtitle <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=6&id=12&artlang=en'); ?>
    	</td>
    	<td>
    <input type="text" name="metasubtitle" size="70" value="<?php getMeta('meta_subtitle'); ?>" />
    	</td>
    </tr>
    <tr>
    	<td  class="formlabel">
    Meta Keywords <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=6&id=13&artlang=en'); ?>
    	</td>
    	<td>
    <input type="text" name="metakeywords" size="70" value="<?php getMeta('meta_keywords'); ?>" />
    	</td>
    </tr>
    <tr>
    	<td  class="formlabel">
    Meta Description <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=6&id=14&artlang=en'); ?>
    	</td>
    	<td>
    <textarea name="metadesc" rows="8" cols="53" /><?php getMeta('meta_description'); ?></textarea>
    	</td>
    </tr>
    <tr>
    	<td colspan="2">
        <input type="submit" value="Confirm Changes" />
        </td>
    </tr>
</table>
</form>