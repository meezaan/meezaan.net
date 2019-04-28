<div id="trail">

<?php
//For additional trails, SECTION defines the first level and SECTFUNCTION defines the second level.  These are also used throughout the page to see what information will be shown
$SECTION = $_REQUEST['section'];
$SECTFUNCTION = $_REQUEST['sectfunction'];
$FUNCTION = $_REQUEST['function'];
?>

> <a href="<?php getSiteLoc(); ?>/CMS" class="traillink" title="Go to CMS Home">CMS Home</a>

<?php   //Main Website Management Area
if ($SECTION == $SITE_MANAGEMENT_TRAIL) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>" class="traillink" title="Go to Site Management"><?php echo $SITE_MANAGEMENT_TRAIL; ?></a>
<?php } ?>

<?php   //Main Meta and SEO Area
if ($SECTION == $META_SEO_TRAIL) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/meta/?section=<?php echo $META_SEO_TRAIL; ?>" class="traillink" title="Go to Site Meta and SEO"><?php echo $META_SEO_TRAIL; ?></a>
<?php } ?>

<?php   //Main Licensing & Site Info
if ($SECTION == $LICENSING_SITEINFO_TRAIL) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/licensing/?section=<?php echo $LICENSING_SITEINFO_TRAIL; ?>" class="traillink" title="Licensing & Site Info"><?php echo $LICENSING_SITEINFO_TRAIL; ?></a>
<?php } ?>

<?php   //Main Users Area
if ($SECTION == $USERS_TRAIL) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/users/?section=<?php echo $USERS_TRAIL; ?>" class="traillink" title="User Management"><?php echo $USERS_TRAIL; ?></a>
<?php } ?>

<?php   //Main Images Area
if ($SECTION == $MANAGE_IMAGES_TRAIL) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/users/?section=<?php echo $MANAGE_IMAGES_TRAIL; ?>" class="traillink" title="Manage Images"><?php echo $MANAGE_IMAGES_TRAIL; ?></a>
<?php } ?>

<?php   //Main Modules Area
if ($SECTION == $MODULES_TRAIL) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/modules/?section=<?php echo $MODULES_TRAIL; ?>" class="traillink" title="Module Management"><?php echo $MODULES_TRAIL; ?></a>
<?php } ?>

<?php  // News Module
if ($SECTFUNCTION == $SECTFUNCT_MODULE_NEWS) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/modules/?section=<?php echo $MODULES_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_MODULE_NEWS; ?>" class="traillink" title="News Module"><?php echo $SECTFUNCT_MODULE_NEWS; ?></a>
<?php } ?>

<?php  // Photo Gallery
if ($SECTFUNCTION == $SECTFUNCT_MODULE_PHOTOGALLERY) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/modules/?section=<?php echo $MODULES_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_MODULE_PHOTOGALLERY; ?>" class="traillink" title="Photo Gallery Module"><?php echo $SECTFUNCT_MODULE_PHOTOGALLERY; ?></a>
<?php } ?>

<?php  // Photo Gallery
if ($FUNCTION == $FUNCTION_ADD_PHOTO) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/modules/?section=<?php echo $MODULES_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_MODULE_PHOTOGALLERY; ?>&function=<?php echo $FUNCTION_ADD_PHOTO; ?>" class="traillink" title="Add Photo"><?php echo $FUNCTION_ADD_PHOTO; ?></a>
<?php } ?>

<?php  // Photo Gallery
if ($FUNCTION == $FUNCTION_EDIT_PHOTO) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/modules/?section=<?php echo $MODULES_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_MODULE_PHOTOGALLERY; ?>&function=<?php echo $FUNCTION_EDIT_PHOTO; ?>" class="traillink" title="Add Photo"><?php echo $FUNCTION_EDIT_PHOTO; ?></a>
<?php } ?>

<?php  // Photo Gallery
if ($FUNCTION == $FUNCTION_DELETE_PHOTO) { ?>
> <?php echo $FUNCTION_DELETE_PHOTO; ?></a>
<?php } ?>

<?php  // Photo Gallery
if ($FUNCTION == $FUNCTION_EDIT_PHOTOALBUM) { ?>
> <?php echo $FUNCTION_EDIT_PHOTOALBUM; ?></a>
<?php } ?>

<?php  // Photo Gallery
if ($FUNCTION == $FUNCTION_DELETE_PHOTOALBUM) { ?>
> <?php echo $FUNCTION_DELETE_PHOTOALBUM; ?></a>
<?php } ?>

<?php   //Edit user
if ($FUNCTION == $FUNCTION_EDIT_USER) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/users/?section=<?php echo $USERS_TRAIL; ?>&function=<?php echo $FUNCTION_EDIT_USER; ?>" class="traillink" title="User Management"><?php echo $FUNCTION_EDIT_USER; ?></a>
<?php } ?>

<?php   //Delete user
if ($FUNCTION == $FUNCTION_DELETE_USER) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/users/?section=<?php echo $USERS_TRAIL; ?>&function=<?php echo $FUNCTION_DELETE_USER; ?>" class="traillink" title="User Management"><?php echo $FUNCTION_DELETE_USER; ?></a>
<?php } ?>

<?php   //Add user
if ($FUNCTION == $FUNCTION_ADD_USER) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/users/?section=<?php echo $USERS_TRAIL; ?>&function=<?php echo $FUNCTION_ADD_USER; ?>" class="traillink" title="User Management"><?php echo $FUNCTION_ADD_USER; ?></a>
<?php } ?>

<?php   //Main Documents Area
if ($SECTION == $MANAGE_DOCS_TRAIL) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/docs/?section=<?php echo $MANAGE_DOCS_TRAIL; ?>" class="traillink" title="Document Management"><?php echo $MANAGE_DOCS_TRAIL; ?></a>
<?php } ?>

