<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
session_start();
if (!isset($_GET['adh'])) {
	$id_adh =$_SESSION['uid'];
	$edit=true;
}
else {
	$tab = getMyAdherents($_SESSION['uid']);
	if (isset($tab[$_GET['adh']])) $id_adh=$_GET['adh'];
	else { 
		print 'Vous n\'avez pas accès à cette page';
		die();
	}
	$resp=true;
}
$adh = getAdherent($id_adh);
$creneaux=getAllCreneaux();
if ($_POST['action'] == 'nouvelle' && $edit) {
	print '<h2>Choisissez vos activités</h2>';
	print '<FORM action="index.php?page=7" method="POST">
	<input type="hidden" name="action" value="select_assos" />';
	print '<ul id="tree_root">';

	$tab=array();
	foreach($creneaux as $creneau){
		$tab[$creneau[nom_act]][nom]=$creneau[nom_act];
		$tab[$creneau[nom_act]][id]=$creneau[id_act];
		$tab[$creneau[nom_act]][creneaux][$creneau[id_cre]][jour]=$creneau[jour_cre];
		$tab[$creneau[nom_act]][creneaux][$creneau[id_cre]][id]=$creneau[id_cre];
		$tab[$creneau[nom_act]][creneaux][$creneau[id_cre]][debut]=$creneau[debut_cre];
		$tab[$creneau[nom_act]][creneaux][$creneau[id_cre]][fin]=$creneau[fin_cre];
	}


	foreach($tab as $act){
		print '<li><input type="checkbox" name="act'.$act[id].'"  value="'.$act[id].'"><label>'.$act[nom].'</label>';
		print '<ul id="creneaux">';
		foreach($act[creneaux] as $cre){
			print '<li><input type="checkbox" name="cre[]"  value="'.$cre[id].'"><label>'.$cre[jour].' - '.substr($cre[debut],0,-3).' - '.substr($cre[fin],0,-3).'</label>';
		}
		print '</ul>';

	}
	print '</ul>';
	print '<INPUT type="submit" value="Suite"></FORM>';
} else
if ($_POST['action'] == 'select_assos' && $edit) {
	print '<FORM action="index.php?page=7" method="POST">
	<input type="hidden" name="action" value="submitted" />';
	$id_statut_adh=$adh['statut'];
	print '<TABLE>';
	$assos_cre=getAssosCreneaux();
	foreach($_POST['cre'] as $cre){
		print '<tr>';
		print '<td>'.$creneaux[$cre]['nom_act'].' - '.$creneaux[$cre]['jour_cre'].' - '.$creneaux[$cre]['debut_cre'].'</td><td class="asso_cre">';
		if(isset($assos_cre[$id_statut_adh][$cre]))
		foreach($assos_cre[$id_statut_adh][$cre] as $id_asso => $nom_asso){

			print "<LABEL FOR=\"asso_cre_$cre\">$nom_asso</LABEL>
			<input type=\"radio\" value=\"$id_asso\" name=\"asso_cre_$cre\" cre=\"$cre\" class=\"radio_cre\">
			";
		}
		print '</tr>';
	}
	print "<tr><td>Total</td><td id=\"total\"></td></tr>";
	print "<span hidden id=id_statut_adh>$id_statut_adh</span>";
	print '</TABLE>
	<INPUT type="submit" value="Valider"><INPUT type="reset" value="Remettre à zéro" ></FORM>';
} else 
{
	if ($_POST['action'] == 'submitted' && $edit){

	}
	if(!(strcmp($_SESSION['user'],"") == 0)){
		print '<h2>Vos adhésions</h2>';
		$ads=getAdhesions($id_adh);
		$crens=getAllCreneaux();
		print '<TABLE>';
		print '<th>Date</th><th>Activité</th><th>Jour</th><th>Heure</th><th>Statut</th><th>Année</th>';
		foreach($ads as $key => $value){
			print '<tr>';
			print "<td>{$value['date']}</td>";
			print "<td>{$crens[$value['id_cre']]['nom_act']}</td>";
			print "<td>{$crens[$value['id_cre']]['jour_cre']}</td>";
			print "<td>{$crens[$value['id_cre']]['debut_cre']} - {$crens[$value['id_cre']]['fin_cre']}</td>";
			print "<td>{$value['statut']}</td>";
			print "<td>{$value['promo']}</td>";
			print '</tr>';
		}
		print '</TABLE>';
		print '<FORM action="index.php?page=7" method="POST">
		<input type="hidden" name="action" value="nouvelle" />
		<INPUT type="submit" value="Nouvelle">
		</FORM>
		</div>
		';
	}
	else {
		print "<p>Vous n'êtes pas connecté</p>";
	}
}



?>
<script type="text/javascript">
$('#tree_root').checkboxTree({
      /* specify here your options */
      onCheck: {
                ancestors: 'checkIfFull',
                descendants: 'check'
            },
            onUncheck: {
                ancestors: 'uncheck'
            }
    });

$(".radio_cre").click(function() {
		var params = {};
		$("input[type=radio]:checked.radio_cre").each(function(){
			params["cre_"+$(this,'input[type=radio]:checked').attr('cre')] = $(this,'input[type=radio]:checked').val();
		});
		params['id_statut_adh'] = $('#id_statut_adh').text();
		//alert($.param(params));
		$.getJSON("webservices/cout_adh.php",
				params,
				function(data) {
					$("#total").text(data['total']);
				}
		);
});
/*
function updateCout(){
	total=0;
	
	$.getJSON("controleur.php",
			{"action" : "calculer",
			"nombre_a" : $("input#nombre_a").val(),
			"nombre_b" : $("input#nombre_b").val() },
			function(data) {
				$("#total").val(data['total']);
			}
			);
			
	$(".asso_cre").each(function() {
		$(this).("input[type='radio']:checked").val();
    });
	$('#total').empty();
	$('#total').append(total);
};

function reset(){
	$(".total_cre").each($(this).empty());
	$('#total').empty();
};
*/
</script>