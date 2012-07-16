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
else
{
	if (isset($_POST['action']) && $_POST['action'] === 'submitted')
		modifAsso($_POST);
	if (isset($_POST['action']) && $_POST['action'] === 'submitted_new')
		newAsso($_POST);
	if (isset($_POST['action']) && $_POST['action'] === 'suppression')
		delAsso($_POST['id']);
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_resp')
		delRespAsso($_POST['id_asso'],$_POST['id_resp']);
	if (isset($_POST['action']) && $_POST['action'] === 'new_resp')
		ajoutResponsableAsso($_POST['id_asso'],$_POST['id_resp']);
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_sup')
		delSup($_POST['id_sup']);
	if (isset($_POST['action']) && $_POST['action'] === 'new_sup')
		addSup("association",$_POST['id_asso'],$_POST['type'],$_POST['valeur'],$_POST['id_statut'],$_POST['id_asso'],$promo);
	if (isset($_POST['action']) && $_POST['action'] === 'copy_old_sups')
	{
		$sups = getSup("association",$_GET['asso'],$_POST['old_promo']);
		foreach ($sups as $key => $value)
			addSup("association",$_GET['asso'],$value['type'],$value['valeur'],$value['id_statut'],$value['id_asso_paie'],$promo);
	}
	if(!(strcmp($_SESSION['user'],"") == 0))
	{
		$tab=getAssociations($_SESSION['uid']);
		print '<ul id="submenu">';
		if(isset($tot_asso) && $tot_asso > 0)
			print '<li><a class="'.(($_GET['page']==3) ? 'selected' : '').'" href="index.php?page=3">Associations</a></li>';
		if(isset($tot_sec) && $tot_sec > 0)
			print '<li><a class="'.(($_GET['page']==4) ? 'selected' : '').'" href="index.php?page=4">Sections</a></li>';
		if(isset($tot_act) && $tot_act > 0)
			print '<li><a class="'.(($_GET['page']==5) ? 'selected' : '').'" href="index.php?page=5">Activités</a></li>';
		if(isset($tot_cre) && $tot_cre > 0)
			print '<li><a class="'.(($_GET['page']==6) ? 'selected' : '').'" href="index.php?page=6">Créneaux</a></li>';
		print '</ul>';
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
			print '<tr><td class="label">Description : </td><td>'.$tab[$_GET['asso']]['description'].'</td></tr>';
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
			//Liste de responsables
			$resps = getResponsablesAsso($_GET['asso']);
			print '<h3>Responsables de l\'association</h3>';
			print '<ul>';
			foreach ($resps as $id => $adh) {
				print '<FORM action="index.php?page=3&resp='.$id.'&asso='.$_GET['asso'].'" method="POST">
					<input type="hidden" name="action" value="suppression_resp" />
					<input type="hidden" name="id_asso" value="'.$_GET['asso'].'" />
					<input type="hidden" name="id_resp" value="'.$id.'" />
				<li><a href=index.php?page=1&adh='.$id.'>'.$adh['prenom'].' '.$adh['nom'].'</a>
				<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit">
					</FORM></li>';
			}
			print '</ul>';
			print '<FORM action="index.php?page=3&asso='.$_GET['asso'].'" method="POST">
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
			//Liste de suppléments
			print '<h3>Suppléments de l\'asssociation</h3>';
			//Selection Promo
			print "<p>Promo:<SELECT id=\"promo\" >";
			print "<OPTION value=$current_promo ".(isset($_GET['promo']) && $_GET['promo']==$current_promo ? "selected" : "")." >$current_promo</OPTION>";
			for ($i=1; $i<=10; $i++ ){
				$p=$current_promo-$i;
				print "<OPTION value=\"$p\" ".(isset($_GET['promo']) && $_GET['promo']==$p ? "selected" : "")." >$p</OPTION>";
			}
			print "</SELECT></p>";
			//Suppléments
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
				<td><INPUT type="image" src="images/checked.gif" value="submit"></td>
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