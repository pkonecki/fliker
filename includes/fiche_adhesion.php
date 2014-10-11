<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');

if (!isset($_GET['adh']) && isset($_SESSION['uid']))
	header('Location: index.php?adh='.$_SESSION['uid']);

$self = false;
$admin = false;
$resp_asso = false;
$resp_section = false;
$resp_creneau = false;

if (!isset($_GET['adh']) or $_GET['adh']==$_SESSION['uid'])
{
	$self = true;
	$id_adh = $_SESSION['uid'];
}
else if($_SESSION['privilege']==1)
{
	$admin = true;
	$resp_asso = true;
	$assos_resp = getMyAssos(-1, "");
	if (!isset($_GET['asso']))
		$current_asso = key($assos_resp);
	else
		$current_asso = $_GET['asso'];
	$id_adh = $_GET['adh'];
}
else
{
//	$tab = getMyAdherents($_SESSION['uid']);
//	if (isset($tab[$_GET['adh']]))
		$id_adh=$_GET['adh'];
//	else
	if (!isAmongMyAdherents($_SESSION['uid'],$id_adh))
	{
		print 'Vous n\'avez pas accès à cette page';
		print $die_footer;
		die();
	}
	$ass_resp = getMyAssos($_SESSION['uid'], "association");
	if (count($ass_resp) > 0 )
		$resp_asso = true;
		
	$sec_resp = getMyAssos($_SESSION['uid'], "section");
	if (count($sec_resp) > 0 )
		$resp_section = true;
		
	$cren_resp = getMyAssos($_SESSION['uid'], "creneau");
	if (count($cren_resp) > 0 )
		$resp_creneau = true;

	$assos_resp = $ass_resp + $sec_resp + $cren_resp;
	if ($resp_asso || $resp_section || $resp_creneau)
	{
		if(!isset($_GET['asso']))
			$current_asso = key($assos_resp);
		else
			$current_asso = $_GET['asso'];
	}
}

//A quoi ça sert ce qui suit ?
//if ($resp_asso)
//	$rep1 = "true";
//else
//	$rep1 = "false";
//if ($self)
//	$rep2 = "true";
//else
//	$rep2 = "false";

if(!empty($_GET['promo']))
	$promo = $_GET['promo'];
else
	$promo = $current_promo;

print "<div class=\"tip\"><center>".getParam('text_adherent.txt')."</center></div>";

$currency = getParam('currency.conf');
$creneaux = getAllCreneaux();
$adh = getAdherent($id_adh);
$id_statut_adh = $adh['statut'];


