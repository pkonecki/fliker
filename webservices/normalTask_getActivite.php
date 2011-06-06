<?php

function getActivite($userid,$sectionid){
	if(empty($sectionid)) return;
	if(!empty($_SESSION['user'])){
		if($_SESSION['privilege']==="1"){
			$query = "SELECT * FROM `activite` where `id_sec` = '".$sectionid."'  ";
		} else {
			if (!empty($userid)) {
				$query = "SELECT * FROM `activite` A, `resp_act` R WHERE id_adh = '".$userid."' AND R.id_sec = S.id";
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