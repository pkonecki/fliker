<?php session_start();
if((strcmp($_SESSION['user'],"") == 0)){
	print "<p>Vous n'êtes pas connecté</p>";
}
else {
	if($_SESSION['privilege']==1){
		$admin=true;
		$id_adh=$_GET['adh'];
		$resp_asso=true;
	}
	else {
		if(count(getMyAssos($_SESSION['uid'])) > 0 ) {
			$resp_asso=true;
		}
	}
	$query="SELECT * FROM {$GLOBALS['prefix_db']}resp_act  WHERE id_adh='".$_SESSION[uid]."'
	UNION
	SELECT * FROM {$GLOBALS['prefix_db']}resp_cren  WHERE id_adh='".$_SESSION[uid]."'
 	UNION
 	SELECT * FROM {$GLOBALS['prefix_db']}resp_section  WHERE id_adh='".$_SESSION[uid]."'
 	UNION
	SELECT * FROM {$GLOBALS['prefix_db']}resp_asso  WHERE id_adh='".$_SESSION[uid]."' ";
	include("opendb.php");
	$results = mysql_query($query);
	include("closedb.php");
	if (!$results) echo mysql_error();
	else if (mysql_num_rows($results)==0 AND $_SESSION['privilege']!='1'){
		print 'Vous n\'avez pas accès à cette page.';
	}
	else{
?>
<div>
<h2 class="inline">Recherche</h2>
<img src="images/downarrow.gif" class="inline" id="toggle_f_search" ></img>
</div>
<?php
print "<span class=\"tip\">".getParam('text_search')."</span>";

function selected($post,$val){
	if ($_POST[$post]===$val) {
		return "selected";
	}
	else return "";
}

function checked($post,$val){
	if ($_POST[$post]===$val) {
		return "checked";
	}
	else return "";
}

function multiselected($post,$val){
	for($i=0; $i < sizeof($_POST[$post]);$i++){
		if($_POST[$post][$i]===$val) return "selected";
	}
	return "";
}

	//print_r($_POST);
	if (empty($_POST['field_count'])) $_POST['field_count']=1;
	//print '<button id="toggle_f_search">Toggle</button>';
	print '<form id="f_search" method="post" action="index.php?page=2">
<fieldset class="main"><legend>Critères Adhérent</legend>
	<input type="hidden" name="field_count" value="'.$_POST['field_count'].'" />
	<input type="hidden" name="action" value="submitted" />
	<div id="solde">
		<label for="select_solde">Solde est</label>
		<select id="select_solde" name="select_solde">
			<option '.selected('select_solde','1').' value="1">Indiff&eacute;rent</option>
			<option '.selected('select_solde','2').' value="2">Positif</option>
			<option '.selected('select_solde','3').' value="3">N&eacute;gatif</option>
			<option '.selected('select_solde','4').' value="4">Nul</option>
		</select>
	</div>
	<div id="responsable">
		<dt><input type="checkbox" '.checked('exclure_adhs','1').' name="exclure_adhs" value="1">Exclure les simples adhérents</input></dt>
		<dt><input type="checkbox" '.checked('responsable','1').' name="responsable" value="1">Inclure responsables des sections (comité directeur)</input></dt>
		<dt><input type="checkbox" '.checked('responsable_asso','1').' name="responsable_asso" value="1">Inclure responsables d\'association (bureau)</input></dt>
	</div>
	<div id="set1">
	<select id="set1_type" name="set1_type">
		<option '.selected('set1_type','1').' label="Nom" value="1">Nom</option>
		<option '.selected('set1_type','2').' label="Pr&eacute;nom" value="2">Pr&eacute;nom</option>
		<option '.selected('set1_type','3').' label="Email" value="3">Email</option>
		<option '.selected('set1_type','4').' label="Cat&eacute;gorie" value="4">Cat&eacute;gorie</option>
	</select>
	<select id="set1_action" name="set1_action">
		<option '.selected('set1_action','1').' label="Contient" value="1">Contient</option>
		<option '.selected('set1_action','2').' label="Commence" value="2">Commence</option>
		<option '.selected('set1_action','3').' label="Est" value="3">Est</option>
	</select>
	<input type="text" id="set1_text" name="set1_text" value="'.$_POST['set1_text'].'"/>
	</div>
	<div id="filters">';
	for($i = 1; $i < $_POST['field_count']; $i++){
		$n=$i+1;
		$type="set".$n."_type";
		$action="set".$n."_action";
		$text="set".$n."_text";
		print '<div id="set'.$n.'">
	<select id="set'.$n.'_type" name="set'.$n.'_type">
		<option '.selected($type,'1').' label="Nom" value="1">Nom</option>
		<option '.selected($type,'2').' label="Prénom" value="2">Prénom</option>
		<option '.selected($type,'3').' label="Email" value="3">Email</option>
		<option '.selected($type,'4').' label="Catégorie" value="4">Catégorie</option>
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
<fieldset class="selects"><legend>Sélection des créneaux</legend>
	<ul id="tree_root">
		<li><input type="checkbox" name="sections" '.checked('sections','sections').' value="sections" ><label>Tout</label>
			<ul id="sections"  >
	';
	$creneaux=getCreneaux($_SESSION['uid']);
	$tab=array();
	foreach($creneaux as $creneau){
		$tab[$creneau[id_sec]][nom]=$creneau[nom_sec];
		$tab[$creneau[id_sec]][id]=$creneau[id_sec];
		$tab[$creneau[id_sec]][activites][$creneau[id_act]][nom]=$creneau[nom_act];
		$tab[$creneau[id_sec]][activites][$creneau[id_act]][id]=$creneau[id_act];
		$tab[$creneau[id_sec]][activites][$creneau[id_act]][creneaux][$creneau[id_cre]][jour]=$creneau[jour_cre];
		$tab[$creneau[id_sec]][activites][$creneau[id_act]][creneaux][$creneau[id_cre]][id]=$creneau[id_cre];
		$tab[$creneau[id_sec]][activites][$creneau[id_act]][creneaux][$creneau[id_cre]][debut]=$creneau[debut_cre];
	}
	foreach($tab as $section){
		print '<li><input type="checkbox" name="section'.$section[id].'" '.checked('section'.$section[id],$section[id]).' value="'.$section[id].'"><label>'.$section[nom].'</label>';
		print '<ul id="activites">';
		foreach($section[activites] as $act){
			print '<li><input type="checkbox" name="act'.$act[id].'" '.checked('act'.$act[id],$act[id]).' value="'.$act[id].'"><label>'.$act[nom].'</label>';
			print '<ul id="creneaux">';
			foreach($act[creneaux] as $cre){
				print '<li><input type="checkbox" name="cre'.$cre[id].'" '.checked('cre'.$cre[id],$cre[id]).' value="'.$cre[id].'"><label>'.$cre[jour].' - '.substr($cre[debut],0,-3).'</label>';
			}
			print '</ul>';
		}
		print '</ul>';
	}
	print '</ul>';
	$first = empty($_POST[affichage]);
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
	<input type="submit" />
	<button type="reset" id="reset">Reset</button>
</fieldset>
</form>';
	if($_POST['action']==="submitted"){
	//SQL
	$sql = "SELECT DISTINCT ADR.* FROM {$GLOBALS['prefix_db']}adherent ADR WHERE true ";
	for($i = 0; $i < $_POST['field_count']; $i++){
		$n=$i+1;
		$type="set".$n."_type";
		$action="set".$n."_action";
		$text="set".$n."_text";
		if(empty($_POST[$text])) continue;
		switch($_POST[$type]){
			case 1: //Nom
					$sql.="AND ADR.nom ";
					break;
			case 2: //Prénom
					$sql.="AND ADR.prenom ";
					break;
			case 3: //email
					$sql.="AND ADR.email ";
					break;
			case 4: //Catégorie
					$sql.="AND ADR.categorie ";
					break;
		}
		switch($_POST[$action]){
			case 1: //Contient
					$sql.="LIKE '%".$_POST[$text]."%' ";
				break;
			case 2: //commence
				$sql.="LIKE '".$_POST[$text]."%' ";
				break;
			case 3: //est
				$sql.="= '".$_POST[$text]."' ";;
				break;
		}
	}
	$in="('0'";
	$i=0;
	foreach($tab as $section){
		foreach($section[activites] as $activite){
			foreach($activite[creneaux] as $creneau){
				
				if(!empty($_POST['cre'.$creneau[id]])){
				 	$in.=",'".$creneau[id]."' ";
					$i++;
				}
			}
		}
	}
	$in.=" ) ";
	if ($i==0 && $resp_asso) {
		$sql.=" AND ADR.id NOT IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}adhesion ADS WHERE ADS.statut=0 AND ADS.promo=$current_promo )";
	} else{
	$sql.="AND ( false ";
	if (!isset($_POST['exclure_adhs'])) $sql.="OR (ADR.id IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}adhesion ADS WHERE ADS.statut=0 AND ADS.id_cre IN $in  ) )";
	if (isset($_POST['responsable_asso']) || isset($_POST['responsable'])){	
		$sql.="OR (ADR.id IN (
			SELECT  ADH.id id_adh
			FROM {$GLOBALS['prefix_db']}activite AC, {$GLOBALS['prefix_db']}creneau CR, {$GLOBALS['prefix_db']}section S, {$GLOBALS['prefix_db']}association A, {$GLOBALS['prefix_db']}asso_section HS , {$GLOBALS['prefix_db']}adherent ADH
			WHERE CR.id_act=AC.id
			AND AC.id_sec=S.id
			AND A.id=HS.id_asso
			AND HS.id_sec=S.id
			AND CR.id IN $in
			AND (";
		if (isset($_POST['responsable_asso'])) {
				$sql.= "A.id IN (SELECT id_asso FROM {$GLOBALS['prefix_db']}resp_asso RA WHERE RA.id_adh=ADH.id)";
				if (isset($_POST['responsable']))$sql.=" OR ";		
		}
		if (isset($_POST['responsable']))  $sql.= "S.id IN (SELECT id_sec FROM {$GLOBALS['prefix_db']}resp_section RA WHERE RA.id_adh=ADH.id )
				OR
				AC.id IN (SELECT id_act FROM {$GLOBALS['prefix_db']}resp_act RA WHERE RA.id_adh=ADH.id )
				OR
				CR.id IN (SELECT id_cre FROM {$GLOBALS['prefix_db']}resp_cren RA WHERE RA.id_adh=ADH.id )
				";
		$sql.=")
			 ) )";
	}
	$sql.=" ) ORDER BY ADR.nom";
	}
	//print $sql;
	$tab = getChampsAdherents();
	include("opendb.php");
	$results = mysql_query($sql);
	if (!$results) echo mysql_error();
	include("closedb.php");
	$num=mysql_num_rows($results);
	switch($_POST['affichage']){
		case 1: //Simple
			print '<table class="search_results" ><FORM action="index.php?page=10" method="POST">';
			print '<thead><tr><th><input type="checkbox" id="select_all" /></th><th>Fiche</th><th>Solde</th>';
			foreach($tab as $champ){
				if ($champ[search_simple]==1) {
					if($champ[type]==='varchar')
					print '<th>'.$champ['description'].'</th>';
					else
					if($champ[type]==='date')
					print '<th>'.$champ['description'].'</th>';
					else
					if($champ[type]==='tinyint')
					print '<th>'.$champ['description'].'</th>';
					else
					if($champ[type]==='file' && $_POST['photos']==='photos'){
					print '<th>'.$champ['description'].'</th>';
					}
				}
			}
			print '</tr></thead>';
			print '<tbody>';
			$i = 0;
			while($row = mysql_fetch_array($results)){
				if($row['active']==='0') $active=' red';
				else $active='';
				$stop = false;
				switch ($_POST['select_solde']){
					case 1:
					//indifférent
					break;
					case 2:
					//positif
					if(!(getSolde($row['id'],$current_promo)>0)) $stop=true;
					break;
					case 3:
					//négatif
					if(!(getSolde($row['id'],$current_promo)<0)) $stop=true;
					break;
					case 4:
					//nul
					if(!(getSolde($row['id'],$current_promo)==0)) $stop=true;
					break;
				}
				if($stop) continue;
				$i++;
				if($i % 2 == 0) print '<tr class="'.$active.'">';
				else print '<tr class="odd '.$active.'">';
				print '<td><input type="checkbox" class="adh" name="adh[]" value="'.$row['id'].'" ></td><td><a href="index.php?page=1&adh='.$row['id'].'"><img src="images/file.gif" height=25 ></a></td><td>'.getSolde($row['id'],$current_promo).'</td>';
				foreach($tab as $champ){
					if ($champ[search_simple]==1){
						if($champ[type]==='varchar')
							print '<td>'.$row[$champ['nom']].'</td>';
						else
						if($champ[type]==='date')
							print '<td>'.$row[$champ['nom']].'</td>';
						else
						if($champ[type]==='tinyint')
							print '<td>'.$row[$champ['nom']].'</td>';
						else
						if($champ[type]==='file' && $_POST['photos']==='photos'){
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
					<OPTION value="sendmail">Envoyer Email</OPTION>
					</SELECT>';
			print '<input type="submit" value="GO"></input></FORM>';
		break;
		case 2: //Complet			
			print '<table class="search_results" ><FORM action="index.php?page=10" method="POST">';
			print '<thead><tr><th>Fiche</th><th>Solde</th>';
			foreach($tab as $champ){
				if ($champ[user_viewable]==1) {
					if($champ[type]==='varchar')
					print '<th>'.$champ['description'].'</th>';
					else
					if($champ[type]==='date')
					print '<th>'.$champ['description'].'</th>';
					else
					if($champ[type]==='tinyint')
					print '<th>'.$champ['description'].'</th>';
					else
					if($champ[type]==='file' && $_POST['photos']==='photos'){
					print '<th>'.$champ['description'].'</th>';
					}
				}
			}
			print '</tr></thead>';
			print '<tbody>';
			$i = 0;
			while($row = mysql_fetch_array($results)){
				$stop = false;
				switch ($_POST['select_solde']){
					case 1:
					//indifférent
					break;
					case 2:
					//positif
					if(!(getSolde($row['id'],$current_promo)>0)) $stop=true;
					break;
					case 3:
					//négatif
					if(!(getSolde($row['id'],$current_promo)<0)) $stop=true;
					break;
					case 4:
					//nul
					if(!(getSolde($row['id'],$current_promo)==0)) $stop=true;
					break;
				}
				if($stop) continue;
				if($row['active']==='0') $active=' red';
				else $active='';
				$i++;
				if($i % 2 == 0) print '<tr class="'.$active.'">';
				else print '<tr class="odd '.$active.'">';
				print '<td><input type="checkbox" name="adh[]" value="'.$row['id'].'" ></td><td><a href="index.php?page=1&adh='.$row['id'].'"><img src="images/file.gif" height=25 ></a></td><td>'.getSolde($row['id'],$current_promo).'</td>';
				foreach($tab as $champ){
					if ($champ[user_viewable]==1){
						if($champ[type]==='varchar')
							print '<td>'.$row[$champ['nom']].'</td>';
						else
						if($champ[type]==='date')
							print '<td>'.$row[$champ['nom']].'</td>';
						else
						if($champ[type]==='tinyint')
							print '<td>'.$row[$champ['nom']].'</td>';
						else
						if($champ[type]==='file' && $_POST['photos']==='photos'){
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
			while($row = mysql_fetch_array($results)){
				$stop = false;
				switch ($_POST['select_solde']){
					case 1:
					//indifférent
					break;
					case 2:
					//positif
					if(!(getSolde($row['id'],$current_promo)>0)) $stop=true;
					break;
					case 3:
					//négatif
					if(!(getSolde($row['id'],$current_promo)<0)) $stop=true;
					break;
					case 4:
					//nul
					if(!(getSolde($row['id'],$current_promo)==0)) $stop=true;
					break;
				}
				if($stop) continue;
				if($i % 5 == 0) print '<tr>';
				$i++;
				print '<td class="trombi" valign="top">';
				foreach($tab as $champ){
					if ($champ[search_trombi]==1){
						if($champ[type]==='varchar')
							print "<span class=\"trombi\">".(empty($row[$champ['nom']]) ? "<br>" : $row[$champ['nom']]).'</span>';
						else
						if($champ[type]==='date')
							print '<span class="trombi">'.(empty($row[$champ['nom']]) ? "<br>" : $row[$champ['nom']]).'</span>';
						else
						if($champ[type]==='tinyint')
							print '<span class="trombi">'.(empty($row[$champ['nom']]) ? "<br>" : $row[$champ['nom']]).'</span>';
						else
						if($champ[type]==='file'){
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
	$('#filters').append('<div id="set'+nr_of_field+'"><select id="set'+nr_of_field+'_type" name="set'+nr_of_field+'_type"><option label="Nom" value="1">Nom</option><option label="Prénom" value="2">Prénom</option><option label="Email" value="3">Email</option><option label="Catégorie" value="4">Catégorie</option></select> <select id="set'+nr_of_field+'_action" name="set'+nr_of_field+'_action" ><option label="Contient" value="1">Contient</option><option label="Commence" value="2">Commence</option><option label="Est" value="3">Est</option></select> <input type="text" id="set'+nr_of_field+'_text" name="set'+nr_of_field+'_text"/></div>');
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
                ancestors: 'checkIfFull', 
                descendants: 'check',
                node: 'expand'
      },
      onUncheck: {
                  ancestors: 'uncheck',
                  node: 'collapse'
      }, 
});
$("#toggle_f_search").click(function () {
      $("#f_search").slideToggle("fast");
});
</script>


<?php
//fin else connexion
}
}
?>