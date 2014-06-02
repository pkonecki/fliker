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

if ($_SESSION['privilege'] == 1 || (isset($tot_asso) && $tot_asso > 0) || (isset($tot_sec) && $tot_sec > 0))
{

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
	
$currency = getParam('currency.conf');

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
	if((isset($tot_asso) && $tot_asso > 0))
		print '<li><a class="'.(($_GET['page']==18) ? 'selected' : '').'" href="index.php?page=18">Cotisations</a></li>';
	print '</ul>';
}

if (isset($_POST['demande_intervalle']))
{
	$date_debut_demande = strtotime("".$_POST['demande_debut_annee']."-".$_POST['demande_debut_mois']."-1");
	$date_fin_demande = strtotime("".$_POST['demande_fin_annee']."-".$_POST['demande_fin_mois']."-30");
}
else
{
	$promo_debut = $promo-1;
	$date_debut_demande = strtotime("".$promo_debut."-9-1");
	$promo_fin = $promo+1;
	$date_fin_demande = strtotime("".$promo_fin."-6-30");
}
if (isset($_POST['statut_demande']))
	$statut_demande = $_POST['statut_demande'];
else
	$statut_demande = "pending";
if (isset($_POST['etape_demande']))
	$etape_demande = $_POST['etape_demande'];
else
	$etape_demande = "all";
print "<br/>";

if (isset($_POST['new_demande']))
{
	$query = "INSERT INTO {$GLOBALS['prefix_db']}finances (type, section, fournisseur, montant, date_enregistrement, enregistreur, description, promo) VALUES('".$_POST['type_dep']."', '".$_POST['section']."', '".$_POST['fournisseur']."', '".$_POST['montant']."', CURRENT_TIMESTAMP()+0, ".$_SESSION['uid'].", '".$_POST['description']."', $current_promo)";
	$res = doQuery($query);
	if (!$res)
	{
		echo mysql_error();
		print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération, l'entrée n'a pas été ajouté</b></font>";
	}
	else
		print "<FONT COLOR='#16B84E'><b>Demande enregistré avec succès</b></font>";
	print "<br/><br/>";
}
if (isset($_POST['autorisation']) && $_POST['autorisation'] == "Refuser")
{
	$res = doQuery("UPDATE {$GLOBALS['prefix_db']}finances SET autorisation=2, authorized_by=".$_SESSION['uid'].", authorized_date=CURRENT_TIMESTAMP()+0 WHERE id=".$_POST['id_transa']." ");
	if (!$res)
	{
		echo mysql_error();
		print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération, le traitement n'a pas été fait</b></font>";
	}
	else
		print "<FONT COLOR='#16B84E'><b>Enregistré</b></font>";
	print "<br/><br/>";
}
if (isset($_POST['confirmation']) && $_POST['confirmation'] == "Refuser")
{
	$res = doQuery("UPDATE {$GLOBALS['prefix_db']}finances SET confirmation=2, confirmed_by=".$_SESSION['uid'].", confirmed_date=CURRENT_TIMESTAMP()+0 WHERE id=".$_POST['id_transa']." ");
	if (!$res)
	{
		echo mysql_error();
		print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération, le traitement n'a pas été fait</b></font>";
	}
	else
		print "<FONT COLOR='#16B84E'><b>Enregistré</b></font>";
	print "<br/><br/>";
}
if (isset($_POST['add_autorisation']))
{
	$res = doQuery("UPDATE {$GLOBALS['prefix_db']}finances SET montant=".$_POST['montant'].", autorisation=1, type_transaction='".$_POST['type_transaction']."' , num_transaction=".$_POST['num_transa'].", date_transaction='".$_POST['date_transa']."', signataire=".$_POST['signataire'].", authorized_by=".$_SESSION['uid'].", authorized_date=CURRENT_TIMESTAMP()+0 WHERE id=".$_POST['id_transa']." ");
	if (!$res)
	{
		echo mysql_error();
		print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération, le traitement n'a pas été fait</b></font>";
	}
	else
		print "<FONT COLOR='#16B84E'><b>Autorisation enregistré</b></font>";
	print "<br/><br/>";
}
if (isset($_POST['add_confirmation']))
{
	$res = doQuery("UPDATE {$GLOBALS['prefix_db']}finances SET confirmation=1, date_bancaire='".$_POST['date_bancaire']."', confirmed_by=".$_SESSION['uid'].", confirmed_date=CURRENT_TIMESTAMP()+0 WHERE id=".$_POST['id_transa']." ");
	if (!$res)
	{
		echo mysql_error();
		print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'opération, le traitement n'a pas été fait</b></font>";
	}
	else
		print "<FONT COLOR='#16B84E'><b>Confirmation enregistré</b></font>";
	print "<br/><br/>";
}
if (isset($_POST['supr_transa']))
	doQuery("DELETE FROM {$GLOBALS['prefix_db']}finances WHERE id='".$_POST['id']."' ");
