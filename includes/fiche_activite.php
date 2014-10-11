<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
$tot_asso=count(getAssociations($_SESSION['uid']));
$tot_sec=count(getSections($_SESSION['uid']));
$tot_act=count(getActivites($_SESSION['uid']));
$tot_cre=count(getCreneaux($_SESSION['uid']));
$tot = $tot_asso + $tot_sec + $tot_act + $tot_cre;
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
if (isset($_POST['action']) && $_POST['action'] == 'modification') {
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
if (isset($_POST['action']) && $_POST['action'] == 'new') {
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

}
elseif(isset($_POST['action']) && $_POST['action'] == 'modif_sup'){
	$res_sup = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}sup WHERE id=".$_GET['sup']." ");
	$data_sup = mysql_fetch_assoc($res_sup);
	$assos = getAssos();
	print '<h2>Modifier Supplément</h2>
	<table>
	<tr><th>Type</th><th>Valeur</th><th>Asso de l\'adherent</th><th>Payer à</th><th>Facultatif</th><th>Modif</th></tr>
	<tr><FORM action="index.php?page=5&act='.$_GET['act'].'" method="POST">
	<input type="hidden" name="action" value="submitted_modif_sup" />
	<input type="hidden" name="entite" value="activite" />
	<input type="hidden" name="id_sup" value="'.$_GET['sup'].'" />
	<td><select name="type">';
	$res_type_sup = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}type_supl ORDER BY nom ASC");
	while ($data_type_sup = mysql_fetch_array($res_type_sup))
		print "<option value='".$data_type_sup['nom']."' ".($data_sup['type']==$data_type_sup['nom']?"selected":"").">".$data_type_sup['nom']."</option>";
	print '</select></td>
	<td><INPUT type="text" name="valeur" value="'.$data_sup['valeur'].'"></INPUT></td>
	<td><SELECT name="id_asso_adh">';
	foreach ($assos as $key => $value)
		print '<OPTION value="'.$key.'" '.($data_sup['id_asso_adh']==$key?"selected":"").'>'.$value.'</OPTION>';
	print '</SELECT></td>';
	print '<td><SELECT name="id_asso_paie">';
	foreach ($assos as $key => $value) {
		print '<OPTION value="'.$key.'" '.($data_sup['id_asso_paie']==$key?"selected":"").'>'.$value.'</OPTION>';
	}
	print '</SELECT></td>
	<td><INPUT type="checkbox" name="facultatif" value="1" '.($data_sup['facultatif']==1?"checked":"").'></td>
	<td><INPUT type="image" width="20" height="20" src="images/Valid.png" value="submit"></td>
	</FORM></table>';
}
else{
	if (isset($_POST['action']) && $_POST['action'] === 'submitted')
		modifActivite($_POST);
	if (isset($_POST['action']) && $_POST['action'] === 'submitted_new')
		newActivite($_POST);
	if (isset($_POST['action']) && $_POST['action'] === 'suppression')
	{
		delActivite($_GET['act']);
		header("Location: index.php?page=5");
	}
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_resp')
		delRespActivite($_GET['act'],$_GET['resp'],$promo);
	if (isset($_POST['action']) && $_POST['action'] === 'new_resp')
		ajoutResponsableAct($_POST['id_act'],$_POST['id_resp'],$promo);
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_sup')
		delSup($_GET['sup']);
	if (isset($_POST['action']) && $_POST['action'] === 'submitted_modif_sup')
		modifSup($_POST);
	if (isset($_POST['action']) && $_POST['action'] === 'new_sup')
		addSup("activite",$_POST['id_act'],$_POST['type'],$_POST['valeur'],$_POST['id_asso_adh'],$_POST['id_asso_paie'],$_POST['facultatif'],$promo);
	if (isset($_POST['action']) && $_POST['action'] === 'copy_old_sups')
	{
		$sups = getSup("activite",$_GET['act'],$_POST['old_promo']);
		foreach ($sups as $key => $value)
			addSup("activite",$_GET['act'],$value['type'],$value['valeur'],$value['id_asso_adh'],$value['id_asso_paie'],$value['facultatif'],$promo);
	}
	if(!(strcmp($_SESSION['user'],"") == 0)){
		$tab=getActivites($_SESSION['uid']);

		if(empty($_GET['act'])){

			print '<h2>Vos Activités</h2>';
			print '<ul>';
			$non_actif="";
			foreach($tab as $act){
				$verif=0;
				$query = doQuery ("SELECT * FROM {$GLOBALS['prefix_db']}resp_act WHERE id_act = ".$act['id']." AND promo = ".$current_promo." ");
				$verif = mysql_num_rows($query);
				if ($verif != 0){
				print '<li><a href=index.php?page=5&act='.$act['id'].'>'.$act['nom_sec'].' - '.$act['nom'].'</a></li>';
				}
				else{
				$non_actif .= '<li><a href=index.php?page=5&act='.$act['id'].'>'.$act['nom_sec'].' - '.$act['nom'].'</a></li>';
				}
			}
			print '</ul>';
			print '<br />
			<h2>Activités SANS Responsables</h2>
			<ul>
			'.$non_actif.'
			</ul>
			';
		}
		else
		{
			print '<h2>Fiche Activité</h2>';
			print "<div class=\"tip\">".getParam('text_activite.conf')."</div>";
			print '<table>';
			print '<tr><td class="label">Nom : </td><td>'.$tab[$_GET['act']]['nom_sec'].' - '.$tab[$_GET['act']]['nom'].'</td></tr>';
			print '<tr><td class="label">Description : </td><td>'.$tab[$_GET['act']]['description'].'</td></tr>';
			print '<tr><td class="label">Url : </td><td>'.$tab[$_GET['act']]['url'].'</td></tr>';
			$_SESSION['auth_thumb']='true';
			$photo="includes/thumb.php?folder=logo_act&file=".$_GET['act'].".jpg";
			if (isset($row['description']))
				$tmp_stock = $row['description'];
			else
				$tmp_stock = "";
			print '<tr><TD>'.$tmp_stock.'</TD><TD><img src="'.$photo.'" ></TD></tr>';
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
				<input type="hidden" name="action" value="suppression" />
				<li><a href=index.php?page=6&creneau='.$creneau['id'].'>'.$creneau['jour'].' - '.$creneau['debut'].' - '.$creneau['fin'].'</a>';
				if($_SESSION['privilege'] == 1)
					print '<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit">';
				print '</FORM></li>';

			}
			print '</ul>';
			print '<td colspan=2><FORM action="index.php?page=6" method="POST">
			<input type="hidden" name="action" value="new" />
			<input type="hidden" name="id_act" value="'.$_GET['act'].'">
			<INPUT type="submit" value="Nouveau">
			</FORM></td>';
			
			//Selection Promo
			// $res = doQuery("SELECT DISTINCT promo FROM {$GLOBALS['prefix_db']}sup WHERE id IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent=".$_GET['act'].") ORDER BY promo DESC");
			$res = doQuery("SELECT DISTINCT promo FROM {$GLOBALS['prefix_db']}adhesion ORDER BY promo DESC");
			print "<p>Promo:<SELECT id=\"promo\" >";
			if (!$res || mysql_num_rows($res) <= 0)
				print "<OPTION value='$promo' 'selected' >$promo</OPTION>";
			while ($tmp_array_promo = mysql_fetch_array($res))
				print "<OPTION value='".$tmp_array_promo['promo']."' ".(isset($_GET['promo']) && $_GET['promo'] == $tmp_array_promo['promo'] ? "selected" : "")." >".$tmp_array_promo['promo']."</OPTION>";
			print "</SELECT></p>";
			
			//Liste de responsables
			$resps = getResponsablesAct($_GET['act'],$promo);
			print '<h3>Responsables de l\'activité</h3>';
			print '<ul>';
			if(empty($resps)){print 'Aucun Responsable';}
			foreach ($resps as $id => $adh) {
				print '<FORM action="index.php?page=5&resp='.$id.'&act='.$_GET['act'].'&promo='.$promo.'" method="POST">
					<input type="hidden" name="action" value="suppression_resp" />
				<li><a href=index.php?page=1&adh='.$id.'>'.$adh['prenom'].' '.$adh['nom'].'</a>
				<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit">
					</FORM></li>';
			}
			print '</ul>';
			print '<FORM action="index.php?page=5&act='.$_GET['act'].'&promo='.$promo.'" method="POST">
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
			print '<table><tr><th>Type</th><th>Valeur</th><th>Asso de l\'adherent</th><th>Payer à</th><th>Facultatif</th>';
			print '</tr>';
			foreach ($sups as $id => $sup) {
				print '<tr>
				<td>'.$sup['type'].'</td><td>'.$sup['valeur'].getParam('currency.conf').'</td><td>'.$assos[$sup['id_asso_adh']].'</td><td>'.$assos[$sup['id_asso_paie']].'</td><td>'.($sup['facultatif']==1 ? "oui" : "non").'</td>';
			if($promo==$current_promo) 	print '
				<td><FORM action="index.php?page=5&sup='.$id.'&act='.$_GET['act'].'" method="POST">
				<input type="hidden" name="action" value="modif_sup" />
				<INPUT type="image" src="images/icone_edit.png" width="14" value="submit"></FORM></td>
				
				<td><FORM action="index.php?page=5&sup='.$id.'&act='.$_GET['act'].'" method="POST">
				<input type="hidden" name="action" value="suppression_sup" />
				<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit"></FORM></td>
				</tr>';
			}

			if($promo==$current_promo) {
				print '<tr><FORM action="index.php?page=5&act='.$_GET['act'].'" method="POST">
				<input type="hidden" name="action" value="new_sup" />
				<input type="hidden" name="id_act" value="'.$_GET['act'].'">
				<td><select name="type">';
				$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}type_supl ORDER BY nom ASC");
				while ($tmp_array = mysql_fetch_array($res))
					print "<option value='".$tmp_array['nom']."'>".$tmp_array['nom']."</option>";
				print '</select></td>
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
				<td><INPUT type="checkbox" name="facultatif" value="1"></td><td></td>
				<td><INPUT type="image" width="14" height="14" src="images/icone_add.png" value="submit"></td>
				';
				print '</FORM>';
			}	else {
						print '<td colspan=4>
						<FORM action="index.php?page=5&act='.$_GET['act'].'" method="POST">
						<input type="hidden" name="old_promo" value="'.$_GET['promo'].'" >
						<input type="hidden" name="action" value="copy_old_sups" >
						<INPUT type="submit" class="confirm" value="Recopier ces suppléments dans la promo courante" >
						</FORM></td>';
					}
			print '</table>';
		}

	}
	else {
		print "<p>Vous n'êtes pas connectéhummm</p>";
	}
}
?>
<script type="text/javascript">
$('#promo').change( function (){
	window.location.search = "page=5&act="+$.getUrlVar('act')+"&promo="+$(this).val();
});
</script>