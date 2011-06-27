
<?php

function delActivite($id){
	include("opendb.php");
	if(!isset($id)) return;
	$query = "DELETE FROM activite WHERE id=".$id."";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}

?>