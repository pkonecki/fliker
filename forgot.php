<?php
include("./includes/paths.php");
include_once("General.php");

$header = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
 <head>
  <title>::Fliker::Connexion</title>
  <link rel="stylesheet" type="text/css" href="./includes/style.css" />
  <meta http-equiv="refresh" content="6;url=index.php" />
 </head>
 <body>
  <h1>Mot de passe oubli�</h1> ';

$footer = '</body></html>';

print "$header";
print "<center>D�sol�, il n'est pour l'instant pas encore possible de modifier ou r�cup�rer votre <b>mot de passe</b> !<br>Prenez contact avec nos <a href=\"".getParam("url_resiliation")."\">administrateurs</a>.</center>";
print "$footer";

?>