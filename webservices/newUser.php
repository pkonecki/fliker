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
			if($row[type]==='file'){
				if($tab[$row[nom]][name]===""){
					$values .= "0,";
				} else {
					$values .= "1,";
				}


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

?>