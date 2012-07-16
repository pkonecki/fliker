<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
if(!($_SESSION['privilege'] === '1'))
{
	print "Vous n'avez pas accès à cette page.";
	print $die_footer;
	die();
}

if(isset($_GET['promo']))
	$promo = $_GET['promo'];
else
	$promo = $current_promo;

if(isset($_POST['action']) && $_POST['action'] === "setparam")
	setParam($_POST['id'], htmlspecialchars($_POST['valeur']));
$params = getConfig("txt");
$params_2 = getConfigBis("txt");
$table_config = "<table id=\"table_config\" >";
foreach($params as $key => $value)
{
	$value = htmlentities($value);
	$table_config .= "<FORM action=\"index.php?page=13\" method=\"POST\">
					  <tr><td><input type=\"text\" name=\"valeur\" value=\"$value\"></input></td><td width='220'>".$params_2[$key]."</td><td><input type=\"submit\" /></td></tr>
					  <input type=\"hidden\" name=\"action\" value=\"setparam\">
					  <input type=\"hidden\" name=\"id\" value=\"$key\">
					  </FORM>";
}
$table_config .= "</table>";
print $table_config;
?>