<?php
function getAdhesions($uid){
	$query = "SELECT * FROM adhesion where id_adh=$uid ";
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

function newAdhesions($tab,$id_adh){
	$query= "INSERT INTO adhesion (id_adh,id_cre,id_asso,date)  VALUES ";
	foreach($tab as $cre => $asso){
		 $query .="('$id_adh', '$cre', '$asso' ,'".date( 'Y-m-d H:i:s')."' ),";
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
	$query = "DELETE FROM adhesion WHERE id=$id ";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}
?>