<?php

function newSection($tab){
	require_once("class.imageconverter.php");
	require_once("saveImage.php");
	if(empty($tab['nom'])) die('il faut un nom!');
	include("opendb.php");
	$set = "(";
	$colonnes="(nom,description,url)";
	//nom
	$set.="'".mysql_real_escape_string($tab['nom'])."', ";
	//Description
	$set.="'".mysql_real_escape_string($tab['description'])."', ";
	//url
	$set.="'".mysql_real_escape_string($tab['url'])."') ";
	$query = "INSERT INTO section ".$colonnes." VALUES ".$set." ";
	//echo $query;
	$results = mysql_query($query);
	if (!$results){ 
		echo mysql_error();
		die();
	}
	$query = "INSERT INTO asso_section (id_asso,id_sec) VALUES (".$tab['id_asso'].",".mysql_insert_id().")";
	$results = mysql_query($query);
	if (!$results){ 
		echo mysql_error();
		die();
	}
	saveImage(mysql_insert_id(),"logo_section");
	include("closedb.php");

}

?>