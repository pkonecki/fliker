<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
session_start();
include("getAdherent.php");
include_once("getSections.php");
getAdherent($_SESSION['user']);
$tab=getSections($_SESSION['uid']);
if(isset($_GET['section']) && !isset($tab[$_GET['section']])){
	print '<p>Vous n"avez pas accès à cette page!</p>';
	die();
}
if ($_POST['action'] == 'modification') {
	print '<h2>Modifier Section</h2>';
	print '<FORM id="f_section_modif" action="index.php?page=4&section='.$_GET['section'].'" enctype="multipart/form-data" method="POST">';
	print '<table border=0>';
	print '<tr><td class="label"><LABEL for ="nom" >Nom</LABEL> : </td><td><INPUT type=text name="nom" id="nom" value="'.$tab[$_GET['section']]['nom'].'"></td></tr>';
	print '<tr><td class="label"><LABEL for ="description" >Description</LABEL> : </td><td><TEXTAREA rows=3 cols=25 name="description" id="description">'.$tab[$_GET['section']]['description'].'</TEXTAREA></td></tr>';
	print '<tr><td class="label"><LABEL for ="logo_section" >Logo</LABEL> : </td><td><INPUT type=file name="logo_section" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="url" >URL</LABEL> : </td><td><INPUT type=text name="url" id="url" value="'.$tab[$_GET['section']]['url'].'"></td></tr>';
	print '<input type="hidden" name="action" value="submitted" />';
	print '<input type="hidden" name="id" value="'.$tab[$_GET['section']]['id'].'" />';
	print '<tr><td colspan="2"><INPUT type="submit" value="Envoyer" ></td></tr>';
	print '</table>';
	print '</FORM>';

} else
if ($_POST['action'] == 'new') {
	print '<h2>Nouvelle Section</h2>';
	print '<FORM id="f_section_new" action="index.php?page=4" enctype="multipart/form-data" method="POST">';
	print '<table border=0>';
	print '<tr><td class="label"><LABEL for ="nom" >Nom</LABEL> : </td><td><INPUT type=text name="nom" id="nom" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="description" >Description</LABEL> : </td><td><TEXTAREA rows=3 cols=25 name="description" id="description"></TEXTAREA></td></tr>';
	print '<tr><td class="label"><LABEL for ="logo_section" >Logo</LABEL> : </td><td><INPUT type=file name="logo_section" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="url" >URL</LABEL> : </td><td><INPUT type=text name="url" id="url" ></td></tr>';
	print '<input type="hidden" name="action" value="submitted_new" />';
	print '<input type="hidden" name="id_asso" value="'.$_POST['id_asso'].'" />';
	print '<tr><td colspan="2"><INPUT type="submit" value="Envoyer"></td></tr>';
	print '</table>';
	print '</FORM>';
	
} else
if ($_POST['action'] == 'suppression_confirm') {
	print '<h2>Supprimer?</h2>';
	print '<FORM action="index.php?page=4" method="POST">
			<input type="hidden" name="id" value="'.$_GET['section'].'" />
			<input type="hidden" name="action" value="suppression" />
			<INPUT type="submit" value="Oui">
			</FORM>';
	print '<FORM action="index.php?page=4" method="POST">
			<INPUT type="submit" value="Non">
			</FORM>';
	
}

else {
	if ($_POST['action'] === 'submitted'){
		include("modifSection.php");
		modifSection($_POST);
		
	}
	if ($_POST['action'] === 'submitted_new'){
		include("newSection.php");
		newSection($_POST);
		
	}
	if ($_POST['action'] === 'suppression'){
		include("delSection.php");
		delSection($_POST['id']);
		
	}
	if(!(strcmp($_SESSION['user'],"") == 0)){
		if(empty($_GET['section'])){
			print '<h2>Vos sections</h2>';	
			print '<ul>';
			$tab=getSections($_SESSION['uid']);
			foreach($tab as $section){
				print '<li><a href=index.php?page=4&section='.$section['id'].'>'.$section['nom'].'</a></li>';
			}
			print '</ul>';

			
		} else {
			print '<h2>Fiche Section</h2>';
			print '<table>';
			print '<tr><td class="label">Nom : </td><td>'.$tab[$_GET['section']]['nom'].'</td></tr>';
			print '<tr><td class="label">Description : </td><td>'.$tab[$_GET['section']]['description'].'</td></tr>';
			print '<tr><td class="label">Url : </td><td>'.$tab[$_GET['section']]['url'].'</td></tr>';		
			$_SESSION['auth_thumb']='true';
			$photo="includes/thumb.php?folder=logo_section&file=".$_GET['section'].".jpg";
			print '<tr><TD>'.$row['description'].'</TD><TD><img src="'.$photo.'" ></TD></tr>';		
			print '<tr>';
			print '<td colspan=2><FORM action="index.php?page=4&section='.$_GET['section'].'" method="POST">
					<input type="hidden" name="action" value="modification" />
					<INPUT type="submit" value="Modifier">
					</FORM></td>';
	
			print '</tr>';					
			print '</table>';
			//Liste d'activités
			include('getActivitesBySection.php');
		    $acts = getActivitesBySection($_GET['section']);
		    print '<h3>Activités de la section</h3>';	
			print '<ul>';
			foreach($acts as $act){
				print '<li>
				<FORM action="index.php?page=5&act='.$act['id'].'" method="POST">
					<input type="hidden" name="action" value="suppression_confirm" />
					<a href=index.php?page=5&act='.$act['id'].'>'.$act['nom'].'</a>
					<INPUT type="image" src="images/unchecked.gif" value="submit">
					</FORM></li>';
				
			}
			print '</ul>';
			print '<td colspan=2><FORM action="index.php?page=5" method="POST">
			<input type="hidden" name="action" value="new" />
			<input type="hidden" name="id_sec" value="'.$_GET['section'].'" />
			<INPUT type="submit" value="Nouvelle">
			</FORM></td>';
		}
	
	}
	else {
		print "<p>Vous n'êtes pas connecté</p>";
	}
}



?>
