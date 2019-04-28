<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
include "./vsadmin/inc/incemail.php";
if(@$_SERVER['CONTENT_LENGTH'] != '' && $_SERVER['CONTENT_LENGTH'] > 10000) exit;
$success=TRUE;
$digidownloads=FALSE;
$allstates='';
$allcountries='';
if(@$enableclientlogin != TRUE){
	$success=FALSE;
	$errmsg="Client login not enabled";
}
function show_states($tstate){
	global $xxOutState,$allstates,$numallstates,$usestateabbrev;
	$foundmatch=FALSE;
	if($xxOutState!='') print '<option value="">' . $xxOutState . '</option>';
	for($index=0;$index<$numallstates;$index++){
		print '<option value="' . str_replace('"','&quot;',(@$usestateabbrev==TRUE?$allstates[$index]['stateAbbrev']:$allstates[$index]['stateName'])) . '"';
		if($tstate==$allstates[$index]['stateName'] || $tstate==$allstates[$index]['stateAbbrev']){
			print ' selected';
			$foundmatch=TRUE;
		}
		print '>' . $allstates[$index]['stateName'] . "</option>\n";
	}
	return $foundmatch;
}
function show_countries($tcountry){
	global $numhomecountries,$nonhomecountries,$allcountries,$numallcountries;
	for($index=0;$index<$numallcountries;$index++){
		print '<option value="' . str_replace('"','&quot;',$allcountries[$index]["countryName"]) . '"';
		if($tcountry==$allcountries[$index]["countryName"]) print " selected";
		print '>' . $allcountries[$index][2] . "</option>\n";
	}
}
$alreadygotadmin = getadminsettings();
if($success && @$_POST["posted"]=="1"){
	$clientEmail = trim(str_replace("'",'',@$_POST['email']));
	$clientPW = trim(str_replace("'",'',@$_POST['pass']));
	$sSQL = "SELECT clID,clUserName,clActions,clLoginLevel,clPercentDiscount FROM customerlogin WHERE (clEmail<>'' AND clEmail=BINARY '" . mysql_escape_string($clientEmail) . "' AND clPW=BINARY '" . mysql_escape_string($clientPW) . "') OR (clEmail='' AND clUserName=BINARY '" . mysql_escape_string($clientEmail) . "' AND clPW=BINARY '" . mysql_escape_string($clientPW) . "')";
	$result = mysql_query($sSQL) or print(mysql_error());
	if($rs = mysql_fetch_array($result)){
		$sSQL = "DELETE FROM cart WHERE cartCompleted=0 AND cartOrderID=0 AND cartSessionID='" . session_id() . "'";
		mysql_query($sSQL) or print(mysql_error());
		$_SESSION['clientID']=$rs['clID'];
		$_SESSION['clientUser']=$rs['clUserName'];
		$_SESSION['clientActions']=$rs['clActions'];
		$_SESSION['clientLoginLevel']=$rs['clLoginLevel'];
		$_SESSION['clientPercentDiscount']=(100.0-(double)$rs['clPercentDiscount'])/100.0;
		print '<script src="vsadmin/savecookie.php?WRITECLL=' . $clientEmail . '&WRITECLP=' . $clientPW;
		if(@$_POST['cook']=='ON') print '&permanent=Y';
		print '"></script>';
		$success=TRUE;
	}else{
		$success=FALSE;
		$errmsg=$xxNoLog;
	}
	mysql_free_result($result);
}
?>
<script language="javascript" type="text/javascript">
<!--
function vieworder(theid){
	document.forms.mainform.action.value="vieworder";
	document.forms.mainform.theid.value=theid;
	document.forms.mainform.submit();
}
function editaddress(theid){
	document.forms.mainform.action.value="editaddress";
	document.forms.mainform.theid.value=theid;
	document.forms.mainform.submit();
}
function newaddress(){
	document.forms.mainform.action.value="newaddress";
	document.forms.mainform.submit();
}
function editaccount(){
	document.forms.mainform.action.value="editaccount";
	document.forms.mainform.submit();
}
function deleteaddress(theid){
	if(confirm("<?php print $xxDelAdd?>")){
		document.forms.mainform.action.value="deleteaddress";
		document.forms.mainform.theid.value=theid;
		document.forms.mainform.submit();
	}
}
//--></script>
	  <table class="cobtbl" width="100%" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
