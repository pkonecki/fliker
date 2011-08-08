<?php
$tot_asso=count(getAssociations($_SESSION['uid']));
$tot_sec=count(getSections($_SESSION['uid']));
$tot_act=count(getActivites($_SESSION['uid']));
$tot_cre=count(getCreneaux($_SESSION['uid']));
$tot = $tot_asso + $tot_sec + $tot_act + $tot_cre;
print '<ul id="main_menu">
<li><a href="index.php?page=1">Mon espace</a></li>';
if($tot > 0){
	print '<li><a href="index.php?page=2">Recherche</a></li>';
}
if($tot_asso > 0){
	print '<li><a href="index.php?page=3">Gestion Assos</a></li>';
}
if($tot_sec > 0){
	print '<li><a href="index.php?page=4">Gestion Sections</a></li>';
}
if($tot_act > 0){
	print '<li><a href="index.php?page=5">Gestion Activités</a></li>';
}
if($tot_cre > 0){
	print '<li><a href="index.php?page=6">Gestion Créneaux</a></li>';
}
print '</ul>';
?>