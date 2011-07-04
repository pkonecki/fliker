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

?>