<?php
	if(@$_GET['action']=='logout'){
		$_SESSION['clientID']=NULL; unset($_SESSION['clientID']);
		$_SESSION['clientUser']=NULL; unset($_SESSION['clientUser']);
		$_SESSION['clientActions']=NULL; unset($_SESSION['clientActions']);
		$_SESSION['clientLoginLevel']=NULL; unset($_SESSION['clientLoginLevel']);
		$_SESSION['clientPercentDiscount']=NULL; unset($_SESSION['clientPercentDiscount']);
		print '<script src="vsadmin/savecookie.php?DELCLL=true"></script>';
		if(@$clientlogoutref != '')
			$refURL = $clientlogoutref;
		else
			$refURL = $xxHomeURL;
		print '<meta http-equiv="refresh" content="3; url=' . $refURL . '">';
?>
		  <tr>
			<td colspan="2" bgcolor="#FFFFFF">
			  <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="">
				<tr> 
				  <td width="100%" colspan="2" align="center"><br /><strong><?php print $xxLOSuc?></strong><br /><br /><?php print $xxAutFo?><br /><br />
					<?php print $xxForAut?> <A href="<?php print $refURL?>"><strong><?php print $xxClkHere?></strong></a>.<br />
					<br />
					<img src="../images/clearpixel.gif" width="300" height="3" alt="" />
				  </td>
				</tr>
			  </table>
			</td>
		  </tr>
<?php	
	}elseif(@$_POST['action']=='dolostpassword'){
		$sSQL = "SELECT clPW FROM customerlogin WHERE clEmail<>'' AND clEmail='" . mysql_escape_string(@$_POST['email']) . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if(mysql_num_rows($result) > 0){
			$rs = mysql_fetch_assoc($result);
			if(@$customheaders == ''){
				$customheaders = "MIME-Version: 1.0\n";
				$customheaders .= "From: %from% <%from%>\n";
				if(@$htmlemails==TRUE)
					$customheaders .= "Content-type: text/html; charset=".$emailencoding."\n";
				else
					$customheaders .= "Content-type: text/plain; charset=".$emailencoding."\n";
			}
			if(@$htmlemails==TRUE) $emlNl = '<br />'; else $emlNl="\n";
			$headers = str_replace('%from%',$emailAddr,$customheaders);
			$headers = str_replace('%to%',$_POST['email'],$headers);
			mail($_POST['email'], $xxForPas, $xxLosPw1 . $emlNl . $storeurl . $emlNl . $emlNl . $xxLosPw2 . $emlNl . $xxLogin . ': ' . $_POST['email'] . $emlNl . $xxPwd . ': ' . $rs['clPW'] . $emlNl . $emlNl . $xxLosPw3 . $emlNl, $headers);
			$success=TRUE;
		}else{
			$success=FALSE;
		} ?>
	  <form method="post" name="mainform" action="">
		<tr>
		  <td class="cobhl" bgcolor="#EBEBEB" align="center" height="38" colspan="2"><strong><?php print $xxCusAcc?></strong></td>
		</tr>
		  <tr>
			<td class="cobhl" bgcolor="#EBEBEB" align="right" height="38" width="40%"><strong><?php print $xxForPas?></strong></td>
			<td class="cobll" bgcolor="#FFFFFF" align="left" height="38"><?php if($success) print $xxSenPw; else print $xxSorPw; ?></td>
		  </tr>
		  <tr>
			<td class="cobll" bgcolor="#FFFFFF" align="center" height="38" colspan="2"><?php
		if($success){
			print '<input type="button" value="' . $xxLogin . '" onclick="document.location=\'cart.php?mode=login\'">';
		}else
			print '<input type="button" value="' . $xxGoBack . '" onclick="history.go(-1)">';
		?></td>
		  </tr>
	  </form>
<?php
	}elseif(@$_GET['mode'] == 'lostpassword'){ ?>
	  <form method="post" name="mainform" action="">
	  <input type="hidden" name="action" value="dolostpassword" />
		<tr>
		  <td class="cobhl" bgcolor="#EBEBEB" align="center" height="32" colspan="2"><strong><?php print $xxCusAcc?></strong></td>
		</tr>
		<tr>
		  <td class="cobhl" bgcolor="#EBEBEB" align="right" height="26"><strong><?php print $xxForPas?></strong></td>
		  <td class="cobll" bgcolor="#FFFFFF" align="left" height="26"><font size="1"><?php print $xxEntEm?></font></td>
		</tr>
		<tr>
		  <td class="cobhl" bgcolor="#EBEBEB" align="right" height="26"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print $xxEmail?>: </strong></td>
		  <td class="cobll" bgcolor="#FFFFFF" align="left" height="26"><input type="text" name="email" size="31" /></td>
		</tr>
		<tr>
		  <td class="cobhl" bgcolor="#EBEBEB" align="center" height="26" colspan="2"><input type="submit" value="<?php print $xxSubmt?>" /></td>
		</tr>
	  </form>
<?php
	}elseif(@$_SESSION['clientID']==''){ ?>
        <tr>
		  <td class="cobhl" bgcolor="#EBEBEB" align="center" height="32" colspan="2"><strong><?php print $xxCusAcc?></strong></td>
		</tr>
		<tr>
		  <td class="cobll" bgcolor="#FFFFFF" align="center" height="32" colspan="2"><p>&nbsp;</p><p><?php print $xxMusLog?></p>
		  <p><input type="button" value="<?php print $xxLogin?>" onclick="document.location='cart.php?mode=login&refurl=<?php print urlencode(@$_SERVER['PHP_SELF'])?>'" /></p>
		  <p>&nbsp;</p>
		  </td>
		</tr>
<?php
	}else{ // is logged in
		if(@$_POST['action']=='vieworder'){ ?>
        <tr>
		  <td width="100%" class="cobll" bgcolor="#FFFFFF"><?php
			$ordID = str_replace("'",'',@$_POST['theid']);
			if(is_numeric($ordID)) $success=TRUE; else $success=FALSE;
			if($success){
				$sSQL = "SELECT ordID FROM orders WHERE ordID=" . $ordID . " AND ordClientID=" . $_SESSION['clientID'];
				$result = mysql_query($sSQL) or print(mysql_error());
				if(mysql_num_rows($result)==0) $success=FALSE;
			}
			if($success){
				$xxThkYou='<input type="button" value="' . $xxBack . '" onclick="history.go(-1)">';
				$xxRecEml='';
				do_order_success($ordID,$emailAddr,FALSE,TRUE,FALSE,FALSE,FALSE);
			}else{
				$errtext = "Sorry, could not find a matching order.";
				order_failed();
			} ?>
		  </td>
		</tr>
<?php	}elseif(@$_POST['action']=='doeditaccount'){
			$oldpw = @$_POST['oldpw'];
			$newpw = @$_POST['newpw'];
			$newpw2 = @$_POST['newpw2'];
			$clientuser = @$_POST['name'];
			$clientemail = @$_POST['email'];
			$allowemail = @$_POST['allowemail'];
			$sSQL = "SELECT clPW,clEmail FROM customerlogin WHERE clID=" . $_SESSION['clientID'];
			$result = mysql_query($sSQL) or print(mysql_error());
			$rs = mysql_fetch_assoc($result);
			$oldpassword=$rs['clPW'];
			$oldemail=$rs['clEmail'];
			$success=TRUE;
			if($newpw!='' || $newpw2!=''){
				if($oldpw!=$oldpassword){
					$success=FALSE;
					$errmsg=$xxExNoMa;
				}
			}
			if($success){
				$sSQL = 'UPDATE customerlogin SET ';
				$sSQL .= "clUserName='" . mysql_escape_string($clientuser) . "',";
				$sSQL .= "clEmail='" . mysql_escape_string($clientemail) . "'";
				if($newpw!='') $sSQL .= ",clPW='" . mysql_escape_string(str_replace("'",'',$newpw)) . "'";
				$sSQL .= " WHERE clID=" . $_SESSION['clientID'];
				mysql_query($sSQL) or print(mysql_error());
				if($allowemail=='ON'){
					mysql_query("INSERT INTO mailinglist (email) VALUES ('" . mysql_escape_string(strtolower($clientemail)) . "')");
					if($oldemail != $clientemail) mysql_query("DELETE FROM mailinglist WHERE email='" . mysql_escape_string($oldemail) . "'");
				}else{
					mysql_query("DELETE FROM mailinglist WHERE email='" . mysql_escape_string($clientemail) . "'");
					mysql_query("DELETE FROM mailinglist WHERE email='" . mysql_escape_string($oldemail) . "'");
				}
				$_SESSION['clientUser']=$clientuser;
				print '<meta http-equiv="Refresh" content="2; URL=' . $_SERVER['PHP_SELF'] . '">';
			}
?>
	  <form method="post" name="mainform" action="">
		<tr>
		  <td class="cobhl" bgcolor="#EBEBEB" align="center" height="38" colspan="2"><strong><?php print $xxCusAcc?></strong></td>
		</tr>
		  <tr>
			<td class="cobll" bgcolor="#FFFFFF" align="center" height="38"><?php if($success) print $xxUpdSuc; else print $errmsg ?></td>
		  </tr>
		  <tr>
			<td class="cobll" bgcolor="#FFFFFF" align="center" height="38" colspan="2"><?php
		if($success)
			print '<input type="submit" value="' . $xxCusAcc . '" />';
		else
			print '<input type="button" value="' . $xxGoBack . '" onclick="history.go(-1)">';
		?></td>
		  </tr>
	  </form>
<?php	}elseif(@$_POST['action']=='editaccount'){ ?>
<script language="javascript" type="text/javascript">
<!--
var checkedfullname=false;
function checknewaccount(){
frm=document.forms.mainform;
if(frm.name.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxName?>\".");
	frm.name.focus();
	return (false);
}
gotspace=false;
var checkStr = frm.name.value;
for (i = 0; i < checkStr.length; i++){
	if(checkStr.charAt(i)==" ")
		gotspace=true;
}
if(!checkedfullname && !gotspace){
	alert("<?php print $xxFulNam?> \"<?php print $xxName?>\".");
	frm.name.focus();
	checkedfullname=true;
	return (false);
}
if(frm.email.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxEmail?>\".");
	frm.email.focus();
	return (false);
}
validemail=0;
var checkStr = frm.email.value;
for (i = 0; i < checkStr.length; i++){
	if(checkStr.charAt(i)=="@")
		validemail |= 1;
	if(checkStr.charAt(i)==".")
		validemail |= 2;
}
if(validemail != 3){
	alert("<?php print $xxValEm?>");
	frm.email.focus();
	return (false);
}
var newpw = frm.newpw.value;
var newpw2 = frm.newpw2.value;
if(newpw!='' && newpw!=newpw2){
	alert("<?php print $xxPwdMat?>");
	frm.newpw.focus();
	return(false);
}
return true;
}
//--></script>
		<form method="post" name="mainform" action="" onsubmit="return checknewaccount()">
		<input type="hidden" name="action" value="doeditaccount" />
		  <tr>
            <td class="cobhl" bgcolor="#EBEBEB" align="center" height="34"><strong><?php print $xxAccDet?></strong></td>
		  </tr>
		  <tr>
            <td class="cobll" bgcolor="#FFFFFF" align="center">
				  <table class="cobtbl" width="100%" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
