<?php

function newAdherent($tab){
	$champs = getChampsAdherents();
	$colonnes ="(";
	$values ="(";
	include("opendb.php");
	foreach($champs as $row){
		if($row[inscription]==1){

			if($row[type]==='varchar'){
				$colonnes .= $row[nom].",";
				$values .= "'".mysql_real_escape_string($tab[$row[nom]])."',";
			}
			else
			if($row[type]==='date'){
				$colonnes .= $row[nom].",";
				$values .= "'".mysql_real_escape_string($tab[$row[nom]])."',";
			}
			else
			if($row[type]==='tinyint'){
				$colonnes .= $row[nom].",";
				if ($tab[$row[nom]]==='on') $values .= "1,";
				else $values .= "0,";
			}
			else
			if($row[type]==='file'){
			$colonnes .= $row[nom].",";
				if($tab[$row[nom]][name]===""){
					$values .= "0,";
				} else {
					$values .= "1,";
				}


			}
			if($row[type]==='select'){
				$colonnes .= 'id_'.$row[nom].",";
				$values .= "'".mysql_real_escape_string($tab['id_'.$row[nom]])."',";
			}
		}
	}
	$activationKey=mt_rand() . mt_rand() . mt_rand() . mt_rand() . mt_rand();
	print $activationKey;
	$colonnes .= "date_creation,last_modif,activationkey,";
	$values .= "'".date( 'Y-m-d H:i:s')."','". date( 'Y-m-d H:i:s')."','".$activationKey."',";



	$colonnes = substr($colonnes,0,-1);
	$values = substr($values,0,-1);
	$colonnes .=")";
	$values .=")";
	$query = "INSERT INTO adherent ".$colonnes." VALUES ".$values;
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	//send mail
	$to      = $tab[email];
	$subject = " Votre inscription à l'ASESCO";
	$message = "Bienvenue à l'ASESCO!\r\rVous, ou quelqu'un utilisant votre adresse email, êtes inscrit au site internet de l'ASESCO. Vous pouvez valider votre inscription en cliquant sur le lien suivant: \r".getParam('url_site')."validate.php?$activationKey\r\r Si c'est une erreur, ignorez tout simplement cet email et nous ne conserveont pas votre adresse.\r\rCordialement, l'équipe de l'ASESCO";
	$headers = 'From: '.getParam('admin_email') . "\r\n" .

    'Reply-To: '.getParam('contact_email') . "\r\n" .

    'X-Mailer: PHP/' . phpversion();
	mail($to, $subject, $message, $headers);

	include("closedb.php");

}


function getAdherent($user){
	$return = array();
	$tab = getChampsAdherents();
	include("opendb.php");

	$query = "SELECT * FROM `adherent` WHERE `id` = '".$user."'";

	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$row = mysql_fetch_assoc($results);
	foreach($tab as $champ){
			if($champ['type']==='select'){
				$return[$champ['nom']]=$row['id_'.$champ['nom']];
			}
			else {
				$return[$champ['nom']]=$row[$champ['nom']];
			}
	}
	include("closedb.php");
	return $return;
}

function getChampsAdherents(){

	include("opendb.php");
	$query = "SELECT * FROM champs_adherent ORDER BY ordre ASC";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$champs = array();
	while($row = mysql_fetch_array($results)){
		$champs[$row[nom]] = $row;
	}
	include("closedb.php");
	return $champs;
}

function modifAdherent($tab){
	require("class.imageconverter.php");
	require("saveImage.php");
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
				if (isset($tab[$row[nom]])) $set .= "1,";
				else $set .= "0,";
			}
			if($row[type]==='file'){
				if($tab[$row[nom]][name]===""){
					$set .= "0,";
				} else {
					$set .= "1,";
					saveImage($tab['email'],$row[nom]);

				}


			} else
			if($row[type]==='select')
				$set .= "'".mysql_real_escape_string($tab['id_'.$row[nom]])."',";

		}
	}

	$set .="last_modif='".date( 'Y-m-d H:i:s')."'";

	$query = "UPDATE adherent SET ".$set." WHERE id='".$tab['id_adh']."'";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();


	include("closedb.php");

}

