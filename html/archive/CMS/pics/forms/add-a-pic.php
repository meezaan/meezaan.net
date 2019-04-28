<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> To upload a document, please fill in the form below and press "Add Document" button.  Please click <img src="<?php echo getSiteLoc();?>/CMS/icons/small/icon_info.gif" alt="VAFTA CMS Knowledge Base" /> icon to view more information about the specific field / section in the VAFTA CMS knowledge base. </span>
<br />
<br />


<form name="add-a-pic" action="processors/add-a-pic.php" enctype="multipart/form-data" method="post">
<table width="600" cellpadding="3">
	<tr>
    	<td width="600" class="form" align="left">
        Please enter a name for the Image (Required) <?php getKBRef('#'); ?> 
        <br />
        <span class="formcomment">(This name will be give to the uploaded document)</span>
        </td>
    </tr>
    <tr>
    	<td width="600" align="left" class="form">
        <input name="imgname" type="text" size = "50" />
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
			$oFCKeditor = new FCKeditor('imgdescription') ;
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
    	<td width="600" class="form" align="left">
        Please enter a caption for the Image (Required) <?php getKBRef('#'); ?> 
        <br />
        <span class="formcomment">(This name will set to the ALT tag of the image)</span>
        </td>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td class="form">If you would like Plum CMS to resize the image, please enter height and a width:</td>
    </tr>
    <tr>
    	<td class="form">
    	Width: <input type="text" name="width" size="3" /> pixels
        </td>
    </tr>
    <tr>
    	<td class="form">
    	Height: <input type="text" name="height" size="3" /> pixels
        </td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td width="600" align="left" class="form">
        Please choose an image to upload (Required) <?php getKBRef('#'); ?>
        <br />
    	<span class="formcomment">(Size Limit: 5 MB.  You can upload almost any document except for executables and images.  Common image extensions are: .jpg, .jpeg, .gif and .png.  To see a comprehensive list of documents allowed, please visit this item in the knowledgebase)</span>
        </td>
    </tr> 
    <tr>
    	<td width="600" align="left" class="form">
      <input type="file" name="uploadedimg" size="50" />
        </td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td width="600" align="center">
        <input type="submit" value="Add Image" />
        </td>
    </tr> 
</table>
</form>