<?php		$sSQL = "SELECT clID,clUserName,clActions,clLoginLevel,clPercentDiscount,clEmail FROM customerlogin WHERE clID=" . $_SESSION['clientID'];
			$result = mysql_query($sSQL) or print(mysql_error());
			$rs = mysql_fetch_assoc($result);
			$theemail=$rs['clEmail'];
			$sSQL = "SELECT email FROM mailinglist WHERE email='" . mysql_escape_string($theemail) . "'";
			$result = mysql_query($sSQL) or print(mysql_error());
			if(mysql_num_rows($result)>0) $allowemail=1; else $allowemail=0;
?>
					<tr><td class="cobhl" bgcolor="#EBEBEB" align="right" width="20%"><strong><?php print $xxName?>:</strong></td>
					<td class="cobll" bgcolor="#FFFFFF" align="left" width="30%"><input type="text" size="30" name="name" value="<?php print htmlspecialchars($_SESSION['clientUser'])?>" /></td>
					<td class="cobll" bgcolor="#FFFFFF" align="right" width="8%" rowspan="2"><input type="checkbox" name="allowemail" value="ON"<?php if($allowemail!=0) print ' checked'?>></td>
					<td class="cobhl" bgcolor="#EBEBEB" align="left" rowspan="2"><strong><?php print $xxAlPrEm?></strong><br />
					<font size="1"><?php print $xxNevDiv?></font></td>
					</tr><tr><td class="cobhl" bgcolor="#EBEBEB" align="right"><strong><?php print $xxEmail?>:</strong></td>
					<td class="cobll" bgcolor="#FFFFFF" align="left"><input type="text" size="30" name="email" value="<?php print $theemail?>" /></td>
					</tr><tr><td class="cobhl" bgcolor="#EBEBEB" align="center" colspan="4" height="34"><strong><?php print $xxPwdChg?></strong></td></tr>
					
					<tr><td class="cobhl" bgcolor="#EBEBEB" align="right" colspan="2"><strong><?php print $xxOldPwd?>:</strong></td>
					<td class="cobll" bgcolor="#FFFFFF" align="left" colspan="2"><input type="password" size="20" name="oldpw" value="" /></td></tr>
					<tr><td class="cobhl" bgcolor="#EBEBEB" align="right" colspan="2"><strong><?php print $xxNewPwd?>:</strong></td>
					<td class="cobll" bgcolor="#FFFFFF" align="left" colspan="2"><input type="password" size="20" name="newpw" value="" /></td></tr>
					<tr><td class="cobhl" bgcolor="#EBEBEB" align="right" colspan="2"><strong><?php print $xxRptPwd?>:</strong></td>
					<td class="cobll" bgcolor="#FFFFFF" align="left" colspan="2"><input type="password" size="20" name="newpw2" value="" /></td></tr>
	
					</tr><tr><td class="cobll" bgcolor="#FFFFFF" align="center" colspan="4" height="34"><input type="submit" value="<?php print $xxSubmt?>" /> <input type="reset" value="<?php print $xxReset?>" /> <input type="button" value="<?php print $xxCancel?>" onclick="history.go(-1)" /></td>
					</tr>
				  </table>
			</td>
		  </tr>
		</form>
