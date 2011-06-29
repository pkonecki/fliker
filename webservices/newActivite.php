<?php

function newActivite($tab){
	require_once("class.imageconverter.php");
	require_once("saveImage.php");
	if(empty($tab['nom'])) die('il faut un nom!');
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
	
	
	include("opendb.php");
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

?>