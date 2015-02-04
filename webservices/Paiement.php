<?php

function getMyPaiements($userid, $promo)
{
	$query="SELECT  P.*
		FROM {$GLOBALS['prefix_db']}paiement P
		WHERE P.id_adh = $userid AND promo = $promo
		ORDER BY P.date DESC
		";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results)){
		$tab[$row['id']] = $row;
		$q2="SELECT PS.id_paiement id_paiement, PS.id_sup id_sup, PS.valeur valeur_paiement, S.valeur valeur_sup, S.type type_sup, S.id_asso_paie
			FROM {$GLOBALS['prefix_db']}paiement_sup PS, {$GLOBALS['prefix_db']}sup S
			WHERE PS.id_sup=S.id
			AND id_paiement={$row['id']} ";
		$r2 = mysql_query($q2);
		if (!$results)
			echo mysql_error();
		while ($row2 = mysql_fetch_array($r2))
		{
			$tab[$row['id']]['ps'][$row2['id_sup']]=$row2;
		}
	}
	include("closedb.php");
	return $tab;
}

function addPaiement($tab)
{
	$query= "INSERT INTO {$GLOBALS['prefix_db']}paiement (id_adh,type,num,remarque,promo,date,date_t,recorded_by)  VALUES ('{$tab['id_adh']}', '{$tab['type']}', '{$tab['num']}' ,'{$tab['remarque']}' ,'{$tab['promo']}' ,'".date( 'Y-m-d H:i:s')."','{$tab['date_t']}' ,'{$tab['recorded_by']}' )";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$id_p=mysql_insert_id();
	$q2 = "INSERT INTO {$GLOBALS['prefix_db']}paiement_sup (id_paiement,id_sup,valeur) VALUES ";
	foreach($tab['sup'] as $id_sup => $valeur)
	{
		$q2 .= "('$id_p', '$id_sup','$valeur' ),";
	}
	$q2 = substr($q2,0,-1);
	$r2 = mysql_query($q2);
	if (!$r2)
		echo mysql_error();
	include("closedb.php");
}

function delPaiement($id)
{

	$query = "DELETE FROM {$GLOBALS['prefix_db']}paiement WHERE id='$id'";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	include("closedb.php");

}

function getPaiementsSup($id_adh, $promo)
{
	$query = "SELECT SUM(PS.valeur) total,PS.id_sup 
	FROM {$GLOBALS['prefix_db']}paiement P, {$GLOBALS['prefix_db']}paiement_sup PS 
	WHERE P.id=PS.id_paiement AND P.id_adh=$id_adh AND P.promo=$promo GROUP BY PS.id_sup";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$totaux= array();
	while($row = mysql_fetch_array($results)){
		if (empty($totaux[$row['id_sup']]))
			$totaux[$row['id_sup']] = $row['total'];
		else
			$totaux[$row['id_sup']] += $row['total'];
	}
	include("closedb.php");
	return $totaux;
}

?>