<?php	}elseif(@$_POST['action']=='editaddress' || @$_POST['action']=='newaddress'){
			$addID = str_replace("'",'',@$_POST['theid']);
			$addIsDefault='';
			$addName='';
			$addAddress='';
			$addAddress2='';
			$addState='';
			$addCity='';
			$addZip='';
			$addPhone='';
			$addCountry='';
			$addExtra1='';
			$addExtra2='';
			$havestate=FALSE;
			$sSQL = "SELECT stateName,stateAbbrev FROM states WHERE stateEnabled=1 ORDER BY stateName";
			$result = mysql_query($sSQL) or print(mysql_error());
			$numallstates=0;
			$numallcountries=0;
			while($rs = mysql_fetch_array($result))
				$allstates[$numallstates++]=$rs;
			mysql_free_result($result);
			$numhomecountries = 0;
			$nonhomecountries = 0;
			$sSQL = "SELECT countryName,countryOrder,".getlangid("countryName",8)." FROM countries WHERE countryEnabled=1 ORDER BY countryOrder DESC," . getlangid("countryName",8);
			$result = mysql_query($sSQL) or print(mysql_error());
			while($rs = mysql_fetch_array($result)){
				$allcountries[$numallcountries++]=$rs;
				if($rs["countryOrder"]==2)$numhomecountries++;else $nonhomecountries++;
			}
			mysql_free_result($result);
			if(@$_POST['action']=='editaddress'){
				$sSQL = "SELECT addID,addIsDefault,addName,addAddress,addAddress2,addState,addCity,addZip,addPhone,addCountry,addExtra1,addExtra2 FROM address WHERE addID=" . $addID . " AND addCustID='" . $_SESSION['clientID'] . "' ORDER BY addIsDefault";
				$result = mysql_query($sSQL) or print(mysql_error());
				if($rs = mysql_fetch_assoc($result)){
					$addIsDefault=$rs['addIsDefault'];
					$addName=$rs['addName'];
					$addAddress=$rs['addAddress'];
					$addAddress2=$rs['addAddress2'];
					$addState=$rs['addState'];
					$addCity=$rs['addCity'];
					$addZip=$rs['addZip'];
					$addPhone=$rs['addPhone'];
					$addCountry=$rs['addCountry'];
					$addExtra1=$rs['addExtra1'];
					$addExtra2=$rs['addExtra2'];
				}
			} ?>
		<form method="post" name="mainform" action="" onsubmit="return checkform(this)">
		<input type="hidden" name="action" value="<?php if(@$_POST['action']=='editaddress') print "doeditaddress"; else print "donewaddress" ?>" />
		<input type="hidden" name="theid" value="<?php print $addID?>" />
		<tr height="32"><td align="center" class="cobhl" bgcolor="#EBEBEB" colspan="2"><strong><?php print $xxEdAdd?></strong></td></tr>
		<?php	if(trim(@$extraorderfield1) != ''){ ?>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php print (@$extraorderfield1required==TRUE ? '<font color="#FF0000">*</font>' : '') . $extraorderfield1 ?>:</strong></td><td class="cobll" bgcolor="#FFFFFF"><?php if(@$extraorderfield1html != '') print $extraorderfield1html; else print '<input type="text" name="ordextra1" id="ordextra1" size="20" value="' . $addExtra1 . '" />'?></td></tr>
		<?php	} ?>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><font color='#FF0000'>*</font><?php print $xxName?>:</strong></td><td class="cobll" bgcolor="#FFFFFF"><input type="text" name="name" id="name" size="20" value="<?php print $addName?>" /></td></tr>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><font color='#FF0000'>*</font><?php print $xxAddress?>:</strong></td><td class="cobll" bgcolor="#FFFFFF"><input type="text" name="address" id="address" size="25" value="<?php print $addAddress?>" /></td></tr>
		<?php	if(@$useaddressline2==TRUE){ ?>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php print $xxAddress2?>:</strong></td><td class="cobll" bgcolor="#FFFFFF"><input type="text" name="address2" id="address2" size="25" value="<?php print $addAddress2?>" /></td></tr>
		<?php	} ?>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><font color='#FF0000'>*</font><?php print $xxCity?>:</strong></td><td class="cobll" bgcolor="#FFFFFF"><input type="text" name="city" id="city" size="20" value="<?php print $addCity?>" /></td></tr>
		<?php	if($numallstates>0){ ?>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><font color='#FF0000'><span id="outspandd" style="visibility:hidden">*</span></font><?php print $xxState?>:</strong></td><td class="cobll" bgcolor="#FFFFFF"><select name="state" id="state" size="1" onchange="dosavestate('')"><?php $havestate = show_states($addState) ?></select></td></tr>
		<?php	}
			if($nonhomecountries != 0){ ?>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><font color='#FF0000'><span id="outspan" style="visibility:hidden">*</span></font><?php print $xxNonState?>:</strong></td><td class="cobll" bgcolor="#FFFFFF"><input type="text" name="state2" id="state2" size="20" value="<?php if(! $havestate) print $addState?>" /></td></tr>
		<?php	} ?>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><font color='#FF0000'>*</font><?php print $xxCountry?>:</strong></td><td class="cobll" bgcolor="#FFFFFF"><select name="country" id="country" size="1" onchange="checkoutspan('')" ><?php show_countries($addCountry) ?></select></td></tr>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><font color='#FF0000'><?php if(@$zipoptional != TRUE) print "*"?></font><?php print $xxZip?>:</strong></td><td class="cobll" bgcolor="#FFFFFF"><input type="text" name="zip" id="zip" size="10" value="<?php print $addZip?>" /></td></tr>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><font color='#FF0000'>*</font><?php print $xxPhone?>:</strong></td><td class="cobll" bgcolor="#FFFFFF"><input type="text" name="phone" id="phone" size="20" value="<?php print $addPhone?>" /></td></tr>
		<?php	if(trim(@$extraorderfield2) != ''){ ?>
		<tr><td align="right" class="cobhl" bgcolor="#EBEBEB"><strong><?php print (@$extraorderfield2required==true ? '<font color="#FF0000">*</font>' : '') . $extraorderfield2 ?>:</strong></td><td class="cobll" bgcolor="#FFFFFF"><?php if(@$extraorderfield2html != '') print $extraorderfield2html; else print '<input type="text" name="ordextra2" id="ordextra2" size="20" value="' . $addExtra2 . '" />'?></td></tr>
		<?php	} ?>
		<tr><td align="center" colspan="2" class="cobll" bgcolor="#FFFFFF"><input type="submit" value="<?php print $xxSubmt?>"> <input type="button" value="Cancel" onclick="history.go(-1)"></td></tr>
		</form>
<script language="javascript" type="text/javascript">
var checkedfullname=false;
var numhomecountries=0,nonhomecountries=0;
function checkform(frm)
{
<?php if(trim(@$extraorderfield1) != '' && @$extraorderfield1required==true){ ?>
if(frm.ordextra1.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $extraorderfield1?>\".");
	frm.ordextra1.focus();
	return (false);
}
<?php } ?>
if(frm.name.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxName?>\".");
	frm.name.focus();
	return (false);
}
gotspace=false;
var checkStr = frm.name.value;
for (i = 0; i < checkStr.length; i++){
	if(checkStr.charAt(i)==" ")
		gotspace=true;
}
if(!checkedfullname && !gotspace){
	alert("<?php print $xxFulNam?> \"<?php print $xxName?>\".");
	frm.name.focus();
	checkedfullname=true;
	return (false);
}
if(frm.address.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxAddress?>\".");
	frm.address.focus();
	return (false);
}
if(frm.city.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxCity?>\".");
	frm.city.focus();
	return (false);
}
if(frm.country.selectedIndex < numhomecountries){
<?php	if($numallstates>0 && $xxOutState != ''){ ?>
	if(frm.state.selectedIndex==0){
		alert("<?php print $xxPlsSlct . " " . $xxState?>.");
		frm.state.focus();
		return (false);
	}
<?php	} ?>
}else{
<?php	if($nonhomecountries>0){ ?>
	if(frm.state2.value==""){
		alert("<?php print $xxPlsEntr?> \"<?php print str_replace('<br />',' ',$xxNonState)?>\".");
		frm.state2.focus();
		return (false);
	}
<?php	} ?>}
if(frm.zip.value==""<?php if(@$zipoptional==TRUE) print ' && FALSE'?>){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxZip?>\".");
	frm.zip.focus();
	return (false);
}
if(frm.phone.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $xxPhone?>\".");
	frm.phone.focus();
	return (false);
}
<?php if(trim(@$extraorderfield2) != '' && @$extraorderfield2required==TRUE){ ?>
if(frm.ordextra2.value==""){
	alert("<?php print $xxPlsEntr?> \"<?php print $extraorderfield2?>\".");
	frm.ordextra2.focus();
	return (false);
}
<?php } ?>
return (true);
}
<?php if(@$termsandconditions==TRUE){ ?>
function showtermsandconds(){
newwin=window.open("termsandconditions.php","Terms","menubar=no, scrollbars=yes, width=420, height=380, directories=no,location=no,resizable=yes,status=no,toolbar=no");
}
<?php } ?>
var savestate=0;
var ssavestate=0;
function dosavestate(shp){
	thestate = eval('document.forms.mainform.'+shp+'state');
	eval(shp+'savestate = thestate.selectedIndex');
}
function checkoutspan(shp){
if(shp=='s' && document.getElementById('saddress').value=="")visib='hidden';else visib='visible';<?php
if($nonhomecountries>0) print "thestyle = document.getElementById(shp+'outspan').style;\r\n";
if($numallstates>0){
	print "theddstyle = document.getElementById(shp+'outspandd').style;\r\n";
	print "thestate = eval('document.forms.mainform.'+shp+'state');\r\n";
} ?>
thecntry = eval('document.forms.mainform.'+shp+'country');
if(thecntry.selectedIndex < numhomecountries){<?php
if($nonhomecountries>0) print "thestyle.visibility='hidden';\r\n";
if($numallstates>0){
	print "theddstyle.visibility=visib;\r\n";
	print "thestate.disabled=false;\r\n";
	print "eval('thestate.selectedIndex='+shp+'savestate');\r\n";
} ?>
}else{<?php
if($nonhomecountries>0) print "thestyle.visibility=visib;\r\n";
if($numallstates>0){ ?>
theddstyle.visibility="hidden";
if(thestate.disabled==false){
thestate.disabled=true;
eval(shp+'savestate = thestate.selectedIndex');
thestate.selectedIndex=0;}
<?php } ?>
}}
<?php
	if($numallstates>0) print "savestate = document.forms.mainform.state.selectedIndex;\r\n";
	print "numhomecountries=" . $numhomecountries . ";\r\n";
	print "checkoutspan('');\r\n";
