<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
session_start();
$tab=getCreneaux($_SESSION['uid']);
if(isset($_GET['promo'])) {
	$promo=$_GET['promo'];
} else {
	$promo=$current_promo;
}
if($_POST['action']==="addpresence"){
	//print_r_html($_POST);
	modifPresence($_POST['id_adh'],$_POST['id_cre'],$_GET['week'],$promo,isset($_POST['present']));
}



$tab=getCreneaux($_SESSION['uid']);
foreach($tab as $creneau){
	print '<div><h4 style="display:inline-block;">'.$creneau['nom_act'].' - '.$creneau['jour_cre'].' - '.$creneau['debut_cre'].' - '.$creneau['fin_cre'].'</h4><img style="display:inline-block;" src="images/downarrow.gif" class="toggle" /></div>';
	$cre = $creneau['id_cre'];
	$adhs = getAdherentsByCreneau($cre,$promo);
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
	print "<table ".($_GET['cre']==$cre ? "" : "style=\"display:none;\"" )."><tr><th>Jour<br>Mois</th>";
	
	while ($date < $w_fin ){
		$week=strftime("%V",$date);
		$p=strftime("%G",$date);

		$range = utf8_decode(strftime("%d<br>%m",strtotime("$p-W$week-$jour_num")));
		print "<th>$range</th>";
		$date = strtotime("+1 week",$date);
	}
	print "</tr>";
	foreach($adhs as $id_adh => $row){
		print "<tr><th>{$row['prenom']} {$row['nom']}</th>";
		$date=$w_debut;
		while ($date < $w_fin){
			$week=strftime("%V",$date);
			print "<td>
			<form class=\"auto\" action=\"index.php?page=8&week={$week}&cre=$cre&promo=$promo\" method=\"POST\">
			<input type=\"hidden\" name=\"action\" value=\"addpresence\">
			<input type=\"hidden\" name=\"id_adh\" value=\"$id_adh\">
			<input type=\"hidden\" name=\"id_cre\" value=\"$cre\">
			<input type=\"hidden\" name=\"week\" value=\"{$week}\">
			
			<input type=\"checkbox\" name=\"present\" ".((etaitPresent($id_adh,$cre,$week,$promo)) ? 'checked' : '')." />
			</form>
			</td>";
			$date = strtotime("+1 week",$date);
		}
		print "</tr>";		
	}
	print "</tr></table>";
}
?>
<script type="text/javascript">
$('#semaine').change( function (){
	window.location.search = "page=8&week="+$(this).val();
});
$(".auto").change( function (){
	$(this).submit();
});
$(".toggle").click(function () {
	$(this).parent().next().toggle();
	//alert($(this).parent().parent().next().html());
});
</script>