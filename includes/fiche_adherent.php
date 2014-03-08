<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');

$self = false;
$admin = false;

if (!isset($_GET['adh']) or $_GET['adh']==$_SESSION['uid'])
{
	$self = true;
	$id_adh = $_SESSION['uid'];
}
else if($_SESSION['privilege']==1)
{
	$admin = true;
	$id_adh = $_GET['adh'];
}
else
{
	$tab = getMyAdherents($_SESSION['uid']);
	if (isset($tab[$_GET['adh']]))
	  $id_adh=$_GET['adh'];
	else
	{
		print 'Vous n\'avez pas accès à cette page';
		die();
	}
}
$adh = getAdherent($id_adh);
$dest_dossier = "../photos";
if (isset($_POST['action']) && $_POST['action'] == 'modification' && $self)
{
	$tab = getChampsAdherents();
	print '<FORM id="f_adherent_modif" action="index.php?page=1" enctype="multipart/form-data" method="POST">';
	print '<table border=0>';
	$current_adh = getAdherent($_SESSION['uid']);
	foreach($tab as $row)
	{
		if($row['user_editable'] == 1)
		{
			$format =$row['format'];
			if ($row['required'] == 1)
			{
				if ($row['nom'] == "photo" || $row['nom'] == "certmed")
				{
					if(($row['nom'] == 'photo' && $current_adh['photo'] == 1) || (($row['nom'] == 'certmed' && $current_adh['certmed'] == 1)))
						$format="class=\"$format\"";
					else
						$format ="class=\"{$format}_req\"";
				}
				else
					$format ="class=\"{$format}_req\"";
			}
			else
				$format="class=\"$format\"";
			if($row['format'] === "categorie")
			{
				if($adh[$row['nom']]==='M')
				{
					$homme='checked';
					$femme='';
				}
				else
				{
					$homme='';
					$femme='checked';
				}
				print '<tr ><td class="label"><LABEL for ='.$row['nom'].' >'.$row['description'].'</LABEL> : </td>
					<td>
					<INPUT type=radio name='.$row['nom'].' '.$format.' value="M" '.$homme.' >Masculin
					<INPUT type=radio name='.$row['nom'].' '.$format.' value="F" '.$femme.' >Féminin
					</td>
					</tr>
					</div>';
			}
			else if($row['type']==='varchar')
				print '<tr><td class="label"><LABEL for ='.$row['nom'].' >'.$row['description'].'</LABEL> : </td><td><INPUT type=text name="'.$row['nom'].'" id="'.$row['nom'].'" '.$format.' value="'.$adh[$row['nom']].'"></td></tr>';
			else if($row['type']==='date')
				print '<tr><td class="label"><LABEL for ='.$row['nom'].' >'.$row['description'].'</LABEL> : </td><td><INPUT type=text name="'.$row['nom'].'" id="datepicker" '.$format.'  value="'.$adh[$row['nom']].'"></td></tr>';
			else if($row['type']==='tinyint')
				print '<tr><td class="label"><LABEL for ='.$row['nom'].' >'.$row['description'].'</LABEL> : </td><td><INPUT type=checkbox name='.$row['nom'].' '.$format.'  '.($adh[$row['nom']]==1 ? "checked" : "").'></td></tr>';
			else if($row['type']==='file')
				print '<tr><td class="label"><LABEL for ='.$row['nom'].' >'.$row['description'].'</LABEL> : </td><td><INPUT type=file name='.$row['nom'].' '.$format.'  ></td></tr>';
			else if($row['type']==='select')
			{
				$values = getSelect($row['nom']);
				print '<tr><td class="label"><LABEL for ='.$row['nom'].' >'.$row['description'].'</LABEL> : </td><td><SELECT name="id_'.$row['nom'].'" id="id_'.$row['nom'].'" '.$format.'>';
				foreach($values as $key => $value)
				{
					print '<OPTION value="'.$key.'">'.$value.'</OPTION>';
				}
				print '</SELECT></td></tr>';
			}
		}
	}
	print '<input type="hidden" name="action" value="submitted" />';
	print "<INPUT type='hidden' name='id_adh' value=\"$id_adh\">";
	print "<INPUT type='hidden' name='email'  value=\"{$adh['email']}\">";
	print '<tr><td colspan="2"><INPUT type="submit" value="Send"></td></tr>';
	print '</table>';
	print '</FORM>';
}
else if (isset($_POST['action']) && $_POST['action'] == 'change_mdp')
{
	print '<h2>Changement de mot de passe</h2><br/>';
	print '<FORM action="index.php?page=1" method="POST">';
	print '<input type="hidden" name="action" value="change_mdp_submitted" />';
	print '<input type="hidden" name="recup_mdp" value="'.$_SESSION['user'].'" />';
	print 'Cliquez sur le bouton ci-dessous pour recevoir un email à l\'adresse <b>'.$_SESSION['user'].'</b> permettant de changer votre mot de passe.<br /><br /><input type="submit" value="Envoyer l\'email"></input>';
	print '</form>';
}
else if (isset($_POST['action']) && $_POST['action'] == 'change_email')
{
	print '<h2>Changement de l\'adresse email</h2><br/>';
	print '<p>Pour pouvoir changer d\'adresse email, il vous faut pouvoir accéder à l\'ancienne ainsi qu\'à la nouvelle. Si vous n\'avez pas accès à l\'ancienne adresse, merci de contacter un administrateur.</p>';
	print '<FORM action="index.php?page=1" method="POST">';
	print '<input type="hidden" name="action" value="change_email_submitted" />';
	print '<input type="hidden" name="recup_mdp" value="'.$_SESSION['user'].'" />';
	print 'Cliquez sur le bouton ci-dessous pour recevoir un email à l\'adresse <b>'.$_SESSION['user'].'</b> permettant d\'indiquer la nouvelle adresse.<br /><br /><input type="submit" value="Envoyer l\'email"></input>';
	print '</form>';
}
else if (isset($_POST['action']) && ($_POST['action'] == 'change_mdp_submitted' || $_POST['action'] == 'change_email_submitted'))
{
	print '<h2>Changement identifiant de connexion</h2><br/>';
	$query= "SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE email='".$_POST['recup_mdp']."' ";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	if (mysql_fetch_array($results) == false)
	{
		include("closedb.php");
		print "L'adresse email <b>".$_POST['recup_mdp']."</b> n'existe pas dans notre base de données, merci de s'assurer de sa validité.<br/><br/>";
		print '<form action="index.php?page=1" method="POST">';
		if ($_POST['action'] == 'change_email_submitted')
			print '<input type="hidden" name="action" value="change_email_submitted" />';
		else
			print '<input type="hidden" name="action" value="change_mdp_submitted" />';
		// print 'Veuillez indiquer votre adresse email : <input type="text" name="recup_mdp" /></input><br/><input type="submit" value="Envoyer"></input>'; // trou de sécurité non justifié !
		print '</form>';
	}
	else
	{
	        // include("closedb.php");
		$activationKey = mt_rand() . mt_rand() . mt_rand() . mt_rand() . mt_rand();
		// include("opendb.php");
		$query = "UPDATE {$GLOBALS['prefix_db']}adherent SET activationkey='".$activationKey."' WHERE email='".$_POST['recup_mdp']."' ";
		$results = mysql_query($query);
		if (!$results)
			echo mysql_error();
		include("closedb.php");
		$to      = $_POST['recup_mdp'];
		if ($_POST['action'] == 'change_email_submitted')
		{
			$subject = "[".getParam('text_top.txt')."] Changement de l\'adresse email";
			$message = "Bonjour,\r\n  Vous, ou quelqu'un utilisant votre adresse email, êtes inscrit sur notre service d'adhésion en ligne.\r\n  Suite à une demande de modification de l\'email lié à ce compte, veuillez cliquer sur le lien suivant :\r\n".getParam('url_site.conf')."validate_email.php?$activationKey\r\n  Si c'est une erreur ou une tentative d'usurpation, ignorez tout simplement cet email et vos coordonnées ne seront pas modifiées.\r\n  \r\n  Remarque : Notre serveur d'adhésion en ligne (".getParam('url_site.conf').") est différent de notre site web principal (wiki) ... Ne vous trompez donc pas d'URL quand vous essaierez de vous connecter !\r\n  Excellente saison sportive,\r\n--\r\nles administrateurs.";
		}
		else
		{
			$subject = "[".getParam('text_top.txt')."] Changement de mot de passe";
			$message = "Bonjour,\r\n  Vous, ou quelqu'un utilisant votre adresse email, êtes inscrit sur notre service d'adhésion en ligne.\r\n  Suite à une demande de modification du mot de passe lié à cette adresse email, veuillez cliquer sur le lien suivant :\r\n".getParam('url_site.conf')."validate.php?$activationKey\r\n  Si c'est une erreur ou une tentative d'usurpation, ignorez tout simplement cet email et vos coordonnées ne seront pas modifiées.\r\n  \r\n  Remarque : Notre serveur d'adhésion en ligne (".getParam('url_site.conf').") est différent de notre site web principal (wiki) ... Ne vous trompez donc pas d'URL quand vous essaierez de vous connecter !\r\n  Excellente saison sportive,\r\n--\r\nles administrateurs.";
		}
		$headers = 'From: '.getParam('admin_email.conf') . "\r\n" .
		'Reply-To: '.getParam('contact_email.conf') . "\r\n" .
		'Return-Path: '.getParam('admin_email.conf') . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
//		$return = FALSE;
		if (getParam('allow_mail.conf') == true)
			mail($to, $subject, $message, $headers);
//		if ($return == TRUE)
			print 'Un email vient d\'être envoyé à l\'adresse '.$to.', veuillez vérifier votre boîte mail.';
//		else
//			print "Une erreur est survenue lors de l'envoi du mail, veuillez vérifier votre adresse email ainsi que votre connexion internet puis recommencer. <br/>Si le problème persiste merci de contacter les <a href=\"".getParam("url_resiliation.conf")."\">administrateurs</a>";
	}
}
else
{
	if (isset($_POST['action']) && $_POST['action'] == 'submitted' && $self)
	{
		modifAdherent($_POST);
		$adh = getAdherent($id_adh);
	}
	if(!(strcmp($_SESSION['user'],"") == 0))
        {
		$tab = getChampsAdherents();
		print '<div id="fiche">';
		print "<h2>Fiche de {$adh['prenom']} {$adh['nom']}</h2>";
		print '<br />';
		if($self) print '<FORM action="index.php?page=1" method="POST">
		<input type="hidden" name="action" value="modification" />
		<INPUT type="submit" value="Modifier"/>
		</FORM>
		';
		print '<FORM action="index.php?page=1" method="POST">
		<input type="hidden" name="action" value="change_mdp" />
		<INPUT type="submit" value="Changer de mot de passe"/>
		</FORM>
		';
		print '<FORM action="index.php?page=1" method="POST">
		<input type="hidden" name="action" value="change_email" />
		<INPUT type="submit" value="Changer d\'email"/>
		</FORM>
		';
		print "<br/>";
		print '<TABLE>';
		foreach($tab as $row)
		{
			if($row['user_viewable'] == 1)
			{
				print '<TR>';
				if($row['type']==="varchar")
					print '<TD>'.$row['description'].'</TD><TD>'.$adh[$row['nom']].'</TD>';
				if($row['type']==="date")
					print '<TD>'.$row['description'].'</TD><TD>'.$adh[$row['nom']].'</TD>';
				if($row['type']==="tinyint")
				{
					if ($adh[$row['nom']]==1)
						print '<TD>'.$row['description'].'</TD><TD>Oui</TD>';
					else
						print '<TD>'.$row['description'].'</TD><TD>Non</TD>';
				}
				if($row['type']==='file')
				{
					$_SESSION['auth_thumb']='true';
					$photo="includes/thumb.php?folder=".$row['nom']."&file=".$adh['email'].".jpg";
					print '<TD>'.$row['description'].'</TD><TD><a href="'.$row['nom'].'/'.$adh['email'].'.jpg"><img src="'.$photo.'" height="150"></a></TD>';
				}
				if($row['type']==="select")
				{
					$tab=getSelect($row['nom']);
					print '<TD>'.$row['description'].'</TD><TD>'.$tab[$adh[$row['nom']]].'</TD>';
				}
				print '</TR>';
			}
		}
		print '</TABLE>';
		print '</div>';
	}
	else
		print "<p>Vous n'êtes pas connecté UHUH</p>";
}
?>
