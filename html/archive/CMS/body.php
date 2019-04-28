<body class="vafta">

	<div id="container"> <!--Start Container !-->
    
	<?php include('body_header.php'); //Header Divs ?>


<!-- Leave this in here !-->
        <div id="maintop">
        <img src="<?php getSiteLoc(); ?>/CMS/images/VAFTA_top.gif" alt="VAFTA CMS">
        </div>
<!-- Leave this in here Ends !-->
        
        
        
        <div id="main">
        
<?php include('bodyparts/trail.php'); 
	  include('bodyparts/error.php'); ?>
        
  		<br />
        <br />
        
			Welcome to the Content Management System for <span class="sitetitle"><?php getSiteName(); ?></span>
        
        <br />
        <br />
        
<?php include('bodyparts/table-menu.php'); ?>  
                      
        </div>
    
    
    
<!-- Leave this in here !-->
        <div id="mainbottom">
        <img src="<?php getSiteLoc(); ?>/CMS/images/VAFTA_bottom.gif" alt="VAFTA CMS">
        </div>
<!-- Leave this in here Ends !-->        
	
	<?php include('body_footer.php'); //Footer Divs ?>     
       
     </div> <!-- End Container div !-->

</body>
