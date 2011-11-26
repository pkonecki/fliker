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
<h1>Validation</h1> ';

$footer = '</body></html>';

if ($_POST['action']==="submitted") {

	include("opendb.php");
	$password = $_POST['password'];
	$id = $_POST['id'];
	$sql="UPDATE {$GLOBALS['prefix_db']}adherent SET password = MD5('$password'), activationkey = '', active=1 WHERE id = '$id'";
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
		<h1>Validation</h1>';

		print '<div>Votre compte est à présent activé ! Vous allez être redirigé vers la page de connexion ...</div>';

		print $footer;
	}
	include("closedb.php");
}
else {

	$queryString = $_SERVER['QUERY_STRING'];
	if (empty($queryString)){
		print('Il n\'y a pas de clef de validation !');
	}
	else {
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent where activationkey='$queryString' ";
	include("opendb.php");
	$result = mysql_query($query) or die(mysql_error());
	if (mysql_num_rows($result))
	while($row = mysql_fetch_array($result)){
	  	if ($queryString == $row["activationkey"]){
  			print $header;
		 	print "<div>Merci, " . $row["prenom"] . " !<p>Votre compte est presque activé. Votre \"identifiant\" sera votre adresse email.</p><p>Veuillez SVP définir le mot de passe qui sera associé à votre identifiant :</p>";
		  	print '
			<form name="f_password" id="f_password" action="validate.php" method="POST">
			<table border=0>
		  	<tr><td>Entrez un mot de passe : </td><td><input name="password" type="password" id="password" size="25"></td></tr>
		  	<tr><td>Vérifiez ce mot de passe : </td><td><input name="password_confirm" type="password" id="password_confirm" size="25"></td></tr>
		  	<input type="hidden" name="action" value="submitted" />
		  	<input type="hidden" name="id" value="'.$row[id].'" />
		  	<tr><td colspan=2 ><input type="submit" value="Envoyer"/></td></tr>
		  	</table>
			</form>

			';
     		print '</div>';
  			print $footer;
	  	}
	}
	else print 'La clef de validation n\'existe pas ou a déjà été utilisée !';
	include("closedb.php");

	}
}
?>