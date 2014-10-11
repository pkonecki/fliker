<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';


include_once("paths.php");
include_once('../webservices/General.php');
$GLOBALS['current_promo'] = getParam('promo.conf');
include_once('../webservices/Supplement.php');
include_once('../webservices/Asso.php');
include_once('../webservices/Adhesion.php');

// echo '<pre>';
// print_r($_POST['id_creneaux']);
// echo '</pre>';

$assos_cre=getAssosCreneaux();


$simulation_adhesion = array();

if(!empty($_POST['id_creneaux']))
foreach($_POST['id_creneaux'] as $cre => $id_asso){

	if(count($assos_cre[$_POST['id_statut_adh']][$cre]) != 0 AND $id_asso != "attente"){
		$simulation_adhesion[$cre]['statut'] = 0;
		$simulation_adhesion[$cre]['id_cre'] = $cre;
		$simulation_adhesion[$cre]['id_asso'] = $id_asso;
	}
	
}

$ads=getAdhesions($_POST['id_adh'],$GLOBALS['current_promo']);
$simulation_adhesion = $simulation_adhesion + $ads;

$valeur = getFacture($simulation_adhesion, $_POST['id_statut_adh'], $GLOBALS['current_promo'], 1);
$valeur_total = array_sum($valeur['totaux']);

$valeur_ancien = getFacture($ads, $_POST['id_statut_adh'], $GLOBALS['current_promo'], 1);
$valeur_ancien = array_sum($valeur_ancien['totaux']);

$valeur_total = $valeur_total - $valeur_ancien;

if($valeur_ancien != 0)
	$texte_sup = " (en plus de votre total actuel)";


$currency = getParam('currency.conf');

echo '<td>Total'.$texte_sup.'</td><td>'.$valeur_total.''.$currency.'</td>
';


// $assos=getAllAssociations();
// print '<table id="tableau_detail">';
// print "<th>Entité</th><th>Type</th><th>Montant</th><th>Gestionnaire</th>";

// foreach($valeur['assos'] as $row) {
	// print "<tr>
	// <td>{$assos[$row['id_asso_paie']]['nom']}</td><td>{$row['type']}</td><td>{$row['valeur']}$currency</td>
	// <td>{$assos[$row['id_asso_paie']]['nom']}</td>";
	// print "</tr>";
	// $valeur['totaux'][$row['id_asso_paie']] = $valeur['totaux'][$row['id_asso_paie']];
// }
// foreach($valeur['secs'] as $row) {
	// print "<tr>
	// <td>{$assos[$row['id_asso_paie']]['nom']}</td><td>{$row['type']}</td><td>{$row['valeur']}$currency</td>
	// <td>{$assos[$row['id_asso_paie']]['nom']}</td>";
	// print "</tr>";
	// $valeur['totaux'][$row['id_asso_paie']] = $valeur['totaux'][$row['id_asso_paie']];
// }
// foreach($valeur['acts'] as $row) {
	// print "<tr>
	// <td>{$assos[$row['id_asso_paie']]['nom']}</td><td>{$row['type']}</td><td>{$row['valeur']}$currency</td>
	// <td>{$assos[$row['id_asso_paie']]['nom']}</td>";
	// print "</tr>";
	// $valeur['totaux'][$row['id_asso_paie']] = $valeur['totaux'][$row['id_asso_paie']];
// }
// foreach($valeur['cres'] as $row) {
	// print "<tr>
	// <td>{$assos[$row['id_asso_paie']]['nom']}</td><td>{$row['type']}</td><td>{$row['valeur']}$currency</td>
	// <td>{$assos[$row['id_asso_paie']]['nom']}</td>";
	// print "</tr>";
	// $valeur['totaux'][$row['id_asso_paie']] = $valeur['totaux'][$row['id_asso_paie']];
// }
// print '</table>';


// echo '<pre>';
// print_r($valeur);
// echo '</pre>';

?>