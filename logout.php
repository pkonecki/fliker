<?php
session_start();
include("./includes/paths.php");

$header = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
 <head>
  <title>::Fliker::Connexion</title>
  <link rel="stylesheet" type="text/css" href="./includes/style.css" />
  <meta http-equiv="refresh" content="3;url=index.php" />
 </head>
 <body>
<h1>Déconnexion</h1> ';

$footer = '</body></html>';

print $header;
if(!isset($_SESSION['uid']))
	echo "<center>Vous devez être connecté pour vous déconnecter !</center>";
else
{
	session_destroy();
	echo "<center>Vous êtes bien déconnecté !</center>";
}
print $footer;

?>
