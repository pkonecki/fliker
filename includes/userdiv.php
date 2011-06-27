<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
print '<div class=userdiv id=userdiv >';
if(strcmp($_SESSION['user'],"") == 0){
	print '<a href="login.php">Connexion</a> | <a href="inscription/index.php">Inscription</a>';
} else {
	print 'Connecté en tant que <b>'.$_SESSION['user'].'</b> | <a href="logout.php">Déconnexion</a>';
}
 print '</div>'



?>