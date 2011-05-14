<?php
session_start();
include("./includes/paths.php");


$header = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
 <head>
  <title>::Fliker::Accueil</title>
  <link rel="stylesheet" type="text/css" href="./includes/style.css" />
 </head>
 <body>
<h1>Fliker</h1> ';

$footer = '</body></html>';

print $header;
include("userdiv.php");


print $footer;



?>