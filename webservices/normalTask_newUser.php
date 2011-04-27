<?php

function newUser($tab){
	//include("normalTask_getChampsAdherents.php");
	$champs = getChampsAdherents();
	$colonnes ="(";
	$values ="(";
	include("opendb.php");
	foreach($champs as $row){
		if($row[inscription]==1){
			$colonnes .= $row[nom].",";
			if($row[type]==='varchar')
			$values .= "'".mysql_real_escape_string($tab[$row[nom]])."',";
			else
			if($row[type]==='date')
			$values .= "'".mysql_real_escape_string($tab[$row[nom]])."',";
			else
			if($row[type]==='tinyint'){
				if ($tab[$row[nom]]==='on') $values .= "1,";
				else $values .= "0,";
			}
			
		}
	}
	$colonnes .= "pre_inscription,last_modif,";
	$values .= "'".date( 'Y-m-d H:i:s')."','". date( 'Y-m-d H:i:s')."',";
	
	
	
	$colonnes = substr($colonnes,0,-1);
	$values = substr($values,0,-1);
	$colonnes .=")";
	$values .=")";
	$query = "INSERT INTO adherent ".$colonnes." VALUES ".$values;
	echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();

	include("closedb.php");

}

?>