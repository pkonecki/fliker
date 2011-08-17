<?php
$tot_asso=count(getAssociations($_SESSION['uid']));
$tot_sec=count(getSections($_SESSION['uid']));
$tot_act=count(getActivites($_SESSION['uid']));
$tot_cre=count(getCreneaux($_SESSION['uid']));
$tot = $tot_asso + $tot_sec + $tot_act + $tot_cre;
print '<ul id="main_menu">
<li><a class="'.(($_GET['page']==1|| $_GET['page']==7) ? 'selected' : '').'" href="index.php?page=1">Mon espace</a></li>';
if($tot > 0){
	print '<li><a class="'.(($_GET['page']==2) ? 'selected' : '').'" href="index.php?page=2">Recherche</a></li>';
}
if($tot_asso > 0){
	print '<li><a class="'.(($_GET['page']==3 || $_GET['page']==4 || $_GET['page']==5 || $_GET['page']==6) ? 'selected' : '').'" href="index.php?page=3">Gestion</a></li>';
} else
if($tot_sec > 0){
	print '<li><a class="'.(($_GET['page']==4 || $_GET['page']==4 || $_GET['page']==5 || $_GET['page']==6) ? 'selected' : '').'" href="index.php?page=4">Gestion</a></li>';
} else
if($tot_act > 0){
	print '<li><a class="'.(($_GET['page']==5 || $_GET['page']==4 || $_GET['page']==5 || $_GET['page']==6) ? 'selected' : '').'" href="index.php?page=5">Gestion</a></li>';
} else
if($tot_cre > 0){
	print '<li><a class="'.(($_GET['page']==6 || $_GET['page']==4 || $_GET['page']==5 || $_GET['page']==6) ? 'selected' : '').'" href="index.php?page=6">Gestion</a></li>';
}
if($tot > 0){
	print '<li><a class="'.(($_GET['page']==8) ? 'selected' : '').'" href="index.php?page=8&week='.week_isonumber(time()).'">Présence</a></li>';
}
print '</ul>';
?>