<?php
define('_VALID_INCLUDE', TRUE);
include("./includes/paths.php");
include_once("General.php");

$header = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
 <head>
  <title>::Fliker::Connexion</title>
  <link rel="stylesheet" type="text/css" href="./includes/style.css" />
 </head>';

print "$header";
print '<body>';
print '<div id="top">';
print '<span id="title">';
print getParam('text_top');
print '</span>';
include("userdiv.php");
print '</div>';
if (isset($_GET['forgot']) && $_GET['forgot'] == "email")
	print '<h2>Changement de l\'adresse email</h2><br/>';
else
	print '<h2>Mot de passe oublié</h2><br/>';
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
		print "L'adresse email <b>".$_POST['recup_mdp']."</b> n'existe pas dans notre base de données, merci de s'assurer de sa validité.<br/><br/>";
		print '<form action="forgot.php" method="POST">';
		print 'Veuillez indiquer votre adresse mail : <input type="text" name="recup_mdp" /></input><br/><input type="submit" name="email_recu" value="Envoyer"></input>';
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
		$to      = $_POST['email_recu'];
		$subject = "Changement de mot de passe Fliker";
		$message = "Bonjour,\r\r  Vous, ou quelqu'un utilisant votre adresse email, êtes inscrit sur notre service d'adhésion en ligne.\r\r  Suite à une demande de modification du mot de passe lié à cette adresse email veuillez cliquer sur le lien suivant:\r".getParam('url_site')."validate.php?$activationKey\r\r  Si c'est une erreur ou une tentative d'usurpation, ignorez tout simplement cet email et vos coordonnées seront automatiquement purgées de notre serveur dans quelques temps.\r\r  \r\r  Remarque: Notre serveur d'adhésion en ligne (".getParam('url_site').") est différent de notre site web principal ... Ne vous trompez donc pas d'URL quand vous essaierez de vous connecter !\r\r  Excellente saison sportive,\r\r--\rles administrateurs.";
		$headers = 'From: '.getParam('admin_email') . "\r\n" .
				'Reply-To: '.getParam('contact_email') . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
		$return = 0;
		$return = mail($to, $subject, $message, $headers);
		if ($return == TRUE)
			print 'Un email vient d\'être envoyé à l\'adresse '.$_POST["email_recu"].', veuiller vérifier votre boîte mail.';
		else
			print "Une erreur est survenu lors de l'envoi du mail, veuiller vérifier votre adresse mail ainsi que votre connexion internet puis recommencer. <br/>Si le problème persiste merci de contacter les <a href=\"".getParam("url_resiliation")."\">administrateurs</a>";
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
		print "L'adresse email <b>".$_POST['recup_mdp']."</b> n'existe pas dans notre base de données, merci de s'assurer de sa validité.<br/><br/>";
		print '<form action="forgot.php?forgot=email" method="POST">';
		print '<input type=\'hidden\' name=\'action\' value=\'change_email_submitted\' />';
		print 'Veuillez indiquer votre adresse mail : <input type="text" name="recup_mdp" /></input><br/><input type="submit" name="email_recup" value="Envoyer"></input>';
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
		$subject = "Changement de l\adresse email Fliker";
		$message = "Bonjour,\r\r  Vous, ou quelqu'un utilisant votre adresse email, êtes inscrit sur notre service d'adhésion en ligne.\r\r  Suite à une demande de modification de l\email lié à ce compte veuillez cliquer sur le lien suivant:\r".getParam('url_site')."validate_email.php?$activationKey\r\r  Si c'est une erreur ou une tentative d'usurpation, ignorez tout simplement cet email et vos coordonnées seront automatiquement purgées de notre serveur dans quelques temps.\r\r  \r\r  Remarque: Notre serveur d'adhésion en ligne (".getParam('url_site').") est différent de notre site web principal ... Ne vous trompez donc pas d'URL quand vous essaierez de vous connecter !\r\r  Excellente saison sportive,\r\r--\rles administrateurs.";
		$headers = 	'From: '.getParam('admin_email') . "\r\n" .
					'Reply-To: '.getParam('contact_email') . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
		$return = mail($to, $subject, $message, $headers);
		if ($return == TRUE)
			print 'Un email vient d\'être envoyé à l\'adresse '.$_POST["email_recup"].', veuiller vérifier votre boîte mail.';
		else
			print "Une erreur est survenu lors de l'envoi du mail, veuiller vérifier votre adresse mail ainsi que votre connexion internet puis recommencer. <br/>Si le problème persiste merci de contacter les <a href=\"".getParam("url_resiliation")."\">administrateurs</a>";
	}
}
else if (isset($_GET['forgot']) && $_GET['forgot'] == "email")
{
	print '<p>Pour pouvoir changer d\'adresse email il vous faut pouvoir accéder à l\'ancienne ainsi qu\'a la nouvelle. Si vous n\'avez pas accès à l\'ancienne adresse, merci de contacter un administrateur.</p>';
	print '<FORM action="forgot.php?forgot=email" method="POST">';
	print '<input type=\'hidden\' name=\'action\' value=\'change_email_submitted\' />';
	print 'Veuillez indiquer votre adresse mail actuelle : <input type="text" name="recup_mdp" /></input><br/><input type="submit" name="email_recup" value="Envoyer"></input>';
	print '</form>';
}
else
{
	print '<form action="forgot.php" method="POST">';
	print 'Veuillez indiquer votre adresse mail : <input type="text" name="recup_mdp" /></input><br/><input type="submit" name="email_recup" value="Envoyer"></input>';
	print '</form>';
}

print "</body></html>";
// <a href=\"".getParam("url_resiliation")."\">administrateurs</a>
?>