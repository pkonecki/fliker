<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
$tot_asso=count(getAssociations($_SESSION['uid']));
$tot_sec=count(getSections($_SESSION['uid']));
$tot_act=count(getActivites($_SESSION['uid']));
$tot_cre=count(getCreneaux($_SESSION['uid']));
$tot = $tot_asso + $tot_sec + $tot_act + $tot_cre;
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
	print '<p>Vous n\'avez pas acc�s � cette page!</p>';
	die();
}
if (isset($_POST['action']) && $_POST['action'] == 'modification') {
	print '<h2>Modifier Cr�neau</h2>';
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
if (isset($_POST['action']) && $_POST['action'] == 'new') {
	print '<h2>Nouveau Cr�neau</h2>';
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

}
else
{
	if (isset($_POST['action']) && $_POST['action'] === 'submitted')
		modifcreneau($_POST);
	if (isset($_POST['action']) && $_POST['action'] === 'submitted_new')
		newcreneau($_POST);
	if (isset($_POST['action']) && $_POST['action'] === 'suppression')
	{
		delcreneau($_GET['creneau']);
		header("Location: index.php?page=6");
	}
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_resp')
		delRespCre($_GET['creneau'],$_GET['resp']);
	if (isset($_POST['action']) && $_POST['action'] === 'new_resp')
		ajoutResponsableCre($_POST['id_cre'],$_POST['id_resp']);
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_sup')
		delSup($_GET['sup']);
	if (isset($_POST['action']) && $_POST['action'] === 'new_sup')
		addSup("creneau",$_POST['id_cre'],$_POST['type'],$_POST['valeur'],$_POST['id_asso_adh'],$_POST['id_asso_paie'],$promo);
	if (isset($_POST['action']) && $_POST['action'] === 'copy_old_sups')
	{
		$sups = getSup("creneau",$_GET['creneau'],$_POST['old_promo']);
		foreach ($sups as $key => $value)
			addSup("creneau",$_GET['creneau'],$value['type'],$value['valeur'],$value['id_asso_adh'],$value['id_asso_paie'],$promo);
	}
	if(!(strcmp($_SESSION['user'],"") == 0))
	{
		$tab=getCreneaux($_SESSION['uid']);
		print '<ul id="submenu">';
		if($tot_asso > 0){
			print '<li><a class="'.(($_GET['page']==3) ? 'selected' : '').'" href="index.php?page=3">Associations</a></li>';
		}
		if($tot_sec > 0){
			print '<li><a class="'.(($_GET['page']==4) ? 'selected' : '').'" href="index.php?page=4">Sections</a></li>';
		}
		if($tot_act > 0){
			print '<li><a class="'.(($_GET['page']==5) ? 'selected' : '').'" href="index.php?page=5">Activit�s</a></li>';
		}
		if($tot_cre > 0){
			print '<li><a class="'.(($_GET['page']==6) ? 'selected' : '').'" href="index.php?page=6">Cr�neaux</a></li>';
		}
		if(isset($tot_asso) && $tot_asso > 0)
			print '<li><a class="'.(($_GET['page']==12) ? 'selected' : '').'" href="index.php?page=12">Utilisateurs</a></li>';
		print '</ul>';
		if(empty($_GET['creneau'])){

			print '<h2>Vos Cr�neaux</h2>';
			print '<ul>';

			foreach($tab as $creneau){
				print '<li><a href=index.php?page=6&creneau='.$creneau['id_cre'].'>'.$creneau['nom_sec'].' - '.$creneau['nom_act'].' - '.$creneau['jour_cre'].' - '.$creneau['debut_cre'].' - '.$creneau['fin_cre'].'</a></li>';
			}
			print '</ul>';

		} else {
			print '<h2>Fiche Cr�neau</h2>';
			print "<div class=\"tip\">".getParam('text_creneau.txt')."</div>";
			print '<table>';
			print '<tr><td class="label">Activit� : </td><td>'.$tab[$_GET['creneau']]['nom_sec']." - ".$tab[$_GET['creneau']]['nom_act'].'</td></tr>';
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
			print '<h3>Responsables du cr�neau</h3>';
			print '<ul>';
			foreach ($resps as $id => $adh) {
				print '<FORM action="index.php?page=6&resp='.$id.'&creneau='.$_GET['creneau'].'" method="POST">
					<input type="hidden" name="action" value="suppression_resp" />
				<li><a href=index.php?page=1&adh='.$id.'>'.$adh['prenom'].' '.$adh['nom'].'</a>
				<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit">
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
			//Liste de suppl�ments
			$sups = getSup("creneau",$_GET['creneau'],$promo);
			$assos = getAssos();
			print '<h3>Suppl�ments du cr�neau</h3>';
		
			//Selection Promo
			$res = doQuery("SELECT DISTINCT promo FROM {$GLOBALS['prefix_db']}sup WHERE id IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent=".$_GET['creneau'].") ORDER BY promo DESC");
			print "<p>Promo:<SELECT id=\"promo\" >";
			if (!$res || mysql_num_rows($res) <= 0)
				print "<OPTION value='$promo' 'selected' >$promo</OPTION>";
			while ($tmp_array_promo = mysql_fetch_array($res))
				print "<OPTION value='".$tmp_array_promo['promo']."' ".(isset($_GET['promo']) && $_GET['promo'] == $tmp_array_promo['promo'] ? "selected" : "")." >".$tmp_array_promo['promo']."</OPTION>";
			print "</SELECT></p>";
			
			print '<table><tr><th>Type</th><th>Valeur</th><th>Asso de l\'adherent</th><th>Payer �</th>';
			if($promo==$current_promo) print '<th>+/-</th>';
			print '</tr>';
			foreach ($sups as $id => $sup) {
				print '<tr>
				<td>'.$sup['type'].'</td><td>'.$sup['valeur'].getParam('currency.conf').'</td><td>'.$assos[$sup['id_asso_adh']].'</td><td>'.$assos[$sup['id_asso_paie']].'</td>';
				if($promo==$current_promo) print '<td><FORM action="index.php?page=6&sup='.$id.'&creneau='.$_GET['creneau'].'" method="POST">
					<input type="hidden" name="action" value="suppression_sup" />
					<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit"></FORM></td>';
				print '</tr>';
			}

			if($promo==$current_promo){ 
				print '<tr><FORM action="index.php?page=6&creneau='.$_GET['creneau'].'" method="POST">
				<input type="hidden" name="action" value="new_sup" />
				<input type="hidden" name="id_cre" value="'.$_GET['creneau'].'">
				<td><select name="type">';
				$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}type_supl ORDER BY nom ASC");
				while ($tmp_array = mysql_fetch_array($res))
					print "<option value='".$tmp_array['nom']."'>".$tmp_array['nom']."</option>";
				print '</select></td>
				<td><INPUT type="text" name="valeur"></INPUT></td>
				<td><SELECT name="id_asso_adh">';
				foreach ($assos as $key => $value)
					print '<OPTION value="'.$key.'">'.$value.'</OPTION>';
				print '</SELECT></td>';
				print '<td><SELECT name="id_asso_paie">';
				foreach ($assos as $key => $value) {
					print '<OPTION value="'.$key.'">'.$value.'</OPTION>';
				}
				print '</SELECT></td>
				<td><INPUT type="image" width="14" height="14" src="images/icone_add.png" value="submit"></td>
				';
				print '</FORM>';
			}		else {
							print '<td colspan=4>
							<FORM action="index.php?page=6&creneau='.$_GET['creneau'].'" method="POST">
							<input type="hidden" name="old_promo" value="'.$_GET['promo'].'" >
							<input type="hidden" name="action" value="copy_old_sups" >
							<INPUT type="submit" class="confirm" value="Recopier ces suppl�ments dans la promo courante" >
							</FORM></td>';
						}
			print '</table>';
		}

	}
	else {
		print "<p>Vous n'�tes pas connect�</p>";
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