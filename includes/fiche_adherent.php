<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
if (!isset($_GET['adh']) or $_GET['adh']==$_SESSION['uid'])
{
	$id_adh =$_SESSION['uid'];
	$edit=true;
}
else if($_SESSION['privilege']==1)
{
	$admin=true;
	$id_adh=$_GET['adh'];
}
else
{
	if(count(getMyAssos($_SESSION['uid'])) > 0)
	{
		$resp_asso=true;
		$assos_resp=getMyAssos($_SESSION['uid']);
	}
	$tab = getMyAdherents($_SESSION['uid']);
	if (isset($tab[$_GET['adh']])) $id_adh=$_GET['adh'];
	else
	{
		print 'Vous n\'avez pas acc�s � cette page';
		die();
	}

}
$adh = getAdherent($id_adh);
print '<ul id="submenu"><li><a class="selected" href="index.php?page=1&adh='.$id_adh.'">Fiche Adh�rent</a></li><li><a href="index.php?page=7&adh='.$id_adh.'">Adh�sions</a></li></ul>';

$dest_dossier = "../photos";
if (isset($_POST['action']) && $_POST['action'] == 'modification' && $edit) {
	$tab = getChampsAdherents();
	print '<FORM id="f_adherent_modif" action="index.php?page=1" enctype="multipart/form-data" method="POST">';
	print '<table border=0>';
	foreach($tab as $row)
	{
		if($row['user_editable'] == 1)
		{
			$format =$row['format'];
			if ($row['required'] == 1)
				$format ="class=\"{$format}_req\"";
			else
				$format="class=\"$format\"";
			if($row['format'] === "categorie"){
				if($adh[$row['nom']]==='M'){
					$homme='checked';
					$femme='';
				} else {
					$homme='';
					$femme='checked';

				}
				print '<tr ><td class="label"><LABEL for ='.$row['nom'].' >'.$row['description'].'</LABEL> : </td>
					<td>
					<INPUT type=radio name='.$row['nom'].' '.$format.' value="M" '.$homme.' >Masculin
					<INPUT type=radio name='.$row['nom'].' '.$format.' value="F" '.$femme.' >F�minin
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
	print '<input type=\'hidden\' name=\'action\' value=\'submitted\' />';
	print "<INPUT type=\"hidden\" name=\"id_adh\" value =\"$id_adh\">";
	print "<INPUT type=\"hidden\" name=\"email\" value =\"{$adh['email']}\">";
	print '<tr><td colspan="2"><INPUT type=\'submit\' value=\'Send\'></td></tr>';

	print '</table>';
	print '</FORM>';
}
else if (isset($_POST['action']) && $_POST['action'] == 'change_mdp')
{
	print '<h2>Changement de mot de passe</h2><br/>';
	print '<FORM action="index.php?page=1" method="POST">';
	print '<input type=\'hidden\' name=\'action\' value=\'change_mdp_submitted\' />';
	print '<input type=\'hidden\' name="recup_mdp" value="'.$_SESSION['user'].'" />';
	print 'Cliquez sur le bouton ci-dessous pour recevoir un email � l\'adresse <b>'.$_SESSION['user'].'</b> permettant de changer votre mot de passe.<br /><br /><input type="submit" name="email_recup" value="Envoyer l\'email"></input>';
	print '</form>';
}
else if (isset($_POST['action']) && $_POST['action'] == 'change_email')
{
	print '<h2>Changement de l\'adresse email</h2><br/>';
	print '<p>Pour pouvoir changer d\'adresse email il vous faut pouvoir acc�der � l\'ancienne ainsi qu\'a la nouvelle. Si vous n\'avez pas acc�s � l\'ancienne adresse, merci de contacter un administrateur.</p>';
	print '<FORM action="index.php?page=1" method="POST">';
	print '<input type=\'hidden\' name=\'action\' value=\'change_email_submitted\' />';
	print '<input type=\'hidden\' name="recup_mdp" value="'.$_SESSION['user'].'" />';
	print 'Cliquez sur le bouton ci-dessous pour recevoir un email � l\'adresse <b>'.$_SESSION['user'].'</b> permettant d\'indiquer la nouvelle adresse.<br /><br /><input type="submit" name="email_recup" value="Envoyer l\'email"></input>';
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
		print "L'adresse email <b>".$_POST['recup_mdp']."</b> n'existe pas dans notre base de donn�es, merci de s'assurer de sa validit�.<br/><br/>";
		print '<form action="index.php?page=1" method="POST">';
		if (isset($_POST['change_email_submitted']))
			print '<input type=\'hidden\' name=\'action\' value=\'change_email_submitted\' />';
		else
			print '<input type=\'hidden\' name=\'action\' value=\'change_mdp_submitted\' />';
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
		if (isset($_POST['change_email_submitted']))
		{
			$subject = "Changement de l\adresse email Fliker";
			$message = "Bonjour,\r\r  Vous, ou quelqu'un utilisant votre adresse email, �tes inscrit sur notre service d'adh�sion en ligne.\r\r  Suite � une demande de modification de l\email li� � ce compte veuillez cliquer sur le lien suivant:\r".getParam('url_site')."validate_email.php?$activationKey\r\r  Si c'est une erreur ou une tentative d'usurpation, ignorez tout simplement cet email et vos coordonn�es seront automatiquement purg�es de notre serveur dans quelques temps.\r\r  \r\r  Remarque: Notre serveur d'adh�sion en ligne (".getParam('url_site').") est diff�rent de notre site web principal ... Ne vous trompez donc pas d'URL quand vous essaierez de vous connecter !\r\r  Excellente saison sportive,\r\r--\rles administrateurs.";
		}
		else
		{
			$subject = "Changement de mot de passe Fliker";
			$message = "Bonjour,\r\r  Vous, ou quelqu'un utilisant votre adresse email, �tes inscrit sur notre service d'adh�sion en ligne.\r\r  Suite � une demande de modification du mot de passe li� � cette adresse email veuillez cliquer sur le lien suivant:\r".getParam('url_site')."validate.php?$activationKey\r\r  Si c'est une erreur ou une tentative d'usurpation, ignorez tout simplement cet email et vos coordonn�es seront automatiquement purg�es de notre serveur dans quelques temps.\r\r  \r\r  Remarque: Notre serveur d'adh�sion en ligne (".getParam('url_site').") est diff�rent de notre site web principal ... Ne vous trompez donc pas d'URL quand vous essaierez de vous connecter !\r\r  Excellente saison sportive,\r\r--\rles administrateurs.";
		}
		$headers = 'From: '.getParam('admin_email') . "\r\n" .
				'Reply-To: '.getParam('contact_email') . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
		$return = mail($to, $subject, $message, $headers);
		if ($return == TRUE)
			print 'Un email vient d\'�tre envoy� � l\'adresse '.$_POST["email_recup"].', veuiller v�rifier votre bo�te mail.';
		else
			print "Une erreur est survenu lors de l'envoi du mail, veuiller v�rifier votre adresse mail ainsi que votre connexion internet puis recommencer. <br/>Si le probl�me persiste merci de contacter les <a href=\"".getParam("url_resiliation")."\">administrateurs</a>";
	}
}
else
{
	if (isset($_POST['action']) && $_POST['action'] == 'submitted' && $edit)
	{
		modifAdherent($_POST);
		$adh = getAdherent($id_adh);
	}
	if(!(strcmp($_SESSION['user'],"") == 0)){

		$tab = getChampsAdherents();
		print '<div id="fiche">';
		print "<h2>Fiche de {$adh['prenom']} {$adh['nom']}</h2>";
		print "<div class=\"tip\"><center>".getParam('text_adherent')."</center></div>";
		print '<br />';
		if(isset($edit)) print '<FORM action="index.php?page=1" method="POST">
		<input type=\'hidden\' name=\'action\' value=\'modification\' />
		<INPUT type=\'submit\' value=\'Modifier\'>
		</FORM>
		';
		print '<FORM action="index.php?page=1" method="POST">
		<input type=\'hidden\' name=\'action\' value=\'change_mdp\' />
		<INPUT type=\'submit\' value=\'Changer de mot de passe\'>
		</FORM>
		';
		print '<FORM action="index.php?page=1" method="POST">
		<input type=\'hidden\' name=\'action\' value=\'change_email\' />
		<INPUT type=\'submit\' value="Changer d\'email">
		</FORM>
		';
		print '<TABLE BORDER="0">';
		foreach($tab as $row){
			if($row['user_viewable']==1){
				print '<TR>';
				if($row['type']==="varchar")
					print '<TD>'.$row['description'].'</TD><TD>'.$adh[$row['nom']].'</TD>';

				if($row['type']==="date")
					print '<TD>'.$row['description'].'</TD><TD>'.$adh[$row['nom']].'</TD>';

				if($row['type']==="tinyint"){
					if ($adh[$row['nom']]==1)
						print '<TD>'.$row['description'].'</TD><TD>Oui</TD>';
					else
						print '<TD>'.$row['description'].'</TD><TD>Non</TD>';
				}
				if($row['type']==='file'){
					$_SESSION['auth_thumb']='true';
					$photo="includes/thumb.php?folder=".$row['nom']."&file=".$adh['email'].".jpg";
					print '<TD>'.$row['description'].'</TD><TD><a href="'.$row['nom'].'/'.$adh['email'].'.jpg"><img src="'.$photo.'" height="150"></a></TD>';
				}
				if($row['type']==="select"){
					$tab=getSelect($row['nom']);
					print '<TD>'.$row['description'].'</TD><TD>'.$tab[$adh[$row['nom']]].'</TD>';
				}

			}
			print '</TR>';
		}
		print '</TABLE>';
		print '</div>';
	}
	else
	{
		print "<p>Vous n'�tes pas connect�</p>";
	}
}



?>