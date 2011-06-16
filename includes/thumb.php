<?php
require_once 'paths.php';
require_once 'ThumbLib.inc.php';

$fileName = (isset($_GET['file'])) ? urldecode($_GET['file']) : null;
$fileName = $photos."/".$fileName;
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

$thumb->adaptiveResize(135, 150);
$thumb->cropFromCenter(135, 150);
$thumb->show();

?>