<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protect under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$menupoplimit=='') $menupoplimit=9;
if(@$_SESSION['clientLoginLevel'] != '') $minloglevel=$_SESSION['clientLoginLevel']; else $minloglevel=0;
$alreadygotadmin = getadminsettings();
$sSQL = 'SELECT sectionID,' . getlangid('sectionName',256) . ',topSection,rootSection,sectionurl FROM sections WHERE sectionDisabled<=' . $minloglevel . ' ORDER BY sectionOrder';
$result = mysql_query($sSQL) or print(mysql_error());
$numrows = 0;
if(@$_SERVER['HTTPS']=='on' || @$_SERVER['SERVER_PORT']=='443') $incstoreurl=$storeurl; else $incstoreurl='';
function mwritemenulevel($id,$itlevel){
	global $mAlldata,$numrows,$menupoplimit,$menuprestr,$storeurl,$menucategoriesatroot,$incstoreurl;
	if($itlevel<=$menupoplimit){
		if(! (@$menucategoriesatroot==2 && $id==0)){
			for($mIndex=0;$mIndex < $numrows;$mIndex++){
				if($mAlldata[$mIndex][2]==$id){
					$mTID = $mAlldata[$mIndex][2];
					if($mTID==0) $mTID = '';
					if(@$menucategoriesatroot==1)
						$menuheadsec = 'mymenu.addMenu(';
					else
						$menuheadsec = 'mymenu.addSubMenu("products' . $mTID . '",';
					if(trim($mAlldata[$mIndex][4]) != ''){
						print $menuheadsec.'"products' . $mAlldata[$mIndex][0] . '","' . @$menuprestr . str_replace('"','\"',$mAlldata[$mIndex][1]) . @$menupoststr . '","' . $incstoreurl . $mAlldata[$mIndex][4] . "\");\n";
					}else{
						if($mAlldata[$mIndex][3]==0)
							print $menuheadsec.'"products' . $mAlldata[$mIndex][0] . '","' . @$menuprestr . str_replace('"','\"',$mAlldata[$mIndex][1]) . @$menupoststr . '","'.$incstoreurl.'categories.php?cat=' . $mAlldata[$mIndex][0] . "\");\n";
						else
							print $menuheadsec.'"products' . $mAlldata[$mIndex][0] . '","' . @$menuprestr . str_replace('"','\"',$mAlldata[$mIndex][1]) . @$menupoststr . '","'.$incstoreurl.'products.php?cat=' . $mAlldata[$mIndex][0] . "\");\n";
					}
				}
			}
		}
		for($mIndex=0;$mIndex < $numrows;$mIndex++)
			if($mAlldata[$mIndex][2]==$id && $mAlldata[$mIndex][3]==0 && @$menucategoriesatroot!=1) mwritemenulevel($mAlldata[$mIndex][0],$itlevel+1);
	}
}
function writesubmenus(){
	global $menucategoriesatroot;
	$menucategoriesatroot=2;
	mwritemenulevel(0,2);
}
if(mysql_num_rows($result) > 0){
	while($rs = mysql_fetch_row($result))
		$mAlldata[$numrows++]=$rs;
	mwritemenulevel(0,1);
}
mysql_free_result($result);
?>