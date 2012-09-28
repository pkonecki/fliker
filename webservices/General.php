<?php

function getParam($id){


	$query= "SELECT valeur FROM {$GLOBALS['prefix_db']}config WHERE id='$id' ";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	$tab = array();
	if (mysql_num_rows($results) > 0)
		$ret = mysql_result($results,0,"valeur");
	else
		$ret = NULL;
	include("closedb.php");
	return $ret;
}

function getConfig($texte = "")
{
	if ($texte == "")
		$query="SELECT * FROM {$GLOBALS['prefix_db']}config";
	else
		$query="SELECT * FROM {$GLOBALS['prefix_db']}config WHERE id LIKE '%.".$texte."'";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results)){
		$tab[$row['id']] = $row['valeur'];
	}
	include("closedb.php");
	return $tab;
}

function getConfigBis($texte = "")
{
	if ($texte == "")
		$query="SELECT * FROM {$GLOBALS['prefix_db']}config";
	else
		$query="SELECT * FROM {$GLOBALS['prefix_db']}config WHERE id LIKE '%.".$texte."'";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results)){
		$tab[$row['id']] = $row['description'];
	}
	include("closedb.php");
	return $tab;
}

function setParam($id,$value){
	include("opendb.php");
	$value = mysql_real_escape_string(html_entity_decode($value));
	$query= "UPDATE {$GLOBALS['prefix_db']}config SET valeur='$value' WHERE id='$id' ";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");
}

function modifPresence($adh,$cre,$week,$promo,$present){
	include("opendb.php");
	if($present) $query="INSERT INTO {$GLOBALS['prefix_db']}presence(id_adh,id_cre,week,promo) VALUES ('$adh','$cre','$week','$promo')";
	else $query="DELETE FROM {$GLOBALS['prefix_db']}presence WHERE id_adh='$adh' AND id_cre='$cre' AND week='$week' AND promo='$promo' ";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");
}

function etaitPresent($adh,$cre,$week,$promo){
	
	$query="SELECT * FROM {$GLOBALS['prefix_db']}presence WHERE id_adh='$adh' AND id_cre='$cre' AND week='$week' AND promo='$promo' ";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results)
	{
		echo mysql_error();
		$ret=false;
	}
	else
	{
		if (mysql_num_rows($results) > 0)
			$ret = true;
		else
			$ret=false;
	}
	include("closedb.php");	
	return $ret;
}

function doQuery($query, $stopIfError = false)
{
	include('opendb.php');
	$res = mysql_query($query);
	if (!$res)
	{
		echo mysql_error();
		if ($stopIfError == true)
			die();
	}
	else
		return $res;
	return false;
}

function secur_data($string)
{
	if(ctype_digit($string))
		$string = intval($string);
	else
	{
		$string = mysql_real_escape_string($string);
		$string = addcslashes($string, '%_');
	}
	return $string;
}

function streamtotab($stream, $key)
{
	$tab = null;
	while ($tmp_array = mysql_fetch_array($stream))
		$tab[$key] = $tmp_array;
	return $tab;
}

function returnSelectMonth($selected)
{
	$string = "";
	$i = 1;
	while ($i != 13)
	{
		$string .= "<option ".($selected == $i ? "selected" : "")." value=$i>$i</option>";
		$i++;
	}
	return ($string);
}

function returnSelectYear($selected)
{
	$year_begin = 0;
	$year_end = 0;
	
	$res = doQuery("SELECT date_enregistrement FROM {$GLOBALS['prefix_db']}finances ORDER BY date_enregistrement DESC");
	if ($res && mysql_num_rows($res) > 0)
	{
		$tmp_array = mysql_fetch_array($res);
		$year_end = strftime("%Y", strtotime($tmp_array['date_enregistrement']));
		while ($tab_tmp = mysql_fetch_array($res))
			$tmp_array = $tab_tmp;
		$year_begin = strftime("%Y", strtotime($tmp_array['date_enregistrement']));
	}
	$string = "";
	$year_end++;
	$year_begin--;
	while ($year_end != $year_begin)
	{
		$string .= "<option ".($selected == $year_end? "selected" : "")." value=$year_end>$year_end</option>";
		$year_end--;
	}
	$string .= "<option ".($selected == $year_end ? "selected" : "")." value=$year_end>$year_end</option>";
	return ($string);
}

