<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> This section contains information about your VAFTA CMS License and website.  Please DO NOT change your Website URL unless the actual addres changes or your site may not function properly.  If you're unclear about what a certain section does, please click on the <img src="<?php echo getSiteLoc();?>/CMS/icons/small/icon_info.gif" alt="VAFTA CMS Knowledge Base" /> icon to view more information about the specific section in the VAFTA CMS knowledge base. </span>
<br />
<br />
<form name="edit-licensing" action="processors/edit-licensing.php" enctype="multipart/form-data" method="post">
<table width="600" cellpadding="2">
	<tr>
    	<td  class="formlabel">
    License No. <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=7&id=15&artlang=en'); ?>
    	</td>
    	<td class="form">
    	<?php getSiteInfo('license_number'); ?>
    	</td>
    </tr>
	<tr>
    	<td class="formlabel">
    Site Name <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=7&id=16&artlang=en'); ?>
    	</td>
    	<td>
    <input type="text" name="sitename" size="70" value="<?php getSiteInfo('site_name'); ?>" />
    	</td>
    </tr>
    <tr>
    	<td  class="formlabel">
    Site Tagline <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=7&id=17&artlang=en'); ?>
    	</td>
    	<td>
    <input type="text" name="tagline" size="70" value="<?php getSiteInfo('site_tagline'); ?>" />
    	</td>
    </tr>
    <tr>
    	<td  class="formlabel">
    Site URL / Address <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=7&id=18&artlang=en'); ?>
    	</td>
    	<td>
    <input type="text" name="url" size="70" value="<?php getSiteInfo('site_url'); ?>" />
    	</td>
    </tr>
    <tr>
    	<td colspan="2">
        <input type="submit" value="Confirm Changes" />
        </td>
    </tr>
</table>
</form>