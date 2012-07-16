<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
$tot_asso=count(getAssociations($_SESSION['uid']));
$tot_sec=count(getSections($_SESSION['uid']));
$tot_act=count(getActivites($_SESSION['uid']));
$tot_cre=count(getCreneaux($_SESSION['uid']));
$tot = $tot_asso + $tot_sec + $tot_act + $tot_cre;
if((strcmp($_SESSION['user'],"") == 0))
{
	print "<p>Vous n'êtes pas connecté</p>";
	die();
}

if ($_SESSION['privilege'] == 1)
	$tab_asso = getAssociations($_SESSION['uid']);
else if (isset($tot_asso) && $tot_asso > 0)
	$tab_asso = getAssociations($_SESSION['uid']);
else if (isset($tot_sec) && $tot_sec > 0)
{
	$tab_section = getSections($_SESSION['uid']);
	$string_id_sec = "";
	foreach ($tab_section as $tmp_array)
		$string_id_sec .= ", ". $tmp_array['id'];
	$string_id_sec[0] = ' ';
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}asso_section WHERE id_sec IN (".$string_id_sec.")");
	$string_id_asso = "";
	while ($tmp_array = mysql_fetch_array($res))
		$string_id_asso .= ", ".$tmp_array['id_asso'];
	$string_id_asso[0] = ' ';
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}association WHERE id IN (".$string_id_asso.")");
	while ($tmp_array = mysql_fetch_array($res))
		$tab_asso[$tmp_array['id']] = $tmp_array;
}
else
{
	print "<p>Vous n'avez pas accès à cette page!</p>";
	die();
}

if(isset($_GET['promo']))
	$promo=$_GET['promo'];
else
	$promo=$current_promo;

if(!(strcmp($_SESSION['user'],"") == 0))
{
	$tab=getAssociations($_SESSION['uid']);
	print '<ul id="submenu">';
	if((isset($tot_asso) && $tot_asso > 0) || (isset($tot_sec) && $tot_sec > 0))
		print '<li><a class="'.(($_GET['page']== 16) ? 'selected' : '').'" href="index.php?page=16">Opérations</a></li>';
	if((isset($tot_asso) && $tot_asso > 0) || (isset($tot_sec) && $tot_sec > 0))
		print '<li><a class="'.(($_GET['page']==14) ? 'selected' : '').'" href="index.php?page=14">Récapitulatif</a></li>';
	if((isset($tot_asso) && $tot_asso > 0) || (isset($tot_sec) && $tot_sec > 0))
		print '<li><a class="'.(($_GET['page']==17) ? 'selected' : '').'" href="index.php?page=17">Inventaire</a></li>';
	print '</ul>';
}
$query = "SELECT DISTINCT promo FROM {$GLOBALS['prefix_db']}paiement ORDER BY promo DESC";
include("opendb.php");
$res = mysql_query($query);
if (!$res)
	echo mysql_error();
else
{
	print "<p>Promo:<SELECT id=\"promo\" >";
	while ($array_promo = mysql_fetch_array($res))
		print "<OPTION value=\"".$array_promo['promo']."\" ".(isset($_GET['promo']) && $_GET['promo']==$array_promo['promo'] ? "selected" : "")." >".$array_promo['promo']."</OPTION>";
	print "</SELECT></p>";
}
/*if($_SESSION['privilege'] == 1)
{
	print "<br/>";
	$res = doQuery("SELECT id, nom FROM {$GLOBALS['prefix_db']}association");
	print 	"Choix de l'association : <select id='choix_association' name='choix_association'>";
	if ($current_asso != 0)
		print	"<option value='0'>Toutes</option>'";
	else
		print	"<option selected value='0'>Toutes</option>'";
	while ($tmp_array = mysql_fetch_array($res))
	{
		if ($current_asso == $tmp_array['id'])
			print "<option selected value='".$tmp_array['id']."'>".$tmp_array['nom']."</option>'";
		else
			print "<option value='".$tmp_array['id']."'>".$tmp_array['nom']."</option>'";
	}
	print		"</select>";
	print "<br/>";
	print "<br/>";
}*/


