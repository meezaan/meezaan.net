<?php
include ('_connections/db_connector.php');
include ('_includes/_functions/functions.php');
include ('_includes/_functions/stripslashes.php');
include ('_includes/constants.php');
include ('_includes/variables.php');
include_once('_includes/fckeditor/fckeditor.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Plum CMS by VAFTA | Website Content Management System from VAFTA Solutions</title>
<META NAME="TITLE"         CONTENT="Plum Content Management System (CMS) by VAFTA">
<META NAME="SUBTITLE"      CONTENT="Easy as Plum Website Editing and Content Management">
<META NAME="AUTHOR"        CONTENT="VAFTA Solutions">
<META NAME="WEBSITE"       CONTENT="VAFTA.COM">
<META NAME="KEYWORDS"      CONTENT="Content Management, Website Editing, CMS, Plum, VAFTA">
<META NAME="DESCRIPTION"   CONTENT="Plum CMS is a cost-effective website management and editing solution from VAFTA solutions.  It makes managing and editing websites easy as plum.">
<link href="<?php getSiteLoc(); ?>/CMS/vafta.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="favicon.ico" >
</head>
<body class="vafta">

	<div id="container"> <!--Start Container !-->
    
	<?php include('body_header.php'); //Header Divs ?>


<!-- Leave this in here !-->
        <div id="maintop">
        <img src="<?php getSiteLoc(); ?>/CMS/images/VAFTA_top.gif" alt="VAFTA CMS">
        </div>
<!-- Leave this in here Ends !-->
        
        
        
        <div id="main">
			
            <div id="trail">        
> <a href="<?php getSiteLoc(); ?>/CMS" class="traillink" title="CMS Home">CMS Home</a> > Login <?php if ($_REQUEST['inquiry'] == "forgotpassword" || $_REQUEST['inquiry'] == "resetpassword") { ?> > Reset Password <?php } ?>
			</div>
            
<?php include('bodyparts/error.php'); ?>
        
  		<br />
        <br />
        
			Welcome to the Content Management System for <span class="sitetitle"><?php getSiteName(); ?></span>.

        <br />
        <br />
        
        
<?php
if ($_REQUEST['inquiry'] == "") { 
include('bodyparts/login-form.php');
} 

if ($_REQUEST['inquiry'] == "forgotpassword") { 
include('bodyparts/password-reset-form.php');
}

if ($_REQUEST['inquiry'] == "resetpassword") { 
include('bodyparts/password-reset-form-2.php');
}  

?>  
                      
        </div>
    
    
    
<!-- Leave this in here !-->
        <div id="mainbottom">
        <img src="<?php getSiteLoc(); ?>/CMS/images/VAFTA_bottom.gif" alt="VAFTA CMS">
        </div>
<!-- Leave this in here Ends !-->        
	
	<?php include('body_footer.php'); //Footer Divs ?>     
       
     </div> <!-- End Container div !-->

</body>

</html>