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
	print '<p>Vous n\'avez pas accès à cette page!</p>';
	die();
}
if (isset($_POST['action']) && $_POST['action'] == 'modification') {

	$res = doQuery("SELECT BAT.nom AS batiment, SALLE.nom AS salle, SALLE.id AS id
	FROM {$GLOBALS['prefix_db']}batiment BAT, {$GLOBALS['prefix_db']}salle SALLE
	WHERE SALLE.id_batiment=BAT.id
	ORDER BY BAT.nom, SALLE.nom");
	while($data = mysql_fetch_assoc($res))
		$all_lieux .= '<option value="'.$data['id'].'" '.($data['id']==$tab[$_GET['creneau']]['lieu']?"selected":"").'>'.$data['batiment'].' '.$data['salle'].'</option>
		';
	
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
	print '<tr><td class="label"><LABEL for ="lieu" >Lieu</LABEL> : </td><td><select name="lieu">'.$all_lieux.'</select></td></tr>';
	print '<input type="hidden" name="action" value="submitted" />';
	print '<input type="hidden" name="id_cre" value="'.$tab[$_GET['creneau']]['id_cre'].'" />';
	print '<tr><td colspan="2"><INPUT type="submit" value="Envoyer" ></td></tr>';
	print '</table>';
	print '</FORM>';

} else
if (isset($_POST['action']) && $_POST['action'] == 'new') {
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
	print '<tr><td class="label"><LABEL for ="lieu" >Lieu</LABEL> : </td><td><select name="lieu">'.getAllLieux().'</select></td></tr>';
	print '<input type="hidden" name="action" value="submitted_new" />';
	print '<tr><td colspan="2"><INPUT type="submit" value="Envoyer"></td></tr>';
	print '<input type="hidden" name="id_act" value="'.$_POST['id_act'].'">';
	print '</table>';
	print '</FORM>';

}
elseif(isset($_POST['action']) && $_POST['action'] == 'modif_sup'){
	$res_sup = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}sup WHERE id=".$_GET['sup']." ");
	$data_sup = mysql_fetch_assoc($res_sup);
	$assos = getAssos();
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}reductions_sup WHERE id_sup = ".$_GET['sup']." ");
		while ($tmp_array = mysql_fetch_array($res))
			$reductions[$tmp_array['id_reduc']] = 1;
	print '<h2>Modifier Supplément</h2>
	<table>
	<tr><th>Type</th><th>Valeur</th><th>Asso de l\'adherent</th><th>Payer à</th><th>Facultatif</th>';
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}reductions ORDER BY nom ASC");
	while ($tmp_array = mysql_fetch_array($res))
	{
		print '<th>'.$tmp_array['nom'].'</th>';
		$liste_reductions[$tmp_array['id']] = 1;
	}
	print '<th>Modif</th></tr>
	<tr><FORM action="index.php?page=6&creneau='.$_GET['creneau'].'" method="POST">
	<input type="hidden" name="action" value="submitted_modif_sup" />
	<input type="hidden" name="entite" value="creneau" />
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
	<td><INPUT type="checkbox" name="facultatif" value="1" '.($data_sup['facultatif']==1?"checked":"").'></td>';
	foreach ($liste_reductions as $id_reduc => $value)
		print '<td bgcolor="#CCFFCC"><INPUT type="checkbox" name="reduction['.$id_reduc.']" value="1" '.($reductions[$id_reduc]==1?"checked":"").'></td>';
	print '<td><INPUT type="image" width="20" height="20" src="images/Valid.png" value="submit"></td>
	</FORM></table>';
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
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_resp'){
		delRespCre($_GET['creneau'],$_GET['resp'],$promo);
		if (getParam('allow_mail.conf') == true && getParam('modif_rights.notif') == "now")
			{
				$to      = "webmaster.sport@u-psud.fr";
				$subject = "[".getParam('text_top.txt')."] Suppression Responsable Créneau";
				$message = "Bonjour,\r\n \r\n l'utilisateur ".$_SESSION['prenom']." ".$_SESSION['nom']." (id ".$_SESSION['uid'].") a supprimé l'id ".$_GET['resp']." comme responsable du créneau ".$_GET['creneau']." ";
				$headers = 'From: '.getParam('admin_email.conf')."\r\n"        .
						   'Reply-To: '.getParam('contact_email.conf')."\r\n"  .
						   'Return-Path: '.getParam('admin_email.conf')."\r\n" .
						   'X-Mailer: PHP/'.phpversion();
				mail($to, $subject, $message, $headers);
			}
	}
	if (isset($_POST['action']) && $_POST['action'] === 'new_resp'){
		ajoutResponsableCre($_POST['id_cre'],$_POST['id_resp'],$promo);
		if (getParam('allow_mail.conf') == true && getParam('modif_rights.notif') == "now")
			{
				$to      = "webmaster.sport@u-psud.fr";
				$subject = "[".getParam('text_top.txt')."] Ajout Responsable Créneau";
				$message = "Bonjour,\r\n \r\n l'utilisateur ".$_SESSION['prenom']." ".$_SESSION['nom']." (id ".$_SESSION['uid']." a ajouté l'id ".$_POST['id_resp']." comme responsable du créneau ".$_POST['id_cre']." ";
				$headers = 'From: '.getParam('admin_email.conf')."\r\n"        .
						   'Reply-To: '.getParam('contact_email.conf')."\r\n"  .
						   'Return-Path: '.getParam('admin_email.conf')."\r\n" .
						   'X-Mailer: PHP/'.phpversion();
				mail($to, $subject, $message, $headers);
			}
	}
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_sup')
		delSup($_GET['sup']);
	if (isset($_POST['action']) && $_POST['action'] === 'submitted_modif_sup')
		modifSup($_POST);
	if (isset($_POST['action']) && $_POST['action'] === 'new_sup')
		addSup("creneau",$_POST['id_cre'],$_POST['type'],$_POST['valeur'],$_POST['id_asso_adh'],$_POST['id_asso_paie'],$_POST['facultatif'],$_POST['reduction'],$promo);
	if (isset($_POST['action']) && $_POST['action'] === 'copy_old_sups')
	{
		$sups = getSup("creneau",$_GET['creneau'],$_POST['old_promo']);
		foreach ($sups as $key => $value)
			addSup("creneau",$_GET['creneau'],$value['type'],$value['valeur'],$value['id_asso_adh'],$value['id_asso_paie'],$value['facultatif'],$value['reduction'],$promo);
	}
	if(!(strcmp($_SESSION['user'],"") == 0))
	{
		$tab=getCreneaux($_SESSION['uid']);

		if(empty($_GET['creneau'])){

			print '<h2>Vos Créneaux</h2>';
			print '<ul>';
			$non_actif="";
			foreach ($tab as $creneau)
			{
				$verif=0;
				$query = doQuery ("SELECT * FROM {$GLOBALS['prefix_db']}resp_cren WHERE id_cre = ".$creneau['id_cre']." AND promo = ".$current_promo." ");
				$verif = mysql_num_rows($query);
				if ($verif != 0){
				print '<li><a href=index.php?page=6&creneau='.$creneau['id_cre'].'>'.$creneau['nom_sec'].' - '.$creneau['nom_act'].' - '.$creneau['jour_cre'].' - '.$creneau['debut_cre'].' - '.$creneau['fin_cre'].'</a></li>';
				}
				else{
				$non_actif .= '<li><a href=index.php?page=6&creneau='.$creneau['id_cre'].'>'.$creneau['nom_sec'].' - '.$creneau['nom_act'].' - '.$creneau['jour_cre'].' - '.$creneau['debut_cre'].' - '.$creneau['fin_cre'].'</a></li>';
				}
			}
			print '</ul>';
			print '<br />
			<h2>Créneaux NON Actifs</h2>
			<ul>
			'.$non_actif.'
			</ul>
			';

		} else {
			print '<h2>Fiche Créneau</h2>';
			print "<div class=\"tip\">".getParam('text_creneau.txt')."</div>";
			print '<table>';
			print '<tr><td class="label">Activité : </td><td>'.$tab[$_GET['creneau']]['nom_sec']." - ".$tab[$_GET['creneau']]['nom_act'].'</td></tr>';
			print '<tr><td class="label">Jour : </td><td>'.$tab[$_GET['creneau']]['jour_cre'].'</td></tr>';
			print '<tr><td class="label">Debut : </td><td>'.$tab[$_GET['creneau']]['debut_cre'].'</td></tr>';
			print '<tr><td class="label">Fin : </td><td>'.$tab[$_GET['creneau']]['fin_cre'].'</td></tr>';
			print '<tr><td class="label">Lieu : </td><td>'.$tab[$_GET['creneau']]['batiment'].' '.$tab[$_GET['creneau']]['salle'].'</td></tr>';
			if($_SESSION['privilege'] == 1)
				print '<tr>
				<td colspan=2><FORM action="index.php?page=6&creneau='.$_GET['creneau'].'" method="POST">
				<input type="hidden" name="action" value="modification" />
				<INPUT type="submit" value="Modifier">
				</FORM></td>
				</tr>';
			print '</table>';
			
			//Selection Promo
			$res = doQuery("SELECT DISTINCT promo FROM {$GLOBALS['prefix_db']}adhesion ORDER BY promo DESC");
			print "<p>Promo:<SELECT id=\"promo\" >";
			if (!$res || mysql_num_rows($res) <= 0)
				print "<OPTION value='$promo' 'selected' >$promo</OPTION>";
			while ($tmp_array_promo = mysql_fetch_array($res))
				print "<OPTION value='".$tmp_array_promo['promo']."' ".(isset($_GET['promo']) && $_GET['promo'] == $tmp_array_promo['promo'] ? "selected" : "")." >".$tmp_array_promo['promo']."</OPTION>";
			print "</SELECT></p>";
			
			//Liste de responsables
			$resps = getResponsablesCre($_GET['creneau'],$promo);
			print '<h3>Responsables du créneau</h3>';
			print '<ul>';
			if(empty($resps)){print 'Aucun Responsable';}
			foreach ($resps as $id => $adh) {
				print '<FORM action="index.php?page=6&resp='.$id.'&creneau='.$_GET['creneau'].'&promo='.$promo.'" method="POST">
					<input type="hidden" name="action" value="suppression_resp" />
				<li><a href=index.php?page=1&adh='.$id.'>'.$adh['prenom'].' '.$adh['nom'].'</a>
				<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit">
					</FORM></li>';
			}
			print '</ul>';
			print '<FORM action="index.php?page=6&creneau='.$_GET['creneau'].'&promo='.$promo.'" method="POST">
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
			print '<h3>Suppléments du créneau</h3>
			<p>Les cases vertes représentent les réductions, Coché = "Non" la réduction de la colonne ne s\'applique pas</p>
			<table><tr><th>Type</th><th>Valeur</th><th>Asso de l\'adherent</th><th>Payer à</th><th>Facultatif</th>';
			$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}reductions ORDER BY nom ASC");
			while ($tmp_array = mysql_fetch_array($res))
			{
				print '<th>'.$tmp_array['nom'].'</th>';
				$liste_reductions[$tmp_array['id']] = 1;
			}
			print '</tr>';
			foreach ($sups as $id => $sup)
			{
				$reductions = NULL;
				$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}reductions_sup WHERE id_sup = $id ");
				while ($tmp_array = mysql_fetch_array($res))
					$reductions[$tmp_array['id_reduc']] = 1;
				print '<tr>
				<td>'.$sup['type'].'</td><td>'.$sup['valeur'].getParam('currency.conf').'</td><td>'.$assos[$sup['id_asso_adh']].'</td><td>'.$assos[$sup['id_asso_paie']].'</td><td>'.($sup['facultatif']==1 ? "Oui" : "Non").'</td>';
				foreach ($liste_reductions as $id_reduc => $value)
					print '<td bgcolor="#CCFFCC">'.($reductions[$id_reduc]==1 ? "Non" : "Oui").'</td>';
				if ($promo==$current_promo)
				{
					if($_SESSION['privilege'] == 1)
						print '
						<td><FORM action="index.php?page=6&sup='.$id.'&creneau='.$_GET['creneau'].'" method="POST">
						<input type="hidden" name="action" value="modif_sup" />
						<INPUT type="image" src="images/icone_edit.png" width="14" value="submit"></FORM></td>';
						
					print '<td><FORM action="index.php?page=6&sup='.$id.'&creneau='.$_GET['creneau'].'" method="POST">
					<input type="hidden" name="action" value="suppression_sup" />
					<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit"></FORM></td>';
				}
				print '</tr>';
			}

			if ($promo==$current_promo)
			{
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
				<td><INPUT type="checkbox" name="facultatif" value="1"></td>';
				foreach ($liste_reductions as $id_reduc => $value)
					print '<td><INPUT type="checkbox" name="reduction['.$id_reduc.']" value="1"></td>';
				print '<td><INPUT type="image" width="14" height="14" src="images/icone_add.png" value="submit"></td>
				';
				print '</FORM>';
			}
			else
			{
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
	else
		print "<p>Vous n'êtes pas connecté</p>";
	
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