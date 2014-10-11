<?php

function newCreneau($tab){
	require_once("class.imageconverter.php");
	require_once("saveImage.php");
	include("opendb.php");
	//Nouvelle entité
	$q1 = "INSERT INTO {$GLOBALS['prefix_db']}entite VALUES ()";
	$r1 = mysql_query($q1);
	if (!$r1){ 
		echo mysql_error();
		die();
	}
	$id = mysql_insert_id();
	
	$set = "(";
	$colonnes="(id,jour,debut,fin,lieu,id_act)";
	//id
	$set.="'$id', ";
	//Jour
	$set.="'".mysql_real_escape_string($tab['jour_cre'])."', ";
	//Debut
	$set.="'".mysql_real_escape_string($tab['debut_cre'])."', ";
	//Fin
	$set.="'".mysql_real_escape_string($tab['fin_cre'])."', ";
	//Lieu
	$set.="'".mysql_real_escape_string($tab['lieu'])."', ";
	//Id_act
	$set.="'".mysql_real_escape_string($tab['id_act'])."') ";
	$query = "INSERT INTO {$GLOBALS['prefix_db']}creneau ".$colonnes." VALUES ".$set." ";
	//echo $query;
	$results = mysql_query($query);
	if (!$results){ 
		echo mysql_error();
		die();
	}
	include("closedb.php");

}

function delCreneau($id)
{
	include("opendb.php");
	if(!isset($id)) return;
	$query = "DELETE FROM {$GLOBALS['prefix_db']}entite WHERE id=".$id."";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}
function getCreneau($id){
	include("opendb.php");
	if(!isset($id)) return;
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}creneau WHERE id=$id ORDER BY CASE jour
                 WHEN 'Lundi' THEN 1 
                 WHEN 'Mardi' THEN 2 
                 WHEN 'Mercredi' THEN 3 
                 WHEN 'Jeudi' THEN 4 
                 WHEN 'Vendredi' THEN 5 
                 WHEN 'Samedi' THEN 6 
                 WHEN 'Dimanche' THEN 7 END, debut ASC";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$row = mysql_fetch_array($results);
	return $row;
	include("closedb.php");

}