if (isset($_POST['action']) && $_POST['action'] == 'nouvelle') {
	print '<h2>Choisissez vos activités</h2>';
	print '<FORM action="index.php?page=7&adh='.$id_adh.'" method="POST">
	<input type="hidden" name="action" value="select_assos" />';
	print '<ul id="tree_root">';
	$tab=array();
	foreach($creneaux['avec_famille'] as $creneau ){
		$tab[$creneau['id_famille']]['nom_famille'] = $creneau['nom_famille'];
		$tab[$creneau['id_famille']]['id_famille'] = $creneau['id_famille'];
		$tab[$creneau['id_famille']]['activites'][$creneau['id_act']]['nom_sec'] = $creneau['nom_sec'];
		$tab[$creneau['id_famille']]['activites'][$creneau['id_act']]['id_sec'] = $creneau['id_sec'];
		$tab[$creneau['id_famille']]['activites'][$creneau['id_act']]['nom'] = $creneau['nom_act'];
		$tab[$creneau['id_famille']]['activites'][$creneau['id_act']]['id'] = $creneau['id_act'];
		$tab[$creneau['id_famille']]['activites'][$creneau['id_act']]['creneaux'][$creneau['id_cre']]['jour'] = $creneau['jour_cre'];
		$tab[$creneau['id_famille']]['activites'][$creneau['id_act']]['creneaux'][$creneau['id_cre']]['id'] = $creneau['id_cre'];
		$tab[$creneau['id_famille']]['activites'][$creneau['id_act']]['creneaux'][$creneau['id_cre']]['debut'] = $creneau['debut_cre'];
		$tab[$creneau['id_famille']]['activites'][$creneau['id_act']]['creneaux'][$creneau['id_cre']]['fin'] = $creneau['fin_cre'];
		$tab[$creneau['id_famille']]['activites'][$creneau['id_act']]['creneaux'][$creneau['id_cre']]['lieu'] = $creneau['batiment'].' '.$creneau['salle'];

	}

	$ads=getAdhesions($id_adh,$promo);
	foreach($tab as $famille){
		$out = '<li><input style="display:none;" type="checkbox" name="famille'.$famille['id_famille'].'" value="'.$famille['id_famille'].'"><label>'.$famille['nom_famille'].'</label>';
		$out .= '<ul id="sections">';
			foreach($famille['activites'] as $act){
				$out2 = '<li><input type="checkbox" name="act'.$act['id'].'" value="'.$act['id'].'"><label>'.$act['nom_sec'].' - '.$act['nom'].'</label>';
				$out2 .= '<ul id="creneaux">';
				$i=0;
				foreach($act['creneaux'] as $cre){
					$resps = getResponsablesCre($cre['id'], $promo);
					if ( !isset( $ads['cre'.$cre['id']] ) and count( $resps ) != 0 ){
					$out2 .= '<li><input type="checkbox" name="cre[]" value="'.$cre['id'].'"><label>'.$cre['jour'].' - '.substr($cre['debut'],0,-3).' - '.substr($cre['fin'],0,-3).' - '.$cre['lieu'].'</label>';
					$i++;
					}
				}
				$out2 .= '</ul>';
				if ($i>0) $out .= $out2;
			}
		$out .= '</ul>';
		print $out;
	}
	print '</ul>';
	print '<INPUT type="submit" value="Valider"></FORM>
	';


}
else if (isset($_POST['action']) && $_POST['action'] == 'select_assos' && !empty($_POST['cre']) )
{
	print '<FORM  id="f_adherent_modif" action="index.php?page=7&adh='.$id_adh.'" method="POST">';
	if (!isset($_POST['update']))
	{
		print '<input type="hidden" name="action" value="submitted" />';
	}
	else
	{
		print  '<input type="hidden" name="id_ads" value="'.$_POST['id_ads'].'" />
		<input type="hidden" name="action" value="submitted_update" />';
	}
	print '<TABLE>';
	$assos_cre=getAssosCreneaux();
	$creneaux = $creneaux['sans_famille'];
	$post_creneau = array_unique($_POST['cre']);

	foreach($post_creneau as $cre)
	{
		print '<tr>';
		print '<td>'.$creneaux[$cre]['nom_sec'].' - '.$creneaux[$cre]['nom_act'].' - '.$creneaux[$cre]['jour_cre'].' - '.$creneaux[$cre]['debut_cre'].' - '.$creneaux[$cre]['batiment'].' '.$creneaux[$cre]['salle'].'</td>';
		
		if(count($assos_cre[$id_statut_adh][$cre]) == 0){
			print "<td class='asso_cre'><input type=\"radio\" value=\"impossible\" name=\"asso_cre[$cre]\" cre=\"$cre\" class=\"radio_cre\">
			<LABEL FOR=\"asso_cre_$cre\">Impossible</LABEL></td><td>
			<input type=\"radio\" value=\"0\" name=\"asso_cre[$cre]\" cre=\"$cre\" class=\"radio_cre\">
			<LABEL>Annuler ce choix</LABEL></td>";
			$texte_impossible = "<span class=\"tip\"><center>".getParam('text_select_asso.txt')."</center></span>";
		}

		else{
		foreach($assos_cre[$id_statut_adh][$cre] as $id_asso => $nom_asso)
		{
			if(count($assos_cre[$id_statut_adh][$cre]) == 1){
				print "<td class='asso_cre'><input type=\"radio\" value=\"$id_asso\" class=\"categorie_req\" name=\"asso_cre[$cre]\" cre=\"$cre\" >
				<LABEL FOR=\"asso_cre_$cre\">$nom_asso</LABEL></td><td>
				<input type=\"radio\" value=\"0\" class=\"categorie_req\" name=\"asso_cre[$cre]\" cre=\"$cre\" >
				<LABEL>Annuler ce choix</LABEL></td>";
			}
			else{
				print "<td class='asso_cre'><input type=\"radio\" value=\"$id_asso\" name=\"asso_cre[$cre]\" cre=\"$cre\" >
				<LABEL FOR=\"asso_cre_$cre\">$nom_asso</LABEL></td><td>
				<input type=\"radio\" value=\"0\" name=\"asso_cre[$cre]\" cre=\"$cre\" >
				<LABEL>Annuler ce choix</LABEL></td>";
			}
		}
		}

	}
	
	$ChampsAdherents = getChampsAdherents();
	
	print '<tr id="affichage"></tr>';
	print "<span style=\"display:none;\" id=\"id_statut_adh\">$id_statut_adh</span>";
	print '</TABLE>

	<p>En validant ce formulaire, '.$ChampsAdherents['charte']['description'].'</p>
	<INPUT type="submit" value="Valider" ><INPUT type="reset" class="reset" value="Remettre à zéro" ></FORM>
	'.$texte_impossible.'
	
	
	<script>

	function Update()
	{

	var data = new Object();
	$("input[type=\'radio\']:checked").each(function(){
		data[$(this).attr("cre")] = $(this).val();
	});


	$.ajax({
	type: "POST",
	url: "includes/fiche_adhesion_total.php",
	data: {id_creneaux:data,
	id_statut_adh:'.$id_statut_adh.',
	id_adh:'.$id_adh.',
	current_promo:'.$current_promo.'
	},
	success: function(data){$("#affichage").html(data);}
	});

	}
	 
	$("document").ready(function(){
	setInterval(function() { Update(); }, 1000);
	});

	</script>
	
	';
}
else
{
	if (isset($_POST['action']) && $_POST['action'] == 'submitted')
	{
		if(!empty($_POST['asso_cre']) ) newAdhesions($_POST['asso_cre'],$id_adh);
	}
	if (isset($_POST['action']) && $_POST['action'] == 'submitted_update')
	{
		if(!empty($_POST['asso_cre']) ) updateAdhesions($_POST['asso_cre'],$_POST['id_ads']);
	}
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_ads')
	{
		delAdhesion($_POST['id_ads']);
	}
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_ads_definitive')
	{
		delAdhesionDefinitive($_POST['id_ads']);
	}
	if (isset($_POST['action']) && $_POST['action'] === 'activation_ads')
	{
		actAdhesion($_POST['id_ads']);
	}
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_paie')
	{
		delPaiement($_POST['id_paie']);
	}
	if (isset($_POST['action']) && $_POST['action']==='nouveau_paiement')
	{
		if(empty($_POST['sup']) || empty($_POST['promo']) || empty($_POST['num']) || empty($_POST['date_t']) )
			print "<pre>Il y a une erreur dans le paiement</pre>";
		else
			addPaiement($_POST);
	}
}
	if(isset($_POST['action']) && $_POST['action']==='setnumcarte')
		setNumCarte($_POST['numcarte'],$id_adh);
	if(!(strcmp($_SESSION['user'],"") == 0))
	{
		//Selection asso
		if(isset($assos_resp) && count($assos_resp) > 1 ){
			print "<p>Consulter en tant que responsable ";
			foreach($assos_resp as $key => $asso) print "<a href=\"index.php?page=7&adh=$id_adh&asso=$key&promo=".(isset($_GET['promo']) ? $_GET['promo'] : "")."\">$asso</a> ";
			print "</p>";
		}
		//Adhésions
		if ($self)
			$ads=getAdhesions($id_adh,$promo);
		else
			$ads=getMyAdhesions($id_adh,$promo);

		$crens=$creneaux['sans_famille'];
		$mycrens=getCreneaux($_SESSION['uid']);
		$assos=getAllAssociations();
		$assos_cre=getAssosCreneaux();
		print "<div class=\"tip\">".getParam('text_adhesion.conf')."</div><br/>";
		print "<h2>Adhésions de {$adh['prenom']} {$adh['nom']} :";

		if (!$self)
			$tri = "AND id_cre IN (".implode(", ", array_keys($mycrens)).")";
		$query = "SELECT DISTINCT promo FROM {$GLOBALS['prefix_db']}adhesion WHERE id_adh = ".$id_adh." ".$tri." ORDER BY promo DESC";
		include("opendb.php");
		$res = mysql_query($query);
		if (!$res)
			echo mysql_error();
		else
		{
			print " Promo: <SELECT id='promo' >";
			while ($array_promo = mysql_fetch_array($res))
				print "<OPTION value=\"".$array_promo['promo']."\" ".(isset($_GET['promo']) && $_GET['promo']==$array_promo['promo'] ? "selected" : "")." >".$array_promo['promo']."</OPTION>";
			print "</SELECT></h2>";
		}
		//Bouton nouvelle adhésion
		if (($self || $resp_asso || $resp_section || $resp_creneau) && $promo == $current_promo && getParam("stop_adhesions.conf") == "false")
			print '<FORM action="index.php?page=7&adh='.$id_adh.'" method="POST">
			<input type="hidden" name="action" value="nouvelle" /><br />
			<INPUT type="submit" style="width:400px;height:30px;font-size:16px;" value="Cliquer ici pour ajouter un sport">';
		print '</FORM><br />';
		// Liste adhésions
		if(empty($ads)){
		print '<h4>Vous n\'avez aucun sport d\'enregistré</h4>';
		}
		else{
		$creneaux = $creneaux['sans_famille'];
		print '<TABLE>';
		print '<th>Date</th><th>Activité</th><th>Jour</th><th>Heure et Lieu</th><th>Etat</th><th>Promo</th><th>Gestionnaire</th>';
		if ($self || $resp_asso || $resp_section || $resp_creneau)
			print "<th>Résilier</th>";
		foreach($ads as $key => $value)
			if(is_numeric($key) && ($self || $value['id_asso']==$current_asso || isset($mycrens[$value['id_cre']])))
			{
				$acts = getActiviteByCre($value['id_cre']);
				$url_act = $acts['url'];
				$id_act = $acts['id_act'];
				$id_act = $crens[$value['id_cre']]['id_act'];
				$id_sec = $crens[$value['id_cre']]['id_sec'];
				print '<tr>';
				print "<td>{$value['date']}</td>";
				print "<td>".($url_act != "" ? "<a href='$url_act'>" : null )."{$crens[$value['id_cre']]['nom_sec']} - {$crens[$value['id_cre']]['nom_act']}".($url_act != "" ? "</a>" : null )."</td>";
				print "<td>{$crens[$value['id_cre']]['jour_cre']}</td>";
				print "<td>".date("H\hi", strtotime($crens[$value['id_cre']]['debut_cre']))." - ".date("H\hi", strtotime($crens[$value['id_cre']]['fin_cre']))." - {$crens[$value['id_cre']]['batiment']} {$crens[$value['id_cre']]['salle']}</td>";

				print "<td>";
				$switch_impossible = 0;
				switch($value['statut'])
				{
					case 0: 
					print "Active";
					print "</td>";
					print "<td>{$value['promo']}</td>";
					print "<td>{$assos[$value['id_asso']]['nom']}</td>";
					$gestionnaires[$assos[$value['id_asso']]['id']] = $assos[$value['id_asso']]['nom'];
					break;
					case 1:
					print "Résiliée";
					print "</td>";
					print "<td>{$value['promo']}</td>";
					print "<td>{$assos[$value['id_asso']]['nom']}</td>";
					break;
					case 2:
					$switch_impossible = 1;
						if(isset($assos_cre[$id_statut_adh][$value['id_cre']]))
						{
							print "Possible";
							print "</td>";
							print "<td>{$value['promo']}</td>";
							print "<td>";
							print '<FORM action="index.php?page=7&adh='.$id_adh.'&asso='.(isset($current_asso) ? $current_asso : "").'" method="POST">';
							print '<input type="hidden" name="action" value="select_assos" />';
							print '<input type="hidden" name="update" value="true" />';
							print '<input type="hidden" name="id_ads" value="'.$key.'" />';
							print '<input type="hidden" name="cre[]" value="'.$value['id_cre'].'" />';
							print '<input type="submit" value="Choisir asso" >';
							print '</FORM>';
							print "</td>";
						}
						else
						{
							print "Impossible";
							print "</td>";
							print "<td>{$value['promo']}</td>";
							print "<td>";
							print "</td>";
						}
						break;
				}
				$deja_venu = mysql_num_rows(doQuery("SELECT * FROM {$GLOBALS['prefix_db']}presence WHERE promo=$promo AND id_adh=$id_adh AND id_cre={$value['id_cre']}"));
				$deja_paye = 0;
				$cout_cre = 0;
				$list_sup = "";
				if($switch_impossible == 0){
					$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}sup_fk a INNER JOIN {$GLOBALS['prefix_db']}sup b ON a.id_sup=b.id WHERE b.promo=$promo AND ((b.id_asso_adh={$value['id_asso']} AND (a.id_ent={$value['id_cre']} OR a.id_ent=$id_act OR a.id_ent=$id_sec)))"); // OR (b.id_statut=$id_statut_adh AND a.id_ent={$value['id_asso']}))"); // ici on ne vérifie que les suppléments d'activités, pas la cotisation asso
					while ($tmp_array = mysql_fetch_array($res)) {
							$cout_cre += $tmp_array['valeur'];
							$list_sup .= ",".$tmp_array['id'];
					}
				}
				if (!empty($list_sup)) // && ($cout_cre > 0)) // ici, peut importe le coût payé, même une activité gratuite doit être comptabilisée
				{
					$list_sup[0] = " ";
					$res = doQuery("SELECT * FROM `{$GLOBALS['prefix_db']}paiement_sup` a INNER JOIN `{$GLOBALS['prefix_db']}paiement` b ON a.`id_paiement`=b.`id` WHERE b.`promo`=$promo AND b.`id_adh`=$id_adh AND a.`id_sup` IN ({$list_sup})");
					while ($tmp_array = mysql_fetch_array($res)) {
//					       $deja_paye += $tmp_array['valeur'];
					       $deja_paye++; // ici, peut importe le montant payé, même un paiement partiel ou "nul" (activités gratuites) doit être comptabilisé
					}
				}
				if ($self || $resp_section || $resp_creneau)
				{
					if ($deja_paye == 0)
					{
						if ($deja_venu == 0)
						{
							print '<td align=center>
							<FORM action="index.php?page=7&adh='.$id_adh.'" method="POST">
							<input type="hidden" name="id_ads" value='.$key.' />
							';
							switch($value['statut'])
							{
								case 0: 
								print '<input type="hidden" name="action" value="suppression_ads" />';
								print '<INPUT type="image" src="images/unchecked.gif" value="submit">';
								break;
								case 1:
//								if ($resp_section) {
								   	print '<input type="hidden" name="action" value="activation_ads" />';
//								   	print '<INPUT type="image" src="images/checked.gif" value="submit">';
								      	print '<INPUT type="submit" name="act_act_ads_rse" value="réactiver">';
//								}
//								else
//									print "(seul un gestionnaire peut réactiver)";
								break;
								case 2:
								print "";
								break;
							}
							print '</FORM>
							</td>';
						}
						else
							print "<td align=center><a href=\"".getParam("url_resiliation.conf")."\" target=\"_blank\" ><!-- img src=\"images/warning.gif\" -->(d&eacute;j&agrave; venu)</a></td>";
					}
					else
						print "<td align=center><a href=\"".getParam("url_resiliation.conf")."\" target=\"_blank\" ><!-- img src=\"images/warning.gif\" -->(d&eacute;j&agrave; pay&eacute;)</a></td>";
				}
				else if ($resp_asso)
				{
					$texte_suppr_definitive = '
					<td><FORM action="index.php?page=7&adh='.$id_adh.'&asso='.$current_asso.'" method="POST">
					<input type="hidden" name="action" value="suppression_ads_definitive" />
					<input type="hidden" name="id_ads" value='.$key.' />
					<input type="submit" name="act_sup_ads_ras_def" value="Supprimer définitivement">
					</FORM></td>
					';
					print '<td align=center>
					<FORM action="index.php?page=7&adh='.$id_adh.'&asso='.$current_asso.'" method="POST">
					<input type="hidden" name="id_ads" value='.$key.' />
					';
					switch($value['statut'])
					{
						case 0: 
						print '<input type="hidden" name="action" value="suppression_ads" />';
						if ($deja_paye > 0){
						   	print '<INPUT type="submit" name="act_sup_ads_ras" value="résilier même si déjà payé">';
							$texte_suppr_definitive = '
							<td><FORM>
							<input type=button value="Supprimer définitivement" onClick="alert(\'Vous ne pouvez pas supprimer cette adhésion. Vous devez supprimer le paiement associé avant\')">
							</FORM></td>
							';
						}
						else if ($deja_venu > 0)
						     	print '<INPUT type="submit" name="act_sup_ads_ras" value="résilier même si déjà venu">';
						else
							print '<INPUT type="image" src="images/unchecked.gif" value="submit">';
						break;
						case 1:
						print '<input type="hidden" name="action" value="activation_ads" />';
//						print '<INPUT type="image" src="images/checked.gif" value="submit">';
						print '<INPUT type="submit" name="act_act_ads_ras" value="réactiver">';
						break;
						case 2:
						print "";
						break;
					}
					print '</FORM></td>
					'.$texte_suppr_definitive.'
					';
				}
				print "</tr>";
			}
		print '</TABLE>';
		//Facture
		if (!isset($current_asso))
			$current_asso = "";
		print '<h2>Facturation des prestations</h2>
				<FORM id="f_adherent_modif" action="index.php?page=7&adh='.$id_adh.'&asso='.$current_asso.'" method="POST">
				<input type="hidden" name="action" value="nouveau_paiement" />
				<table>';
		print "<th>Entité</th><th>Type</th><th>Montant</th><th>Déjà payé</th><th>Reste à payer</th><th>Gestionnaire</th>";
		if ($resp_asso || $resp_section || $resp_creneau)
			print "<th>Nouveau Paiement :</th>";
		$tab = getFacture($ads, $adh['statut'], $promo, 0);
		$p_sup = getPaiementsSup($id_adh, $promo);
		$paiement_possible=false;
		foreach($tab['assos'] as $row) {
			$tmp_id = 0;
			if (isset($p_sup[$row['id']])) $tmp_id = $p_sup[$row['id']];
			print "<tr>
			<td>{$assos[$row['id_asso_paie']]['nom']}</td><td>{$row['type']}".($row['facultatif']==1 ? " (facultatif)" : "")."</td><td>{$row['valeur']}$currency</td>
			<td>".$tmp_id."$currency</td><td>".($row['valeur'] - $tmp_id)."$currency</td>
			<td>{$assos[$row['id_asso_paie']]['nom']}</td>";
			if (($resp_asso || $resp_section || $resp_creneau) && $row['id_asso_paie']==$current_asso && (($row['valeur'] - $tmp_id)!=0)) {
				$paiement_possible=true;
				print "<td><INPUT name=\"sup[{$row['id']}]\" class=\"tot\" type=\"text\" size=\"4\" />{$currency} ".($row['facultatif']==1 ? "<input type='checkbox' name='facultatif[".$row['id']."]' value='".$row['valeur']."'> Cocher s'il ne prend pas ce supplément facultatif" : "")."</td>";			} else if ($resp_asso || $resp_section || $resp_creneau) print "<td></td>";
			print "</tr>";
			$tab['totaux'][$row['id_asso_paie']] = $tab['totaux'][$row['id_asso_paie']] - $tmp_id;
		}
		foreach($tab['secs'] as $row) {
			$tmp_id = 0;
			if (isset($p_sup[$row['id']])) $tmp_id = $p_sup[$row['id']];
			print "<tr>
			<td>{$row['nom_sec']}</td><td>{$row['type']}".($row['facultatif']==1 ? " (facultatif)" : "")."</td><td >{$row['valeur']}$currency</td>
			<td>".$tmp_id."$currency</td><td>".($row['valeur'] - $tmp_id)."$currency</td>
			<td>{$assos[$row['id_asso_paie']]['nom']}</td>";
			if (($resp_asso || $resp_section || $resp_creneau) && $row['id_asso_paie']==$current_asso && (($row['valeur'] - $tmp_id)!=0)) {
				$paiement_possible=true;
				print "<td><INPUT name=\"sup[{$row['id']}]\" class=\"tot\" type=\"text\" size=\"4\" />{$currency} ".($row['facultatif']==1 ? "<input type='checkbox' name='facultatif[".$row['id']."]' value='".$row['valeur']."'> Cocher s'il ne prend pas ce supplément facultatif" : "")."</td>";			} else if ($resp_asso || $resp_section || $resp_creneau) print "<td></td>";
			print "</tr>";
			$tab['totaux'][$row['id_asso_paie']] = $tab['totaux'][$row['id_asso_paie']] - $tmp_id;
		}
		foreach($tab['acts'] as $row) {
			$tmp_id = 0;
			if (isset($p_sup[$row['id']])) $tmp_id = $p_sup[$row['id']];
			print "<tr>
			<td>{$row['nom_sec']} - {$row['nom_act']}</td><td>{$row['type']}".($row['facultatif']==1 ? " (facultatif)" : "")."</td><td >{$row['valeur']}$currency</td>
			<td>".$tmp_id."$currency</td><td>".($row['valeur'] - $tmp_id)."$currency</td>
			<td>{$assos[$row['id_asso_paie']]['nom']}</td>";
			if (($resp_asso || $resp_section || $resp_creneau) && $row['id_asso_paie']==$current_asso && (($row['valeur'] - $tmp_id)!=0)) {
				$paiement_possible=true;
				print "<td><INPUT name=\"sup[{$row['id']}]\" class=\"tot\" type=\"text\" size=\"4\" />{$currency} ".($row['facultatif']==1 ? "<input type='checkbox' name='facultatif[".$row['id']."]' value='".$row['valeur']."'> Cocher s'il ne prend pas ce supplément facultatif" : "")."</td>";			} else if ($resp_asso || $resp_section || $resp_creneau) print "<td></td>";
			print "</tr>";
			$tab['totaux'][$row['id_asso_paie']] = $tab['totaux'][$row['id_asso_paie']] - $tmp_id;
		}
		foreach($tab['cres'] as $row) {
			$tmp_id = 0;
			if (isset($p_sup[$row['id']])) $tmp_id = $p_sup[$row['id']];
			print "<tr>
			<td>{$row['nom_sec']} - {$row['nom_act']} - {$row['jour_cre']} - {$row['debut_cre']}</td><td>{$row['type']}".($row['facultatif']==1 ? " (facultatif)" : "")."</td><td >{$row['valeur']}$currency</td>
			<td>".$tmp_id."$currency</td><td>".($row['valeur'] - $tmp_id)."$currency</td>
			<td>{$assos[$row['id_asso_paie']]['nom']}</td>";
			if (($resp_asso || $resp_section || $resp_creneau) && $row['id_asso_paie']==$current_asso && (($row['valeur'] - $tmp_id)!=0)) {
				$paiement_possible=true;
				print "<td><INPUT name=\"sup[{$row['id']}]\" class=\"tot\" type=\"text\" size=\"4\" />{$currency} ".($row['facultatif']==1 ? "<input type='checkbox' name='facultatif[".$row['id']."]' value='".$row['valeur']."'> Cocher s'il ne prend pas ce supplément facultatif" : "")."</td>";
			} else if ($resp_asso || $resp_section || $resp_creneau) print "<td></td>";
			print "</tr>";
			$tab['totaux'][$row['id_asso_paie']] = $tab['totaux'][$row['id_asso_paie']] - $tmp_id;
		}