function getTypeRecap()
{
	$tab_1 = null;
	$tab_2 = null;
	$tab_final = null;
	
	$res = doQuery("SELECT DISTINCT type FROM {$GLOBALS['prefix_db']}sup");
	while ($tmp_array = mysql_fetch_array($res))
		$tab_1[$tmp_array['type']] = 0;
	
	$res = doQuery("SELECT DISTINCT nom FROM {$GLOBALS['prefix_db']}type_dep");
	while ($tmp_array = mysql_fetch_array($res))
		$tab_2[$tmp_array['nom']] = 0;
	
	$tab_final = array_merge($tab_1, $tab_2);
	ksort($tab_final);
	return ($tab_final);
}

function getPromoRecap()
{
	$tab_1 = null;
	$tab_2 = null;
	$tab_final = null;
	$tab = null;
	
	$res = doQuery("SELECT DISTINCT promo FROM {$GLOBALS['prefix_db']}finances");
	while ($tmp_array = mysql_fetch_array($res))
		$tab_1[" ".$tmp_array['promo']." "] = 0;
	
	$res = doQuery("SELECT DISTINCT promo FROM {$GLOBALS['prefix_db']}paiement");
	while ($tmp_array = mysql_fetch_array($res))
		$tab_2[" ".$tmp_array['promo']." "] = 0;

	$tab_final = array_merge($tab_1, $tab_2);
	ksort($tab_final);
	foreach ($tab_final as $key => $value)
		$tab[str_replace(' ', '', $key)] = 0;
	return ($tab);
}

function getPaiementsAsso($id_asso, $promo, $tab_type)
{
	$demander = 0;
	$autoriser = 0;
	$cotis_depot = 0;
	
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}sup WHERE id IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent='".$id_asso."')");
	$list_type = null;
	while ($tmp_array = mysql_fetch_array($res))
		$list_type[$tmp_array['type']][$tmp_array['id']] = 0;
	
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}paiement_sup, {$GLOBALS['prefix_db']}paiement WHERE {$GLOBALS['prefix_db']}paiement.id = {$GLOBALS['prefix_db']}paiement_sup.id_paiement AND id_sup IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent='".$id_asso."') AND id_paiement IN (SELECT id FROM {$GLOBALS['prefix_db']}paiement WHERE promo=$promo)");
	$list_paie = null;
	while ($tmp_array = mysql_fetch_array($res))
	{
		if ($tmp_array['date_bordereau'] == NULL)
			$cotis_depot += $tmp_array['valeur'];
		else if ($tmp_array['date_bordereau'] != 0)
		{
			if (isset($list_paie[$tmp_array['id_sup']]))
				$list_paie[$tmp_array['id_sup']] += $tmp_array['valeur'];
			else
				$list_paie[$tmp_array['id_sup']] = $tmp_array['valeur'];
		}
	}
	
	foreach($tab_type as $key => $value)
	{
		if (isset($list_type[$key]))
		{
			foreach ($list_type[$key] as $id => $number)
			{
				if (isset($list_paie[$id]))
					$tab_type[$key] += $list_paie[$id];
			}
		}
	}
	
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}finances WHERE section='".$id_asso."' AND promo=$promo");
	while ($tmp_array = mysql_fetch_array($res))
	{
		if ($tmp_array['autorisation'] != '1')
		{
			if ($tmp_array['autorisation'] == '0')
				$demander += $tmp_array['montant'];
		}
		else
		{
			if ($tmp_array['confirmation'] != '1')
			{
				if ($tmp_array['confirmation'] == '0')
					$autoriser += $tmp_array['montant'];
			}
			else
			{
				if (isset($tab_type[$tmp_array['type']]))
					$tab_type[$tmp_array['type']] += $tmp_array['montant'];
				else
					$tab_type[$tmp_array['type']] = $tmp_array['montant'];
			}
		}
	}
	return (array("Paiements" => $tab_type, "Demandé" => $demander, "Autorisé" => $autoriser, "Cotis_depot" => $cotis_depot));
}

