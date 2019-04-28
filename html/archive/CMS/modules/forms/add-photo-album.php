<span class="formtitle"><img src="<?php echo getSiteLoc();?>/CMS/icons/small/comment_yellow.gif" /> Please fill in the following form to add an album and press the "Submit" button.  If you're unclear about what a certain section does, please click on the <img src="<?php echo getSiteLoc();?>/CMS/icons/small/icon_info.gif" alt="VAFTA CMS Knowledge Base" /> icon to view more information about the specific section in the Plum CMS knowledge base. </span>
<br />
<br />
<br />
<br />
<form name="add-photo-album" action="processors/add-photo-album.php" enctype="multipart/form-data" method="post">
<table width="600" cellpadding="3">
       <tr>
            <td class="form" width="300">
                 Album Name
            </td>
            <td class="form" width="300">
                <input type="text" size="20" name ="album_name" />
            </td>
       </tr>
        <tr>
            <td class="form" width="300">
                 Album Description
            </td>
            <td class="form" width="300">
                <input type="text" size="30" name ="album_desc" />
            </td>
       </tr>
        <tr>
            <td class="form" width="300">
                 Album Caption <?php getKBRef('http://www.vafta.com/cms/knowledgebase/'); ?>
            </td>
            <td class="form" width="300">
                <input type="text" size="30" name ="album_caption" />
            </td>
       </tr>
        <tr>
            <td class="form" width="300">
                 Album Photo <?php getKBRef('http://www.vafta.com/cms/knowledgebase/'); ?>
            </td>
            <td class="form" width="300">
                <input type="file" size="30" name ="album_img" />
            </td>
       </tr>
       <tr>
            <td align="center" width="600" colspan="2">
                <br />
            <input type="submit" value="Submit">
            </td>
       </tr>
</table>
</form>