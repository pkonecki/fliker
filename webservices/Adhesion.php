<?php
function getAdhesions($uid,$promo){
	$query = "SELECT * FROM adhesion where id_adh=$uid AND promo=$promo ORDER BY date DESC";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	while($row = mysql_fetch_array($results)){
			$tab[$row['id']] = $row;
			$tab['cre'.$row['id_cre']] = $row;
	}
	include("closedb.php");
	return $tab;
}

function getMyAdhesions($id_adh,$promo){
	$mycrens=getCreneaux($_SESSION['uid']);
	$ads= getAdhesions($id_adh,$promo);
	foreach ($ads as $key => $value){
		if(!isset($mycrens[$value['id_cre']])) unset($ads[$key]);
	}
	return $ads;
}

function newAdhesions($tab,$id_adh){
	$query= "INSERT INTO adhesion (id_adh,id_cre,id_asso,date,promo)  VALUES ";
	foreach($tab as $cre => $asso){
		 $query .="('$id_adh', '$cre', '$asso' ,'".date( 'Y-m-d H:i:s')."', '{$GLOBALS['current_promo']}' ),";
	}
	$query = substr($query,0,-1);
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");
}

function delAdhesion($id){
	include("opendb.php");
	if(!isset($id)) return;
	$query = "UPDATE adhesion SET statut=1 WHERE id=$id ";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}

function actAdhesion($id){
	include("opendb.php");
	if(!isset($id)) return;
	$query = "UPDATE adhesion SET statut=0 WHERE id=$id ";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}


?>