function getPaiementsSec($id_asso, $id_sec, $promo, $tab_type, $list_id)
{
	$demander = 0;
	$autoriser = 0;
	$cotis_depot = 0;
	
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}sup WHERE id IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent IN (".$list_id[$id_sec].")) AND id_asso_paie=".$id_asso." ");
	$list_type = null;
	while ($tmp_array = mysql_fetch_array($res))
		$list_type[$tmp_array['type']][$tmp_array['id']] = 0;
	
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}paiement_sup, {$GLOBALS['prefix_db']}paiement WHERE {$GLOBALS['prefix_db']}paiement.id = {$GLOBALS['prefix_db']}paiement_sup.id_paiement AND id_sup IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent IN (".$list_id[$id_sec].")) AND id_paiement IN (SELECT id FROM {$GLOBALS['prefix_db']}paiement WHERE promo=$promo)");
	$list_paie = null;
	while ($tmp_array = mysql_fetch_array($res))
	{
		if ($tmp_array['date_bordereau'] == NULL)
			$cotis_depot += $tmp_array['valeur'];
		else if ($tmp_array['date_bordereau'] != 0)
		{
			if (isset($list_paie[$tmp_array['id_sup']]))
				$list_paie[$tmp_array['id_sup']] += $tmp_array['valeur'];
			else
				$list_paie[$tmp_array['id_sup']] = $tmp_array['valeur'];
		}
	}
	
	foreach($tab_type as $key => $value)
	{
		if (isset($list_type[$key]))
		{
			foreach ($list_type[$key] as $id => $number)
			{
				if (isset($list_paie[$id]))
					$tab_type[$key] += $list_paie[$id];
			}
		}
	}
	
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}finances WHERE section='".$id_asso."-".$id_sec."' AND promo=$promo");
	while ($tmp_array = mysql_fetch_array($res))
	{
		if ($tmp_array['autorisation'] != '1')
		{
			if ($tmp_array['autorisation'] == '0')
				$demander += $tmp_array['montant'];
		}
		else
		{
			if ($tmp_array['confirmation'] != '1')
			{
				if ($tmp_array['confirmation'] == '0')
					$autoriser += $tmp_array['montant'];
			}
			else
			{
				if (isset($tab_type[$tmp_array['type']]))
					$tab_type[$tmp_array['type']] += $tmp_array['montant'];
				else
					$tab_type[$tmp_array['type']] = $tmp_array['montant'];
			}
		}
	}
	return (array("Paiements" => $tab_type, "Demandé" => $demander, "Autorisé" => $autoriser, "Cotis_depot" => $cotis_depot));
}

function findColor($number)
{
	if ($number > 0)
		return("#16B84E");
	else if ($number < 0)
		return("red");
	else
		return("black");
}

function getPaiementsAssoAll($id_asso, $tab_type)
{
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}paiement_sup, {$GLOBALS['prefix_db']}paiement WHERE {$GLOBALS['prefix_db']}paiement_sup.id_paiement = {$GLOBALS['prefix_db']}paiement.id AND id_sup IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent='".$id_asso."') AND date_bordereau is not null AND date_bordereau != 0");
	while ($tmp_array = mysql_fetch_array($res))
	{
		if (isset($tab_type[$tmp_array['promo']]))
			$tab_type[$tmp_array['promo']] += $tmp_array['valeur'];
		else
			$tab_type[$tmp_array['promo']] = $tmp_array['valeur'];
	}

	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}finances WHERE section='".$id_asso."' AND autorisation = 1 AND confirmation = 1");
	while ($tmp_array = mysql_fetch_array($res))
	{
		if (isset($tab_type[$tmp_array['promo']]))
			$tab_type[$tmp_array['promo']] += $tmp_array['montant'];
		else
			$tab_type[$tmp_array['promo']] = $tmp_array['montant'];
	}
	return ($tab_type);
}

function getPaiementsSecAll($id_asso, $id_sec, $tab_type, $list_id)
{
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}paiement_sup, {$GLOBALS['prefix_db']}paiement WHERE {$GLOBALS['prefix_db']}paiement_sup.id_paiement = {$GLOBALS['prefix_db']}paiement.id AND id_sup IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent IN (".$list_id[$id_sec].")) AND id_sup IN (SELECT id FROM {$GLOBALS['prefix_db']}sup WHERE id_asso_paie = ".$id_asso.") AND date_bordereau is not null AND date_bordereau != 0");
	while ($tmp_array = mysql_fetch_array($res))
	{
		if (isset($tab_type[$tmp_array['promo']]))
			$tab_type[$tmp_array['promo']] += $tmp_array['valeur'];
		else
			$tab_type[$tmp_array['promo']] = $tmp_array['valeur'];
	}
	
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}finances WHERE section='".$id_asso."-".$id_sec."' AND autorisation = 1 AND confirmation = 1");
	while ($tmp_array = mysql_fetch_array($res))
	{
		if (isset($tab_type[$tmp_array['promo']]))
			$tab_type[$tmp_array['promo']] += $tmp_array['montant'];
		else
			$tab_type[$tmp_array['promo']] = $tmp_array['montant'];
	}
	return ($tab_type);
}

