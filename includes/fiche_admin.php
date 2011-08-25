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
if($_POST['action']==="setparam"){
	setParam($_POST['id'],htmlspecialchars($_POST['valeur']));
}

$params=getParams();
$table_config="<table id=\"table_config\" >";
foreach($params as $key => $value){
	$table_config.="<FORM action=\"index.php?page=9\" method=\"POST\">
					<tr><td>$key</td><td><input type=\"text\" name=\"valeur\" value=\"$value\"></input></td><td><input type=\"submit\" /></td></tr>
					<input type=\"hidden\" name=\"action\" value=\"setparam\">
					<input type=\"hidden\" name=\"id\" value=\"$key\">
					</FORM>";
}
$table_config.="</table>";
print $table_config;
?>
<script type="text/javascript">

</script>