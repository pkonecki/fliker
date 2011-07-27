<?php
session_start();
define('_VALID_INCLUDE', TRUE);
include("./includes/paths.php");
include_once("Adherent.php");
include_once("Activite.php");
include_once('Creneau.php');
include_once("Asso.php");
include_once("Section.php");
include_once('Select.php');
include_once('Adhesion.php');
include_once('Supplement.php');
$header = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
 <head>
  <title>::Fliker::Accueil</title>
  <link rel="stylesheet" type="text/css" href="./includes/style.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery-ui-timepicker.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery.multiselect.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery.multiselect.filter.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery.checkboxtree.min.css" />
  <script type="text/javascript" src="./includes/js/jquery.js"></script>
  <script type="text/javascript" src="./includes/js/jquery-ui.js"></script>
  <script type="text/javascript" src="./includes/js/jquery.ui.timepicker.js"></script>
  <script type="text/javascript" src="./includes/js/jquery.multiselect.min.js"></script>
  <script type="text/javascript" src="./includes/js/jquery.multiselect.filter.js"></script>
  <script type="text/javascript" src="./includes/js/jquery.checkboxtree.min.js"></script>
 </head>
 <body>
<div id="top">
<h1>Fliker</h1> ';
$footer = '</body></html>';
print $header;
include("userdiv.php");
print '</div>';
if(!(strcmp($_SESSION['user'],"") == 0)){
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
		case 7:
			include("fiche_adhesion.php");
		break;
	}
	print '</div>';
} else print 'Vous n\'�tes pas connect�';

print $footer;

?>
<script type="text/javascript">
$(".filterselect").multiselect({
   multiple: false,
   header: "Choisissez un",
   noneSelectedText: "Choisissez un",
   selectedList: 1
}).multiselectfilter();
</script>