<?php

function getSectionsByAsso($assoid){
	if(!empty($_SESSION['user'])){
			if (!empty($assoid)) {
				$query = "SELECT * FROM `section` S, `asso_section` A WHERE A.id_asso= ".$assoid." AND A.id_sec = S.id";
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