function getCreneaux($userid)
{
	if(!empty($_SESSION['user']))
	{
		if($_SESSION['privilege']=="1")
		{
			$query = "SELECT BAT.nom AS batiment, SALLE.nom AS salle, A.id id_asso, A.nom nom_asso, S.id id_sec, S.nom nom_sec, AC.id id_act, AC.nom nom_act, CR.id id_cre, CR.jour jour_cre, CR.debut debut_cre, CR.fin fin_cre, CR.lieu lieu
						FROM {$GLOBALS['prefix_db']}activite AC, {$GLOBALS['prefix_db']}creneau CR, {$GLOBALS['prefix_db']}batiment BAT, {$GLOBALS['prefix_db']}salle SALLE, {$GLOBALS['prefix_db']}section S, {$GLOBALS['prefix_db']}association A, {$GLOBALS['prefix_db']}asso_section HS
						WHERE CR.id_act=AC.id
						AND AC.id_sec=S.id
						AND CR.lieu=SALLE.id AND SALLE.id_batiment=BAT.id
						AND A.id=HS.id_asso
						AND HS.id_sec=S.id 
						ORDER BY nom_sec, nom_act, CASE jour
                 WHEN 'Lundi' THEN 1 
                 WHEN 'Mardi' THEN 2 
                 WHEN 'Mercredi' THEN 3 
                 WHEN 'Jeudi' THEN 4 
                 WHEN 'Vendredi' THEN 5 
                 WHEN 'Samedi' THEN 6 
                 WHEN 'Dimanche' THEN 7 END, CR.debut";
		}
		else
		{
			if (!empty($userid))
			{
				$query = "SELECT BAT.nom AS batiment, SALLE.nom AS salle, A.id id_asso, A.nom nom_asso, S.id id_sec, S.nom nom_sec, AC.id id_act, AC.nom nom_act, CR.id id_cre, CR.jour jour_cre, CR.debut debut_cre , CR.fin fin_cre, CR.lieu lieu
						FROM {$GLOBALS['prefix_db']}activite AC, {$GLOBALS['prefix_db']}creneau CR, {$GLOBALS['prefix_db']}batiment BAT, {$GLOBALS['prefix_db']}salle SALLE, {$GLOBALS['prefix_db']}section S, {$GLOBALS['prefix_db']}association A, {$GLOBALS['prefix_db']}asso_section HS
						WHERE CR.id_act=AC.id
						AND AC.id_sec=S.id
						AND CR.lieu=SALLE.id AND SALLE.id_batiment=BAT.id
						AND A.id=HS.id_asso
						AND HS.id_sec=S.id
							AND
							(
							S.id IN (SELECT id_sec FROM {$GLOBALS['prefix_db']}resp_section WHERE id_adh = '$userid' AND promo = ".getParam('promo.conf').")
							OR AC.id IN (SELECT id_act FROM {$GLOBALS['prefix_db']}resp_act WHERE id_adh = '$userid' AND promo = ".getParam('promo.conf').")
							OR CR.id IN (SELECT id_cre FROM {$GLOBALS['prefix_db']}resp_cren WHERE id_adh = '$userid' AND promo = ".getParam('promo.conf').")
							OR A.id IN (SELECT id_asso FROM {$GLOBALS['prefix_db']}resp_asso WHERE id_adh = '$userid' AND promo = ".getParam('promo.conf').")
							/*OR CR.id IN (SELECT id_cre FROM adhesion WHERE id_adh = '$userid')*/
							)
							ORDER BY nom_sec, nom_act, CASE jour
                 WHEN 'Lundi' THEN 1 
                 WHEN 'Mardi' THEN 2 
                 WHEN 'Mercredi' THEN 3 
                 WHEN 'Jeudi' THEN 4 
                 WHEN 'Vendredi' THEN 5 
                 WHEN 'Samedi' THEN 6 
                 WHEN 'Dimanche' THEN 7 END, CR.debut";
			}
			else
				return;
		}


		include("opendb.php");
		$results = mysql_query($query);
		if (!$results) echo mysql_error();
		$tab = array();
		while($row = mysql_fetch_array($results)){
			$tab[$row['id_cre']] = $row;
		}
		include("closedb.php");
		return $tab;
	}
}

function getCreneauxByActivite($actid){
	if(!empty($_SESSION['user'])){
			if (!empty($actid)) {
				$query = "SELECT * FROM {$GLOBALS['prefix_db']}creneau A WHERE A.id_act= ".$actid." ORDER BY CASE jour
                 WHEN 'Lundi' THEN 1 
                 WHEN 'Mardi' THEN 2 
                 WHEN 'Mercredi' THEN 3 
                 WHEN 'Jeudi' THEN 4 
                 WHEN 'Vendredi' THEN 5 
                 WHEN 'Samedi' THEN 6 
                 WHEN 'Dimanche' THEN 7 END, debut ASC";
			}
			else return;
		

	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results)){
			$tab[$row['id']] = $row;
	}
	include("closedb.php");
	return $tab;
	}
}

function modifCreneau($tab){
	require_once("class.imageconverter.php");
	require_once("saveImage.php");
	include("opendb.php");
	//$set = "";
	//Jour
	if(!empty($tab['jour_cre'])) $set.="jour='".mysql_real_escape_string($tab['jour_cre'])."', ";
	//Debut
	if(!empty($tab['debut_cre'])) $set.="debut='".mysql_real_escape_string($tab['debut_cre'])."', ";
	if ($set==="") return;
	//Fin
	if(!empty($tab['fin_cre'])) $set.="fin='".mysql_real_escape_string($tab['fin_cre'])."', ";
	//Lieu
	if(!empty($tab['lieu'])) $set.="lieu='".mysql_real_escape_string($tab['lieu'])."', ";
	$set=substr($set,0,-2);
	$query = "UPDATE {$GLOBALS['prefix_db']}creneau SET ".$set." WHERE id=".$tab['id_cre']."";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}