function getAdherents(){
	$query = "SELECT * FROM adherent WHERE active=1 ORDER BY nom ";
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

function getAdherentsByCreneau($id_cre,$promo){
	$query = "SELECT ADH.* FROM adherent ADH, adhesion AD WHERE AD.id_adh=ADH.id  AND AD.id_cre=$id_cre AND AD.promo=$promo ORDER BY nom ";
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

function getStatuts(){
	$query = "SELECT * FROM statut ORDER BY nom ";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results)){
			$tab[$row['id']] = $row['nom'];
	}
	include("closedb.php");
	return $tab;


}



function getAssos(){
	$query = "SELECT * FROM association ";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results)){
			$tab[$row['id']] = $row['nom'];
	}
	include("closedb.php");
	return $tab;
}

function getMyAdherents($userid){
	$query="SELECT  ADH.id 
		FROM activite AC, creneau CR, section S, association A, asso_section HS , adhesion AD, adherent ADH
		WHERE CR.id_act=AC.id
		AND AC.id_sec=S.id
		AND A.id=HS.id_asso
		AND HS.id_sec=S.id
		AND AD.id_cre=CR.id
		AND ( AD.id_adh=ADH.id
			OR
			(
				ADH.id IN
				(SELECT id_adh FROM resp_asso WHERE resp_asso.id_asso IN (SELECT id_asso FROM resp_asso WHERE id_adh = '$userid'))
				OR ADH.id IN
				(SELECT id_adh FROM resp_section WHERE resp_section.id_sec IN (SELECT id_sec FROM resp_section WHERE id_adh = '$userid'))
				OR ADH.id IN
				(SELECT id_adh FROM resp_act WHERE resp_act.id_act IN (SELECT id_act FROM resp_act WHERE id_adh = '$userid'))
				OR ADH.id IN
				(SELECT id_adh FROM resp_cren WHERE resp_cren.id_cre IN (SELECT id_cre FROM resp_cren WHERE id_adh = '$userid'))
			)
		)
		AND
		(
			S.id IN (SELECT id_sec FROM resp_section WHERE id_adh = '$userid')
			OR AC.id IN (SELECT id_act FROM resp_act WHERE id_adh = '$userid')
			OR CR.id IN (SELECT id_cre FROM resp_cren WHERE id_adh = '$userid')
			OR A.id IN (SELECT id_asso FROM resp_asso WHERE id_adh = '$userid')
		)
		";
		
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results)){
			$tab[$row['id']] = $row['id'];
	}
	include("closedb.php");
	return $tab;
}

function getMyAssos($userid){
	if($userid==-1) $query="SELECT  A.*
		FROM association A";
	else $query="SELECT  A.*
		FROM association A, resp_asso RS
		WHERE A.id=RS.id_asso AND RS.id_adh=$userid
		";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results)){
		$tab[$row['id']] = $row['nom'];
	}
	include("closedb.php");
	return $tab;
}

function getSolde($id_adh,$promo){
	$mycrens=getCreneaux($_SESSION['uid']);
	$ads= getAdhesions($id_adh,$promo);
	foreach ($ads as $key => $value){
		if(!isset($mycrens[$value['id_cre']])) unset($ads[$key]);
	}
	$adh = getAdherent($id_adh);
	$tab = getFacture($ads,$adh['statut'],$promo);
	$p_sup = getPaiementsSup($id_adh);
	$solde=0;
	foreach($tab['assos'] as $row) $solde+=$row['valeur']-$p_sup[$row['id']];
	foreach($tab['secs'] as $row) $solde+=$row['valeur']-$p_sup[$row['id']];	
	foreach($tab['acts'] as $row) $solde+=$row['valeur']-$p_sup[$row['id']];	
	foreach($tab['cres'] as $row) $solde+=$row['valeur']-$p_sup[$row['id']];
	return -$solde;
}
function setNumCarte($num,$adh){
	$q="UPDATE adherent SET numcarte=$num WHERE id=$adh";
	include("opendb.php");
	$results = mysql_query($q);
	if (!$results) echo mysql_error();
	include("closedb.php");
}

function getMaxNumCarte(){
	$query= "SELECT MAX(numcarte) max FROM adherent ";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	$ret = mysql_result($results,0,"max");
	include("closedb.php");
	$ret++;
	return $ret;
}

?>