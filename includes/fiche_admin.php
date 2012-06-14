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

if (isset($_POST['replace_email']))	// Page admin utilisé pour le changement d'une adresse email
{
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE email = '".$_POST['replace_email']."' ";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	else if (mysql_num_rows($results) > 1)
		print "Il y a plusieurs utilisateurs possédant cette adresse email, impossible d'effectuer le remplacement.";
	else
	{
		$stock_result = mysql_fetch_array($results);
		if ($stock_result['active'] == 1)
			$user_actif = "actif";
		else
			$user_actif = "non activé";
		print "Vous souhaitez remplacer l'adresse email <b>".$stock_result['email']."</b> de ".$stock_result['prenom']." ".$stock_result['nom']." possédant un compte <b>".$user_actif."</b>.";
		print 	'<br /><br />';
		print 	"<FORM action=\"index.php?page=9\" method=\"POST\">
				<input type='hidden' name='old_email' value='".$stock_result['email']."' />
				Choisissez la nouvelle adresse email :<input type='text' name='new_email'></input>
				<input type=\"submit\" />
				</form>";
	}
	include("closedb.php");
}
else if (isset($_POST['modif_compte']))	// Page modifiant l'activité d'un compte
{
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE email = '".$_POST['modif_compte']."' ";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	else if (mysql_num_rows($results) > 1)
		print "Il y a plusieurs utilisateurs possédant cette adresse email, impossible d'effectuer le remplacement.";
	else
	{
		$text_activ = "";
		$stock_result = mysql_fetch_array($results);
		if ($stock_result['active'] == 1)
		{
			$user_actif = "actif";
			$text_activ = "dés";
		}
		else
			$user_actif = "non activé";
		print "Vous souhaitez modifier l'activité du compte ayant l'email <b>".$stock_result['email']."</b>, le prenom ".$stock_result['prenom'].", le nom ".$stock_result['nom']." et possédant un compte <b>".$user_actif."</b>.";
		print 	'<br /><br />';
		print 	"<FORM action=\"index.php?page=9\" method=\"POST\">
				<input type='hidden' name='etat_compte' value='".$stock_result['active']."' />
				<input type='hidden' name='etat_compte_email' value='".$stock_result['email']."' />
				En cliquant sur le bouton ci-dessous vous allez <b>".$text_activ."activer</b> le compte<br/><input type=\"submit\" value='Effectuer le changement' />
				</form>";
	}
}
else	// Page admin de base
{
	if (isset($_POST['new_email']))
	{
		$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE email = '".$_POST['new_email']."' ";
		include("opendb.php");
		$results = mysql_query($query);
		if (!$results)
			echo mysql_error();
		if ( mysql_num_rows($results) == 0)
		{
			$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE email = '".$_POST['old_email']."' ";
			include("opendb.php");
			$res = mysql_query($query);
			if (!$res)
				echo mysql_error();
			else
			{
				$array_adh = mysql_fetch_array($res);
				$EspaceMembre = new EspaceMembre;
				if ($EspaceMembre->updateUser("email", $_POST['new_email'], $array_adh['email']) == TRUE)
					print "<FONT COLOR='#16B84E'><b>Le changement d'adresse a été effectué avec succès.</b></font>";
				else
					print "<FONT COLOR='#FF0000'><b>Un problème est servenu lors de la mise à jour, le changement n'a pas été réalisé.</b></font>";
				print "<br /><br/>";
			}
		}
		else
			print "<FONT COLOR='#FF0000'><b>L'adresse email existe déjà dans notre base de données.</b></font><br /><br />";
		include("closedb.php");
	}
	if (isset($_POST['etat_compte']))
	{
		if ($_POST['etat_compte'] == 1)
			$var_tmp = 0;
		else
			$var_tmp = 1;
		$query = "UPDATE {$GLOBALS['prefix_db']}adherent SET active=".$var_tmp." WHERE email='".$_POST['etat_compte_email']."' ";
		include("opendb.php");
		$res = mysql_query($query);
		if (!$res)
		{
			echo mysql_error();
			print "<FONT COLOR='#FF0000'><b>Erreur lors de la mise à jour du compte.</b></font><br /><br />";
		}
		else
			print "<FONT COLOR='#16B84E'><b>Mise à jour du compte effectué avec succès.</b></font>";
	}
	if(isset($_POST['action']) && $_POST['action'] === "setparam")
		setParam($_POST['id'], htmlspecialchars($_POST['valeur']));
	$params = getParams();
	$params_2 = getParamsBis();
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
	print '<br />';
	print 	"<FORM action=\"index.php?page=9\" method=\"POST\">
			Activer ou désactiver le compte correspondant à l'adresse email suivante :<input type='text' name='modif_compte'></input>
			<input type=\"submit\" />
			</form>";
	print '<br />';
	print 	"<FORM action=\"index.php?page=9\" method=\"POST\">
			Adresse email à remplacer :<input type='text' name='replace_email'></input>
			<input type=\"submit\" />
			</form>";
}
?>