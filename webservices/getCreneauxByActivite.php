<?php

function getCreneauxByActivite($actid){
	if(!empty($_SESSION['user'])){
			if (!empty($actid)) {
				$query = "SELECT * FROM `creneau` A WHERE A.id_act= ".$actid." ";
			}
			else return;
		

	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results)){
			$tab[$row['id']] = $row;
	}
	include("closedb.php");
	return $tab;
	}
}

?>