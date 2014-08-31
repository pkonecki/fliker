<?php
include("./includes/paths.php");
include_once("General.php");
$header = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
 <head>
  <title>::Fliker::Validation</title>
  <link rel="stylesheet" type="text/css" href="./includes/style.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
	<script type="text/javascript" src="./includes/js/jquery.js"></script>
	<script type="text/javascript" src="./includes/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="./includes/js/jquery-ui.js"></script>
	<script type="text/javascript" src="./includes/js/jquery.ui.datepicker-fr.js"></script>
	<script>
		  $(document).ready(function(){
		  	$.extend($.validator.messages, {
		        required: "Ce champs est requis",
		        number: "Veuillez entrer un numéro correct"

    		});
			$("#f_password").validate({

			rules : {
				password: {
	                required: true,
	                minlength: 5
	            },
	            password_confirm: {
	                required: true,
	                minlength: 5,
	                equalTo: "#password"
            	}
			},
			messages: {
				password: {
	                required: "Choisissez un mot de passe",
	                minlength: jQuery.format("Au moins {0} caractères")
	            },
	            password_confirm: {
	                required: "Confirmez votre mot de passe",
	                minlength: jQuery.format("Au moins {0} caractères"),
	                equalTo: "Les mots de passe ne correspondent pas"
	            }
			},
			errorPlacement: function(error, element) {
	            if ( element.is(":radio") )
	                error.appendTo( element.parent() );
	          	else
                	error.appendTo( element.parent() );
        	},
        	success: function(label) {
	            // set   as text for IE
	            label.html(" ").addClass("checked");
        	}
			});
		  });
		  $(function() {
			$( "#datepicker" ).datepicker({ changeYear: true , yearRange: "-100:+0" , changeMonth: true , dateFormat: "yy-mm-dd"  });
	});
	</script>
 </head>
 <body>
<h2>Validation</h2> ';

$footer = '</body></html>';

if (isset($_POST['action']) && $_POST['action']==="submitted") {

	include("opendb.php");
	$email = $_POST['email'];
	$id = $_POST['id'];
	$sql="UPDATE {$GLOBALS['prefix_db']}adherent SET add_mail_temp = '$email' WHERE id = '$id'";
	if (!mysql_query($sql)){
		die('Error: ' . mysql_error());
	}
	else {
		print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
		<html>
		 <head>
		  <title>::Fliker::Validation</title>
		  <link rel="stylesheet" type="text/css" href="./includes/style.css" />
		 </head>
		 <body>
		<h2>Validation</h2>';

		$activationKey=mt_rand() . mt_rand() . mt_rand() . mt_rand() . mt_rand();
		$to      = $email;
		$subject = "[".getParam('text_top.txt')."] Confirmation nouvel email";
		$message = "Bonjour,\r\n Pour confirmer le changement d'adresse email, veuillez cliquer sur le lien suivant :\r\n".getParam('url_site.conf')."validate.php?$activationKey\r\n  Si c'est une erreur ou une tentative d'usurpation, ignorez tout simplement cet email et votre adresse email ne sera pas modifiée.\r\n  Excellente saison sportive,\r\n--\r\nles administrateurs.";
		$headers = 'From: '.getParam('admin_email.conf')."\r\n"        .
		           'Reply-To: '.getParam('contact_email.conf')."\r\n"  .
		           'Return-Path: '.getParam('admin_email.conf')."\r\n" .
		           'X-Mailer: PHP/'.phpversion();
		if (getParam('allow_mail.conf') == true)
			mail($to, $subject, $message, $headers);
		print '<p>Une demande de confirmation vient d\'être envoyée à l\'adresse '.$to.'. Une fois cette confirmation effectuée, le changement d\'adresse sera fait.</p>';
		print $footer;
		// Remplacement phpmailer
		// $mail = new PHPMailer();
		// $mail->SetFrom(getParam('admin_email.conf'), $_SESSION['prenom'] . ' ' . $_SESSION['nom']);
		// $mail->AddReplyTo(getParam('contact_email.conf'), "ASESCO");
		// $mail->AddCustomHeader('Return-Path: '. getParam('admin_email.conf'));
		// $mail->AddCustomHeader('X-Mailer: PHP/'.phpversion());
		// $mail->Subject = $subject;
		// $mail->Body = $message;
		// $mail->AddAddress($to);
		// if (getParam('allow_mail.conf') == true)
		// {
		// 	$mail->Send();
		// 	print 'Un email vient d\'être envoyé à l\'adresse '.$to.', veuillez vérifier votre boîte mail.';
		// }
	}
}
else
{

	$queryString = $_SERVER['QUERY_STRING'];
	if (empty($queryString))
		print('Il n\'y a pas de clef de validation !');
	else
	{
		$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent where activationkey='$queryString' ";
		include("opendb.php");
		$result = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result))
		{
			while($row = mysql_fetch_array($result))
			{
				if ($queryString == $row["activationkey"])
				{
					if ($row["add_mail_temp"] != null)
					{
						$query = "UPDATE {$GLOBALS['prefix_db']}adherent SET add_mail_temp='', email='".$row["add_mail_temp"]."' WHERE id=".$row['id']." ";
						include("opendb.php");
						$results = mysql_query($query);
						if (!$results)
							die(mysql_error());
						print $header;
						print '<p>Changement d\'adresse email effectué.</p>';
						print $footer;
					}
					else
					{
						print $header;
						print '
							<form name="f_password" id="f_password" action="validate_email.php" method="POST">
							<table border=0>
							<tr><td>Entrez votre nouvelle adresse email : </td><td><input name="email" type="text"></td></tr>
							<input type="hidden" name="action" value="submitted" />
							<input type="hidden" name="id" value="'.$row['id'].'" />
							<tr><td colspan=2 ><input type="submit" value="Envoyer"/></td></tr>
							</table>
							</form>';
						print '</div>';
						print $footer;
					}
				}
			}
		}
		else
			print 'La clef de validation n\'existe pas ou a déjà été utilisée !';
		include("closedb.php");
	}
}
?>
