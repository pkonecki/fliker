<?php

defined('_VALID_INCLUDE') or die('Direct access not allowed.');
if(!($_SESSION['privilege'] === '1'))
{
	print "Vous n'avez pas accès à cette page.";
	print $die_footer;
	die();
}

if(isset($_GET['promo']))
	$promo = $_GET['promo'];
else
	$promo = $current_promo;

if (isset($_POST['send_tab']))
{
	doQuery("UPDATE {$GLOBALS['prefix_db']}champs_adherent SET type='".$_POST['type']."', description='".secur_data($_POST['description'])."', ordre='".$_POST['ordre']."' ".(isset($_POST['inscription']) ? ", inscription=1" : ", inscription=0")." ".(isset($_POST['admin']) ? ", admin=1" : ", admin=0")." ".(isset($_POST['user_editable']) ? ", user_editable=1" : ", user_editable=0")." ".(isset($_POST['user_viewable']) ? ", user_viewable=1" : ", user_viewable=0")." ".(isset($_POST['search_simple']) ? ", search_simple=1" : ", search_simple=0")." ".(isset($_POST['search_trombi']) ? ", search_trombi=1" : ", search_trombi=0")." WHERE nom='".$_POST['nom']."' ");
}

print "<table><tr align='center'><th>Nom</th><th>Type</th><th>Description</th><th>Formulaire d'inscription</th><th>Affichage de son profil</th><th>Modification de son profil</th><th>Modification d'un profil</th><th>Affichage recherche simple</th><th>Affichage trombi</th><th>Format</th><th>Ordre</th><th>Requis</th><th></th></tr>";
$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}champs_adherent ORDER BY ordre ASC, type ASC, nom ASC");
while ($tmp_array = mysql_fetch_array($res))
	print "<tr align='center'><form method='POST' action='index.php?page=19'><td><input type='hidden' name='nom' value='".$tmp_array['nom']."'/>".$tmp_array['nom']."</td><td><input type='text' name='type' value='".$tmp_array['type']."' /></td><td><input type='text' name='description' value=\"".htmlentities($tmp_array['description'])."\" /></td><td><input type='checkbox' name='inscription' ".($tmp_array['inscription'] == 0 ? "" : "checked")." /></td><td><input type='checkbox' name='user_viewable' ".($tmp_array['user_viewable'] == 0 ? "" : "checked")." /></td><td><input type='checkbox' name='user_editable' ".($tmp_array['user_editable'] == 0 ? "" : "checked")." /></td><td><input type='checkbox' name='admin' ".($tmp_array['admin'] == 0 ? "" : "checked")." /></td><td><input type='checkbox' name='search_simple' ".($tmp_array['search_simple'] == 0 ? "" : "checked")." /></td><td><input type='checkbox' name='search_trombi' ".($tmp_array['search_trombi'] == 0 ? "" : "checked")." /></td><td>".$tmp_array['format']."</td><td><input type='text' name='ordre' value='".$tmp_array['ordre']."' /></td><td><input type='checkbox' name='required' ".($tmp_array['required'] == 0 ? "" : "checked")." /></td><td><input type='submit' name='send_tab' value='Enregistrer' /></td></form></tr>";
print "</table>";

?>