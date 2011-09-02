<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
session_start();


function selected($post,$val,$tab){
	if ($tab[$_GET['creneau']][$post]===$val) {
		return "selected";
	}
	else return "";
}
if(isset($_GET['promo'])) {
	$promo=$_GET['promo'];
} else {
	$promo=$current_promo;
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
else if ($_POST['action'] == 'suppression_resp_confirm') {
	print '<h2>Supprimer Responsable?</h2>';
	print '<FORM action="index.php?page=6&creneau='.$_GET['creneau'].'" method="POST">
			<input type="hidden" name="id_cre" value="'.$_GET['creneau'].'" />
			<input type="hidden" name="id_resp" value="'.$_GET['resp'].'" />
			<input type="hidden" name="action" value="suppression_resp" />
			<INPUT type="submit" value="Oui">
			</FORM>';
	print '<FORM action="index.php?page=6&creneau='.$_GET['creneau'].'" method="POST">
			<INPUT type="submit" value="Non">
			</FORM>';

}
else if ($_POST['action'] == 'suppression_sup_confirm') {
	print '<h2>Supprimer Supplément?</h2>';
	print '<FORM action="index.php?page=6&creneau='.$_GET['creneau'].'" method="POST">
			<input type="hidden" name="id_cre" value="'.$_GET['creneau'].'" />
			<input type="hidden" name="id_sup" value="'.$_GET['sup'].'" />
			<input type="hidden" name="action" value="suppression_sup" />
			<INPUT type="submit" value="Oui">
			</FORM>';
	print '<FORM action="index.php?page=6&creneau='.$_GET['creneau'].'" method="POST">
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
	if ($_POST['action'] === 'suppression_resp'){
		delRespCre($_POST['id_cre'],$_POST['id_resp']);
	}
	if ($_POST['action'] === 'new_resp'){
		ajoutResponsableCre($_POST['id_cre'],$_POST['id_resp']);
	}
	if ($_POST['action'] === 'suppression_sup'){
		delSup($_POST['id_sup']);
	}
	if ($_POST['action'] === 'new_sup'){
		//$tb,$id_tb,$type,$valeur,$id_fk,$id_asso_paie
		addSup("creneau",$_POST['id_cre'],$_POST['type'],$_POST['valeur'],$_POST['id_asso_adh'],$_POST['id_asso_paie'],$promo);
	}
	if ($_POST['action'] === 'copy_old_sups'){
		$sups = getSup("creneau",$_GET['creneau'],$_POST['old_promo']);
		foreach ($sups as $key => $value) {
			//print "add sup: idasso={$_GET['asso']} type={$value['type']} valeur={$value['valeur']} id_statut={$value['id_statut']} id_asso_paie={$value['id_asso_paie']} promo=$current_promo";
			addSup("creneau",$_GET['creneau'],$value['type'],$value['valeur'],$value['id_asso_adh'],$value['id_asso_paie'],$promo);
		}
	}
	if(!(strcmp($_SESSION['user'],"") == 0)){
		$tab=getCreneaux($_SESSION['uid']);
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
		if(empty($_GET['creneau'])){

			print '<h2>Vos Créneaux</h2>';
			print '<ul>';

			foreach($tab as $creneau){
				print '<li><a href=index.php?page=6&creneau='.$creneau['id_cre'].'>'.$creneau['nom_sec'].' - '.$creneau['nom_act'].' - '.$creneau['jour_cre'].' - '.$creneau['debut_cre'].' - '.$creneau['fin_cre'].'</a></li>';
			}
			print '</ul>';

		} else {
			print '<h2>Fiche Créneau</h2>';
			print "<div class=\"tip\">".getParam('text_creneau')."</div>";
			print '<table>';
			print '<tr><td class="label">Activité : </td><td>'.$tab[$_GET['creneau']]['nom_sec']." - ".$tab[$_GET['creneau']]['nom_act'].'</td></tr>';
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
			//Liste de responsables
			$resps = getResponsablesCre($_GET['creneau']);
			print '<h3>Responsables du créneau</h3>';
			print '<ul>';
			foreach ($resps as $id => $adh) {
				print '<FORM action="index.php?page=6&resp='.$id.'&creneau='.$_GET['creneau'].'" method="POST">
					<input type="hidden" name="action" value="suppression_resp_confirm" />
				<li><a href=index.php?page=1&adh='.$id.'>'.$adh['prenom'].' '.$adh['nom'].'</a>
				<INPUT type="image" src="images/unchecked.gif" value="submit">
					</FORM></li>';
			}
			print '</ul>';
			print '<FORM action="index.php?page=6&creneau='.$_GET['creneau'].'" method="POST">
			<input type="hidden" name="action" value="new_resp" />
			<input type="hidden" name="id_cre" value="'.$_GET['creneau'].'">';
			print '<label for="new_resp">Ajouter un Responsable </label><SELECT name="id_resp" class="filterselect">';
			$candidates = getAdherents();
			foreach ($candidates as $key => $value) {
				if(!isset($resps[$key])) print '<OPTION value='.$key.' >'.$value['prenom'].' '.$value['nom'].'</OPTION>';
			}
			print '<INPUT type="submit" /> ';
			print '</SELECT>';
			print '</FORM>';
			//Liste de suppléments
			$sups = getSup("creneau",$_GET['creneau'],$promo);
			$assos = getAssos();
			print '<h3>Suppléments du créneau</h3>';
			//Selection Promo
			print "<p>Promo:<SELECT id=\"promo\" >";
			print "<OPTION value=$current_promo ".($_GET['promo']==$current_promo ? "selected" : "")." >$current_promo</OPTION>";
			for ($i=1; $i<=10; $i++ ){
				$p=$current_promo-$i;
				print "<OPTION value=\"$p\" ".($_GET['promo']==$p ? "selected" : "")." >$p</OPTION>";
			}
			print "</SELECT></p>";
			print '<table><tr><th>Type</th><th>Valeur</th><th>Asso de l\'adherent</th><th>Payer à</th>';
			if($promo==$current_promo) print '<th>+/-</th>';
			print '</tr>';
			foreach ($sups as $id => $sup) {
				print '<tr>
				<td>'.$sup['type'].'</td><td>'.$sup['valeur'].getParam('currency').'</td><td>'.$assos[$sup['id_asso_adh']].'</td><td>'.$assos[$sup['id_asso_paie']].'</td>';
				if($promo==$current_promo) print '<td><FORM action="index.php?page=6&sup='.$id.'&creneau='.$_GET['creneau'].'" method="POST">
					<input type="hidden" name="action" value="suppression_sup_confirm" />
					<INPUT type="image" src="images/unchecked.gif" value="submit"></FORM></td>';
				print '</tr>';
			}

			if($promo==$current_promo){ 
				print '<tr><FORM action="index.php?page=6&creneau='.$_GET['creneau'].'" method="POST">
				<input type="hidden" name="action" value="new_sup" />
				<input type="hidden" name="id_cre" value="'.$_GET['creneau'].'">
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
			}		else {
							print '<td colspan=4>
							<FORM action="index.php?page=6&creneau='.$_GET['creneau'].'" method="POST">
							<input type="hidden" name="old_promo" value="'.$_GET['promo'].'" >
							<input type="hidden" name="action" value="copy_old_sups" >
							<INPUT type="submit" class="confirm" value="Recopier ces suppléments dans la promo courante" >
							</FORM></td>';
						}
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

$('#promo').change( function (){
	window.location.search = "page=6&creneau="+$.getUrlVar('creneau')+"&promo="+$(this).val();
});
</script>