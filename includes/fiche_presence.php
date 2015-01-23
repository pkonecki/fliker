<?php

defined('_VALID_INCLUDE') or die('Direct access not allowed.');

$tab=getCreneaux($_SESSION['uid']);

if(isset($_POST['part_tab']) && isset($_POST['all_tab']))
	unset($_POST['all_tab']);

if(isset($_POST['promo']))
	$promo=$_POST['promo'];
else
	$promo=$current_promo;

if (isset($_POST['modif_week']))
	$modif_week = $_POST['modif_week'];
else
	$modif_week = 0;

$compteur_modifs = 0;
$compteur_modifs_vacances = 0;
if (isset($_POST['save'])) {
	$ancien_etat = explode(' ', $_POST['ancien_etat']);
	$compteur = 0;
//	while (isset($_POST[$compteur])) {
	while (isset($ancien_etat[$compteur])) {
//		$tmp_ancien = explode('--', $_POST[$compteur]);
//		$tmp_nouvau = isset($_POST[$_POST[$compteur]]);
		$tmp_ancien = explode('--', $ancien_etat[$compteur]);
		$tmp_nouvau = isset($_POST[$ancien_etat[$compteur]]);
		if (($tmp_ancien[4] != "checked" && $tmp_nouvau) || ($tmp_ancien[4] == "checked" && !$tmp_nouvau)) {
			modifPresence($tmp_ancien[0], $tmp_ancien[1], $tmp_ancien[2], $tmp_ancien[3], $tmp_nouvau);
			$compteur_modifs++;
		}
		$compteur++;
	}
	
	
	//Enregistrement modifications vacances
	$ancien_etat_vacances = explode(' ', $_POST['ancien_etat_vacances']);
	$compteur_vacances = 0;
	while(isset($ancien_etat_vacances[$compteur_vacances])){
		$tmp_ancien_vacances = explode('--', $ancien_etat_vacances[$compteur_vacances]);
		$tmp_nouvau_vacances = isset($_POST[$ancien_etat_vacances[$compteur_vacances]]);
		if (($tmp_ancien_vacances[3] != "checked" && $tmp_nouvau_vacances) || ($tmp_ancien_vacances[3] == "checked" && !$tmp_nouvau_vacances)) {
			modifVacances($tmp_ancien_vacances[0], $tmp_ancien_vacances[1], $tmp_ancien_vacances[2], $tmp_nouvau_vacances);
			$compteur_modifs_vacances++;
		}
		$compteur_vacances++;
	}
/* echo '<pre>';
print_r($_POST['vacances']);
echo '</pre>';
echo '<pre>';
print_r($_POST['ancien_vacances']);
echo '</pre>';
	foreach($_POST['vacances'] as $vacances){
	$vacancese = explode('--', $vacances);
	$week = $vacances[0];
	if(in_array($vacances, $_POST['ancien_vacances'])){echo 'oui!!<br />';}
	} */
	
	
}
else if (isset($_POST['previous']))
	$modif_week--;
else if (isset($_POST['next']))
	$modif_week++;
else if (isset($_POST['current']))
	$modif_week = 0;

$week_window = 8;
$date_debut = "08/01";
$date_fin = "07/31";
$query = "SELECT * FROM {$GLOBALS['prefix_db']}config";
include("opendb.php");
$res = mysql_query($query);
if (!$res)
	echo mysql_error();
else {
	while ($tmp_date = mysql_fetch_array($res)) {
		if ($tmp_date['id'] == "date_debut_promo.conf")
			$date_debut = $tmp_date['valeur'];
		if ($tmp_date['id'] == "date_fin_promo.conf")
			$date_fin = $tmp_date['valeur'];
	}
}
$pre_promo = $promo - 1;
$w_debut = strtotime("".$date_debut."/{$pre_promo}");
$w_debut = strtotime("next Monday", $w_debut);
$w_fin = strtotime("".$date_fin."/{$promo}");
$w_fin = strtotime("last Monday", $w_fin);

$output = '<h3>'.getParam('text_presence.txt').'</h3>';

if ($compteur_modifs > 0) $output .= "<div class=\"tip\"><font color=green>[ $compteur_modifs présences mises à jour sur $compteur cases affichées ]</font></div>";
if ($compteur_modifs_vacances > 0) $output .= "<div class=\"tip\"><font color=green>[ $compteur_modifs_vacances fermetures mises à jour sur $compteur_vacances cases affichées ]</font></div>";

