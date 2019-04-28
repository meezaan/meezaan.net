<div id="menu">
	<?php 
	if ($SECTION == $SITE_MANAGEMENT_TRAIL) { //Site Management Menu
    	include ('../_includes/_menus/site_management.php');
  			}   
			
	if ($SECTION == $META_SEO_TRAIL) { //META Menu
    	include ('../_includes/_menus/meta.php');
  			}   

	if ($SECTION == $LICENSING_SITEINFO_TRAIL) { //Site Info Menu
    	include ('../_includes/_menus/licensing.php');
  			}  
	if ($SECTION == $CURRENTUSER_TRAIL) { //Current User Menu
    	include ('../_includes/_menus/curruser.php');
  			} 		 		
	if ($SECTION == $USERS_TRAIL) { //Users Menu
    	include ('../_includes/_menus/users.php');
  			} 	
	if ($SECTION == $MANAGE_DOCS_TRAIL) { //DocumentsMenu
    	include ('../_includes/_menus/docs.php');
  			} 
	if ($SECTION == $MANAGE_IMAGES_TRAIL) { //Images Menu
    	include ('../_includes/_menus/pics.php');
  			}	
	if ($SECTION == $MODULES_TRAIL) { //Modules  Menu
    	include ('../_includes/_menus/modules.php');
  			}	
  ?>
    
    <br /><br />
    <img src="<?php getSiteLoc(); ?>/CMS/icons/small/home-icon6.gif" alt="Back to CMS Home" border="0" /> <a href="<?php getSiteLoc(); ?>/CMS" title="Back to CMS Home">CMS Home</a>
</div>