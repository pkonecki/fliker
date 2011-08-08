<?php

/**
 * Fonctions en rapport avec les paiements
 *
 * @version $Id$
 * @copyright 2011
 */

function getMyPaiements($userid){
	$query="SELECT  P.*
		FROM paiement P
		WHERE P.id_adh=$userid
		";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results)){
		$tab[$row['id']] = $row;
		$q2="SELECT PS.id_paiement id_paiement, PS.id_sup id_sup, PS.valeur valeur_paiement, S.valeur valeur_sup, S.type type_sup, S.id_asso_paie
			FROM paiement_sup PS,sup S
			WHERE PS.id_sup=S.id
			AND id_paiement={$row['id']} ";
		$r2=mysql_query($q2);
		if (!$results) echo mysql_error();
		while ($row2 = mysql_fetch_array($r2)) {
			$tab[$row['id']]['ps'][$row2['id_sup']]=$row2;
		}
	}
	include("closedb.php");
	return $tab;
}



?>