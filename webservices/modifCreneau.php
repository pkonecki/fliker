<?php

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