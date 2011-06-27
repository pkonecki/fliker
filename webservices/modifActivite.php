<?php

function modifActivite($tab){
	require_once("class.imageconverter.php");
	require_once("saveImage.php");
	//$set = "";
	//nom
	if(!empty($tab['nom'])) $set.="nom='".mysql_real_escape_string($tab['nom'])."', ";
	//Description
	if(!empty($tab['description'])) $set.="description='".mysql_real_escape_string($tab['description'])."', ";
	//logo
	 saveImage($tab['id'],"logo_act");
	//url
	if(!empty($tab['url'])) $set.="url='".mysql_real_escape_string($tab['url'])."', ";
	if ($set==="") return;
	include("opendb.php");
	$set=substr($set,0,-2);
	$query = "UPDATE activite SET ".$set." WHERE id=".$tab['id']."";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");

}

?>