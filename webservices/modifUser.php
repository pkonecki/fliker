<?php

function modifUser($tab){
	require("class.imageconverter.php");
	require("saveImage.php");
	$champs = getChampsAdherents();
	$set = "";
	include("opendb.php");
	foreach($champs as $row){
		if($row[user_editable]==1){
			$set .= $row[nom]."=";
			if($row[type]==='varchar')
				$set .= "'".mysql_real_escape_string($tab[$row[nom]])."',";
			else
			if($row[type]==='date')
				$set .= "'".mysql_real_escape_string($tab[$row[nom]])."',";
			else
			if($row[type]==='tinyint'){
				if ($tab[$row[nom]]==='on') $values .= "1,";
				else $set .= "0,";
			}
			if($row[type]==='file'){
				if($tab[$row[nom]][name]===""){
					$set .= "0,";
				} else {
					$set .= "1,";
					saveImage($_SESSION['user'],$row[nom]);
					/*
					$ext =get_extension($_FILES[$row[nom]][name]);
					if($ext==='jpeg') $ext='jpg';
					$dest_fichier = $_SESSION['user'].'.'.$ext;
					$photos=$_SERVER['DOCUMENT_ROOT']."/fliker/photos";
					move_uploaded_file($_FILES['photo']['tmp_name'], $photos ."/". $dest_fichier);
					if(!($ext==='jpg')){
						$img = new ImageConverter($photos ."/". $dest_fichier,'jpg');
						rename($_SESSION['user'].'.jpg',$photos ."/".$_SESSION['user'].'.jpg');
						unlink($photos ."/". $dest_fichier);
					}
					*/
				}


			}

		}
	}

	$set .="last_modif='".date( 'Y-m-d H:i:s')."'";

	$query = "UPDATE adherent SET ".$set." WHERE email='".$_SESSION['user']."'";
	//echo $query;
	$results = mysql_query($query);
	if (!$results) echo mysql_error();


	include("closedb.php");

}

?>