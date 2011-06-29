<?php

function getActivitesBySection($sectionid){
	if(!empty($_SESSION['user'])){
			if (!empty($sectionid)) {
				$query = "SELECT * FROM `activite` A WHERE A.id_sec= ".$sectionid." ";
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