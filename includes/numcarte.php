<?php 
include("paths.php");
include("opendb.php");
if(mysql_num_rows(mysql_query("SELECT {$GLOBALS['prefix_db']}numcarte FROM adherent WHERE numcarte='".mysql_real_escape_string($_GET['numcarte'])."'"))){
	echo "false";

} else {
	echo "true";
}
include("closedb.php");
?>