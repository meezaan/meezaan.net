<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
$success=TRUE;
if(@$customheaders == ""){
	$customheaders = "MIME-Version: 1.0\n";
	$customheaders .= "From: %from% <%from%>\n";
	if(@$htmlemails==TRUE)
		$customheaders .= "Content-type: text/html; charset=".$emailencoding."\n";
	else
		$customheaders .= "Content-type: text/plain; charset=".$emailencoding."\n";
}
if($success){
	if(@$_POST["posted"]=="1"){
		$alreadygotadmin = getadminsettings();
		$sSQL = "SELECT adminEmail, adminStoreURL, adminUser, adminPassword FROM admin WHERE adminID=1";
		$result = mysql_query($sSQL) or print(mysql_error());
		$rs = mysql_fetch_array($result);
		if(@$notifyloginattempt==TRUE){
			if(@$htmlemails==TRUE) $emlNl = "<br />"; else $emlNl="\n";
			$sMessage = "This is notification of a login attempt at your store."  . $emlNl;
			$sMessage .= $storeurl . $emlNl;
			if(trim($_POST["user"])==$rs["adminUser"] && trim($_POST["pass"])==$rs["adminPassword"])
				$sMessage .= "The correct login / password was used." . $emlNl;
			else{
				$sMessage .= "An incorrect login was used." . $emlNl .
					"Username: " . $_POST["user"] . $emlNl .
					"Password: " . $_POST["pass"] . $emlNl;
			}
			$sMessage .= "User Agent: " . @$_SERVER["HTTP_USER_AGENT"] . $emlNl .
				"IP: " . @$_SERVER["REMOTE_ADDR"] . $emlNl;
			$headers = str_replace('%from%',$emailAddr,$customheaders);
			$headers = str_replace('%to%',$emailAddr,$headers);
			mail($emailAddr, "Login attempt at your store", $sMessage, $headers);
		}
		if(! (trim($_POST["user"])==$rs["adminUser"] && trim($_POST["pass"])==$rs["adminPassword"]) || @$disallowlogin==TRUE){
			$success = FALSE;
			$errmsg = $yyLogSor;
		}else{
			if(@$storesessionvalue=="") $storesessionvalue="virtualstore";
			$_SESSION["loggedon"] = $storesessionvalue;
			print "<meta http-equiv=\"refresh\" content=\"3; url=admin.php\">";
		}
	}
}
?>
      <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="" align="center">
<?php if(@$_POST["posted"]=="1" && $success){ ?>
        <tr>
          <td width="100%">
            <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyLogCor?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
                        <?php print $yyNoAuto?><A href="admin.php"><strong><?php print $yyClkHer?></strong></a>.<br />
                        <br />
				<p align="center"><img src="../images/clearpixel.gif" width="300" height="1" alt="" /> 
                  </p>
                </td>
			  </tr>
			</table>
		  </td>
        </tr>
<?php }else{ ?>
        <tr>
		        <form method="post" action="login.php">
                  <td width="100%">
			<input type="hidden" name="posted" value="1">
            <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyEntUna?><br />&nbsp;</strong>
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
				<td width="50%" align="left"><input type="text" name="user" size="20"> 
                </td>
			  </tr>
			  <tr> 
                <td width="50%" align="right"><strong><?php print $yyPass?>: </strong>
                </td>
				<td width="50%" align="left"><input type="password" name="pass" size="20"> 
                </td>
			  </tr>
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><input type="submit" value="<?php print $yySubmit?>"><br />
				<p align="center"><img src="../images/clearpixel.gif" width="300" height="1" alt="" /> 
                  </p>
                </td>
			  </tr>
            </table>
          </td>
		  </form>
        </tr>
<?php } ?>
      </table>