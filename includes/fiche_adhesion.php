<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
session_start();
if (!isset($_GET['adh'])) {
	$id_adh =$_SESSION['uid'];
	$edit=true;
}
else {
	$tab = getMyAdherents($_SESSION['uid']);
	if (isset($tab[$_GET['adh']])) $id_adh=$_GET['adh'];
	else { 
		print 'Vous n\'avez pas accès à cette page';
		die();
	}
	$resp=true;
}
$adh = getAdherent($id_adh);





if ($_POST['action'] == 'modification' && $edit) {
	
}
else {
	if ($_POST['action'] == 'submitted' && $edit){

	}
	if(!(strcmp($_SESSION['user'],"") == 0)){
		print '<h2>Vos adhésions</h2>';
		$ads=getAdhesions($id_adh);
		$crens=getCreneaux($id_adh);
		print '<TABLE>';
		print '<th>Date</th><th>Activité</th><th>Jour</th><th>Heure</th><th>Statut</th><th>Année</th>';
		foreach($ads as $key => $value){
			print '<tr>';
			print "<td>{$value['date']}</td>";
			print "<td>{$crens[$value['id_cre']]['nom_act']}</td>";
			print "<td>{$crens[$value['id_cre']]['jour_cre']}</td>";
			print "<td>{$crens[$value['id_cre']]['debut_cre']}</td>";
			print "<td>{$value['statut']}</td>";
			print "<td>{$value['promo']}</td>";
			print '</tr>';
		}
		print '</TABLE>';
	}
	else {
		print "<p>Vous n'êtes pas connecté</p>";
	}
}



?>