//		if (($resp_asso || $resp_section || $resp_creneau) && isset($paiement_possible))
		if ($paiement_possible)
		{
			print "<tr><td></td><td></td><td></td><td></td><td></td><td>Réduction :</td><td>";
			$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}reductions ORDER BY nom ASC");
			while ($tmp_array = mysql_fetch_array($res)){
				print "<label><input type='checkbox' name='reductions[]' id='".$tmp_array['nom']."' value='".$tmp_array['id']."' onclick='UpdateCost()' >".$tmp_array['nom']."</label> ";
				$valeur_reduction = $tmp_array['valeur'];
			}
			print "(Indiquer le montant sans la remise et elle sera enregistrée automatiquement)</td></tr>";
			print "<tr><td></td><td></td><td></td><td></td><td></td><td>Total :</td><td><INPUT type=\"text\" name=\"total\" id=\"total\" style=\"background:#B3ADAD;\" size=\"4\" READONLY />{$currency}</td></tr>";
			print "<tr><td></td><td></td><td></td><td></td><td></td><td>Type :</td><td><select id='type' name='type'>";
			$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}type_transa ORDER BY nom ASC");
			while ($tmp_array = mysql_fetch_array($res))
				print "<option value='".$tmp_array['nom']."' ".('Chèque'==$tmp_array['nom'] ? "selected" : "")." >".$tmp_array['nom']."</option>";
			print "</select></td></tr>";
			print "<tr><td></td><td></td><td></td><td></td><td></td><td>Numéro :</td><td><INPUT name=\"num\" type=\"text\" />de la transaction (NON NUL !)</td></tr>";
			print "<tr><td></td><td></td><td></td><td></td><td></td><td colspan=2>*** Chèque   : indiquer nom banque et numéro chèque<br>
			                                                                      *** Dispense : indiquer raison dispense<br>
			                                                                      *** Espèces  : indiquer numéro bordereau dépôt</td></tr>";
			print "<tr><td></td><td></td><td></td><td></td><td></td><td>Date :</td><td><INPUT name=\"date_t\" class=\"datepicker\" readonly type=\"text\" />de la transaction (pas de cet enregistrement !)</td></tr>";
			print "<tr><td></td><td></td><td></td><td></td><td></td><td>Commentaire :</td><td><INPUT name=\"remarque\" type=\"text\" />(facultatif)</td></tr>";
			print "<tr><td></td><td></td><td></td><td></td><td></td><td colspan=2>(par exemple, indiquer nom de l'émetteur du chèque si différent du pratiquant ...)</td></tr>";
			print "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td><INPUT type=\"submit\" name=\"submit\" value=\"Enregistrer\" /></td></tr>";
			print "<INPUT type=\"hidden\" name=\"promo\" value=\"{$promo}\" />
                               <INPUT type=\"hidden\" name=\"id_adh\" value=$id_adh />
                               <INPUT type=\"hidden\" name=\"recorded_by\" value=\"{$_SESSION['nom']} {$_SESSION['prenom']}\" />";
		}
		print '</table></FORM>';
		
		print '<h2>Totaux</h2>';
		
		$totaux_affichage = '<p>Préparez vos chèques comme suit SVP :</p>
		<table><th>A l\'ordre de</th><th>Montant</th>';
		$totaux = 0;
		foreach($tab['totaux'] as $asso => $total) {
			if($total != 0){
				if($assos[$asso]['ordre_cheques'] != "")
					$ordre = $assos[$asso]['ordre_cheques'];
				else
					$ordre = $assos[$asso]['nom'];
			$totaux_affichage .= "<tr><td>$ordre</td><td>$total $currency</td></tr>";
			$totaux += $total;
			}
		}
		$totaux_affichage .= '</table>';
		if($totaux != 0)
			print $totaux_affichage;
		else
			print 'Vous êtes à jour de vos règlements';

	
		//Paiements
		print "<h2>Paiements</h2>";
		$paiements=getMyPaiements($id_adh);
		print "<table><th>Type</th><th>Numéro</th><th>Date</th><th>Remarque</th><th>Total</th><th>Promo</th><th>Enregistré par</th><th>Enregistré le</th><th>Détails</th>";
		if ($resp_asso)
			print "<th>Supprimer</th>";
		foreach ($paiements as $id => $row )
		{
			if($row['promo']!=$promo) continue;
			$tot=0;
			if (isset($row['ps']))
			{
				foreach($row['ps'] as $row2)
					$tot+=$row2['valeur_paiement'];
			}
			print "<tr>";
			print "<td>{$row['type']}</td><td>{$row['num']}</td><td>{$row['date_t']}</td><td>{$row['remarque']}</td><td>$tot$currency</td><td>{$row['promo']}</td><td>{$row['recorded_by']}</td><td>{$row['date']}</td><td><img src=\"images/downarrow.gif\" class=\"toggle\" /></td>";
			if ($resp_asso)
			{
				print '<td>';
				print '<FORM action="index.php?page=7&adh='.$id_adh.'&asso='.$current_asso.'" method="POST">
				<input type="hidden" name="action" value="suppression_paie" />
				<input type="hidden" name="id_paie" value='.$id.' />
				<INPUT type="image" src="images/unchecked.gif" value="submit">
				</form>
				';
				print '</td>';
			}
			print "</tr>";
			print "<tr style=\"display : none; \"><td></td><td></td><td>Détails :</td><td colspan=6><table><th>Type</th><th>Dû</th><th>Payé</th><th>Ordre</th>";
			if (isset($row['ps']))
			{
				foreach($row['ps'] as $row2)
					print "<tr><td>{$row2['type_sup']}</td><td>{$row2['valeur_sup']}$currency</td><td>{$row2['valeur_paiement']}$currency</td><td>{$assos[$row2['id_asso_paie']]['nom']}</td></tr>";
			}
			print "</table></td>".($resp_asso ? "<td></td>" : "")."</tr>";
		}
		print "</table>";
		}
		//$adh = getAdherent($id_adh);
		//Numéro de carte
		if ($resp_asso && !$self)
			print "<h2>Changer le numéro de carte</h2><FORM id=\"f_numcarte\" action=\"index.php?page=7&adh=$id_adh&asso=$current_asso\" method=\"POST\"  >
		 		<input type=\"hidden\" name=\"action\" value=\"setnumcarte\" /> 
				<dt>Numéro actuel:<input type=\"text\" value=\"".getNumCarte($id_adh)."\" disabled />
				<dt>Nouveau Numéro:<input type=\"text\" name=\"numcarte\" id=\"numcarte\"  class=\"numcarte\" >
				<input type=\"submit\" >
				</FORM>
				"; 
	}
	else {
		print "<p>Vous n'êtes pas connecté LOL</p>";
	}
}
?>
<script type="text/javascript">
$('#tree_root').checkboxTree({
      initializeChecked: 'collapsed', 
      initializeUnchecked: 'collapsed',
      onCheck: {
                descendants: 'check',
	        node: 'expand',
      },
      onUncheck: {
                  ancestors: 'uncheck',
	          node: 'collapse',
      }, 

    });
