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

if (isset($_POST['purge']))	// Page purger l'année en cours
{
	print 	"<FORM action=\"index.php?page=9\" method=\"POST\">
			Lors d'une purge de données les certifications médicales sont supprimé et les adhérents n'ayant jamais effectué de paiement sont supprimé.<br/></br>Etes-vous sûr de vouloir effectuer cette action ?<br/><br/>   => ";
	print	"<input type=\"submit\" name='purge_confirmed' value='Oui'/>";
	print	"<input type=\"submit\" name='no_purge' value='Non'/>";
	print	"</form><br/>";
}
else
{
	if (isset($_POST['modif_statut']))
	{
		$res = doQuery("UPDATE {$GLOBALS['prefix_db']}statut SET nom='".secur_data($_POST['modif_statut'])."' WHERE id=".$_POST['old_statut']." ");
		if (!$res)
			print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération.</b></font>";
		else
			print "<FONT COLOR='#16B84E'><b>Statut modifié avec succès.</b></font>";
	}
	else if (isset($_POST['supr_statut']))
	{
		$res = doQuery("DELETE FROM {$GLOBALS['prefix_db']}statut WHERE id=".$_POST['supr_statut']." ");
		if (!$res)
			print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération.</b></font>";
		else
			print "<FONT COLOR='#16B84E'><b>Statut supprimé avec succès.</b></font>";
	}
	else if (isset($_POST['new_statut']))
	{
		$res = doQuery("INSERT INTO {$GLOBALS['prefix_db']}statut (nom) VALUES('".secur_data($_POST['new_statut'])."')");
		if (!$res)
			print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération.</b></font>";
		else
			print "<FONT COLOR='#16B84E'><b>Statut ajouté avec succès.</b></font>";
	}
	else if (isset($_POST['modif_type_transa']))
	{
		$res = doQuery("UPDATE {$GLOBALS['prefix_db']}type_transa SET nom='".secur_data($_POST['modif_type_transa'])."' WHERE id=".$_POST['old_type_transa']." ");
		if (!$res)
			print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération.</b></font>";
		else
			print "<FONT COLOR='#16B84E'><b>Valeur modifié avec succès.</b></font>";
	}
	else if (isset($_POST['supr_type_transa']))
	{
		$res = doQuery("DELETE FROM {$GLOBALS['prefix_db']}type_transa WHERE id=".$_POST['supr_type_transa']." ");
		if (!$res)
			print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération.</b></font>";
		else
			print "<FONT COLOR='#16B84E'><b>Valeur supprimé avec succès.</b></font>";
	}
	else if (isset($_POST['new_type_transa']))
	{
		$res = doQuery("INSERT INTO {$GLOBALS['prefix_db']}type_transa (nom) VALUES('".secur_data($_POST['new_type_transa'])."')");
		if (!$res)
			print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération.</b></font>";
		else
			print "<FONT COLOR='#16B84E'><b>Valeur ajouté avec succès.</b></font>";
	}
	else if (isset($_POST['modif_type_dep']))
	{
		$res = doQuery("UPDATE {$GLOBALS['prefix_db']}type_dep SET nom='".secur_data($_POST['modif_type_dep'])."' WHERE id=".$_POST['old_type_dep']." ");
		if (!$res)
			print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération.</b></font>";
		else
			print "<FONT COLOR='#16B84E'><b>Valeur modifié avec succès.</b></font>";
	}
	else if (isset($_POST['supr_type_dep']))
	{
		$res = doQuery("DELETE FROM {$GLOBALS['prefix_db']}type_dep WHERE id=".$_POST['supr_type_dep']." ");
		if (!$res)
			print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération.</b></font>";
		else
			print "<FONT COLOR='#16B84E'><b>Valeur supprimé avec succès.</b></font>";
	}
	else if (isset($_POST['new_type_dep']))
	{
		$res = doQuery("INSERT INTO {$GLOBALS['prefix_db']}type_dep (nom) VALUES('".secur_data($_POST['new_type_dep'])."')");
		if (!$res)
			print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération.</b></font>";
		else
			print "<FONT COLOR='#16B84E'><b>Valeur ajouté avec succès.</b></font>";
	}
	else if (isset($_POST['new_famille']))
	{
		$res = doQuery("INSERT INTO {$GLOBALS['prefix_db']}famille (nom) VALUES('".secur_data($_POST['new_famille'])."')");
		if (!$res)
			print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération.</b></font>";
		else
			print "<FONT COLOR='#16B84E'><b>Valeur ajouté avec succès.</b></font>";
	}
	else if (isset($_POST['modif_famille']))
	{
		$res = doQuery("UPDATE {$GLOBALS['prefix_db']}famille SET nom='".secur_data($_POST['modif_famille'])."' WHERE id=".$_POST['old_famille']." ");
		if (!$res)
			print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération.</b></font>";
		else
			print "<FONT COLOR='#16B84E'><b>Valeur modifié avec succès.</b></font>";
	}
	else if (isset($_POST['modifier_reductions']))
	{
		$res = doQuery("UPDATE {$GLOBALS['prefix_db']}reductions SET valeur='".secur_data($_POST['valeur'])."' WHERE id=".$_POST['id']." ");
		if (!$res)
			print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération.</b></font>";
		else
			print "<FONT COLOR='#16B84E'><b>Valeur modifié avec succès.</b></font>";
	}
	else if (isset($_POST['synchro_wiki']))	// Page synchronisant les comptes avec le wiki
	{
		$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent";
		include("opendb.php");
		$res = mysql_query($query);
		if (!$res)
			echo mysql_error();
		else
		{
			$i = 0;
			while ($list_adh[$i] = mysql_fetch_array($res))
			{
				if ($list_adh[$i]['email'] != "")
					$list_adh[$i]['email'][0] = strtoupper($list_adh[$i]['email'][0]);
				$i++;
			}
			$query = "SELECT * FROM mw_user";
			include("opendb_wiki.php");
			$res = mysql_query($query);
			if (!$res)
				echo mysql_error();
			else
			{
				$i = 0;
				while ($list_adh_wiki[$i] = mysql_fetch_array($res))
					$i++;
				$i = 0;
				while ($list_adh[$i])
				{
					$to_add = true;
					$count = 0;
					while ($list_adh_wiki[$count])
					{
						if ($list_adh_wiki[$count]['user_name'] == $list_adh[$i]['email'])
							$to_add = false;
						$count++;
					}
					if ($to_add == true)
						$EspaceMembre->addWiki($list_adh[$i]['id']);
					$i++;
				}
			}
		}
	}
	else if (isset($_POST['purge_confirmed']))	// Page synchronisant les comptes avec le wiki
	{
		$query = "UPDATE {$GLOBALS['prefix_db']}adherent SET certmed=''";
		include("opendb.php");
		$res = mysql_query($query);
		if (!$res)
			echo mysql_error();
		$dirname = './certmed/';
		$dir = opendir($dirname); 
		while($file = readdir($dir))
		{
			if($file != '.' && $file != '..' && !is_dir($dirname.$file) && $file != "index.html")
				unlink("./certmed/".$file);
		}
		closedir($dir);
		
		$query = "SELECT DISTINCT id_adh FROM {$GLOBALS['prefix_db']}paiement";
		include("closedb.php");
		include("opendb.php");
		$res = mysql_query($query);
		if (!$res)
			echo mysql_error();
		else
		{
			$tmp_setup = "";
			while ($tmp = mysql_fetch_array($res))
			{
				$array_adhs[$tmp['id_adh']] = 1;
				$tmp_setup .= " AND id != ".$tmp['id_adh']."";
			}
			$query = "SELECT id FROM {$GLOBALS['prefix_db']}adherent WHERE 1=1". $tmp_setup." ORDER BY id ASC";
			include("opendb.php");
			$res = mysql_query($query);
			if (!$res)
				echo mysql_error();
			else
			{
				while ($tmp = mysql_fetch_array($res))
				{
					print $tmp['id']."<br/>";
				}
			}
		}
	}
	if(isset($_POST['supr_asso']) && $_POST['choix_asso_type'] != 0)
	{
		$query = "SELECT * FROM {$GLOBALS['prefix_db']}asso_section WHERE id_asso='".$_POST['choix_asso_type']."' ";
		include("opendb.php");
		$res = mysql_query($query);
		if (!$res)
		{
			print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération.</b></font>";
			echo mysql_error();
		}
		else if (mysql_num_rows($res) == 0)
		{
			$query = "DELETE FROM {$GLOBALS['prefix_db']}association WHERE id='".$_POST['choix_asso_type']."' ";
			include("opendb.php");
			$res = mysql_query($query);
			if (!$res)
			{
				print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération.</b></font>";
				echo mysql_error();
			}
			else
				print "<FONT COLOR='#16B84E'><b>L'association a été supprimé.</b></font>";
		}
		else
			print "<FONT COLOR='#FF0000'><b>L'association contient encore des sections.</b></font>";
	}
	
	if(isset($_POST['action']) && $_POST['action'] === "setparam")
		setParam($_POST['id'], htmlspecialchars($_POST['valeur']));
	$params = getConfig("conf");
	$params_2 = getConfigBis("conf");

	print 	"<FORM action=\"index.php?page=9\" method=\"POST\">
			<input type='hidden' name='synchro_wiki' value='1'/>
			Insérer les nouveaux comptes Fliker dans Wiki <img src='./images/Button_Next.png' height='18' width='18' > ";
	if ($params['is_wiki.conf'] == "true")
		print	"<input type=\"submit\" value='Synchroniser maintenant'/>";
	else
		print "Activation d'une base de données Wiki nécessaire";
	print	"</form><br/>";

	print 	"<FORM action=\"index.php?page=9\" method=\"POST\">
			<input type='hidden' name='purge' value='1'/>
			Purge de l'année courante (certmed, adherents sans paiements) <img src='./images/Button_Next.png' height='18' width='18' > ";
	print	"<input type=\"submit\" value='Purger maintenant'/>";
	print	"</form><br/>";

	$tab = getAssociations($_SESSION['uid']);
	print 	"<FORM action=\"index.php?page=9\" method=\"POST\">";
	print 'Supprimer une association <select id="choix_asso_type" name="choix_asso_type">
			<option selected  name="choix" value="0">Aucune</option>';
	foreach($tab as $asso)
		print '<option name="choix" value="'.$asso['id'].'">'.$asso['nom'].'</option>';
	print	"<input type=\"submit\" name='supr_asso' value='Supprimer'/>";
	print	"</form>";
	
	print 	"<br/><table>
			<tr><th colspan='3' align='center'>Gestion des statuts</th></tr>
			<form method='POST' action='index.php?page=9'><tr><td>Ajouter : </td><td><input name='new_statut' type='text'/></td><td><input type='submit'/></td></tr></form>
			
			<form method='POST' action='index.php?page=9'><tr><td>Modifier : </td><td>Ancien : <select name='old_statut'>";
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}statut ORDER BY nom ASC");
	while ($tmp_array = mysql_fetch_array($res))
		print "<option name='choix' value='".$tmp_array['id']."'>".$tmp_array['nom']."</option>";	
	print	"</select><br/>Nouveau : <input name='modif_statut' type='text'/></td><td><input type='submit'/></td></tr></form>";
			
	// print	"<form method='POST' action='index.php?page=9'><tr><td>Supprimer : </td><td><select name='supr_statut'>";
	// $res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}statut ORDER BY nom ASC");
	// while ($tmp_array = mysql_fetch_array($res))
		// print "<option name='choix' value='".$tmp_array['id']."'>".$tmp_array['nom']."</option>";
	// print 	"</select></td><td><input type='submit'/></td></tr></form>";
	print 		"</table>";
	
	print 	"<br/><table>
			<tr><th colspan='3' align='center'>Gestion des types de transaction</th></tr>
			<form method='POST' action='index.php?page=9'><tr><td>Ajouter : </td><td><input name='new_type_transa' type='text'/></td><td><input type='submit'/></td></tr></form>
			
			<form method='POST' action='index.php?page=9'><tr><td>Modifier : </td><td>Ancien : <select name='old_type_transa'>";
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}type_transa ORDER BY nom ASC");
	while ($tmp_array = mysql_fetch_array($res))
		print "<option name='choix' value='".$tmp_array['id']."'>".$tmp_array['nom']."</option>";	
	print	"</select><br/>Nouveau : <input name='modif_type_transa' type='text'/></td><td><input type='submit'/></td></tr></form>";
			
	// print	"<form method='POST' action='index.php?page=9'><tr><td>Supprimer : </td><td><select name='supr_type_transa'>";
	// $res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}type_transa ORDER BY nom ASC");
	// while ($tmp_array = mysql_fetch_array($res))
		// print "<option name='choix' value='".$tmp_array['id']."'>".$tmp_array['nom']."</option>";
	// print 	"</select></td><td><input type='submit'/></td></tr></form>";
	print 		"</table>";
	
	print 	"<br/><table>
			<tr><th colspan='3' align='center'>Gestion des types de dépenses</th></tr>
			<form method='POST' action='index.php?page=9'><tr><td>Ajouter : </td><td><input name='new_type_dep' type='text'/></td><td><input type='submit'/></td></tr></form>
			
			<form method='POST' action='index.php?page=9'><tr><td>Modifier : </td><td>Ancien : <select name='old_type_dep'>";
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}type_dep ORDER BY nom ASC");
	while ($tmp_array = mysql_fetch_array($res))
		print "<option name='choix' value='".$tmp_array['id']."'>".$tmp_array['nom']."</option>";	
	print	"</select><br/>Nouveau : <input name='modif_type_dep' type='text'/></td><td><input type='submit'/></td></tr></form>";
			
	// print	"<form method='POST' action='index.php?page=9'><tr><td>Supprimer : </td><td><select name='supr_type_dep'>";
	// $res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}type_dep ORDER BY nom ASC");
	// while ($tmp_array = mysql_fetch_array($res))
		// print "<option name='choix' value='".$tmp_array['id']."'>".$tmp_array['nom']."</option>";
	// print 	"</select></td><td><input type='submit'/></td></tr></form>";
	print 		"</table>";
	
	print 	"<br/><table>
			<tr><th colspan='3' align='center'>Gestion des familles</th></tr>
			<form method='POST' action='index.php?page=9'><tr><td>Ajouter : </td><td><input name='new_famille' type='text'/></td><td><input type='submit'/></td></tr></form>
			
			<form method='POST' action='index.php?page=9'><tr><td>Modifier : </td><td>Ancien : <select name='old_famille'>";
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}famille ORDER BY nom ASC");
	while ($tmp_array = mysql_fetch_array($res))
		print "<option name='choix' value='".$tmp_array['id']."'>".$tmp_array['nom']."</option>";	
	print	"</select><br/>Nouveau : <input name='modif_famille' type='text'/></td><td><input type='submit'/></td></tr></form>";
	print 	"</table>";
	
		
	print 	"<br/><table>
			<tr><th colspan='3' align='center'>Gestion des réductions</th></tr>";
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}reductions ORDER BY nom ASC");
	while ($tmp_array = mysql_fetch_array($res))
		print "<tr><form method='POST' action='index.php?page=9'><td align='right'>".$tmp_array['nom']." :</td><td><input type='text' name='valeur' value='".$tmp_array['valeur']."' size='2'>%</td><td><input type='hidden' name='id' value='".$tmp_array['id']."' ><input type='hidden' name='modifier_reductions' ><input type='submit' value='Modifier' ></td></tr></form>";	
	print 	"</table>";
	
	print "<br/><br/>";
	$table_config = "<table id=\"table_config\" >";
	foreach($params as $key => $value)
	{
		$value = htmlentities($value);
		$table_config .= "<FORM action=\"index.php?page=9\" method=\"POST\">
						  <tr><td><input type=\"text\" name=\"valeur\" value=\"$value\"></input></td><td width='220'>".$params_2[$key]."</td><td><input type=\"submit\" /></td></tr>
						  <input type=\"hidden\" name=\"action\" value=\"setparam\">
						  <input type=\"hidden\" name=\"id\" value=\"$key\">
						  </FORM>";
	}
	$table_config .= "</table>";
	print $table_config;
}
?>