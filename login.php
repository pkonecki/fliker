<?php
session_start();
include("./includes/paths.php");
include("Adherent.php");
$tab = getChampsAdherents();
$header = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
 <head>
  <title>::Fliker::Connexion</title>
  <link rel="stylesheet" type="text/css" href="./includes/style.css" />
 </head>
 <body>
<h1>Connexion</h1> ';

$footer = '</body></html>';




//If the user has submitted the form
if($_POST['submit']){
	include("opendb.php");
	//protect the posted value then store them to variables
	$username = protect($_POST['username']);
	$password = protect($_POST['password']);

	//Check if the username or password boxes were not filled in
	if(!$username || !$password){
		//if not display an error message
		echo "<center>Vous devez rentrer votre <b>email</b> et votre <b>mot de passe</b>!</center>";
	}else{
		//if the were continue checking

		//select all rows from the table where the username matches the one entered by the user
		$res = mysql_query("SELECT * FROM `adherent` WHERE `email` = '".$username."'");
		$num = mysql_num_rows($res);

		//check if there was not a match
		if($num == 0){
			//if not display an error message
			echo "<center>L'<b>email</b> que vous avez entrer n'existe pas!</center>";
		}else{
			//if there was a match continue checking

			//select all rows where the username and password match the ones submitted by the user
			$res = mysql_query("SELECT * FROM `adherent` WHERE `email` = '".$username."' AND `password` = MD5('".$password."')");
			$num = mysql_num_rows($res);

			//check if there was not a match
			if($num == 0){
				//if not display error message
				echo "<center>Le <b>mot de passe</b> que vous avez entré est erroné!</center>";
			}else{
				//if there was continue checking

				//split all fields fom the correct row into an associative array
				$row = mysql_fetch_assoc($res);

				//check to see if the user has not activated their account yet
				if($row['active'] != 1){
					//if not display error message
					echo "<center>Votre compte n'est pas <b>activé</b>!</center>";
				}else{
					//if they have log them in

					//set the login session storing there id - we use this to see if they are logged in or not
					$_SESSION['uid'] = $row['id'];
					$_SESSION['user'] = $username;

					foreach($tab as $champ){
						if($champ['type']==='select') $_SESSION[$champ['nom']]=$row['id_'.$champ['nom']];
						else
						$_SESSION[$champ['nom']]=$row[$champ['nom']];

					}


					//show message
					print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
							<html>
							 <head>
  								<title>::Fliker::Connexion</title>
  								<link rel="stylesheet" type="text/css" href="./includes/style.css" />
  								<meta http-equiv="refresh" content="3;url=index.php" />
  								 </head>
 								<body>';
					echo "<center>Vous êtes connecté!</center></body></html>";

					include("closedb.php");


				}
			}
		}
	}
}
else {
	print $header;
	print '
			<form action="login.php" method="post">
				<div id="border">
					<table cellpadding="2" cellspacing="0" border="0">
						<tr>
							<td>Email:</td>
							<td><input type="text" name="username" /></td>
						</tr>
						<tr>
							<td>Mot de passe:</td>
							<td><input type="password" name="password" /></td>
						</tr>
						<tr>
							<td colspan="2" align="center"><input type="submit" name="submit" value="Connexion" /></td>
						</tr>
						<tr>
							<td align="center" colspan="2"><a href="inscription/index.php">S\'inscrire</a> | <a href="forgot.php">Mot de passe oublié</a></td>
						</tr>
					</table>
				</div>
			</form>
			';
}
print $footer;



?>