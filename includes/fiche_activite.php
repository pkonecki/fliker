<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
session_start();
include("getAdherent.php");
include_once("getActivites.php");
getAdherent($_SESSION['user']);
$tab=getActivites($_SESSION['uid']);
if(isset($_GET['act']) && !isset($tab[$_GET['act']])){
	print '<p>Vous n"avez pas accès à cette page!</p>';
	die();
}
if ($_POST['action'] == 'modification') {

	print '<FORM id="f_act_modif" action="index.php?page=5&act='.$_GET['act'].'" enctype="multipart/form-data" method="POST">';
	print '<table border=0>';
	print '<tr><td class="label"><LABEL for ="nom" >Nom</LABEL> : </td><td><INPUT type=text name="nom" id="nom" value="'.$tab[$_GET['act']]['nom'].'"></td></tr>';
	print '<tr><td class="label"><LABEL for ="description" >Description</LABEL> : </td><td><TEXTAREA rows=3 cols=25 name="description" id="description">'.$tab[$_GET['act']]['description'].'</TEXTAREA></td></tr>';
	print '<tr><td class="label"><LABEL for ="logo_act" >Logo</LABEL> : </td><td><INPUT type=file name="logo_act" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="url" >URL</LABEL> : </td><td><INPUT type=text name="url" id="url" value="'.$tab[$_GET['act']]['url'].'"></td></tr>';
	print '<input type="hidden" name="action" value="submitted" />';
	print '<input type="hidden" name="id" value="'.$tab[$_GET['act']]['id'].'" />';
	print '<tr><td class="label"><LABEL>Section mère</label></td><td><select name="id_sec" id="ctlSection"></select></td>';
	print '<tr><td colspan="2"><INPUT type="submit" value="Envoyer" ></td></tr>';
	print '</table>';
	print '</FORM>';
} else
if ($_POST['action'] == 'new') {
	print '<FORM id="f_act_new" action="index.php?page=5" enctype="multipart/form-data" method="POST">';
	print '<table border=0>';
	print '<tr><td class="label"><LABEL for ="nom" >Nom</LABEL> : </td><td><INPUT type=text name="nom" id="nom" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="description" >Description</LABEL> : </td><td><TEXTAREA rows=3 cols=25 name="description" id="description"></TEXTAREA></td></tr>';
	print '<tr><td class="label"><LABEL for ="logo_act" >Logo</LABEL> : </td><td><INPUT type=file name="logo_act" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="url" >URL</LABEL> : </td><td><INPUT type=text name="url" id="url" ></td></tr>';
	print '<tr><td class="label"><LABEL>Section mère</label></td><td><select name="id_sec" id="ctlSection"></select></td>';
	print '<input type="hidden" name="action" value="submitted_new" />';
	print '<tr><td colspan="2"><INPUT type="submit" value="Envoyer"></td></tr>';
	print '</table>';
	print '</FORM>';
	
} else
if ($_POST['action'] == 'suppression_confirm') {
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
		include("modifActivite.php");
		modifActivite($_POST);
		
	}
	if ($_POST['action'] === 'submitted_new'){
		include("newActivite.php");
		newActivite($_POST);
		
	}
	if ($_POST['action'] === 'suppression'){
		include("delActivite.php");
		delActivite($_POST['id']);
		
	}
	if(!(strcmp($_SESSION['user'],"") == 0)){
		if(empty($_GET['act'])){
			print '<h2>Vos Activités</h2>';	
			print '<ul>';
			$tab=getActivites($_SESSION['uid']);
			foreach($tab as $act){
				print '<li><a href=index.php?page=5&act='.$act['id'].'>'.$act['nom'].'</a></li>';
				
			}
			print '</ul>';
			print '<td colspan=2><FORM action="index.php?page=5" method="POST">
			<input type="hidden" name="action" value="new" />
			<INPUT type="submit" value="Nouvelle">
			</FORM></td>';
			
		} else {
			print '<table>';
			print '<tr><td class="label">Nom : </td><td>'.$tab[$_GET['act']]['nom'].'</td></tr>';
			print '<tr><td class="label">Description : </td><td>'.$tab[$_GET['act']]['description'].'</td></tr>';
			print '<tr><td class="label">Url : </td><td>'.$tab[$_GET['act']]['url'].'</td></tr>';		
			$_SESSION['auth_thumb']='true';
			$photo="includes/thumb.php?folder=logo_act&file=".$_GET['act'].".jpg";
			print '<tr><TD>'.$row[description].'</TD><TD><img src="'.$photo.'" ></TD></tr>';		
			print '<tr>';
			print '<td><FORM action="index.php?page=5&act='.$_GET['act'].'" method="POST">
					<input type="hidden" name="action" value="modification" />
					<INPUT type="submit" value="Modifier">
					</FORM></td>';
			print '<td><FORM action="index.php?page=5&act='.$_GET['act'].'" method="POST">
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
<script type="text/javascript">
function populatectlSection() {
   
    $.getJSON('includes/sections.php', function(data) {
		  var items = [];
		
		  $.each(data, function(key, val) {
		    $('#ctlSection').append('<option id="' + key + '">' + val + '</option>');
		  });
		

    });

}

$(document).ready(function() {
	
	populatectlSection();

	
});
</script>