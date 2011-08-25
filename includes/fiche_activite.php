<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
session_start();
getAdherent($_SESSION['user']);
$tab=getActivites($_SESSION['uid']);
if(isset($_GET['act']) && !isset($tab[$_GET['act']])){
	print '<p>Vous n\'avez pas accès à cette page!</p>';
	die();
}
if(isset($_GET['promo'])) {
	$promo=$_GET['promo'];
} else {
	$promo=$current_promo;
}
if ($_POST['action'] == 'modification') {
	print '<h2>Modifier Activité</h2>';
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
	print '<h2>Nouvelle Activité</h2>';
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
	print '<h2>Supprimer Activité?</h2>';
	print '<FORM action="index.php?page=5" method="POST">
			<input type="hidden" name="id" value="'.$_GET['act'].'" />
			<input type="hidden" name="action" value="suppression" />
			<INPUT type="submit" value="Oui">
			</FORM>';
	print '<FORM action="index.php?page=5" method="POST">
			<INPUT type="submit" value="Non">
			</FORM>';

}

else if ($_POST['action'] == 'suppression_resp_confirm') {
	print '<h2>Supprimer Responsable?</h2>';
	print '<FORM action="index.php?page=5&act='.$_GET['act'].'" method="POST">
			<input type="hidden" name="id_act" value="'.$_GET['act'].'" />
			<input type="hidden" name="id_resp" value="'.$_GET['resp'].'" />
			<input type="hidden" name="action" value="suppression_resp" />
			<INPUT type="submit" value="Oui">
			</FORM>';
	print '<FORM action="index.php?page=5&act='.$_GET['act'].'" method="POST">
			<INPUT type="submit" value="Non">
			</FORM>';

}
else if ($_POST['action'] == 'suppression_sup_confirm') {
	print '<h2>Supprimer Supplément?</h2>';
	print '<FORM action="index.php?page=5&act='.$_GET['act'].'" method="POST">
			<input type="hidden" name="id_act" value="'.$_GET['act'].'" />
			<input type="hidden" name="id_sup" value="'.$_GET['sup'].'" />
			<input type="hidden" name="action" value="suppression_sup" />
			<INPUT type="submit" value="Oui">
			</FORM>';
	print '<FORM action="index.php?page=5&act='.$_GET['act'].'" method="POST">
				<INPUT type="submit" value="Non">
				</FORM>';

}
else{
	if ($_POST['action'] === 'submitted'){
		modifActivite($_POST);

	}
	if ($_POST['action'] === 'submitted_new'){
		newActivite($_POST);

	}
	if ($_POST['action'] === 'suppression'){
		delActivite($_POST['id']);
	}
	if ($_POST['action'] === 'suppression_resp'){
		delRespActivite($_POST['id_act'],$_POST['id_resp']);
	}
	if ($_POST['action'] === 'new_resp'){
		ajoutResponsableAct($_POST['id_act'],$_POST['id_resp']);
	}
	if ($_POST['action'] === 'suppression_sup'){
		delSup($_POST['id_sup']);
	}
	if ($_POST['action'] === 'new_sup'){
		//$tb,$id_tb,$type,$valeur,$id_fk,$id_asso_paie
		addSup("activite",$_POST['id_act'],$_POST['type'],$_POST['valeur'],$_POST['id_asso_adh'],$_POST['id_asso_paie'],$promo);
	}
	if(!(strcmp($_SESSION['user'],"") == 0)){
		$tab=getActivites($_SESSION['uid']);
		print '<ul id="submenu">';
		if($tot_asso > 0){
			print '<li><a class="'.(($_GET['page']==3) ? 'selected' : '').'" href="index.php?page=3">Associations</a></li>';
		}
		if($tot_sec > 0){
			print '<li><a class="'.(($_GET['page']==4) ? 'selected' : '').'" href="index.php?page=4">Sections</a></li>';
		}
		if($tot_act > 0){
			print '<li><a class="'.(($_GET['page']==5) ? 'selected' : '').'" href="index.php?page=5">Activités</a></li>';
		}
		if($tot_cre > 0){
			print '<li><a class="'.(($_GET['page']==6) ? 'selected' : '').'" href="index.php?page=6">Créneaux</a></li>';
		}
		print '</ul>';
		if(empty($_GET['act'])){

			print '<h2>Vos Activités</h2>';
			print '<ul>';

			foreach($tab as $act){
				print '<li><a href=index.php?page=5&act='.$act['id'].'>'.$act['nom'].'</a></li>';

			}
			print '</ul>';


		} else {
			print '<h2>Fiche Activité</h2>';
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
			//Liste de créneaux
		    $crens = getCreneauxByActivite($_GET['act']);
			print '<h2>Créneaux de l\'activité</h2>';
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
			//Liste de responsables
			$resps = getResponsablesAct($_GET['act']);
			print '<h3>Responsables de l\'activité</h3>';
			print '<ul>';
			foreach ($resps as $id => $adh) {
				print '<FORM action="index.php?page=5&resp='.$id.'&act='.$_GET['act'].'" method="POST">
					<input type="hidden" name="action" value="suppression_resp_confirm" />
				<li><a href=index.php?page=1&adh='.$id.'>'.$adh['prenom'].' '.$adh['nom'].'</a>
				<INPUT type="image" src="images/unchecked.gif" value="submit">
					</FORM></li>';
			}
			print '</ul>';
			print '<FORM action="index.php?page=5&act='.$_GET['act'].'" method="POST">
			<input type="hidden" name="action" value="new_resp" />
			<input type="hidden" name="id_act" value="'.$_GET['act'].'">';
			print '<label for="new_resp">Ajouter un Responsable </label><SELECT name="id_resp" class="filterselect">';
			$candidates = getAdherents();
			foreach ($candidates as $key => $value) {
				if(!isset($resps[$key])) print '<OPTION value='.$key.' >'.$value['prenom'].' '.$value['nom'].'</OPTION>';
			}
			print '<INPUT type="submit" /> ';
			print '</SELECT>';
			print '</FORM>';
			//Liste de suppléments
			$sups = getSup("activite",$_GET['act'],$promo);
			$assos = getAssos();
			print '<h3>Suppléments de l\'activité</h3>';
			print '<table><tr><th>Type</th><th>Valeur</th><th>Asso de l\'adherent</th><th>Payer à</th><th>+/-</th></tr>';
			foreach ($sups as $id => $sup) {
				print '<tr><FORM action="index.php?page=5&sup='.$id.'&act='.$_GET['act'].'" method="POST">
					<input type="hidden" name="action" value="suppression_sup_confirm" />
				<td>'.$sup['type'].'</td><td>'.$sup['valeur'].getParam('currency').'</td><td>'.$assos[$sup['id_asso_adh']].'</td><td>'.$assos[$sup['id_asso_paie']].'</td>
				<td><INPUT type="image" src="images/unchecked.gif" value="submit"></td>
					</FORM></tr>';
			}

			print '<tr><FORM action="index.php?page=5&act='.$_GET['act'].'" method="POST">
			<input type="hidden" name="action" value="new_sup" />
			<input type="hidden" name="id_act" value="'.$_GET['act'].'">
			<td><INPUT type="text" name="type"></INPUT></td>
			<td><INPUT type="text" name="valeur"></INPUT></td>
			<td><SELECT name="id_asso_adh">';
			foreach ($assos as $key => $value) {
				print '<OPTION value="'.$key.'">'.$value.'</OPTION>';
			}
			print '</SELECT></td>';
			print '<td><SELECT name="id_asso_paie">';
			foreach ($assos as $key => $value) {
				print '<OPTION value="'.$key.'">'.$value.'</OPTION>';
			}
			print '</SELECT></td>
			<td><INPUT type="image" src="images/checked.gif" value="submit"></td>
			';
			print '</FORM>';
			print '</table>';
		}

	}
	else {
		print "<p>Vous n'êtes pas connecté</p>";
	}
}



?>