if(isset($_POST['cre'])) {

	$output .= '<p>Les cases <span style="background:#aa0202;color:#FFFFFF;">rouges</span> correspondent aux fermetures de l\'association</p>
				<p>Les cases <span style="background:#DEDEDE;">grises</span> correspondent aux fermetures du créneau (et/ou de l\'encadrant) que vous pouvez modifier</p>';
	
	$InfoCreneau = getInfoCreneau($_POST['cre']);
	
	$semaines_vacances = array();
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}vacances
	WHERE promo=".$promo." AND (id_entite=".$_POST['cre']." OR id_entite=".$InfoCreneau[$_POST['cre']]['id_association'].") ");
	while($tmp_array = mysql_fetch_array($res)){
		$semaines_vacances[$tmp_array['week']] = $tmp_array['id_entite'];
	}

	$cre = $_POST['cre'];
	$adhs = getAdherentsByCreneau($cre,$promo);
	if(isset($_POST['week']))
		$current_week = $_POST['week'];
	else
		$current_week = strftime("%V",strtotime("now"));
	$creneau = $tab[$cre];
	$jour_num = 0;
	switch ($creneau['jour_cre']) {
		case "Lundi":
			$jour_num = 1;
			break;
		case "Mardi":
			$jour_num = 2;
			break;
		case "Mercredi":
			$jour_num = 3;
			break;
		case "Jeudi":
			$jour_num = 4;
			break;
		case "Vendredi":
			$jour_num = 5;
			break;
		case "Samedi":
			$jour_num = 6;
			break;
		case "Dimanche":
			$jour_num = 7;
			break;
	}
	$week_plus = $week_window / 2 - 1;
	$week_moins = - $week_window / 2;
	$date_start = strtotime("now");
	$date_start = strtotime("last Monday", $date_start);
	$date_start = strtotime("$modif_week week", $date_start);
	$date_end = strtotime("$week_plus week", $date_start);
	$date_start = strtotime("$week_moins week", $date_start);
	if (isset($_POST['all_tab'])) {
		$date_start = $w_debut;
		$date_end = $w_fin;
	} else {
		if ($date_end > $w_fin) $date_end = $w_fin;
		if ($date_start < $w_debut) $date_start = $w_debut;
	}

	$output.= "<table style='margin-bottom:20px;'><form class=\"auto\" action=\"index.php?page=8\" method=\"POST\">";
	if (isset($_POST['all_tab'])) {
		$output .= "<thead><tr><th></th><th></th><th>Association</th><th>A jour</th><th>Jour<br>Mois</th>";
		$count_cols = 5;
	} else {
		$output .= "<thead><tr><th></th>                                            <th>Jour<br>Mois</th><th><input type='submit' name='previous' value='<<' /></th>";
		$count_cols = 3;
	}
	$date = $date_start;
	$fermeture = ""; $ancien_etat_vacances = "";
	while ($date <= $date_end) {
		$week=strftime("%V",$date);
//		if ($week[0] == '0') $week = $week[1]; // ne pas enlever le "leading zero" ici, sinon strtotime ne marche plus !
		$p=strftime("%G",$date);
		$range = utf8_decode(strftime("%d<br>%m",strtotime("$p-W$week-$jour_num")));
		
		// Vacances
		$color = "";
		foreach($semaines_vacances as $week_vacances => $id_entite){
			if($week_vacances == $week && $id_entite == $_POST['cre']){
				$color = 'DEDEDE';
				$fermeture .= '<th><input type="checkbox" name="'.$week.'--'.$_POST['cre'].'--'.$promo.'--checked" checked></th>';
				$checked = "checked";
			}
			elseif($week_vacances == $week)
				$color = 'aa0202';
		}
		if($color == "aa0202")
			$fermeture .= '<th><input type="checkbox" checked disabled></th>';
		if($color != ""){
			$output.= '<th style="background:#'.$color.';">'.$range.'</th>';
		}
		else{
			$output.= '<th>'.$range.'</th>';
			$fermeture .= '<th><input type="checkbox" name="'.$week.'--'.$_POST['cre'].'--'.$promo.'--"></th>';
			$checked = "";
		}
		$ancien_etat_vacances .= ''.$week.'--'.$_POST['cre'].'--'.$promo.'--'.$checked.' ';
		
		$count_cols++;
		$date = strtotime("+1 week",$date);
	}

	if (isset($_POST['all_tab'])) {
		$output .= '</tr></thead><input type="hidden" name="ancien_etat_vacances" value="'.$ancien_etat_vacances.'">';
		$output .= '<tr><th></th><th></th><th></th><th></th><th>Fermetures</th>'.$fermeture;
		$output .= "<tr><th></th><th></th><th></th><th></th><th>Présents</th>";
	} else {
		$output .= "<th><input type='submit' name='next' value='>>' /></th></tr></thead>";
		$count_cols++;
		$output .= "<tr><th></th><th></th><th>Présents</th>";
	}

	$query = "SELECT * FROM {$GLOBALS['prefix_db']}presence WHERE id_cre=$cre AND promo=$promo";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$i = 0;
	$tmp_ressource = array();
	while ($tmp_ressource[$i] = mysql_fetch_array($results))
		$i++;
	include("closedb.php");

	$date = $date_start;
	while ($date <= $date_end) {
		$week=strftime("%V",$date);
		if ($week[0] == '0') $week = $week[1];
		$count_week = 0;
		$i = 0;
		while ($tmp_array = $tmp_ressource[$i]) {
			if ($tmp_array['week'] == $week)
				$count_week++;
			$i++;
		}
		$date = strtotime("+1 week", $date);
		$output .= "<th align='center'>$count_week</th>";
	}
	if (isset($_POST['all_tab']))
		$output .= "</tr>";
	else
		$output .= "<th></th></tr>";
	$output .= "<input type=\"hidden\" name=\"cre\" value=\"$cre\">";
	$output .= "<input type=\"hidden\" name=\"promo\" value=\"$promo\">";
	$output .= "<input type=\"hidden\" name=\"modif_week\" value=\"$modif_week\">";
	$hiddeninput = "";
	$compteur_id = 0;
	$i = 0;
	foreach($adhs as $id_adh => $row) {
		$i++;
		$id_statut_adh = $row['id_statut'];
		$count_array = 0;
		$count_pre = 0;
		while ($tmp_array = $tmp_ressource[$count_array]) {
			if ($tmp_array['id_adh'] == $row['id'])
				$count_pre++;
			$count_array++;
		}
		$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}adhesion a INNER JOIN {$GLOBALS['prefix_db']}association b ON a.id_asso=b.id WHERE id_adh={$row['id']} AND id_cre=$cre");
		while ($tmp_array = mysql_fetch_array($res)) {
			$id_asso = $tmp_array['id_asso'];
			$nom_asso = $tmp_array['nom'];
			$actif = $tmp_array['statut'];
		}
		$a_jour = "";
		if ($actif == 0) {
			$deja_paye = 0;
			$cout_cre = 0;
			$list_sup = "";
			$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}sup_fk a INNER JOIN {$GLOBALS['prefix_db']}sup b ON a.id_sup=b.id WHERE promo=$promo AND ((id_asso_adh=$id_asso AND (id_ent=$cre OR id_ent={$creneau['id_act']} OR id_ent={$creneau['id_sec']})) OR (id_statut=$id_statut_adh AND id_ent=$id_asso))");
			while ($tmp_array = mysql_fetch_array($res)) {
			       	$cout_cre += $tmp_array['valeur'];
			       	$list_sup .= ",".$tmp_array['id'];
			}
			if ($cout_cre > 0 && !empty($list_sup)) {
				$list_sup[0] = " ";
				$res = doQuery("SELECT * FROM `{$GLOBALS['prefix_db']}paiement_sup` a INNER JOIN `{$GLOBALS['prefix_db']}paiement` b ON a.`id_paiement`=b.`id` WHERE promo=$promo AND b.`id_adh`={$row['id']} AND a.`id_sup` IN ({$list_sup})");
				while ($tmp_array = mysql_fetch_array($res))
				       $deja_paye += $tmp_array['valeur'];
			}
			if ($cout_cre <= $deja_paye)
				$a_jour = "A jour";
			else
				$a_jour = "Pas à jour";
		} else if ($actif == 1)
			$a_jour = "Résilié";
		else
			$a_jour = "Impossible";
		if (isset($_POST['all_tab']))
			$output .= "<tr><th>{$i}</th><th>".($row['active'] == 0 ? "<font color='red'>{$row['prenom']}<br>{$row['nom']}</font>" : "<a href='index.php?page=7&adh=".$row['id']."' target='_blank' style='color:white;'>{$row['prenom']}<br>{$row['nom']}</a>")."</th><th>".$nom_asso."</th><th>".$a_jour."</th><th>$count_pre</th>";
		else if ($a_jour == "A jour")
			$output .= "<tr><th>{$i}</th><th>".($row['active'] == 0 ? "<font color='red'>{$row['prenom']}<br>{$row['nom']}</font>" : "{$row['prenom']}<br>{$row['nom']}")."</th><th>$count_pre</th>";
		if (isset($_POST['all_tab']) || $a_jour == "A jour") {
			$count_array = 0;
			$array_id = array();
			while ($tmp_array = $tmp_ressource[$count_array]) {
				if ($tmp_array['id_adh'] == $row['id'])
					$array_id[$tmp_array['week']] = 1;
				$count_array++;
			}
			$date=$date_start;
			while ($date <= $date_end) {
				$week=strftime("%V",$date);
				if ($week[0] == '0') $week = $week[1];
				
				//Vacances
				$color = "";
				foreach($semaines_vacances as $week_vacances => $id_entite){
					if($week_vacances == $week && $id_entite == $_POST['cre'])
						$color = 'DEDEDE';
					elseif($week_vacances == $week)
						$color = 'aa0202';
				}
				
				$output_vacances = "";
				if($color != "") $output_vacances = 'style="background:#'.$color.';"';
				$output.= "<td ".($week==$current_week ? 'bgcolor=lightgreen' : '')." ".$output_vacances." >";
				if (isset($array_id[$week]))
					$presence = 'checked';
				else
					$presence = '';
//				$output.= "<input type=\"hidden\" name=\"_$compteur_id\" value=\"".$presence."\">";
//				$output.= "<input type=\"hidden\" name=\"$compteur_id\" value=\"$id_adh--$cre--$week--$promo--$presence\">";
								
				//Vacances
				$disabled="";
				if($color != "") $disabled = 'disabled="disabled"';
				$hiddeninput .= "${id_adh}--${cre}--${week}--${promo}--${presence} ";
				$output.= "<input type=\"checkbox\" name=\"${id_adh}--${cre}--${week}--${promo}--${presence}\" ${presence} ".$disabled."/>";
				$output.= "</td>";
				$date = strtotime("+1 week",$date);
				$compteur_id++;
			}
			if (isset($_POST['all_tab']))
				$output.= "</tr>";
			else
				$output.= "<td></td></tr>";
		}
	}
	if (isset($_POST['all_tab']))
		$tmp_string = '<input type="submit" name="part_tab" value="Afficher partiellement" /><input type="hidden" name="all_tab" value="Continuer de Afficher tout">';
	else
		$tmp_string = '<input type="submit" name="all_tab" value="Afficher tout" /><input type="submit" name="current" value="Retour semaine courante" />';
	$output.= "<input type=\"hidden\" name=\"ancien_etat\" value=\"${hiddeninput}\">";
	$output.= '<tr><td colspan="'.$count_cols.'" align="left">'.$tmp_string.'<input type="submit" name="save" value="Sauvegarder" /></td></tr>';
	$output.= "</form>";
	$output.= "</table>";
} else {
	$output.= "<form class=\"toggle\" action=\"index.php?page=8\" method=\"POST\" >";
	$query = "SELECT DISTINCT promo FROM {$GLOBALS['prefix_db']}adhesion ORDER BY promo DESC";
	include("opendb.php");
	$res = mysql_query($query);
	if (!$res) echo mysql_error();
	else {
		$output.= "<p>Promo :<SELECT id=\"promo\" name=\"promo\" >";
		while ($array_promo = mysql_fetch_array($res))
			$output.= "<OPTION value=\"".$array_promo['promo']."\" ".($promo==$array_promo['promo'] ? "selected" : "")." >".$array_promo['promo']."</OPTION>";
		$output.= "</SELECT>";
	}
	$output .= "<input type='submit' value='Afficher' /></p><table><tr align='center'><th></th><th>Section</th><th>Activité</th><th>Jour</th><th>Heure</th><th>Pré-Inscrits</th><th>Présence des Inscrits<br/>(en %)</th><th>Réguliers</th><th align=center>Présence des réguliers<br/>(en %)</th><th>Encadrants</th><th>Présence des Encadrants<br/>(en %)</th></tr>";

	$tab_regular = getAdherentsByPromo($promo);

	$time_debut = $w_debut;
	$time_fin = $w_fin;
	$nb_week = 0;
	while ($time_debut <= $time_fin) {
		$nb_week++;
		$time_debut = strtotime("+1 week", $time_debut);
	}
	foreach($tab as $creneau) {
		$cre = $creneau['id_cre'];
		$count_presence = 0;
		$i = 0;
		while ($tmp_array = $tab_regular[$i]) {
			if ($tmp_array['id_cre'] == $cre)
				$count_presence++;
			$i++;
		}
		$inscrits = getAdherentsByCreneau($cre, $promo);
		$nb_inscrits = 0;
		foreach ($inscrits as $tmp_array) {
			if ($tmp_array['statut'] == "0")
				$nb_inscrits++;
		}
		$count_presence *= 100;
		if ($nb_inscrits != 0)
			$count_presence /= ($nb_inscrits * $nb_week);
		$count_presence = round($count_presence);
		$tmp_tab_adh = null;
		$tmp_tab_value = null;
		$count_value = 0;
		$tab_week = null;
		$i = 0;
		while ($tmp_array = $tab_regular[$i]) {
			if ($tmp_array['id_cre'] == $cre) {
				$tab_week[$tmp_array['week']] = 1;
				if (isset($tmp_tab_value[$tmp_array['id_adh']]))
					$tmp_tab_value[$tmp_array['id_adh']]++;
				else {
					$tmp_tab_adh[$count_value] = $tmp_array['id_adh'];
					$tmp_tab_value[$tmp_array['id_adh']] = 1;
					$count_value++;
				}
			}
			$i++;
		}
		$i = 0;
		$count_regular_adh = 0;
		$tmp_string = 0;
		$count_presence = 0;
		while (isset($tmp_tab_adh[$i])) {
			if ($tmp_tab_value[$tmp_tab_adh[$i]] > 2) {
				$count_presence += $tmp_tab_value[$tmp_tab_adh[$i]];
				$count_regular_adh++;
			}
			$i++;
		}
		$count_presence *= 100;
		$number_week = 0;
		$i = 0;
		while ($i != 60) {
			if (isset($tab_week[$i]) && $tab_week[$i] == 1)
				$number_week++;
			$i++;
		}
		if ($count_regular_adh != 0)
			$count_presence /= ($count_regular_adh * $number_week);
		$count_presence = round($count_presence);
		
		$i = 0;
		$tmp_string = 0;
		$count_presence_inscrit = 0;
		while (isset($tmp_tab_adh[$i])) {
			$count_presence_inscrit += $tmp_tab_value[$tmp_tab_adh[$i]];
			$i++;
		}
		$count_presence_inscrit *= 100;
		$number_week = 0;
		$i = 0;
		while ($i != 60) {
			if (isset($tab_week[$i]) && $tab_week[$i] == 1)
				$number_week++;
			$i++;
		}
		if ($nb_inscrits != 0 && $number_week != 0)
			$count_presence_inscrit /= ($nb_inscrits * $number_week);
		$count_presence_inscrit = round($count_presence_inscrit);
		$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}resp_cren WHERE id_cre=$cre");
		$nb_encadrant = null;
		while ($tmp_array = mysql_fetch_array($res))
			$nb_encadrant[$tmp_array['id_adh']] = 1;
		$presence_encadrant = 0;
		$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}presence WHERE id_cre=$cre AND promo=$promo AND id_adh IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}resp_cren WHERE id_cre=$cre)");
		while ($tmp_array = mysql_fetch_array($res))
			$presence_encadrant++;
		$presence_encadrant *=100;
		if (sizeof($nb_encadrant) != 0)
			$presence_encadrant /= (sizeof($nb_encadrant) * $nb_week);
		else
			$presence_encadrant = 100;
		if (($nb_inscrits != 0) && (sizeof($nb_encadrant) != 0 || $promo != $current_promo))
		if (($nb_inscrits != 0) && (sizeof($nb_encadrant) != 0 || $promo != $current_promo))
			$output.= '<tr align="center"><td><input type="radio" name="cre" value='.$cre.' /></td><td>'.$creneau['nom_sec'].'</td><td>'.$creneau['nom_act'].'</td><td>'.$creneau['jour_cre'].'</td><td width="100">'.date("H\hi", strtotime($creneau['debut_cre'])).' - '.date("H\hi", strtotime($creneau['fin_cre'])).'</td><td>'.$nb_inscrits.'</td><td>'.$count_presence_inscrit.'</td><td>'.$count_regular_adh.'</td><td>'.$count_presence.'</td><td>'.sizeof($nb_encadrant).'</td><td>'.round($presence_encadrant).'</td></tr>';
	}
	$output.= '</table></form>';
}
print $output;
// <script type="text/javascript">
// $(".auto").change(
//	function (){
//		 $(this).submit();
//	}
// );
// </script>
?>