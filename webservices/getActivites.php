<?php

function getActivites($userid){
	if(!empty($_SESSION['user'])){
		if($_SESSION['privilege']==="1"){
			$query = "SELECT * FROM `activite`  ";
		} else {
			if (!empty($userid)) {
				$query = "SELECT * FROM `activite` A, `resp_act` R WHERE id_adh = '".$userid."' AND R.id_act = A.id";
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