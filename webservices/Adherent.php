<?php
function newAdherent($tab)
{
	$champs = getChampsAdherents();
	$colonnes ="(";
	$values ="(";
	$tab['nom'] = strtolower($tab['nom']);
	$tab['nom'][0] = strtoupper($tab['nom'][0]);
	$tab['prenom'] = strtolower($tab['prenom']);
	$tab['prenom'][0] = strtoupper($tab['prenom'][0]);
	include("opendb.php");
	foreach($champs as $row)
	{
		if($row['inscription']==1)
		{
			if($row['type']==='varchar')
			{
				$colonnes .= $row['nom'].",";
				$values .= "'".mysql_real_escape_string($tab[$row['nom']])."',";
			}
			else if($row['type']==='date')
			{
				$colonnes .= $row['nom'].",";
				$values .= "'".mysql_real_escape_string($tab[$row['nom']])."',";
			}
			else if($row['type']==='tinyint')
			{
				$colonnes .= $row['nom'].",";
				if (isset($tab[$row['nom']]) && $tab[$row['nom']]==='on')
					$values .= "1,";
				else
					$values .= "0,";
			}
			else if($row['type']==='file')
			{
				$colonnes .= $row['nom'].",";
				if($tab[$row['nom']]['name']==="")
					$values .= "0,";
				else
					$values .= "1,";
			}
			if($row['type']==='select')
			{
				$colonnes .= 'id_'.$row['nom'].",";
				$values .= "'".mysql_real_escape_string($tab['id_'.$row['nom']])."',";
			}
		}
	}
	$activationKey=mt_rand() . mt_rand() . mt_rand() . mt_rand() . mt_rand();
	$colonnes .= "date_creation,last_modif,last_modif_droit_image,activationkey,";
	$values .= "'".date( 'Y-m-d H:i:s')."','". date( 'Y-m-d H:i:s')."','". date( 'Y-m-d H:i:s')."','".$activationKey."',";
	$colonnes = substr($colonnes,0,-1);
	$values = substr($values,0,-1);
	$colonnes .=")";
	$values .=")";
	$query = "INSERT INTO {$GLOBALS['prefix_db']}adherent ".$colonnes." VALUES ".$values;
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	//send mail
	$to      = $tab['email'];
	$subject = "[".getParam('text_top.txt')."] Votre inscription sportive";
	$message = "Bienvenue !\r\n  Vous, ou quelqu'un utilisant votre adresse email, êtes pré-inscrit sur notre service d'adhésion en ligne.\r\n  Si c'est une erreur ou une tentative d'usurpation, ignorez tout simplement cet email et vos coordonnées seront automatiquement purgées de notre serveur dans quelques temps.\r\n  Vous devez à présent activer votre compte en cliquant sur le lien suivant :\r\n".getParam('url_site.conf')."validate.php?$activationKey\r\n  APRES ACTIVATION, vous pourrez vous connecter sur votre profil et vous PRE-inscrire à vos sports favoris (obligatoire, même pour un premier cours d'essai gratuit !) ...\r\n  ENFIN, vos inscriptions ne seront définitives qu'APRES PAIEMENT au bureau de permanence !\r\n  Remarque : pour exercer votre droit de consultation ou modification de vos données personnelles, vous devez d'abord activer votre compte.\r\n  Excellente saison sportive,\r\n--\r\nles administrateurs.";
	$headers = 'From: '.getParam('admin_email.conf')."\r\n"        .
	           'Reply-To: '.getParam('contact_email.conf')."\r\n"  .
	           'Return-Path: '.getParam('admin_email.conf')."\r\n" .
	           'X-Mailer: PHP/'.phpversion();
	if (getParam('allow_mail.conf') == true)
		mail($to, $subject, $message, $headers);

	// Remplacement phpmailer
	// $mail = new PHPMailer();
	// $mail->SetFrom(getParam('admin_email.conf'), $_SESSION['prenom'] . ' ' . $_SESSION['nom']);
	// $mail->AddReplyTo(getParam('contact_email.conf'), "ASESCO");
	// $mail->AddCustomHeader('Return-Path: '. getParam('admin_email.conf'));
	// $mail->AddCustomHeader('X-Mailer: PHP/'.phpversion());
	// $mail->Subject = $subject;
	// $mail->Body = $message;
	// $mail->AddAddress($to);
	// if (getParam('allow_mail.conf') == true)
	// {
	// 	$mail->Send();
	// 	print 'Un email vient d\'être envoyé à l\'adresse '.$to.', veuillez vérifier votre boîte mail.';
	// }
}

