<?php
session_start();
include("getAdherent.php");
include_once("getAssociations.php");
getAdherent($_SESSION['user']);

if ($_POST['action'] == 'modification') {
		$tab=getAssociations($_SESSION['uid']);
		print '<FORM id="f_asso_modif" action="index.php?page=3" enctype="multipart/form-data" method="POST">';
		print '<table border=0>';
		print '<tr><td class="label"><LABEL for ="nom" >Nom</LABEL> : </td><td><INPUT type=text name="nom" id="nom" value="'.$tab[$_GET['asso']]['nom'].'"></td></tr>';
		print '<tr><td class="label"><LABEL for ="description" >Description</LABEL> : </td><td><TEXTAREA rows=3 cols=25 name="description" id="description">'.$tab[$_GET['asso']]['description'].'</TEXTAREA></td></tr>';
		print '<tr><td class="label"><LABEL for ="logo_asso" >Logo</LABEL> : </td><td><INPUT type=file name="logo_asso" ></td></tr>';
		print '<tr><td class="label"><LABEL for ="url" >URL</LABEL> : </td><td><INPUT type=text name="url" id="url" value="'.$tab[$_GET['asso']]['url'].'"></td></tr>';
		print '<tr><td class="label"><LABEL for ="cotisation" >Cotisation</LABEL> : </td><td><INPUT type=text name="cotisation" id="cotisation" value="'.$tab[$_GET['asso']]['cotisation'].'"></td></tr>';
		print '<input type="hidden" name="action" value="submitted" />';
		print '<input type="hidden" name="id" value="'.$tab[$_GET['asso']]['id'].'" />';
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
			print '<tr><td class="label">Cotisation : </td><td>'.$tab[$_GET['asso']]['cotisation'].'</td></tr>';
			$_SESSION['auth_thumb']='true';
			$photo="includes/thumb.php?folder=logo_asso&file=".$_GET['asso'].".jpg";
			print '<tr><TD>'.$row[description].'</TD><TD><img src="'.$photo.'" height="150"></TD></tr>';		
			print '<td colspan=2><FORM action="index.php?page=3&asso='.$_GET['asso'].'" method="POST">
					<input type=\'hidden\' name=\'action\' value=\'modification\' />
					<INPUT type=\'submit\' value=\'Modifier\'>
					</FORM></td>';
			print '</table>';
			
		}
	
	}
	else {
		print "<p>Vous n'êtes pas connecté</p>";
	}
}



?>
