<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');

$tot_asso=count(getAssociations($_SESSION['uid']));
$tot_sec=count(getSections($_SESSION['uid']));
$tot_act=count(getActivites($_SESSION['uid']));
$tot_cre=count(getCreneaux($_SESSION['uid']));
$tot = $tot_asso + $tot_sec + $tot_act + $tot_cre;
if((strcmp($_SESSION['user'],"") == 0)){
	print "<p>Vous n'êtes pas connecté</p>";
	die();
}
getAdherent($_SESSION['user']);
$tab=getAssociations($_SESSION['uid']);
if(isset($_GET['asso']) && !isset($tab[$_GET['asso']])){
	print '<p>Vous n\'avez pas accès à cette page!</p>';
	die();
}
if(isset($_GET['promo'])) {
	$promo=$_GET['promo'];
} else {
	$promo=$current_promo;
}
if (isset($_POST['action']) && $_POST['action'] == 'modification') {
	print '<h2>Fiche Association: Modification</h2>';
	print '<FORM id="f_asso_modif" action="index.php?page=3&asso='.$_GET['asso'].'" enctype="multipart/form-data" method="POST">';
	print '<table border=0>';
	print '<tr><td class="label"><LABEL for ="nom" >Nom</LABEL> : </td><td><INPUT type=text name="nom" id="nom" value="'.$tab[$_GET['asso']]['nom'].'"></td></tr>';
	print '<tr><td class="label"><LABEL for ="description" >Description</LABEL> : </td><td><TEXTAREA rows=3 cols=25 name="description" id="description">'.$tab[$_GET['asso']]['description'].'</TEXTAREA></td></tr>';
	print '<tr><td class="label"><LABEL for ="logo_asso" >Logo</LABEL> : </td><td><INPUT type=file name="logo_asso" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="url" >URL</LABEL> : </td><td><INPUT type=text name="url" id="url" value="'.$tab[$_GET['asso']]['url'].'"></td></tr>';
	print '<input type="hidden" name="action" value="submitted" />';
	print '<input type="hidden" name="id" value="'.$tab[$_GET['asso']]['id'].'" />';
	print '<tr><td colspan="2"><INPUT type="submit" value="Envoyer" ></td></tr>';
	print '</table>';
	print '</FORM>';

}
else if (isset($_POST['action']) && $_POST['action'] == 'new') {
	print '<h2>Nouvelle Association</h2>';
	print '<FORM id="f_asso_new" action="index.php?page=3" enctype="multipart/form-data" method="POST">';
	print '<table border=0>';
	print '<tr><td class="label"><LABEL for ="nom" >Nom</LABEL> : </td><td><INPUT type=text name="nom" id="nom" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="description" >Description</LABEL> : </td><td><TEXTAREA rows=3 cols=25 name="description" id="description"></TEXTAREA></td></tr>';
	print '<tr><td class="label"><LABEL for ="logo_asso" >Logo</LABEL> : </td><td><INPUT type=file name="logo_asso" ></td></tr>';
	print '<tr><td class="label"><LABEL for ="url" >URL</LABEL> : </td><td><INPUT type=text name="url" id="url" ></td></tr>';
	print '<input type="hidden" name="action" value="submitted_new" />';
	print '<tr><td colspan="2"><INPUT type="submit" value="Envoyer"></td></tr>';
	print '</table>';
	print '</FORM>';

}

else if (isset($_POST['action']) && $_POST['action'] == 'new_vacances') {
	print '<h2>Ajouter Vacances</h2>';
	print 'Association : '.$_POST['nom_asso'].'<br /><br />';
	print 'Selectionnez ci-dessous la semaine que vous souhaitez fermer (n\'importe quel jour de la semaine)<br /><br />';
	print "<script>
	$('.ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
	$('.ui-datepicker-calendar tr').live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });
	</script>";
	print '<FORM action="index.php?page=3&asso='.$_POST['id_asso'].'" method="POST">';
	print 'Date : <input name="date_vacances" type="text" class="datepicker" required />';
	print '<input type="hidden" name="id_asso" value="'.$_POST['id_asso'].'" />';
	print '<input type="hidden" name="action" value="submitted_new_vacances" />';
	print '<INPUT type="submit" value="Ajouter">';
	print '</FORM>';

}

