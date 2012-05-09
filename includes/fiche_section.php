<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
/*session_start();*/
getAdherent($_SESSION['user']);
$tab=getSections($_SESSION['uid']);
if(isset($_GET['section']) && !isset($tab[$_GET['section']])){
	print '<p>Vous n\'avez pas accès à cette page!</p>';
	die();
}
if(isset($_GET['promo'])) {
	$promo=$_GET['promo'];
} else {
	$promo=$current_promo;
}
if (isset($_POST['action']) && $_POST['action'] == 'modification') {
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
if (isset($_POST['action']) && $_POST['action'] == 'new') {
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
if (isset($_POST['action']) && $_POST['action'] == 'suppression_confirm') {
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
else if (isset($_POST['action']) && $_POST['action'] == 'suppression_resp_confirm') {
	print '<h2>Supprimer Responsable?</h2>';
	print '<FORM action="index.php?page=4&section='.$_GET['section'].'" method="POST">
			<input type="hidden" name="id_sec" value="'.$_GET['section'].'" />
			<input type="hidden" name="id_resp" value="'.$_GET['resp'].'" />
			<input type="hidden" name="action" value="suppression_resp" />
			<INPUT type="submit" value="Oui">
			</FORM>';
	print '<FORM action="index.php?page=4&section='.$_GET['section'].'" method="POST">
			<INPUT type="submit" value="Non">
			</FORM>';

}
else if (isset($_POST['action']) && $_POST['action'] == 'suppression_sup_confirm') {
	print '<h2>Supprimer Supplément?</h2>';
	print '<FORM action="index.php?page=4&section='.$_GET['section'].'" method="POST">
			<input type="hidden" name="id_sec" value="'.$_GET['section'].'" />
			<input type="hidden" name="id_sup" value="'.$_GET['sup'].'" />
			<input type="hidden" name="action" value="suppression_sup" />
			<INPUT type="submit" value="Oui">
			</FORM>';
	print '<FORM action="index.php?page=4&section='.$_GET['section'].'" method="POST">
				<INPUT type="submit" value="Non">
				</FORM>';

}
else {
	if (isset($_POST['action']) && $_POST['action'] === 'submitted'){
		modifSection($_POST);

	}
	if (isset($_POST['action']) && $_POST['action'] === 'submitted_new'){
		newSection($_POST);

	}
	if (isset($_POST['action']) && $_POST['action'] === 'suppression'){
		delSection($_POST['id']);
	}
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_resp'){
		delRespSec($_POST['id_sec'],$_POST['id_resp']);
	}
	if (isset($_POST['action']) && $_POST['action'] === 'new_resp'){
		ajoutResponsableSec($_POST['id_sec'],$_POST['id_resp']);
	}
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_sup'){
		delSup($_POST['id_sup']);
	}
	if (isset($_POST['action']) && $_POST['action'] === 'new_sup'){
		//$tb,$id_tb,$type,$valeur,$id_fk,$id_asso_paie
		addSup("section",$_POST['id_sec'],$_POST['type'],$_POST['valeur'],$_POST['id_asso_adh'],$_POST['id_asso_paie'],$promo);
	}
	if (isset($_POST['action']) && $_POST['action'] === 'copy_old_sups'){
		$sups = getSup("section",$_GET['section'],$_POST['old_promo']);
		foreach ($sups as $key => $value) {
			//print "add sup: idasso={$_GET['asso']} type={$value['type']} valeur={$value['valeur']} id_statut={$value['id_statut']} id_asso_paie={$value['id_asso_paie']} promo=$current_promo";
			addSup("section",$_GET['section'],$value['type'],$value['valeur'],$value['id_asso_adh'],$value['id_asso_paie'],$promo);
		}

	}
	if(!(strcmp($_SESSION['user'],"") == 0)){
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
		$tab=getSections($_SESSION['uid']);
		if(empty($_GET['section'])){

			print '<h2>Vos sections</h2>';
			print '<ul>';
			foreach($tab as $section){
				print '<li><a href=index.php?page=4&section='.$section['id'].'>'.$section['nom'].'</a></li>';
			}
			print '</ul>';


		} else {
			print '<h2>Fiche Section</h2>';
			print "<div class=\"tip\">".getParam('text_section')."</div>";
			print '<table>';
			print '<tr><td class="label">Nom : </td><td>'.$tab[$_GET['section']]['nom'].'</td></tr>';
			print '<tr><td class="label">Description : </td><td>'.$tab[$_GET['section']]['description'].'</td></tr>';
			print '<tr><td class="label">Url : </td><td>'.$tab[$_GET['section']]['url'].'</td></tr>';
			$_SESSION['auth_thumb']='true';
			$photo="includes/thumb.php?folder=logo_section&file=".$_GET['section'].".jpg";
			if (isset($row['description']))
				$tmp_stock = $row['description'];
			else
				$tmp_stock = "";
			print '<tr><TD>'.$tmp_stock.'</TD><TD><img src="'.$photo.'" ></TD></tr>';
			print '<tr>';
			print '<td colspan=2><FORM action="index.php?page=4&section='.$_GET['section'].'" method="POST">
					<input type="hidden" name="action" value="modification" />
					<INPUT type="submit" value="Modifier">
					</FORM></td>';

			print '</tr>';
			print '</table>';
			//Liste d'activités
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
			//Liste responsables
			$resps = getResponsablesSec($_GET['section']);
			print '<h3>Responsables de la section</h3>';
			print '<ul>';
			foreach ($resps as $id => $adh) {
				print '<FORM action="index.php?page=4&resp='.$id.'&section='.$_GET['section'].'" method="POST">
					<input type="hidden" name="action" value="suppression_resp_confirm" />
				<li><a href=index.php?page=1&adh='.$id.'>'.$adh['prenom'].' '.$adh['nom'].'</a>
				<INPUT type="image" src="images/unchecked.gif" value="submit">
					</FORM></li>';
			}
			print '</ul>';
			print '<FORM action="index.php?page=4&section='.$_GET['section'].'" method="POST">
			<input type="hidden" name="action" value="new_resp" />
			<input type="hidden" name="id_sec" value="'.$_GET['section'].'">';
			print '<label for="new_resp">Ajouter un Responsable </label><SELECT name="id_resp" class="filterselect" >';
			$candidates = getAdherents();
			foreach ($candidates as $key => $value) {
				if(!isset($resps[$key])) print '<OPTION value='.$key.' >'.$value['prenom'].' '.$value['nom'].'</OPTION>';
			}
			print '<INPUT type="submit" /> ';
			print '</SELECT>';
			print '</FORM>';
			//Liste de suppléments
			$sups = getSup("section",$_GET['section'],$promo);
			$assos = getAssos();
			//Selection Promo
			print "<p>Promo:<SELECT id=\"promo\" >";
			print "<OPTION value=$current_promo ".($_GET['promo']==$current_promo ? "selected" : "")." >$current_promo</OPTION>";
			for ($i=1; $i<=10; $i++ ){
				$p=$current_promo-$i;
				print "<OPTION value=\"$p\" ".($_GET['promo']==$p ? "selected" : "")." >$p</OPTION>";
			}
			print "</SELECT></p>";
			print '<h3>Suppléments de la section</h3>';
			print '<table><tr><th>Type</th><th>Valeur</th><th>Asso de l\'adherent</th><th>Payer à</th>';
			if($promo==$current_promo) print '<th>+/-</th>';
			print '</tr>';
			foreach ($sups as $id => $sup) {
				print '<tr>
				<td>'.$sup['type'].'</td><td>'.$sup['valeur'].getParam('currency').'</td><td>'.$assos[$sup['id_asso_adh']].'</td><td>'.$assos[$sup['id_asso_paie']].'</td>';
				
				if($promo==$current_promo) print '<td><FORM action="index.php?page=4&sup='.$id.'&section='.$_GET['section'].'" method="POST">
					<input type="hidden" name="action" value="suppression_sup_confirm" />
					<INPUT type="image" src="images/unchecked.gif" value="submit"></FORM></td>
				';
				print '</tr>';
			}

			if($promo==$current_promo) {
				print '<tr><FORM action="index.php?page=4&section='.$_GET['section'].'" method="POST">
				<input type="hidden" name="action" value="new_sup" />
				<input type="hidden" name="id_sec" value="'.$_GET['section'].'">
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
			}	else {
					print '<td colspan=4>
					<FORM action="index.php?page=4&section='.$_GET['section'].'" method="POST">
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
$('#promo').change( function (){
	window.location.search = "page=4&section="+$.getUrlVar('section')+"&promo="+$(this).val();
});
</script>