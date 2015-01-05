<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
require_once('./includes/phpmailer/class.phpmailer.php');
switch($_POST['action'])
{
	case "sendmail_confirm":
		$to = $_POST['to'];
		$subject = "[".getParam('text_top.txt')."] ".$_POST['subject'];
		$message = $_POST['message'];
		$headers = 'From: '.$_SESSION['user']."\r\n"        .
		           'Reply-To: '.$_SESSION['user']."\r\n"    .
		           'Return-Path: '.$_SESSION['user']."\r\n" .
		           'X-Mailer: PHP/'.phpversion();
//		print $to;
		if (getParam('allow_mail.conf') == true)
		{
			print getParam('action_patienter.txt');
			if (ob_get_level() == 0) ob_start();
			$i = 0;	
			$mytab = explode(",", $to);
			foreach($mytab as $email)
			{
				usleep(100000);
				$i++;
//				print "{$i}-{$email}-";
				print "{$i}-";
				ob_flush();
				flush();
// 1ere version avec la commande mail php-built-in :
//				mail($email, stripslashes($subject), stripslashes($message), $headers);
// 2eme version avec la librairie phpmailer :
				$mail = new PHPMailer();
                                // le from sera toujours l'utilisateur connecté :
				$mail->SetFrom($_SESSION['user'], $_SESSION['prenom'].' '.$_SESSION['nom']);
                                // ici il faut une condition si sender = responsable alors reply-to à l'asso :
//				$mail->AddReplyTo(getParam('contact_email.conf'), "ASESCO");
                                // le return path sert surtout pour les problèmes de delivery donc seulement vers webmaster :
				$mail->Sender = getParam('admin_email.conf');
                                // ensuite le reste est trivial :
				$mail->AddCustomHeader('X-Mailer: PHP/'.phpversion());
				$mail->Subject = stripslashes($subject);
				$mail->Body = stripslashes($message);
				$mail->AddAddress($email);
				$mail->ClearCustomHeaders("X-Mailer");
//				if(!$mail->Send()) {
//					echo "Mailer Error: " . $mail->ErrorInfo;
//				} else {
//					print "-";
//				}
				$mail->Send();
			}
			// cette ligne est nécessaire car on a mis $SMTPKeepAlive = true dans class.phpmailer !
			$mail->SmtpClose();
			// rmq : on aurait pu aussi mettre dans class.phpmailer $SingleTo = true afin de remplacer tout le foreach précédent !?
// fin 2eme version !
			ob_end_flush();
		}
		else
			print "Envoi d'emails désactivé";
		print getParam('action_continuer.txt');
		break;
	case "sendmail":
			print "<h2>Envoi d'email</h2>";
			if (isset($_POST['to']))
				$to = $_POST['to'];
			else
				$to = "";
//1ere version qui marche pas car les arrays dans _POST ne peuvent pas avoir plus de 1000 cases (environ)
//		if (isset($_POST['adh']))
//		{
//			$mytab = $_POST['adh[]'];
//2eme version qui marche pas car le javascript "traiteform" renvoit la liste des options possibles et pas seulement la liste des options cochées
//en fait si elle marche, il suffisait juste de faire le test 'if checked' ...
		if (isset($_POST['member']))
		{
			$mytab = explode("_", $_POST['member']);
//3eme version avec des variables _POST individuelles mais qui ne marche pas non plus
//			$mytab = [];
//			for($i = 1; $i <= $_POST['adhcount']; $i++)
//			{
//				if (isset($_POST["adh${i}"]))
//				   $mytab[] = $_POST["adh${i}"];
//			}
//		if (count($mytab) > 0)
//		{
//fin 3eme version
			foreach($mytab as $id)
				$adhs[$id] = getAdherent($id);
			print '<table class="sendmail">
                               <thead>
                               <tr> <th>'.count($mytab).' Destinataires :</th> <th>Composez votre message : </th> </tr>
			       <tr> <td>';
                        print '<dt>'.$to.'</dt>';
			$check_exist = array();
			foreach($adhs as $adh)
			{
				print '<dt>'.$adh['nom'].' '.$adh['prenom'].' - '.$adh['email'].'</dt>';
				if (!isset($check_exist[$adh['email']]))
				{
					$to .= $adh['email'].',';
					$check_exist[$adh['email']] = 1;
				}
			}
			$to .= getParam('admin_email.conf');
			print '</td> <td> <FORM action="index.php?page=10" method=POST>
			      	     <dt> <INPUT type="text" name="subject" size="80" value="Sujet" /></dt>
				     <dt> <TEXTAREA cols="80" rows="25" name="message">Entrez ici votre message</TEXTAREA> </dt>';
//			foreach ($adhs as $adh)
//				print '<input type="hidden" name="to[]"   value="'.${adh['email']}.'" />';
				print '<input type="hidden" name="to"     value="'.${to}          .'" />';
			print '        <input type="hidden" name="action" value="sendmail_confirm"    />';
			print '<dt>    <input type="submit"               value="envoyer"             /> </dt>';
			print '</FORM> </td> </tr> </TABLE>
			<style>.sendmail td{vertical-align:top;}</style>
			';
		}
		else
		{
			print "Aucun destinataires sélectionné";
		}
		break;
	default:
                print "Aucune action définie";
}
?>