<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
session_start();
if(!($_SESSION['privilege']==='1')){
	print "Vous n'avez pas accès à cette page.";
	print $die_footer;
	die();
}

if(isset($_GET['promo'])) {
	$promo=$_GET['promo'];
} else {
	$promo=$current_promo;
}
$params=getParams();

$table_config="<table id=\"table_config\" >";
foreach($params as $key => $value){
	$table_config.="<tr><td>$key</td><td>$value</td></tr>";
}
$table_config.="</table>";
print $table_config;
?>
<script type="text/javascript">

</script>