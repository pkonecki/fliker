<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
session_start();
include_once("Adherent.php");
include_once("Creneau.php");
function selected($post,$val,$tab){
	if ($tab[$_GET['creneau']][$post]===$val) {
		return "selected";
	}
	else return "";
}

getAdherent($_SESSION['user']);
$tab=getCreneaux($_SESSION['uid']);
if(isset($_GET['creneau']) && !isset($tab[$_GET['creneau']])){
	print '<p>Vous n\'avez pas accès à cette page!</p>';
	die();
}
if ($_POST['action'] == 'modification') {
	print '<h2>Modifier Créneau</h2>';
	print '<FORM id="f_creneau_modif" action="index.php?page=6&creneau='.$_GET['creneau'].'" enctype="multipart/form-data" method="POST">';
	print '<table border=0>';
	print '<tr><td class="label"><LABEL for ="jour" >Jour</LABEL> : </td><td>
	<SELECT name="jour_cre" id="jour_cre">
	<OPTION value="Lundi" '.selected('jour_cre',"Lundi",$tab).' >Lundi</OPTION>
	<OPTION value="Mardi" '.selected('jour_cre',"Mardi",$tab).' >Mardi</OPTION>	
	<OPTION value="Mercredi" '.selected('jour_cre',"Mercredi",$tab).' >Mercredi</OPTION>
	<OPTION value="Jeudi" '.selected('jour_cre',"Jeudi",$tab).' >Jeudi</OPTION>
	<OPTION value="Vendredi" '.selected('jour_cre',"Vendredi",$tab).' >Vendredi</OPTION>
	<OPTION value="Samedi" '.selected('jour_cre',"Samedi",$tab).' >Samedi</OPTION>
	<OPTION value="Dimanche" '.selected('jour_cre',"Dimanche",$tab).' >Dimanche</OPTION>
	</SELECT></td></tr>';
	print '<tr><td class="label"><LABEL for ="debut_cre" >Debut</LABEL> : </td><td><INPUT type=text readonly name="debut_cre" id="debut_cre" class="timepicker "value="'.$tab[$_GET['creneau']]['debut_cre'].'"></td></tr>';
	print '<tr><td class="label"><LABEL for ="fin_cre" >Fin</LABEL> : </td><td><INPUT type=text readonly name="fin_cre" id="fin_cre" class="timepicker "value="'.$tab[$_GET['creneau']]['fin_cre'].'"></td></tr>';
	print '<tr><td class="label"><LABEL for ="lieu" >Lieu</LABEL> : </td><td><INPUT type=text name="lieu" id="lieu" value="'.$tab[$_GET['creneau']]['lieu'].'"></td></tr>';
	print '<input type="hidden" name="action" value="submitted" />';
	print '<input type="hidden" name="id_cre" value="'.$tab[$_GET['creneau']]['id_cre'].'" />';
	print '<tr><td colspan="2"><INPUT type="submit" value="Envoyer" ></td></tr>';
	print '</table>';
	print '</FORM>';

} else
if ($_POST['action'] == 'new') {
	print '<h2>Nouveau Créneau</h2>';
	print '<FORM id="f_creneau_new" action="index.php?page=6" enctype="multipart/form-data" method="POST">';
	print '<table border=0>';
	print '<tr><td class="label"><LABEL for ="jour" >Jour</LABEL> : </td><td>
	<SELECT name="jour_cre" id="jour_cre">
	<OPTION value="Lundi" selected >Lundi</OPTION>
	<OPTION value="Mardi" >Mardi</OPTION>	
	<OPTION value="Mercredi" >Mercredi</OPTION>
	<OPTION value="Jeudi" >Jeudi</OPTION>
	<OPTION value="Vendredi" >Vendredi</OPTION>
	<OPTION value="Samedi" >Samedi</OPTION>
	<OPTION value="Dimanche" >Dimanche</OPTION>
	</SELECT></td></tr>';
	print '<tr><td class="label"><LABEL for ="debut_cre" >Debut</LABEL> : </td><td><INPUT type=text readonly name="debut_cre" id="debut_cre" class="timepicker" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="fin_cre" >Fin</LABEL> : </td><td><INPUT type=text readonly name="fin_cre" id="fin_cre" class="timepicker" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="lieu" >Lieu</LABEL> : </td><td><INPUT type=text name="lieu" id="lieu" ></td></tr>';
	print '<input type="hidden" name="action" value="submitted_new" />';
	print '<tr><td colspan="2"><INPUT type="submit" value="Envoyer"></td></tr>';
	print '<input type="hidden" name="id_act" value="'.$_POST['id_act'].'">';
	print '</table>';
	print '</FORM>';
	
} else
if ($_POST['action'] == 'suppression_confirm') {
	print '<h2>Supprimer le Créneau?</h2>';
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
		modifcreneau($_POST);
		
	}
	if ($_POST['action'] === 'submitted_new'){
		newcreneau($_POST);
		
	}
	if ($_POST['action'] === 'suppression'){
		delcreneau($_POST['id']);
		
	}
	if(!(strcmp($_SESSION['user'],"") == 0)){
		$tab=getCreneaux($_SESSION['uid']);
		if(empty($_GET['creneau'])){
			print '<h2>Vos Créneaux</h2>';	
			print '<ul>';

			foreach($tab as $creneau){
				print '<li><a href=index.php?page=6&creneau='.$creneau['id_cre'].'>'.$creneau['nom_act'].' - '.$creneau['jour_cre'].' - '.$creneau['debut_cre'].' - '.$creneau['fin_cre'].'</a></li>';
				
			}
			print '</ul>';
			
		} else {
			print '<h2>Fiche Créneau</h2>';
			print '<table>';
			print '<tr><td class="label">Activité : </td><td>'.$tab[$_GET['creneau']]['nom_act'].'</td></tr>';
			print '<tr><td class="label">Jour : </td><td>'.$tab[$_GET['creneau']]['jour_cre'].'</td></tr>';
			print '<tr><td class="label">Debut : </td><td>'.$tab[$_GET['creneau']]['debut_cre'].'</td></tr>';		
			print '<tr><td class="label">Fin : </td><td>'.$tab[$_GET['creneau']]['fin_cre'].'</td></tr>';
			print '<tr><td class="label">Lieu : </td><td>'.$tab[$_GET['creneau']]['lieu'].'</td></tr>';
			print '<tr>';
			print '<td colspan=2><FORM action="index.php?page=6&creneau='.$_GET['creneau'].'" method="POST">
					<input type="hidden" name="action" value="modification" />
					<INPUT type="submit" value="Modifier">
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
$('.timepicker').timepicker({
    showPeriodLabels: false,
});
</script>