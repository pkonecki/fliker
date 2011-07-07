<?php

function newActivite($tab){
	require_once("class.imageconverter.php");
	require_once("saveImage.php");
	
	if(empty($tab['nom'])) die('il faut un nom!');
	include("opendb.php");
	$set = "(";
	$colonnes="(nom,description,id_sec,url)";
	//nom
	$set.="'".mysql_real_escape_string($tab['nom'])."', ";
	//Description
	$set.="'".mysql_real_escape_string($tab['description'])."', ";
	//id_sec
	$set.="'".mysql_real_escape_string($tab['id_sec'])."', ";
	//url
	$set.="'".mysql_real_escape_string($tab['url'])."') ";
	$query = "INSERT INTO activite ".$colonnes." VALUES ".$set." ";
	//echo $query;
	$results = mysql_query($query);
	if (!$results){ 
		echo mysql_error();
		die();
	}
	saveImage(mysql_insert_id(),"logo_act");
	include("closedb.php");

}

function delActivite($id){
	include("opendb.php");
	if(!isset($id)) return;
	$query = "DELETE FROM activite WHERE id=".$id."";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}

function getActivites($userid){
	if(!empty($_SESSION['user'])){
		if($_SESSION['privilege']==="1"){
			$query = "SELECT A.id id_asso, A.nom nom_asso, S.id id_sec, S.nom nom_sec, AC.* 
						FROM activite AC, section S, association A, asso_section HS
						WHERE AC.id_sec=S.id
						AND A.id=HS.id_asso
						AND HS.id_sec=S.id";
		} else {
			if (!empty($userid)) {
				$query = "SELECT A.id id_asso, A.nom nom_asso, S.id id_sec, S.nom nom_sec, AC.* 
						FROM activite AC, section S, association A, asso_section HS
						WHERE AC.id_sec=S.id
						AND A.id=HS.id_asso
						AND HS.id_sec=S.id
							AND
							(
							S.id IN (SELECT id_sec FROM resp_section WHERE id_adh = '".$userid."')
							OR AC.id IN (SELECT id_act FROM resp_act WHERE id_adh = '".$userid."')
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

function modifActivite($tab){
	require_once("class.imageconverter.php");
	require_once("saveImage.php");
	include("opendb.php");
	//$set = "";
	//nom
	if(!empty($tab['nom'])) $set.="nom='".mysql_real_escape_string($tab['nom'])."', ";
	//Description
	if(!empty($tab['description'])) $set.="description='".mysql_real_escape_string($tab['description'])."', ";
	//logo
	 saveImage($tab['id'],"logo_act");
	//url
	if(!empty($tab['url'])) $set.="url='".mysql_real_escape_string($tab['url'])."', ";
	if ($set==="") return;
	$set=substr($set,0,-2);
	$query = "UPDATE activite SET ".$set." WHERE id=".$tab['id']."";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}

?>