if (isset($_POST['modif_transa_sub']))
	$res = doQuery("UPDATE {$GLOBALS['prefix_db']}finances SET description='".$_POST['description']."', montant=".$_POST['montant'].", type='".$_POST['type']."' WHERE id=".$_POST['id_transa']." ");
if (isset($_POST['autorisation']) && $_POST['autorisation'] == "Autoriser")
{
	print 	"<table><form method='POST' action='index.php?page=16'>
			<tr><th align='center'colspan='2'>Détails de la transaction</th></tr>
			<tr><td>Type de paiement : </td><td><select name='type_transaction'>";
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}type_transa ORDER BY nom ASC");
	while ($tmp_array = mysql_fetch_array($res))
		print "<option value='".$tmp_array['nom']."'>".$tmp_array['nom']."</option>";
	print 	"</select></td></tr>";
	$res = doQuery("SELECT montant FROM {$GLOBALS['prefix_db']}finances WHERE id=".$_POST['id_transa']."");
	$tmp_array = mysql_fetch_array($res);
	print	"<tr><td>Montant : </td><td><input name='montant' type='number' value=".$tmp_array['montant']." required /></td></tr>
			<tr><td>Date de transaction : </td><td><input name='date_transa' type='text' class='datepicker' required /></td></tr>
			<tr><td>Numéro de transaction</td><td><input name='num_transa' type='text' required /></td></tr>
			<tr><td>Nom du signataire : </td><td><SELECT name='signataire' class='filterselect' >";
	$candidates = getAdherents();
	foreach ($candidates as $key => $value)
		print '<OPTION value='.$key.' >'.$value['prenom'].' '.$value['nom'].'</OPTION>';
	print 	"</SELECT></td></tr>
			<tr><td align='center'colspan='2'><input type='hidden' name='id_transa' value='".$_POST['id_transa']."' /><input type='submit' name='add_autorisation'/></td></tr>
			</form></table>";
}
else if (isset($_POST['confirmation']) && $_POST['confirmation'] == "Confirmer")
{
	print 	"<table><form method='POST' action='index.php?page=16'>
			<tr><th>Date sur le relevé bancaire</th></tr>
			<tr><td><input name='date_bancaire' type='text' class='datepicker' required /></td></tr>
			<tr><td align='center'><input type='hidden' name='id_transa' value='".$_POST['id_transa']."' /><input type='submit' name='add_confirmation'/></td></tr>
			</form></table>";
}
else if (isset($_POST['modif_transa']))
{
	print 	"<table><form method='POST' action='index.php?page=16'>
			<tr><td>Poste budgétaire :</td><td><select name='type'>";
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}type_dep ORDER BY nom ASC");
	while ($tmp_array = mysql_fetch_array($res))
		print "<option ".($_POST['type'] == $tmp_array['nom'] ? "selected" : "")." value='".$tmp_array['nom']."'>".$tmp_array['nom']."</option>";
	print 	"</select></td></tr>
			<tr><td>Montant :</td><td><input name='montant' type='text' value='".$_POST['montant']."' /></td></tr>
			<tr><td>Description :</td><td><input name='description' type='text' value='".$_POST['description']."' /></td></tr>
			<tr><td align='center' colspan='2'><input type='hidden' name='id_transa' value=".$_POST['id']." /><input type='submit' name='modif_transa_sub'/></td></tr>
			</form></table>";
}
else
{
	// Enregistrer une nouvelle demande
	print 	"<table><form method='POST' action='index.php?page=16'>
			<tr><th colspan='2' align='center'><b>Enregistrer une nouvelle demande</b></th></tr>
			<tr><td>Poste budgétaire:</td><td><select name='type_dep'>";
	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}type_dep ORDER BY nom ASC");
	while ($tmp_array = mysql_fetch_array($res))
		print "<option value='".$tmp_array['nom']."'>".$tmp_array['nom']."</option>";
	print 	"</select></td></tr>
			<tr><td>Section : </td>";
			
	$list_enti = null;
	$tmp_list_sec = null;
	$tmp_adh_resp = null;
	$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}association ORDER BY nom ASC");
	while ($tmp_array = mysql_fetch_array($res))
		$list_enti[$tmp_array['id']] = $tmp_array['nom'];
	$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}section ORDER BY nom ASC");
	while ($tmp_array = mysql_fetch_array($res))
		$tmp_list_sec[$tmp_array['id']] = $tmp_array['nom'];

	$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}resp_asso WHERE id_adh=".$_SESSION['uid']."");
	if (mysql_num_rows($res) > 0)
		while ($tmp_array = mysql_fetch_array($res))
			$tmp_adh_resp[$tmp_array['id_asso']] = 1;

	$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}resp_section WHERE id_adh=".$_SESSION['uid']."");
	if (mysql_num_rows($res) > 0)
		while ($tmp_array = mysql_fetch_array($res))
			$tmp_adh_resp[$tmp_array['id_sec']] = 1;
			
	$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}asso_section");
	while ($tmp_array = mysql_fetch_array($res))
		$list_enti[$tmp_array['id_asso']."-".$tmp_array['id_sec']] = $list_enti[$tmp_array['id_asso']]." - ".$tmp_list_sec[$tmp_array['id_sec']];
	asort($list_enti);

	print "<td><select name='section'>";
	foreach ($list_enti as $key => $value)
	{
		$tmp_stock = explode('-', $key);
		if (isset($tmp_stock[1]))
		{
			if (isset($tmp_adh_resp[$tmp_stock[1]]) || $_SESSION['privilege'] == 1)
				print "<option name='choix' value='".$key."'>".$value."</option>";
		}
		else
		{
			if (isset($tmp_adh_resp[$tmp_stock[0]]) || $_SESSION['privilege'] == 1)
				print "<option name='choix' value='".$key."'>".$value."</option>";
		}
	}
	print 	"</select></td></tr>
			<tr><td>Fournisseur: </td><td><input name='fournisseur' type='text' required /></td></tr>
			<tr><td>Montant (en $currency): </td><td><input name='montant' type='number' required /></td></tr>
			<tr><td>Description : </td><td><input name='description' type='text' /></td></tr>
			<tr><td colspan='2' align='center'><input name='new_demande' type='submit'/></td>
			</form></table>";
			
	print "<br/>";

	// Liste des demandes d'autorisation
	print "<br/><h2>Liste des transactions</h2><br/>";
	print 	"<form method='POST' action='index.php?page=16'>
			<b>La confirmation est </b><select name='statut_demande'><option value='all'>Indifférent</option><option ".($statut_demande == "pending" ? "selected" : "")." value='pending'>En attente</option><option ".($statut_demande == "accepted" ? "selected" : "")." value='accepted'>Accepté</option><option ".($statut_demande == "refused" ? "selected" : "")." value='refused'>Refusé</option></select><br/>
			<b>La validation est </b><select name='etape_demande'><option value='all'>Indifférent</option><option ".($etape_demande == "ask" ? "selected" : "")." value='ask'>Demandé</option><option ".($etape_demande == "authorized" ? "selected" : "")." value='authorized'>Autorisé</option><option ".($etape_demande == "over" ? "selected" : "")." value='over'>Terminé</option></select><br/>
			Date de début des demandes : Année <select name='demande_debut_annee'>".returnSelectYear((isset($_POST['demande_debut_annee']) ? $_POST['demande_debut_annee'] : strftime("%Y", $date_debut_demande)))."</select> - Mois <select name='demande_debut_mois'>".returnSelectMonth((isset($_POST['demande_debut_mois']) ? $_POST['demande_debut_mois'] : strftime("%m", $date_debut_demande)))."</select><br/>
			Date de fin des demandes : Année <select name='demande_fin_annee'>".returnSelectYear((isset($_POST['demande_fin_annee']) ? $_POST['demande_fin_annee'] : strftime("%Y", $date_fin_demande)))."</select> - Mois <select name='demande_fin_mois'>".returnSelectMonth((isset($_POST['demande_fin_mois']) ? $_POST['demande_fin_mois'] : strftime("%m", $date_fin_demande)))."</select><br/>
			<input type='submit' name='demande_intervalle' value='Rechercher'/>
			</form>";

	$res = doQuery( "SELECT * FROM {$GLOBALS['prefix_db']}finances WHERE 1 "
               .($statut_demande == "pending"    ? "AND (confirmation = 0 OR autorisation = 0) AND confirmation != 2 AND autorisation != 2" :
                ($statut_demande == "accepted"   ? "                                           AND confirmation  = 1 AND autorisation  = 1" :
                ($statut_demande == "refused"    ? "AND (confirmation = 2 OR autorisation = 2)"                                             : ""
                )
                )
                ) 
               .($etape_demande  == "ask"        ? "AND autorisation  = 0 AND confirmation  = 0" :
                ($etape_demande  == "authorized" ? "AND autorisation != 0 AND confirmation  = 0" :
                ($etape_demande  == "over"       ? "AND autorisation != 0 AND confirmation != 0" : ""
                )
                )
                )." ORDER BY section ASC, autorisation ASC, date_enregistrement DESC" );
	if ($res && mysql_num_rows($res) > 0)
	{
		$liste_transaction = null;
		$transaction = null;
		while ($tmp_array = mysql_fetch_array($res))
			$liste_transaction[$tmp_array['id']] = $tmp_array;
		print 	"<table>
					<tr align='center'><th>Poste budgétaire</th><th>Section</th><th>Fournisseur</th><th>Montant</th><th>Demandé par</th><th>Demandé le</th><th>Description</th><th>Type de paiement</th><th>Numéro de la transaction</th><th>Date de la transaction</th><th>Signataire</th><th>Action</th>".(isset($_POST['detail_demandes']) && $_POST['detail_demandes'] == 'all' ? "<th>Traité par</th><th>Traité le</th>": "")."<th></th></tr>";
		foreach ($liste_transaction as $transaction)
		{
			$asso_demandeur = null;
			if ($transaction['montant'] < 0)
			{
				$tab_emetteur = explode('-', $transaction['section']);
				$asso_demandeur = $tab_emetteur[0];
				$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}association WHERE id=".$tab_emetteur[0]." ");
				if ($res)
				{
					$tmp_stock = mysql_fetch_array($res);
					$transaction['section'] = $tmp_stock['nom'];
				}
				if (isset($tab_emetteur[1]))
				{
					$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}section WHERE id=".$tab_emetteur[1]." ");
					$transaction['section'] .= " - ";
					if ($res)
					{
						$tmp_stock = mysql_fetch_array($res);
						$transaction['section'] .= $tmp_stock['nom'];
					}
				}
			}
			else // if ($transaction['montant'] > 0)
			{
				$tab_beneficiaire = explode('-', $transaction['section']);
				$asso_demandeur = $tab_beneficiaire[0];
				$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}association WHERE id=".$tab_beneficiaire[0]." ");
				if ($res)
				{
					$tmp_stock = mysql_fetch_array($res);
					$transaction['section'] = $tmp_stock['nom'];
				}
				if (isset($tab_beneficiaire[1]))
				{
					$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}section WHERE id=".$tab_beneficiaire[1]." ");
					$transaction['section'] .= " - ";
					if ($res)
					{
						$tmp_stock = mysql_fetch_array($res);
						$transaction['section'] .= $tmp_stock['nom'];
					}
				}
			}
			$self_transaction = false;
			if ($transaction['enregistreur'] == $_SESSION['uid'])
				$self_transaction = true;
			$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}resp_asso WHERE id_asso=".$asso_demandeur." ");
			$is_authorized = false;
			if ($res)
			{
				while ($tmp_array = mysql_fetch_array($res))
					if ($tmp_array['id_adh'] == $_SESSION['uid'])
						$is_authorized = true;
			}
			if ($_SESSION['privilege'] == 1)
				$is_authorized = true;
			if ($is_authorized == true || $_SESSION['privilege'] == 1 || $self_transaction == true)
			{
				$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE id=".$transaction['enregistreur']." ");
				if ($res)
				{
					$tmp_stock = mysql_fetch_array($res);
					$transaction['enregistreur'] = $tmp_stock['prenom']." ".$tmp_stock['nom'];
				}
				$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE id=".$transaction['signataire']." ");
				if ($res)
				{
					$tmp_stock = mysql_fetch_array($res);
					$transaction['signataire'] = $tmp_stock['prenom']." ".$tmp_stock['nom'];
				}
				if ($date_debut_demande < strtotime($transaction['date_enregistrement']) && $date_fin_demande > strtotime($transaction['date_enregistrement'])){

					if ($transaction['autorisation'] != 0){
							if ($transaction['autorisation'] == 1){$action = "Accepté";}
							else{$action = "Refusé";}
							
					if ($transaction['confirmation'] != 0){
						if ($transaction['confirmation'] == 1){$action = "Terminé";}
						else{$action = "Confirmation Refusée";}
					}
					else{
						if ($self_transaction == true && $is_authorized == false){$action = "Non traité";}
						else{$action = "<form method='POST' action='index.php?page=16'><input type='hidden' name='id_transa' value=".$transaction['id']." /><input type='submit' name='confirmation' value='Confirmer'/><input type='submit' name='confirmation' value='Refuser'/></form>";}
					}

					
					}
					else{
						if ($self_transaction == true && $is_authorized == false){$action = "Non traité";}
						else{$action = "<form method='POST' action='index.php?page=16'><input type='hidden' name='id_transa' value=".$transaction['id']." /><input type='submit' name='autorisation' value='Autoriser'/><input type='submit' name='autorisation' value='Refuser'/></form>";}
					}
					
					
					print "<tr align='center'><td>".$transaction['type']."</td><td>".$transaction['section']."</td><td>".$transaction['fournisseur']."</td><td>".$transaction['montant']."$currency</td><td>".$transaction['enregistreur']."</td><td>".$transaction['date_enregistrement']."</td><td>".$transaction['description']."</td><td>".$transaction['type_transaction']."</td><td>".$transaction['num_transaction']."</td><td>".$transaction['date_transaction']."</td><td>".$transaction['signataire']."</td><td>".$action."</td><td>".($self_transaction == true && $transaction['confirmation'] == 0 || ($_SESSION['privilege'] == 1 || (isset($tot_asso) && $tot_asso > 0)) ? "<form method='POST' action='index.php?page=16'><input type='hidden' name='id' value='".$transaction['id']."'/><input type='hidden' name='type' value='".$transaction['type']."' /><input type='hidden' name='description' value='".$transaction['description']."' /><input type='hidden' name='montant' value=".$transaction['montant']." /><input name='modif_transa' title='Modifier' border='0' type='image' src='./images/icone_edit.png' height='17' width='17' value='submit' /><input name='supr_transa' title='Supprimer' border='0' type='image' src='./images/icone_delete.png' height='17' width='17' value='submit' /></form>": "")."</td></tr>";
					}
					
			}
		}
		print 	"</table>";
	}
}
?>
