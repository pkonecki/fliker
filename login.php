<?php
session_start();
define('_VALID_INCLUDE', TRUE);
include("./includes/paths.php");
include("Adherent.php");
include_once("General.php");
include_once("EspaceMembre.class.php");

$tab = getChampsAdherents();
$output = "";
//If the user has submitted the form
if(isset($_POST['submit']))
{
	$output .= '<br/><font color="#	FF	00	00">';
	include("opendb.php");
	$res = mysql_query("SELECT * FROM {$GLOBALS['prefix_db']}config WHERE id = 'msgError_email.txt'");
	$array_res_email = mysql_fetch_array($res);
	$res = mysql_query("SELECT * FROM {$GLOBALS['prefix_db']}config WHERE id = 'msgError_mdp.txt'");
	$array_res_mdp = mysql_fetch_array($res);
	$username = protect($_POST['username']);
	$password = protect($_POST['password']);

	if(!$username || !$password)
        $output .= "Vous devez entrer votre <b>email</b> et votre <b>mot de passe</b> !";
	else
	{
		//select all rows from the table where the username matches the one entered by the user
		$res = mysql_query("SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE `email` = '".$username."'");
		$num = mysql_num_rows($res);
		if($num == 0)
			$output .= $array_res_email['valeur'];
		else
		{
			//select all rows where the username and password match the ones submitted by the user
			$res = mysql_query("SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE `email` = '".$username."' AND `password` = MD5('".$password."')");
			$num = mysql_num_rows($res);

			if($num == 0)
				$output .= $array_res_mdp['valeur'];
			else
			{
				//split all fields fom the correct row into an associative array
				$row = mysql_fetch_assoc($res);

				//check to see if the user has not activated his account yet
				if($row['active'] != 1)
					  $output .= "Désolé, votre compte a été <b>désactivé</b> !<br>Prenez contact avec nos <a href=\"".getParam("url_resiliation.conf")."\">administrateurs</a>.";
				else
				{
					//set the login session storing there id - we use this to see if they are logged in or not
					$_SESSION['uid'] = $row['id'];
					$_SESSION['user'] = $username;

					foreach($tab as $champ)
					{
						if($champ['type']==='select')
							$_SESSION[$champ['nom']]=$row['id_'.$champ['nom']];
						else
							$_SESSION[$champ['nom']]=$row[$champ['nom']];
					}
					include("closedb.php");
					include("opendb.php");
					$res = mysql_query("SELECT valeur FROM {$GLOBALS['prefix_db']}config WHERE id = 'dest_redirect.conf' ");
					$stock_res = mysql_fetch_array($res);
					header("location: ".$stock_res['valeur']."") ;
				}
			}
		}
	}
	$output .= '</font><br /><br />';
}
print 	'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
		<html>
		<head>
		<title>'.getParam('text_top.txt').'</title>
		<link rel="stylesheet" type="text/css" href="./includes/style.css" />
		</head>
		<body>';
		
$EspaceMembre = new EspaceMembre;
$EspaceMembre->showMenu();
print '<h2>Connexion</h2>';
print $output;

print '<form action="login.php" method="post">
			<div id="border">
				<table cellpadding="2" cellspacing="0" border="0">
					<tr>
						<td>Email :</td>
						<td><input type="text" name="username" /></td>
					</tr>
					<tr>
						<td>Mot de passe :</td>
						<td><input type="password" name="password" /></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><input type="submit" name="submit" value="Connexion" /></td>
					</tr>
					<tr>
						<td align="center" colspan="2"><a href="inscription.php">S\'inscrire</a> | <a href="forgot.php">Mot de passe oublié</a></td>
					</tr>
				</table>
			</div>
		</form>
		';
print '</body></html>';

?>