function getAdherent($user)
{
	$return = array();
	$tab = getChampsAdherents();
	include("opendb.php");
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE `id` = '".$user."'";
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	$row = mysql_fetch_assoc($results);
	foreach($tab as $champ)
	{
		if($champ['type']==='select')
			$return[$champ['nom']] = $row['id_'.$champ['nom']];
		else
			$return[$champ['nom']] = $row[$champ['nom']];
	}
	include("closedb.php");
	return $return;
}

function getChampsAdherents()
{
	include("opendb.php");
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}champs_adherent ORDER BY ordre ASC";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$champs = array();
	while($row = mysql_fetch_array($results)){
		$champs[$row['nom']] = $row;
	}
	include("closedb.php");
	return $champs;
}

function modifAdherent($tab)
{
	include_once("class.imageconverter.php");
	include_once("saveImage.php");
	$champs = getChampsAdherents();
	$set = "";
	$tab['nom'] = strtolower($tab['nom']);
	$tab['nom'][0] = strtoupper($tab['nom'][0]);
	$tab['prenom'] = strtolower($tab['prenom']);
	$tab['prenom'][0] = strtoupper($tab['prenom'][0]);
	
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE id='".$tab['id_adh']."'";
		include("opendb.php");
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	include("closedb.php");
	$stock_droit_image = mysql_fetch_array($results);
	$is_change_droit_image = false;

	include("opendb.php");
	foreach($champs as $row)
	{
		if($row['user_editable']==1)
		{
			$set .= $row['nom']."=";
			if($row['type']==='varchar')
				$set .= "'".mysql_real_escape_string($tab[$row['nom']])."',";
			else if($row['type']==='date')
				$set .= "'".mysql_real_escape_string($tab[$row['nom']])."',";
			else if($row['type']==='tinyint')
			{
				if (isset($tab[$row['nom']]))
				{
					$set .= "1,";
					if ($row['nom'] == "droit_image" && $stock_droit_image['droit_image'] == 0)
						$is_change_droit_image = true;
				}
				else
				{
					$set .= "0,";
					if ($row['nom'] == "droit_image" && $stock_droit_image['droit_image'] == 1)
						$is_change_droit_image = true;
				}
			}
			if($row['type']==='file')
			{
				if ($tab[$row['nom']]['name']==="")
					$set .= "0,";
				else
				{
					$set .= "1,";
					saveImage($tab['email'],$row['nom']);
					
				}
			}
			else if($row['type']==='select')
				$set .= "'".mysql_real_escape_string($tab['id_'.$row['nom']])."',";
		}
	}
	if ($is_change_droit_image == true)
		$set .=" last_modif_droit_image='".date( 'Y-m-d H:i:s')."', ";
	$set .=" last_modif='".date( 'Y-m-d H:i:s')."' ";
	$query = "UPDATE {$GLOBALS['prefix_db']}adherent SET ".$set." WHERE id='".$tab['id_adh']."'";
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	include("closedb.php");
}

function getAdherents()
{
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE active=1 ORDER BY nom ";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results))
		$tab[$row['id']] = $row;
	include("closedb.php");
	return $tab;
}

