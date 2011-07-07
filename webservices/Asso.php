<?php

function newAsso($tab){
	require_once("class.imageconverter.php");
	require_once("saveImage.php");
	if(empty($tab['nom'])) die();
	include("opendb.php");
	$set = "(";
	$colonnes="(nom,description,url,cotisation)";
	//nom
	$set.="'".mysql_real_escape_string($tab['nom'])."', ";
	//Description
	$set.="'".mysql_real_escape_string($tab['description'])."', ";
	//url
	$set.="'".mysql_real_escape_string($tab['url'])."', ";
	//cotisation
	$set.="'".mysql_real_escape_string($tab['cotisation'])."') ";
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
	$query = "DELETE FROM association WHERE id=".$id."";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}

function getAssociations($userid){
	if(!empty($_SESSION['user'])){
		if($_SESSION['privilege']==="1"){
			$query = "SELECT * FROM `association` ";
		} else {
			if (!empty($userid)) {
				$query = "SELECT * FROM `association` A, `resp_asso` R WHERE `id_adh` = '".$userid."' AND R.id_asso = A.id";
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
	//cotisation
	if(!empty($tab['cotisation'])) $set.="cotisation='".mysql_real_escape_string($tab['cotisation'])."', ";
	if ($set==="") return;
	$set=substr($set,0,-2);
	$query = "UPDATE association SET ".$set." WHERE id=".$tab['id']."";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}
?>