function age($naiss, $year, $month)
{
	$naissance = explode("-", $naiss);
	
	$final_year = $year - $naissance[0];
	$final_month = $month - $naissance[1];
	if ($final_month < 0)
		$final_year--;
	return ($final_year);
}

function getStats(&$nb_statut, $id_asso)
{
	$homme = 0;
	$femme = 0;
	$statut = 0;
	$inscrit = 0;
	$a_jour = adhStateAsso($id_asso);
	$age_0 = 0;
	$age_19 = 0;
	$age_26 = 0;
	$age_46 = 0;
	$age_66 = 0;
	
	$year = strftime("%Y",strtotime("now"));
	$month = strftime("%m",strtotime("now"));

	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE id IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}adhesion WHERE id_asso=".$id_asso." AND statut=0) ");
	while ($tmp_array = mysql_fetch_array($res))
	{
		if ($tmp_array['categorie'] == "M")
			$homme++;
		if ($tmp_array['categorie'] == "F")
			$femme++;
		$nb_statut[$tmp_array['id_statut']]++;
		$inscrit++;
		$age = age($tmp_array['naissance'], $year, $month);
		if ($age <= 18)
			$age_0++;
		else if ($age <= 25)
			$age_19++;
		else if ($age <= 45)
			$age_26++;
		else if ($age <= 65)
			$age_46++;
		else if ($age >= 66)
			$age_66++;
	}
	if ($a_jour == -1)
		$a_jour = $inscrit;
	return (array("Inscrits" => $inscrit, "Homme" => $homme, "Femme" => $femme, "0-18" => "<font color='blue'>".$age_0."</font>", "19-25" =>" <font color='blue'>".$age_19."</font>", "26-45" => "<font color='blue'>".$age_26."</font>", "46-65" => "<font color='blue'>".$age_46."</font>", "66+" => "<font color='blue'>".$age_66."</font>", "A jour" => $a_jour));
}

function adhStateAsso($asso)
{
	$list_adh = null;
	$listSup = null;
	$nb_ok = 0;
	$listStatut = null;
	$listTotalAdh = null;

	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}sup_fk, {$GLOBALS['prefix_db']}sup WHERE {$GLOBALS['prefix_db']}sup_fk.id_sup={$GLOBALS['prefix_db']}sup.id AND id_ent = ".$asso."");
	while ($tmp_array = mysql_fetch_array($res))
		$listSup[$tmp_array['id_statut']] = $tmp_array['valeur'];

	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}adhesion INNER JOIN {$GLOBALS['prefix_db']}adherent ON {$GLOBALS['prefix_db']}adhesion.id_adh={$GLOBALS['prefix_db']}adherent.id WHERE id_asso=".$asso." AND statut=0");
	while ($tmp_array = mysql_fetch_array($res))
		$listTotalAdh[$tmp_array['id_adh']] = $tmp_array['id_statut'];
	if ($listTotalAdh != null)
	{
		foreach ($listTotalAdh as $key => $value)
		{
			if (!isset($listSup[$value]) || $listSup[$value] == 0)
				$nb_ok++;
		}
	}
	$res = doQuery("SELECT * FROM ({$GLOBALS['prefix_db']}paiement_sup INNER JOIN {$GLOBALS['prefix_db']}paiement ON {$GLOBALS['prefix_db']}paiement_sup.id_paiement = {$GLOBALS['prefix_db']}paiement.id) INNER JOIN {$GLOBALS['prefix_db']}adherent ON {$GLOBALS['prefix_db']}paiement.id_adh = {$GLOBALS['prefix_db']}adherent.id WHERE id_adh IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}adhesion WHERE id_asso=".$asso." AND statut=0) AND id_sup IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent = ".$asso.")");
	while ($tmp_array = mysql_fetch_array($res))
	{
		$listStatut[$tmp_array['id_adh']] = $tmp_array['id_statut'];
		if (isset($list_adh[$tmp_array['id_adh']]))
			$list_adh[$tmp_array['id_adh']] += $tmp_array['valeur'];
		else
			$list_adh[$tmp_array['id_adh']] = $tmp_array['valeur'];
	}
	if ($list_adh != null)
	{
		foreach ($list_adh as $key => $value)
		{
			if (isset($listSup[$listStatut[$key]]))
			{
				if ($listSup[$listStatut[$key]] == $value)
					$nb_ok++;
			}
			else
				$nb_ok++;
		}
	}
	if ($listSup == null || tabConIsEmpty($listSup) == true)
		return -1;
	else
		return ($nb_ok);
}