?></script>
<?php	}elseif(@$_POST['action']=="deleteaddress" || @$_POST['action']=="doeditaddress" || @$_POST['action']=="donewaddress"){
			$addID = str_replace("'",'',@$_POST['theid']);
			$ordName=@$_POST['name'];
			$ordAddress=@$_POST['address'];
			$ordAddress2=@$_POST['address2'];
			$ordState=@$_POST['state'];
			$ordCity=@$_POST['city'];
			$ordZip=@$_POST['zip'];
			$ordPhone=@$_POST['phone'];
			$ordCountry=@$_POST['country'];
			$ordExtra1=@$_POST['ordextra1'];
			$ordExtra2=@$_POST['ordextra2'];
			if(@$_POST['action']=="deleteaddress"){
				$sSQL = "DELETE FROM address WHERE addID=" . $addID . " AND addCustID=" . $_SESSION['clientID'];
				mysql_query($sSQL) or print(mysql_error());
			}elseif(@$_POST['action']=="donewaddress"){
				$sSQL = "INSERT INTO address (addCustID,addIsDefault,addName,addAddress,addAddress2,addCity,addState,addZip,addCountry,addPhone,addExtra1,addExtra2) VALUES (" . $_SESSION['clientID'] . ",0,'" . mysql_escape_string($ordName) . "','" . mysql_escape_string($ordAddress) . "','" . mysql_escape_string($ordAddress2) . "','" . mysql_escape_string($ordCity) . "','" . mysql_escape_string($ordState) . "','" . mysql_escape_string($ordZip) . "','" . mysql_escape_string($ordCountry) . "','" . mysql_escape_string($ordPhone) . "','" . mysql_escape_string($ordExtra1) . "','" . mysql_escape_string($ordExtra2) . "')";
				mysql_query($sSQL) or print(mysql_error());
			}elseif(@$_POST['action']=="doeditaddress"){
				$sSQL = "UPDATE address SET addName='" . mysql_escape_string($ordName) . "',addAddress='" . mysql_escape_string($ordAddress) . "',addAddress2='" . mysql_escape_string($ordAddress2) . "',addCity='" . mysql_escape_string($ordCity) . "',addState='" . mysql_escape_string($ordState) . "',addZip='" . mysql_escape_string($ordZip) . "',addCountry='" . mysql_escape_string($ordCountry) . "',addPhone='" . mysql_escape_string($ordPhone) . "',addExtra1='" . mysql_escape_string($ordExtra1) . "',addExtra2='" . mysql_escape_string($ordExtra2) . "' WHERE addCustID=" . $_SESSION['clientID'] . " AND addID=" . $addID;
				mysql_query($sSQL) or print(mysql_error());
			}
			print '<meta http-equiv="Refresh" content="2; URL=' . $_SERVER['PHP_SELF'] . '">';
?>		<tr>
          <td class="cobll" bgcolor="#FFFFFF" width="100%" align="center">
			<br /><strong><?php print $xxUpdSuc?></strong><br /><br />
		  </td>
        </tr>
<?php	}else{ ?>
		  <form method="post" name="mainform" action="">
			<input type="hidden" name="posted" value="1" />
			<input type="hidden" name="action" value="none" />
			<input type="hidden" name="theid" value="" />
              <tr> 
                <td class="cobhl" colspan="2" bgcolor="#EBEBEB" align="center" height="34"><strong><?php print $xxAccDet?></strong></td>
			  </tr>
			  <tr> 
                <td class="cobll" bgcolor="#FFFFFF" height="34" align="center">
				  <table class="cobtbl" width="100%" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
<?php		$sSQL = "SELECT clID,clUserName,clActions,clLoginLevel,clPercentDiscount,clEmail FROM customerlogin WHERE clID=" . $_SESSION['clientID'];
			$result = mysql_query($sSQL) or print(mysql_error());
			$rs = mysql_fetch_assoc($result);
			$theemail=$rs['clEmail'];
			$sSQL = "SELECT email FROM mailinglist WHERE email='" . mysql_escape_string($theemail) . "'";
			$result = mysql_query($sSQL) or print(mysql_error());
			if(mysql_num_rows($result)>0) $allowemail=1; else $allowemail=0;
?>
					<tr><td class="cobhl" bgcolor="#EBEBEB" align="right" width="25%"><strong><?php print $xxName?>:</strong></td>
					<td class="cobll" bgcolor="#FFFFFF" align="left" width="25%"><?php print $_SESSION['clientUser']?></td>
					<td class="cobll" bgcolor="#FFFFFF" align="right" width="8%" rowspan="2"><input type="checkbox" name="allowemail" value="ON"<?php if($allowemail!=0) print ' checked'?> disabled></td>
					<td class="cobhl" bgcolor="#EBEBEB" align="left" rowspan="2"><strong><?php print $xxAlPrEm?></strong><br />
					<font size="1"><?php print $xxNevDiv?></font></td>
					</tr><tr><td class="cobhl" bgcolor="#EBEBEB" align="right"><strong><?php print $xxEmail?>:</strong></td>
					<td class="cobll" bgcolor="#FFFFFF" align="left"><?php print $theemail?></td>
					</tr><tr><td class="cobll" bgcolor="#FFFFFF" align="left" colspan="4"><br /><ul><li><?php print $xxChaAcc?> <a href="javascript:editaccount()"><strong><?php print $xxClkHere?></strong></a>.</li></ul></td>
					</tr>
				  </table>
				</td>
			  </tr>
              <tr> 
                <td class="cobhl" colspan="2" bgcolor="#EBEBEB" align="center" height="34"><strong><?php print $xxAddMan?></strong></td>
			  </tr>
			  <tr> 
                <td class="cobll" bgcolor="#FFFFFF" height="34" align="center">
				  <table class="cobtbl" width="100%" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
<?php		$sSQL = "SELECT addID,addIsDefault,addName,addAddress,addAddress2,addState,addCity,addZip,addPhone,addCountry FROM address WHERE addCustID=" . $_SESSION['clientID'] . " ORDER BY addIsDefault";
			$result = mysql_query($sSQL) or print(mysql_error());
			if(mysql_num_rows($result)>0){
				while($rs = mysql_fetch_assoc($result)){
					print '<tr><td width="50%" class="cobll" bgcolor="#FFFFFF" align="left">' . $rs['addName'] . "<br />" . $rs['addAddress'] . (trim($rs['addAddress2']) != '' ? '<br />' . $rs['addAddress2'] : '') . "<br /> " . $rs['addCity'] . ", " . $rs['addState'] . ($rs['addZip'] != '' ? '<br />' . $rs['addZip'] : '') . '<br />' . $rs['addCountry'] . '</td>';
					print '<td class="cobhl" bgcolor="#EBEBEB" align="left"><ul><li><a href="javascript:editaddress(' . $rs['addID'] . ')">' . $xxEdAdd . '</a></li><br><br><li><a href="javascript:deleteaddress(' . $rs['addID'] . ')">' . $xxDeAdd . '</a></li></td></tr>';
				}
			}else{
				print '<tr><td class="cobll" bgcolor="#FFFFFF" align="center" colspan="2" height="34">' . $xxNoAdd . '</td></tr>';
			}
?>
					<tr><td class="cobhl" colspan="2" bgcolor="#EBEBEB"><br /><ul><li><?php print $xxPCAdd?> <a href="javascript:newaddress()"><strong><?php print $xxClkHere?></strong></a>.</li></ul></td></tr>
				  </table>
				</td>
			  </tr>
			  <tr> 
                <td class="cobhl" colspan="2" bgcolor="#EBEBEB" align="center" height="34"><strong><?php print $xxOrdMan?></strong></td>
			  </tr>
			  <tr> 
                <td class="cobll" bgcolor="#FFFFFF" height="34" align="center">
				  <table class="cobtbl" width="100%" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
					<tr><td class="cobhl" bgcolor="#EBEBEB"><?php print $xxOrdId?></td><td class="cobhl" bgcolor="#EBEBEB"><?php print $xxDate?></td><td class="cobhl" bgcolor="#EBEBEB"><?php print $xxStatus?></td><td class="cobhl" bgcolor="#EBEBEB"><?php print $xxGndTot?></td><td class="cobhl" bgcolor="#EBEBEB"><?php print $xxCODets?></td></tr>
<?php		$sSQL = "SELECT ordID,ordDate,ordTotal,ordStateTax,ordCountryTax,ordShipping,ordHSTTax,ordHandling,ordDiscount," . getlangid('statPublic',64) . " FROM orders LEFT OUTER JOIN orderstatus ON orders.ordStatus=orderstatus.statID WHERE ordClientID=" . $_SESSION['clientID'] . " ORDER BY ordDate";
			$result = mysql_query($sSQL) or print(mysql_error());
			if(mysql_num_rows($result)>0){
				while($rs = mysql_fetch_assoc($result)){
					print '<tr><td class="cobll" bgcolor="#FFFFFF">' . $rs['ordID'] . '</td>';
					print '<td class="cobll" bgcolor="#FFFFFF">' . $rs['ordDate'] . '</td>';
					print '<td class="cobll" bgcolor="#FFFFFF">' . $rs[getlangid("statPublic",64)] . '</td>';
					print '<td class="cobll" bgcolor="#FFFFFF">' . FormatEuroCurrency(($rs['ordTotal']+$rs['ordStateTax']+$rs['ordCountryTax']+$rs['ordShipping']+$rs['ordHSTTax']+$rs['ordHandling'])-$rs['ordDiscount']) . '</td>';
					print '<td class="cobll" bgcolor="#FFFFFF"><a href="javascript:vieworder(' . $rs['ordID'] . ')">' . $xxClkHere . '</a></td></tr>';
				}
			}else{
				print '<tr><td class="cobll" bgcolor="#FFFFFF" colspan="5" height="34" align="center">' . $xxNoOrd . '</td></tr>';
			}
?>
				  </table>
				</td>
			  </tr>
		  </form>
<?php	}
	} ?>
      </table>