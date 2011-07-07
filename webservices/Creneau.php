<?php

function newCreneau($tab){
	require_once("class.imageconverter.php");
	require_once("saveImage.php");
	include("opendb.php");
	$set = "(";
	$colonnes="(jour,debut,fin,lieu,id_act)";
	//Jour
	$set.="'".mysql_real_escape_string($tab['jour_cre'])."', ";
	//Debut
	$set.="'".mysql_real_escape_string($tab['debut_cre'])."', ";
	//Fin
	$set.="'".mysql_real_escape_string($tab['fin_cre'])."', ";
	//Lieu
	$set.="'".mysql_real_escape_string($tab['lieu'])."', ";
	//Id_act
	$set.="'".mysql_real_escape_string($tab['id_act'])."') ";
	$query = "INSERT INTO creneau ".$colonnes." VALUES ".$set." ";
	//echo $query;
	$results = mysql_query($query);
	if (!$results){ 
		echo mysql_error();
		die();
	}
	include("closedb.php");

}

function delCreneau($id){
	include("opendb.php");
	if(!isset($id)) return;
	$query = "DELETE FROM creneau WHERE id=".$id."";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}

function getCreneaux($userid){
	if(!empty($_SESSION['user'])){
		if($_SESSION['privilege']==="1"){
			$query = "SELECT A.id id_asso, A.nom nom_asso, S.id id_sec, S.nom nom_sec, AC.id id_act, AC.nom nom_act, CR.id id_cre, CR.jour jour_cre, CR.debut debut_cre, CR.fin fin_cre, CR.lieu lieu
						FROM activite AC, creneau CR, section S, association A, asso_section HS
						WHERE CR.id_act=AC.id
						AND AC.id_sec=S.id
						AND A.id=HS.id_asso
						AND HS.id_sec=S.id 
						ORDER BY nom_sec";
		} else {
			if (!empty($userid)) {
				$query = "SELECT A.id id_asso, A.nom nom_asso, S.id id_sec, S.nom nom_sec, AC.id id_act, AC.nom nom_act, CR.id id_cre, CR.jour jour_cre, CR.debut debut_cre , CR.fin fin_cre, CR.lieu lieu
						FROM activite AC, creneau CR, section S, association A, asso_section HS
						WHERE CR.id_act=AC.id
						AND AC.id_sec=S.id
						AND A.id=HS.id_asso
						AND HS.id_sec=S.id
							AND
							(
							S.id IN (SELECT id_sec FROM resp_section WHERE id_adh = '".$userid."')
							OR AC.id IN (SELECT id_act FROM resp_act WHERE id_adh = '".$userid."')
							OR CR.id IN (SELECT id_cre FROM resp_cren WHERE id_adh = '".$userid."')
							OR A.id IN (SELECT id_asso FROM resp_asso WHERE id_adh = '".$userid."')
							)
							ORDER BY nom_sec
						";
			}
			else return;
		}

		include("opendb.php");
		$results = mysql_query($query);
		if (!$results) echo mysql_error();
		$tab = array();
		while($row = mysql_fetch_array($results)){
			$tab[$row['id_cre']] = $row;
		}
		include("closedb.php");
		return $tab;
	}
}

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

function modifCreneau($tab){
	require_once("class.imageconverter.php");
	require_once("saveImage.php");
	include("opendb.php");
	//$set = "";
	//Jour
	if(!empty($tab['jour_cre'])) $set.="jour='".mysql_real_escape_string($tab['jour_cre'])."', ";
	//Debut
	if(!empty($tab['debut_cre'])) $set.="debut='".mysql_real_escape_string($tab['debut_cre'])."', ";
	if ($set==="") return;
	//Fin
	if(!empty($tab['fin_cre'])) $set.="fin='".mysql_real_escape_string($tab['fin_cre'])."', ";
	//Lieu
	if(!empty($tab['lieu'])) $set.="lieu='".mysql_real_escape_string($tab['lieu'])."', ";
	$set=substr($set,0,-2);
	$query = "UPDATE creneau SET ".$set." WHERE id=".$tab['id_cre']."";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}

?>