<?php   //Add a Document 
if ($FUNCTION == $FUNCTION_ADD_DOC) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/docs/?section=<?php echo $MANAGE_DOCS_TRAIL; ?>&function=<?php echo $FUNCTION_ADD_DOC; ?>" class="traillink" title="Add a Document"><?php echo $FUNCTION_ADD_DOC; ?></a>
<?php } ?>

<?php   //Add an image 
if ($FUNCTION == $FUNCTION_ADD_IMAGE) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/docs/?section=<?php echo $MANAGE_IMAGES_TRAIL; ?>&function=<?php echo $FUNCTION_ADD_IMAGE; ?>" class="traillink" title="Add a Document"><?php echo $FUNCTION_ADD_IMAGE; ?></a>
<?php } ?>

<?php   //Del an image 
if ($FUNCTION == $FUNCTION_DELETE_IMAGE) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/docs/?section=<?php echo $MANAGE_IMAGES_TRAIL; ?>&function=<?php echo $FUNCTION_DELETE_IMAGE; ?>" class="traillink" title="Add a Document"><?php echo $FUNCTION_DELETE_IMAGE; ?></a>
<?php } ?>


<?php   //Delete a Document 
if ($FUNCTION == $FUNCTION_DELETE_DOC) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/docs/?section=<?php echo $MANAGE_DOCS_TRAIL; ?>&function=<?php echo $FUNCTION_DELETE_DOC; ?>" class="traillink" title="Delete a Document"><?php echo $FUNCTION_DELETE_DOC; ?></a>
<?php } ?>


<?php   //Edit password etc. area for logged in user
if ($SECTION == $CURRENTUSER_TRAIL) { ?>
> <?php echo $CURRENTUSER_TRAIL; ?></a>
<?php } ?>

<?php  // Edit Password Page
if ($SECTFUNCTION == $SECTFUNCT_PWD) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/curruser/?section=<?php echo $CURRENTUSER_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_PWD; ?>" class="traillink" title="Change Password"><?php echo $SECTFUNCT_PWD; ?></a>
<?php } ?>

<?php  // Edit profile Page
if ($SECTFUNCTION == $SECTFUNCT_PROFILE) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/curruser/?section=<?php echo $CURRENTUSER_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_PROFILE; ?>" class="traillink" title="Change Password"><?php echo $SECTFUNCT_PROFILE; ?></a>
<?php } ?>


<?php  // Main Pages Area
if ($SECTFUNCTION == $SECTFUNCT_PAGES) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_PAGES; ?>" class="traillink" title="Pages"><?php echo $SECTFUNCT_PAGES; ?></a>
<?php } ?>

<?php //Main Static Areas Area 
if ($SECTFUNCTION == $SECTFUNCT_AREAS) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_AREAS; ?>" class="traillink" title="Static Areas"><?php echo $SECTFUNCT_AREAS; ?></a>
<?php } ?>

<?php  //Main Sidebars Area 
if ($SECTFUNCTION == $SECTFUNCT_SIDEBARS) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_SIDEBARS; ?>" class="traillink" title="Static Areas"><?php echo $SECTFUNCT_SIDEBARS; ?></a>
<?php } ?>

<?php  //Main Menus Area
if ($SECTFUNCTION == $SECTFUNCT_MENUS) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_MENUS; ?>" class="traillink" title="Static Areas"><?php echo $SECTFUNCT_MENUS; ?></a>
<?php } ?>

<?php // Add a Page
if ($FUNCTION == $FUNCTION_ADD_PAGE) { ?>
> <?php echo $FUNCTION_ADD_PAGE; ?>
<?php } ?>

<?php // Edit a Page
if ($FUNCTION == $FUNCTION_EDIT_PAGE) { ?>
> <?php echo $FUNCTION_EDIT_PAGE; ?>
<?php } ?>

<?php // Delete a Page
if ($FUNCTION == $FUNCTION_DELETE_PAGE) { ?>
> <?php echo $FUNCTION_DELETE_PAGE; ?>
<?php } ?>

<?php // Edit an Area
if ($FUNCTION == $FUNCTION_EDIT_AREA) { ?>
> <?php echo $FUNCTION_EDIT_AREA; ?>
<?php } ?>

<?php // Add a Sidebar
if ($FUNCTION == $FUNCTION_ADD_SIDEBAR) { ?>
> <?php echo $FUNCTION_ADD_SIDEBAR; ?>
<?php } ?>

<?php // Edit a Sidebar
if ($FUNCTION == $FUNCTION_EDIT_SIDEBAR) { ?>
> <?php echo $FUNCTION_EDIT_SIDEBAR; ?>
<?php } ?>

<?php // Delete a Sidebar
if ($FUNCTION == $FUNCTION_DELETE_SIDEBAR) { ?>
> <?php echo $FUNCTION_DELETE_SIDEBAR; ?>
<?php } ?>

<?php // Edit a Menu
if ($FUNCTION == $FUNCTION_EDIT_MENU) { ?>
> <?php echo $FUNCTION_EDIT_MENU; ?>
<?php } ?>

<?php  // Main Google Analytics Integration Area
if ($SECTFUNCTION == $SECTFUNCT_ANALYTICS) { ?>
> <a href="<?php getSiteLoc(); ?>/CMS/website/?section=<?php echo $SITE_MANAGEMENT_TRAIL; ?>&sectfunction=<?php echo $SECTFUNCT_ANALYTICS; ?>" class="traillink" title="Google Analytics Integration"><?php echo $SECTFUNCT_ANALYTICS; ?></a>
<?php } ?>


</div>