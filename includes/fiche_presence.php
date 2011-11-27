<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
session_start();
$tab=getCreneaux($_SESSION['uid']);
if(isset($_POST['promo'])) {
	$promo=$_POST['promo'];
} else {
	$promo=$current_promo;
}
if($_POST['action']==="addpresence"){
	modifPresence($_POST['id_adh'],$_POST['cre'],$_POST['week'],$promo,isset($_POST['present']));
}
print "<div class=\"tip\">".getParam('text_presence')."</div>";
$output = "<form class=\"toggle\" action=\"index.php?page=8\" method=\"POST\" >";
$output.= "<p>Promo :<SELECT id=\"promo\" name=\"promo\" >";
$output.= "<OPTION value=\"$current_promo\" ".($_POST['promo']==$current_promo ? "selected" : "")." >$current_promo</OPTION>";
for ($i=1; $i<=10; $i++ ){
	$p=$current_promo-$i;
	$output.= "<OPTION value=\"$p\" ".($_POST['promo']==$p ? "selected" : "")." >$p</OPTION>";
}
$output.= "</SELECT></p>";
foreach($tab as $creneau){
	$cre = $creneau['id_cre'];
	$output.= '<div><input '.($_POST['cre']==$cre ? "checked" : "").' type="radio" name="cre" value='.$cre.' ><h4 style="display:inline-block;">'.$creneau['nom_sec'].' - '.$creneau['nom_act'].' - '.$creneau['jour_cre'].' - '.$creneau['debut_cre'].' - '.$creneau['fin_cre'].'</h4></input></div>';
}
$output.= '<input type="submit" value="Ouvrir" /></form>';
if(isset($_POST['cre'])) {
	$cre = $_POST['cre'];
	$adhs = getAdherentsByCreneau($cre,$promo);
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
	$date=$w_debut;
	$output.= "<table><thead><tr><th>Jour<br>Mois</th>";
	while ($date < $w_fin ){
		$week=strftime("%V",$date);
		$p=strftime("%G",$date);
		$range = utf8_decode(strftime("%d<br>%m",strtotime("$p-W$week-$jour_num")));
		$output.= "<th>$range</th>";
		$date = strtotime("+1 week",$date);
	}
	$output.= "</tr></thead>";
	$date=$w_debut;
	$output.= "<tfoot><tr><td></td>";
	while ($date < $w_fin ){
		$output.= "<td></td>";
		$date = strtotime("+1 week",$date);
	}
	$output.= "</tr></tfoot>";
	foreach($adhs as $id_adh => $row){
		$output.= "<tr><th>{$row['prenom']} {$row['nom']}</th>";
		$date=$w_debut;
		while ($date < $w_fin){
			$week=strftime("%V",$date);
			$output.= "<td>
			<form class=\"auto\" action=\"index.php?page=8\" method=\"POST\">
			<input type=\"hidden\" name=\"action\" value=\"addpresence\">
			<input type=\"hidden\" name=\"id_adh\" value=\"$id_adh\">
			<input type=\"hidden\" name=\"cre\" value=\"$cre\">
			<input type=\"hidden\" name=\"week\" value=\"$week\">
			<input type=\"hidden\" name=\"promo\" value=\"$promo\">
			<input type=\"checkbox\" name=\"present\" ".((etaitPresent($id_adh,$cre,$week,$promo)) ? 'checked' : '')." />
			</form>
			</td>";
			$date = strtotime("+1 week",$date);
		}
		$output.= "</tr>";
	}
	$output.= "</tr></table>";
}
print $output;
?>
<script type="text/javascript">
$(".auto").change( function (){
	$(this).submit();
});
</script>
