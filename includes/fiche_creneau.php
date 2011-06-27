<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
session_start();
include("getAdherent.php");
include_once("getCreneaux.php");
getAdherent($_SESSION['user']);
$tab=getCreneaux($_SESSION['uid']);
if(isset($_GET['creneau']) && !isset($tab[$_GET['creneau']])){
	print '<p>Vous n"avez pas accès à cette page!</p>';
	die();
}
if ($_POST['action'] == 'modification') {

	print '<FORM id="f_creneau_modif" action="index.php?page=6&creneau='.$_GET['creneau'].'" enctype="multipart/form-data" method="POST">';
	print '<table border=0>';
	print '<tr><td class="label"><LABEL for ="nom" >Nom</LABEL> : </td><td><INPUT type=text name="nom" id="nom" value="'.$tab[$_GET['creneau']]['nom'].'"></td></tr>';
	print '<tr><td class="label"><LABEL for ="description" >Description</LABEL> : </td><td><TEXTAREA rows=3 cols=25 name="description" id="description">'.$tab[$_GET['creneau']]['description'].'</TEXTAREA></td></tr>';
	print '<tr><td class="label"><LABEL for ="logo_creneau" >Logo</LABEL> : </td><td><INPUT type=file name="logo_creneau" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="url" >URL</LABEL> : </td><td><INPUT type=text name="url" id="url" value="'.$tab[$_GET['creneau']]['url'].'"></td></tr>';
	print '<tr><td class="label"><LABEL for ="cotisation" >Cotisation</LABEL> : </td><td><INPUT type=text name="cotisation" id="cotisation" value="'.$tab[$_GET['creneau']]['cotisation'].'"></td></tr>';
	print '<input type="hidden" name="action" value="submitted" />';
	print '<input type="hidden" name="id" value="'.$tab[$_GET['creneau']]['id'].'" />';
	print '<tr><td colspan="2"><INPUT type="submit" value="Envoyer" ></td></tr>';
	print '</table>';
	print '</FORM>';

} else
if ($_POST['action'] == 'new') {
	print '<FORM id="f_creneau_new" action="index.php?page=6" enctype="multipart/form-data" method="POST">';
	print '<table border=0>';
	print '<tr><td class="label"><LABEL for ="nom" >Nom</LABEL> : </td><td><INPUT type=text name="nom" id="nom" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="description" >Description</LABEL> : </td><td><TEXTAREA rows=3 cols=25 name="description" id="description"></TEXTAREA></td></tr>';
	print '<tr><td class="label"><LABEL for ="logo_creneau" >Logo</LABEL> : </td><td><INPUT type=file name="logo_creneau" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="url" >URL</LABEL> : </td><td><INPUT type=text name="url" id="url" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="cotisation" >Cotisation</LABEL> : </td><td><INPUT type=text name="cotisation" id="cotisation" ></td></tr>';
	print '<input type="hidden" name="action" value="submitted_new" />';
	print '<tr><td colspan="2"><INPUT type="submit" value="Envoyer"></td></tr>';
	print '</table>';
	print '</FORM>';
	
} else
if ($_POST['action'] == 'suppression_confirm') {
	print '<FORM action="index.php?page=6" method="POST">
			<input type="hidden" name="id" value="'.$_GET['creneau'].'" />
			<input type="hidden" name="action" value="suppression" />
			<INPUT type="submit" value="Oui">
			</FORM>';
	print '<FORM action="index.php?page=6" method="POST">
			<INPUT type="submit" value="Non">
			</FORM>';
	
}

else {
	if ($_POST['action'] === 'submitted'){
		include("modifCreneau.php");
		modifcreneau($_POST);
		
	}
	if ($_POST['action'] === 'submitted_new'){
		include("newCreneau.php");
		newcreneau($_POST);
		
	}
	if ($_POST['action'] === 'suppression'){
		include("delCreneau.php");
		delcreneau($_POST['id']);
		
	}
	if(!(strcmp($_SESSION['user'],"") == 0)){
		if(empty($_GET['creneau'])){
			print '<h2>Vos Créneaux</h2>';	
			print '<ul>';
			$tab=getCreneaux($_SESSION['uid']);
			foreach($tab as $creneau){
				print '<li><a href=index.php?page=6&creneau='.$creneau['id'].'>'.$creneau['nom'].'</a></li>';
				
			}
			print '</ul>';
			print '<td colspan=2><FORM action="index.php?page=6" method="POST">
			<input type="hidden" name="action" value="new" />
			<INPUT type="submit" value="Nouvelle">
			</FORM></td>';
			
		} else {
			print '<table>';
			print '<tr><td class="label">Nom : </td><td>'.$tab[$_GET['creneau']]['nom'].'</td></tr>';
			print '<tr><td class="label">Description : </td><td>'.$tab[$_GET['creneau']]['description'].'</td></tr>';
			print '<tr><td class="label">Url : </td><td>'.$tab[$_GET['creneau']]['url'].'</td></tr>';		
			print '<tr><td class="label">Cotisation : </td><td>'.$tab[$_GET['creneau']]['cotisation'].'</td></tr>';
			$_SESSION['auth_thumb']='true';
			$photo="includes/thumb.php?folder=logo_creneau&file=".$_GET['creneau'].".jpg";
			print '<tr><TD>'.$row[description].'</TD><TD><img src="'.$photo.'" ></TD></tr>';		
			print '<tr>';
			print '<td><FORM action="index.php?page=6&creneau='.$_GET['creneau'].'" method="POST">
					<input type="hidden" name="action" value="modification" />
					<INPUT type="submit" value="Modifier">
					</FORM></td>';
			print '<td><FORM action="index.php?page=6&creneau='.$_GET['creneau'].'" method="POST">
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
