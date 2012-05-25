<?php
include("./includes/paths.php");
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
		  <meta http-equiv="refresh" content="3;url=login.php" />
		 </head>
		 <body>
		<h2>Validation</h2>';

		$to      = $email;
		$subject = "Confirmation nouvel email Fliker";
		$message = "Bonjour,\r\r Pour que le changement d\'addresse email se fasse veuillez cliquer sur le lien suivant:\r".getParam('url_site')."validate.php?$activationKey\r\r  Si c'est une erreur ou une tentative d'usurpation, ignorez tout simplement cet email et vos coordonnées seront automatiquement purgées de notre serveur dans quelques temps.\r\r  \r\r  Remarque: Notre serveur d'adhésion en ligne (".getParam('url_site').") est différent de notre site web principal ... Ne vous trompez donc pas d'URL quand vous essaierez de vous connecter !\r\r  Excellente saison sportive,\r\r--\rles administrateurs.";
		$headers = 'From: '.getParam('admin_email') . "\r\n" .
				'Reply-To: '.getParam('contact_email') . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
		$return = mail($to, $subject, $message, $headers);
		print '<p>Une demande de confirmation vient d\'être envoyé à l\'adresse '.$email.'. Une fois cette confirmation effectué le changement d\'adresse sera fait.</p>';

		print $footer;
	}
	include("closedb.php");
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
						$query = "UPDATE {$GLOBALS['prefix_db']}presence SET add_mail_temp='' WHERE id=".$row['id']." ";
						include("opendb.php");
						$result = mysql_query($query);
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
						<input type="hidden" name="id" value="'.$row[id].'" />
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