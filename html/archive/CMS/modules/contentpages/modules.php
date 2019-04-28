<span class="formtitle"><img src="<?php echo getSiteLoc(); ?>/CMS/icons/small/comment_yellow.gif" /> The following modules are currently available for use with Plum CMS.  As new modules are released, they will become available here for you to activate and integrate into your website.</span>
<br />
<br />

<table width="600" border="0" cellspacing="0" cellpadding="4">
<?php
$COLOR = "1";  // For alternating <tr> colours
$RESULT_mods = mysql_query("SELECT * FROM `modules`") or die(mysql_error());
$NUM_mods = mysql_num_rows($RESULT_mods);
for ($i=0; $i<$NUM_mods; $i++) {
$MODINFO = mysql_fetch_array($RESULT_mods);
if ($COLOR == "1") {
?>
	<tr class="gray"> 
      	<td class="form" colspan="2"><b>&raquo; <?php getModInfo($MODINFO['mod_id'], 'mod_name'); ?></b></td>
  	</tr>
    <tr class="gray"> 
    	<td class="form" colspan="2"><?php getModInfo($MODINFO['mod_id'], 'mod_description'); ?></td>
    </tr>
  	<tr class="gray"> 
    	<td width="300" class="form">This module is currently
		<?php
        if ($MODINFO['mod_status_id'] == "1") { ?>
        <b>Enabled</b>.
        <p>
        <i>&raquo; <a href="<?php getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=<?php getModInfo($MODINFO['mod_id'], 'mod_url_tag'); ?>"><b>Start Using it!</b></a></i>
        </p>
		<?php
		} 
		elseif  ($MODINFO['mod_status_id'] == "2") { ?>
        <b>Disabled</b>.
        <?php
		}
		?>
        </td>
        
        <td width="300">
		<?php
        if ($MODINFO['mod_status_id'] == "1") { ?>
        <a href="<?php getSiteLoc(); ?>/CMS/modules/processors/disable-mod.php?module=<?php echo $MODINFO['mod_id']; ?>"><b>&raquo; Disable it</b></a>
        <?php
		} 
		elseif  ($MODINFO['mod_status_id'] == "2") { ?>
        <a href="<?php getSiteLoc(); ?>/CMS/modules/processors/enable-mod.php?module=<?php echo $MODINFO['mod_id']; ?>"><b>&raquo; Enable it!</b></a>
        <?php
		}
		?>
        </td>    
    </tr>
    
<?php 
$COLOR = "2";
}
else {
?>
<tr> 
      	<td colspan="2" class="displayleft"><b>&raquo; <?php getModInfo($MODINFO['mod_id'], 'mod_name'); ?></b></td>
  	</tr>
    <tr> 
    	<td class="form" colspan="2"><?php getModInfo($MODINFO['mod_id'], 'mod_description'); ?></td>
    </tr>
  	<tr> 
    	<td width="300" class="form">This module is currently
		<?php
        if ($MODINFO['mod_status_id'] == "1") { ?>
        <b>Enabled</b>.
        <p>
        <i>&raquo; <a href="<?php getSiteLoc(); ?>/CMS/modules/?section=Modules&sectfunction=<?php getModInfo($MODINFO['mod_id'], 'mod_url_tag'); ?>"><b>Start Using it!</b></a></i>
        </p>
		<?php
		} 
		elseif  ($MODINFO['mod_status_id'] == "2") { ?>
        <b>Disabled</b>.
        <?php
		}
		?>
        </td>
        
        <td width="300">
		<?php
        if ($MODINFO['mod_status_id'] == "1") { ?>
        <a href="<?php getSiteLoc(); ?>/CMS/modules/processors/disable-mod.php?module=<?php echo $MODINFO['mod_id']; ?>"><b>&raquo; Disable it</b></a>
        <?php
		} 
		elseif  ($MODINFO['mod_status_id'] == "2") { ?>
        <a href="<?php getSiteLoc(); ?>/CMS/modules/processors/enable-mod.php?module=<?php echo $MODINFO['mod_id']; ?>"><b>&raquo; Enable it!</b></a>
        <?php
		}
		?>
        </td>
        
    </tr>


<?php 
$COLOR = "1";
			}

					}
?>
</table>