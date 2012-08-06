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

$currency = getParam('currency.conf');

if(!(strcmp($_SESSION['user'],"") == 0))
{
	$tab=getAssociations($_SESSION['uid']);
	print '<ul id="submenu">';
	if((isset($tot_asso) && $tot_asso > 0) || (isset($tot_sec) && $tot_sec > 0))
		print '<li><a class="'.(($_GET['page']== 16) ? 'selected' : '').'" href="index.php?page=16">Opérations</a></li>';
	if((isset($tot_asso) && $tot_asso > 0) || (isset($tot_sec) && $tot_sec > 0))
		print '<li><a class="'.(($_GET['page']==14) ? 'selected' : '').'" href="index.php?page=14">Récapitulatif</a></li>';
	if((isset($tot_asso) && $tot_asso > 0) || (isset($tot_sec) && $tot_sec > 0))
		print '<li><a class="'.(($_GET['page']==17) ? 'selected' : '').'" href="index.php?page=17">Inventaire</a></li>';
	if((isset($tot_asso) && $tot_asso > 0) || (isset($tot_sec) && $tot_sec > 0))
		print '<li><a class="'.(($_GET['page']==18) ? 'selected' : '').'" href="index.php?page=18">Cotisations</a></li>';
	print '</ul>';
}

$tab_promo = getPromoRecap();
print "<p>Promo:<SELECT id='promo' ><option value='0'>Toutes</option>";
foreach ($tab_promo as $key => $value)
	print "<OPTION value='".$key."' ".($promo == $key ? "selected" : "")." >".$key."</OPTION>";
print "</SELECT></p>";

