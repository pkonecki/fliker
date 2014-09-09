<?php

function addSup($tb,$id_tb,$type,$valeur,$id_fk,$id_asso_paie,$facultatif,$promo){
	//Add sup
	if($tb==="association") $col = "id_statut";
	else $col="id_asso_adh";
	$query = "INSERT INTO {$GLOBALS['prefix_db']}sup(type,valeur,$col,id_asso_paie,facultatif,promo) VALUES ('$type','$valeur','$id_fk','$id_asso_paie','$facultatif','$promo')";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$id_sup = mysql_insert_id();

	//Ajouter sup_fk avec id_sup_fk déterminé
	$req3="INSERT INTO {$GLOBALS['prefix_db']}sup_fk (id_ent,id_sup) VALUES ('$id_tb','$id_sup')";
	$res3=mysql_query($req3);
	if (!$res3) echo mysql_error();
	include("closedb.php");
}

function delSup($id){
	$query = "DELETE FROM {$GLOBALS['prefix_db']}sup WHERE id='$id'";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");
}

function getSup($tb,$id_tb,$promo){
	if($tb==="association") {
		$query = "SELECT S.*,SF.id_ent id_ent, ST.nom statut 
		FROM {$GLOBALS['prefix_db']}sup S ,{$GLOBALS['prefix_db']}sup_fk SF , {$GLOBALS['prefix_db']}statut ST 
		WHERE SF.id_sup=S.id AND S.id_statut=ST.id AND SF.id_ent='$id_tb' AND S.promo='$promo' ";
	} else {
		$query = "SELECT S.*,SF.id_ent id_ent 
		FROM {$GLOBALS['prefix_db']}sup S ,{$GLOBALS['prefix_db']}sup_fk SF 
		WHERE SF.id_sup=S.id AND SF.id_ent='$id_tb' AND S.promo='$promo' ";
	}

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

function getAssosCreneaux(){
	//Pour chaque asso, les supplément suivant le statut de l'adhérent
	$q1="SELECT  sup.id_statut id_statut_sup, A.id id_asso, A.nom nom_asso
	FROM {$GLOBALS['prefix_db']}association A, {$GLOBALS['prefix_db']}sup sup, {$GLOBALS['prefix_db']}sup_fk sup_fk
	WHERE sup_fk.id_ent = A.id
	AND sup.id_asso_adh IS NULL
	AND sup.id=sup_fk.id_sup
	AND sup.promo={$GLOBALS['current_promo']}
	GROUP BY id_asso,id_statut_sup";

	//Pour chaque créneau, les supplément suivant l'asso de l'adhérent
	$q2="SELECT sup.id_asso_adh id_asso_adh, S.id id_sec, S.nom nom_sec, AC.id id_act, AC.nom nom_act, CR.id id_cre, CR.jour jour_cre, CR.debut debut_cre, CR.fin fin_cre, CR.lieu lieu
	FROM {$GLOBALS['prefix_db']}activite AC, {$GLOBALS['prefix_db']}creneau CR, {$GLOBALS['prefix_db']}section S, {$GLOBALS['prefix_db']}sup sup, {$GLOBALS['prefix_db']}sup_fk sup_fk
	WHERE CR.id_act=AC.id
	AND AC.id_sec=S.id 
	AND (
	sup_fk.id_ent = S.id
	OR sup_fk.id_ent = AC.id
	OR sup_fk.id_ent = CR.id
	)
	AND sup.id_statut IS NULL
	AND sup.id=sup_fk.id_sup
	AND sup.promo={$GLOBALS['current_promo']}
	GROUP BY id_cre,id_asso_adh
	ORDER BY id_cre";

	$query="SELECT  S1.id_statut_sup, S1.id_asso, S1.nom_asso, S2.id_cre 
	FROM ($q1) AS S1, ($q2) AS S2
	WHERE S1.id_asso = S2.id_asso_adh";

	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results)){
		$tab[$row['id_statut_sup']][$row['id_cre']][$row['id_asso']]=$row['nom_asso'];
	}
	include("closedb.php");
	return $tab;

}

