
<?
$subject="from ".$_GET['your_name'];
$headers= "From: ".$_GET['your_email']."\n";
 $headers.='Content-type: text/html; charset=iso-8859-1';
mail($_GET['recipient'], $subject,  "
<html>
<head>
 <title>Contact letter</title>
</head>
<body>

<br>
  ".$_GET['message']."
</body>
</html>" , $headers);
echo ("Your message was successfully sent!");
?>
<script>
	resizeTo(300, 300)
	//window.close()
</script>