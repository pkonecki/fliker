<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
include_once("./includes/paths.php");
include_once("General.php");

class EspaceMembre
{
    public function login()
	{
	
	}
    public function logout()
	{
		session_destroy();
	}
    public function register()
	{
	
	}
	public function showMenu()
	{
		print '<div id="top">';
		print '<span id="title">';
		print getParam('text_top');
		print '</span>';
		if(isset($_SESSION['user']))
			include("menu.php");
		print '<div class=userdiv id=userdiv >';
		if(!isset($_SESSION['user']))
			print '<a href="login.php">Connexion</a> | <a href="inscription.php">Inscription</a>';
		else 
			print 'Connecté en tant que <b>'.$_SESSION['user'].'</b> | <a href="logout.php">Déconnexion</a>';
		 print '</div>';
		print '</div>';
	}
}

?>