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
	print '<p>Vous n\'avez pas accès à cette page!</p>';
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
elseif(isset($_POST['action']) && $_POST['action'] == 'modif_sup'){
	$res_sup = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}sup WHERE id=".$_GET['sup']." ");
	$data_sup = mysql_fetch_assoc($res_sup);
	$assos = getAssos();
	print '<h2>Modifier Supplément</h2>
	<table>
	<tr><th>Type</th><th>Valeur</th><th>Asso de l\'adherent</th><th>Payer à</th><th>Facultatif</th><th>Modif</th></tr>
	<tr><FORM action="index.php?page=4&section='.$_GET['section'].'" method="POST">
	<input type="hidden" name="action" value="submitted_modif_sup" />
	<input type="hidden" name="entite" value="section" />
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
	if (isset($_POST['action']) && $_POST['action'] === 'modif_famille')
		modifFamilleSec($_GET['section'], $_POST['id_famille']);
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_famille')
		suppressionFamilleSec($_GET['section'], $_POST['id_famille']);
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_resp')
		delRespSec($_GET['section'], $_GET['resp'],$promo);
	if (isset($_POST['action']) && $_POST['action'] === 'new_resp')
		ajoutResponsableSec($_POST['id_sec'], $_POST['id_resp'],$promo);
	if (isset($_POST['action']) && $_POST['action'] === 'suppression_sup')
		delSup($_GET['sup']);
	if (isset($_POST['action']) && $_POST['action'] === 'submitted_modif_sup')
		modifSup($_POST);
	if (isset($_POST['action']) && $_POST['action'] === 'new_sup')
		addSup("section",$_POST['id_sec'],$_POST['type'],$_POST['valeur'],$_POST['id_asso_adh'],$_POST['id_asso_paie'],$_POST['facultatif'],$promo);
	if (isset($_POST['action']) && $_POST['action'] === 'copy_old_sups')
	{
		$sups = getSup("section",$_GET['section'],$_POST['old_promo']);
		foreach ($sups as $key => $value)
			addSup("section",$_GET['section'],$value['type'],$value['valeur'],$value['id_asso_adh'],$value['id_asso_paie'],$value['facultatif'],$promo);
	}
	if(!(strcmp($_SESSION['user'],"") == 0))
	{

		$tab=getSections($_SESSION['uid']);
		if(empty($_GET['section'])){

			print '<h2>Vos Sections</h2>';
			print '<ul>';
			$non_actif="";
			foreach($tab as $section){
				$verif=0;
				$query = doQuery ("SELECT * FROM {$GLOBALS['prefix_db']}resp_section WHERE id_sec = ".$section['id']." AND promo = ".$current_promo." ");
				$verif = mysql_num_rows($query);
				if ($verif != 0){
				print '<li><a href=index.php?page=4&section='.$section['id'].'>'.$section['nom'].'</a></li>';
				}
				else{
				$non_actif .= '<li><a href=index.php?page=4&section='.$section['id'].'>'.$section['nom'].'</a></li>';
				}
			}
			print '</ul>';
			if($non_actif != "")
				print '<br />
				<h2>Sections SANS Responsables</h2>
				<ul>
				'.$non_actif.'
				</ul>
				';


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
			//Liste d'activités
		    $acts = getActivitesBySection($_GET['section']);
		    print '<h3>Activités de la section</h3>';
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
			
			//Famille
			print '<h3>Famille de la section</h3>';
			foreach (getFamilleSec($_GET['section']) as $id => $valeur){
					if($valeur['select'] == "select"){$select .= '
					<li><FORM action="index.php?page=4&section='.$_GET['section'].'" method="POST">
					<input type="hidden" name="action" value="suppression_famille" />
					<input type="hidden" name="id_famille" value="'.$id.'" />
					'.$valeur['nom'].'
					<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit">
					</FORM></li>';}
					else{$option .= '<OPTION value='.$id.'>'.$valeur['nom'].'</OPTION>';}
			}
			print '<ul>'.$select.'</ul>
			<FORM action="index.php?page=4&section='.$_GET['section'].'" method="POST">
			<input type="hidden" name="action" value="modif_famille" />
			<input type="hidden" name="id_sec" value="'.$_GET['section'].'">
			<SELECT name="id_famille">
			'.$option.'
			</SELECT>
			<INPUT type="submit" value="Ajouter"/>
			</FORM>';
					
			//Selection Promo
			// $res = doQuery("SELECT DISTINCT promo FROM {$GLOBALS['prefix_db']}sup WHERE id IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent=".$_GET['section'].") ORDER BY promo DESC");
			$res = doQuery("SELECT DISTINCT promo FROM {$GLOBALS['prefix_db']}adhesion ORDER BY promo DESC");
			print "<p>Promo:<SELECT id=\"promo\" >";
			if (!$res || mysql_num_rows($res) <= 0)
				print "<OPTION value='$promo' 'selected' >$promo</OPTION>";
			while ($tmp_array_promo = mysql_fetch_array($res))
				print "<OPTION value='".$tmp_array_promo['promo']."' ".(isset($_GET['promo']) && $_GET['promo'] == $tmp_array_promo['promo'] ? "selected" : "")." >".$tmp_array_promo['promo']."</OPTION>";
			print "</SELECT></p>";
		
			//Liste responsables
			$resps = getResponsablesSec($_GET['section'],$promo);
			print '<h3>Responsables de la section</h3>';
			print '<ul>';
			if(empty($resps)){print 'Aucun Responsable';}
			foreach ($resps as $id => $adh) {
				print '<FORM action="index.php?page=4&resp='.$id.'&section='.$_GET['section'].'&promo='.$promo.'" method="POST">
					<input type="hidden" name="action" value="suppression_resp" />
				<li><a href=index.php?page=1&adh='.$id.'>'.$adh['prenom'].' '.$adh['nom'].'</a>
				<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit">
					</FORM></li>';
			}
			print '</ul>';
			print '<FORM action="index.php?page=4&section='.$_GET['section'].'&promo='.$promo.'" method="POST">
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
			print '<h3>Suppléments de la section</h3>';
			print '<table><tr><th>Type</th><th>Valeur</th><th>Asso de l\'adherent</th><th>Payer à</th><th>Facultatif</th>';
			print '</tr>';
			foreach ($sups as $id => $sup)
			{
				print '<tr>
				<td>'.$sup['type'].'</td><td>'.$sup['valeur'].getParam('currency.conf').'</td><td>'.$assos[$sup['id_asso_adh']].'</td><td>'.$assos[$sup['id_asso_paie']].'</td><td>'.($sup['facultatif']==1 ? "oui" : "non").'</td>';
				
				if($promo==$current_promo) print '
					<td><FORM action="index.php?page=4&sup='.$id.'&section='.$_GET['section'].'" method="POST">
					<input type="hidden" name="action" value="modif_sup" />
					<INPUT type="image" src="images/icone_edit.png" width="14" value="submit"></FORM></td>
					
					<td><FORM action="index.php?page=4&sup='.$id.'&section='.$_GET['section'].'" method="POST">
					<input type="hidden" name="action" value="suppression_sup" />
					<INPUT type="image" src="images/unchecked.gif" class="confirm" value="submit"></FORM></td>
					</tr>';
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
				<td><INPUT type="checkbox" name="facultatif" value="1"></td><td></td>
				<td><INPUT type="image" width="14" height="14" src="images/icone_add.png" value="submit"></td>
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