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

$params = getConfig("notif");
$params_2 = getConfigBis("notif");
$table_config = "<table id=\"table_config\" >";
foreach($params as $key => $value)
{
	$value = htmlentities($value);
	$is_selected = $value;
	$table_config .= "<FORM action=\"index.php?page=11\" method=\"POST\">
					  <tr><td width='220'>".$params_2[$key]."</td>
					  <td><input type='radio' name='valeur' value='never' ".($is_selected == 'never' ? 'checked' : '').">Jamais</input><input type='radio' name='valeur' value='now' ".($is_selected == 'now' ? 'checked' : '').">Immédiat</input><input type='radio' name='valeur' value='daily' ".($is_selected == 'daily' ? 'checked' : '').">Chaque jour</input><input type='radio' name='valeur' value='weekly' ".($is_selected == 'weekly' ? 'checked' : '').">Chaque semaine</input><input type='radio' name='valeur' value='monthly' ".($is_selected == 'monthly' ? 'checked' : '').">Chaque mois</input></td>
					  <td><input type=\"submit\" /></td></tr>
					  <input type=\"hidden\" name=\"action\" value=\"setparam\">
					  <input type=\"hidden\" name=\"id\" value=\"$key\">
					  </FORM>";
}
$table_config .= "</table>";
print $table_config;

?>