function getFacture($ads,$id_statut_adh,$promo)
{
	$where=" false ";
	foreach($ads as $key => $ad ){
		
		if(is_numeric($key) && $ad['statut']==0 ) $where.="OR (CR.id={$ad['id_cre']} AND A.id={$ad['id_asso']} ) ";
		
	}
	$tout ="SELECT A.id id_asso, A.nom nom_asso, S.id id_sec, S.nom nom_sec, AC.id id_act, AC.nom nom_act, CR.id id_cre, CR.jour jour_cre, CR.debut debut_cre, CR.fin fin_cre, CR.lieu lieu
						FROM {$GLOBALS['prefix_db']}activite AC, {$GLOBALS['prefix_db']}creneau CR, {$GLOBALS['prefix_db']}section S, {$GLOBALS['prefix_db']}association A
						WHERE CR.id_act=AC.id
						AND AC.id_sec=S.id
						AND ($where)
						ORDER BY nom_sec";
	$assos = "SELECT DISTINCT sup.facultatif, sup.id, sup.valeur as valeur, sup.id_asso_paie, sup.id_asso_adh, sup.type FROM ($tout) AS S1 ,{$GLOBALS['prefix_db']}sup sup
				WHERE sup.id_asso_adh IS NULL AND sup.id_asso_paie = S1.id_asso AND sup.id_statut='$id_statut_adh' AND sup.promo='$promo'";
	$secs = "SELECT DISTINCT sup.facultatif, sup.id, sup.valeur as valeur, S1.id_asso, S1.id_sec, S1.nom_sec, sup.id_asso_paie, sup.type FROM ($tout) AS S1 ,{$GLOBALS['prefix_db']}sup sup,{$GLOBALS['prefix_db']}sup_fk sup_fk
				WHERE sup.id_statut IS NULL AND sup.id_asso_adh = S1.id_asso AND S1.id_sec=sup_fk.id_ent AND sup_fk.id_sup=sup.id AND sup.promo=$promo";
	$acts = "SELECT DISTINCT sup.facultatif, sup.id, sup.valeur as valeur, S1.id_asso, S1.id_act, S1.nom_sec, S1.nom_act, sup.id_asso_paie, sup.type FROM ($tout) AS S1 ,{$GLOBALS['prefix_db']}sup sup,{$GLOBALS['prefix_db']}sup_fk sup_fk
				WHERE sup.id_statut IS NULL AND sup.id_asso_adh = S1.id_asso AND S1.id_act=sup_fk.id_ent AND sup_fk.id_sup=sup.id AND sup.promo=$promo";
	$cres = "SELECT DISTINCT sup.facultatif, sup.id, sup.valeur as valeur, S1.id_asso, S1.id_cre, S1.nom_sec, S1.nom_act, S1.jour_cre, S1.debut_cre, sup.id_asso_paie, sup.type FROM ($tout) AS S1 ,{$GLOBALS['prefix_db']}sup sup,{$GLOBALS['prefix_db']}sup_fk sup_fk
				WHERE sup.id_statut IS NULL AND sup.id_asso_adh = S1.id_asso AND S1.id_cre=sup_fk.id_ent AND sup_fk.id_sup=sup.id AND sup.promo=$promo";
	$t_assos = "SELECT SUM(A.valeur) total, A.id_asso_paie FROM ($assos) AS A GROUP BY A.id_asso_paie";
	$t_secs  = "SELECT SUM(A.valeur) total, A.id_asso_paie FROM ($secs)  AS A GROUP BY A.id_asso_paie";
	$t_acts  = "SELECT SUM(A.valeur) total, A.id_asso_paie FROM ($acts)  AS A GROUP BY A.id_asso_paie";
	$t_cres  = "SELECT SUM(A.valeur) total, A.id_asso_paie FROM ($cres)  AS A GROUP BY A.id_asso_paie";

	include("opendb.php");
	$results = mysql_query($assos);
	if (!$results) echo mysql_error();
	$tab_assos = array();
	while($results != false && ($row = mysql_fetch_array($results))){
		$tab_assos[$row['id']] = $row;
	}
	$results = mysql_query($secs);
	if (!$results) echo mysql_error();
	$tab_secs = array();
	while($row = mysql_fetch_array($results)){
		$tab_secs[$row['id']] = $row;
	}
	$results = mysql_query($acts);
	if (!$results) echo mysql_error();
	$tab_acts = array();
	while($row = mysql_fetch_array($results)){
		$tab_acts[$row['id']] = $row;
	}
	$results = mysql_query($cres);
	if (!$results) echo mysql_error();
	$tab_cres = array();
	while($row = mysql_fetch_array($results)){
		$tab_cres[$row['id']] = $row;
	}
	
	$totaux = array();
	$results = mysql_query($t_assos);
	if (!$results) echo mysql_error();
	while($results != false && ($row = mysql_fetch_array($results)))
	{
		if (empty($totaux[$row['id_asso_paie']]))
			$totaux[$row['id_asso_paie']] = $row['total'];
		else
			$totaux[$row['id_asso_paie']] += $row['total'];
	}
	$results = mysql_query($t_secs);
	if (!$results) echo mysql_error();
	while($row = mysql_fetch_array($results))
	{
		if (empty($totaux[$row['id_asso_paie']]))
			$totaux[$row['id_asso_paie']] = $row['total'];
		else
			$totaux[$row['id_asso_paie']] += $row['total'];
	}
	$results = mysql_query($t_acts);
	if (!$results) echo mysql_error();
	while($row = mysql_fetch_array($results))
	{
		if (empty($totaux[$row['id_asso_paie']]))
			$totaux[$row['id_asso_paie']] = $row['total'];
		else
			$totaux[$row['id_asso_paie']] += $row['total'];
	}
	$results = mysql_query($t_cres);
	if (!$results) echo mysql_error();
	while($row = mysql_fetch_array($results)){
		$totaux[$row['id_asso_paie']] += $row['total'];
	}

	include("closedb.php");
	$ret=array();
	$ret['assos']=$tab_assos;
	$ret['secs']=$tab_secs;
	$ret['acts']=$tab_acts;
	$ret['cres']=$tab_cres;
	$ret['totaux']=$totaux;

	return $ret;
}

?>