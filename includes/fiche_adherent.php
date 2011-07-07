<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
session_start();
include_once("Adherent.php");
getAdherent($_SESSION['user']);


$dest_dossier = "../photos";
$script = '<script type="text/javascript" src="./includes/js/jquery.js"></script>
	<script type="text/javascript" src="./includes/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="./includes/js/jquery-ui.js"></script>
	<script type="text/javascript" src="./includes/js/jquery.ui.datepicker-fr.js"></script>

	<script>

		  $(document).ready(function(){
		  	$.extend($.validator.messages, {
		        required: "Ce champs est requis",
		        number: "Veuillez entrer un numéro correct"

    		});

			$("#f_adherent_modif").validate({

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

	</script>';

print $script;
	if ($_POST['action'] == 'modification') {
		$tab = getChampsAdherents();
		print '<FORM id="f_adherent_modif" action="index.php?page=1" enctype="multipart/form-data" method="POST">';
		print '<table border=0>';
		foreach($tab as $row){
			if($row[user_editable]==1){
				$format ="class=\"$row[format]\"";
				if ($row[required]==1) $format ="class=\"required\"";
				if($row[format] === "categorie"){
					if($_SESSION[$row['nom']]==='M'){
						$homme='checked';
						$femme='';
					} else {
						$homme='';
						$femme='checked';

					}
					print '<tr ><td class="label"><LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : </td>
						<td>
						<INPUT type=radio name='.$row[nom].' class="'.$row[format].'" value="M" '.$homme.' >Masculin
						<INPUT type=radio name='.$row[nom].' class="'.$row[format].'" value="F" '.$femme.' >Féminin
						</td>
						</tr>
						</div>';
				}
				else
				if($row[type]==='varchar')
					print '<tr><td class="label"><LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : </td><td><INPUT type=text name="'.$row[nom].'" id="'.$row[nom].'" '.$format.' value="'.$_SESSION[$row['nom']].'"></td></tr>';
				else
				if($row[type]==='date')
					print '<tr><td class="label"><LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : </td><td><INPUT type=text name="'.$row[nom].'" id ="datepicker" '.$format.'  value="'.$_SESSION[$row['nom']].'"></td></tr>';
				else
				if($row[type]==='tinyint')
					print '<tr><td class="label"><LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : </td><td><INPUT type=checkbox name='.$row[nom].' '.$format.'  value="'.$_SESSION[$row['nom']].'"></td></tr>';
				else
				if($row[type]==='file')
					print '<tr><td class="label"><LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : </td><td><INPUT type=file name='.$row[nom].' '.$format.'  ></td></tr>';

			}
		}
		print '<input type=\'hidden\' name=\'action\' value=\'submitted\' />';
		print '<tr><td colspan="2"><INPUT type=\'submit\' value=\'Send\'></td></tr>';

		print '</table>';
		print '</FORM>';






	}
	else {
		if ($_POST['action'] == 'submitted'){

			modifAdherent($_POST);
			getAdherent($_SESSION['user']);
		}
		if(!(strcmp($_SESSION['user'],"") == 0)){

			$tab = getChampsAdherents();
			print "<h2>Fiche adherent</h2>";
			print '<TABLE BORDER="0">';
			foreach($tab as $row){
				if($row[user_viewable]==1){
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
						$_SESSION['auth_thumb']='true';
						$photo="includes/thumb.php?folder=".$row['nom']."&file=".$_SESSION['user'].".jpg";
						print '<TD>'.$row[description].'</TD><TD><img src="'.$photo.'" height="150"></TD>';
					}

				}
				print '</TR>';
			}
			print '</TABLE>';

			print '<FORM action="index.php?page=1" method="POST">
		<input type=\'hidden\' name=\'action\' value=\'modification\' />
		<INPUT type=\'submit\' value=\'Modifier\'>
		</FORM>
		';
		}
		else {
			print "<p>Vous n'êtes pas connecté</p>";
		}
	}



?>
