<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Here you can integrate google analytics into your website.  To do so, simply log-in to your google analytics account (or create one if you don't already have one), add your website to google analytics, and paste the code that Google generates for you in the field below. </span>
<br />
<br />

Please paste your Google Analytics tracking code below <?php getKBRef('http://www.vafta.com/cms/knowledgebase/index.php?action=artikel&cat=2&id=33&artlang=en'); ?>:
<br /><br />
<form name="google-analytics" action="processors/google-analytics.php" enctype="multipart/form-data" method="post">
<table width="600" cellpadding="3">
	<tr>
    <td width="600" colspan="2">
    <textarea name="tracking-code" cols="40" rows="12"><?php getSiteInfo('google_analytics'); ?></textarea>
    </td>
    </tr>
	<tr>
    	<td width="300">
       <input type="submit" value="Update" />
        </td>
        <td width="300">
       <input type="button" onclick="window.location='../website/?section=Website Management';" value="Cancel"/>
        </td>
    </tr> 
</table>
</form>