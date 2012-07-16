<?php
include_once("class.imageconverter.php");
function saveImage($fichier,$dossier){
	if(!is_dir($GLOBALS['root']."/".$dossier))
		mkdir($GLOBALS['root']."/".$dossier);
	$ext = get_extension($_FILES[$dossier]['name']);
	if($ext==='jpeg')
		$ext='jpg';
	$dest_fichier = $fichier.'.'.$ext;
	$dest_dossier=$GLOBALS['root']."/".$dossier;
	if (move_uploaded_file($_FILES[$dossier]['tmp_name'], $dest_dossier."/".$dest_fichier))
	{
		if(!($ext==='jpg'))
		{
			$img = new ImageConverter($dest_dossier ."/". $dest_fichier,'jpg');
			rename($fichier.'.jpg',$dest_dossier ."/".$fichier.'.jpg');
			unlink($dest_dossier ."/". $dest_fichier);
		}
	}
}

?>