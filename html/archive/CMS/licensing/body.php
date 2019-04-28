<body class="vafta">

	<div id="container"> <!--Start Container !-->
    
	<?php include ('../body_header.php'); ?>


<!-- Leave this in here !-->
        <div id="maintop">
        <img src="<?php getSiteLoc(); ?>/CMS/images/VAFTA_top.gif" alt="VAFTA CMS">
        </div>
<!-- Leave this in here Ends !-->
        
        
        
        <div id="main">        
        
			<?php include('../bodyparts/trail.php'); ?>
			<?php include('../bodyparts/error.php'); ?>

			<?php //Verifies is if the Section URL is correct. If not, redirected to CMS Home 
			SectionVerify($SECTION); 
			?>
       			<br />
        		<br />
			<?php include('../bodyparts/navigation.php'); //Menu ?>
            
            
            <?php include('../bodyparts/licensing.php'); //This is the main template for content ?>					
            
            
            <?php include('../bodyparts/clearer.php'); // to fix CSS bugs ?>

        </div>
    
    
    
<!-- Leave this in here !-->
        <div id="mainbottom">
        <img src="<?php getSiteLoc(); ?>/CMS/images/VAFTA_bottom.gif" alt="VAFTA CMS">
        </div>
<!-- Leave this in here Ends !-->        
	
			<?php include('../body_footer.php'); //Footer Divs ?>     
       
     </div> <!-- End Container div !-->

</body>
