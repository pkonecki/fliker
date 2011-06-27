<?php

function newAsso($tab){
	require_once("class.imageconverter.php");
	require_once("saveImage.php");
	if(empty($tab['nom'])) die();
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
	include("opendb.php");
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

?>