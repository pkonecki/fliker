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

function getParams()
{
	$query="SELECT * FROM {$GLOBALS['prefix_db']}config";
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

function getParamsBis()
{
	$query="SELECT * FROM {$GLOBALS['prefix_db']}config";
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
?>