<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
switch($_POST['action']){
	case "sendmail_confirm":
		$headers = 'From: '.$_SESSION['user'] . "\r\n" .

		'Reply-To: '.$_SESSION['user'] . "\r\n" .

		'X-Mailer: PHP/' . phpversion();
		$message = $_POST['message'];
		$to = $_POST['to'];
		$subject = $_POST['subject'];
		include("opendb.php");
		$res = mysql_query("SELECT * FROM {$GLOBALS['prefix_db']}config WHERE id = 'action_patienter' ");
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
		$res = mysql_query("SELECT * FROM {$GLOBALS['prefix_db']}config WHERE id = 'action_continuer' ");
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
		foreach($_POST['adh'] as $id)
		{
			$adhs[$id] = getAdherent($id);
		}
		print "<div>Destinaires:";
		foreach($adhs as $adh)
		{
			print "<dt>".$adh['nom']." ".$adh['prenom']."</dt>";
			$to .= $adh['email'].",";
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