function ajoutResponsableCre($id_cre,$id_adh,$promo){
	include("opendb.php");
	$query = "INSERT into {$GLOBALS['prefix_db']}resp_cren(id_cre,id_adh,promo) VALUES ('$id_cre.','$id_adh','$promo')";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();	
	include("closedb.php");
	
}
function delRespCre($id_cre,$id_adh,$promo){
	include("opendb.php");
	$query = "DELETE FROM {$GLOBALS['prefix_db']}resp_cren WHERE id_cre='$id_cre' AND id_adh='$id_adh' AND promo='$promo' ";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();	
	include("closedb.php");
}

function getResponsablesCre($id_cre,$promo){

	$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent A ,{$GLOBALS['prefix_db']}resp_cren RA WHERE A.id=RA.id_adh AND RA.id_cre='".$id_cre."' AND promo='".$promo."'  ";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results)){
			$tab[$row['id']] = $row;
	}
	include("closedb.php");
	return $tab;
	
	
}

function getAllCreneaux(){

	$query = "SELECT BAT.nom AS batiment, SALLE.nom AS salle, CR.quota AS quota, A.id id_asso, A.nom nom_asso, F.id id_famille, F.nom nom_famille, S.id id_sec, S.nom nom_sec, AC.id id_act, AC.nom nom_act, CR.id id_cre, CR.jour jour_cre, CR.debut debut_cre, CR.fin fin_cre, CR.lieu lieu
						FROM {$GLOBALS['prefix_db']}activite AC, {$GLOBALS['prefix_db']}creneau CR, {$GLOBALS['prefix_db']}batiment BAT, {$GLOBALS['prefix_db']}salle SALLE, {$GLOBALS['prefix_db']}section S, {$GLOBALS['prefix_db']}association A, {$GLOBALS['prefix_db']}asso_section HS, {$GLOBALS['prefix_db']}famille F, {$GLOBALS['prefix_db']}famille_section FS
						WHERE CR.id_act=AC.id
						AND CR.lieu=SALLE.id AND SALLE.id_batiment=BAT.id
						AND S.id=FS.id_sec AND F.id=FS.id_famille
						AND AC.id_sec=S.id
						AND A.id=HS.id_asso
						AND HS.id_sec=S.id
						AND (
							A.id IN (SELECT id_asso FROM {$GLOBALS['prefix_db']}resp_asso WHERE promo = ".getParam('promo.conf')." )
							OR S.id IN (SELECT id_sec FROM {$GLOBALS['prefix_db']}resp_section WHERE promo = ".getParam('promo.conf')." )
							OR AC.id IN (SELECT id_act FROM {$GLOBALS['prefix_db']}resp_act WHERE promo = ".getParam('promo.conf')." )
							OR CR.id IN (SELECT id_cre FROM {$GLOBALS['prefix_db']}resp_cren WHERE promo = ".getParam('promo.conf')." )
							)
						ORDER BY nom_famille, nom_sec, nom_act, CASE jour
                 WHEN 'Lundi' THEN 1 
                 WHEN 'Mardi' THEN 2 
                 WHEN 'Mercredi' THEN 3 
                 WHEN 'Jeudi' THEN 4 
                 WHEN 'Vendredi' THEN 5 
                 WHEN 'Samedi' THEN 6 
                 WHEN 'Dimanche' THEN 7 END, CR.debut";
		

		include("opendb.php");
		$results = mysql_query($query);
		if (!$results) echo mysql_error();
		$tab_avec_famille = array();
		$tab_sans_famille = array();
		while($row = mysql_fetch_array($results)){
				$tab_sans_famille[$row['id_cre']] = $row;
				$tab_avec_famille[$row['id_cre'].'-'.$row['id_famille']] = $row;
		}
		include("closedb.php");
		$tab['avec_famille'] = $tab_avec_famille;
		$tab['sans_famille'] = $tab_sans_famille;
		return $tab;
	
}



