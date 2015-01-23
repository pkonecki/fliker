<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
$tot_asso=count(getAssociations($_SESSION['uid']));
$tot_sec=count(getSections($_SESSION['uid']));
$tot_act=count(getActivites($_SESSION['uid']));
$tot_cre=count(getCreneaux($_SESSION['uid']));
$tot = $tot_asso + $tot_sec + $tot_act + $tot_cre;
if((strcmp($_SESSION['user'],"") == 0))
{
	print "<p>Vous n'êtes pas connecté</p>";
	die();
}

if ($_SESSION['privilege'] == 1)
	$tab_asso = getAssociations($_SESSION['uid']);
else if (isset($tot_asso) && $tot_asso > 0)
	$tab_asso = getAssociations($_SESSION['uid']);
else if (isset($tot_sec) && $tot_sec > 0)
{
	$tab_section = getSections($_SESSION['uid']);
	$string_id_sec = "";
	foreach ($tab_section as $tmp_array)
		$string_id_sec .= ", ". $tmp_array['id'];
	$string_id_sec[0] = ' ';
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}asso_section WHERE id_sec IN (".$string_id_sec.")");
	$string_id_asso = "";
	while ($tmp_array = mysql_fetch_array($res))
		$string_id_asso .= ", ".$tmp_array['id_asso'];
	$string_id_asso[0] = ' ';
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}association WHERE id IN (".$string_id_asso.")");
	while ($tmp_array = mysql_fetch_array($res))
		$tab_asso[$tmp_array['id']] = $tmp_array;
}
else
{
	print "<p>Vous n'avez pas accès à cette page!</p>";
	die();
}


if(isset($_GET['promo']))
	$promo=$_GET['promo'];
else
	$promo=$current_promo;

if(isset($_POST['promo']))
	$promo=$_POST['promo'];


// if(count($tab_asso) > 1){
	// $choix_asso = '<select name="asso">';	
	// foreach($tab_asso as $id_asso => $tab){
		// $choix_asso .= '<option value="'.$id_asso.'" '.($_POST['asso']==$id_asso ? "selected" : "").' >'.$tab['nom'].'</option>';
		// if($_POST['asso'] == $id_asso)
			// $tab_asso2[$id_asso] = $tab;
	// }
	// $choix_asso .= '</select>';
// }

// echo '<pre>';
// print_r($tab_asso2);
// echo '</pre>';

// if(isset($_POST['asso']))
	// $tab_asso = $tab_asso2;


echo '<form action="index.php?page=20'.(isset($_GET['action']) ? "&action=".$_GET['action'] : "").'" method="POST" >';
$res = doQuery("SELECT DISTINCT promo FROM {$GLOBALS['prefix_db']}adhesion ORDER BY promo DESC");
print '<p>Promo:<SELECT name="promo" >';
if (!$res || mysql_num_rows($res) <= 0)
	print "<OPTION value='$promo' 'selected' >$promo</OPTION>";
while ($data = mysql_fetch_array($res))
	echo '<option value="'.$data['promo'].'" '.($promo==$data['promo'] ? "selected" : "").' >'.$data['promo'].'</option>';
print '</SELECT>'.$choix_asso.'<input type="submit" value="Afficher" /></p></form>';


if(isset($_GET['action']) AND $_GET['action'] == "detail"){
	$base_sql_fk = "_fk";
	print '<a href="index.php?page=20">Cliquez ici pour revenir sur les statistiques simplifiées</a>';
}
else
	print '<a href="index.php?page=20&action=detail">Cliquez ici pour afficher les statistiques avec les statuts détaillés</a>';


$currency = getParam('currency.conf');
$tab_type = array("Pré-Inscrits", "<img src='images/homme.png' width='25' height='25' />", "<img src='images/femme.png' width='25' height='25' />", "0-18", "19-25", "26-45", "46-65", "66+", "A jour");

$list_statut = null;
$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}statut".$base_sql_fk." ORDER BY nom");
$nb_statut = 0;
while ($tmp_array = mysql_fetch_array($res))
{
	$list_statut[$tmp_array['id']] = $tmp_array['nom'];
	$nb_statut++;
}

foreach ($list_statut as $key => $value)
{
	array_push($tab_type, $value);
	$list_statut[$key] = 0;
}

print "<br /><br /><table><tr align='center'><th></th><th>Nombre</th><th colspan='2'>Nombre</th><th colspan='5'>Age</th><th>Nombre</th><th colspan='".$nb_statut."'>Statut</th></tr><tr align='center'><th></th>";
	foreach ($tab_type as $key => $value)
		print "<th>".$value."</th>";