else
{
	if (isset($_POST['action']) && $_POST['action'] === 'submitted')
		modifAsso($_POST);
	if (isset($_POST['action']) && $_POST['action'] === 'submitted_new')
		newAsso($_POST);
	if (isset($_POST['action']) && $_POST['action'] === 'submitted_new_vacances')
		ajouter_vacances($_POST['date_vacances'], $_POST['id_asso'], $promo);
	if (isset($_POST['action']) && $_POST['action'] === 'suppression')
		delAsso($_POST['id']);
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_resp')
		delRespAsso($_POST['id_asso'],$_POST['id_resp'], $promo);
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_vacances')
		delVacances($_POST['week'], $promo);
	if (isset($_POST['action']) && $_POST['action'] === 'new_resp')
		ajoutResponsableAsso($_POST['id_asso'],$_POST['id_resp'],$promo);
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_sup')
		delSup($_POST['id_sup']);
	if (isset($_POST['action']) && $_POST['action'] === 'new_sup')
		addSup("association",$_POST['id_asso'],$_POST['type'],$_POST['valeur'],$_POST['id_statut'],$_POST['id_asso'],0,$promo);
	if (isset($_POST['action']) && $_POST['action'] === 'copy_old_sups')
	{
		$sups = getSup("association",$_GET['asso'],$_POST['old_promo']);
		foreach ($sups as $key => $value)
			addSup("association",$_GET['asso'],$value['type'],$value['valeur'],$value['id_statut'],$value['id_asso_paie'],$promo);
	}
	if(!(strcmp($_SESSION['user'],"") == 0))
	{
		$tab=getAssociations($_SESSION['uid']);

		if(empty($_GET['asso'])){

			print '<h2>Vos associations</h2>';
			print '<ul>';

			foreach($tab as $asso){
				print '<li><a href=index.php?page=3&asso='.$asso['id'].'>'.$asso['nom'].'</a></li>';

			}
			print '</ul>';
			if($_SESSION['privilege']==="1") print '<td colspan=2><FORM action="index.php?page=3" method="POST">
			<input type="hidden" name="action" value="new" />
			<INPUT type="submit" value="Nouvelle">
			</FORM></td>';

		} else {
			print '<h2>Fiche Association</h2>';
			print "<div class=\"tip\">".getParam('text_asso.txt')."</div>";
			print '<table>';
			print '<tr><td class="label">Nom : </td><td>'.$tab[$_GET['asso']]['nom'].'</td></tr>';
			print '<tr><td class="label" style="vertical-align:top;">Description : </td><td>'.nl2br($tab[$_GET['asso']]['description']).'</td></tr>';
			print '<tr><td class="label">Url : </td><td>'.$tab[$_GET['asso']]['url'].'</td></tr>';
			$_SESSION['auth_thumb']='true';
			$photo="includes/thumb.php?folder=logo_asso&file=".$_GET['asso'].".jpg";
			if (isset($row['description']))
				$tmp_stock = $row['description'];
			else
				$tmp_stock = "";
			print '<tr><TD>'.$tmp_stock.'</TD><TD><img src="'.$photo.'" ></TD></tr>';
			print '<tr>';
			print '<td colspan="2"><FORM action="index.php?page=3&asso='.$_GET['asso'].'" method="POST">
					<input type="hidden" name="action" value="modification" />
					<INPUT type="submit" value="Modifier">
					</FORM></td>';
			print '</tr>';

			if($_SESSION['privilege']==="1") print '<tr><td colspan=2><FORM action="index.php?page=3" method="POST">
					<input type="hidden" name="action" value="suppression" />
					<input type="hidden" name="id" value="'.$_GET['asso'].'" />
					<INPUT type="submit" class="confirm" value="Supprimer">
					</FORM></td></tr>';

			print '</table>';

			//liste sections de l'asso
			$sections=getSectionsByAsso($_GET['asso']);
			print '<h3>Sections de l\'association</h3>';
			print '<ul>';
			foreach($sections as $section){
				print '<li>
				<FORM action="index.php?page=4&section='.$section['id'].'"" method="POST">
					<input type="hidden" name="action" value="suppression" />
					<input type="hidden" name="id" value="'.$section['id'].'" />
					<a href="index.php?page=4&section='.$section['id'].'">'.$section['nom'].'</a>
					<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit">
					</FORM></li>';
			}
			print '</ul>';
			print '<td colspan=2><FORM action="index.php?page=4" method="POST">
			<input type="hidden" name="action" value="new" />
			<input type="hidden" name="id_asso" value="'.$_GET['asso'].'" />
			<INPUT type="submit" value="Nouvelle">
			</FORM></td>';
					
			//Selection Promo
			$res = doQuery("SELECT DISTINCT promo FROM {$GLOBALS['prefix_db']}sup WHERE id IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent=".$_GET['asso'].") ORDER BY promo DESC");
			print "<p>Promo:<SELECT id=\"promo\" >";
			if (!$res || mysql_num_rows($res) <= 0)
				print "<OPTION value='$promo' 'selected' >$promo</OPTION>";
			while ($tmp_array_promo = mysql_fetch_array($res))
				print "<OPTION value='".$tmp_array_promo['promo']."' ".(isset($_GET['promo']) && $_GET['promo'] == $tmp_array_promo['promo'] ? "selected" : "")." >".$tmp_array_promo['promo']."</OPTION>";
			print "</SELECT></p>";
			
			//Liste de responsables
			$resps = getResponsablesAsso($_GET['asso'],$promo);
			print '<h3>Responsables de l\'association</h3>';
			print '<ul>';
			if(empty($resps)){print 'Aucun Responsable';}
			foreach ($resps as $id => $adh) {
				print '<FORM action="index.php?page=3&resp='.$id.'&asso='.$_GET['asso'].'&promo='.$promo.'" method="POST">
					<input type="hidden" name="action" value="suppression_resp" />
					<input type="hidden" name="id_asso" value="'.$_GET['asso'].'" />
					<input type="hidden" name="id_resp" value="'.$id.'" />
				<li><a href=index.php?page=1&adh='.$id.'>'.$adh['prenom'].' '.$adh['nom'].'</a>
				<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit">
					</FORM></li>';
			}
			print '</ul>';
			print '<FORM action="index.php?page=3&asso='.$_GET['asso'].'&promo='.$promo.'" method="POST">
			<input type="hidden" name="action" value="new_resp" />
			<input type="hidden" name="id_asso" value="'.$_GET['asso'].'">';
			print '<label for="new_resp">Ajouter un Responsable </label><SELECT name="id_resp" class="filterselect" >';
			$candidates = getAdherents();
			foreach ($candidates as $key => $value) {
				if(!isset($resps[$key])) print '<OPTION value='.$key.' >'.$value['prenom'].' '.$value['nom'].'</OPTION>';
			}
			print '<INPUT type="submit" /> ';
			print '</SELECT>';
			print '</FORM>';
			//Liste de suppléments = cotisations
			print '<h3>Cotisations de l\'association</h3>';
			$sups = getSup("association",$_GET['asso'],$promo);
			print '<table><tr><th>Type</th><th>Valeur</th><th>Pour</th>';
			if($promo==$current_promo) print '<th>+/-</th>';
			print '</tr>';
			foreach ($sups as $id => $sup) {
				print '<tr><td>'.$sup['type'].'</td><td>'.$sup['valeur'].getParam('currency.conf').'</td><td>'.$sup['statut'].'</td>';
				if($promo==$current_promo) print '<td><FORM action="index.php?page=3&asso='.$_GET['asso'].'" method="POST">
					<input type="hidden" name="action" value="suppression_sup" />
					<input type="hidden" name="id_sup" value="'.$id.'" />
					<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit"></td>
					</FORM>'; 
				print '</tr>';
			}

			if($promo==$current_promo){
				print '<tr><FORM action="index.php?page=3&asso='.$_GET['asso'].'" method="POST">
				<input type="hidden" name="action" value="new_sup" />
				<input type="hidden" name="id_asso" value="'.$_GET['asso'].'" />
				<INPUT type="hidden" name="type" value="Cotisation" / >
				<td><INPUT type="text" name="lol" value="Cotisation" disabled ></INPUT></td>
				<td><INPUT type="text" name="valeur"></INPUT></td>
				<td><SELECT name="id_statut">';
				$status = getStatuts();
				foreach ($status as $key => $value) {
					print '<OPTION value="'.$key.'">'.$value.'</OPTION>';
				}

				print '</SELECT></td>
				<td><INPUT type="image" width="14" height="14" src="images/icone_add.png" value="submit"></td>
				';
			print '</FORM></tr>';
			} else {
				print '<td colspan=3>
				<FORM action="index.php?page=3&asso='.$_GET['asso'].'" method="POST">
				<input type="hidden" name="old_promo" value="'.$_GET['promo'].'" >
				<input type="hidden" name="action" value="copy_old_sups" >
				<INPUT type="submit" class="confirm" value="Recopier ces suppléments dans la promo courante" >
				</FORM></td>';
			}
			print '</table>';
			
			//Gestion Vacances
			$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}vacances WHERE promo=".$promo." AND id_entite=".$_GET['asso']." ");
			print "<h3>Vacances / Fermetures</h3><ul>";
			if (!$res || mysql_num_rows($res) <= 0)
				print "Pas de Fermetures";
			while ($tmp_array_vacances = mysql_fetch_array($res)){
				$date = date_create();
				$annee = $promo;
				date_isodate_set($date, $annee, $tmp_array_vacances['week']);
				
				if(date_format($date, 'm') >= 9)
				date_isodate_set($date, $annee-1, $tmp_array_vacances['week']);
				
				$date_debut = date_format($date, 'd/m/Y');
				date_add($date, date_interval_create_from_date_string('6 days'));
				$date_fin = date_format($date, 'd/m/Y');
				echo '<li>
				<FORM action="index.php?page=3&asso='.$_GET['asso'].'&promo='.$promo.'" method="POST">
				<input type="hidden" name="action" value="suppression_vacances" />
				<input type="hidden" name="week" value="'.$tmp_array_vacances['week'].'" />
				Semaine '.$tmp_array_vacances['week'].' : Fermé du '.$date_debut.' au '.$date_fin.'
				<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit">
				</FORM></li>
				';
			}
			print '</ul>
			<FORM action="index.php?page=3&asso='.$_GET['asso'].'" method="POST">
			<input type="hidden" name="action" value="new_vacances" />
			<input type="hidden" name="id_asso" value="'.$_GET['asso'].'" />
			<input type="hidden" name="nom_asso" value="'.$tab[$_GET['asso']]['nom'].'" />
			<INPUT type="submit" value="Nouvelle">
			</FORM>
			';
			
		}

	}
	else
	{
		print "<p>Vous n'êtes pas connecté</p>";
	}
}



?>
<script type="text/javascript">
$('#promo').change( function (){
	window.location.search = "page=3&asso="+$.getUrlVar('asso')+"&promo="+$(this).val();
});



</script>