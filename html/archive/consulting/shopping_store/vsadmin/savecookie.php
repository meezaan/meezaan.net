<?php
if(trim(@$_GET["id1"]) != "" && trim(@$_GET["id2"]) != ""){
setcookie("id1",@$_GET["id1"],time()+16000000, "/");
setcookie("id2",@$_GET["id2"],time()+16000000, "/");
}elseif(trim(@$_GET["PARTNER"]) != ""){
setcookie("PARTNER",trim(@$_GET["PARTNER"]),time()+(60*60*24*(int)@$_GET["EXPIRES"]), "/");
}elseif(trim(@$_GET["WRITECKL"]) != ""){
setcookie("WRITECKL",trim(@$_GET["WRITECKL"]),time()+(60*60*24*365), "/");
setcookie("WRITECKP",trim(@$_GET["WRITECKP"]),time()+(60*60*24*365), "/");
}elseif(trim(@$_GET["DELCK"]) == "yes"){
setcookie ('WRITECKL', '', (time() - 2592000), '/', '', 0);
setcookie ('WRITECKP', '', (time() - 2592000), '/', '', 0);
}elseif(trim(@$_GET["WRITECLL"]) <> ""){
	$thetimelim=0;
	if(trim(@$_GET["permanent"]) == "Y")
		$thetimelim = (time()+(60*60*24*365));
	setcookie ('WRITECLL', trim(@$_GET["WRITECLL"]), $thetimelim, '/');
	setcookie ('WRITECLP', trim(@$_GET["WRITECLP"]), $thetimelim, '/');
}elseif(trim(@$_GET["DELCLL"]) <> ""){
	setcookie ('WRITECLL', '', time() - 2592000, '/', '', 0);
	setcookie ('WRITECLP', '', time() - 2592000, '/', '', 0);
}
flush();
?>