print "</tr>";

foreach ($tab_asso as $asso)
{
	print "<tr align='center'><td><b>".$asso['nom']."</b></td>";
	
	// Récupération de la liste des sections
	$sections = null;
	if (isset($tot_asso) && $tot_asso > 0 || $_SESSION['privilege'] == 1)
		$sections = getSectionsByAsso($asso['id']);
	else
	{
		$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}section WHERE id IN (SELECT id_sec FROM {$GLOBALS['prefix_db']}asso_section WHERE id_asso=".$asso['id']." AND id_sec IN (SELECT id_sec FROM {$GLOBALS['prefix_db']}resp_section WHERE id_adh=".$_SESSION['uid']."))");
		while ($tmp_array_sec = mysql_fetch_array($res))
			$sections[$tmp_array_sec['id']] = $tmp_array_sec;
	}
	$list_id_ent = array();
	$list_id = array();
	$id_creneaux = array();
	foreach ($sections as $key => $value)
	{
		$id_creneaux[$key] = 0;
		$list_id[$key] = $key;
		$list_id_ent[$key] = getActivitesBySection($key);
		foreach ($list_id_ent[$key] as $key_act => $value_act)
		{
			$list_id_ent[$key][$key_act] = getCreneauxByActivite($key_act);
			$list_id[$key] .= ', '.$key_act.'';
			foreach ($list_id_ent[$key][$key_act] as $key_cre => $value_cre){
				$list_id[$key] .= ', '.$key_cre.'';
				// $id_creneaux[$key] += nbre_a_jour($key_cre, $promo);
			}
		}
	}
	
	// Affichage des données d'une section
	$output = "";
	$test_inscrit_null = 0;
	// $output_asso = array();
	foreach ($sections as $sec)
	{
		$output2 .= "<tr align='center'>";
		$output2 .= "<td>".$sec['nom']."</td>";
		
		// Initialisation des variables
		$nb_statut = $list_statut;
		$tab_stats = getStatsSec($nb_statut, $asso['id'], $list_id[$sec['id']], $promo, $base_sql_fk);

		foreach ($tab_stats as $key => $value){
			$output2 .= "<td>$value</td>";
			if($key == "Inscrits")
				$test_inscrit_null = $value;
			// $output_asso[$key] += $value;
		}
		foreach ($nb_statut as $key => $value)
			$output2 .= "<td>$value</td>";
	
		// print '<td>'.$id_creneaux[$sec['id']].'</td>';
		$output2 .= "</tr>";
		
		if($test_inscrit_null != 0)
			$output .= $output2;

		$output2 = "";
		
	}

	
	// Affichage pour Asso (en gras)
	$nb_statut = $list_statut;
	$tab_stats = getStats($nb_statut, $asso['id'], $promo, $base_sql_fk);

	foreach ($tab_stats as $key => $value)
		print "<td>$value</td>";
	foreach ($nb_statut as $key => $value)
		print "<td>$value</td>";
	print '</tr>';
	// Affichage pour Sections
	print $output;
	
	
	
	
	
	
	
	
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}sup_fk, {$GLOBALS['prefix_db']}sup WHERE {$GLOBALS['prefix_db']}sup_fk.id_sup={$GLOBALS['prefix_db']}sup.id AND id_ent IN (460) AND promo=".$promo." ");
	while ($tmp_array = mysql_fetch_array($res)){
		$cout_sup += $tmp_array['valeur'];
		$listSup[$tmp_array['id_asso_adh']] = $tmp_array['valeur'];
	}
	
// echo '<pre>';
// print_r($listSup);
// echo '</pre>';	
	
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}paiement_sup, {$GLOBALS['prefix_db']}paiement WHERE {$GLOBALS['prefix_db']}paiement_sup.id_paiement = {$GLOBALS['prefix_db']}paiement.id AND promo=".$promo." AND id_adh IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}adhesion WHERE statut=0 AND promo=".$promo.") AND id_sup IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent IN (460))");
	while ($tmp_array = mysql_fetch_array($res))
	{
		if (isset($list_adh[$tmp_array['id_adh']]))
			$list_adh[$tmp_array['id_adh']] += $tmp_array['valeur'];
		else
			$list_adh[$tmp_array['id_adh']] = $tmp_array['valeur'];
	}
	
	
// echo '<pre>';
// print_r($list_adh);
// echo '</pre>';
	
	
}
print '</table>';

?>