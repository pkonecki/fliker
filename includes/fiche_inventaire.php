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


		
if (isset($_POST['number']))
{
	$id_entitie = null;
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}resp_asso WHERE id_adh=".$_SESSION['uid']."");
	if (mysql_num_rows() < 0)
	{
		$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}resp_section WHERE id_adh=".$_SESSION['uid']."");
		$tmp_array = mysql_fetch_array($res);
		$id_entitie = $tmp_array['id_sec'];
	}
	else
	{
		$tmp_array = mysql_fetch_array($res);
		$id_entitie = $tmp_array['id_asso'];
	}
	if (isset($_POST['reservable']))
		$reservable = 1;
	else
		$reservable = 0;
	//$query = "INSERT INTO {$GLOBALS['prefix_db']}inventaire (id_entite, nom, description, quantite, dates_entree, reservable) VALUES(".$id_entitie.", '".$_POST['name']."', '".$_POST['description']."', '".$_POST['number']."', CURRENT_TIMESTAMP()+0, ".$reservable.")";
	//$res = doQuery($query);
}

if (isset($_POST['modifier']))
{
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}inventaire WHERE id_objet='".$_POST['id_obj']."' ");
	$tmp_array = mysql_fetch_array($res);
	if ($tmp_array['reservable'] == 1)
		$reservable_check = "checked";
	print 	"<table><form method='POST' action='index.php?page=17'>
				<tr><th>Nom</th><th>Description</th><th>Quantité</th><th>Réservable</th><th></th></tr>
				<tr><td><input type='text' name='nom' value='".$tmp_array['nom']."' /></td>
				<td><input type='text' name='description' value='".$tmp_array['description']."' /></td>
				<td><input type='text' name='quantite' value='".$tmp_array['quantite']."' /></td>
				<td><input type='checkbox' name='reservable' ".$reservable_check." /></td>
				<td><input type='hidden' name='id' value=".$tmp_array['id_objet']." /><input type='hidden' name='old_res' value=".$tmp_array['reservable']." /><input name='modif_submit' type='submit'/></td></tr>
			</form></table>";
}
else
{
	if (isset($_POST['modif_submit']))
		$res = doQuery("UPDATE {$GLOBALS['prefix_db']}inventaire SET nom='".$_POST['nom']."', description='".$_POST['description']."', quantite=".$_POST['quantite']." ".(isset($_POST['reservable']) ? ", reservable=1" : ", reservable=0" )." WHERE id_objet=".$_POST['id']." ");
	if (isset($_POST['supprimer']))
		doQuery("DELETE FROM {$GLOBALS['prefix_db']}inventaire WHERE id_objet='".$_POST['id_obj']."' ");
	print "<table><form method='POST' action='index.php?page=17'>";
	print "<tr><th colspan='2'>Enregistrer un nouvel article</th></tr>";
	print "<tr><td>Nom objet</td><td><input type='text' name='name'/></td></tr>";
	print "<tr><td>Description</td><td><input type='text' name='description'/></td></tr>";
	print "<tr><td>Quantité</td><td><input type='text' name='number'/></td></tr>";
	print "<tr><td>Réservable</td><td><input type='checkbox' name='reservable'/></td></tr>";
	print "<tr><td colspan='2' align='center'><input type='submit'/></td></tr>";
	print "</form></table>";

	$id_entitie = null;
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}resp_asso WHERE id_adh=".$_SESSION['uid']."");
	if (mysql_num_rows() < 0)
	{
		$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}resp_section WHERE id_adh=".$_SESSION['uid']."");
		$tmp_array = mysql_fetch_array($res);
		$id_entitie = $tmp_array['id_sec'];
	}
	else
	{
		$tmp_array = mysql_fetch_array($res);
		$id_entitie = $tmp_array['id_asso'];
	}
	$id_entitie = 108;
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}inventaire WHERE id_entite='".$id_entitie."' ");

	print 	"<table>
			<tr align='center'><th>Nom</th><th>Description</th><th>Quantité</th><th>Date d'entrée</th><th>Date de sortie</th><th>Date de vérification</th><th>Amortissement</th><th>Réservable</th><th>Action</th></tr>";
			while ($tmp_array = mysql_fetch_array($res))
				print "<tr><td>".$tmp_array['nom']."</td><td>".$tmp_array['description']."</td><td>".$tmp_array['quantite']."</td><td>".$tmp_array['dates_entree']."</td><td>".$tmp_array['dates_sortie']."</td><td>".$tmp_array['dates_verification']."</td><td>".$tmp_array['amortissement']."</td><td>".($tmp_array['reservable'] == 1 ? "Oui" : "Non")."</td><td><form name='action_inv' method='POST' action='index.php?page=17'><input type='hidden' name='id_obj' value='".$tmp_array['id_objet']."'/><input name='modifier' title='Modifier' border='0' type='image' src='./images/icone_edit.png' height='17' width='17' value='submit' /><input name='supprimer' title='Supprimer' border='0' type='image' src='./images/icone_delete.png' height='17' width='17' value='submit' /><input type='submit' name='historique' value='Historique'/></form></td></tr>";
	print	"</table>";
}
?>
