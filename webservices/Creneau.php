<?php

function newCreneau($tab){
	require_once("class.imageconverter.php");
	require_once("saveImage.php");
	include("opendb.php");
	//Nouvelle entité
	$q1 = "INSERT INTO {$GLOBALS['prefix_db']}entite VALUES ()";
	$r1 = mysql_query($q1);
	if (!$r1){ 
		echo mysql_error();
		die();
	}
	$id = mysql_insert_id();
	
	$set = "(";
	$colonnes="(id,jour,debut,fin,lieu,id_act)";
	//id
	$set.="'$id', ";
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
	$query = "INSERT INTO {$GLOBALS['prefix_db']}creneau ".$colonnes." VALUES ".$set." ";
	//echo $query;
	$results = mysql_query($query);
	if (!$results){ 
		echo mysql_error();
		die();
	}
	include("closedb.php");

}

function delCreneau($id)
{
	include("opendb.php");
	if(!isset($id)) return;
	$query = "DELETE FROM {$GLOBALS['prefix_db']}entite WHERE id=".$id."";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}
function getCreneau($id){
	include("opendb.php");
	if(!isset($id)) return;
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}creneau WHERE id=$id ORDER BY debut ASC";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$row = mysql_fetch_array($results);
	return $row;
	include("closedb.php");

}

function getCreneaux($userid)
{
	if(!empty($_SESSION['user']))
	{
		if($_SESSION['privilege']==="1")
		{
			$query = "SELECT A.id id_asso, A.nom nom_asso, S.id id_sec, S.nom nom_sec, AC.id id_act, AC.nom nom_act, CR.id id_cre, CR.jour jour_cre, CR.debut debut_cre, CR.fin fin_cre, CR.lieu lieu
						FROM {$GLOBALS['prefix_db']}activite AC, {$GLOBALS['prefix_db']}creneau CR, {$GLOBALS['prefix_db']}section S, {$GLOBALS['prefix_db']}association A, {$GLOBALS['prefix_db']}asso_section HS
						WHERE CR.id_act=AC.id
						AND AC.id_sec=S.id
						AND A.id=HS.id_asso
						AND HS.id_sec=S.id 
						ORDER BY nom_sec, nom_act, CR.debut";
		}
		else
		{
			if (!empty($userid))
			{
				$query = "SELECT A.id id_asso, A.nom nom_asso, S.id id_sec, S.nom nom_sec, AC.id id_act, AC.nom nom_act, CR.id id_cre, CR.jour jour_cre, CR.debut debut_cre , CR.fin fin_cre, CR.lieu lieu
						FROM {$GLOBALS['prefix_db']}activite AC, {$GLOBALS['prefix_db']}creneau CR, {$GLOBALS['prefix_db']}section S, {$GLOBALS['prefix_db']}association A, {$GLOBALS['prefix_db']}asso_section HS
						WHERE CR.id_act=AC.id
						AND AC.id_sec=S.id
						AND A.id=HS.id_asso
						AND HS.id_sec=S.id
							AND
							(
							S.id IN (SELECT id_sec FROM {$GLOBALS['prefix_db']}resp_section WHERE id_adh = '$userid')
							OR AC.id IN (SELECT id_act FROM {$GLOBALS['prefix_db']}resp_act WHERE id_adh = '$userid')
							OR CR.id IN (SELECT id_cre FROM {$GLOBALS['prefix_db']}resp_cren WHERE id_adh = '$userid')
							OR A.id IN (SELECT id_asso FROM {$GLOBALS['prefix_db']}resp_asso WHERE id_adh = '$userid')
							/*OR CR.id IN (SELECT id_cre FROM adhesion WHERE id_adh = '$userid')*/
							)
							ORDER BY nom_sec, nom_act, CR.debut";
			}
			else
				return;
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
				$query = "SELECT * FROM {$GLOBALS['prefix_db']}creneau A WHERE A.id_act= ".$actid." ORDER BY debut ASC";
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
	$query = "UPDATE {$GLOBALS['prefix_db']}creneau SET ".$set." WHERE id=".$tab['id_cre']."";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}

function ajoutResponsableCre($id_cre,$id_adh){
	include("opendb.php");
	$query = "INSERT into {$GLOBALS['prefix_db']}resp_cren(id_cre,id_adh) VALUES ('$id_cre.','$id_adh')";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();	
	include("closedb.php");
	
}
function delRespCre($id_cre,$id_adh){
	include("opendb.php");
	$query = "DELETE FROM {$GLOBALS['prefix_db']}resp_cren WHERE id_cre='$id_cre' AND id_adh='$id_adh' ";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();	
	include("closedb.php");
}

function getResponsablesCre($id_cre){

	$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent A ,{$GLOBALS['prefix_db']}resp_cren RA WHERE A.id=RA.id_adh AND RA.id_cre='".$id_cre."'  ";
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

function getAllCreneaux(){
	
	$query = "SELECT A.id id_asso, A.nom nom_asso, S.id id_sec, S.nom nom_sec, AC.id id_act, AC.nom nom_act, CR.id id_cre, CR.jour jour_cre, CR.debut debut_cre, CR.fin fin_cre, CR.lieu lieu
						FROM {$GLOBALS['prefix_db']}activite AC, {$GLOBALS['prefix_db']}creneau CR, {$GLOBALS['prefix_db']}section S, {$GLOBALS['prefix_db']}association A, {$GLOBALS['prefix_db']}asso_section HS
						WHERE CR.id_act=AC.id
						AND AC.id_sec=S.id
						AND A.id=HS.id_asso
						AND HS.id_sec=S.id
						AND (
							A.id IN (SELECT id_asso FROM {$GLOBALS['prefix_db']}resp_asso )
							OR S.id IN (SELECT id_sec FROM {$GLOBALS['prefix_db']}resp_section )
							OR AC.id IN (SELECT id_act FROM {$GLOBALS['prefix_db']}resp_act )
							OR CR.id IN (SELECT id_cre FROM {$GLOBALS['prefix_db']}resp_cren )
							)
						ORDER BY nom_sec, nom_act, CR.debut";
		

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
?>