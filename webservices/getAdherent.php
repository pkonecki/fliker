<?php
require_once("getChampsAdherents.php");
function getAdherent($user){
	if(!(strcmp($_SESSION['user'],"") == 0)){


	$tab = getChampsAdherents();
	include("opendb.php");

	$query = "SELECT * FROM `adherent` WHERE `email` = '".$user."'";

	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$row = mysql_fetch_assoc($results);
	foreach($tab as $champ){
		if ($champ[user_editable]==1) {
			$_SESSION[$champ['nom']]=$row[$champ['nom']];
		}
	}
	include("closedb.php");

	}
}

?>