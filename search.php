<?php
function selected($post,$val)
{
	if (isset($_POST[$post]) && $_POST[$post]===$val)
		return "selected";
	else
		return "";
}

function checked($post,$val)
{
	if (isset($_POST[$post]) && $_POST[$post]===$val)
		return "checked";
	else
		return "";
}

function multiselected($post,$val)
{
	for($i=0; $i < sizeof($_POST[$post]); $i++)
	{
		if($_POST[$post][$i]===$val)
			return "selected";
	}
	return "";
}

if(!isset($_SESSION['user']))	// Si l'utilisateur est d�connect�
{
	print "<p>Vous n'�tes pas connect� !</p>";
}
else	// Si l'utilisateur est connect�
{
	if($_SESSION['privilege']==1)	// Si l'utilisateur est administrateur
	{
		$admin=true;
		if (isset($_GET['adh']))
			$id_adh=$_GET['adh'];
		else
			$id_adh = "";
		$resp_asso=true;
	}
	else
	{
		if(count(getMyAssos($_SESSION['uid'])) > 0 ) {
			$resp_asso=true;
		}
	}
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}resp_act  WHERE id_adh='".$_SESSION['uid']."'
	UNION
	SELECT * FROM {$GLOBALS['prefix_db']}resp_cren  WHERE id_adh='".$_SESSION['uid']."'
 	UNION
 	SELECT * FROM {$GLOBALS['prefix_db']}resp_section  WHERE id_adh='".$_SESSION['uid']."'
 	UNION
	SELECT * FROM {$GLOBALS['prefix_db']}resp_asso  WHERE id_adh='".$_SESSION['uid']."' ";
	include("opendb.php");
	$results = mysql_query($query);
	include("closedb.php");
	if (!$results) echo mysql_error();
	else if (mysql_num_rows($results)==0 AND $_SESSION['privilege']!='1'){
		print "Vous n'avez pas acc&egrave;s &agrave; cette page.";
	}
	else{
?>
<div>
<h2 class="inline">Recherche</h2>
<img src="images/downarrow.gif" class="inline" id="toggle_f_search" ></img>
</div>
<?php
print '<span class="tip">'.getParam('text_search.txt').'</span>';

	if (empty($_POST['field_count'])) $_POST['field_count']=1;
	if (empty($_POST['set1_text'])) $_POST['set1_text']="";
	//print '<button id="toggle_f_search">Toggle</button>';

	if(isset($_POST['select_promo']))
		$promo=$_POST['select_promo'];
	else
		$promo=$current_promo;
	$query = "SELECT DISTINCT promo FROM {$GLOBALS['prefix_db']}adhesion ORDER BY promo DESC";
	include("opendb.php");
	$promoliste = mysql_query($query);
	if (!$promoliste) echo mysql_error();

	print '
	<form id="f_search" method="post" action="index.php?page=2">
	<fieldset class="main"><legend>Crit�res Adh�rent</legend>
	<input type="hidden" name="field_count" value="'.$_POST['field_count'].'" />
	<input type="hidden" name="action" value="submitted" />

	<div id="promo">
	     <label for="select_promo">Promo est</label>
	     <select id="select_promo" name="select_promo">
	';
	while ($array_promo = mysql_fetch_array($promoliste))
		print '<option '.selected('select_promo',$array_promo['promo']).' value="'.$array_promo['promo'].'">'.$array_promo['promo'].'</option>';
	print '
	      </select>
	</div>

	<div id="solde">
		<label for="select_solde">Solde est</label>
		<select id="select_solde" name="select_solde">
			<option '.selected('select_solde','1').' value="1">Indiff&eacute;rent</option>
			<option '.selected('select_solde','2').' value="2">Positif</option>
			<option '.selected('select_solde','3').' value="3">N&eacute;gatif</option>
			<option '.selected('select_solde','4').' value="4">Nul</option>
		</select>
	</div>
	<div id ="choix_association">
		<label for="choix_association_type">Association est</label>
		<select id="choix_association_type" name="choix_association_type">
			<option '.selected('choix_association_type','0').' label="Indiff&eacute;rent" value="0">Indiff&eacute;rent</option>
	';
	// Cr�ation de la liste d�roulante sur le filtre des associations
	include("opendb.php");
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}association";
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	$compteur = 0;
	while ($stock_association[$compteur] = mysql_fetch_array($results))
		$compteur++;
	include("closedb.php");
	$compteur_asso = 0;
	while ($compteur_asso != $compteur)
	{
		print '<option '.selected('choix_association_type', $stock_association[$compteur_asso]['id']).' label="'.$stock_association[$compteur_asso]['nom'].'" value="'.$stock_association[$compteur_asso]['id'].'">'.$stock_association[$compteur_asso]['nom'].'</option>';
		$compteur_asso++;
	}
	// fin filtre assos
	print '
	      </select>
	</div>
	<div id ="choix_statut">
		<label for="choix_statut_type">Statut est</label>
		<select id="choix_statut_type" name="choix_statut_type[]" multiple="yes" size="5">
			<option '.multiselected('choix_statut_type','0').' label="Indiff&eacute;rent" value="0">Indiff&eacute;rent</option>
	';
	// Cr�ation de la liste d�roulante sur la liste des statuts
	include("opendb.php");
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}statut";
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	$compteur = 0;
	while ($stock_statut[$compteur] = mysql_fetch_array($results))
		$compteur++;
	include("closedb.php");
	$compteur_statut = 0;
	while ($compteur_statut != $compteur)
	{
		print '<option '.multiselected('choix_statut_type', $stock_statut[$compteur_statut]['id']).' label="'.$stock_statut[$compteur_statut]['nom'].'" value="'.$stock_statut[$compteur_statut]['id'].'">'.$stock_statut[$compteur_statut]['nom'].'</option>';
		$compteur_statut++;
	}
	// fin filtre statuts
	print '
	      </select>
	</div>
	<div id="responsable">
		<dt><input type="checkbox" '.checked('exclure_adhs','1').' name="exclure_adhs" value="1">Exclure les simples adh�rents</input></dt>
		<dt><input type="checkbox" '.checked('responsable_cren','1').' name="responsable_cren" value="1">Inclure les responsables des cr�neaux (les encadrants)</input></dt>
		<dt><input type="checkbox" '.checked('responsable_acti','1').' name="responsable_acti" value="1">Inclure les responsables des activit�s (?)</input></dt>
		<dt><input type="checkbox" '.checked('responsable_sect','1').' name="responsable_sect" value="1">Inclure les responsables des sections (le comit� directeur)</input></dt>
		<dt><input type="checkbox" '.checked('responsable_asso','1').' name="responsable_asso" value="1">Inclure les responsables de l\'association (le bureau)</input></dt>
		<dt><input type="checkbox" '.checked('sans_certif','1').' name="sans_certif" value="1">Afficher seulement les adh�rents sans certif</input></dt>
		<dt><input type="checkbox" '.checked('sans_photo','1').' name="sans_photo" value="1">Afficher seulement les adh�rents sans photo</input></dt>
		<dt><input type="checkbox" '.checked('compte_inactif','1').' name="compte_inactif" value="1">Afficher seulement les comptes d�sactiv�s</input></dt>
	</div>
	<div id="set1">
	<select id="set1_type" name="set1_type">
		<option '.selected('set1_type','1').' label="Nom" value="1">Nom</option>
		<option '.selected('set1_type','2').' label="Pr&eacute;nom" value="2">Pr&eacute;nom</option>
		<option '.selected('set1_type','3').' label="Email" value="3">Email</option>
		<option '.selected('set1_type','4').' label="Cat&eacute;gorie" value="4">Cat&eacute;gorie</option>
		<option '.selected('set1_type','5').' label="Code postal" value="5">Code Postal</option>
	</select>
	<select id="set1_action" name="set1_action">
		<option '.selected('set1_action','1').' label="Contient" value="1">Contient</option>
		<option '.selected('set1_action','2').' label="Commence" value="2">Commence</option>
		<option '.selected('set1_action','3').' label="Est" value="3">Est</option>
	</select>
	<input type="text" id="set1_text" name="set1_text" value="'.$_POST['set1_text'].'"/>
	</div>
	<div id="filters">';
	for($i = 1; $i < $_POST['field_count']; $i++)
	{
		$n=$i+1;
		$type="set".$n."_type";
		$action="set".$n."_action";
		$text="set".$n."_text";
		print '<div id="set'.$n.'">
		<select id="set'.$n.'_type" name="set'.$n.'_type">
		<option '.selected($type,'1').' label="Nom" value="1">Nom</option>
		<option '.selected($type,'2').' label="Pr�nom" value="2">Pr�nom</option>
		<option '.selected($type,'3').' label="Email" value="3">Email</option>
		<option '.selected($type,'4').' label="Cat�gorie" value="4">Cat�gorie</option>
		</select>
		<select id="set'.$n.'_action" name="set'.$n.'_action">
		<option '.selected($action,'1').' label="Contient" value="1">Contient</option>
		<option '.selected($action,'2').' label="Commence" value="2">Commence</option>
		<option '.selected($action,'3').' label="Est" value="3">Est</option>
		</select>
		<input type="text" id="set'.$n.'_text" name="set'.$n.'_text" value="'.$_POST[$text].'"/>
		</div>';
	}
	print '</div>
	<button type="button" id="add_field">Ajouter un champ</button>
	</fieldset>
	<fieldset class="selects"><legend>S�lection des cr�neaux</legend>
	<ul id="tree_root">
		<li><input type="checkbox" name="sections" '.checked('sections','sections').' value="sections" ><label>Tout</label>
			<ul id="sections"  >
	';
	$creneaux=getCreneaux($_SESSION['uid']);
	$tab=array();
	foreach($creneaux as $creneau){
		$tab[$creneau['id_sec']]['nom'] = $creneau['nom_sec'];
		$tab[$creneau['id_sec']]['id'] = $creneau['id_sec'];
		$tab[$creneau['id_sec']]['activites'][$creneau['id_act']]['nom'] = $creneau['nom_act'];
		$tab[$creneau['id_sec']]['activites'][$creneau['id_act']]['id'] = $creneau['id_act'];
		$tab[$creneau['id_sec']]['activites'][$creneau['id_act']]['creneaux'][$creneau['id_cre']]['jour'] = $creneau['jour_cre'];
		$tab[$creneau['id_sec']]['activites'][$creneau['id_act']]['creneaux'][$creneau['id_cre']]['id'] = $creneau['id_cre'];
		$tab[$creneau['id_sec']]['activites'][$creneau['id_act']]['creneaux'][$creneau['id_cre']]['debut'] = $creneau['debut_cre'];
	}
	foreach($tab as $section){
		print '<li><input type="checkbox" name="section'.$section['id'].'" '.checked('section'.$section['id'],$section['id']).' value="'.$section['id'].'"><label>'.$section['nom'].'</label>';
		print '<ul id="activites">';
		foreach($section['activites'] as $act){
			print '<li><input type="checkbox" name="act'.$act['id'].'" '.checked('act'.$act['id'],$act['id']).' value="'.$act['id'].'"><label>'.$act['nom'].'</label>';
			print '<ul id="creneaux">';
			foreach($act['creneaux'] as $cre){
				print '<li><input type="checkbox" name="cre'.$cre['id'].'" '.checked('cre'.$cre['id'],$cre['id']).' value="'.$cre['id'].'"><label>'.$cre['jour'].' - '.substr($cre['debut'],0,-3).'</label>';
			}
			print '</ul>';
		}
		print '</ul>';
	}
	print '</ul>';
	if (isset($_POST['affichage']))
		$first = empty($_POST['affichage']);
	else
		$first = true;
	$second = 'checked';
	$third = checked('affichage','1');
	print '
	</ul>
	</fieldset>
	<fieldset class="affichage"><legend>Affichage</legend>
	<input '.checked('photos','photos').' type="checkbox" name="photos" value="photos" >Avec Photos</input>
	<input '.($first ? $second : $third ).' type="radio" name="affichage" value="1" >Simple</input>
	<input '.checked('affichage','2').' type="radio" name="affichage" value="2" >Complet</input>
	<input '.checked('affichage','3').' type="radio" name="affichage" value="3" >Trombino</input>
	</fieldset>
	<fieldset class="buttons">
	<input type="submit" value="Chercher" />
	<button type="reset" id="reset">Remettre � z�ro</button>
	</fieldset>
	</form>';