function getAdherentsByCreneau($id_cre,$promo)
{
	$query = "SELECT ADH.*, ADS.statut FROM {$GLOBALS['prefix_db']}adherent ADH, {$GLOBALS['prefix_db']}adhesion ADS WHERE ADS.id_adh=ADH.id AND ADS.id_cre=$id_cre AND ADS.promo=$promo ORDER BY nom";
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

function getAdherentsByPromo($promo)
{
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}presence WHERE promo=$promo";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	$i = 0;
	while($tab[$i] = mysql_fetch_array($results)){
		$i++;
	}
	include("closedb.php");
	return $tab;
}

function getStatuts()
{
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}statut ORDER BY nom ";
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

function getAssos()
{
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}association ";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results))
	{
			$tab[$row['id']] = $row['nom'];
	}
	include("closedb.php");
	return $tab;
}

function isAmongMyAdherents($userid,$adhid)
{
	$query="SELECT ADH.id
		FROM   {$GLOBALS['prefix_db']}adherent     ADH,
                       {$GLOBALS['prefix_db']}adhesion     AD,
                       {$GLOBALS['prefix_db']}creneau      CR,
                       {$GLOBALS['prefix_db']}activite     AC,
                       {$GLOBALS['prefix_db']}section      S,
                       {$GLOBALS['prefix_db']}asso_section HS,
                       {$GLOBALS['prefix_db']}association  A
		WHERE  
		       ADH.id = $adhid
		AND    AD.id_cre  = CR.id
		AND    CR.id_act  = AC.id
		AND    AC.id_sec  = S.id
		AND    S.id       = HS.id_sec
		AND    HS.id_asso = A.id
                AND
		 (
		       CR.id IN (SELECT id_cre  FROM {$GLOBALS['prefix_db']}resp_cren    WHERE id_adh = '$userid')
		       OR
		       AC.id IN (SELECT id_act  FROM {$GLOBALS['prefix_db']}resp_act     WHERE id_adh = '$userid')
		       OR
		       S.id  IN (SELECT id_sec  FROM {$GLOBALS['prefix_db']}resp_section WHERE id_adh = '$userid')
		       OR
		       A.id  IN (SELECT id_asso FROM {$GLOBALS['prefix_db']}resp_asso    WHERE id_adh = '$userid')
		 )
		AND
                 (
                       ADH.id = AD.id_adh
                       OR
		        (
			      0
		        )
                       OR
                       ADH.id NOT IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}adhesion WHERE 1)
		 )
		";
// Avec "0" qui pourrait être :
// * cette nouvelle formulation trop permissive (un responsable de créneau peut avoir du pouvoir sur la fiche d'un responsable de section) :
//			      ADH.id IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}resp_asso    RA WHERE RA.id_asso = A.id)
//			      OR
//			      ADH.id IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}resp_section RA WHERE RA.id_sec  = S.id)
//			      OR
//			      ADH.id IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}resp_act     RA WHERE RA.id_act  = AC.id)
//			      OR
//			      ADH.id IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}resp_cren    RA WHERE RA.id_cre  = CR.id)
// * l'ancienne formulation trop restreinte (il fallait être responsable au même niveau de responsabilité exactement) :
//                            ADH.id IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}resp_asso    RA WHERE RA.id_asso IN (SELECT id_asso FROM {$GLOBALS['prefix_db']}resp_asso    WHERE id_adh = '$userid'))
//			      OR
//			      ADH.id IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}resp_section RA WHERE RA.id_sec  IN (SELECT id_sec  FROM {$GLOBALS['prefix_db']}resp_section WHERE id_adh = '$userid'))
//			      OR
//			      ADH.id IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}resp_act     RA WHERE RA.id_act  IN (SELECT id_act  FROM {$GLOBALS['prefix_db']}resp_act     WHERE id_adh = '$userid'))
//			      OR
//			      ADH.id IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}resp_cren    RA WHERE RA.id_cre  IN (SELECT id_cre  FROM {$GLOBALS['prefix_db']}resp_cren    WHERE id_adh = '$userid'))
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
//	$tab = array();
	$tab = false;
	while($row = mysql_fetch_array($results))
	{
//			$tab[$row['id']] = $row['id'];
			$tab = true;
	}
	include("closedb.php");
	return $tab;
}

