<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
session_start();
include("getAdherent.php");
include_once("getAssociations.php");
getAdherent($_SESSION['user']);
$tab=getAssociations($_SESSION['uid']);
if(isset($_GET['asso']) && !isset($tab[$_GET['asso']])){
	print '<p>Vous n"avez pas accès à cette page!</p>';
	die();
}
if ($_POST['action'] == 'modification') {

	print '<FORM id="f_asso_modif" action="index.php?page=3&asso='.$_GET['asso'].'" enctype="multipart/form-data" method="POST">';
	print '<table border=0>';
	print '<tr><td class="label"><LABEL for ="nom" >Nom</LABEL> : </td><td><INPUT type=text name="nom" id="nom" value="'.$tab[$_GET['asso']]['nom'].'"></td></tr>';
	print '<tr><td class="label"><LABEL for ="description" >Description</LABEL> : </td><td><TEXTAREA rows=3 cols=25 name="description" id="description">'.$tab[$_GET['asso']]['description'].'</TEXTAREA></td></tr>';
	print '<tr><td class="label"><LABEL for ="logo_asso" >Logo</LABEL> : </td><td><INPUT type=file name="logo_asso" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="url" >URL</LABEL> : </td><td><INPUT type=text name="url" id="url" value="'.$tab[$_GET['asso']]['url'].'"></td></tr>';
	print '<tr><td class="label"><LABEL for ="cotisation" >Cotisation</LABEL> : </td><td><INPUT type=text name="cotisation" id="cotisation" value="'.$tab[$_GET['asso']]['cotisation'].'"></td></tr>';
	print '<input type="hidden" name="action" value="submitted" />';
	print '<input type="hidden" name="id" value="'.$tab[$_GET['asso']]['id'].'" />';
	print '<tr><td colspan="2"><INPUT type="submit" value="Envoyer" ></td></tr>';
	print '</table>';
	print '</FORM>';

} else
if ($_POST['action'] == 'new') {
	print '<FORM id="f_asso_new" action="index.php?page=3" enctype="multipart/form-data" method="POST">';
	print '<table border=0>';
	print '<tr><td class="label"><LABEL for ="nom" >Nom</LABEL> : </td><td><INPUT type=text name="nom" id="nom" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="description" >Description</LABEL> : </td><td><TEXTAREA rows=3 cols=25 name="description" id="description"></TEXTAREA></td></tr>';
	print '<tr><td class="label"><LABEL for ="logo_asso" >Logo</LABEL> : </td><td><INPUT type=file name="logo_asso" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="url" >URL</LABEL> : </td><td><INPUT type=text name="url" id="url" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="cotisation" >Cotisation</LABEL> : </td><td><INPUT type=text name="cotisation" id="cotisation" ></td></tr>';
	print '<input type="hidden" name="action" value="submitted_new" />';
	print '<tr><td colspan="2"><INPUT type="submit" value="Envoyer"></td></tr>';
	print '</table>';
	print '</FORM>';
	
} else
if ($_POST['action'] == 'suppression_confirm') {
	print '<FORM action="index.php?page=3" method="POST">
			<input type="hidden" name="id" value="'.$_GET['asso'].'" />
			<input type="hidden" name="action" value="suppression" />
			<INPUT type="submit" value="Oui">
			</FORM>';
	print '<FORM action="index.php?page=3" method="POST">
			<INPUT type="submit" value="Non">
			</FORM>';
	
}

else {
	if ($_POST['action'] === 'submitted'){
		include("modifAsso.php");
		modifAsso($_POST);
		
	}
	if ($_POST['action'] === 'submitted_new'){
		include("newAsso.php");
		newAsso($_POST);
		
	}
	if ($_POST['action'] === 'suppression'){
		include("delAsso.php");
		delAsso($_POST['id']);
		
	}
	if(!(strcmp($_SESSION['user'],"") == 0)){
		if(empty($_GET['asso'])){
			print '<h2>Vos associations</h2>';	
			print '<ul>';
			$tab=getAssociations($_SESSION['uid']);
			foreach($tab as $asso){
				print '<li><a href=index.php?page=3&asso='.$asso['id'].'>'.$asso['nom'].'</a></li>';
				
			}
			print '</ul>';
			print '<td colspan=2><FORM action="index.php?page=3" method="POST">
			<input type="hidden" name="action" value="new" />
			<INPUT type="submit" value="Nouvelle">
			</FORM></td>';
			
		} else {
			print '<table>';
			print '<tr><td class="label">Nom : </td><td>'.$tab[$_GET['asso']]['nom'].'</td></tr>';
			print '<tr><td class="label">Description : </td><td>'.$tab[$_GET['asso']]['description'].'</td></tr>';
			print '<tr><td class="label">Url : </td><td>'.$tab[$_GET['asso']]['url'].'</td></tr>';		
			print '<tr><td class="label">Cotisation : </td><td>'.$tab[$_GET['asso']]['cotisation'].'</td></tr>';
			$_SESSION['auth_thumb']='true';
			$photo="includes/thumb.php?folder=logo_asso&file=".$_GET['asso'].".jpg";
			print '<tr><TD>'.$row[description].'</TD><TD><img src="'.$photo.'" ></TD></tr>';		
			print '<tr>';
			print '<td><FORM action="index.php?page=3&asso='.$_GET['asso'].'" method="POST">
					<input type="hidden" name="action" value="modification" />
					<INPUT type="submit" value="Modifier">
					</FORM></td>';
			print '<td><FORM action="index.php?page=3&asso='.$_GET['asso'].'" method="POST">
					<input type="hidden" name="action" value="suppression_confirm" />
					<INPUT type="submit" value="Supprimer">
					</FORM></td>';		
			print '</tr>';					
			print '</table>';
			
		}
	
	}
	else {
		print "<p>Vous n'êtes pas connecté</p>";
	}
}



?>