if(isset($_POST['action']) && $_POST['action']==="submitted")
{
	$sql = "SELECT DISTINCT ADR.* FROM {$GLOBALS['prefix_db']}adherent ADR WHERE true";
	if (isset($_POST['sans_certif']))	// Si case sans certif coch�
		$sql .= ' AND certmed != 1';
	if (isset($_POST['sans_photo']))	// Si case sans photo coch�
		$sql .= ' AND photo != 1';
	if (isset($_POST['compte_inactif']))	// Si case compte inactif coch�
		$sql .= ' AND active = 0';
	if (isset($_POST['choix_statut_type']) && !(multiselected('choix_statut_type','0')))	// Si filtre par statut s�lectionn�
	{
		$sql .= ' AND (0';
		$statuts_choisis = $_POST['choix_statut_type'];
		foreach($statuts_choisis AS $key=>$value)
			$sql .= " OR id_statut = $value";
		$sql .= ')';
	}
	for($i = 0; $i < $_POST['field_count']; $i++)
	{
		$n = $i + 1;
		$type="set".$n."_type";
		$action="set".$n."_action";
		$text="set".$n."_text";
		if(empty($_POST[$text]))
			continue;
		switch($_POST[$type])
		{
			case 1: //Nom
					$sql .= " AND ADR.nom";
					break;
			case 2: //Pr�nom
					$sql .= " AND ADR.prenom";
					break;
			case 3: //email
					$sql .= " AND ADR.email";
					break;
			case 4: //Cat�gorie
					$sql .= " AND ADR.categorie";
					break;
			case 5: //Code postal
					$sql .= " AND ADR.code_postal";
					break;
		}
		switch($_POST[$action])
		{
			case 1: //Contient
				$sql .= " LIKE '%".$_POST[$text]."%'";
				break;
			case 2: //commence
				$sql .= " LIKE '".$_POST[$text]."%'";
				break;
			case 3: //est
				$sql .= "= '".$_POST[$text]."'";;
				break;
		}
	}
	$in="('0'";
	$i=0;
	foreach($tab as $section)
	{
		foreach($section['activites'] as $activite){
			foreach($activite['creneaux'] as $creneau){
				if(!empty($_POST['cre'.$creneau['id']])){
				 	$in.=",'".$creneau['id']."' ";
					$i++;
				}
			}
		}
	}
	$in.=" ) ";
	if ($_POST['choix_association_type'] != 0)
		$sql .= " AND ADR.id IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}adhesion ADS WHERE ADS.id_asso={$_POST['choix_association_type']} AND ADS.promo={$promo})";
	if ($i == 0 && $resp_asso)	// Utilis� pour recherche des gens sans adh�sion
		$sql .= " AND ADR.id NOT IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}adhesion ADS WHERE ADS.statut=0 AND ADS.promo={$promo})";
	else
	{
		$sql .= " AND ( false";
		if (!isset($_POST['exclure_adhs'])) // Utilis� pour la recherche classique sur les cr�neaux s�lectionn�s
			$sql .= " OR (ADR.id IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}adhesion ADS WHERE ADS.statut=0 AND ADS.promo={$promo} AND ADS.id_cre IN {$in}))";
		if (isset($_POST['responsable_asso']) || isset($_POST['responsable_sect']) || isset($_POST['responsable_acti']) || isset($_POST['responsable_cren']))
		{	
			$sql .= " OR (ADR.id IN (
				SELECT ADH.id id_adh
				FROM {$GLOBALS['prefix_db']}activite AC, {$GLOBALS['prefix_db']}creneau CR, {$GLOBALS['prefix_db']}section S, {$GLOBALS['prefix_db']}association A, {$GLOBALS['prefix_db']}asso_section HS , {$GLOBALS['prefix_db']}adherent ADH
				WHERE CR.id_act=AC.id
				AND AC.id_sec=S.id
				AND A.id=HS.id_asso
				AND HS.id_sec=S.id
				AND CR.id IN $in
				AND (0 ";
			if (isset($_POST['responsable_asso']))
				$sql .= " OR A.id  IN (SELECT id_asso FROM {$GLOBALS['prefix_db']}resp_asso    RA WHERE RA.id_adh=ADH.id)";
			if (isset($_POST['responsable_sect']))
				$sql .= " OR S.id  IN (SELECT id_sec  FROM {$GLOBALS['prefix_db']}resp_section RS WHERE RS.id_adh=ADH.id)";
			if (isset($_POST['responsable_acti']))
				$sql .= " OR AC.id IN (SELECT id_act  FROM {$GLOBALS['prefix_db']}resp_act     RT WHERE RT.id_adh=ADH.id)";
			if (isset($_POST['responsable_cren']))
				$sql .= " OR CR.id IN (SELECT id_cre  FROM {$GLOBALS['prefix_db']}resp_cren    RC WHERE RC.id_adh=ADH.id)";
			$sql .= " )))";
		}
		$sql .= " ) ORDER BY ADR.nom";
	}
	$tab = getChampsAdherents();
	include("opendb.php");
	$results = mysql_query($sql);
	if (!$results) echo mysql_error();
	include("closedb.php");
	$num = mysql_num_rows($results);
	switch($_POST['affichage']){
		case 1: //Simple
			print '<table class="search_results" ><FORM name="all_results" action="index.php?page=10" method="POST">';
			print '<thead><tr><th><input type="button" value="tout" onclick="javascript:cocheToute(1);" /></th><th><input type="button" value="rien" onclick="javascript:cocheToute(0);" /></th><th>Fiche</th><th>Solde</th>';
			foreach($tab as $champ)	// T�te du tableau d'affichage des r�sultats
			{
				if ($champ['search_simple']==1)
				{
					if($champ['type']==='varchar')
						print '<th>'.$champ['description'].'</th>';
					else
						if($champ['type']==='date')
							print '<th>'.$champ['description'].'</th>';
					else
						if($champ['type']==='tinyint')
							print '<th>'.$champ['description'].'</th>';
					else
						if($champ['type']==='file' && isset($_POST['photos']) && $_POST['photos']==='photos')
							print '<th>'.$champ['description'].'</th>';
				}
			}
			print '<th>Association</th></tr></thead>';
			print '<tbody>';
			$i = 0;
			while($row = mysql_fetch_array($results))
			{
				if($row['active']==='0')
					$active=' red';
				else
					$active='';
				$stop = false;
				switch ($_POST['select_solde']) // Une ligne du tableau (adh�rent)
				{
					case 1: //indiff�rent
						break;
					case 2: //positif
						if(!(getSolde($row['id'],$promo) > 0))
							$stop = true;
						break;
					case 3: //n�gatif
						if(!(getSolde($row['id'],$promo) < 0))
							$stop = true;
						break;
					case 4: //null
						if(!(getSolde($row['id'],$promo) == 0))
							$stop = true;
						break;
				}
				if($stop)
					continue;
				$i++;
				if($i % 2 == 0)
					print '<tr class="'.$active.'">';
				else
					print '<tr class="odd '.$active.'">';
				print '<td>'.$i.'</td><td><input type="checkbox" class="adh" name="adh[]" value="'.$row['id'].'" ></td><td><a href="index.php?page=1&adh='.$row['id'].'"><img src="images/file.gif" height=25 ></a></td><td>'.getSolde($row['id'],$promo).'</td>';
				foreach($tab as $champ)
				{
					if ($champ['search_simple']==1){
						if($champ['type']==='varchar')
							print '<td>'.$row[$champ['nom']].'</td>';
						else
						if($champ['type']==='date')
							print '<td>'.$row[$champ['nom']].'</td>';
						else
						if($champ['type']==='tinyint')
							print '<td>'.$row[$champ['nom']].'</td>';
						else
						if($champ['type']==='file' && isset( $_POST['photos']) && $_POST['photos']==='photos'){
							$_SESSION['auth_thumb']='true';
							$photo="includes/thumb.php?folder=".$champ['nom']."&file=".$row['email'].".jpg";
							print '<TD><img src="'.$photo.'" height="70"></TD>';
						}
					}
				}
				$res = doQuery("SELECT DISTINCT id_asso, nom FROM {$GLOBALS['prefix_db']}adhesion a INNER JOIN {$GLOBALS['prefix_db']}association b ON a.id_asso=b.id WHERE promo={$promo} AND id_adh={$row['id']}");
				print "<td>";
				$output = "";
				while ($tmp_array = mysql_fetch_array($res)) $output .= ", ".$tmp_array['nom'];
				$output[0] = "";
				print $output;
				print '</td></tr>';
			}
			print '</tbody>';
			print '</table>';
			print '<SELECT name="action" >
			      	       <OPTION value="sendmail">Envoyer Email</OPTION>
				</SELECT>';
			print '<input type="submit" value="Go"></input></FORM>';
		break;
		case 2: //Complet			
			print '<table class="search_results" ><FORM name="all_results" action="index.php?page=10" method="POST">';
			print '<thead><tr><th><input type="button" value="tout" onclick="javascript:cocheToute(1);" /></th><th><input type="button" value="rien" onclick="javascript:cocheToute(0);" /></th><th>Fiche</th><th>Solde</th>';
			foreach($tab as $champ)
			{
				if ($champ['user_viewable']==1)
				{
					if($champ['type']==='varchar')
						print '<th>'.$champ['description'].'</th>';
					else
						if($champ['type']==='date')
							print '<th>'.$champ['description'].'</th>';
					else
						if($champ['type']==='tinyint')
							print '<th>'.$champ['description'].'</th>';
					else
						if($champ['type']==='file' && isset($_POST['photos']) && $_POST['photos']==='photos')
							print '<th>'.$champ['description'].'</th>';
				}
			}
			print '</tr></thead>';
			print '<tbody>';
			$i = 0;
			while($row = mysql_fetch_array($results))
			{
				$stop = false;
				switch ($_POST['select_solde'])
				{
					case 1: //indiff�rent
						break;
					case 2: //positif
						if(!(getSolde($row['id'],$promo) > 0))
							$stop=true;
						break;
					case 3: //n�gatif
						if(!(getSolde($row['id'],$promo) < 0))
							$stop=true;
						break;
					case 4: //nul
						if(!(getSolde($row['id'],$promo) == 0))
							$stop=true;
						break;
				}
				if($stop) continue;
				if($row['active']==='0') $active=' red';
				else $active='';
				$i++;
				if($i % 2 == 0) print '<tr class="'.$active.'">';
				else print '<tr class="odd '.$active.'">';
				print '<td>'.$i.'</td><td><input type="checkbox" name="adh[]" value="'.$row['id'].'" ></td><td><a href="index.php?page=1&adh='.$row['id'].'"><img src="images/file.gif" height=25 ></a></td><td>'.getSolde($row['id'],$promo).'</td>';
				foreach($tab as $champ){
					if ($champ['user_viewable']==1){
						if($champ['type']==='varchar')
							print '<td>'.$row[$champ['nom']].'</td>';
						else
						if($champ['type']==='date')
							print '<td>'.$row[$champ['nom']].'</td>';
						else
						if($champ['type']==='tinyint')
							print '<td>'.$row[$champ['nom']].'</td>';
						else
						if($champ['type']==='file' && isset($_POST['photos']) && $_POST['photos']==='photos'){
							$_SESSION['auth_thumb']='true';
							$photo="includes/thumb.php?folder=".$champ['nom']."&file=".$row['email'].".jpg";
							print '<TD><img src="'.$photo.'" height="70"></TD>';
						}
					}
				}
				print '</tr>';
			}
			print '</tbody>';
			print '</table>';
			print '<SELECT name="action" >
					<OPTION value="sendmail" > Envoyer Email</OPTION>
			       </SELECT>';
			print '<input type="submit" value="Go"></FORM>';
		break;
		case 3: //Trombino
			$i=0;
			print '<table>';
			while($row = mysql_fetch_array($results))
			{
				$stop = false;
				switch ($_POST['select_solde'])
				{
					case 1: //indiff�rent
						break;
					case 2: //positif
						if(!(getSolde($row['id'],$promo) > 0))
							$stop=true;
						break;
					case 3: //n�gatif
						if(!(getSolde($row['id'],$promo) < 0))
							$stop=true;
						break;
					case 4: //nul
						if(!(getSolde($row['id'],$promo) == 0))
							$stop=true;
						break;
				}
				if($stop) continue;
				if($i % 5 == 0) print '<tr>';
				$i++;
				print '<td class="trombi" valign="top">';
				foreach($tab as $champ){
					if ($champ['search_trombi']==1){
						if($champ['type']==='varchar')
							print '<span class="trombi">'.(empty($row[$champ['nom']]) ? '<br>' : $row[$champ['nom']]).'</span>';
						else
						if($champ['type']==='date')
							print '<span class="trombi">'.(empty($row[$champ['nom']]) ? '<br>' : $row[$champ['nom']]).'</span>';
						else
						if($champ['type']==='tinyint')
							print '<span class="trombi">'.(empty($row[$champ['nom']]) ? '<br>' : $row[$champ['nom']]).'</span>';
						else
						if($champ['type']==='file'){
							$_SESSION['auth_thumb']='true';
							$photo="includes/thumb.php?folder=".$champ['nom']."&file=".$row['email'].".jpg";
							print '<span class="trombi"><img src="'.$photo.'" ></span>';
						}
					}
				}
				print '</td>';
				if($i % 5 == 0) print '</tr>';
			}
			print '</table>';
		break;
	}	
}
?>

<script type="text/javascript">
$('#add_field').click(function() {
	var nr_of_field = parseInt($('#f_search [name=field_count]').val()) + 1;
	$('#filters').append('<div id="set'+nr_of_field+'"><select id="set'+nr_of_field+'_type" name="set'+nr_of_field+'_type"><option label="Nom" value="1">Nom</option><option label="Pr�nom" value="2">Pr�nom</option><option label="Email" value="3">Email</option><option label="Cat�gorie" value="4">Cat�gorie</option></select> <select id="set'+nr_of_field+'_action" name="set'+nr_of_field+'_action" ><option label="Contient" value="1">Contient</option><option label="Commence" value="2">Commence</option><option label="Est" value="3">Est</option></select> <input type="text" id="set'+nr_of_field+'_text" name="set'+nr_of_field+'_text"/></div>');
	$('#f_search [name=field_count]').val(nr_of_field)
});
$('#reset').click(function() {
	    $('#filters').children().remove();
		$('#f_search [name=field_count]').val(1)
});
$('#tree_root').checkboxTree({
      /* specify here your options */
      initializeChecked: 'expanded', 
      initializeUnchecked: 'collapsed',
      onCheck: {
                descendants: 'check',
	        node: 'expand',
      },
      onUncheck: {
                  ancestors: 'uncheck',
	          node: 'collapse',
      }, 
});
$("#toggle_f_search").click(function () {
      $("#f_search").slideToggle("fast");
});

function cocheToute(value){
   var taille = document.forms['all_results'].elements.length;
   var element = null;
   for(i=0; i < taille; i++)
   {
		element = document.forms['all_results'].elements[i];
		if(element.type == "checkbox")
		{
			if (value == 1)
				element.checked = true;
			else
				element.checked = false;
		}
	}
}
</script>

<?php
//fin else connexion
}
}
?>