$query = "SELECT DISTINCT type FROM {$GLOBALS['prefix_db']}sup WHERE promo='".$promo."' ORDER BY type ASC";
include('opendb.php');
$res = mysql_query($query);
if (!$res)
{
	echo mysql_error();
	die();
}
else
{
	print "<div style='float:left;'><table class='tab_grille'><tr><th></th>";
	$i = 0;
	while ($tmp_array = mysql_fetch_array($res))
	{
		print "<th>".$tmp_array['type']."</th>";
		$list_type[$tmp_array['type']] = 0;
		$i++;
	}
	print "<th class='tab_footer_colonne'><b>TOTAL</b></th>";
	print "</tr>";
	$option = "";
	if ($current_asso != 0)
		$option = "WHERE id='".$current_asso."'";
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}association " . $option." ORDER BY nom ASC");
	while ($tmp_array = mysql_fetch_array($res))
		$list_asso[$tmp_array['id']] = $tmp_array;
	foreach ($tab_asso as $asso)
	{
		print "<tr><td align='center'>|</td>";
		foreach ($list_type as $key => $value)
			print "<td></td>";
		print "<td></td></tr>";
		print "<tr align='center'>";
		print "<td><b>".$asso['nom']."</b></td>";
		if ($_SESSION['privilege'] == 1 || (isset($tot_asso) && $tot_asso > 0))
		{
			$count = 0;
			$total_line = 0;
			$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}sup WHERE promo='".$promo."' AND id IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent='".$asso['id']."')");
			$list_sup = null;
			while ($tmp_array = mysql_fetch_array($res))
				$list_sup[$tmp_array['type']][$tmp_array['id']] = 0;
			$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}paiement_sup WHERE id_sup IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent='".$asso['id']."')");
			$list_paie = null;
			while ($tmp_array = mysql_fetch_array($res))
				$list_paie[$tmp_array['id_sup']] += $tmp_array['valeur'];
			foreach ($list_type as $key => $value)
			{
				if (isset($list_sup[$key]))
				{
					$count_total = 0;
					foreach ($list_sup[$key] as $id => $number)
						$count_total += $list_paie[$id];
					print "<td>";
					if ($count_total > 0)
						print "<FONT COLOR='#16B84E'>";
					print $count_total."€";
					if ($count_total > 0)
						print "</FONT>";
					print "</td>";
					$total_line += $count_total;
					$list_type[$key] += $count_total;
				}
				else
					print "<td >0€</td>";
			}
			print "<td class='tab_footer_colonne'>".$total_line."€</td>";
		}
		else
		{
			foreach ($list_type as $key => $value)
				print "<td ></td>";
			print "<td class='tab_footer_colonne'></td>";
		}
		print "</tr>";
		
		$sections = null;
		if (isset($tot_asso) && $tot_asso > 0 || $_SESSION['privilege'] == 1)
			$sections = getSectionsByAsso($asso['id']);
		else
		{
			$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}section WHERE id IN (SELECT id_sec FROM {$GLOBALS['prefix_db']}asso_section WHERE id_asso=".$asso['id']." AND id_sec IN (SELECT id_sec FROM {$GLOBALS['prefix_db']}resp_section WHERE id_adh=".$_SESSION['uid']."))");
			while ($tmp_array_sec = mysql_fetch_array($res))
				$sections[$tmp_array_sec['id']] = $tmp_array_sec;
		}
		$list_id_ent = array();
		$list_id = array();
		foreach ($sections as $key => $value)
		{
			$list_id[$key] = $key;
			$list_id_ent[$key] = getActivitesBySection($key);
			foreach ($list_id_ent[$key] as $key_act => $value_act)
			{
				$list_id_ent[$key][$key_act] = getCreneauxByActivite($key_act);
				$list_id[$key] .= ', '.$key_act.'';
				foreach ($list_id_ent[$key][$key_act] as $key_cre => $value_cre)
					$list_id[$key] .= ', '.$key_cre.'';
			}
		}
		foreach ($sections as $sec)
		{
			print "<tr align='center'>";
			print "<td>".$sec['nom']."</td>";
			$count = 0;
			$total_line = 0;
			$list_id_sec = $sec['id'];
			$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}sup WHERE promo='".$promo."' AND id IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent IN (".$list_id[$sec['id']].")) AND id_asso_paie=".$asso['id']." ");
			$list_sup = null;
			while ($tmp_array = mysql_fetch_array($res))
				$list_sup[$tmp_array['type']][$tmp_array['id']] = 0;
			$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}paiement_sup WHERE id_sup IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent IN (".$list_id[$sec['id']]."))");
			$list_paie = null;
			while ($tmp_array = mysql_fetch_array($res))
				$list_paie[$tmp_array['id_sup']] += $tmp_array['valeur'];
			foreach ($list_type as $key => $value)
			{
				if (isset($list_sup[$key]))
				{
					$count_total = 0;
					foreach ($list_sup[$key] as $id => $number)
						$count_total += $list_paie[$id];
					print "<td>";
					if ($count_total > 0)
						print "<FONT COLOR='#16B84E'>";
					print $count_total."€";
					if ($count_total > 0)
						print "</FONT>";
					print "</td>";
					$total_line += $count_total;
					$list_type[$key] += $count_total;
				}
				else
					print "<td >0€</td>";
			}
			print "<td class='tab_footer_colonne'>".$total_line."€</td>";
			print "</tr>";
		}
		print "</tr>";
		print "<tr align='center' class='tab_footer_line'><td><b>Sous-Total</b></td>";
		$final_total = 0;
		$total_line = 0;
		foreach ($list_type as $key => $value)
		{
			print "<td>".$value."€</td>";
			$final_total += $value;
			$total_line += $value;
			$list_type[$key] = 0;
		}
		print "<td class='tab_footer_colonne'>".$total_line."€</td></tr>";
		
	}
	print "</table></div>";
}
$final_total = 0;
$total_line = 0;
$list_type = null;
$total_line = 0;
$count_total = 0;
$list_sup = null;
$list_paie = null;

