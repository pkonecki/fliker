<?php
include("../includes/paths.php");
include("opendb.php");
if(mysql_num_rows(mysql_query("SELECT email FROM {$GLOBALS['prefix_db']}adherent WHERE email='".mysql_real_escape_string($_GET['email'])."'"))){
	echo "false";

} else {
	echo "true";
}
include("closedb.php");

?>