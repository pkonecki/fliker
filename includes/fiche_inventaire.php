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

if (isset($_GET['promo']))
	$promo=$_GET['promo'];
else
	$promo=$current_promo;
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
	print '</ul>';
}
print "<br/>";

$list_enti = null;
$tmp_list_sec = null;
$tmp_adh_resp = null;
$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}association ORDER BY nom ASC");
while ($tmp_array = mysql_fetch_array($res))
	$list_enti[$tmp_array['id']] = $tmp_array['nom'];
$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}section ORDER BY nom ASC");
while ($tmp_array = mysql_fetch_array($res))
	$tmp_list_sec[$tmp_array['id']] = $tmp_array['nom'];

$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}resp_asso WHERE id_adh=".$_SESSION['uid']."");
if (mysql_num_rows($res) > 0)
	while ($tmp_array = mysql_fetch_array($res))
		$tmp_adh_resp[$tmp_array['id_asso']] = 1;

$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}resp_section WHERE id_adh=".$_SESSION['uid']."");
if (mysql_num_rows($res) > 0)
	while ($tmp_array = mysql_fetch_array($res))
		$tmp_adh_resp[$tmp_array['id_sec']] = 1;
		
$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}asso_section");
while ($tmp_array = mysql_fetch_array($res))
	$list_enti[$tmp_array['id_sec']] = $list_enti[$tmp_array['id_asso']]." => ".$tmp_list_sec[$tmp_array['id_sec']];
asort($list_enti);

