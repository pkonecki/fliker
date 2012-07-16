<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
include_once("./includes/paths.php");
include_once("General.php");
include_once("Adherent.php");

class EspaceMembre
{
    public function login()
	{
	
	}
    public function logout()
	{
		$_SESSION = array();
		session_destroy();
	}
    public function register()
	{
	
	}
	public function showMenu($page = 0)
	{
		print '<div id="top">';
		print '<span id="title"><h1>';
		print getParam('text_top.txt');
		print '</h1></span>';
		if(isset($_SESSION['user']))
			include("menu.php");
		print '<div class=userdiv id=userdiv >';
		if(!isset($_SESSION['user']))
			print '<a href="login.php">Connexion</a> | <a href="inscription.php">Inscription</a>';
		else 
			print 'Connecté en tant que <b>'.$_SESSION['user'].'</b> | <a href="index.php?page=logout">Déconnexion</a>';
		 print '</div>';
		print '</div>';
		if (isset($_SESSION['user']))
		{
			if ($page == 1)
				print '<ul id="submenu"><li><a class="selected" href="index.php?page=1&adh='.(isset($_GET['adh']) ? $_GET['adh'] : $_SESSION['uid']).'">Fiche Adhérent</a></li><li><a href="index.php?page=7&adh='.(isset($_GET['adh']) ? $_GET['adh'] : $_SESSION['uid']).'">Adhésions</a></li></ul>';
			else if ($page == 7)
				print '<ul id="submenu"><li><a href="index.php?page=1&adh='.(isset($_GET['adh']) ? $_GET['adh'] : $_SESSION['uid']).'">Fiche Adhérent</a></li><li><a class="selected" href="index.php?page=7&adh='.(isset($_GET['adh']) ? $_GET['adh'] : $_SESSION['uid']).'">Adhésions</a></li></ul>';
			else if ($page == 9)
				print '<ul id="submenu"><li><a class="selected" href="index.php?page=9">Général</a></li><li><a href="index.php?page=11">Notifications</a></li><li><a href="index.php?page=12">Utilisateurs</a></li><li><a href="index.php?page=13">Messages</a></li></ul>';
			else if ($page == 11)
				print '<ul id="submenu"><li><a href="index.php?page=9">Général</a></li><li><a class="selected" href="index.php?page=11">Notifications</a></li><li><a href="index.php?page=12">Utilisateurs</a></li><li><a href="index.php?page=13">Messages</a></li></ul>';
			else if ($page == 12)
				print '<ul id="submenu"><li><a href="index.php?page=9">Général</a></li><li><a href="index.php?page=11">Notifications</a></li><li><a class="selected" href="index.php?page=12">Utilisateurs</a></li><li><a href="index.php?page=13">Messages</a></li></ul>';
			else if ($page == 13)
				print '<ul id="submenu"><li><a href="index.php?page=9">Général</a></li><li><a href="index.php?page=11">Notifications</a></li><li><a href="index.php?page=12">Utilisateurs</a></li><li><a class="selected" href="index.php?page=13">Messages</a></li></ul>';
		}
	}
	
	public function addUser($session)
	{
		$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE email='".$session['email']."' ";
		include("opendb.php");
		$res = mysql_query($query);
		if (!$res || mysql_num_rows($res) > 0)
		{
			echo mysql_error();
			return (false);
		}
		newAdherent($session);
		include("opendb.php");
		$query = "SELECT * FROM {$GLOBALS['prefix_db']}config";
		$res = mysql_query($query);
		if (!$res)
		{
			echo mysql_error();
			return (false);
		}
		else
		{
			$i = 0;
			while ($array[$i] = mysql_fetch_array($res))
				$i++;
			include("opendb.php");
			$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE email='".$session['email']."' ";
			$res = mysql_query($query);
			if (!$res)
			{
				echo mysql_error();
				return (false);
			}
			else
			{
				$stock_adh = mysql_fetch_array($res);		
				$i = 0;
				while ($array[$i])
				{
					if ($array[$i]['id'] == "is_wiki" && $array[$i]['valeur'] == "true")
						$this->addWiki($stock_adh['id']);
					$i++;
				}
			}
		}
		return (true);
	}

	public function addWiki($id_user)
	{
		$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE id = '".$id_user."' ";
		include("opendb.php");
		$res = mysql_query($query);
		if (!$res)
		{
			echo mysql_error();
			return (false);
		}
		else
		{
			$array = mysql_fetch_array($res);
			include("closedb.php");
			include("opendb_wiki.php");
			$user_name = $array['email'];
			if ($user_name != "")
				$user_name[0] = strtoupper($user_name[0]);
			$user_mdp = $array['password'];
			$result = mysql_query("INSERT INTO mw_user (user_name, user_password, user_registration, user_editcount) VALUES ('".$user_name."', CONCAT(':A:', '".$user_mdp."'), CURRENT_TIMESTAMP()+0, 0) ");
			if (!$result)
			{
				echo mysql_error();
				return (false);
			}
		}
		return (true);
	}
	
	public function updateUser($column, $value, $email)
	{
		if ($column == "email")
		{
			$query = "SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE email='".$value."' ";
			include("opendb.php");
			$res = mysql_query($res);
			if (!$res || mysql_num_rows($res) > 0)
			{
				echo mysql_error();
				return (false);
			}
		}
		if ($column == "password")
			$query= "UPDATE {$GLOBALS['prefix_db']}adherent SET $column= MD5('$value') WHERE email='$email' ";
		else
			$query= "UPDATE {$GLOBALS['prefix_db']}adherent SET $column='$value' WHERE email='$email' ";
		include("opendb.php");
		$res = mysql_query($query);
		if (!$res)
		{
			echo mysql_error();
			return (false);
		}
		else if ($column == "email" || $column == "password")
		{
			include("opendb.php");
			$query = "SELECT * FROM {$GLOBALS['prefix_db']}config";
			$res = mysql_query($query);
			if (!$res)
			{
				echo mysql_error();
				return (false);
			}
			else
			{
				$i = 0;
				while ($array[$i] = mysql_fetch_array($res))
					$i++;
				$i = 0;
				while ($array[$i])
				{
					if ($array[$i]['id'] == "is_wiki" && $array[$i]['valeur'] == "true")
						$this->updateWiki($column, $value, $email);
					$i++;
				}
			}
		}
		return (true);
	}
	
	public function updateWiki($column, $value, $email)
	{
		$email[0] = strtoupper($email[0]);
		$query = "";
		if ($column == "email")
		{
			$value[0] = strtoupper($value[0]);
			$query= "UPDATE mw_user SET user_name='$value' WHERE user_name='$email' ";
		}
		else if ($column == "password")
			$query= "UPDATE mw_user SET user_password=CONCAT(':A:', MD5('".$value."')), user_touched=CURRENT_TIMESTAMP()+0 WHERE user_name='$email' ";
		include("opendb_wiki.php");
		$res = mysql_query($query);
		if (!$res)
		{
			echo mysql_error();
			return (false);
		}
		return (true);
	}
}

?>