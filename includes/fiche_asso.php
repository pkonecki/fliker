<?php
session_start();
include("getAdherent.php");
getAdherent($_SESSION['user']);

if ($_POST['action'] == 'modification') {
		print '<br/><FORM id="f_asso_modif" action="index.php?page=1" enctype="multipart/form-data" method="POST">';
		print '<table border=0>';
		
		print '<tr><td class="label"><LABEL for ="nom" >Nom</LABEL> : </td><td><INPUT type=text name="nom" id="nom" value="'.$_POST['nom'].'"></td></tr>';
		print '<tr><td class="label"><LABEL for ="description" >Nom</LABEL> : </td><td><TEXTAREA type=text name="description" id="description" value="'.$_POST['description'].'"></td></tr>';
		
		print '<input type=\'hidden\' name=\'action\' value=\'submitted\' />';
		print '<tr><td colspan="2"><INPUT type=\'submit\' value=\'Send\'></td></tr>';
		print '</table>';
		print '</FORM>';
			
}
if ($_POST['action'] == 'new') {
		
			
}
else {
	if ($_POST['action'] == 'submitted'){
		include("modifAsso.php");
		modifAsso($_POST);
		
	}
	if(!(strcmp($_SESSION['user'],"") == 0)){
	
	
	}
	else {
		print "<p>Vous n'etes pas connecté</p>";
	}
}



?>
