<?php

function getChampsAdherents(){
	
	include("opendb.php");
	$query = "SELECT * FROM champs_adherent";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$champs = array();
	while($row = mysql_fetch_array($results)){
		$champs[$row[nom]] = $row;
	}
	include("closedb.php");
	return $champs;
}

?>