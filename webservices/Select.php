<?php

function getSelect($table){
	include("opendb.php");
	$query = "SELECT id,nom FROM {$GLOBALS['prefix_db']}$table ORDER BY nom";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results)){
			$tab[$row['id']] = $row['nom'];
	}
 	include("closedb.php");
	return $tab;
}

?>