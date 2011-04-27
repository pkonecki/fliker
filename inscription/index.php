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
				email: { 
                required: true, 
                email: true, 
                remote: "emails.php" 
            }
			
			},
			messages: {
				required : "Ce champ est requis",
				email: { 
					required: "Please enter a valid email address", 
					minlength: "Please enter a valid email address", 
					remote: jQuery.format("{0} is already in use") 
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

	if ($_POST['action'] == 'submitted') {
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
		$_SESSION=$_POST;
		print $footer;
	} else if ($_POST['action'] == 'confirmed'){
		include("normalTask_newUser.php");
		newUser($_SESSION);
		session_destroy();
	}
	else {
		
		print $header;
		$tab = getChampsAdherents();
		print '<br/><FORM id="f_inscription" action="index.php" method="POST">';
		foreach($tab as $row){
			if($row[inscription]==1){
			$format =$row[format];
			// if ($row[required]==1)
				// $format ="required ".$row[format];
			if($row[format] === "civilite"){
				print '<LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : <INPUT type=radio name='.$row[nom].' value="mr">Monsieur 
					<INPUT type=radio name='.$row[nom].' value="mme">Madame 
					<INPUT type=radio name='.$row[nom].' value="mlle">Mademoiselle
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
			}
		}
		print '<input type=\'hidden\' name=\'action\' value=\'submitted\' />';
		print '<INPUT type=\'submit\' value=\'Send\'>';
		print '</FORM>';
		print $footer;
	}
	


?>