print "<table class='tab_grille'><tr><th></th>";
$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}type_dep ORDER BY nom ASC");
$i = 0;
while ($tmp_array = mysql_fetch_array($res))
{
	print "<th>".$tmp_array['nom']."</th>";
	$list_type[$tmp_array['nom']] = 0;
}
print "<th class='tab_footer_colonne'><b>TOTAL</b></th>";
print "</tr>";
$option = "";
if (isset($_GET['asso']) && $_GET['asso'] != 0)
	$option = "WHERE id='".$_GET['asso']."'";
$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}association " . $option." ORDER BY nom ASC");
while ($tmp_array = mysql_fetch_array($res))
	$list_asso[$tmp_array['id']] = $tmp_array;
foreach ($tab_asso as $asso)
{
	print "<tr><td align='center'>|</td>";
	foreach ($list_type as $key => $value)
		print "<td></td>";
	print "<td></td></tr>";
	print "<tr align='center'>";
	print "<td><b>".$asso['nom']."</b></td>";
	$count = 0;
	$total_line = 0;
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}finances WHERE emetteur IN(SELECT id_adh FROM {$GLOBALS['prefix_db']}resp_asso WHERE id_asso='".$asso['id']."')");
	$list_dep = null;
	while ($tmp_array = mysql_fetch_array($res))
		$list_dep[$tmp_array['type']] += $tmp_array['montant'];
	foreach ($list_type as $key => $value)
	{
		if (isset($list_dep[$key]))
		{
			print "<td>";
			if ($count_total > 0)
				print "<FONT COLOR='#16B84E'>";
			print $count_total."€";
			if ($count_total > 0)
				print "</FONT>";
			print "</td>";
			$total_line += $count_total;
			$list_type[$key] += $count_total;
		}
		else
			print "<td >0€</td>";
	}
	print "<td class='tab_footer_colonne'>".$total_line."€</td>";
	print "</tr>";
	$sections = null;
	if (isset($tot_asso) && $tot_asso > 0 || $_SESSION['privilege'] == 1)
		$sections = getSectionsByAsso($asso['id']);
	else
	{
		$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}section WHERE id IN (SELECT id_sec FROM {$GLOBALS['prefix_db']}asso_section WHERE id_asso=".$asso['id']." AND id_sec IN (SELECT id_sec FROM {$GLOBALS['prefix_db']}resp_section WHERE id_adh=".$_SESSION['uid']."))");
		while ($tmp_array_sec = mysql_fetch_array($res))
			$sections[$tmp_array_sec['id']] = $tmp_array_sec;
	}
	$list_id_ent = array();
	$list_id = array();
	foreach ($sections as $key => $value)
	{
		$list_id[$key] = $key;
		$list_id_ent[$key] = getActivitesBySection($key);
		foreach ($list_id_ent[$key] as $key_act => $value_act)
		{
			$list_id_ent[$key][$key_act] = getCreneauxByActivite($key_act);
			$list_id[$key] .= ', '.$key_act.'';
			foreach ($list_id_ent[$key][$key_act] as $key_cre => $value_cre)
				$list_id[$key] .= ', '.$key_cre.'';
		}
	}
	foreach ($sections as $sec)
	{
		print "<tr align='center'>";
		print "<td>".$sec['nom']."</td>";
		$count = 0;
		$total_line = 0;
		$list_id_sec = $sec['id'];
		$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}sup WHERE promo='".$promo."' AND id IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent IN (".$list_id[$sec['id']].")) AND (id_asso_adh=".$asso['id']." OR id_asso_adh=NULL)");
		$list_sup = null;
		while ($tmp_array = mysql_fetch_array($res))
			$list_sup[$tmp_array['type']][$tmp_array['id']] = 0;
		$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}paiement_sup WHERE id_sup IN (SELECT id_sup FROM {$GLOBALS['prefix_db']}sup_fk WHERE id_ent IN (".$list_id[$sec['id']]."))");
		$list_paie = null;
		while ($tmp_array = mysql_fetch_array($res))
			$list_paie[$tmp_array['id_sup']] += $tmp_array['valeur'];
			
		foreach ($list_type as $key => $value)
		{
			if (isset($list_sup[$key]))
			{
				$count_total = 0;
				foreach ($list_sup[$key] as $id => $number)
					$count_total += $list_paie[$id];
				print "<td>";
				if ($count_total > 0)
					print "<FONT COLOR='#16B84E'>";
				print $count_total."€";
				if ($count_total > 0)
					print "</FONT>";
				print "</td>";
				$total_line += $count_total;
				$list_type[$key] += $count_total;
			}
			else
				print "<td >0€</td>";
		}
		print "<td class='tab_footer_colonne'>".$total_line."€</td>";
		print "</tr>";
	}
	print "</tr>";
	print "<tr align='center' class='tab_footer_line'><td><b>Sous-Total</b></td>";
	$final_total = 0;
	$total_line = 0;
	foreach ($list_type as $key => $value)
	{
		print "<td>".$value."€</td>";
		$total_line += $value;
	}
	print "<td class='tab_footer_colonne'>".$total_line."€</td></tr>";
}
print "</table>";
?>
<script type="text/javascript">
$('#promo').change( function (){
        window.location.search = "page=14&adh="+$.getUrlVar('adh')+"&promo="+$(this).val();
});
</script>
<script type="text/javascript">

$('#choix_association').change( function (){
        window.location.search = "page=14&asso="+$(this).val();
});
</script>