<?php

function newAsso($tab){
	require_once("class.imageconverter.php");
	require_once("saveImage.php");
	if(empty($tab['nom'])) die();
	include("opendb.php");
	//Nouvelle entité
	$q1 = "INSERT INTO entite VALUES ()";
	$r1 = mysql_query($q1);
	if (!$r1){ 
		echo mysql_error();
		die();
	}
	$id = mysql_insert_id();
	
	// Asso
	$set = "(";
	$colonnes="(id,nom,description,url,cotisation)";
	//id
	$set.="'$id', ";
	//nom
	$set.="'".mysql_real_escape_string($tab['nom'])."', ";
	//Description
	$set.="'".mysql_real_escape_string($tab['description'])."', ";
	//url
	$set.="'".mysql_real_escape_string($tab['url'])."') ";
	$query = "INSERT INTO association ".$colonnes." VALUES ".$set." ";
	//echo $query;
	$results = mysql_query($query);
	if (!$results){ 
		echo mysql_error();
		die();
	}
	saveImage(mysql_insert_id(),"logo_asso");
	include("closedb.php");

}

function delAsso($id){
	include("opendb.php");
	if(!isset($id)) return;
	$query = "DELETE FROM entite WHERE id=".$id."";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}

function getAssociations($userid){
	if(!empty($_SESSION['user'])){
		if($_SESSION['privilege']==="1"){
			$query = "SELECT * FROM association ";
		} else {
			if (!empty($userid)) {
				$query = "SELECT * FROM association A, resp_asso R WHERE id_adh = '$userid' AND R.id_asso = A.id";
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

function modifAsso($tab){
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
	 saveImage($tab['id'],"logo_asso");
	//url
	if(!empty($tab['url'])) $set.="url='".mysql_real_escape_string($tab['url'])."', ";
	if ($set==="") return;
	$set=substr($set,0,-2);
	$query = "UPDATE association SET ".$set." WHERE id=".$tab['id']."";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}

function ajoutResponsableAsso($id_asso,$id_adh){
	include("opendb.php");
	$query = "INSERT into resp_asso(id_asso,id_adh) VALUES ('$id_asso.','$id_adh')";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();	
	include("closedb.php");
	
}
function delRespAsso($id_asso,$id_adh){
	include("opendb.php");
	$query = "DELETE FROM resp_asso WHERE id_asso='$id_asso' AND id_adh='$id_adh' ";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();	
	include("closedb.php");
}

function getResponsablesAsso($id_asso){

	$query = "SELECT * FROM adherent A ,resp_asso RA WHERE A.id=RA.id_adh AND RA.id_asso='$id_asso'  ";
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





?>