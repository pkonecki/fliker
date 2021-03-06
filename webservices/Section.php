<?php

function newSection($tab){
	require_once("class.imageconverter.php");
	require_once("saveImage.php");
	if(empty($tab['nom'])) die('il faut un nom!');
	include("opendb.php");
	$q1 = "INSERT INTO {$GLOBALS['prefix_db']}entite VALUES ()";
	$r1 = mysql_query($q1);
	if (!$r1){ 
		echo mysql_error();
		die();
	}
	$id = mysql_insert_id();
	
	$set = "('$id', ";
	$colonnes="(id,nom,description,url)";
	//nom
	$set.="'".mysql_real_escape_string($tab['nom'])."', ";
	//Description
	$set.="'".mysql_real_escape_string($tab['description'])."', ";
	//url
	$set.="'".mysql_real_escape_string($tab['url'])."') ";
	$query = "INSERT INTO {$GLOBALS['prefix_db']}section ".$colonnes." VALUES ".$set." ";
	//echo $query;
	$results = mysql_query($query);
	if (!$results){
		echo mysql_error();
		die();
	}
	$query = "INSERT INTO {$GLOBALS['prefix_db']}asso_section (id_asso,id_sec) VALUES (".$tab['id_asso'].",".$id.")";
	$results = mysql_query($query);
	if (!$results){
		echo mysql_error();
		die();
	}
	
	saveImage(mysql_insert_id(),"logo_section");
	include("closedb.php");
	
	modifFamilleSec($id, 1); // 1 == Famille Par Défault
	
}


function delSection($id)
{
	include("opendb.php");
	if(!isset($id)) return;
	$query = "DELETE FROM {$GLOBALS['prefix_db']}section WHERE id=".$id."";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");
}

function getSections($userid)
{
	if(!empty($_SESSION['user']))
	{
		if($_SESSION['privilege']==="1")
		{
			$query = "SELECT A.id id_asso, A.nom nom_asso, S.* 
						FROM {$GLOBALS['prefix_db']}section S, {$GLOBALS['prefix_db']}association A, {$GLOBALS['prefix_db']}asso_section HS
						WHERE A.id=HS.id_asso
						AND HS.id_sec=S.id
						ORDER BY S.nom";
		}
		else
		{
			if (!empty($userid))
			{
				$query = "SELECT A.id id_asso, A.nom nom_asso, S.* 
						FROM {$GLOBALS['prefix_db']}section S, {$GLOBALS['prefix_db']}association A, {$GLOBALS['prefix_db']}asso_section HS
						WHERE A.id=HS.id_asso
						AND HS.id_sec=S.id
							AND
							(
							S.id IN (SELECT id_sec FROM {$GLOBALS['prefix_db']}resp_section WHERE id_adh = '$userid' AND promo = ".getParam('promo.conf').")
							OR A.id IN (SELECT id_asso FROM {$GLOBALS['prefix_db']}resp_asso WHERE id_adh = '$userid' AND promo = ".getParam('promo.conf').")
							)
						ORDER BY S.nom";
			}
			else
				return;
		}

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

function getSectionsByAsso($assoid){
	if(!empty($_SESSION['user'])){
			if (!empty($assoid)) {
				$query = "SELECT * FROM {$GLOBALS['prefix_db']}section S, {$GLOBALS['prefix_db']}asso_section A WHERE A.id_asso=$assoid AND A.id_sec = S.id ORDER BY S.nom ASC";
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

function modifSection($tab){
	require_once("class.imageconverter.php");
	require_once("saveImage.php");
	include("opendb.php");
	//$set = "";
	//nom
	if(!empty($tab['nom'])) $set.="nom='".mysql_real_escape_string($tab['nom'])."', ";
	//Description
	if(!empty($tab['description'])) $set.="description='".mysql_real_escape_string($tab['description'])."', ";
	//logo
	//if(!empty($tab['logo_asso']))
	 saveImage($tab['id'],"logo_section");
	//url
	if(!empty($tab['url'])) $set.="url='".mysql_real_escape_string($tab['url'])."', ";
	if ($set==="") return;
	$set=substr($set,0,-2);
	$query = "UPDATE {$GLOBALS['prefix_db']}section SET ".$set." WHERE id=".$tab['id']."";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}
function ajoutResponsableSec($id_sec,$id_adh,$promo){
	include("opendb.php");
	$query = "INSERT into {$GLOBALS['prefix_db']}resp_section(id_sec,id_adh,promo) VALUES ('$id_sec.','$id_adh','$promo')";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();	
	include("closedb.php");
	
}
function delRespSec($id_sec,$id_adh,$promo){
	include("opendb.php");
	$query = "DELETE FROM {$GLOBALS['prefix_db']}resp_section WHERE id_sec='$id_sec' AND id_adh='$id_adh' AND promo='$promo' ";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();	
	include("closedb.php");
}

function getResponsablesSec($id_sec,$promo){

	$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent A ,{$GLOBALS['prefix_db']}resp_section RA WHERE A.id=RA.id_adh AND RA.id_sec='$id_sec' AND promo='".$promo."' ";
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


function getFamilleSec($id_sec){

	$query = "SELECT * FROM {$GLOBALS['prefix_db']}famille ORDER BY nom";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results)){
			$tab[$row['id']]['nom'] = $row['nom'];
			$tab[$row['id']]['select'] = "null";
			
			$query2 = "SELECT * FROM {$GLOBALS['prefix_db']}famille_section WHERE id_sec=".$id_sec."";
			$results2 = mysql_query($query2);
			if (!$results2) echo mysql_error();
			while($row2 = mysql_fetch_array($results2)){
			if($row2['id_famille']==$row['id']){$tab[$row['id']]['select'] = "select";}
			}
	}
	include("closedb.php");
	return $tab;
	
}


function modifFamilleSec($id_sec, $id_famille){
	include("opendb.php");
	$query = "INSERT into {$GLOBALS['prefix_db']}famille_section(id_famille,id_sec) VALUES ('$id_famille.','$id_sec')";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();	
	include("closedb.php");
}

function suppressionFamilleSec($id_sec, $id_famille){
	include("opendb.php");
	$query = "DELETE FROM {$GLOBALS['prefix_db']}famille_section WHERE id_sec='$id_sec' AND id_famille='$id_famille' ";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();	
	include("closedb.php");
}

?>