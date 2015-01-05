<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
if(isset($_SESSION['user'])){
$tot_asso=count(getAssociations($_SESSION['uid']));
$tot_sec=count(getSections($_SESSION['uid']));
$tot_act=count(getActivites($_SESSION['uid']));
$tot_cre=count(getCreneaux($_SESSION['uid']));
}
$tot = $tot_asso + $tot_sec + $tot_act + $tot_cre;

if (isset($_GET['page'])){
	$tmp_stock1 = (($_GET['page']==1|| $_GET['page']==7) ? 'selected' : '');
	$tmp_stock2 = (($_GET['page']==21) ? 'selected' : '');
	$tmp_stock3 = (($_GET['page']==23) ? 'selected' : '');
}

$info = pathinfo($_SERVER['PHP_SELF']);
$actuel = substr($info['basename'], 0, -4);

if($actuel == "login"){$tmp_stock4 = 'selected';}
if($actuel == "inscription"){$tmp_stock5 = 'selected';}

if(isset($_SESSION['user']))
{$menu_logger='<li><a class="'.$tmp_stock1.'" href="index.php?page=7&adh='.$_SESSION['uid'].'">Mon espace</a></li>';}

else{
$menu_logger='
<li><a class="'.$tmp_stock3.'" href="index.php?page=23">Accueil</a></li>
<li><a class="'.$tmp_stock4.'" href="login.php">Connexion</a></li>
<li><a class="'.$tmp_stock5.'" href="inscription.php">Inscription</a></li>
';
}

print '
<ul id="main_menu">
'.$menu_logger.'
<li><a class="'.$tmp_stock2.'" href="index.php?page=21">Liste des Sports</a></li>
';

if($tot > 0)
{
	if (isset($_GET['page']))
		$tmp_stock = (($_GET['page']==2) ? 'selected' : '');
	else
		$tmp_stock = '';
	print '<li><a class="'.$tmp_stock.'" href="index.php?page=2">Recherche</a></li>';
}
if($_SESSION['privilege']==='1' || $tot_asso > 0)
{
	if (isset($_GET['page']))
		$tmp_stock = (($_GET['page']==3 || $_GET['page']==4 || $_GET['page']==5 || $_GET['page']==6 || $_GET['page']==12 || $_GET['page']==20) ? 'selected' : '');
	else
		$tmp_stock = '';
	print '<li><a class="'.$tmp_stock.'" href="index.php?page=3">Gestion</a></li>';
}
else if($tot_sec > 0)
{
	if (isset($_GET['page']))
		$tmp_stock = (($_GET['page']==3 || $_GET['page']==4 || $_GET['page']==5 || $_GET['page']==6 || $_GET['page']==12 || $_GET['page']==20) ? 'selected' : '');
	else
		$tmp_stock = '';
	print '<li><a class="'.$tmp_stock.'" href="index.php?page=4">Gestion</a></li>';
}
else if($tot_act > 0)
{
	if (isset($_GET['page']))
		$tmp_stock = (($_GET['page']==3 || $_GET['page']==4 || $_GET['page']==5 || $_GET['page']==6 || $_GET['page']==12 || $_GET['page']==20) ? 'selected' : '');
	else
		$tmp_stock = '';
	print '<li><a class="'.$tmp_stock.'" href="index.php?page=5">Gestion</a></li>';
}
else if($tot_cre > 0)
{
	if (isset($_GET['page']))
		$tmp_stock = (($_GET['page']==3 || $_GET['page']==4 || $_GET['page']==5 || $_GET['page']==6 || $_GET['page']==12 || $_GET['page']==20) ? 'selected' : '');
	else
		$tmp_stock = '';
	print '<li><a class="'.$tmp_stock.'" href="index.php?page=6">Gestion</a></li>';
}
if ($_SESSION['privilege']==='1' || $tot_sec > 0 || $tot_asso > 0)
{
	if (isset($_GET['page']))
		$tmp_stock = (($_GET['page']==14 || $_GET['page']==15 || $_GET['page']==16 || $_GET['page']==17 || $_GET['page']==18) ? 'selected' : '');
	else
		$tmp_stock = '';
	print '<li><a class="'.$tmp_stock.'" href="index.php?page=16">Finances</a></li>';
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
		$tmp_stock = (($_GET['page']==9 || $_GET['page']==11 || $_GET['page']==13 || $_GET['page']==19) ? 'selected' : '');
	else
		$tmp_stock = '';
	print '<li><a class="'.$tmp_stock.'" href="index.php?page=9">Admin</a></li>';
	
}


if (isset($_GET['page']))
	$tmp_stock3 = (($_GET['page']==22) ? 'selected' : '');
else
	$tmp_stock3 = '';

print '
<li><a class="'.$tmp_stock3.'" href="index.php?page=22">Contact</a></li>
';


print '</ul>';


if(!empty($_SESSION['tab_responsabilite'])){

	print '<form><h4>Basculer en tant que Responsable
	<select name="responsabilite">
	<option value="normal">Utilisateur Normal</option>
	';

	foreach($_SESSION['tab_responsabilite'] as $entite => $tab){
	
		foreach($tab as $id_ent => $nom)
			print '<option value="'.$entite.'-'.$id_ent.'">'.$entite.' : '.$nom.'</option>';
		
	}
		
	if($_SESSION['privilege'] == 1)
		print '<option value="admin">Administrateur</option>';

	print '</select></h4>
	</form>';

}


?>
