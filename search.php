<?php session_start();
if((strcmp($_SESSION['user'],"") == 0)){
	print "<p>Vous n'êtes pas connecté</p>";
}
else {
	$query="SELECT * FROM resp_act  WHERE id_adh='".$_SESSION[uid]."'
	UNION
	SELECT * FROM resp_cren  WHERE id_adh='".$_SESSION[uid]."'
 	UNION
 	SELECT * FROM resp_section  WHERE id_adh='".$_SESSION[uid]."'
 	UNION
	SELECT * FROM resp_asso  WHERE id_adh='".$_SESSION[uid]."' ";
	include("opendb.php");
	$results = mysql_query($query);
	include("closedb.php");
	if (!$results) echo mysql_error();
	else if (mysql_num_rows($results)==0 AND $_SESSION['privilege']!='1'){
		print 'Vous n\'avez pas accès à cette page.';
	}
	
	else{



?>
<h2 id="toggle_f_search">Recherche</h2>

<?php

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

include("Creneau.php");
include("Adherent.php");

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
			<option '.selected('select_solde','1').' label="Indiff&eacute;rrent" value="1">Indiff&eacute;rrent</option>
			<option '.selected('select_solde','2').' label="Positif" value="2">Positif</option>
			<option '.selected('select_solde','3').' label="N&eacute;gatif" value="3">N&eacute;gatif</option>
			<option '.selected('select_solde','4').' label="Nul" value="4">Nul</option>
		</select>
	</div>
	<div id="set1">
	<select id="set1_type" name="set1_type">
		<option '.selected('set1_type','1').' label="Nom" value="1">Nom</option>
		<option '.selected('set1_type','2').' label="Pr&eacute;bom" value="2">Pr&eacute;nom</option>
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
		<option '.selected($type,'2').' label="Pr&eacute;bom" value="2">Pr&eacute;nom</option>
		<option '.selected($type,'3').' label="Email" value="3">Email</option>
		<option '.selected($type,'4').' label="Cat&eacute;gorie" value="4">Cat&eacute;gorie</option>
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
		<li><input type="checkbox" name="sections" '.checked('sections','sections').' value="sections" ><label>Sections</label>
			<ul id="sections"  >
	';
	$creneaux=getCreneaux($_SESSION['uid']);
	$tab=array();
	foreach($creneaux as $creneau){
		$tab[$creneau[nom_sec]][nom]=$creneau[nom_sec];
		$tab[$creneau[nom_sec]][id]=$creneau[id_sec];
		$tab[$creneau[nom_sec]][activites][$creneau[nom_act]][nom]=$creneau[nom_act];
		$tab[$creneau[nom_sec]][activites][$creneau[nom_act]][id]=$creneau[id_act];
		$tab[$creneau[nom_sec]][activites][$creneau[nom_act]][creneaux][$creneau[id_cre]][jour]=$creneau[jour_cre];
		$tab[$creneau[nom_sec]][activites][$creneau[nom_act]][creneaux][$creneau[id_cre]][id]=$creneau[id_cre];
		$tab[$creneau[nom_sec]][activites][$creneau[nom_act]][creneaux][$creneau[id_cre]][debut]=$creneau[debut_cre];
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
	$sql = "SELECT DISTINCT ADR.* FROM adherent ADR ,adhesion ADS, creneau CR WHERE true ";
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
	$sql.=" AND ADS.id_cre IN ('0'";
	foreach($tab as $section){
		foreach($section[activites] as $activite){
			foreach($activite[creneaux] as $creneau){
				 if(!empty($_POST['cre'.$creneau[id]])) $sql.=",'".$creneau[id]."' ";
			}
		}
	}
	
	$sql.=" ) AND  ADR.id=ADS.id_adh AND ADS.id_cre=CR.id ORDER BY ADR.nom";
	//print $sql;
	$tab = getChampsAdherents();
	include("opendb.php");
	
	$results = mysql_query($sql);
	if (!$results) echo mysql_error();
	include("closedb.php");
	$num=mysql_num_rows($results);

	switch($_POST['affichage']){
		case 1: //Simple
			print '<table class="search_results" >';
			print '<thead><tr>';
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
				$i++;
				if($i % 2 == 0) print '<tr>';
				else print '<tr class="odd">';
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
		break;
		case 2: //Complet			
			print '<table class="search_results" >';
			print '<thead><tr>';
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
				$i++;
				if($i % 2 == 0) print '<tr>';
				else print '<tr class="odd">';
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
		break;
		case 3: //Trombino
			$i=0;
			print '<table>';
			while($row = mysql_fetch_array($results)){
				
				
				if($i % 5 == 0) print '<tr>';
				$i++;
				print '<td>';
				foreach($tab as $champ){
					
					if ($champ[search_trombi]==1){
						if($champ[type]==='varchar')
							print '<span class="trombi">'.$row[$champ['nom']].'</span>';
						else
						if($champ[type]==='date')
							print '<span class="trombi">'.$row[$champ['nom']].'</span>';
						else
						if($champ[type]==='tinyint')
							print '<span class="trombi">'.$row[$champ['nom']].'</span>';
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
      onCheck: {
                ancestors: 'checkIfFull',
                descendants: 'check'
            },
            onUncheck: {
                ancestors: 'uncheck'
            }
    });
$("#toggle_f_search").click(function () {
      $("#f_search").slideToggle("slow");
    });
</script>


<?php
//fin else connexion
}
}
?>