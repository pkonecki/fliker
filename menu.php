<?php
$tot_asso=count(getAssociations($_SESSION['uid']));
$tot_sec=count(getSections($_SESSION['uid']));
$tot_act=count(getActivites($_SESSION['uid']));
$tot_cre=count(getCreneaux($_SESSION['uid']));
$tot = $tot_asso + $tot_sec + $tot_act + $tot_cre;
if (isset($_GET['page']))
	$tmp_stock = (($_GET['page']==1|| $_GET['page']==7) ? 'selected' : '');
else
	$tmp_stock = '';
print '<ul id="main_menu">
<li><a class="'.$tmp_stock.'" href="index.php?page=1">Mon espace</a></li>';
if($tot > 0)
{
	if (isset($_GET['page']))
		$tmp_stock = (($_GET['page']==2) ? 'selected' : '');
	else
		$tmp_stock = '';
	print '<li><a class="'.$tmp_stock.'" href="index.php?page=2">Recherche</a></li>';
}
if($tot_asso > 0)
{
	if (isset($_GET['page']))
		$tmp_stock = (($_GET['page']==3 || $_GET['page']==4 || $_GET['page']==5 || $_GET['page']==6) ? 'selected' : '');
	else
		$tmp_stock = '';
	print '<li><a class="'.$tmp_stock.'" href="index.php?page=3">Gestion</a></li>';
}
else if($tot_sec > 0)
{
	if (isset($_GET['page']))
		$tmp_stock = (($_GET['page']==4 || $_GET['page']==4 || $_GET['page']==5 || $_GET['page']==6) ? 'selected' : '');
	else
		$tmp_stock = '';
	print '<li><a class="'.$tmp_stock.'" href="index.php?page=4">Gestion</a></li>';
}
else if($tot_act > 0)
{
	if (isset($_GET['page']))
		$tmp_stock = (($_GET['page']==5 || $_GET['page']==4 || $_GET['page']==5 || $_GET['page']==6) ? 'selected' : '');
	else
		$tmp_stock = '';
	print '<li><a class="'.$tmp_stock.'" href="index.php?page=5">Gestion</a></li>';
}
else if($tot_cre > 0)
{
	if (isset($_GET['page']))
		$tmp_stock = (($_GET['page']==6 || $_GET['page']==4 || $_GET['page']==5 || $_GET['page']==6) ? 'selected' : '');
	else
		$tmp_stock = '';
	print '<li><a class="'.$tmp_stock.'" href="index.php?page=6">Gestion</a></li>';
}
if($tot > 0)
{
	if (isset($_GET['page']))
		$tmp_stock = (($_GET['page']==8) ? 'selected' : '');
	else
		$tmp_stock = '';
	print '<li><a class="'.$tmp_stock.'" href="index.php?page=8">Présence</a></li>';
}
if($_SESSION['privilege']==='1')
{
	if (isset($_GET['page']))
		$tmp_stock = (($_GET['page']==9) ? 'selected' : '');
	else
		$tmp_stock = '';
	print '<li><a class="'.$tmp_stock.'" href="index.php?page=9">Admin</a></li>';
	
}
print '</ul>';
?>