<?php
session_start();
include("./includes/paths.php");

$header = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
 <head>
  <title>::Fliker::Connexion</title>
  <link rel="stylesheet" type="text/css" href="./includes/style.css" />
  <meta http-equiv="refresh" content="3;url=index.php" />
 </head>
 <body>
<h1>D�connexion</h1> ';

$footer = '</body></html>';

print $header;
if(strcmp($_SESSION['uid'],"") == 0){
			//if it doesn't display an error message
			echo "<center>Vous devez �tre connect� pour vous d�connecter !</center>";
}
else{
			//if it does continue checking

			//destroy all sessions canceling the login session
			session_destroy();

			//display success message
			echo "<center>Vous �tes bien d�connect� !</center>";
}
print $footer;

?>