function getMyAssos($userid, $section = false)
{
	if($section)
		$query="SELECT A.*, S.nom as 'section' FROM {$GLOBALS['prefix_db']}association A, {$GLOBALS['prefix_db']}asso_section SA, {$GLOBALS['prefix_db']}resp_section RS, {$GLOBALS['prefix_db']}section S
		        WHERE A.id=SA.id_asso AND SA.id_sec=RS.id_sec AND RS.id_adh=$userid AND S.id=RS.id_sec
		";
	else if($userid==-1)
		$query="SELECT A.* FROM {$GLOBALS['prefix_db']}association A
		";
	else
		$query="SELECT A.* FROM {$GLOBALS['prefix_db']}association A, {$GLOBALS['prefix_db']}resp_asso RS
		        WHERE A.id=RS.id_asso AND RS.id_adh=$userid
		";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results))
	{
		$tab[$row['id']] = $row['nom'];
	}
	include("closedb.php");
	return $tab;
}

function getSolde($id_adh,$promo)
{
	$mycrens=getCreneaux($_SESSION['uid']);
	$ads= getAdhesions($id_adh,$promo);
	foreach ($ads as $key => $value)
	{
		if(!isset($mycrens[$value['id_cre']]))
			unset($ads[$key]);
	}
	$adh = getAdherent($id_adh);
	if (!isset($adh['statut']))
		$adh['statut'] = "";
	$tab = getFacture($ads,$adh['statut'], $promo);
	$p_sup = getPaiementsSup($id_adh, $promo);
	$solde=0;
	foreach($tab['assos'] as $row)
	{
		if (empty($p_sup[$row['id']]))
			$solde += $row['valeur'];
		else
			$solde += $row['valeur'] - $p_sup[$row['id']];
	}
	foreach($tab['secs'] as $row)
	{
		if (empty($p_sup[$row['id']]))
			$solde += $row['valeur'];
		else
			$solde += $row['valeur'] - $p_sup[$row['id']];
	}
	foreach($tab['acts'] as $row)
	{
		if (empty($p_sup[$row['id']]))
			$solde += $row['valeur'];
		else
			$solde += $row['valeur'] - $p_sup[$row['id']];
	}
	foreach($tab['cres'] as $row)
	{
		if (empty($p_sup[$row['id']]))
			$solde += $row['valeur'];
		else
			$solde += $row['valeur'] - $p_sup[$row['id']];
	}
	return -$solde;
}

function setNumCarte($num,$adh)
{
	$promo = getParam('promo.conf');
	$q="UPDATE {$GLOBALS['prefix_db']}numcarte_fk SET numcarte=$num WHERE id_adh=$adh AND promo='".$promo."' ";
	include("opendb.php");
	$results = mysql_query($q);
	if (!$results) echo mysql_error();
	include("closedb.php");
}

function getNumCarte($adh)
{
	$promo = getParam('promo.conf');
	$q="SELECT numcarte FROM {$GLOBALS['prefix_db']}numcarte_fk WHERE id_adh=$adh AND promo='".$promo."' ";
	include("opendb.php");
	$results = mysql_query($q);
	if (!$results)
		echo mysql_error();
	$array_tmp = mysql_fetch_array($results);
	include("closedb.php");
	return $array_tmp['numcarte'];
}

function getMaxNumCarte()
{
	$query= "SELECT numcarte FROM {$GLOBALS['prefix_db']}numcarte_fk ORDER BY numcarte ASC";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	$i = 1;
	while ($array_tmp = mysql_fetch_array($results))
	{
		if ($array_tmp['numcarte'] != 0)
		{
			if ($array_tmp['numcarte'] == $i)
				$i++;
			else
				break;
		}
	}
	include("closedb.php");
	return $i;
}
?>