if (isset($_POST['modifier']))
{
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}inventaire WHERE id_objet='".$_POST['id_obj']."' ");
	$tmp_array = mysql_fetch_array($res);
	if ($tmp_array['reservable'] == 1)
		$reservable_check = "checked";
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}inv_hist WHERE id_obj=".$_POST['id_obj']." ORDER BY date_modif DESC");
	$tab_quant = mysql_fetch_array($res);
	print 	"<table><form method='POST' action='index.php?page=17'>
				<tr align='center'><th>Nom</th><th>Description</th><th>Quantité</th><th>Réservable</th><th>Commentaire</th><th></th></tr>
				<tr><td><input type='text' name='nom' value='".$tmp_array['nom']."' /></td>
				<td><input type='text' name='description' value='".$tmp_array['description']."' /></td>
				<td><input type='text' name='quantite' value='".$tab_quant['quantite']."' /></td>
				<td><input type='checkbox' name='reservable' ".$reservable_check." /></td>
				<td><textarea rows='3' name='commentaire'></textarea></td>
				<td><input type='hidden' name='id' value=".$tmp_array['id_objet']." /><input type='hidden' name='old_res' value=".$tmp_array['reservable']." /><input name='modif_submit' type='submit'/></td></tr>
			</form></table>";
}
else if (isset($_POST['historique']))
{
	
	$res = doQuery("SELECT nom FROM {$GLOBALS['prefix_db']}inventaire WHERE id_objet=".$_POST['id_obj']."");
	$name_tab = mysql_fetch_array($res);
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}inv_hist WHERE id_obj=".$_POST['id_obj']." ORDER BY date_modif DESC");
	print "<h3 align='center'>Historique des modifications pour l'objet : ".$name_tab['nom']."</h3><br/>";
	print 	"<table align='center'>
				<tr align='center'><th>Quantité</th><th>Date de modification</th><th>Commentaire</th></tr>";
	while ($tmp_array = mysql_fetch_array($res))
		print "<tr align='center'><td>".$tmp_array['quantite']."</td><td>".$tmp_array['date_modif']."</td><td>".$tmp_array['commentaire']."</td></tr>";
	print	"</table>";
}
else
{
	if (isset($_POST['new_art']))
	{
		if (isset($_POST['reservable']))
			$reservable = 1;
		else
			$reservable = 0;
		$query = "INSERT INTO {$GLOBALS['prefix_db']}inventaire (id_entite, nom, description, date_enregistrement, reservable, promo) VALUES(".secur_data($_POST['appartenance']).", '".secur_data($_POST['name'])."', '".secur_data($_POST['description'])."', CURRENT_TIMESTAMP()+0, ".secur_data($reservable).", ".secur_data($current_promo).")";
		$res = doQuery($query);
		$query = "INSERT INTO {$GLOBALS['prefix_db']}inv_hist (id_obj, quantite, date_modif, commentaire) VALUES(".secur_data(mysql_insert_id()).", ".secur_data($_POST['number']).", CURRENT_TIMESTAMP()+0, '".secur_data($_POST['commentaire'])."')";
		$res = doQuery($query);
	}

	if (isset($_POST['modif_submit']))
	{
		$res = doQuery("UPDATE {$GLOBALS['prefix_db']}inventaire SET nom='".secur_data($_POST['nom'])."', description='".$_POST['description']."' ".(isset($_POST['reservable']) ? ", reservable=1" : ", reservable=0" )." WHERE id_objet=".$_POST['id']." ");
		$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}inv_hist WHERE id_obj=".$_POST['id']." ORDER BY date_modif DESC");
		$tab_quant = mysql_fetch_array($res);
		if ($tab_quant['quantite'] != $_POST['quantite'])
			$res = doQuery("INSERT INTO {$GLOBALS['prefix_db']}inv_hist (id_obj, quantite, date_modif, commentaire) VALUES(".$_POST['id'].", ".$_POST['quantite'].", CURRENT_TIMESTAMP()+0, '".$_POST['commentaire']."')");
	}
	if (isset($_POST['supprimer']))
	{
		doQuery("DELETE FROM {$GLOBALS['prefix_db']}inventaire WHERE id_objet='".$_POST['id_obj']."' ");
		doQuery("DELETE FROM {$GLOBALS['prefix_db']}inv_hist WHERE id_obj='".$_POST['id_obj']."' ");
	}
	print "<table><form method='POST' action='index.php?page=17'>";
	print "<tr><th align='center' colspan='2'>Enregistrer un nouvel article</th></tr>";
	print "<tr><td>Nom objet</td><td><input type='text' name='name'/></td></tr>";
	print "<tr><td>Description</td><td><input type='text' name='description'/></td></tr>";
	print "<tr><td>Quantité</td><td><input type='number' name='number' required/></td></tr>";
	print "<tr><td>Appartenance</td><td><select name='appartenance'>";
	foreach ($list_enti as $key => $value)
			if (isset($tmp_adh_resp[$key]) || $_SESSION['privilege'] == 1)
				print "<option name='choix' value='".$key."'>".$value."</option>";
	print "</select></td></tr>";
	print "<tr><td>Réservable</td><td><input type='checkbox' name='reservable'/></td></tr>";
	print "<tr><td colspan='2' align='center'><input type='hidden' name='commentaire' value='Ajout objet'/><input type='submit' name='new_art' /></td></tr>";
	print "</form></table>";

	if ($_SESSION['privilege'] == 1)
		$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}inventaire");
	else
		$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}inventaire WHERE id_entite IN (SELECT id_asso FROM {$GLOBALS['prefix_db']}resp_asso WHERE id_adh=".$_SESSION['uid'].") OR id_entite IN (SELECT id_sec FROM {$GLOBALS['prefix_db']}resp_section WHERE id_adh=".$_SESSION['uid'].")");
	
	while ($tmp_array = mysql_fetch_array($res))
		$tab_inv[$tmp_array['id_objet']] = $tmp_array;

	print "<br/>";
	print 	"<table>
			<tr align='center'><th>Nom</th><th>Description</th><th>Quantité</th><th>Date d'enregistrement</th><th>Date de vérification</th><th>Amortissement</th><th>Réservable</th><th>Action</th></tr>";
			foreach ($tab_inv as $tmp_tab)
			{
				$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}inv_hist WHERE id_obj=".$tmp_tab['id_objet']." ORDER BY date_modif DESC");
				$tab_quant = mysql_fetch_array($res);
				print "<tr><td>".$tmp_tab['nom']."</td><td>".$tmp_tab['description']."</td><td>".$tab_quant['quantite']."</td><td>".$tmp_tab['date_enregistrement']."</td><td>".$tmp_tab['dates_verification']."</td><td>".$tmp_tab['amortissement']."</td><td>".($tmp_tab['reservable'] == 1 ? "Oui" : "Non")."</td><td><form name='action_inv' method='POST' action='index.php?page=17'><input type='hidden' name='id_obj' value='".$tmp_tab['id_objet']."'/><input name='modifier' title='Modifier' border='0' type='image' src='./images/icone_edit.png' height='17' width='17' value='submit' /><input name='supprimer' title='Supprimer' border='0' type='image' src='./images/icone_delete.png' height='17' width='17' value='submit' /><input type='submit' name='historique' value='Historique'/></form></td></tr>";
			}
	print	"</table>";
}
?>
