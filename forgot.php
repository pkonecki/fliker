<?php
define('_VALID_INCLUDE', TRUE);
include("./includes/paths.php");
include_once("General.php");
include_once("EspaceMembre.class.php");

$header = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
 <head>
  <title>::Fliker::Connexion</title>
  <link rel="stylesheet" type="text/css" href="./includes/style.css" />
 </head>';

print "$header";
print '<body>';
$EspaceMembre = new EspaceMembre;
$EspaceMembre->showMenu();
if (isset($_GET['forgot']) && $_GET['forgot'] == "email")
	print '<h2>Changement de l\'adresse email</h2><br/>';
else
	print '<h2>Mot de passe oubli�</h2><br/>';
if (isset($_POST['email_recu']))
{
	$query= "SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE email='".$_POST['recup_mdp']."' ";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	if (mysql_fetch_array($results) == false)
	{
		include("closedb.php");
		print "L'adresse email <b>".$_POST['recup_mdp']."</b> n'existe pas dans notre base de donn�es, merci de s'assurer de sa validit�.<br/><br/>";
		print '<form action="forgot.php" method="POST">';
		print 'Veuillez indiquer votre adresse email : <input type="text" name="recup_mdp" /></input><br/><input type="submit" name="email_recu" value="Envoyer"></input>';
		print '</form>';
	}
	else
	{
		include("closedb.php");
		$activationKey = mt_rand() . mt_rand() . mt_rand() . mt_rand() . mt_rand();
		include("opendb.php");
		$query = "UPDATE {$GLOBALS['prefix_db']}adherent SET activationkey='$activationKey' WHERE email='".$_POST['recup_mdp']."' ";
		$results = mysql_query($query);
		if (!$results)
			echo mysql_error();
		include("closedb.php");
		$to      = $_POST['recup_mdp'];
		$subject = "Changement de mot de passe Fliker";
		$message = "Bonjour,\r\n  Vous, ou quelqu'un utilisant votre adresse email, �tes inscrit sur notre service d'adh�sion en ligne.\r\n  Suite � une demande de modification du mot de passe li� � cette adresse email, veuillez cliquer sur le lien suivant :\r\n".getParam('url_site.conf')."validate.php?$activationKey\r\n  Si c'est une erreur ou une tentative d'usurpation, ignorez tout simplement cet email et vos coordonn�es ne seront pas modifi�es.\r\n  \r\n  Remarque : Notre serveur d'adh�sion en ligne (".getParam('url_site.conf').") est diff�rent de notre site web principal (wiki) ... Ne vous trompez donc pas d'URL quand vous essaierez de vous connecter !\r\n  Excellente saison sportive,\r\n--\r\nles administrateurs.";
		$headers = 'From: '.getParam('admin_email.conf') . "\r\n" .
		'Reply-To: '.getParam('contact_email.conf') . "\r\n" .
		'Return-Path: '.getParam('admin_email.conf') . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
//		$return = FALSE;
		if (getParam('allow_mail.conf') == true)
			mail($to, $subject, $message, $headers);
//		if ($return == TRUE)
			print 'Un email vient d\'�tre envoy� � l\'adresse '.$to.', veuillez v�rifier votre bo�te mail.';
//		else
//			print "Une erreur est survenue lors de l'envoi du mail, veuillez v�rifier votre adresse email ainsi que votre connexion internet puis recommencer. <br/>Si le probl�me persiste merci de contacter les <a href=\"".getParam("url_resiliation.conf")."\">administrateurs</a>";
	}
}
else if (isset($_POST['action']) && $_POST['action'] == 'change_email_submitted')
{
	$query= "SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE email='".$_POST['recup_mdp']."' ";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	if (mysql_fetch_array($results) == false)
	{
		include("closedb.php");
		print "L'adresse email <b>".$_POST['recup_mdp']."</b> n'existe pas dans notre base de donn�es, merci de s'assurer de sa validit�.<br/><br/>";
		print '<form action="forgot.php?forgot=email" method="POST">';
		print '<input type=\'hidden\' name=\'action\' value=\'change_email_submitted\' />';
		print 'Veuillez indiquer votre adresse email : <input type="text" name="recup_mdp" /></input><br/><input type="submit" name="email_recup" value="Envoyer"></input>';
		print '</form>';
	}
	else
	{
		include("closedb.php");
		$activationKey = mt_rand() . mt_rand() . mt_rand() . mt_rand() . mt_rand();
		include("opendb.php");
		$query = "UPDATE {$GLOBALS['prefix_db']}adherent SET activationkey='".$activationKey."' WHERE email='".$_POST['recup_mdp']."' ";
		$results = mysql_query($query);
		if (!$results)
			echo mysql_error();
		include("closedb.php");
		$to      = $_POST['recup_mdp'];
		$subject = "Changement de l\'adresse email Fliker";
		$message = "Bonjour,\r\n  Vous, ou quelqu'un utilisant votre adresse email, �tes inscrit sur notre service d'adh�sion en ligne.\r\n  Suite � une demande de modification de l\'email li� � ce compte, veuillez cliquer sur le lien suivant :\r\n".getParam('url_site.conf')."validate_email.php?$activationKey\r\n  Si c'est une erreur ou une tentative d'usurpation, ignorez tout simplement cet email et vos coordonn�es ne seront pas modifi�es.\r\n  \r\n  Remarque : Notre serveur d'adh�sion en ligne (".getParam('url_site.conf').") est diff�rent de notre site web principal (wiki) ... Ne vous trompez donc pas d'URL quand vous essaierez de vous connecter !\r\n  Excellente saison sportive,\r\n--\r\nles administrateurs.";
		$headers = 'From: '.getParam('admin_email.conf') . "\r\n" .
		'Reply-To: '.getParam('contact_email.conf') . "\r\n" .
		'Return-Path: '.getParam('admin_email.conf') . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
//		$return = FALSE;
		if (getParam('allow_mail.conf') == true)
			mail($to, $subject, $message, $headers);
//		if ($return == TRUE)
			print 'Un email vient d\'�tre envoy� � l\'adresse '.$to.', veuillez v�rifier votre bo�te mail.';
//		else
//			print "Une erreur est survenue lors de l'envoi du mail, veuillez v�rifier votre adresse email ainsi que votre connexion internet puis recommencer. <br/>Si le probl�me persiste merci de contacter les <a href=\"".getParam("url_resiliation.conf")."\">administrateurs</a>";
	}
}
else if (isset($_GET['forgot']) && $_GET['forgot'] == "email")
{
	print '<p>Pour pouvoir changer d\'adresse email, il vous faut pouvoir acc�der � l\'ancienne ainsi qu\'&agrave; la nouvelle. Si vous n\'avez plus acc�s � l\'ancienne adresse, merci de contacter un administrateur.</p>';
	print '<FORM action="forgot.php?forgot=email" method="POST">';
	print '<input type=\'hidden\' name=\'action\' value=\'change_email_submitted\' />';
	print 'Veuillez indiquer votre adresse email actuelle : <input type="text" name="recup_mdp" /></input><br/><input type="submit" name="email_recup" value="Envoyer"></input>';
	print '</form>';
}
else if (isset($_POST['action']) && $_POST['action'] == 'form')
{
	$cryptinstall="./includes/cryptographp.fct.php";
	include $cryptinstall;
	if (isset($_POST['code']) && chk_crypt($_POST['code']))
	{
		print '<form action="forgot.php" method="POST">';
		print 'Veuillez indiquer votre adresse email : <input type="text" name="recup_mdp" /></input><br/><input type="submit" name="email_recu" value="Envoyer"></input>';
		print '</form>';
	}
	else
	{
		echo "<center><a><font color='#FF0000'>=> Erreur, le code est incorrect</font></a></center>" ;
		print '<div align="center">
				<p>Pour des raisons de s�curit� merci de bien vouloir recopier le code suivant dans le champ de texte.</p>
				<form action="forgot.php?';
		echo SID;
		print '" method="post">
				<table cellpadding=1>
				<tr><td align="center">';
		dsp_crypt(0,1);
		print '</td></tr>
				<tr><td align="center">Recopier le code :<br><input type="text" name="code"></td></tr>
				<tr><td align="center"><input type="hidden" name="action" value="form" /><input type="submit" name="submit" value="Envoyer"></td></tr>
				</table>
				</form>
				</div>';
	}
}
else
{
	$cryptinstall="./includes/cryptographp.fct.php";
	include $cryptinstall; 
	print '<div align="center">
			<p>Pour des raisons de s�curit� merci de bien vouloir recopier le code suivant dans le champ de texte.</p>
			<form action="forgot.php?';
	echo SID;
	print '" method="post">
			<table cellpadding=1>
			<tr><td align="center">';
	dsp_crypt(0,1);
	print '</td></tr>
			<tr><td align="center">Recopier le code :<br><input type="text" name="code"></td></tr>
			<tr><td align="center"><input type="hidden" name="action" value="form" /><input type="submit" name="submit" value="Envoyer"></td></tr>
			</table>
			</form>
			</div>';
}
print "</body></html>";
?>
