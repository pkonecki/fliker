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
	$colonnes .= "pre_inscription,last_modif,activationkey,";
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
	$message = "Bienvenue à l'ASESCO!\r\rVous, ou quelqu'un utilisant votre adresse email, êtes inscrit au site internet de l'ASESCO. Vous pouvez valider votre inscription en cliquant sur le lien suivant:\rhttp://fliker.dyndns.org/verify.php?$activationKey\r\rSi c'est une erreur, ignorez tout simplement cet email et nous ne conserveont pas votre adresse.\r\rCordialement, l'équipe de l'ASESCO";
	$headers = 'From: noreply@fliker.dyndns.org' . "\r\n" .

    'Reply-To: bureau@asesco.fr' . "\r\n" .

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
				if ($tab[$row[nom]]==='on') $values .= "1,";
				else $set .= "0,";
			}
			if($row[type]==='file'){
				if($tab[$row[nom]][name]===""){
					$set .= "0,";
				} else {
					$set .= "1,";
					saveImage($_SESSION['user'],$row[nom]);
					
				}


			} else
			if($row[type]==='select')
				$set .= "'".mysql_real_escape_string($tab['id_'.$row[nom]])."',";

		}
	}

	$set .="last_modif='".date( 'Y-m-d H:i:s')."'";

	$query = "UPDATE adherent SET ".$set." WHERE email='".$_SESSION['user']."'";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();


	include("closedb.php");

}

function getAdherents(){
	$query = "SELECT * FROM adherent ORDER BY nom ";
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



function addSup($tb,$id_tb,$id_sup_fk,$type,$valeur,$id_statut){
	//Add sup
	$query = "INSERT INTO sup(type,valeur,id_statut) VALUES ('$type','$valeur','$id_statut')";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$id_sup = mysql_insert_id();
	if($id_sup_fk==0){
		//Determiner max tb.id_sup_fk + 1
		$req1="SELECT  greatest(max(A.id_sup_fk), max(S.id_sup_fk) , max(AC.id_sup_fk), max(C.id_sup_fk) ) as id_sup_fk  FROM association A,section S,activite AC,creneau C "; 
		$res1=mysql_query($req1); 
		if (!$res1) echo mysql_error();
		$id_sup_fk=mysql_result($res1,0,"id_sup_fk");
		$id_sup_fk++;
		echo "NEW SUP FK = $id_sup_fk";
		//Update asso.id_sup_fk
		$req2 = "UPDATE $tb SET id_sup_fk='$id_sup_fk' WHERE id='$id_tb'";
		$res2=mysql_query($req2); 
		if (!$res2) echo mysql_error();
		
	} 
	//Ajouter sup_fk avec id_sup_fk déterminé
	$req3="INSERT INTO sup_fk (id,id_sup) VALUES ('$id_sup_fk','$id_sup')";
	$res3=mysql_query($req3); 
	if (!$res3) echo mysql_error();
	include("closedb.php");
}

function delSup($id){
	$query = "DELETE FROM sup WHERE id='$id'";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");
}

function getSup($tb,$id_asso){
	$query = "SELECT S.*,SF.id id_sup_fk, ST.nom statut FROM sup S ,sup_fk SF , $tb A, statut ST WHERE A.id_sup_fk=SF.id AND SF.id_sup=S.id AND S.id_statut=ST.id AND A.id='$id_asso'  ";
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


?>