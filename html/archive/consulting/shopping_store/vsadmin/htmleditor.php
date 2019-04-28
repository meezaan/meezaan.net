<SCRIPT language="php">
session_cache_limiter('none');
session_start();
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protected under law as the intellectual property
//of Internet Business Solutions SL. Any use, reproduction, disclosure or copying
//of any kind without the express and written permission of Internet Business 
//Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
include "db_conn_open.php";
include "includes.php";
include "inc/languageadmin.php";
include "inc/incfunctions.php";
if(@$storesessionvalue=="") $storesessionvalue="virtualstore";
if(@$_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE){
	if(@$_SERVER["HTTPS"] == "on" || @$_SERVER["SERVER_PORT"] == "443")$prot='https://';else $prot='http://';
	header('Location: '.$prot.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/login.php');
	exit;
}
$isprinter=FALSE;
</SCRIPT>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Edit HTML</title>
<script language="javascript" type="text/javascript" src="tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
	tinyMCE.init({
		mode : "textareas",
		theme : "advanced",
		plugins : "table,advimage,advlink,iespell,flash,searchreplace,print,contextmenu,paste,noneditable",
		theme_advanced_buttons1_add_before : "",
		theme_advanced_buttons1_add : "fontselect,fontsizeselect",
		theme_advanced_buttons2_add : "separator,forecolor,backcolor",
		theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator",
		theme_advanced_buttons3_add_before : "tablecontrols,separator",
		theme_advanced_buttons3_add : "iespell,flash,separator,print",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_path_location : "bottom",
<?php	if(@$htmleditorstyle!='') print 'content_css : "' . $htmleditorstyle . '",' . "\r\n";
		?>valid_elements : "*[*]",
		extended_valid_elements : "a[class|href|target|name|onclick]," +
			"embed[quality|type|pluginspage|width|height|src|align]," +
			"hr[class|width|size|noshade]," + 
			"img[class|src|border|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name]," +
			"object[classid|codebase|width|height|align]," +
			"param[name|value]," +
			"input[checked|class|disabled|id|name|type|value|size|maxlength|src|width|height|readonly|tabindex|onfocus|onblur|onchange|onselect]",
		external_link_list_url : "example_link_list.js",
		external_image_list_url : "example_image_list.js",
		flash_external_list_url : "example_flash_list.js",
		file_browser_callback : "fileBrowserCallBack",
		theme_advanced_resize_horizontal : false,
		theme_advanced_resizing : true
	});
	function fileBrowserCallBack(field_name, url, type, win) {
		// This is where you insert your custom filebrowser logic
		alert("Example of filebrowser callback: field_name: " + field_name + ", url: " + url + ", type: " + type);

		// Insert new URL, this would normaly be done in a popup
		win.document.forms[0].elements[field_name].value = "someurl.htm";
	}
	function updateContent(){		
		inst = window.opener.tinyMCE.instances['<?php print @$_GET['themceinstance']?>'];
		thisinst = tinyMCE.instances['mce_editor_0'];
		inst.getBody().innerHTML = thisinst.getBody().innerHTML;
		window.close();
	}
	function initcontent(){		
		inst = window.opener.tinyMCE.instances['<?php print @$_GET['themceinstance']?>'];
		thisinst = tinyMCE.instances['mce_editor_0'];
		if(thisinst.getBody()==null){
			window.setTimeout('initcontent();',500)
		}else{
			thisinst.getBody().innerHTML = inst.getBody().innerHTML;
		}
	}
</script>
</head>
<body onload="window.setTimeout('initcontent();',500)">
	<form name="source" onsubmit="saveContent();" action="#">
		<textarea name="thehtmlsrc" id="thehtmlsrc" rows="15" cols="100" style="width: 100%; height: 90%; font-family: 'Courier New',Courier,mono; font-size: 12px" dir="ltr" wrap="off"></textarea>
		<div class="mceActionPanel">
			<div style="float: left">
				<input type="button" name="insert" value="<?php print $yyUpdate?>" onclick="updateContent();" id="insert" />
			</div>
			<div style="float: right">
				<input type="button" name="cancel" value="<?php print $yyCancel?>" onclick="window.close();" id="cancel" />
			</div>
		</div>
	</form>
</body>
</html>
