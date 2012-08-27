<?php
 
// INCLUDES
include_once("./../includes/paths.php");
include_once("General.php");
include("config.php");
require_once('../iCalcreator-2.12/iCalcreator.class.php');


// Tableaux en fonction du lieu
$last_obj = -1;
$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}creneau a INNER JOIN {$GLOBALS['prefix_db']}activite b ON a.id_act=b.id ORDER BY lieu");
while ($tmp_array = mysql_fetch_array($res))
{
	if ($last_obj != $tmp_array['lieu'])
	{
		if ($last_obj != -1)
		{
			// Sauvegarde du calendrier en choisissant le nom
			$v->saveCalendar('./phpicalendar/calendars', ''.$last_obj.'.ics'); // save calendar to file
		}
		// Création du calendrier
		$v = new vcalendar(); // create a new calendar instance
		$v->setProperty( 'method', 'PUBLISH' ); // required of some calendar software
		$v->setProperty( "x-wr-calname",  $tmp_array['lieu'] );
		$v->setProperty( "X-WR-TIMEZONE", "Europe/Paris" );
	}
	
	$last_obj = $tmp_array['lieu'];

	$time = strtotime('last Monday');
	switch ($tmp_array['jour'])
	{
		case "Lundi" :
			break;
		case "Mardi" :
			$time = strtotime('+1 day', $time);
			break;
		case "Mercredi" :
			$time = strtotime('+2 day', $time);
			break;
		case "Jeudi" :
			$time = strtotime('+3 day', $time);
			break;
		case "Vendredi" :
			$time = strtotime('+4 day', $time);
			break;
		case "Samedi" :
			$time = strtotime('+5 day', $time);
			break;
		case "Dimanche" :
			$time = strtotime('+6 day', $time);
			break;
	}
	$annee = strftime('%Y', $time);
	$mois = strftime('%m', $time);
	$jour = strftime('%d', $time);
	
	$heure_debut = explode(':', $tmp_array['debut']);
	$heure_fin = explode(':', $tmp_array['fin']);
	// Création d'un évènement sur le calendrier
	$vevent = new vevent(); // create an event calendar component
	$vevent->setProperty( 'DTSTART', array( 'year'=>$annee, 'month'=>$mois, 'day'=>$jour, 'hour'=>$heure_debut[0], 'min'=>$heure_debut[1],  'sec'=>$heure_debut[2] )); // Date de début
	$vevent->setProperty( 'DTEND',  array( 'year'=>$annee, 'month'=>$mois, 'day'=>$jour, 'hour'=>$heure_fin[0], 'min'=>$heure_fin[1], 'sec'=>$heure_fin[2] ));	// Date de fin
	$vevent->setProperty('SUMMARY', utf8_encode($tmp_array['nom']));
	$vevent->setProperty( 'DESCRIPTION', utf8_encode($tmp_array['description']) );
	$vevent->setProperty( 'LOCATION', utf8_encode($tmp_array['lieu']) ); // Lieu
	$v->setComponent ( $vevent ); // Ajoute l'évènement au calendrier
}

	// Sauvegarde du calendrier en choisissant le nom
	$v->saveCalendar('./phpicalendar/calendars', ''.$last_obj.'.ics'); // save calendar to file
	
// Tableaux en fonction de la section
$last_obj = -1;
$res = doQuery("SELECT lieu, jour, debut, fin, id_sec, b.description, c.nom FROM ({$GLOBALS['prefix_db']}creneau a INNER JOIN {$GLOBALS['prefix_db']}activite b ON a.id_act=b.id) INNER JOIN {$GLOBALS['prefix_db']}section c ON b.id_sec=c.id ORDER BY id_sec");
while ($tmp_array = mysql_fetch_array($res))
{
	if ($last_obj != $tmp_array['id_sec'])
	{
		if ($last_obj != -1)
		{
			// Sauvegarde du calendrier en choisissant le nom
			$v->saveCalendar('./phpicalendar/calendars', ''.$last_obj.'.ics'); // save calendar to file
		}
		// Création du calendrier
		$v = new vcalendar(); // create a new calendar instance
		$v->setProperty( 'method', 'PUBLISH' ); // required of some calendar software
		$v->setProperty( "x-wr-calname",  $tmp_array['nom'] );
		$v->setProperty( "X-WR-TIMEZONE", "Europe/Paris" );
	}
	
	$last_obj = $tmp_array['id_sec'];

	$time = strtotime('last Monday');
	switch ($tmp_array['jour'])
	{
		case "Lundi" :
			break;
		case "Mardi" :
			$time = strtotime('+1 day', $time);
			break;
		case "Mercredi" :
			$time = strtotime('+2 day', $time);
			break;
		case "Jeudi" :
			$time = strtotime('+3 day', $time);
			break;
		case "Vendredi" :
			$time = strtotime('+4 day', $time);
			break;
		case "Samedi" :
			$time = strtotime('+5 day', $time);
			break;
		case "Dimanche" :
			$time = strtotime('+6 day', $time);
			break;
	}
	$annee = strftime('%Y', $time);
	$mois = strftime('%m', $time);
	$jour = strftime('%d', $time);
	
	$heure_debut = explode(':', $tmp_array['debut']);
	$heure_fin = explode(':', $tmp_array['fin']);
	// Création d'un évènement sur le calendrier
	$vevent = new vevent(); // create an event calendar component
	$vevent->setProperty( 'DTSTART', array( 'year'=>$annee, 'month'=>$mois, 'day'=>$jour, 'hour'=>$heure_debut[0], 'min'=>$heure_debut[1],  'sec'=>$heure_debut[2] )); // Date de début
	$vevent->setProperty( 'DTEND',  array( 'year'=>$annee, 'month'=>$mois, 'day'=>$jour, 'hour'=>$heure_fin[0], 'min'=>$heure_fin[1], 'sec'=>$heure_fin[2] ));	// Date de fin
	$vevent->setProperty('SUMMARY', utf8_encode($tmp_array['nom']));
	$vevent->setProperty( 'DESCRIPTION', utf8_encode($tmp_array['description']) );
	$vevent->setProperty( 'LOCATION', utf8_encode($tmp_array['lieu']) ); // Lieu
	$v->setComponent ( $vevent ); // Ajoute l'évènement au calendrier
}

	// Sauvegarde du calendrier en choisissant le nom
	$v->saveCalendar('./phpicalendar/calendars', ''.$last_obj.'.ics'); // save calendar to file
?>