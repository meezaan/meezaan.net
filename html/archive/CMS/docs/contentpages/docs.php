<span class="formtitle"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/comment_yellow.gif" /> This sections lists all the documents that have been uploaded and are available for use on the website. Here you can add new documents, see the direct URL to an existing document, or delete documents.</span>
<br />
<br />
<span class="pagesubmenu"><a href="<?php echo getSiteLoc(); ?>/CMS/docs/?section=Manage Documents&function=Add a Document" title="Add a Document"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/add-document.gif" alt="Add a Document" border="0" /> Add a Document</a></span>
<br />
<br />
<table width="600" border="0" cellspacing="0" cellpadding="4">
<?php
$COLOR = "1";  // For alternating <tr> colours
$RESULT_docs= mysql_query("SELECT `doc_id` FROM `documents`") or die(mysql_error());
$NUM_docs = mysql_num_rows($RESULT_docs);
for ($i=0; $i<$NUM_docs; $i++) {
$DOCINFO = mysql_fetch_array($RESULT_docs);
if ($COLOR == "1") {
?>
	<tr class="gray"> 
		<td width="50"><?php echo $i+1; ?>.</td>
      <td class="form"><?php getDocInfo($DOCINFO['doc_id'], 'doc_name'); ?></td>
      <td class="form"><?php  getDocInfo($DOCINFO['doc_id'], 'doc_size'); ?> KB</td>
      <td class="form">
        	<a href="<?php getSiteLoc(); ?>/CMS/docs/?section=<?php echo $MANAGE_DOCS_TRAIL; ?>&function=<?php echo $FUNCTION_DELETE_DOC; ?>&docid=<?php echo $DOCINFO['doc_id']; ?>" title="Delete this Document"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/delete-doc.gif" alt="Delete this Document" border="0" /></a>      </td>
  </tr>
    <tr class="gray">
    	<td class="form">
        <b>Direct Link:</b> 
        </td>
        <td colspan="3" class="form"><a href="<?php getSiteLoc(); ?>/CMS/uploadedfiles/docs/<?php getDocInfo($DOCINFO['doc_id'], 'doc_loc'); ?>"><?php getSiteLoc(); ?>/CMS/uploadedfiles/docs/<?php getDocInfo($DOCINFO['doc_id'], 'doc_loc'); ?></a></td>
    </tr>
    <tr class="gray">
    	<td class="form">
        <b>Description:</b> 
        </td>
        <td colspan="3" class="form"><?php getDocInfo($DOCINFO['doc_id'], 'doc_desc'); ?></td>
    </tr>

<?php 
$COLOR = "2";
}
else {
?>
	<tr> 
		<td width="50"><?php echo $i+1; ?>.</td>
      <td class="form"><?php getDocInfo($DOCINFO['doc_id'], 'doc_name'); ?></td>
      <td class="form"><?php  getDocInfo($DOCINFO['doc_id'], 'doc_size'); ?> KB</td>
      <td class="form">
        	<a href="<?php getSiteLoc(); ?>/CMS/docs/?section=<?php echo $MANAGE_DOCS_TRAIL; ?>&function=<?php echo $FUNCTION_DELETE_DOC; ?>&docid=<?php echo $DOCINFO['doc_id']; ?>" title="Delete this Document"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/delete-doc.gif" alt="Delete this Document" border="0" /></a>      </td>
  </tr>
    <tr>
    	<td class="form">
        <b>Direct Link:</b> 
        </td>
        <td colspan="3" class="form"><a href="<?php getSiteLoc(); ?>/CMS/uploadedfiles/docs/<?php getDocInfo($DOCINFO['doc_id'], 'doc_loc'); ?>"><?php getSiteLoc(); ?>/CMS/uploadedfiles/docs/<?php getDocInfo($DOCINFO['doc_id'], 'doc_loc'); ?></a></td>
    </tr>
    <tr>
    	<td class="form">
        <b>Description:</b>
        </td>
        <td colspan="3" class="form"><?php getDocInfo($DOCINFO['doc_id'], 'doc_desc'); ?></td>
    </tr>

<?php 
$COLOR = "1";
			}

					}
?>
</table>