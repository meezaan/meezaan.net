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
		<td colspan="7">
			<img src="images/index_01.jpg" width="800" height="16" alt="Asif Nawaz Consulting -  Publications & Articles"></td>
	</tr>
	<tr>
		<td colspan="7">
			<img src="images/index_02.jpg" width="800" height="12" alt="Asif Nawaz Consulting -  Publications & Articles"></td>
	</tr>
	<tr>
		<td colspan="7">
			<img src="images/index_03.jpg" width="800" height="34" alt="Latest Publications"></td>
	</tr>
	<tr>
		<td colspan="2" rowspan="5">
			<img src="images/index_04.jpg" width="16" height="314" alt=""></td>
		<td rowspan="2">
			<img src="images/index_05.jpg" width="2" height="168" alt=""></td>
		<td colspan="2" background="images/index_06.jpg" width="332" height="167" valign="top" class="regular_black">
			<?php
				$query="Select publications.*, pub_categories.* from publications, pub_categories where
				publications.pub_cid = pub_categories.pub_cid
				order by pub_date limit 4";
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

echo '<b><a href="pdfs/';
echo $row['pub_pdf'];
echo '" class="pub_link">';
echo $row['pub_name'];
echo '</a></b>';
echo '<br />';
echo '<span class="pub_abouttext"><i>';
echo 'Written by ';
echo $row['pub_auth'];
echo '<br />';
echo 'Published on ';
echo $row['pub_date'];
echo ' in ';
echo $row['pub_cname'];
echo '</i>';
echo '<br />';
echo '<a href="view_publication.php?pub_id=';
echo $row['pub_id'];
echo '" class="pub_html"> View HTML Version</a>';
echo '</span><br /><br />';		

	}}	
			?>
		</td>
		<td colspan="2" rowspan="2">
			<img src="images/index_07.jpg" width="450" height="168" alt="Asif Nawaz Consulting -  Publications & Articles"></td>
	</tr>
	<tr>
		<td colspan="2">
			<img src="images/index_08.jpg" width="332" height="1" alt=""></td>
	</tr>
	<tr>
		<td colspan="5">
			<img src="images/index_09.jpg" width="784" height="23" alt="Asif Nawaz Consulting -  Publications & Articles"></td>
	</tr>
	<tr>
		<td colspan="2" rowspan="2" background="images/index_10.jpg" width="333" height="123" valign="top" class="pub_abouttext">
		 <form action="search_publications.php" method="post" enctype="multipart/form-data">
		<?php
		$sql_categories= "Select * from pub_categories";
$sql_categories= mysql_query($sql_categories) or die("Error : ".mysql_errno() . ": " . mysql_error()) ;
   while ($myrow = mysql_fetch_array($sql_categories))
      {
     $categories .= '<option value="' . $myrow['pub_cid'] . '">' .
$myrow['pub_cname'] . '</option>';
   }		?>
		<br />
		
		<select name="category" class="pub_abouttext">
                <option value="none">Pick a Category</option>
 <?= $categories ?> 	
		</select>	
		<br />
<br />
<input type="text" value="Enter Search Criteria (Author, Title, Content etc.)" class="pub_abouttext" size="65" name="searchterm" onfocus="value=''"/>	<br />
<i>Example:  Competitive Finance Strategies by Christopher Morgan</i>	<br />
<center>	<input type="submit" value="Search Publications" >	</center>
</form>

		
		</td>
		<td colspan="3">
			<img src="images/index_11.jpg" width="451" height="1" alt=""></td>
	</tr>
	<tr>
		<td colspan="3">
			<img src="images/index_12.jpg" width="451" height="122" alt="Asif Nawaz Consulting -  Publications & Articles"></td>
	</tr>
	<tr>
		<td colspan="7">
			<img src="images/index_13.jpg" width="800" height="3" alt=""></td>
	</tr>
	<tr>
		<td rowspan="2">
			<img src="images/index_14.jpg" width="15" height="51" alt=""></td>
		<td colspan="5" background="images/index_15.jpg" width="389" height="20" class="small">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="../index.html">Home</a> | <a href="../store/index.html">Shopping Store</a> | <a href="../about/contact.html">Contact</a> | <a href="../sitemap.html">Sitemap</a></td>
		<td rowspan="2">
			<img src="images/index_16.jpg" width="396" height="51" alt=""></td>
	</tr>
	<tr>
		<td colspan="5">
			<img src="images/index_17.jpg" width="389" height="31" alt=""></td>
	</tr>
	<tr>
		<td colspan="7" background="images/index_18.jpg" width="800" height="16" class="small" align="center">
		 <a href="../privacy.html">Privacy Policy &amp; Terms of Use</a> | <a href="mailto:webmaster@asifnawaz.net">Contact Webmaster</a></td>
	</tr>

	<tr>
		<td>
			<img src="images/spacer.gif" width="15" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="1" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="2" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="331" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="1" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="54" height="1" alt=""></td>
		<td>
			<img src="images/spacer.gif" width="396" height="1" alt=""></td>
	</tr>
</table>
<!-- End ImageReady Slices -->
</body>
</html>