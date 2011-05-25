<?php

function modifUser($tab){

	$champs = getChampsAdherents();
	$set = "";
	include("opendb.php");
	foreach($champs as $row){
		if($row[user_editable]==1){
			$set .= $row[nom]."=";
			if($row[type]==='varchar')
				$set .= "'".mysql_real_escape_string($tab[$row[nom]])."',";
			else
			if($row[type]==='date')
				$set .= "'".mysql_real_escape_string($tab[$row[nom]])."',";
			else
			if($row[type]==='tinyint'){
				if ($tab[$row[nom]]==='on') $values .= "1,";
				else $set .= "0,";
			}
			if($row[type]==='file'){
				if($tab[$row[nom]][name]===""){
					$set .= "0,";
				} else {
					$set .= "1,";
				}


			}

		}
	}

	$set .="last_modif='".date( 'Y-m-d H:i:s')."'";

	$query = "UPDATE adherent SET ".$set." WHERE email='".$_SESSION['user']."'";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();


	include("closedb.php");

}

?>