<?php
include ('includes/db_conn.php');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../site.css" rel="stylesheet" type="text/css">
<link href="../menu.css" rel="stylesheet" type="text/css">
<!-- to fix IE CSS dropdown problems -->
<script type="text/javascript"><!--//--><![CDATA[//><!--
sfHover = function() {
	var sfEls = document.getElementById("navmain4").getElementsByTagName("LI");
	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onmouseover=function() {
			this.className+=" sfhover";
		}
		sfEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", sfHover);

//--><!]]>
</script>
<title>Asif Nawaz Consulting > Publications</title>
</head>
<body bgcolor="#AA7037" leftmargin="0" >
<table width="100%" cellspacing="0">
<tr>
<td colspan="3" width="100%" bgcolor="#ffffff" class="heading" align="center"> ASIF NAWAZ CONSULTING 
</td>
</tr>
<tr>
<td width="68" bgcolor="#976432" border="0">&nbsp;</td>
<td bgcolor="#976432" width="650" >
<ul id="navmain4">
<li><a href="../about/index.html">about asif N.</a>
		<ul>
			<li><a href="../about/mission.html">mission</a></li>
			<li><a href="../about/strategy.html">strategy</a></li>
			<li><a href="../about/vision.html">vision</a></li>
			<li><a href="../about/contact.html">contact information</a></li>

		</ul>
	</li>
	<li><a href="../expertise/index.html">our expertise</a>
		<ul>
			<li><a href="../expertise/contacts.html">contacts</a></li>
			<li><a href="../expertise/past_projects.php">past projects</a></li>
			<li><a href="../expertise/industries.html">industries</a></li>
			<li><a href="../expertise/functions.html">functions</a></li>

		</ul>
	</li>

	<li><a href="index.php">publications</a>
	</li>
	
	<li><a href="../current_projects/index.php">current projects</a>
	</li>
	
	<li><a href="../shopping_store/">shop online</a>
	</li>

</ul>

</td>
<td width="30%" bgcolor="#976432">&nbsp;</td>
</tr>
</table>
<!-- ImageReady Slices (publications.psd) -->
<table id="Table_01" width="800" height="447" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="3">
			<img src="images/view_project_01.jpg" width="800" height="18" alt="View Publication"></td>
	</tr>
	<tr>
		<td rowspan="7" bgcolor="#95612F" valign="bottom">
			<img src="images/view_project_02.jpg" width="35" height="70" alt=""></td>
		<td>
			<img src="images/view_project_03.jpg" width="743" height="21" alt="View Publication"></td>
		<td rowspan="7" valign="bottom" bgcolor="#95612F">
			<img src="images/view_project_04.jpg" width="22" height="70" alt=""></td>
	</tr>
	<tr>
		<td bgcolor="#95612F" width="743" height="328" class="pub_view" valign="top">
		<?php
		$pub_id = $_REQUEST["pub_id"];
		$query="Select publications.*, pub_categories.* from publications, pub_categories WHERE
				publications.pub_cid = pub_categories.pub_cid AND
				pub_id='".$pub_id."'";
				
		$result = mysql_query($query);
if (!$result) {
print "There was a database error when executing <pre>$query</pre>.  <br /><br />Please help us resolve the issue by copying this error report and email it to webmaster@asifnawaz.net.  Thank you.";
print mysql_error();
}
else
{
$num_results = mysql_num_rows($result);

for ($i=0; $i <$num_results; $i++)  {
$row = mysql_fetch_array($result);

if

($row['pub_mag'] == '') {

echo '<span class="pub_head"> <b>';
echo $row['pub_name'];
echo '</span></b>';
echo '<br />';
echo 'Written by <i>';
echo $row['pub_auth'];
echo '.  <br /><br />Published on <a href="http://www.asifnawaz.net/publications">Asif Nawaz Consulting Publications</a> on ';
echo $row ['pub_date'];
echo '.';
echo '</i> <br /><br />';
echo $row['pub_content'];
}
else  {
echo '<span class="pub_head"> <b>';
echo $row['pub_name'];
echo '</span></b>';
echo '<br />';
echo 'Written by <i>';
echo $row['pub_auth'];
echo '.<br /><br />  Published on <a href="http://www.asifnawaz.net/publications">Asif Nawaz Consulting Publications</a> on ';
echo $row ['pub_date'];
echo '. <br /><br />This Article was originally published in <a href="';
echo $row['pub_maglink'];
echo '">';
echo $row['pub_mag'];
echo '</a> on ';
echo $row ['pub_magdate'];
echo '.';
echo '</i> <br /><br />';
echo $row['pub_content'];
}
} 
} 
		?>
		

		
		
		</td>
	</tr>
	<tr>
		<td>
			<img src="images/view_project_06.jpg" width="743" height="10" alt=""></td>
	</tr>
	<tr>
		<td background="images/view_project_07.jpg" width="743" height="21" class="small">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="../index.html">Home</a> | <a href="../store/index.html">Shopping Store</a> | <a href="../about/contact.html">Contact</a> | <a href="../sitemap.html">Sitemap</a></td>
	</tr>
	<tr>
		<td>
			<img src="images/view_project_08.jpg" width="743" height="16" alt=""></td>
	</tr>
	<tr>
		<td background="images/view_project_09.jpg" width="743" height="24" class="small" align="center">
		 <a href="../privacy.html">Privacy Policy &amp; Terms of Use</a> | <a href="mailto:webmaster@asifnawaz.net">Contact Webmaster</a></td>
	</tr>
	<tr>
		<td>
			<img src="images/view_project_10.jpg" width="743" height="9" alt=""></td>
	</tr>
	
</table>
<!-- End ImageReady Slices -->
</body>
</html>