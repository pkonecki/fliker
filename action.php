<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
switch($_POST['action'])
{
	case "sendmail_confirm":
		$headers = 'From: '.$_SESSION['user'] . "\r\n" .

		'Reply-To: '.$_SESSION['user'] . "\r\n" .

		'X-Mailer: PHP/' . phpversion();
		$message = $_POST['message'];
		$to = $_POST['to'];
		$subject = $_POST['subject'];
		include("opendb.php");
		$res = mysql_query("SELECT * FROM {$GLOBALS['prefix_db']}config WHERE id = 'action_patienter.txt' ");
		$stock_pat = mysql_fetch_array($res);
		print $stock_pat['valeur'];
		include("closedb.php");
		$i = 0;
		foreach($to as $email)
		{
			usleep(100000);
			$i++;
			print "$i-";
			flush();
			mail($email, $subject, $message, $headers);
		}
		include("opendb.php");
		$res = mysql_query("SELECT * FROM {$GLOBALS['prefix_db']}config WHERE id = 'action_continuer.txt' ");
		$stock_pat = mysql_fetch_array($res);
		print $stock_pat['valeur'];
		include("closedb.php");
		
		break;
	case "sendmail":
		print "<h2>Envoi d'email</h2>";
		if (isset($_POST['to']))
			$to = $_POST['to'];
		else
			$to = "";
		print $_POST['to'];
		foreach($_POST['adh'] as $id)
			$adhs[$id] = getAdherent($id);
		print "<div>Destinaires:";
		$check_exist = array();
		foreach($adhs as $adh)
		{
			print "<dt>".$adh['nom']." ".$adh['prenom']."</dt>";
			if (!isset($check_exist[$adh['email']]))
			{
				$to .= $adh['email'].",";
				$check_exist[$adh['email']] = 1;
			}
		}
		print "</div>";
		print '<table><FORM action="index.php?page=10" method=POST>
				<tr><td><INPUT type="text" name="subject" size="80" value="Sujet" /></td></tr>
				<tr><td><TEXTAREA cols="80" rows="25" name="message">Entrez ici votre message</TEXTAREA> </td></tr>
				';
		foreach ($adhs as $adh)
			print "<input type=\"hidden\" name=\"to[]\" value=\"{$adh['email']}\"  />";
		print '<input type="hidden" name="action" value="sendmail_confirm" >
				<tr><td><input type="submit" ></td></tr></FORM>';
		break;
}
?>