<?php
include ('includes/db_conn.php'); //Establish DB Connection
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../site.css" rel="stylesheet" type="text/css">
<link href="../menu.css" rel="stylesheet" type="text/css">
<!-- to fix IE CSS dropdown problems -->
<script type="text/javascript"><!--//--><![CDATA[//><!--
sfHover = function() {
	var sfEls = document.getElementById("navmain6").getElementsByTagName("LI");
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
<title>Asif Nawaz Consulting > Current Projects</title>
</head>

<body bgcolor="#900000"  leftmargin="0" marginwidth="0" marginheight="0">
<table width="100%" cellspacing="0">
<tr>
<td colspan="3" width="100%" bgcolor="#ffffff" class="heading" align="center"> ASIF NAWAZ CONSULTING 
</td>
</tr>
<tr>
<td width="68" bgcolor="#900000" border="0">&nbsp;</td>
<td bgcolor="#900000" width="650" >
<ul id="navmain6">
<li><a href="../../about/index.html">about asif N.</a>
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

	<li><a href="../publications/index.php">publications</a>
	</li>
	
	<li><a href="index.php">current projects</a>
	</li>
	
	<li><a href="../shopping_store/index.php">shop online</a>
	</li>

</ul>
</td>
<td width="30%" bgcolor="#900000">&nbsp;</td>
</tr>
</table>

<!-- ImageReady Slices (past_projects.psd) -->
<table id="Table_01" width="751" height="435" border="0" cellpadding="0" cellspacing="0">

	<tr>
		<td colspan="5">
			<img src="images/index_03.jpg" width="750" height="32" alt="Current Projects - Asif Nawaz Consulting"></td>
		<td>
			<img src="images/spacer.gif" width="1" height="32" alt=""></td>
	</tr>
	<tr>
		<td rowspan="5" bgcolor="#FFFFFF"></td>
		<td colspan="4">
			<img src="images/index_05.jpg" width="724" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="1" height="1" alt=""></td>
	</tr>
	<tr>
		<td colspan="2">
			<img src="images/index_06.jpg" width="696" height="22" alt=""></td>
		<td colspan="2" rowspan="2" bgcolor="#FFFFFF"></td>
		<td>
			<img src="images/spacer.gif" width="1" height="22" alt=""></td>
	</tr>
	<tr>
		<td colspan="2" background="images/index_08.jpg" width="696" height="327" valign="top" class="regular_black">
		<!-- This is where the php code goes -->
		<?php
		$query="Select projects.*, ptypes.* from projects, ptypes where 
		 ptypes.ptypeid=projects.ptypeid AND
		 projects.pstatusid='1' ";
		$result = mysql_query($query);
if (!$result) {
print "There was a database error when executing <pre>$query</pre>.  <br /><br />Please help us resolve the issue by copying this error report and email it to webmaster@nausheensheikh.com.  Thank you.";
print mysql_error();
}
else
{
$num_results = mysql_num_rows($result);

for ($i=0; $i <$num_results; $i++)
{
$row = mysql_fetch_array($result);
echo '<table width="100%">';
echo '<tr>';
echo'<td class="regular_black" width"50%">';
echo $i+1;
echo '.';
echo '&nbsp;';
echo '<a href="../current_projects/displayproject.php?pid=';
echo htmlspecialchars(stripslashes($row['pid']));
echo '" class="intext">';
echo '&nbsp;';
echo htmlspecialchars(stripslashes($row['pname']));
echo '</a>'; 
echo '<br />';
echo '<b>Project Type:</b> ';
echo $row['ptype'];
echo ' <br />
<b>Client:</b> ';
echo $row['pclient'];
echo '<br />';
echo '</td>';
echo '<td align="center" width="50%">';
echo '<a href="displayproject.php?pid=';
echo htmlspecialchars(stripslashes($row['pid']));
echo '" class="intext">';
echo '<img src="../project_photos/'; 
echo htmlspecialchars(stripslashes($row['pimage_sm']));
echo '" border="0" ALT=" ';
echo $row['pname'];
echo ' - Asif Nawaz Consulting"';
echo '> </a>
</td>
</tr>';
echo '<br /><br />';
} } 
		echo '</table>';
		?>
		
		
		
		</td>
		<td>
			<img src="images/spacer.gif" width="1" height="327" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="images/index_09.jpg" width="3" height="1" alt=""></td>
		<td colspan="2" rowspan="2">
			<img src="images/index_10.jpg" width="694" height="13" alt=""></td>
		<td rowspan="2">
			<img src="images/index_11.jpg" width="27" height="13" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="1" height="1" alt=""></td>
	</tr>
	<tr>
		<td rowspan="2">
			<img src="images/index_12.jpg" width="3" height="16" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="1" height="12" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="images/index_13.jpg" width="26" height="4" alt=""></td>
		<td colspan="3">
			<img src="images/index_14.jpg" width="721" height="4" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="1" height="4" alt=""></td>
	</tr>
	<tr>
		<td colspan="5" background="images/index_15.jpg" width="750" height="15" class="small">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="../index.html">Home</a> | <a href="../store/index.html">Shopping Store</a> | <a href="../about/contact.html">Contact</a> | <a href="../sitemap.html">Sitemap</a></td>
		<td>
			<img src="images/spacer.gif" width="1" height="15" alt=""></td>
	</tr>
	<tr>
		<td colspan="5">
			<img src="images/index_16.jpg" width="750" height="29" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="1" height="29" alt=""></td>
	</tr>
	<tr>
		<td colspan="5" background="images/index_17.jpg" width="750" height="20" class="small" align="center">
		 <a href="../privacy.html">Privacy Policy &amp; Terms of Use</a> | <a href="mailto:webmaster@asifnawaz.net">Contact Webmaster</a></td>
		<td>
			<img src="images/spacer.gif" width="1" height="20" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="images/spacer.gif" width="26" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="3" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="693" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="1" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="27" height="1" alt=""></td>
		<td></td>
	</tr>
</table>
<!-- End ImageReady Slices -->