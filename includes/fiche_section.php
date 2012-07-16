<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
$tot_asso=count(getAssociations($_SESSION['uid']));
$tot_sec=count(getSections($_SESSION['uid']));
$tot_act=count(getActivites($_SESSION['uid']));
$tot_cre=count(getCreneaux($_SESSION['uid']));
$tot = $tot_asso + $tot_sec + $tot_act + $tot_cre;
getAdherent($_SESSION['user']);
$tab=getSections($_SESSION['uid']);
if(isset($_GET['section']) && !isset($tab[$_GET['section']]))
{
	print '<p>Vous n\'avez pas acc�s � cette page!</p>';
	die();
}
if(isset($_GET['promo']))
	$promo=$_GET['promo'];
else
	$promo=$current_promo;
if (isset($_POST['action']) && $_POST['action'] == 'modification')
{
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

}
else if (isset($_POST['action']) && $_POST['action'] == 'new') {
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

}
else
{
	if (isset($_POST['action']) && $_POST['action'] === 'submitted')
		modifSection($_POST);
	if (isset($_POST['action']) && $_POST['action'] === 'submitted_new')
		newSection($_POST);
	if (isset($_POST['action']) && $_POST['action'] === 'suppression')
	{
		delSection($_GET['section']);
		header("Location: index.php?page=4");
		
	}
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_resp')
		delRespSec($_GET['section'], $_GET['resp']);
	if (isset($_POST['action']) && $_POST['action'] === 'new_resp')
		ajoutResponsableSec($_POST['id_sec'], $_POST['id_resp']);
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_sup')
		delSup($_GET['sup']);
	if (isset($_POST['action']) && $_POST['action'] === 'new_sup')
		addSup("section",$_POST['id_sec'],$_POST['type'],$_POST['valeur'],$_POST['id_asso_adh'],$_POST['id_asso_paie'],$promo);
	if (isset($_POST['action']) && $_POST['action'] === 'copy_old_sups')
	{
		$sups = getSup("section",$_GET['section'],$_POST['old_promo']);
		foreach ($sups as $key => $value)
			addSup("section",$_GET['section'],$value['type'],$value['valeur'],$value['id_asso_adh'],$value['id_asso_paie'],$promo);
	}
	if(!(strcmp($_SESSION['user'],"") == 0))
	{
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
			print "<div class=\"tip\">".getParam('text_section.txt')."</div>";
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
			//Liste d'activit�s
		    $acts = getActivitesBySection($_GET['section']);
		    print '<h3>Activit�s de la section</h3>';
			print '<ul>';
			foreach($acts as $act){
				print '<li>
				<FORM action="index.php?page=5&act='.$act['id'].'" method="POST">
					<input type="hidden" name="action" value="suppression" />
					<a href=index.php?page=5&act='.$act['id'].'>'.$act['nom'].'</a>
					<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit">
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
					<input type="hidden" name="action" value="suppression_resp" />
				<li><a href=index.php?page=1&adh='.$id.'>'.$adh['prenom'].' '.$adh['nom'].'</a>
				<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit">
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
			//Liste de suppl�ments
			$sups = getSup("section",$_GET['section'],$promo);
			$assos = getAssos();
			//Selection Promo
			print "<p>Promo:<SELECT id=\"promo\" >";
			print "<OPTION value=$current_promo ".(isset($_GET['promo']) && $_GET['promo']==$current_promo ? "selected" : "")." >$current_promo</OPTION>";
			for ($i=1; $i<=10; $i++ ){
				$p=$current_promo-$i;
				print "<OPTION value=\"$p\" ".(isset($_GET['promo']) && $_GET['promo']==$p ? "selected" : "")." >$p</OPTION>";
			}
			print "</SELECT></p>";
			print '<h3>Suppl�ments de la section</h3>';
			print '<table><tr><th>Type</th><th>Valeur</th><th>Asso de l\'adherent</th><th>Payer �</th>';
			if($promo==$current_promo) print '<th>+/-</th>';
			print '</tr>';
			foreach ($sups as $id => $sup)
			{
				print '<tr>
				<td>'.$sup['type'].'</td><td>'.$sup['valeur'].getParam('currency.conf').'</td><td>'.$assos[$sup['id_asso_adh']].'</td><td>'.$assos[$sup['id_asso_paie']].'</td>';
				
				if($promo==$current_promo) print '<td><FORM action="index.php?page=4&sup='.$id.'&section='.$_GET['section'].'" method="POST">
					<input type="hidden" name="action" value="suppression_sup" />
					<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit"></FORM></td>
				';
				print '</tr>';
			}

			if($promo==$current_promo) {
				print '<tr><FORM action="index.php?page=4&section='.$_GET['section'].'" method="POST">
				<input type="hidden" name="action" value="new_sup" />
				<input type="hidden" name="id_sec" value="'.$_GET['section'].'">
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
				<td><INPUT type="image" src="images/checked.gif" value="submit"></td>
				';
				print '</FORM>';
			}	else {
					print '<td colspan=4>
					<FORM action="index.php?page=4&section='.$_GET['section'].'" method="POST">
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
$('#promo').change( function (){
	window.location.search = "page=4&section="+$.getUrlVar('section')+"&promo="+$(this).val();
});
</script>