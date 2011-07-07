<?php
session_start();
$header = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
 <head>
  <title>::Fliker::Inscription</title>
  <link rel="stylesheet" type="text/css" href="../includes/style.css" />
  <link rel="stylesheet" type="text/css" href="../includes/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
	<script type="text/javascript" src="../includes/js/jquery.js"></script>
	<script type="text/javascript" src="../includes/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>
	<script type="text/javascript" src="../includes/js/jquery.ui.datepicker-fr.js"></script>

	<script>

		  	</script>
 </head>
 <body>
<h1>Inscription</h1> ';

$footer = '</body></html>';

include("../includes/paths.php");
include("getChampsAdherents.php");
include("saveImage.php");
require("class.imageconverter.php");
$dest_dossier = "../photos";

	if ($_POST['action'] == 'submitted') {

		if(!(strcmp($_SESSION['uid'],"") == 0)){
			session_start();


		$_SESSION=$_POST;
		$tab = getChampsAdherents();
		print $header;
		print "<h2>Recapitulatif</h2>";
		print '<TABLE BORDER="1">';
		foreach($tab as $row){
			if($row[inscription]==1){
				print '<TR>';
				if($row[type]==="varchar")
					print '<TD>'.$row[description].'</TD><TD>'.$_SESSION[$row[nom]].'</TD>';

				if($row[type]==="tinyint"){
					if ($_SESSION[$row[nom]]==="on")
						print '<TD>'.$row[description].'</TD><TD>Oui</TD>';
					else
						print '<TD>'.$row[description].'</TD><TD>Non</TD>';
				}
				if($row[type]==='file'){
					print '<TD>'.$row[description].'</TD><TD>'.$_FILES[$row[nom]][name].'</TD>';
					saveImage($_SESSION['email'],$row[nom]);
				}
				if($row[type]==="select")
					print '<TD>'.$row[description].'</TD><TD>'.$_SESSION[$row[nom]].'</TD>';


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
		}
		else {
			header("location: index.php") ;
		}


	} else if ($_POST['action'] == 'confirmed'){
		include("newUser.php");
		newUser($_SESSION);
		print $header;
		print "<h2>Félicitations!</h2> Votre inscription a été enregistrée! Veuillez vérifier vos email pour valider votre inscription!";
		print $footer;
		session_unset();
		session_destroy();
	}
	else {
		session_start();
		print $header;
		$tab = getChampsAdherents();
		print '<br/><FORM id="f_inscription" action="index.php" enctype="multipart/form-data" method="POST">';
		print '<table border=0>';
		foreach($tab as $row){
			if($row[inscription]==1){
			$format ="class=\"$row[format]\"";
			if ($row[required]==1) $format ="class=\"required\"";
			if($row[format] === "categorie"){
				print '<tr ><td class="label"><LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : </td>
					<td>
					<INPUT type=radio name='.$row[nom].' class="'.$row[format].'" value="M">Masculin
					<INPUT type=radio name='.$row[nom].' class="'.$row[format].'" value="F">Féminin
					</td>
					</tr>
					</div>';
			}
			else
			if($row[type]==='varchar')
				print '<tr><td class="label"><LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : </td><td><INPUT type=text name="'.$row[nom].'" id="'.$row[nom].'" '.$format.' ></td></tr>';
			else
			if($row[type]==='date')
				print '<tr><td class="label"><LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : </td><td><INPUT type=text readonly name="'.$row[nom].'" id ="datepicker" '.$format.' ></td></tr>';
			else
			if($row[type]==='tinyint')
				print '<tr><td class="label"><LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : </td><td><INPUT type=checkbox name='.$row[nom].' '.$format.'></td></tr>';
			else
			if($row[type]==='file')
				print '<tr><td class="label"><LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : </td><td><INPUT type=file name='.$row[nom].' '.$format.'></td></tr>';
			else
			if($row[type]==='select')
				print '<tr><td class="label"><LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : </td><td><SELECT name='.$row[nom].' id="'.$row[nom].'" '.$format.'></SELECT></td></tr>';

			}
		}
		print '<input type=\'hidden\' name=\'action\' value=\'submitted\' />';
		print '<tr><td colspan="2"><INPUT type=\'submit\' value=\'Send\'></td></tr>';

		print '</table>';
		print '</FORM>';
		$_SESSION['uid']=session_id();
		print $footer;
	}



?>
<script type="text/javascript">
function populatectlStatuts() {
   
    $.getJSON('../includes/statuts.php', function(data) {
		  var items = [];
		
		  $.each(data, function(key, val) {
		    $('#statut').append('<option value="' + val + '">' + key + '</option>');
		  });
		

    });

}

$(document).ready(function() {
	
	populatectlStatuts();


		  	$.extend($.validator.messages, {
		        required: "Ce champs est requis",
		        number: "Veuillez entrer un numéro correct"

    		});

			$("#f_inscription").validate({

			rules : {
				email: {
	                required: true,
	                email: true,
	                remote: "emails.php"
            	},
            	categorie: "required"

			},
			messages: {
				email: {
					required: "Ce champs est requis",
					email: "Entrez une adresse email valide",
					remote: "L\'adresse email est déjà utilisée"
					},
				categorie : "Ce champs est requis"


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