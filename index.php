<?php
session_start();
include("./includes/paths.php");


$header = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
 <head>
  <title>::Fliker::Accueil</title>
  <link rel="stylesheet" type="text/css" href="./includes/style.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
 </head>
 <body>
<h1>Fliker</h1> ';

$footer = '</body></html>';

print $header;
include("userdiv.php");
print '<div id=content>';
include("fiche_adherent.php");
print '</div>';
print $footer;



?>