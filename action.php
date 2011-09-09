<?php

switch($_POST['action']){
	case "sendmail_confirm":
	$headers = 'From: '.getParam('admin_email') . "\r\n" .

	'Reply-To: '.getParam('contact_email') . "\r\n" .

	'X-Mailer: PHP/' . phpversion();
	$message=$_POST['message'];
	$to=$_POST['to'];
	$subject=$_POST['subject'];
	print "Envoi de ".count($to)." emails (merci de patienter) : ";
	$i=0;
	foreach($to as $email){
		usleep(100000);
		$i++;
		print "$i-";
		flush();
		mail($email, $subject, $message, $headers);
	}
	print "OK (Vous pouvez continuer à naviguer)";
	
	break;
	case "sendmail":
	print "<h2>Envoi d'email</h2>";
	
	foreach($_POST['adh'] as $id){
		$adhs[$id]=getAdherent($id);

	}
	print "<div>Destinaires:";
	foreach($adhs as $adh){
		print "<dt>".$adh['nom']." ".$adh['prenom']."</dt>";
		$to.=$adh['email'].",";
	}

	print "</div>";
	print '<table><FORM action="index.php?page=10" method=POST>
			<tr><td><INPUT type="text" name="subject" size="80" value="Sujet" /></td></tr>
			<tr><td><TEXTAREA cols="80" rows="25" name="message">Entrez ici votre message</TEXTAREA> </td></tr>
			';
	foreach ($adhs as $adh) print "<input type=\"hidden\" name=\"to[]\" value=\"{$adh['email']}\"  />";
	print '<input type="hidden" name="action" value="sendmail_confirm" >
			<tr><td><input type="submit" ></td></tr></FORM>';
	



	
	break;
}


?>