function getInfoCreneau($creneau)
{
	$query = "SELECT AC.id AS id_activite, AC.id_sec AS id_section, HS.id_asso AS id_association
	FROM {$GLOBALS['prefix_db']}creneau C, {$GLOBALS['prefix_db']}activite AC, {$GLOBALS['prefix_db']}asso_section HS, {$GLOBALS['prefix_db']}resp_cren RC
	WHERE C.id=".$creneau."
	AND RC.id_cre=".$creneau."
	AND C.id_act=AC.id
	AND AC.id_sec=HS.id_sec
	";
	
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_assoc($results))
		$tab[$creneau] = $row;
	include("closedb.php");
	return $tab;
}

function getAllLieux()
{
	$query = "SELECT BAT.nom AS batiment, SALLE.nom AS salle, SALLE.id AS id
	FROM {$GLOBALS['prefix_db']}batiment BAT, {$GLOBALS['prefix_db']}salle SALLE
	WHERE SALLE.id_batiment=BAT.id
	ORDER BY BAT.nom, SALLE.nom
	";
	
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	$tab = "";
	while($row = mysql_fetch_assoc($results))
		$tab .= '<option value="'.$row['id'].'">'.$row['batiment'].' '.$row['salle'].'</option>
		';
	include("closedb.php");
	return $tab;
}


function nbre_a_jour($cre, $promo)
{

	$tab = getAllCreneaux();
	$tab = $tab['sans_famille'];
	// $tab=getCreneaux($_SESSION['uid']);
	$creneau = $tab[$cre];
	$adhs = getAdherentsByCreneau($cre,$promo);
	foreach($adhs as $id_adh => $row) {
	
		$id_statut_adh = $row['id_statut'];
		$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}adhesion a INNER JOIN {$GLOBALS['prefix_db']}association b ON a.id_asso=b.id WHERE id_adh={$row['id']} AND id_cre=$cre");
		while ($tmp_array = mysql_fetch_array($res)) {
			$id_asso = $tmp_array['id_asso'];
			$nom_asso = $tmp_array['nom'];
			$actif = $tmp_array['statut'];
		}

		if ($actif == 0) {
			$deja_paye = 0;
			$cout_cre = 0;
			$list_sup = "";
			$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}sup_fk a INNER JOIN {$GLOBALS['prefix_db']}sup b ON a.id_sup=b.id WHERE promo=$promo AND ((id_asso_adh=$id_asso AND (id_ent=$cre OR id_ent={$creneau['id_act']} OR id_ent={$creneau['id_sec']})) OR (id_statut=$id_statut_adh AND id_ent=$id_asso))");
			while ($tmp_array = mysql_fetch_array($res)) {
					$cout_cre += $tmp_array['valeur'];
					$list_sup .= ",".$tmp_array['id'];
			}
			if ($cout_cre > 0 && !empty($list_sup)) {
				$list_sup[0] = " ";
				$res = doQuery("SELECT * FROM `{$GLOBALS['prefix_db']}paiement_sup` a INNER JOIN `{$GLOBALS['prefix_db']}paiement` b ON a.`id_paiement`=b.`id` WHERE promo=$promo AND b.`id_adh`={$row['id']} AND a.`id_sup` IN ({$list_sup})");
				while ($tmp_array = mysql_fetch_array($res))
					   $deja_paye += $tmp_array['valeur'];
			}
			if ($cout_cre <= $deja_paye)
				$a_jour ++;
			else
				$a_jour2 = "Pas à jour";
		} else if ($actif == 1)
			$a_jour2 = "Résilié";
		else
			$a_jour2 = "Impossible";
	}
	return $a_jour;
	
}


?>