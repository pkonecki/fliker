<?php 
include("paths.php");

$query="SELECT id,nom FROM statut ORDER BY nom";
include("opendb.php");
$results = mysql_query($query);
	if (!$results){ 
		echo mysql_error();
		die();
}
$rows=array();
while($row = mysql_fetch_array($results)){
	$rows[utf8_encode($row['nom'])] = $row['id'];
}
echo json_encode($rows);

include("closedb.php");
?>