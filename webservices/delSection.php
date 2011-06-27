
<?php

function delSection($id){
	include("opendb.php");
	if(!isset($id)) return;
	$query = "DELETE FROM section WHERE id=".$id."";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}

?>