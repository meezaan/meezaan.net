<?php 
$ERRORMESSAGE = $_REQUEST['errormessage'];
if ($ERRORMESSAGE != "") { ?>
<br />
<br />
<div id="error">
<?php echo $ERRORMESSAGE; ?>
</div>
<?php 
}
?>