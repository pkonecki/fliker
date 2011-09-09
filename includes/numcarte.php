<?php 
include("paths.php");
if(empty($_GET['numcarte'])){
	echo "true";
	die();
}
include("opendb.php");
$res = mysql_query("SELECT numcarte FROM {$GLOBALS['prefix_db']}adherent WHERE numcarte='".mysql_real_escape_string($_GET['numcarte'])."'");
if(mysql_num_rows($res)){
	echo "false";

} else {
	echo "true";
}
include("closedb.php");
?>