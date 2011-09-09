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
		print $die_footer;
		die();
	}

}
if(isset($_GET['promo'])) {
	$promo=$_GET['promo'];
} else {
	$promo=$current_promo;
}



$adh = getAdherent($id_adh);
$creneaux=getAllCreneaux();
$id_statut_adh=$adh['statut'];
if ($_POST['action'] == 'nouvelle' && $self) {
	print '<h2>Choisissez vos activités</h2>';
	print '<FORM action="index.php?page=7" method="POST">
	<input type="hidden" name="action" value="select_assos" />';
	print '<ul id="tree_root">';

	$tab=array();
	foreach($creneaux as $creneau){
		$tab[$creneau[nom_act]][nom]=$creneau[nom_act];
		$tab[$creneau[nom_act]][id]=$creneau[id_act];
		$tab[$creneau[nom_act]][nom_sec]=$creneau[nom_sec];
		$tab[$creneau[nom_act]][creneaux][$creneau[id_cre]][jour]=$creneau[jour_cre];
		$tab[$creneau[nom_act]][creneaux][$creneau[id_cre]][id]=$creneau[id_cre];
		$tab[$creneau[nom_act]][creneaux][$creneau[id_cre]][debut]=$creneau[debut_cre];
		$tab[$creneau[nom_act]][creneaux][$creneau[id_cre]][fin]=$creneau[fin_cre];
	}
	$ads=getAdhesions($id_adh,$promo);

	foreach($tab as $act){
		$out= '<li><input type="checkbox" name="act'.$act[id].'"  value="'.$act[id].'"><label>'.$act[nom_sec].' - '.$act[nom].'</label>';
		$out.= '<ul id="creneaux">';
		$i=0;
		foreach($act[creneaux] as $cre){
			
			if (!isset($ads['cre'.$cre['id']]) ){
				$out.= '<li><input type="checkbox" name="cre[]"  value="'.$cre[id].'"><label>'.$cre[jour].' - '.substr($cre[debut],0,-3).' - '.substr($cre[fin],0,-3).'</label>';
				$i++;
			}
		}
		$out.= '</ul>';
		if ($i>0) print $out;

	}
	print '</ul>';
	print '<INPUT type="submit" value="Suite"></FORM>';
} else
if ($_POST['action'] == 'select_assos' && $self && !empty($_POST['cre']) ) {
	print "<span class=\"tip\">".getParam('text_select_asso')."</span>";
	print '<FORM action="index.php?page=7" method="POST">';
	if (!isset($_POST['update'])) {
		print '<input type="hidden" name="action" value="submitted" />';
	}
	else {
		print  '<input type="hidden" name="id_ads" value="'.$_POST['id_ads'].'" />
		<input type="hidden" name="action" value="submitted_update" />';
	}
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
		else{ 
			
			print "<LABEL FOR=\"asso_cre_$cre\">Impossible</LABEL>
			<input type=\"radio\" checked value=\"\" name=\"asso_cre[$cre]\" cre=\"$cre\" class=\"radio_cre\">";
		}
		print '</tr>';
	}
	print "<tr><td>Total</td><td id=\"total\">0</td></tr>";
	print "<span style=\"display:none;\" id=\"id_statut_adh\">$id_statut_adh</span>";
	print '</TABLE>
	<INPUT type="submit" value="Valider"><INPUT type="reset" class="reset" value="Remettre à zéro" ></FORM>';
}
 else{
	if ($_POST['action'] == 'submitted' && $self){

		if(!empty($_POST['asso_cre']) ) newAdhesions($_POST['asso_cre'],$id_adh);

	}
	if ($_POST['action'] == 'submitted_update' && $self){

		if(!empty($_POST['asso_cre']) ) updateAdhesions($_POST['asso_cre'],$_POST['id_ads']);

	}
	if ($_POST['action'] === 'suppression_ads'){
		delAdhesion($_POST['id_ads']);
	}
	if ($_POST['action'] === 'activation_ads'){
		actAdhesion($_POST['id_ads']);
	}
	if ($_POST['action'] === 'suppression_paie'){
		delPaiement($_POST['id_paie']);
	}
	if ($_POST['action']==='nouveau_paiement') {
		//print_r_html($_POST);
		if(empty($_POST['sup']) || empty($_POST['type']) || empty($_POST['promo']) || empty($_POST['num']) ){
			print "<pre>Il y a une erreur dans le paiement</pre>";
		} else {
			addPaiement($_POST);
		}

	}
	if($_POST['action']==='setnumcarte'){
		setNumCarte($_POST['numcarte'],$id_adh);
	}
	if(!(strcmp($_SESSION['user'],"") == 0)){
		print '<ul id="submenu"><li><a href="index.php?page=1&adh='.$id_adh.'">Fiche Adhérent</a></li><li><a class="selected" href="index.php?page=7&adh='.$id_adh.'">Adhésions</a></li></ul>';
		//Selection Promo
		print "<p>Promo:<SELECT id=\"promo\" >";
		print "<OPTION value=$current_promo ".($_GET['promo']==$current_promo ? "selected" : "")." >$current_promo</OPTION>";
		for ($i=1; $i<=10; $i++ ){
			$p=$current_promo-$i;
			print "<OPTION value=\"$p\" ".($_GET['promo']==$p ? "selected" : "")." >$p</OPTION>";
		}
		print "</SELECT></p>";
		//Selection asso
		if(count($assos_resp) > 1 ){
			print "<p>Consulter en tant que responsable de: ";
			foreach($assos_resp as $key => $asso) print "<a href=\"index.php?page=7&adh=$id_adh&asso=$key\">$asso</a> ";
			print "</p>";
		}
		//Adhésions
		if ($self){
			$ads=getAdhesions($id_adh,$promo);
		} else {
			$ads=getMyAdhesions($id_adh,$promo);
		}
		$crens=getAllCreneaux();
		$mycrens=getCreneaux($_SESSION['uid']);
		$assos=getAllAssociations();
		$assos_cre=getAssosCreneaux();
		print "<div class=\"tip\">".getParam('text_adhesion')."</div>";
		print "<h2>Adhésions de {$adh['prenom']} {$adh['nom']}</h2>";
		print '<TABLE>';
		print '<th>Date</th><th>Activité</th><th>Jour</th><th>Heure</th><th>Statut</th><th>Année</th><th>Association</th>';
		if ($self || $resp_asso) print "<th>Résilier</th>";
		foreach($ads as $key => $value) if(is_numeric($key) && ($self || $value['id_asso']==$current_asso || isset($mycrens[$value['id_cre']]))){
			print '<tr>';
			print "<td>{$value['date']}</td>";
			print "<td>{$crens[$value['id_cre']]['nom_sec']} - {$crens[$value['id_cre']]['nom_act']}</td>";
			print "<td>{$crens[$value['id_cre']]['jour_cre']}</td>";
			print "<td>{$crens[$value['id_cre']]['debut_cre']} - {$crens[$value['id_cre']]['fin_cre']}</td>";
			print "<td>";
			
			switch($value['statut']) {
				case 0: 
				print "Active";
				print "</td>";
				print "<td>{$value['promo']}</td>";
				print "<td>{$assos[$value['id_asso']]['nom']}</td>";
				break;
				case 1:
				print "Résiliée";
				print "</td>";
				print "<td>{$value['promo']}</td>";
				print "<td>{$assos[$value['id_asso']]['nom']}</td>";
				break;
				case 2:
			
					if(isset($assos_cre[$id_statut_adh][$value['id_cre']])) {
						print "Possible";
						print "</td>";
						print "<td>{$value['promo']}</td>";
						print "<td>";
						print '<FORM action="index.php?page=7&adh='.$id_adh.'&asso='.$current_asso.'" method="POST">';
						print '<input type="hidden" name="action" value="select_assos" />';
						print '<input type="hidden" name="update" value="true" />';
						print '<input type="hidden" name="id_ads" value="'.$key.'" />';
						print '<input type="hidden" name="cre[]" value="'.$value['id_cre'].'" />';
						print '<input type="submit" value="Choisir asso" >';
						print '</FORM>';
						print "</td>";
					} else {
						print "Impossible";
						print "</td>";
						print "<td>{$value['promo']}</td>";
						print "<td>";
						print "</td>";
					}
				break;
			}
		
			if ($self){
				print "<td><a href=\"".getParam("url_resiliation")."\" target=\"_blank\" ><img src=\"images/unchecked.gif\" ></a></td>";
			} else
			if ($resp_asso) {
				print '<td>
				<FORM action="index.php?page=7&adh='.$id_adh.'&asso='.$current_asso.'" method="POST">
				<input type="hidden" name="id_ads" value='.$key.' />
				';
				switch($value['statut']) {
					case 0: 
					print '<input type="hidden" name="action" value="suppression_ads" />
					<INPUT type="image" src="images/unchecked.gif" value="submit">';
					break;
					case 1:
					print '<input type="hidden" name="action" value="activation_ads" />
					<INPUT type="image" src="images/checked.gif" value="submit">';
					break;
					case 2:
					print "";
					break;
				}
				
				print '</FORM>
				</td>
				';
			}
			print "</tr>";
			
		}
		print '</TABLE>';
		if (($self || $resp_asso) && $promo == $current_promo) print '<FORM action="index.php?page=7" method="POST">
		<input type="hidden" name="action" value="nouvelle" />
		<INPUT type="submit" value="Nouvelle">
		</FORM>';

		//Facture
		print '<h2>Facture</h2>
				<FORM action="index.php?page=7&adh='.$id_adh.'&asso='.$current_asso.'" method="POST">
				<input type="hidden" name="action" value="nouveau_paiement" />
				<table>';
		print "<th>Entité du supplément</th><th>Type du supplément</th><th>Valeur</th><th>Payé</th><th>Reste à payer</th><th>Payer à</th>";
		if ($resp_asso) {
			print "<th>Nouveau Paiement</th>";
		}
		$tab = getFacture($ads,$adh['statut'],$promo);
		$p_sup = getPaiementsSup($id_adh);
		foreach($tab['assos'] as $row){

				print "<tr><td>Association {$assos[$row['id_asso_paie']]['nom']}</td><td>{$row['type']}</td><td>{$row['valeur']}</td>
				<td>".(isset($p_sup[$row['id']]) ? $p_sup[$row['id']] : 0)."</td><td>".($row['valeur']-$p_sup[$row['id']])."</td>
				<td>{$assos[$row['id_asso_paie']]['nom']}</td>";
				if ($resp_asso && $row['id_asso_paie']==$current_asso) {
					$paiement_possible=true;
					print "<td><INPUT name=\"sup[{$row['id']}]\" value=\"{$_POST['sup'][$row['id']]}\" class=\"tot\" type=\"text\" /></td>";
				} else if ($resp_asso) print "<td></td>";
				print "</tr>";


		}
		foreach($tab['secs'] as $row){

				print "<tr><td>Section {$row['nom_sec']}</td><td>{$row['type']}</td><td >{$row['valeur']}</td>
				<td>".(isset($p_sup[$row['id']]) ? $p_sup[$row['id']] : 0)."</td><td>".($row['valeur']-$p_sup[$row['id']])."</td>
				<td>{$assos[$row['id_asso_paie']]['nom']}</td>";
				if ($resp_asso && $row['id_asso_paie']==$current_asso) {
					$paiement_possible=true;
					print "<td><INPUT name=\"sup[{$row['id']}]\" value=\"{$_POST['sup'][$row['id']]}\" class=\"tot\" type=\"text\" /></td>";
				} else if ($resp_asso) print "<td></td>";
				print "</tr>";

		}
		foreach($tab['acts'] as $row){

				print "<tr><td>Activité {$row['nom_act']}</td><td>{$row['type']}</td><td >{$row['valeur']}</td>
				<td>".(isset($p_sup[$row['id']]) ? $p_sup[$row['id']] : 0)."</td><td>".($row['valeur']-$p_sup[$row['id']])."</td>
				<td>{$assos[$row['id_asso_paie']]['nom']}</td>";
				if ($resp_asso && $row['id_asso_paie']==$current_asso) {
					$paiement_possible=true;
					print "<td><INPUT name=\"sup[{$row['id']}]\" value=\"{$_POST['sup'][$row['id']]}\" class=\"tot\" type=\"text\" /></td>";
				} else if ($resp_asso) print "<td></td>";
				print "</tr>";

		}
		foreach($tab['cres'] as $row){

				print "<tr><td>Créneau {$row['nom_act']} - {$row['jour_cre']} - {$row['debut_cre']}</td><td>{$row['type']}</td><td >{$row['valeur']}</td>
				<td>".(isset($p_sup[$row['id']]) ? $p_sup[$row['id']] : 0)."</td><td>".($row['valeur']-$p_sup[$row['id']])."</td>
				<td>{$assos[$row['id_asso_paie']]['nom']}</td>";
				if ($resp_asso && $row['id_asso_paie']==$current_asso) {
					$paiement_possible=true;
					print "<td><INPUT name=\"sup[{$row['id']}]\" class=\"tot\" type=\"text\" /></td>";
				} else if ($resp_asso) print "<td></td>";
				print "</tr>";

		}
		if ($resp_asso && $paiement_possible){
			print "<tr><td></td><td></td><td></td><td></td><td></td><td>Total :</td><td><INPUT type=\"text\" id=\"total\" disabled /></td></tr>";
			print "<tr><td></td><td></td><td></td><td></td><td></td><td>Type :</td><td><INPUT name=\"type\" type=\"text\" /></td></tr>";
			print "<tr><td></td><td></td><td></td><td></td><td></td><td>Numéro :</td><td><INPUT name=\"num\" type=\"text\" /></td></tr>";
			print "<tr><td></td><td></td><td></td><td></td><td></td><td>Date de la transaction :</td><td><INPUT name=\"date_t\" class=\"datepicker\" readonly type=\"text\" /></td></tr>";
			print "<tr><td></td><td></td><td></td><td></td><td></td><td>Remarque :</td><td><INPUT name=\"remarque\" type=\"text\" /></td></tr>";
			print "<tr><td></td><td></td><td></td><td></td><td></td><td>Promo :</td><td><INPUT type=\"text\" readonly name=\"promo\" value=\"{$promo}\" /></td></tr>";
			print "<tr><td></td><td></td><td></td><td></td><td></td><td>Envoyer :</td><td><INPUT type=\"submit\" /></td></tr>";
			print "<INPUT type=\"hidden\" name=\"id_adh\" value=$id_adh />
			<INPUT type=\"hidden\" name=\"recorded_by\" value=\"{$_SESSION['nom']} {$_SESSION['prenom']}\" />";
			
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
		//Paiements
		print "<h2>Paiements</h2>";
		$paiements=getMyPaiements($id_adh);
		//print_r_html($paiements);
		print "<table><th>Type</th><th>Numéro</th><th>Date Transaction</th><th>Remarque</th><th>Total</th><th>Promo</th><th>Enregistré par</th><th>Date Enregistrement</th><th>Details</th>";
		if($resp_asso) print "<th>Supprimer</th>";
		foreach ($paiements as $id => $row ) {
			if($row['promo']!=$promo) continue;
			$tot=0;
			foreach($row['ps'] as $row2) $tot+=$row2['valeur_paiement'];
			print "<tr>";
			print "<td>{$row['type']}</td><td>{$row['num']}</td><td>{$row['date_t']}</td><td>{$row['remarque']}</td><td>$tot</td><td>{$row['promo']}</td><td>{$row['recorded_by']}</td><td>{$row['date']}</td><td><img src=\"images/downarrow.gif\" class=\"toggle\" /></td>";
			if ($resp_asso){
				print '<td>';
				print '<FORM action="index.php?page=7&adh='.$id_adh.'&asso='.$current_asso.'" method="POST">
				<input type="hidden" name="action" value="suppression_paie" />
				<input type="hidden" name="id_paie" value='.$id.' />
				<INPUT type="image" src="images/unchecked.gif" value="submit">
				</form>
				';
				print '</td>';
			}
			print "</tr>";
			print "<tr style=\"display : none; \"><td></td><td></td><td>Suppléments:</td><td colspan=6><table><th>Type</th><th>A payer</th><th>Payé</th><th>Payer à</th>";
			foreach($row['ps'] as $row2){
				print "<tr><td>{$row2['type_sup']}</td><td>{$row2['valeur_sup']}</td><td>{$row2['valeur_paiement']}</td><td>{$assos[$row2['id_asso_paie']]['nom']}</td></tr>";
			}
			print "</table></td>".($resp_asso ? "<td></td>" : "")."</tr>";
		}
		print "</table>";
		$adh = getAdherent($id_adh);
		//Numéro de carte
		if ($resp_asso && !$self) print "<h2>Changer le numéro de carte</h2><FORM id=\"f_numcarte\" action=\"index.php?page=7&adh=$id_adh&asso=$current_asso\" method=\"POST\"  >
		 		<input type=\"hidden\" name=\"action\" value=\"setnumcarte\" /> 
				<dt>Numéro actuel:<input type=\"text\" value=\"{$adh['numcarte']}\" disabled />
				<dt>Nouveau Numéro:<input type=\"text\" name=\"numcarte\" id=\"numcarte\"  class=\"numcarte\" value=\"".getMaxNumCarte()."\" >
				<input type=\"submit\" >
				</FORM>
				"; 
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
$(".tot").change(function(){
	total = 0.0;
	$(".tot").each(function(){
		if(!isNaN(parseFloat($(this).val()))){
			total= total + parseFloat($(this).val());
		}
	});
	$("#total").val(total);
});
$(".toggle").click(function () {
      $(this).parent().parent().next().toggle();
      //alert($(this).parent().parent().next().html());
});
$('#promo').change( function (){
	window.location.search = "page=7&adh="+$.getUrlVar('adh')+"&promo="+$(this).val();
});
$(function() {
	$( ".datepicker" ).datepicker({ 
		changeYear: true , yearRange: "-100:+0" , changeMonth: true , dateFormat: "yy-mm-dd"  
	});
});
$(document).ready(function() {
		  	$.extend($.validator.messages, {
		        required: "Ce champs est requis",
		        number: "Veuillez entrer un numéro correct"

    		});

			$("#f_numcarte").validate({

			rules : {
				numcarte: {
	                required: true,
					number: true,
	                remote: "includes/numcarte.php"
            	}

			},
			messages: {
				numcarte: {
					required: "Ce champs est requis",
					number: "Veuillez entrer un numéro correct",
					remote: "Le numéro est déjà utilisé"
					}
			},
			errorPlacement: function(error, element) {
	            if ( element.is(":radio") )
	                error.appendTo( element.parent() );
	          	else
                	error.appendTo( element.parent() );
        	},
        	success: function(label) {
            	// set   as text for IE
            	label.html(" ").addClass("checked");
	        }

			});
});
</script>