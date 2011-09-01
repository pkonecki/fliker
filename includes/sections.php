<?php 
include("paths.php");

$query="SELECT id,nom FROM {$GLOBALS['prefix_db']}section";
include("opendb.php");
$results = mysql_query($query);
	if (!$results){ 
		echo mysql_error();
		die();
}
$rows=array();
while($row = mysql_fetch_array($results)){
	$rows[$row['id']] = $row['nom'];
}
echo json_encode($rows);

include("closedb.php");
?>