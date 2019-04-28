<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$storesessionvalue=="") $storesessionvalue="virtualstore".time();
if($_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE) exit;
$success=TRUE;
$sSQL = "SELECT adminUser, adminPassword FROM admin WHERE adminID=1";
$result = mysql_query($sSQL) or print(mysql_error());
$rs = mysql_fetch_assoc($result);
$adminUser = $rs["adminUser"];
$adminPassword = $rs["adminPassword"];
mysql_free_result($result);
if(@$_POST["posted"]=="1"){
	if(trim(@$_POST["pass"]) != trim(@$_POST["pass2"])){
		$success = FALSE;
		$errmsg=$yyNoMat;
	}else{
		$sSQL = "UPDATE admin SET adminUser='" . mysql_escape_string(@$_POST["user"]) . "',adminPassword='" . mysql_escape_string(@$_POST["pass"]) . "' WHERE adminID=1";
		mysql_query($sSQL) or print(mysql_error());
		print "<meta http-equiv=\"refresh\" content=\"3; url=admin.php\">";
	}
}
?>
      <table border="0" cellspacing="<?php print $maintablespacing?>" cellpadding="<?php print $maintablepadding?>" width="<?php print $maintablewidth?>" bgcolor="<?php print $maintablebg?>" align="center">
<?php if(@$_POST["posted"]=="1" && $success){ ?>
        <tr>
          <td width="100%">
            <table width="<?php print $innertablewidth?>" border="0" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyUpdSuc?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
                        <?php print $yyNoAuto?> <A href="admin.php"><strong><?php print $yyClkHer?></strong></a>.<br />
                        <br />
				<img src="../images/clearpixel.gif" width="300" height="3" alt="" />
                </td>
			  </tr>
			</table></td>
        </tr>
<?php }else{ ?>
        <tr>
		        <form method="post" action="adminlogin.php">
                  <td width="100%">
			<input type="hidden" name="posted" value="1" />
            <table width="<?php print $innertablewidth?>" border="0" cellspacing="<?php print $innertablespacing?>" cellpadding="<?php print $innertablepadding?>" bgcolor="<?php print $innertablebg?>">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyNewUN?></strong>
                </td>
			  </tr>
<?php if(! $success){ ?>
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><font color="#FF0000"><?php print $errmsg?></font>
                </td>
			  </tr>
<?php } ?>
              <tr> 
                <td width="50%" align="right"><strong><?php print $yyUname?>: </strong>
                </td>
				<td width="50%" align="left"><input type="text" name="user" size="20" value="<?php print $adminUser?>" /> 
                </td>
			  </tr>
			  <tr> 
                <td width="50%" align="right"><strong><?php print $yyPass?>: </strong>
                </td>
				<td width="50%" align="left"><input type="password" name="pass" size="20" value="<?php print $adminPassword?>" /> 
                </td>
			  </tr>
			  <tr> 
                <td width="50%" align="right"><strong><?php print $yyPassCo?>: </strong>
                </td>
				<td width="50%" align="left"><input type="password" name="pass2" size="20" value="<?php print $adminPassword?>" /> 
                </td>
			  </tr>
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><input type="submit" value="<?php print $yySubmit?>" /><br />
				<img src="../images/clearpixel.gif" width="300" height="3" alt="" /></td>
			  </tr>
            </table></td>
		  </form>
        </tr>
<?php } ?>
      </table>