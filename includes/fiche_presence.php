<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
$tab=getCreneaux($_SESSION['uid']);
if(isset($_POST['promo'])) {
	$promo=$_POST['promo'];
} else {
	$promo=$current_promo;
}

if (isset($_POST['modif_week']))
	$modif_week = $_POST['modif_week'];
else
	$modif_week = 0;

if (isset($_POST['save']))
{
	$compteur = 0;
	while (isset($_POST[$compteur]))
	{
		if (($_POST["_$compteur"] != "checked" && isset($_POST[$_POST[$compteur]])) || ($_POST["_$compteur"] == "checked" && !isset($_POST[$_POST[$compteur]])))
		{
			$tmp_cont = explode('--', $_POST[$compteur]);
			modifPresence($tmp_cont[0],$tmp_cont[1], $tmp_cont[2],$tmp_cont[3], isset($_POST[$_POST[$compteur]]));
		}
		$compteur++;
	}
}
else if (isset($_POST['previous']))
	$modif_week--;
else if (isset($_POST['next']))
	$modif_week++;
else if (isset($_POST['current']))
	$modif_week = 0;

if ($modif_week > 100)
	$modif_week = 100;
if ($modif_week < -100)
	$modif_week = -100;

$output = "<div class=\"tip\">".getParam('text_presence.txt')."</div>";
if(isset($_POST['cre'])) {
	$cre = $_POST['cre'];
	$adhs = getAdherentsByCreneau($cre,$promo);
	if(isset($_POST['week'])) {
		$current_week=$_POST['week'];
	} else {
		$current_week=date('W');
	}
	$creneau = $tab[$cre];
	switch ($creneau['jour_cre']){
		case "Lundi":
			$jour_num=1;
			break;
		case "Mardi":
			$jour_num=2;
			break;
		case "Mercredi":
			$jour_num=3;
			break;
		case "Jeudi":
			$jour_num=4;
			break;
		case "Vendredi":
			$jour_num=5;
			break;
		case "Samedi":
			$jour_num=6;
			break;
		case "Dimanche":
			$jour_num=7;
			break;
	}
	$date_debut = "09/01";
	$date_fin = "06/30";
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}config";
	include("opendb.php");
	$res = mysql_query($query);
	if (!$res)
		echo mysql_error();
	else
	{
		while ($tmp_date = mysql_fetch_array($res))
		{
			if ($tmp_date['id'] == "date_debut_promo")
				$date_debut = $tmp_date['valeur'];
			if ($tmp_date['id'] == "date_fin_promo")
				$date_fin = $tmp_date['valeur'];
		}
	}
	$pre_promo = $promo - 1;
	$w_debut = strtotime("".$date_debut."/{$pre_promo}");
	$w_debut = strtotime("next Monday", $w_debut);
	$w_fin = strtotime("".$date_fin."/{$promo}");
	$date_now = strtotime("now");
	$date_now = strtotime("next Monday", $date_now);
	$date_now = strtotime("$modif_week week", $date_now);
	$date_end = strtotime("+1 week", $date_now);
	$date_now = strtotime("-7 week", $date_now);
	if ($date_end > $w_fin)
	{
		$modif_week--;
		$date_end = $w_fin;
		$date_now = strtotime("-8 week", $date_end);
	}
	else if ($date_now < $w_debut)
	{
		$modif_week++;
		$date_now = $w_debut;
		$date_end = strtotime("+8 week", $date_now);
	}
	if (isset($_POST['all_tab']))
	{
		$date_now = $w_debut;
		$date_end = $w_fin;
	}
	$date = $date_now;
	if (isset($_POST['all_tab']))
		$output.= "<table><form class=\"auto\" action=\"index.php?page=8\" method=\"POST\"><thead><tr><th></th><th>Jour<br>Mois</th><th></th>";
	else
		$output.= "<table><form class=\"auto\" action=\"index.php?page=8\" method=\"POST\"><thead><tr><th></th><th>Jour<br>Mois</th><th><input type='submit' name='previous' value='<<'></th>";
	while ($date < $date_end){
		$week=strftime("%U",$date);
		$p=strftime("%Y",$date);
		$range = utf8_decode(strftime("%d<br>%m",strtotime("$p-W$week-$jour_num")));
		$output.= "<th>$range</th>";
		$date = strtotime("+1 week",$date);
	}
	if (isset($_POST['all_tab']))
		$output.= "<th></th></tr></thead>";
	else
		$output.= "<th><input type='submit' name='next' value='>>'></th></tr></thead>";
	$date=$date_now;

	$output .= "<tr><th colspan='3' >Nombre de pr�sents</th>";
	$query = "SELECT * FROM {$GLOBALS['prefix_db']}presence WHERE id_cre='$cre' AND promo='$promo'";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results)
		echo mysql_error();
	$week_calc = strftime("%U",$date);
	$i = 0;
	$tmp_ressource = array();
	while ($tmp_ressource[$i] = mysql_fetch_array($results))
		$i++;
	include("closedb.php");
	$date = $date_now;
	while ($date < $date_end)
	{
		$count_week = 0;
		$i = 0;
		while ($tmp_array = $tmp_ressource[$i])
		{
			if ($tmp_array['week'] == $week_calc)
				$count_week++;
			$i++;
		}
		if ($week_calc == 52)
			$week_calc = 1;
		else
			$week_calc++;
		$date = strtotime("+1 week", $date);
		$output .= "<th align='center'>$count_week</th>";
	}
	$output .= "<td></td></tr>";
	$output.= "<input type=\"hidden\" name=\"cre\" value=\"$cre\">
	<input type=\"hidden\" name=\"promo\" value=\"$promo\">
	<input type=\"hidden\" name=\"modif_week\" value=\"$modif_week\">";
	$compteur_id = 0;
	$i = 0;
	$count = 0;
	foreach($adhs as $id_adh => $row)
	{	
		$i++;
		$count_array = 0;
		$count_pre = 0;
		while ($tmp_array = $tmp_ressource[$count_array])
		{
			if ($tmp_array['id_adh'] == $row['id'])
				$count_pre++;
			$count_array++;
		}
		$output.= "<tr><th>{$i}</th><th>{$row['prenom']}<br>{$row['nom']}</th><th>$count_pre</th>";
		$date=$date_now;
		$count_array = 0;
		$array_id = array();
		while ($tmp_array = $tmp_ressource[$count_array])
		{
			if ($tmp_array['id_adh'] == $row['id'])
				$array_id[$tmp_array['week']] = 1;
			$count_array++;
		}
		$count = 0;
		while ($date < $date_end)
		{
			$week=strftime("%W",$date);
			if ($week[0] == '0')
				$week = $week[1];
			$output.= "<td ".($week==$current_week ? 'bgcolor=lightgreen' : '')." >";
			if (isset($array_id[$week]))
				$presence = 'checked';
			else
				$presence = '';
			/*$presence = (etaitPresent($id_adh,$cre,$week,$promo) ? 'checked' : '');*/
			$output.= "<input type=\"hidden\" name=\"_$compteur_id\" value=\"".$presence."\">";
			$output.= "<input type=\"hidden\" name=\"$compteur_id\" value=\"$id_adh--$cre--$week--$promo\">
			<input type=\"checkbox\" name=\"$id_adh--$cre--$week--$promo\" ".$presence." />
			</td>";
			$date = strtotime("+1 week",$date);
			$compteur_id++;
			$count++;
		}
		$output.= "<td></td></tr>";
	}
	$count += 3;
	if (isset($_POST['all_tab']))
		$tmp_string = '<input  type="submit" name="part_tab" value="Afficher partiellement" />';
	else
		$tmp_string = '<input  type="submit" name="all_tab" value="Afficher tout" /><input  type="submit" name="current" value="Retour semaine courante" />';
	$output.= '<tr><td colspan="'.$count.'" align="right">'.$tmp_string.'<input  type="submit" name="save" value="Sauvegarder" /></td><td></td></tr>';
	$output.= "</form>";
	$output.= "</table>";
}
else
{
	$output.= "<form class=\"toggle\" action=\"index.php?page=8\" method=\"POST\" >";
	$query = "SELECT DISTINCT promo FROM {$GLOBALS['prefix_db']}presence ORDER BY promo DESC";
	include("opendb.php");
	$res = mysql_query($query);
	if (!$res)
		echo mysql_error();
	else
	{
		$output.= "<p>Promo :<SELECT id=\"promo\" name=\"promo\" >";
		while ($array_promo = mysql_fetch_array($res))
			$output.= "<OPTION value=\"".$array_promo['promo']."\" ".(isset($_GET['promo']) && $_GET['promo']==$array_promo['promo'] ? "selected" : "")." >".$array_promo['promo']."</OPTION>";
		$output.= "</SELECT></p>";
	}
	$output .= "<table><tr><th></th><th>Section</th><th>Activit�</th><th>Jour</th><th>Heure de d�but</th><th>Heure de fin</th><th>Inscrits</th><th>R�guliers</th><th align=center>Pr�sence des r�guliers<br/>(en %)</th></tr>";
	$tab_regular = getAdherentsByPromo($promo);
	foreach($tab as $creneau)
	{
		$cre = $creneau['id_cre'];
		$count_presence = 0;
		$inc = 0;
		while ($tmp_array = $tab_regular[$inc])
		{
			if ($tmp_array['id_cre'] == $cre)
				$count_presence++;
			$inc++;
		}
		$tmp_value = sizeof(getAdherentsByCreneau($cre, $promo));
		$count_presence *= 100;
		if ($tmp_value == 0)
			$tmp_value = 1;
		$count_presence /= ($tmp_value * 43);
		$count_presence = round($count_presence);
		$i = 0;
		$tmp_tab_adh = null;
		$tmp_tab_value = null;
		$count_value = 0;
		$tab_week = null;
		while ($tmp_array = $tab_regular[$i])
		{
			if ($tmp_array['id_cre'] == $cre)
			{
				$tab_week[$tmp_array['week']] = 1;
				if (isset($tmp_tab_value[$tmp_array['id_adh']]))
					$tmp_tab_value[$tmp_array['id_adh']]++;
				else
				{
					$tmp_tab_adh[$count_value] = $tmp_array['id_adh'];
					$tmp_tab_value[$tmp_array['id_adh']] = 1;
					$count_value++;
				}
			}
			$i++;
		}
		$i = 0;
		$count_regular_adh = 0;
		$tmp_string = 0;
		$count_presence = 0;
		while (isset($tmp_tab_adh[$i]))
		{
			if ($tmp_tab_value[$tmp_tab_adh[$i]] > 2)
			{
				$count_presence += $tmp_tab_value[$tmp_tab_adh[$i]];
				$count_regular_adh++;
			}
			$i++;
		}
		$count_presence *= 100;
		$number_week = 0;
		$i = 0;
		while ($i != 60)
		{
			if (isset($tab_week[$i]) && $tab_week[$i] == 1)
				$number_week++;
			$i++;
		}
		if ($count_regular_adh != 0)
			$count_presence /= ($count_regular_adh * $number_week);
		$count_presence = round($count_presence);
		$output.= '<tr><div><td><input type="radio" name="cre" value='.$cre.' ></td><h4><td>'.$creneau['nom_sec'].'</td><td>'.$creneau['nom_act'].'</td><td>'.$creneau['jour_cre'].'</td><td>'.$creneau['debut_cre'].'</td><td>'.$creneau['fin_cre'].'</td><td align=center>'.$tmp_value.'</td><td align=center>'.$count_regular_adh.'</td><td align=center>'.$count_presence.'</td></h4></input></div></tr>';
	}
	$output.= '</table><input type="submit" value="Ouvrir" /></form>';
}
print $output;
?>
<script type="text/javascript">
$(".auto").change( function (){
	$(this).submit();
});
</script>
