<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
$tot_asso=count(getAssociations($_SESSION['uid']));
$tot_sec=count(getSections($_SESSION['uid']));
$tot_act=count(getActivites($_SESSION['uid']));
$tot_cre=count(getCreneaux($_SESSION['uid']));
$tot = $tot_asso + $tot_sec + $tot_act + $tot_cre;
if(!($_SESSION['privilege'] === '1') && $tot_asso <= 0)
{
	print "Vous n'avez pas acc�s � cette page.";
	die();
}

print '<ul id="submenu">';
if(isset($tot_asso) && $tot_asso > 0)
	print '<li><a class="'.(($_GET['page']==3) ? 'selected' : '').'" href="index.php?page=3">Associations</a></li>';
if(isset($tot_sec) && $tot_sec > 0)
	print '<li><a class="'.(($_GET['page']==4) ? 'selected' : '').'" href="index.php?page=4">Sections</a></li>';
if(isset($tot_act) && $tot_act > 0)
	print '<li><a class="'.(($_GET['page']==5) ? 'selected' : '').'" href="index.php?page=5">Activit�s</a></li>';
if(isset($tot_cre) && $tot_cre > 0)
	print '<li><a class="'.(($_GET['page']==6) ? 'selected' : '').'" href="index.php?page=6">Cr�neaux</a></li>';
if(isset($tot_asso) && $tot_asso > 0)
	print '<li><a class="'.(($_GET['page']==12) ? 'selected' : '').'" href="index.php?page=12">Utilisateurs</a></li>';
if(isset($tot_cre) && $tot_cre > 0)
	print '<li><a class="'.(($_GET['page']==20) ? 'selected' : '').'" href="index.php?page=20">Statistiques</a></li>';
print '</ul>';

if(isset($_GET['promo']))
	$promo = $_GET['promo'];
else
	$promo = $current_promo;

