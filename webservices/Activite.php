<?php

function newActivite($tab){
	require_once("class.imageconverter.php");
	require_once("saveImage.php");
	//Nouvelle entitÃ©
	include("opendb.php");
	$q1 = "INSERT INTO {$GLOBALS['prefix_db']}entite VALUES ()";
	$r1 = mysql_query($q1);
	if (!$r1)
	{ 
		echo mysql_error();
		die();
	}
	$id = mysql_insert_id();
	
	if(empty($tab['nom']))
		die('il faut un nom!');
	$set = "(";
	$colonnes="(id,nom,description,id_sec,url)";
	$set.="'$id', ";
	$set.="'".mysql_real_escape_string($tab['nom'])."', ";
	$set.="'".mysql_real_escape_string($tab['description'])."', ";
	$set.="'".mysql_real_escape_string($tab['id_sec'])."', ";
	$set.="'".mysql_real_escape_string($tab['url'])."') ";
	$query = "INSERT INTO {$GLOBALS['prefix_db']}activite ".$colonnes." VALUES ".$set." ";

	$results = mysql_query($query);
	if (!$results){ 
		echo mysql_error();
		die();
	}
	saveImage(mysql_insert_id(),"logo_act");
	include("closedb.php");

}

function delActivite($id)
{
	include("opendb.php");
	if(!isset($id)) return;
	$query = "DELETE FROM {$GLOBALS['prefix_db']}entite WHERE id=".$id."";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}

function getActivites($userid)
{
	if(!empty($_SESSION['user']))
	{
		if($_SESSION['privilege']==="1")
		{
			$query = "SELECT A.id id_asso, A.nom nom_asso, S.id id_sec, S.nom nom_sec, AC.* 
						FROM {$GLOBALS['prefix_db']}activite AC, {$GLOBALS['prefix_db']}section S, {$GLOBALS['prefix_db']}association A, {$GLOBALS['prefix_db']}asso_section HS
						WHERE AC.id_sec=S.id
						AND A.id=HS.id_asso
						AND HS.id_sec=S.id
						ORDER BY S.nom, AC.nom";
		}
		else
		{
			if (!empty($userid))
			{
				$query = "SELECT A.id id_asso, A.nom nom_asso, S.id id_sec, S.nom nom_sec, AC.* 
						FROM {$GLOBALS['prefix_db']}activite AC, {$GLOBALS['prefix_db']}section S, {$GLOBALS['prefix_db']}association A, {$GLOBALS['prefix_db']}asso_section HS
						WHERE AC.id_sec=S.id
						AND A.id=HS.id_asso
						AND HS.id_sec=S.id
							AND
							(
							S.id IN (SELECT id_sec FROM {$GLOBALS['prefix_db']}resp_section WHERE id_adh = '".$userid."' AND promo = ".getParam('promo.conf').")
							OR AC.id IN (SELECT id_act FROM {$GLOBALS['prefix_db']}resp_act WHERE id_adh = '".$userid."' AND promo = ".getParam('promo.conf').")
							OR A.id IN (SELECT id_asso FROM {$GLOBALS['prefix_db']}resp_asso WHERE id_adh = '".$userid."' AND promo = ".getParam('promo.conf').")
							)
						ORDER BY S.nom, AC.nom";
			}
			else
				return;
		}

		include("opendb.php");
		$results = mysql_query($query);
		if (!$results) echo mysql_error();
		$tab = array();
		while($row = mysql_fetch_array($results))
		{
			$tab[$row['id']] = $row;
		}
		include("closedb.php");
		return $tab;
	}
}

function getActivitesBySection($sectionid)
{
	if(!empty($_SESSION['user']))
	{
		if (!empty($sectionid))
			$query = "SELECT * FROM {$GLOBALS['prefix_db']}activite A WHERE A.id_sec= ".$sectionid." ORDER BY A.nom ASC";
		else
			return;
		include("opendb.php");
		$results = mysql_query($query);
		if (!$results)
			echo mysql_error();
		$tab = array();
		while($row = mysql_fetch_array($results))
			$tab[$row['id']] = $row;
		include("closedb.php");
		return $tab;
	}
}

function modifActivite($tab)
{
	require_once("class.imageconverter.php");
	require_once("saveImage.php");
	include("opendb.php");
	//nom
	if(!empty($tab['nom']))
		$set.="nom='".mysql_real_escape_string($tab['nom'])."', ";
	//Description
	if(!empty($tab['description']))
		$set.="description='".mysql_real_escape_string($tab['description'])."', ";
	//logo
	 saveImage($tab['id'],"logo_act");
	//url
	if(!empty($tab['url']))
		$set.="url='".mysql_real_escape_string($tab['url'])."', ";
	if ($set==="")
		return;
	$set = substr($set,0,-2);
	$query = "UPDATE {$GLOBALS['prefix_db']}activite SET ".$set." WHERE id=".$tab['id']."";
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	include("closedb.php");

}

function ajoutResponsableAct($id_act,$id_adh,$promo)
{
	include("opendb.php");
	$query = "INSERT into {$GLOBALS['prefix_db']}resp_act(id_act,id_adh,promo) VALUES ('$id_act.','$id_adh','$promo')";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();	
	include("closedb.php");
	
}
function delRespActivite($id_act,$id_adh,$promo)
{
	include("opendb.php");
	$query = "DELETE FROM {$GLOBALS['prefix_db']}resp_act WHERE id_act='$id_act' AND id_adh='$id_adh' AND promo='$promo' ";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();	
	include("closedb.php");
}

function getResponsablesAct($id_act)
{

	$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent A ,{$GLOBALS['prefix_db']}resp_act RA WHERE A.id=RA.id_adh AND RA.id_act='".$id_act."' AND promo='".$promo."'  ";
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

function getActiviteByCre($id_cre)
{
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}creneau WHERE id='".$id_cre."'";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	$tab = mysql_fetch_array($results);
	include("closedb.php");
	
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}activite WHERE id='".$tab['id_act']."'";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	$tab = mysql_fetch_array($results);
	include("closedb.php");
	return $tab;
}

?>