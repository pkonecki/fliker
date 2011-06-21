<?php
session_start();
require_once 'paths.php';
require_once 'ThumbLib.inc.php';
if(!($_SESSION['auth_thumb']==='true')) die();
$fileName = (isset($_GET['file'])) ? urldecode($_GET['file']) : null;
$fileName = $GLOBALS['root']."/".$_GET['folder']."/".$fileName;
if ($fileName == null || !file_exists($fileName))
{
     // handle missing images however you want... perhaps show a default image??  Up to you...
     print "FILE NOT FOUND";
}

try
{
     $thumb = PhpThumbFactory::create($fileName);
}
catch (Exception $e)
{
	print "ERREUR: ".$e->getMessage();
     // handle error here however you'd like
}

$thumb->resize(135, 150);
//$thumb->cropFromCenter(135, 150);
$thumb->show();

?>