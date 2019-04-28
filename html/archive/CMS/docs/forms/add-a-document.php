<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> To upload a document, please fill in the form below and press "Add Document" button.  Please click <img src="<?php echo getSiteLoc();?>/CMS/icons/small/icon_info.gif" alt="VAFTA CMS Knowledge Base" /> icon to view more information about the specific field / section in the VAFTA CMS knowledge base. </span>
<br />
<br />


<form name="add-a-document" action="processors/add-a-document.php" enctype="multipart/form-data" method="post">
<table width="600" cellpadding="3">
	<tr>
    	<td width="600" class="form" align="left">
        Please enter a name for the document (Required) <?php getKBRef('#'); ?> 
        <br />
        <span class="formcomment">(This name will be give to the uploaded document)</span>
        </td>
    </tr>
    <tr>
    	<td width="600" align="left" class="form">
        <input name="docname" type="text" size = "50" />
    	</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
	<tr>
    	<td width="600" align="left" class="form">
        Please enter a short description (Optional) <?php getKBRef('#'); ?>
         <br />
         <span class="formcomment">(This is for your reference to help you recall what the document is for)</span>
        </td>
    </tr>
    <tr>
    	<td width="600" align="left" colspan="2" class="form">
        	<?php
			$oFCKeditor = new FCKeditor('docdescription') ;
			$oFCKeditor->BasePath = '../_includes/fckeditor/' ;
			$oFCKeditor->Height= '200' ;
			$oFCKeditor->ToolbarSet = 'Basic';
			$oFCKeditor->Create() ;
			?>
    	</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td width="600" align="left" class="form">
        Please choose a document / file to upload (Required) <?php getKBRef('#'); ?>
        <br />
    	<span class="formcomment">(Size Limit: 5 MB.  You can upload almost any document except for executables and images.  Common file extensions are: .doc, .xls, .pdf, .xps, .accdb and others.  To see a comprehensive list of documents allowed, please visit this item in the knowledgebase)</span>
        </td>
    </tr> 
    <tr>
    	<td width="600" align="left" class="form">
      <input type="file" name="uploadeddoc" size="50" />
        </td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td width="600" align="center">
        <input type="submit" value="Add Document" />
        </td>
    </tr> 
</table>
</form>