if ($promo == 0)
{
	$tab_type = getPromoRecap();
	$tab_type_montant = $tab_type;
	print "<table><tr align='center'><th></th>";
	foreach ($tab_type as $key => $value)
		print "<th>".$key."</th>";
	print "<th>TOTAL</th><tr>";
	foreach ($tab_asso as $asso)
	{
		print "<tr><td><font color='#f6f6f6'>|</font></td>";
		foreach ($tab_type as $key => $value)
			print "<td></td>";
		print "<td></td></tr>";
		print "<tr align='center'><td><b>".$asso['nom']."</b></td>";
		
		// Affichage des données d'une association
		if ($_SESSION['privilege'] == 1 || (isset($tot_asso) && $tot_asso > 0))
		{
			// Initialisation des variables
			$total_line = 0;
			$tab_paiement = getPaiementsAssoAll($asso['id'], $tab_type);

			// Affichage des paiement pour chaque type
			foreach ($tab_type as $key => $value)
			{
				if (isset($tab_paiement[$key]))
				{
					print "<td>";
					print "<FONT COLOR='".findColor($tab_paiement[$key])."'>";
					print $tab_paiement[$key]."$currency";
					print "</FONT>";
					print "</td>";
					$total_line += $tab_paiement[$key];
					$tab_type_montant[$key] += $tab_paiement[$key];
				}
				else
					print "<td>0$currency</td>";
			}
			
			// Total de la ligne
			print "<td class='tab_footer_colonne_top'><FONT COLOR='".findColor($total_line)."'>".$total_line."$currency</FONT></td>";
		}
		else
		{
			foreach ($tab_type as $key => $value)
				print "<td ></td>";
			print "<td class='tab_footer_colonne_top'></td>";
		}
		print "</tr>";
		
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
		foreach ($sections as $key => $value)
		{
			$list_id[$key] = $key;
			$list_id_ent[$key] = getActivitesBySection($key);
			foreach ($list_id_ent[$key] as $key_act => $value_act)
			{
				$list_id_ent[$key][$key_act] = getCreneauxByActivite($key_act);
				$list_id[$key] .= ', '.$key_act.'';
				foreach ($list_id_ent[$key][$key_act] as $key_cre => $value_cre)
					$list_id[$key] .= ', '.$key_cre.'';
			}
		}
		
		// Affichage des données d'une section
		foreach ($sections as $sec)
		{
			print "<tr align='center'>";
			print "<td>".$sec['nom']."</td>";
			
			// Récupération des paiements
			$total_line = 0;
			$tab_paiement = getPaiementsSecAll($asso['id'], $sec['id'], $tab_type, $list_id);

			// Affichage des paiement pour chaque type
			foreach ($tab_type as $key => $value)
			{
				if (isset($tab_paiement[$key]))
				{
					print "<td>";
					print "<FONT COLOR='".findColor($tab_paiement[$key])."'>";
					print $tab_paiement[$key]."$currency";
					print "</FONT>";
					print "</td>";
					$total_line += $tab_paiement[$key];
					$tab_type_montant[$key] += $tab_paiement[$key];
				}
				else
					print "<td>0$currency</td>";
			}
			
			// Total de la ligne
			print "<td class='tab_footer_colonne_top'><FONT COLOR='".findColor($total_line)."'>".$total_line."$currency</FONT></td>";
		}
		
		// Affichage des sous-totaux pour chaque type
		print "<tr align='center' class='tab_footer_line'><td><b>Sous-Total</b></td>";
		$final_total = 0;
		$total_line = 0;
		foreach ($tab_type_montant as $key => $value)
		{
			print "<td><FONT COLOR='".findColor($value)."'>".$value."$currency</font></td>";
			$final_total += $value;
			$total_line += $value;
			$tab_type_montant[$key] = 0;
		}
		print "<td class='tab_footer_colonne'><FONT COLOR='".findColor($total_line)."'>".$total_line."$currency</font></td></tr>";
	}
	print "</table>";
}
else
{
	$tab_type = getTypeRecap();
	$tab_type_montant = $tab_type;

	print "<table><tr align='center'><th></th>";
	foreach ($tab_type as $key => $value)
		print "<th>".$key."</th>";
	print "<th>TOTAL</th><th>Cotisations à réclamer</th><th>Cotisations à déposer</th><th>Demandé</th><th>Autorisé</th><tr>";
	foreach ($tab_asso as $asso)
	{
		$tab_total_supl = array("Cotis_demande" => 0,"Cotis_depot" => 0,"Demander" => 0,"Autoriser" => 0);

		print "<tr><td><font color='#f6f6f6'>|</font></td>";
		foreach ($tab_type as $key => $value)
			print "<td></td>";
		print "<td></td><td></td><td></td><td></td><td></td></tr>";
		print "<tr align='center'><td><b>".$asso['nom']."</b></td>";
		
		// Affichage des données d'une association
		if ($_SESSION['privilege'] == 1 || (isset($tot_asso) && $tot_asso > 0))
		{
			// Initialisation des variables
			$total_line = 0;
			$tab_paiement_ligne = getPaiementsAsso($asso['id'], $promo, $tab_type);
			$tab_paiement = $tab_paiement_ligne['Paiements'];

			// Affichage des paiement pour chaque type
			foreach ($tab_type as $key => $value)
			{
				if (isset($tab_paiement[$key]))
				{
					print "<td>";
					print "<FONT COLOR='".findColor($tab_paiement[$key])."'>";
					print $tab_paiement[$key]."$currency";
					print "</FONT>";
					print "</td>";
					$total_line += $tab_paiement[$key];
					$tab_type_montant[$key] += $tab_paiement[$key];
				}
				else
					print "<td>0$currency</td>";
			}
			
			// Total de la ligne
			print "<td class='tab_footer_colonne_top'><FONT COLOR='".findColor($total_line)."'>".$total_line."$currency</FONT></td>";
			
			// Informations supplémentaire pour la section
			$tab_total_supl['Cotis_demande'] += 0;
			$tab_total_supl['Cotis_depot'] += $tab_paiement_ligne['Cotis_depot'];
			$tab_total_supl['Demander'] += $tab_paiement_ligne['Demandé'];
			$tab_total_supl['Autoriser'] += $tab_paiement_ligne['Autorisé'];

			print 	"<td>0$currency</td>
					<td>".$tab_paiement_ligne['Cotis_depot']."$currency</td>
					<td><FONT COLOR='blue'>".$tab_paiement_ligne['Demandé']."$currency</font></td>
					<td><FONT COLOR='orange'>".$tab_paiement_ligne['Autorisé']."$currency</font></td>";
		}
		else
		{
			foreach ($tab_type as $key => $value)
				print "<td ></td>";
			print "<td class='tab_footer_colonne_top'></td><td ></td><td ></td><td ></td><td ></td>";
		}
		print "</tr>";
		
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
		foreach ($sections as $key => $value)
		{
			$list_id[$key] = $key;
			$list_id_ent[$key] = getActivitesBySection($key);
			foreach ($list_id_ent[$key] as $key_act => $value_act)
			{
				$list_id_ent[$key][$key_act] = getCreneauxByActivite($key_act);
				$list_id[$key] .= ', '.$key_act.'';
				foreach ($list_id_ent[$key][$key_act] as $key_cre => $value_cre)
					$list_id[$key] .= ', '.$key_cre.'';
			}
		}
		
		// Affichage des données d'une section
		foreach ($sections as $sec)
		{
			print "<tr align='center'>";
			print "<td>".$sec['nom']."</td>";
			
			// Récupération des paiements
			$total_line = 0;
			$tab_paiement_ligne = getPaiementsSec($asso['id'], $sec['id'], $promo, $tab_type, $list_id);
			$tab_paiement = $tab_paiement_ligne['Paiements'];

			// Affichage des paiement pour chaque type
			foreach ($tab_type as $key => $value)
			{
				if (isset($tab_paiement[$key]))
				{
					print "<td>";
					print "<FONT COLOR='".findColor($tab_paiement[$key])."'>";
					print $tab_paiement[$key]."$currency";
					print "</FONT>";
					print "</td>";
					$total_line += $tab_paiement[$key];
					$tab_type_montant[$key] += $tab_paiement[$key];
				}
				else
					print "<td>0$currency</td>";
			}
			
			// Total de la ligne
			print "<td class='tab_footer_colonne_top'><FONT COLOR='".findColor($total_line)."'>".$total_line."$currency</FONT></td>";
			
			// Informations supplémentaire pour la section
			$tab_total_supl['Cotis_demande'] += 0;
			$tab_total_supl['Cotis_depot'] += $tab_paiement_ligne['Cotis_depot'];
			$tab_total_supl['Demander'] += $tab_paiement_ligne['Demandé'];
			$tab_total_supl['Autoriser'] += $tab_paiement_ligne['Autorisé'];

			print 	"<td>0$currency</td>
					<td>".$tab_paiement_ligne['Cotis_depot']."$currency</td>
					<td><FONT COLOR='blue'>".$tab_paiement_ligne['Demandé']."$currency</font></td>
					<td><FONT COLOR='orange'>".$tab_paiement_ligne['Autorisé']."$currency</font></td>
					</tr>";
		}
		
		// Affichage des sous-totaux pour chaque type
		print "<tr align='center' class='tab_footer_line'><td><b>Sous-Total</b></td>";
		$final_total = 0;
		$total_line = 0;
		foreach ($tab_type_montant as $key => $value)
		{
			print "<td><FONT COLOR='".findColor($value)."'>".$value."$currency</font></td>";
			$final_total += $value;
			$total_line += $value;
			$tab_type_montant[$key] = 0;
		}
		print "<td class='tab_footer_colonne'><FONT COLOR='".findColor($total_line)."'>".$total_line."$currency</font></td><td>".$tab_total_supl['Cotis_demande']."$currency</td><td>".$tab_total_supl['Cotis_depot']."$currency</td><td><FONT COLOR='blue' >".$tab_total_supl['Demander']."$currency</font></td><td><FONT COLOR='orange' >".$tab_total_supl['Autoriser']."$currency</font></td></tr>";
	}
	print "</table>";
}
?>
<script type="text/javascript">
$('#promo').change( function (){
        window.location.search = "page=14&adh="+$.getUrlVar('adh')+"&promo="+$(this).val();
});
$('#choix_association').change( function (){
        window.location.search = "page=14&asso="+$(this).val();
});
</script>