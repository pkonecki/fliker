<?php
session_start();
include("getAdherent.php");
getAdherent($_SESSION['user']);

if ($_POST['action'] == 'modification') {
		print '<br/><FORM id="f_asso_modif" action="index.php?page=1" enctype="multipart/form-data" method="POST">';
		print '<table border=0>';
		
		print '<tr><td class="label"><LABEL for ="nom" >Nom</LABEL> : </td><td><INPUT type=text name="nom" id="nom" value="'.$_POST['nom'].'"></td></tr>';
		print '<tr><td class="label"><LABEL for ="description" >Nom</LABEL> : </td><td><TEXTAREA type=text name="description" id="description" value="'.$_POST['description'].'"></td></tr>';
		print '<tr><td class="label"><LABEL for ="logo_asso" >Logo</LABEL> : </td><td><INPUT type=file name=logo_asso ></td></tr>';
		print '<tr><td class="label"><LABEL for ="url" >URL</LABEL> : </td><td><INPUT type=text name="url" id="url" value="'.$_POST['url'].'"></td></tr>';
		print '<input type=\'hidden\' name=\'action\' value=\'submitted\' />';
		print '<tr><td colspan="2"><INPUT type=\'submit\' value=\'Send\'></td></tr>';
		print '</table>';
		print '</FORM>';
			
} else
if ($_POST['action'] == 'new') {
		
			
}
else {
	if ($_POST['action'] == 'submitted'){
		include("modifAsso.php");
		modifAsso($_POST);
		
	}
	if(!(strcmp($_SESSION['user'],"") == 0)){
		include_once("getAssociations.php");
		$tab=getAssociations($_SESSION['uid']);
		if(empty($_GET['asso'])){
			print '<h2>Vos associations</h2>';	
			foreach($tab as $asso){
				print '<a href=index.php?page=3&asso='.$asso['id'].'>'.$asso['nom'].'</a>  ';
				
			}
			
			
		} else {
			print '<table>';
			print '<tr><td class="label">Nom : </td><td>'.$tab[$_GET['asso']]['nom'].'</td></tr>';
			print '<tr><td class="label">Description : </td><td>'.$tab[$_GET['asso']]['description'].'</td></tr>';
			print '<tr><td class="label">Url : </td><td>'.$tab[$_GET['asso']]['url'].'</td></tr>';		
			print '</table>';
		}
	
	}
	else {
		print "<p>Vous n'etes pas connecté</p>";
	}
}



?>
