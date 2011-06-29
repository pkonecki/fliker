<?php

function getSections($userid){
	if(!empty($_SESSION['user'])){
		if($_SESSION['privilege']==="1"){
			$query = "SELECT A.id id_asso, A.nom nom_asso, S.* 
						FROM section S, association A, asso_section HS
						WHERE A.id=HS.id_asso
						AND HS.id_sec=S.id ";
		} else {
			if (!empty($userid)) {
				$query = "SELECT A.id id_asso, A.nom nom_asso, S.* 
						FROM section S, association A, asso_section HS
						WHERE A.id=HS.id_asso
						AND HS.id_sec=S.id
							AND
							(
							S.id IN (SELECT id_sec FROM resp_section WHERE id_adh = '".$userid."')
							OR A.id IN (SELECT id_asso FROM resp_asso WHERE id_adh = '".$userid."')
							)";
			}
			else return;
		}

	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results)){
			$tab[$row[id]] = $row;
	}
	include("closedb.php");
	return $tab;
	}
}

?>