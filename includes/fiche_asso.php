<?php
session_start();
include("getAdherent.php");
getAdherent($_SESSION['user']);

if ($_POST['action'] == 'modification') {
		print '<br/><FORM id="f_asso_modif" action="index.php?page=1" enctype="multipart/form-data" method="POST">';
		print '<table border=0>';
		
		print '<tr ><td class="label"><LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : </td>';
		
		print '<input type=\'hidden\' name=\'action\' value=\'submitted\' />';
		print '<tr><td><INPUT type=\'submit\' value=\'Send\'></td></tr>';
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
