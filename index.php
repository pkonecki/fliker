<?php
session_start();
define('_VALID_INCLUDE', TRUE);
include("./includes/paths.php");


$header = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
 <head>
  <title>::Fliker::Accueil</title>
  <link rel="stylesheet" type="text/css" href="./includes/style.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
  <link rel="stylesheet" type="text/css" href="http://checkboxtree.googlecode.com/svn/tags/checkboxtree-0.5/jquery.checkboxtree.min.css" />
  <script type="text/javascript" src="./includes/js/jquery.js"></script>
  <script type="text/javascript" src="./includes/js/jquery-ui.js"></script>
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
	case 4:
		include("fiche_section.php");
	break;
	case 5:
		include("fiche_activite.php");
	break;
	case 6:
		include("fiche_creneau.php");
	break;	
}

print '</div>';
print $footer;



?>