<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
session_start();
if (!isset($_GET['adh']) or $_GET['adh']==$_SESSION['uid']) {
	$id_adh =$_SESSION['uid'];
	$self=true;
} else if($_SESSION['privilege']==1){
	$admin=true;
	$id_adh=$_GET['adh'];
	$resp_asso=true;
	$assos_resp=getMyAssos(-1);
	if(!isset($_GET['asso'])) $current_asso=key($assos_resp);
	else $current_asso=$_GET['asso'];

}
else {
	if(count(getMyAssos($_SESSION['uid'])) > 0 ) {
		$resp_asso=true;
		$assos_resp=getMyAssos($_SESSION['uid']);
		if(!isset($_GET['asso'])) $current_asso=key($assos_resp);
		else $current_asso=$_GET['asso'];
	}
	$tab = getMyAdherents($_SESSION['uid']);

	if (isset($tab[$_GET['adh']])) $id_adh=$_GET['adh'];
	else {
		print 'Vous n\'avez pas accès à cette page';
		die();
	}

}
$adh = getAdherent($id_adh);
$creneaux=getAllCreneaux();
if ($_POST['action'] == 'nouvelle' && $self) {
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
	$ads=getAdhesions($id_adh);

	foreach($tab as $act){
		print '<li><input type="checkbox" name="act'.$act[id].'"  value="'.$act[id].'"><label>'.$act[nom].'</label>';
		print '<ul id="creneaux">';
		foreach($act[creneaux] as $cre){
			if (!isset($ads['cre'.$cre['id']]) ) print '<li><input type="checkbox" name="cre[]"  value="'.$cre[id].'"><label>'.$cre[jour].' - '.substr($cre[debut],0,-3).' - '.substr($cre[fin],0,-3).'</label>';
		}
		print '</ul>';

	}
	print '</ul>';
	print '<INPUT type="submit" value="Suite"></FORM>';
} else
if ($_POST['action'] == 'select_assos' && $self && !empty($_POST['cre']) ) {

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
			<input type=\"radio\" value=\"$id_asso\" name=\"asso_cre[$cre]\" cre=\"$cre\" class=\"radio_cre\">";
		}
		print '</tr>';
	}
	print "<tr><td>Total</td><td id=\"total\">0</td></tr>";
	print "<span hidden id=id_statut_adh>$id_statut_adh</span>";
	print '</TABLE>
	<INPUT type="submit" value="Valider"><INPUT type="reset" class="reset" value="Remettre à zéro" ></FORM>';
}
 else{
	if ($_POST['action'] == 'submitted' && $self){

		if(!empty($_POST['asso_cre']) ) newAdhesions($_POST['asso_cre'],$id_adh);

	}
	if ($_POST['action'] === 'suppression_ads'){
		delAdhesion($_POST['id_ads']);
	}
	if ($_POST['action']==='nouveau_paiement') {
		//print_r_html($_POST);
		if(empty($_POST['sup']) || empty($_POST['type']) || empty($_POST['promo']) || empty($_POST['num']) ){
			print "<pre>Il y a une erreur dans le paiement</pre>";
		} else {
			addPaiement($_POST);
		}

	}
	if(!(strcmp($_SESSION['user'],"") == 0)){
		print '<ul id="submenu"><li><a href="index.php?page=1&adh='.$id_adh.'">Fiche Adhérent</a></li><li><a class="selected" href="index.php?page=7&adh='.$id_adh.'">Adhésions</a></li></ul>';
		//Selection asso
		if(count($assos_resp) > 1 ){
			print "<p>Consulter en tant que responsable de: ";
			foreach($assos_resp as $key => $asso) print "<a href=\"index.php?page=7&adh=$id_adh&asso=$key\">$asso</a> ";
		}
		//Adhésions
		$ads=getAdhesions($id_adh);//GetMyAdhesions(id_adh)
		$crens=getAllCreneaux();
		$mycrens=getCreneaux($_SESSION['uid']);
		$assos=getAllAssociations();
		print "<h2>Adhésions de {$adh['prenom']} {$adh['nom']}</h2>";
		print '<TABLE>';
		print '<th>Date</th><th>Activité</th><th>Jour</th><th>Heure</th><th>Statut</th><th>Année</th><th>Association de rattachement</th><th>Supprimer</th>';
		foreach($ads as $key => $value) if(is_numeric($key) && ($self || $value['id_asso']==$current_asso || isset($mycrens[$value['id_cre']]))){
			print '<tr><FORM action="index.php?page=7&adh='.$id_adh.'&asso='.$current_asso.'" method="POST">
				<input type="hidden" name="action" value="suppression_ads" />
				<input type="hidden" name="id_ads" value='.$key.' />
				';
			print "<td>{$value['date']}</td>";
			print "<td>{$crens[$value['id_cre']]['nom_act']}</td>";
			print "<td>{$crens[$value['id_cre']]['jour_cre']}</td>";
			print "<td>{$crens[$value['id_cre']]['debut_cre']} - {$crens[$value['id_cre']]['fin_cre']}</td>";
			print "<td>{$value['statut']}</td>";
			print "<td>{$value['promo']}</td>";
			print "<td>{$assos[$value['id_asso']]['nom']}</td>";
			print '<td><INPUT type="image" src="images/unchecked.gif" value="submit"></td>
				</FORM></tr>';
		}
		print '</TABLE>';
		if ($self || $resp_asso) print '<FORM action="index.php?page=7" method="POST">
		<input type="hidden" name="action" value="nouvelle" />
		<INPUT type="submit" value="Nouvelle">
		</FORM>';

		//Facture
		print '<h2>Facture</h2>
				<FORM action="index.php?page=7&adh='.$id_adh.'&asso='.$current_asso.'" method="POST">
				<input type="hidden" name="action" value="nouveau_paiement" />
				<table>';
		print "<th>Entité du supplément</th><th>Type du supplément</th><th>Valeur</th><th>Payer à</th>";
		if ($resp_asso) {
			print "<th>Nouveau Paiement</th>";
		}
		$tab = getFacture($ads,$adh['statut']);
		foreach($tab['assos'] as $row){
			if (($self || $row['id_asso_paie']==$current_asso)) {
				print "<tr><td>Association {$assos[$row['id_asso_paie']]['nom']}</td><td>{$row['type']}</td><td>{$row['valeur']}</td><td>{$assos[$row['id_asso_paie']]['nom']}</td>";
				if ($resp_asso) print "<td><INPUT name=\"sup[{$row['id']}]\" value=\"{$_POST['sup'][$row['id']]}\" type=\"text\" /></td>";
				print "</tr>";
			}

		}
		foreach($tab['secs'] as $row){
			if (($self || $row['id_asso_paie']==$current_asso)) {
				print "<tr><td>Section {$row['nom_sec']}</td><td>{$row['type']}</td><td>{$row['valeur']}</td><td>{$assos[$row['id_asso_paie']]['nom']}</td>";
				if ($resp_asso) print "<td><INPUT name=\"sup[{$row['id']}]\" value=\"{$_POST['sup'][$row['id']]}\" type=\"text\" /></td>";
				print "</tr>";
			}
		}
		foreach($tab['acts'] as $row){
			if (($self || $row['id_asso_paie']==$current_asso)) {
				print "<tr><td>Activité {$row['nom_act']}</td><td>{$row['type']}</td><td>{$row['valeur']}</td><td>{$assos[$row['id_asso_paie']]['nom']}</td>";
				if ($resp_asso) print "<td><INPUT name=\"sup[{$row['id']}]\" value=\"{$_POST['sup'][$row['id']]}\" type=\"text\" /></td>";
				print "</tr>";
			}
		}
		foreach($tab['cres'] as $row){
			if (($self || $row['id_asso_paie']==$current_asso)) {
				print "<tr><td>Créneau {$row['nom_act']} - {$row['jour_cre']} - {$row['debut_cre']}</td><td>{$row['type']}</td><td>{$row['valeur']}</td><td>{$assos[$row['id_asso_paie']]['nom']}</td>";
				if ($resp_asso) print "<td><INPUT name=\"sup[{$row['id']}]\" value=\"{$_POST['sup'][$row['id']]}\" type=\"text\" /></td>";
				print "</tr>";
			}
		}
		if ($resp_asso){
			print "<tr><td></td><td></td><td></td><td>Type :</td><td><INPUT name=\"type\" value=\"{$_POST['type']}\"  type=\"text\" /></td></tr>";
			print "<tr><td></td><td></td><td></td><td>Numéro :</td><td><INPUT name=\"num\" value=\"{$_POST['num']}\" type=\"text\" /></td></tr>";
			print "<tr><td></td><td></td><td></td><td>Remarque :</td><td><INPUT name=\"remarque\" value=\"{$_POST['remarque']}\" type=\"text\" /></td></tr>";
			print "<tr><td></td><td></td><td></td><td>Promo :</td><td><INPUT name=\"promo\" value=\"{$_POST['promo']}\" type=\"text\" /></td></tr>";
			print "<tr><td></td><td></td><td></td><td>Envoyer :</td><td><INPUT type=\"submit\" /></td></tr>";
			print "<INPUT type=\"hidden\" name=\"id_adh\" value=$id_adh >";
		}

		print '</table></FORM>';
		if($self){
			print '<h2>Totaux</h2><p>Préparez des cheques aux ordres des associations respectives</p>';
			print '<table><th>A payer à</th><th>Total</th>';
			foreach($tab['totaux'] as $asso => $total){
				print "<tr><td>{$assos[$asso]['nom']}</td><td>$total</td></tr>";
			}
			print '</table>';
		}

		//Päiements
		print "<h2>Paiements</h2>";
		$paiements=getMyPaiements($id_adh);
		//print_r_html($paiements);
		print "<table><th>Date</th><th>Type</th><th>Numéro</th><th>Remarque</th><th>Total</th><th>Promo</th><th>Details</th>";
		foreach ($paiements as $id => $row ) {
			$tot=0;
			foreach($row['ps'] as $row2) $tot+=$row2['valeur_paiement'];
			print "<tr>";
			print "<td>{$row['date']}</td><td>{$row['type']}</td><td>{$row['num']}</td><td>{$row['remarque']}</td><td>$tot</td><td>{$row['promo']}</td><td><img src=\"images/downarrow.gif\" class=\"toggle\" /></td>";
			print "</tr>";


			print "<tr style=\"display : none; \"><td>Suppléments:</td><td colspan=6><table><th>Type</th><th>A payer</th><th>Payé</th><th>Payer à</th>";
			foreach($row['ps'] as $row2){
				print "<tr><td>{$row2['type_sup']}</td><td>{$row2['valeur_sup']}</td><td>{$row2['valeur_paiement']}</td><td>{$assos[$row2['id_asso_paie']]['nom']}</td></tr>";
			}
			print "</table></td></tr>";
		}
		print "</table>";

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
$('.reset').click(function() {
		$('#total').text("0");
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
$(".toggle").click(function () {
      $(this).parent().parent().next().toggle();
      //alert($(this).parent().parent().next().html());
    });

</script>