$('.reset').click(function() {
                $('#total').text("0");
        });
$(".radio_cre").click(function() {
                var params = {};
                $("input[type=radio]:checked.radio_cre").each(function(){
                        params["cre_"+$(this,'input[type=radio]:checked').attr('cre')] = $(this,'input[type=radio]:checked').val();
                });
                params['id_statut_adh'] = $('#id_statut_adh').text();
                //alert($.param(params));
                $.getJSON("webservices/cout_adh.php",
                                params,
                                function(data) {
                                        $("#total").text(data['total']);
                                }
                );
});
$(".tot").change(function(){
        total = 0.0;
        $(".tot").each(function(){
                if(!isNaN(parseFloat($(this).val()))){
                        total= total + parseFloat($(this).val());
                }
        });
        $("#total").val(total);
});

function UpdateCost() {
	total = document.getElementById("total").value;
	if(document.getElementById("Boursier").checked == true){
		document.getElementById("total").value =  (1-<?php echo $valeur_reduction; ?>/100)*total;
	}
	else{
        total = 0.0;
        $(".tot").each(function(){
                if(!isNaN(parseFloat($(this).val()))){
                        total= total + parseFloat($(this).val());
                }
        });
        $("#total").val(total);
	}
}


$(".toggle").click(function () {
      $(this).parent().parent().next().toggle();
      //alert($(this).parent().parent().next().html());
});
$('#promo').change( function (){
        window.location.search = "page=7&adh="+$.getUrlVar('adh')+"&promo="+$(this).val();
});
$(function() {
        $( ".datepicker" ).datepicker({ 
                changeYear: true , yearRange: "-100:+0" , changeMonth: true , dateFormat: "yy-mm-dd"  
        });
});
$(document).ready(function() {
                        $.extend($.validator.messages, {
                        required: "Ce champs est requis",
                        number: "Veuillez entrer un numéro correct"

                });
                        $("#f_numcarte").validate({

                        rules : {
                                numcarte: {
                        required: true,
                                        number: true,
                        remote: "includes/numcarte.php"
                }
                        },
                        messages: {
                                numcarte: {
                                        required: "Ce champs est requis",
                                        number: "Veuillez entrer un numéro correct",
                                        remote: "Le numéro est déjà utilisé"
                                        }
                        },
                        errorPlacement: function(error, element) {
                    if ( element.is(":radio") )
                        error.appendTo( element.parent() );
                        else
                        error.appendTo( element.parent() );
                },
                success: function(label) {
                // set   as text for IE
                label.html(" ").addClass("checked");
                }
                        });
});
</script>