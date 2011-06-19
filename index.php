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
  <link rel="stylesheet" type="text/css" href="http://checkboxtree.googlecode.com/svn/tags/checkboxtree-0.5/jquery.checkboxtree.min.css" />
 </head>
 <body>
<h1>Fliker</h1> ';

$footer = '</body></html>';

print $header;
include("userdiv.php");
include("menu.php");
print '<div id="content">';
if(empty($_GET['page'])) $_GET['page']=1;

switch($_GET['page']){
	case 1:
		include("fiche_adherent.php");
		break;
	case 2:
		include("search.php");
		break;
	case 3:
		include("fiche_asso.php");
	break;
}

print '</div>';
print $footer;



?>