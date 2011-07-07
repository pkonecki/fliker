<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
session_start();
include_once("Adherent.php");
include_once("Activite.php");
include_once('Creneau.php');
getAdherent($_SESSION['user']);
$tab=getActivites($_SESSION['uid']);
if(isset($_GET['act']) && !isset($tab[$_GET['act']])){
	print '<p>Vous n\'avez pas acc�s � cette page!</p>';
	die();
}
if ($_POST['action'] == 'modification') {
	print '<h2>Modifier Activit�</h2>';
	print '<FORM id="f_act_modif" action="index.php?page=5&act='.$_GET['act'].'" enctype="multipart/form-data" method="POST">';
	print '<table border=0>';
	print '<tr><td class="label"><LABEL for ="nom" >Nom</LABEL> : </td><td><INPUT type=text name="nom" id="nom" value="'.$tab[$_GET['act']]['nom'].'"></td></tr>';
	print '<tr><td class="label"><LABEL for ="description" >Description</LABEL> : </td><td><TEXTAREA rows=3 cols=25 name="description" id="description">'.$tab[$_GET['act']]['description'].'</TEXTAREA></td></tr>';
	print '<tr><td class="label"><LABEL for ="logo_act" >Logo</LABEL> : </td><td><INPUT type=file name="logo_act" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="url" >URL</LABEL> : </td><td><INPUT type=text name="url" id="url" value="'.$tab[$_GET['act']]['url'].'"></td></tr>';
	print '<input type="hidden" name="action" value="submitted" />';
	print '<input type="hidden" name="id" value="'.$tab[$_GET['act']]['id'].'" />';
	print '<tr><td colspan="2"><INPUT type="submit" value="Envoyer" ></td></tr>';
	print '</table>';
	print '</FORM>';
} else
if ($_POST['action'] == 'new') {
	print '<h2>Nouvelle Activit�</h2>';
	print '<FORM id="f_act_new" action="index.php?page=5" enctype="multipart/form-data" method="POST">';
	print '<table border=0>';
	print '<tr><td class="label"><LABEL for ="nom" >Nom</LABEL> : </td><td><INPUT type=text name="nom" id="nom" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="description" >Description</LABEL> : </td><td><TEXTAREA rows=3 cols=25 name="description" id="description"></TEXTAREA></td></tr>';
	print '<tr><td class="label"><LABEL for ="logo_act" >Logo</LABEL> : </td><td><INPUT type=file name="logo_act" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="url" >URL</LABEL> : </td><td><INPUT type=text name="url" id="url" ></td></tr>';
	print '<input type="hidden" name="action" value="submitted_new" />';
	print '<input type="hidden" name="id_sec" value="'.$_POST['id_sec'].'" />';
	print '<tr><td colspan="2"><INPUT type="submit" value="Envoyer"></td></tr>';
	print '</table>';
	print '</FORM>';
	
} else
if ($_POST['action'] == 'suppression_confirm') {
	print '<h2>Supprimer Activit�?</h2>';
	print '<FORM action="index.php?page=5" method="POST">
			<input type="hidden" name="id" value="'.$_GET['act'].'" />
			<input type="hidden" name="action" value="suppression" />
			<INPUT type="submit" value="Oui">
			</FORM>';
	print '<FORM action="index.php?page=5" method="POST">
			<INPUT type="submit" value="Non">
			</FORM>';
	
}

else {
	if ($_POST['action'] === 'submitted'){
		modifActivite($_POST);
		
	}
	if ($_POST['action'] === 'submitted_new'){
		newActivite($_POST);
		
	}
	if ($_POST['action'] === 'suppression'){
		delActivite($_POST['id']);
		
	}
	if(!(strcmp($_SESSION['user'],"") == 0)){
		$tab=getActivites($_SESSION['uid']);
		if(empty($_GET['act'])){
			print '<h2>Vos Activit�s</h2>';	
			print '<ul>';

			foreach($tab as $act){
				print '<li><a href=index.php?page=5&act='.$act['id'].'>'.$act['nom'].'</a></li>';
				
			}
			print '</ul>';

			
		} else {
			print '<h2>Fiche Activit�</h2>';
			print '<table>';
			print '<tr><td class="label">Nom : </td><td>'.$tab[$_GET['act']]['nom'].'</td></tr>';
			print '<tr><td class="label">Description : </td><td>'.$tab[$_GET['act']]['description'].'</td></tr>';
			print '<tr><td class="label">Url : </td><td>'.$tab[$_GET['act']]['url'].'</td></tr>';		
			$_SESSION['auth_thumb']='true';
			$photo="includes/thumb.php?folder=logo_act&file=".$_GET['act'].".jpg";
			print '<tr><TD>'.$row['description'].'</TD><TD><img src="'.$photo.'" ></TD></tr>';		
			print '<tr>';
			print '<td colspan=2><FORM action="index.php?page=5&act='.$_GET['act'].'" method="POST">
					<input type="hidden" name="action" value="modification" />
					<INPUT type="submit" value="Modifier">
					</FORM></td>';	
			print '</tr>';					
			print '</table>';
			//Liste de cr�neaux
		    $crens = getCreneauxByActivite($_GET['act']);
			print '<h2>Cr�neaux de l\'activit�</h2>';	
			print '<ul>';
			foreach($crens as $creneau){
				print '<FORM action="index.php?page=6&creneau='.$creneau['id'].'" method="POST">
					<input type="hidden" name="action" value="suppression_confirm" />
				<li><a href=index.php?page=6&creneau='.$creneau['id'].'>'.$creneau['jour'].' - '.$creneau['debut'].' - '.$creneau['fin'].'</a>
				<INPUT type="image" src="images/unchecked.gif" value="submit">
					</FORM></li>';
				
			}
			print '</ul>';
			print '<td colspan=2><FORM action="index.php?page=6" method="POST">
			<input type="hidden" name="action" value="new" />
			<input type="hidden" name="id_act" value="'.$_GET['act'].'">
			<INPUT type="submit" value="Nouveau">
			</FORM></td>';
			
		}
	
	}
	else {
		print "<p>Vous n'�tes pas connect�</p>";
	}
}



?>