if (isset($_POST['modif_compte'])) // Page des informations personnelles d'un compte utilisateur
{
	print '<br />';
	print 	"<FORM action=\"index.php?page=12\" method=\"POST\">
			Chercher un autre compte ? <input type='text' name='modif_compte'></input>
			<input type=\"submit\" />
			</form><br/>";
	$tab = getChampsAdherents();
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE email = '".$_POST['modif_compte']."' ";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	else if (mysql_num_rows($results) > 1)
		print "Il y a plusieurs utilisateurs poss�dant cette adresse email, impossible d'effectuer le remplacement.";
	else
	{
		$final = mysql_fetch_array($results);
		print "Informations personnelles de <b>".$_POST['modif_compte']."</b> :<br/><br/>";
		print "<table><form action='index.php?page=12' enctype='multipart/form-data' method='POST'>";
		foreach($tab as $row)
		{
			if ($row['admin']==1)
			{
				if($row['format'] === "categorie")
				{
					print '<tr><td>'.$row['description'].' : </td>
						<td>
						<input type=radio name='.$row['nom'].'  value="M" '.($final[$row['nom']] == 'M' ? 'checked' : '').'>Masculin
						<input type=radio name='.$row['nom'].'  value="F" '.($final[$row['nom']] == 'F' ? 'checked' : '').'>F�minin
						</td>
						</tr>
						</div>';
				}
				else if($row['format'] === "active")
				{
					print 	"<tr><td>Etat du compte : </td>
							<td>
							<input type=radio name='".$row['nom']."' value=0 ".($final[$row['nom']] == 0 ? 'checked' : '').">Inactif
							<input type=radio name='".$row['nom']."'  value=1 ".($final[$row['nom']] == 1 ? 'checked' : '').">Activ�
							</td>
							</tr>";
				}
				else if($row['type']==='varchar')
					print '<tr><td>'.$row['description'].' : </td><td><input type=text name="'.$row['nom'].'" value="'.$final[$row['nom']].'" ></td></tr>';
				else if($row['type']==='tinyint')
					print '<tr><td>'.$row['description'].' : </td><td><input type=checkbox name='.$row['nom'].' '.($final[$row['nom']] == 1 ? 'checked' : '').'></td></tr>';
				else if($row['type']==='file')
					print '<tr><td>'.$row['description'].' : </td><td><input type=file name='.$row['nom'].' value="'.$final[$row['nom']].'"></td></tr>';
				else if($row['type']==='select')
				{
					$values = getSelect($row['nom']);
					print '<tr><td>'.$row['description'].' : </td><td><SELECT name="id_'.$row['nom'].'" >';
					foreach($values as $key => $value)
						print '<OPTION value="'.$key.'" '.($final['id_'.$row['nom'].''] == $key ? 'selected' : '').'>'.$value.'</OPTION>';
					print '</SELECT></td></tr>';
				}
			}
		}
		print "<tr><td colspan='2' align='center'><input type='submit' name='modif_compte_submitted' value='Enregistrer'></td></tr>";
		print "</form></table>";
	}
}
else	// Page demande de l'adresse email et traitements
{
	if (isset($_POST['modif_compte_submitted']))
	{
		$champs = getChampsAdherents();
		$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE `email` = '".$_POST['email']."'";
		include('opendb.php');
		$res = mysql_query($query);
		$array_adh = null;
		if (!$res)
			echo mysql_error();
		else
		{
			foreach (mysql_fetch_array($res) as $key => $value)
				$array_adh[$key] = $value;
		}
		$values = "";
		foreach ($champs as $champs)
 			if ($champs['admin'] == 1)
			{
				if ($champs['type'] == "select")
					$values .= ", id_".$champs['nom']."=".(isset($_POST['id_'.$champs['nom']]) ? $_POST['id_'.$champs['nom']] : 0)."";
				else if ($champs['type'] == "file")
				{
					if (isset($_POST[$champs['nom']]['name']))
						$values .= ", ".$champs['nom']."=0";
					else
					{
						$values .= ", ".$champs['nom']."=1";
						saveImage($_POST['email'],$champs['nom']);
					}
				}
				else if ($champs['type'] == "tinyint")
				{
					if ($champs['nom'] == "active")
					{	
						$values .= ", ".$champs['nom']."=".(isset($_POST[$champs['nom']]) && $_POST[$champs['nom']] == 1 ? 1 : 0)."";
						if ($array_adh["active"] == 1 && $_POST[$champs['nom']] == 0 && getParam('account_out.notif') == "now")
						{
							$to      = $_POST['email'];
							$subject = "D�sactivation du compte Fliker";
							$message = "Bonjour,\r\n  Votre compte Fliker a �t� d�sactiv�, pour plus d'informations merci de contacter les administrateurs. \r\n  Remarque 1 : pour pouvoir exercer votre droit de consultation et de modification de vos donn�es personnelles, vous devez d'abord activer votre compte.\r\n  Remarque 2 : Notre serveur d'adh�sion en ligne (".getParam('url_site.conf').") est diff�rent de notre site web principal (wiki) ... Ne vous trompez donc pas d'URL quand vous essaierez de vous connecter !\r\n  Excellente saison sportive,\r\n--\r\nles administrateurs.";
							$headers = 'From: '.getParam('admin_email.conf') . "\r\n" .
							'Reply-To: '.getParam('contact_email.conf') . "\r\n" .
							'Return-Path: '.getParam('admin_email.conf') . "\r\n" .
							'X-Mailer: PHP/' . phpversion();
							if (getParam('allow_mail.conf') == true)
								mail($to, $subject, $message, $headers);
						}
					}
					else
						$values .= ", ".$champs['nom']."=".(isset($_POST[$champs['nom']]) ? 1 : 0)."";
				}
				else
				{
					$values .= ", ".$champs['nom']."=\"".(isset($_POST[$champs['nom']]) ? $_POST[$champs['nom']] : 0)."\"";
					//if ($champs['nom'] == 'email' && getParam('account_out.notif') == "now")
				}
			}
		$values[0] = " ";
		$query = "UPDATE {$GLOBALS['prefix_db']}adherent SET ".$values." WHERE email=\"".$_POST['email']."\" ";
		include("opendb.php");
		$results = mysql_query($query);
		if (!$results)
		{
			print "<FONT COLOR='#FF0000'><b>Un probl�me est survenu lors de la mise � jour, les modifications n'ont pas �t� effectu�es.</b></font>";
			echo mysql_error();
		}
		else
			print "<FONT COLOR='#16B84E'><b>Les modifications ont �t� effectu�es avec succ�s.</b></font>";
	}
	else if (isset($_POST['new_email']))
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
					print "<FONT COLOR='#16B84E'><b>Le changement d'adresse a �t� effectu� avec succ�s.</b></font>";
				else
					print "<FONT COLOR='#FF0000'><b>Un probl�me est survenu lors de la mise � jour, le changement n'a pas �t� effectu�.</b></font>";
				print "<br /><br/>";
			}
		}
		else
			print "<FONT COLOR='#FF0000'><b>L'adresse email existe d�j� dans notre base de donn�es.</b></font><br /><br />";
		include("closedb.php");
	}
	else if (isset($_POST['etat_compte']))
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
			print "<FONT COLOR='#FF0000'><b>Erreur lors de la mise � jour du compte.</b></font><br /><br />";
			echo mysql_error();
		}
		else
			print "<FONT COLOR='#16B84E'><b>Mise � jour du compte effectu�e avec succ�s.</b></font><br/>";
	}
	else if (isset($_POST['current_email']))
	{
		$query = "UPDATE {$GLOBALS['prefix_db']}adherent SET id_statut=".$_POST['choix_statut_type']." WHERE email='".$_POST['current_email']."' ";
		include("opendb.php");
		$res = mysql_query($query);
		if (!$res)
		{
			echo mysql_error();
			print "<FONT COLOR='#FF0000'><b>Erreur lors de la mise � jour du compte.</b></font><br /><br />";
		}
		else
			print "<FONT COLOR='#16B84E'><b>Mise � jour du compte effectu�e avec succ�s.</b></font>";
	}

	print '<br />';
	print 	"<FORM action=\"index.php?page=12\" method=\"POST\">
			Adresse email du compte � modifier :<input type='text' name='modif_compte'></input>
			<input type=\"submit\" />
			</form>";
}
?>
