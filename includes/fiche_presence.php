<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
/*session_start();*/
$tab=getCreneaux($_SESSION['uid']);
if(isset($_POST['promo'])) {
	$promo=$_POST['promo'];
} else {
	$promo=$current_promo;
}

if(isset($_POST['action']) && $_POST['action']==="addpresence")
{
	$compteur = 0;
	while (isset($_POST[$compteur]))
	{
		$tmp_cont = explode('--', $_POST[$compteur]);
		modifPresence($tmp_cont[0],$tmp_cont[1], $tmp_cont[2],$tmp_cont[3], isset($_POST[$_POST[$compteur]]));
		$compteur++;
	}
}

/*
if($_POST['action']==="addpresence")
{
	echo 'ID = '.$_POST['id_adh'].', Creneaux = '.$_POST['cre'].', Week = '.$_POST['week'].', isset = ';
	echo isset($_POST['present']);
	modifPresence($_POST['id_adh'],$_POST['cre'],$_POST['week'],$promo,isset($_POST['present']));
}
*/
$output = "<div class=\"tip\">".getParam('text_presence')."</div>";
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
	$pre_promo=$promo-1;
    $w_debut=strtotime("09/01/{$pre_promo}");
    $w_debut=strtotime("next Monday",$w_debut);
    $w_fin=strtotime("06/30/{$promo}");
    $date_now = strtotime("now");
    $date_now=strtotime("next Monday", $date_now);
    $date_end = strtotime("+1 week", $date_now);
    $date_now = strtotime("-7 week", $date_now);
    $date = $date_now;
    $output.= "<table><thead><tr><th></th><th>Jour<br>Mois</th><th><input type='button' value='<<'></th>";
    while ($date < $date_end){
    	$week=strftime("%U",$date);
    	$p=strftime("%Y",$date);
    	$range = utf8_decode(strftime("%d<br>%m",strtotime("$p-W$week-$jour_num")));
    	$output.= "<th>$range</th>";
    	$date = strtotime("+1 week",$date);
    }
    $output.= "<th><input type='button' value='>>'></th></tr></thead>";
    $date=$date_now;
    $output.= "<tfoot><tr><td></td><td></td><td></td>";
    while ($date < $date_end ){
    	$output.= "<td></td>";
    	$date = strtotime("+1 week",$date);
    }
    $output.= "<td></td></tr></tfoot>";
    $i = 0;
    
    $output.= "<form class=\"auto\" action=\"index.php?page=8\" method=\"POST\">";
    $output.= "<input type=\"hidden\" name=\"action\" value=\"addpresence\">";
    $output.= "<input type=\"hidden\" name=\"cre\" value=\"$cre\">
			   <input type=\"hidden\" name=\"promo\" value=\"$promo\">";
    $compteur_id = 0;
    foreach($adhs as $id_adh => $row)
     {
	    $i++;
	    $output.= "<tr><th>{$i}</th><th>{$row['prenom']}<br>{$row['nom']}</th><th></th>";
	    $date=$date_now;
	    while ($date < $date_end)
	    {
	    	$week=strftime("%U",$date);
	    	$output.= "<td ".($week==$current_week ? 'bgcolor=lightgreen' : '')." >";
		    $output.= "<input type=\"hidden\" name=\"$compteur_id\" value=\"$id_adh--$cre--$week--$promo\">
		    <input type=\"checkbox\" name=\"$id_adh--$cre--$week--$promo\" ".(etaitPresent($id_adh,$cre,$week,$promo) ? 'checked' : '')." />
		    </td>";
		    $date = strtotime("+1 week",$date);
		    $compteur_id++;
	    }
	    $output.= "<td></td></tr>";
    }
    $output.= '<tr><td colspan="11" align="right"><input  type="submit" value="Sauvegarder" /></td><td></td></tr>';
    $output.= "</form>";
    /*
 	foreach($adhs as $id_adh => $row)
	{
		$i++;
		$output.= "<tr><th>{$i}</th><th>{$row['prenom']}<br>{$row['nom']}</th>";
		$date=$date_now;
		while ($date < $date_end){
			$week=strftime("%U",$date);
			$output.= "<td ".($week==$current_week ? 'bgcolor=lightgreen' : '')." >
			<form class=\"auto\" action=\"index.php?page=8\" method=\"POST\">
			<input type=\"hidden\" name=\"action\" value=\"addpresence\">
			<input type=\"hidden\" name=\"id_adh\" value=\"$id_adh\">
			<input type=\"hidden\" name=\"cre\" value=\"$cre\">
			<input type=\"hidden\" name=\"week\" value=\"$week\">
			<input type=\"hidden\" name=\"promo\" value=\"$promo\">
			<input type=\"checkbox\" name=\"present\" ".(etaitPresent($id_adh,$cre,$week,$promo) ? 'checked' : '')." />
			</form>
			</td>";
			$date = strtotime("+1 week",$date);
		}
		$output.= "</tr>";
	}
	*/
	$output.= "</table>";
}
else
{
$output.= "<form class=\"toggle\" action=\"index.php?page=8\" method=\"POST\" >";
$output.= "<p>Promo :<SELECT id=\"promo\" name=\"promo\" >";
$output.= "<OPTION value=\"$current_promo\" ".($_POST['promo']==$current_promo ? "selected" : "")." >$current_promo</OPTION>";
for ($i=1; $i<=10; $i++ ){
	$p=$current_promo-$i;
	$output.= "<OPTION value=\"$p\" ".(isset($_POST['promo']) && $_POST['promo']==$p ? "selected" : "")." >$p</OPTION>";
	   }
$output.= "</SELECT></p>";
foreach($tab as $creneau){
	$cre = $creneau['id_cre'];
	$output.= '<div><input '.(isset($_POST['cre']) && $_POST['cre']==$cre ? "checked" : "").' type="radio" name="cre" value='.$cre.' ><h4 style="display:inline-block;">'.$creneau['nom_sec'].' - '.$creneau['nom_act'].' - '.$creneau['jour_cre'].' - '.$creneau['debut_cre'].' - '.$creneau['fin_cre'].'</h4></input></div>';
   }
$output.= '<input type="submit" value="Ouvrir" /></form>';
}
print $output;
?>
<script type="text/javascript">
$(".auto").change( function (){
	$(this).submit();
});
</script>
