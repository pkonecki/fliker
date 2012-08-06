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

?>