function tabConIsEmpty($tab)
{
	foreach ($tab as $key => $value)
	{
		if ($value != 0)
			return false;
	}
	return true;
}

function getStatsSec(&$nb_statut, $asso, $list_id)
{
	$homme = 0;
	$femme = 0;
	$statut = 0;
	$inscrit = 0;
	$a_jour = adhStateSec($asso, $list_id);
	$age_0 = 0;
	$age_19 = 0;
	$age_26 = 0;
	$age_46 = 0;
	$age_66 = 0;
	
	$year = strftime("%Y",strtotime("now"));
	$month = strftime("%m",strtotime("now"));

	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE id IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}adhesion WHERE id_asso=".$asso." AND id_cre IN (".$list_id.") AND statut=0) ");
	while ($tmp_array = mysql_fetch_array($res))
	{
		if ($tmp_array['categorie'] == "M")
			$homme++;
		if ($tmp_array['categorie'] == "F")
			$femme++;
		$nb_statut[$tmp_array['id_statut']]++;
		$inscrit++;
		$age = age($tmp_array['naissance'], $year, $month);
		if ($age <= 18)
			$age_0++;
		else if ($age <= 25)
			$age_19++;
		else if ($age <= 45)
			$age_26++;
		else if ($age <= 65)
			$age_46++;
		else if ($age >= 66)
			$age_66++;
	}
	
	if ($a_jour == -1)
		$a_jour = $inscrit;
	return (array("Inscrits" => $inscrit, "Homme" => $homme, "Femme" => $femme, "0-18" => "<font color='blue'>".$age_0."</font>", "19-25" =>" <font color='blue'>".$age_19."</font>", "26-45" => "<font color='blue'>".$age_26."</font>", "46-65" => "<font color='blue'>".$age_46."</font>", "66+" => "<font color='blue'>".$age_66."</font>", "A jour" => $a_jour));
}

function adhStateSec($asso, $list_id)
{
	$list_adh = null;
	$listTotalAdh = null;
	$cout_sup = 0;
	$nb_ok = 0;

	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}sup_fk, {$GLOBALS['prefix_db']}sup WHERE {$GLOBALS['prefix_db']}sup_fk.id_sup={$GLOBALS['prefix_db']}sup.id AND id_ent IN (".$list_id.") AND id_asso_adh=".$asso."");
	while ($tmp_array = mysql_fetch_array($res))
		$cout_sup += $tmp_array['valeur'];
	
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}adhesion WHERE id_cre IN (".$list_id.") AND statut=0");
	while ($tmp_array = mysql_fetch_array($res))
	{
		if (isset($listTotalAdh[$tmp_array['id_adh']]))
			$listTotalAdh[$tmp_array['id_adh']] .= ",".$tmp_array['id_cre'];
		else
			$listTotalAdh[$tmp_array['id_adh']] = $tmp_array['id_cre'];
	}
	
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}paiement_sup, {$GLOBALS['prefix_db']}paiement WHERE {$GLOBALS['prefix_db']}paiement_sup.id_paiement = {$GLOBALS['prefix_db']}paiement.id AND id_adh IN (SELECT id_adh FROM {$GLOBALS['prefix_db']}adhesion WHERE id_asso=".$asso." AND statut=0) AND id_sup IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent IN (".$list_id."))");
	while ($tmp_array = mysql_fetch_array($res))
	{
		if (isset($list_adh[$tmp_array['id_adh']]))
			$list_adh[$tmp_array['id_adh']] += $tmp_array['valeur'];
		else
			$list_adh[$tmp_array['id_adh']] = $tmp_array['valeur'];
	}
	if ($list_adh != null)
	{
		foreach ($list_adh as $key => $value)
		{
			if ($value == $cout_sup)
				$nb_ok++;
		}
	}
	if ($cout_sup == 0)
		return -1;
	else
		return ($nb_ok);
}
?>
