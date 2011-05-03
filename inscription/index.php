<?php
session_start();
$header = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
 <head>
  <title>Inscription</title>
  <link rel="stylesheet" type="text/css" href="../includes/style.css" />
  <link rel="stylesheet" type="text/css" href="../includes/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
	<script type="text/javascript" src="../includes/js/jquery.js"></script>
	<script type="text/javascript" src="../includes/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>
	<script type="text/javascript" src="../includes/js/jquery.ui.datepicker-fr.js"></script>

	<script>

		  $(document).ready(function(){
			$("#f_inscription").validate({
			rules : {
				required : {
					required: true,
					minlength: 2

				},
				email: {
	                required: true,
	                email: true,
	                remote: "emails.php"
            	},
            	categorie: "required",

			},
			messages: {
				required : "Ce champ est requis",
				email: {
					required: "Ce champs est requis",
					email: "Entrez une adresse email valide",
					remote: "L\'adresse email est déjà utilisée"
					},

				number : "Veuillez entrer un numéro correct",
				date : "Veuillez entrer une date valide"

			}

			});
		  });
		  $(function() {
			$( "#datepicker" ).datepicker({ changeYear: true , yearRange: "-100:+0" , changeMonth: true , dateFormat: "yy-mm-dd"  });

	});

	</script>
 </head>
 <body>
<h1>Inscription</h1> ';

$footer = '</body></html>';

include("../includes/paths.php");
include("normalTask_getChampsAdherents.php");
function get_extension($nom) {
	$nom = explode(".", $nom);
	$nb = count($nom);
	return strtolower($nom[$nb-1]);
}
$dest_dossier = "../photos";

	if ($_POST['action'] == 'submitted') {

		$_SESSION=$_POST;
		$tab = getChampsAdherents();
		print $header;
		print "<h2>Recapitulatif</h2>";
		print '<TABLE BORDER="1">';
		foreach($tab as $row){
			if($row[inscription]==1){
				print '<TR>';
				if($row[type]==="varchar")
					print '<TD>'.$row[description].'</TD><TD>'.$_POST[$row[nom]].'</TD>';

				if($row[type]==="tinyint"){
					if ($_POST[$row[nom]]==="on")
						print '<TD>'.$row[description].'</TD><TD>Oui</TD>';
					else
						print '<TD>'.$row[description].'</TD><TD>Non</TD>';
				}
				if($row[type]==='file'){
					print '<TD>'.$row[description].'</TD><TD>'.$_FILES[$row[nom]][name].'</TD>';
					$_SESSION[$row[nom]]=$_FILES[$row[nom]];
					$dest_fichier = $_POST[email].'.'.get_extension($_FILES[$row[nom]][name]);
					move_uploaded_file($_FILES['photo']['tmp_name'], $photos ."\\". $dest_fichier);

				}

			}
			print '</TR>';
		}
		print '</TABLE>';
		print '<button type="button" onclick="history.go(-1)">
			Modifier
		</button> ';
		print '<FORM action="index.php" method="POST">
		<input type=\'hidden\' name=\'action\' value=\'confirmed\' />
		<INPUT type=\'submit\' value=\'Confirmer\'>
		</FORM>
		';
		print $footer;
	} else if ($_POST['action'] == 'confirmed'){
		include("normalTask_newUser.php");
		newUser($_SESSION);
		print $header;
		print "<h2>Félicitations!</h2> Votre inscription a été enregistrée! Veuillez vérifier vos email pour valider votre inscription!";
		print $footer;
		session_destroy();
	}
	else {

		print $header;
		$tab = getChampsAdherents();
		print '<br/><FORM id="f_inscription" action="index.php" enctype="multipart/form-data" method="POST">';
		foreach($tab as $row){
			if($row[inscription]==1){
			$format =$row[format];
			if ($row[required]==1)
				$format .=" required ";
			if($row[format] === "categorie"){
				print '<LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : <INPUT type=radio name='.$row[nom].' value="M">Homme
					<INPUT type=radio name='.$row[nom].' value="F">Femme
					<br/>';
			}
			else
			if($row[type]==='varchar')
				print '<LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : <INPUT type=text name='.$row[nom].' id='.$row[nom].' value="'.$_POST[$row[nom]].'" class="'.$format.'" minlength="2" ><br/>';
			else
			if($row[type]==='date')
				print '<LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : <INPUT type=text name='.$row[nom].' id ="datepicker" class="'.$format.'"><br/>';
			else
			if($row[type]==='tinyint')
				print '<LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : <INPUT type=checkbox name='.$row[nom].' class="'.$format.'"><br/>';
			else
			if($row[type]==='file')
				print '<LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : <INPUT type=file name='.$row[nom].' class="'.$format.'"><br/>';

			}
		}
		print '<input type=\'hidden\' name=\'action\' value=\'submitted\' />';
		print '<INPUT type=\'submit\' value=\'Send\'>';
		print '</FORM>';
		print $footer;
	}



?>
