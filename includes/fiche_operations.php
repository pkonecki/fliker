<?phpdefined('_VALID_INCLUDE') or die('Direct access not allowed.');$tot_asso=count(getAssociations($_SESSION['uid']));$tot_sec=count(getSections($_SESSION['uid']));$tot_act=count(getActivites($_SESSION['uid']));$tot_cre=count(getCreneaux($_SESSION['uid']));$tot = $tot_asso + $tot_sec + $tot_act + $tot_cre;if((strcmp($_SESSION['user'],"") == 0)){	print "<p>Vous n'�tes pas connect�</p>";	die();}if ($_SESSION['privilege'] == 1)	$tab_asso = getAssociations($_SESSION['uid']);else if (isset($tot_asso) && $tot_asso > 0)	$tab_asso = getAssociations($_SESSION['uid']);else if (isset($tot_sec) && $tot_sec > 0){	$tab_section = getSections($_SESSION['uid']);	$string_id_sec = "";	foreach ($tab_section as $tmp_array)		$string_id_sec .= ", ". $tmp_array['id'];	$string_id_sec[0] = ' ';	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}asso_section WHERE id_sec IN (".$string_id_sec.")");	$string_id_asso = "";	while ($tmp_array = mysql_fetch_array($res))		$string_id_asso .= ", ".$tmp_array['id_asso'];	$string_id_asso[0] = ' ';	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}association WHERE id IN (".$string_id_asso.")");	while ($tmp_array = mysql_fetch_array($res))		$tab_asso[$tmp_array['id']] = $tmp_array;}else{	print "<p>Vous n'avez pas acc�s � cette page!</p>";	die();}if(isset($_GET['promo']))	$promo=$_GET['promo'];else	$promo=$current_promo;if(!(strcmp($_SESSION['user'],"") == 0)){	$tab=getAssociations($_SESSION['uid']);	print '<ul id="submenu">';	if((isset($tot_asso) && $tot_asso > 0) || (isset($tot_sec) && $tot_sec > 0))		print '<li><a class="'.(($_GET['page']== 16) ? 'selected' : '').'" href="index.php?page=16">Op�rations</a></li>';	if((isset($tot_asso) && $tot_asso > 0) || (isset($tot_sec) && $tot_sec > 0))		print '<li><a class="'.(($_GET['page']==14) ? 'selected' : '').'" href="index.php?page=14">R�capitulatif</a></li>';	if((isset($tot_asso) && $tot_asso > 0) || (isset($tot_sec) && $tot_sec > 0))		print '<li><a class="'.(($_GET['page']==17) ? 'selected' : '').'" href="index.php?page=17">Inventaire</a></li>';	print '</ul>';}if (isset($_POST['demande_intervalle'])){	$date_debut_demande = strtotime("".$_POST['demande_debut_annee']."-".$_POST['demande_debut_mois']."-1");	$date_fin_demande = strtotime("".$_POST['demande_fin_annee']."-".$_POST['demande_fin_mois']."-30");}else{	$promo_debut = $promo-1;	$date_debut_demande = strtotime("".$promo_debut."-9-1");	$promo_fin = $promo+1;	$date_fin_demande = strtotime("".$promo_fin."-6-30");}if (isset($_POST['confirmation_intervalle'])){	$date_debut_confirmation = strtotime("".$_POST['confirmation_debut_annee']."-".$_POST['confirmation_debut_mois']."-1");	$date_fin_confirmation = strtotime("".$_POST['confirmation_fin_annee']."-".$_POST['confirmation_fin_mois']."-30");}else{	$promo_debut = $promo-1;	$date_debut_confirmation = strtotime("".$promo_debut."-9-1");	$promo_fin = $promo+1;	$date_fin_confirmation = strtotime("".$promo_fin."-6-30");}print "<br/>";if (isset($_POST['new_dep']) || isset($_POST['new_rec'])){	$type_enregistrement = "";	$_POST['montant'] = intval($_POST['montant']);	if (isset($_POST['new_dep']))	{		if ($_POST['montant'] > 0)			$_POST['montant'] *= -1;		$type_enregistrement = "depense";	}	else if (isset($_POST['new_rec']))	{		if ($_POST['montant'] < 0)			$_POST['montant'] *= -1;		$type_enregistrement = "recette";	}	$query = "INSERT INTO {$GLOBALS['prefix_db']}finances (type, emetteur, beneficiaire, montant, date_enregistrement, enregistreur, description, type_register) VALUES('".$_POST['type_dep']."', '".$_POST['emetteur']."', '".$_POST['beneficiaire']."', '".$_POST['montant']."', CURRENT_TIMESTAMP()+0, ".$_SESSION['uid'].", '".$_POST['description']."', '".$type_enregistrement."')";	$res = doQuery($query);	if (!$res)	{		echo mysql_error();		print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'op�ration, l'entr�e n'a pas �t� ajout�</b></font>";	}	else		print "<FONT COLOR='#16B84E'><b>Demande enregistr� avec succ�s</b></font>";	print "<br/><br/>";}if (isset($_POST['autorisation']) && $_POST['autorisation'] == "Non"){	$res = doQuery("UPDATE {$GLOBALS['prefix_db']}finances SET autorisation=2, authorized_by=".$_SESSION['uid'].", authorized_date=CURRENT_TIMESTAMP()+0 WHERE id=".$_POST['id_transa']." ");	if (!$res)	{		echo mysql_error();		print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'op�ration, le traitement n'a pas �t� fait</b></font>";	}	else		print "<FONT COLOR='#16B84E'><b>Enregistr�</b></font>";	print "<br/><br/>";}if (isset($_POST['confirmation']) && $_POST['confirmation'] == "Non"){	$res = doQuery("UPDATE {$GLOBALS['prefix_db']}finances SET confirmation=2, confirmed_by=".$_SESSION['uid'].", confirmed_date=CURRENT_TIMESTAMP()+0 WHERE id=".$_POST['id_transa']." ");	if (!$res)	{		echo mysql_error();		print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'op�ration, le traitement n'a pas �t� fait</b></font>";	}	else		print "<FONT COLOR='#16B84E'><b>Enregistr�</b></font>";	print "<br/><br/>";}if (isset($_POST['add_autorisation'])){	$res = doQuery("UPDATE {$GLOBALS['prefix_db']}finances SET autorisation=1, type_transaction='".$_POST['type_transaction']."' , num_transaction=".$_POST['num_transa'].", date_transaction='".$_POST['date_transa']."', signataire=".$_POST['signataire'].", authorized_by=".$_SESSION['uid'].", authorized_date=CURRENT_TIMESTAMP()+0 WHERE id=".$_POST['id_transa']." ");	if (!$res)	{		echo mysql_error();		print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'op�ration, le traitement n'a pas �t� fait</b></font>";	}	else		print "<FONT COLOR='#16B84E'><b>Autorisation enregistr�</b></font>";	print "<br/><br/>";}if (isset($_POST['add_confirmation'])){	$res = doQuery("UPDATE {$GLOBALS['prefix_db']}finances SET confirmation=1, date_bancaire='".$_POST['date_bancaire']."', confirmed_by=".$_SESSION['uid'].", confirmed_date=CURRENT_TIMESTAMP()+0 WHERE id=".$_POST['id_transa']." ");	if (!$res)	{		echo mysql_error();		print "<FONT COLOR='#FF0000'><b>Une erreur c'est produite lors de l'op�ration, le traitement n'a pas �t� fait</b></font>";	}	else		print "<FONT COLOR='#16B84E'><b>Confirmation enregistr�</b></font>";	print "<br/><br/>";}if (isset($_POST['autorisation']) && $_POST['autorisation'] == "Oui"){	print 	"<table><form method='POST' action='index.php?page=16'>			<tr><th align='center'colspan='2'>D�tails de la transaction</th></tr>			<tr><td>Type de paiement : </td><td><select name='type_transaction'>";	$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}type_transa ORDER BY nom ASC");	while ($tmp_array = mysql_fetch_array($res))		print "<option value='".$tmp_array['nom']."'>".$tmp_array['nom']."</option>";	print "</select></td></tr>			<tr><td>Date de transaction : </td><td><input name='date_transa' type='text' class='datepicker' required /></td></tr>			<tr><td>Num�ro de transaction</td><td><input name='num_transa' type='text' required /></td></tr>			<tr><td>Nom du signataire : </td><td><SELECT name='signataire' class='filterselect' >";	$candidates = getAdherents();	foreach ($candidates as $key => $value)		print '<OPTION value='.$key.' >'.$value['prenom'].' '.$value['nom'].'</OPTION>';	print 	"</SELECT></td></tr>			<tr><td align='center'colspan='2'><input type='hidden' name='id_transa' value='".$_POST['id_transa']."' /><input type='submit' name='add_autorisation'/></td></tr>			</form></table>";}else if (isset($_POST['confirmation']) && $_POST['confirmation'] == "Oui"){	print 	"<table><form method='POST' action='index.php?page=16'>			<tr><th>Date sur le relev� bancaire</th></tr>			<tr><td><input name='date_bancaire' type='text' class='datepicker' required /></td></tr>			<tr><td align='center'><input type='hidden' name='id_transa' value='".$_POST['id_transa']."' /><input type='submit' name='add_confirmation'/></td></tr>			</form></table>";}else{	// New d�pense	print 	"<div style='float:left;'><table><form method='POST' action='index.php?page=16'>			<tr><th colspan='2' align='center'><b>Enregistrer une nouvelle d�pense</b></th></tr>			<tr><td>Poste budg�taire:</td><td><select name='type_dep'>";			$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}type_dep ORDER BY nom ASC");			while ($tmp_array = mysql_fetch_array($res))				print "<option value='".$tmp_array['nom']."'>".$tmp_array['nom']."</option>";			print "</select></td></tr>			<tr><td>Emetteur : </td>";						$list_enti = null;			$tmp_list_sec = null;			$tmp_adh_resp = null;			$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}association ORDER BY nom ASC");			while ($tmp_array = mysql_fetch_array($res))				$list_enti[$tmp_array['id']] = $tmp_array['nom'];			$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}section ORDER BY nom ASC");			while ($tmp_array = mysql_fetch_array($res))				$tmp_list_sec[$tmp_array['id']] = $tmp_array['nom'];			$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}resp_asso WHERE id_adh=".$_SESSION['uid']."");			if (mysql_num_rows($res) > 0)				while ($tmp_array = mysql_fetch_array($res))					$tmp_adh_resp[$tmp_array['id_asso']] = 1;			$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}resp_section WHERE id_adh=".$_SESSION['uid']."");			if (mysql_num_rows($res) > 0)				while ($tmp_array = mysql_fetch_array($res))					$tmp_adh_resp[$tmp_array['id_sec']] = 1;								$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}asso_section");			while ($tmp_array = mysql_fetch_array($res))				$list_enti[$tmp_array['id_asso']."-".$tmp_array['id_sec']] = $list_enti[$tmp_array['id_asso']]." - ".$tmp_list_sec[$tmp_array['id_sec']];			asort($list_enti);			print "<td><select name='emetteur'>";			foreach ($list_enti as $key => $value)			{				$tmp_stock = explode('-', $key);				if (isset($tmp_stock[1]))				{					if (isset($tmp_adh_resp[$tmp_stock[1]]) || $_SESSION['privilege'] == 1)						print "<option name='choix' value='".$key."'>".$value."</option>";				}				else				{					if (isset($tmp_adh_resp[$tmp_stock[0]]) || $_SESSION['privilege'] == 1)						print "<option name='choix' value='".$key."'>".$value."</option>";				}			}			print "</select></td></tr>			<tr><td>B�n�ficiaire : </td><td><input name='beneficiaire' type='text' required  /></td></tr>			<tr><td>Montant (en �): </td><td><input name='montant' type='number' required /></td></tr>			<tr><td>Description : </td><td><input name='description' type='text' /></td></tr>			<tr><td colspan='2' align='center'><input name='new_dep' type='submit'/></td>			</form></table></div>";	// New recette	print 	"<table><form method='POST' action='index.php?page=16'>			<tr><th colspan='2' align='center'><b>Enregistrer une nouvelle recette</b></th></tr>			<tr><td >Poste budg�taire:</td><td><select name='type_dep'>";			$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}type_dep ORDER BY nom ASC");			while ($tmp_array = mysql_fetch_array($res))				print "<option value='".$tmp_array['nom']."'>".$tmp_array['nom']."</option>";			print "</select></td></tr>			<tr><td>Emetteur : </td><td><input name='emetteur' type='text' required /></td></tr>			<tr><td>B�n�ficiaire : </td>";			$list_enti = null;			$tmp_list_sec = null;			$tmp_adh_resp = null;			$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}association ORDER BY nom ASC");			while ($tmp_array = mysql_fetch_array($res))				$list_enti[$tmp_array['id']] = $tmp_array['nom'];			$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}section ORDER BY nom ASC");			while ($tmp_array = mysql_fetch_array($res))				$tmp_list_sec[$tmp_array['id']] = $tmp_array['nom'];			$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}resp_asso WHERE id_adh=".$_SESSION['uid']."");			if (mysql_num_rows($res) > 0)				while ($tmp_array = mysql_fetch_array($res))					$tmp_adh_resp[$tmp_array['id_asso']] = 1;			$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}resp_section WHERE id_adh=".$_SESSION['uid']."");			if (mysql_num_rows($res) > 0)				while ($tmp_array = mysql_fetch_array($res))					$tmp_adh_resp[$tmp_array['id_sec']] = 1;								$res= doQuery("SELECT * FROM {$GLOBALS['prefix_db']}asso_section");			while ($tmp_array = mysql_fetch_array($res))				$list_enti[$tmp_array['id_asso']."-".$tmp_array['id_sec']] = $list_enti[$tmp_array['id_asso']]." - ".$tmp_list_sec[$tmp_array['id_sec']];			asort($list_enti);			print "<td><select name='beneficiaire'>";					foreach ($list_enti as $key => $value)			{				$tmp_stock = explode('-', $key);				if (isset($tmp_stock[1]))				{					if (isset($tmp_adh_resp[$tmp_stock[1]]) || $_SESSION['privilege'] == 1)						print "<option name='choix' value='".$key."'>".$value."</option>";				}				else				{					if (isset($tmp_adh_resp[$tmp_stock[0]]) || $_SESSION['privilege'] == 1)						print "<option name='choix' value='".$key."'>".$value."</option>";				}			}			print "</select></td></tr>			<tr><td>Montant (en �): </td><td><input name='montant' type='number' required /></td></tr>			<tr><td>Description : </td><td><input name='description' type='text' /></td></tr>			<tr><td colspan='2' align='center'><input name='new_rec' type='submit'/></td>			</form></table>";						print "<br/>";	// Liste des demandes d'autorisation	if ($_SESSION['privilege'] == 1 || $tot_asso > 0)	{		$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}finances ORDER BY autorisation ASC");		$liste_transaction = null;		$transaction = null;		if (mysql_num_rows($res) > 0)		{			while ($tmp_array = mysql_fetch_array($res))				$liste_transaction[$tmp_array['id']] = $tmp_array;						print "<br/><h2>Demandes d'autorisation</h2><br/>";			print 	"<form method='POST' action='index.php?page=16'>Date de d�but des demandes : Ann�e <input type='text' name='demande_debut_annee' value='".(isset($_POST['demande_debut_annee']) ? $_POST['demande_debut_annee'] : strftime("%Y", $date_debut_demande))."'/> - Mois <input type='text' name='demande_debut_mois' value='".(isset($_POST['demande_debut_mois']) ? $_POST['demande_debut_mois'] : strftime("%m", $date_debut_demande))."'/><br/>					Date de fin des demandes : Ann�e <input type='text' name='demande_fin_annee' value='".(isset($_POST['demande_fin_annee']) ? $_POST['demande_fin_annee'] : strftime("%Y", $date_fin_demande))."'/> - Mois <input type='text' name='demande_fin_mois' value='".(isset($_POST['demande_fin_mois']) ? $_POST['demande_fin_mois'] : strftime("%m", $date_fin_demande))."'/><br/>					<input type='submit' name='demande_intervalle' value='Rechercher'/>					</form>";			if (isset($_POST['detail_demandes']) && $_POST['detail_demandes'] == 'all')				print "<form method='POST' action='index.php?page=16'><input type='submit' name='affichage' value='Afficher uniquement les demandes en attente' /></form>";			else				print "<form method='POST' action='index.php?page=16'><input type='hidden' name='detail_demandes' value='all'/><input type='submit' name='affichage' value='Afficher toutes les demandes' /></form>";			print 	"<table>						<tr align='center'><th>Poste budg�taire</th><th>Emetteur</th><th>B�n�ficiaire</th><th>Montant</th><th>Demand� par</th><th>Demand� le</th><th>Description</th><th>Autorisation ?</th>".(isset($_POST['detail_demandes']) && $_POST['detail_demandes'] == 'all' ? "<th>Trait� par</th><th>Trait� le</th>": "")."</tr>";			foreach ($liste_transaction as $transaction)			{				$asso_demandeur = null;				if ($transaction['type_register'] == 'depense')				{					$tab_emetteur = explode('-', $transaction['emetteur']);					$asso_demandeur = $tab_emetteur[0];					$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}association WHERE id=".$tab_emetteur[0]." ");					if ($res)					{						$tmp_stock = mysql_fetch_array($res);						$transaction['emetteur'] = $tmp_stock['nom'];					}					if (isset($tab_emetteur[1]))					{						$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}section WHERE id=".$tab_emetteur[1]." ");						$transaction['emetteur'] .= " - ";						if ($res)						{							$tmp_stock = mysql_fetch_array($res);							$transaction['emetteur'] .= $tmp_stock['nom'];						}					}				}				else if ($transaction['type_register'] == 'recette')				{					$tab_beneficiaire = explode('-', $transaction['beneficiaire']);					$asso_demandeur = $tab_beneficiaire[0];					$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}association WHERE id=".$tab_beneficiaire[0]." ");					if ($res)					{						$tmp_stock = mysql_fetch_array($res);						$transaction['beneficiaire'] = $tmp_stock['nom'];					}					if (isset($tab_beneficiaire[1]))					{						$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}section WHERE id=".$tab_beneficiaire[1]." ");						$transaction['beneficiaire'] .= " - ";						if ($res)						{							$tmp_stock = mysql_fetch_array($res);							$transaction['beneficiaire'] .= $tmp_stock['nom'];						}					}				}				$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}resp_asso WHERE id_asso=".$asso_demandeur." ");				$is_authorized = false;				if ($res)				{					while ($tmp_array = mysql_fetch_array($res))						if ($tmp_array['id_adh'] == $_SESSION['uid'])							$is_authorized = true;				}				if ($is_authorized == true || $_SESSION['privilege'] == 1)				{					$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE id=".$transaction['enregistreur']." ");					if ($res)					{						$tmp_stock = mysql_fetch_array($res);						$transaction['enregistreur'] = $tmp_stock['prenom']." ".$tmp_stock['nom'];					}					$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE id=".$transaction['signataire']." ");					if ($res)					{						$tmp_stock = mysql_fetch_array($res);						$transaction['signataire'] = $tmp_stock['prenom']." ".$tmp_stock['nom'];					}					if (isset($_POST['detail_demandes']) && $_POST['detail_demandes'] == 'all')					{						$name_authorized = "";						$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE id=".$transaction['authorized_by']." ");						if ($res && mysql_num_rows($res) > 0)						{							$tmp_stock_id = mysql_fetch_array($res);							$name_authorized = $tmp_stock_id['prenom']." ".$tmp_stock_id['nom'];						}						if ($date_debut_demande < strtotime($transaction['date_enregistrement']) && $date_fin_demande > strtotime($transaction['date_enregistrement']))							print "<tr align='center'><td>".$transaction['type']."</td><td>".$transaction['emetteur']."</td><td>".$transaction['beneficiaire']."</td><td>".$transaction['montant']."�</td><td>".$transaction['enregistreur']."</td><td>".$transaction['date_enregistrement']."</td><td>".$transaction['description']."</td><td>".($transaction['autorisation'] != 0 ? "".($transaction['autorisation'] == 1 ? "Accept�" : "Refus�")."" : "<form method='POST' action='index.php?page=16'><input type='hidden' name='id_transa' value=".$transaction['id']." /><input type='submit' name='autorisation' value='Oui'/><input type='submit' name='autorisation' value='Non'/></form>")."</td><td>".($transaction['authorized_by'] == 0 ? "Non trait�" : $name_authorized)."</td><td>".($transaction['authorized_date'] == 0 ? "Non trait�" : $transaction['authorized_date'])."</td></tr>";					}					else					{						if ($transaction['autorisation'] == 0 && $date_debut_demande < strtotime($transaction['date_enregistrement']) && $date_fin_demande > strtotime($transaction['date_enregistrement']))							print "<tr align='center'><td>".$transaction['type']."</td><td>".$transaction['emetteur']."</td><td>".$transaction['beneficiaire']."</td><td>".$transaction['montant']."�</td><td>".$transaction['enregistreur']."</td><td>".$transaction['date_enregistrement']."</td><td>".$transaction['description']."</td><td>".($transaction['autorisation'] != 0 ? "".($transaction['autorisation'] == 1 ? "Accept�" : "Refus�")."" : "<form method='POST' action='index.php?page=16'><input type='hidden' name='id_transa' value=".$transaction['id']." /><input type='submit' name='autorisation' value='Oui'/><input type='submit' name='autorisation' value='Non'/></form>")."</td></tr>";					}				}			}			print 	"</table>";		}	}	// Liste des confirmations	if ($_SESSION['privilege'] == 1 || $tot_asso > 0)	{		$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}finances WHERE autorisation=1 ORDER BY confirmation ASC");		$liste_transaction = null;		$transaction = null;		if (mysql_num_rows($res) > 0)		{			while ($tmp_array = mysql_fetch_array($res))				$liste_transaction[$tmp_array['id']] = $tmp_array;						print "<br/><h2>Confirmations</h2><br/>";			print 	"<form method='POST' action='index.php?page=16'>Date de d�but des confirmations : Ann�e <input type='text' name='confirmation_debut_annee' value='".(isset($_POST['confirmation_debut_annee']) ? $_POST['confirmation_debut_annee'] : strftime("%Y", $date_debut_confirmation))."'/> - Mois <input type='text' name='confirmation_debut_mois' value='".(isset($_POST['confirmation_debut_mois']) ? $_POST['confirmation_debut_mois'] : strftime("%m", $date_debut_confirmation))."'/><br/>					Date de fin des confirmations : Ann�e <input type='text' name='confirmation_fin_annee' value='".(isset($_POST['confirmation_fin_annee']) ? $_POST['confirmation_fin_annee'] : strftime("%Y", $date_fin_confirmation))."'/> - Mois <input type='text' name='confirmation_fin_mois' value='".(isset($_POST['confirmation_fin_mois']) ? $_POST['confirmation_fin_mois'] : strftime("%m", $date_fin_confirmation))."'/><br/>					<input type='submit' name='confirmation_intervalle' value='Rechercher'/>					</form>";			if (isset($_POST['detail_confirmation']) && $_POST['detail_confirmation'] == 'all')				print "<form method='POST' action='index.php?page=16'><input type='submit' name='affichage' value='Afficher uniquement les confirmations en attente' /></form>";			else				print "<form method='POST' action='index.php?page=16'><input type='hidden' name='detail_confirmation' value='all'/><input type='submit' name='affichage' value='Afficher toutes les confirmations' /></form>";			print 	"<table>						<tr align='center'><th>Poste budg�taire</th><th>Type de paiement</th><th>Num�ro de la transaction</th><th>Date de la transaction</th><th>Emetteur</th><th>B�n�ficiaire</th><th>Montant</th><th>Confirmation ?</th>".(isset($_POST['detail_confirmation']) && $_POST['detail_confirmation'] == 'all' ? "<th>Trait� par</th><th>Trait� le</th>": "")."</tr>";			foreach ($liste_transaction as $transaction)			{				$asso_demandeur = null;				if ($transaction['type_register'] == 'depense')				{					$tab_emetteur = explode('-', $transaction['emetteur']);					$asso_demandeur = $tab_emetteur[0];					$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}association WHERE id=".$tab_emetteur[0]." ");					if ($res)					{						$tmp_stock = mysql_fetch_array($res);						$transaction['emetteur'] = $tmp_stock['nom'];					}					if (isset($tab_emetteur[1]))					{						$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}section WHERE id=".$tab_emetteur[1]." ");						$transaction['emetteur'] .= " - ";						if ($res)						{							$tmp_stock = mysql_fetch_array($res);							$transaction['emetteur'] .= $tmp_stock['nom'];						}					}				}				else if ($transaction['type_register'] == 'recette')				{					$tab_beneficiaire = explode('-', $transaction['beneficiaire']);					$asso_demandeur = $tab_beneficiaire[0];					$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}association WHERE id=".$tab_beneficiaire[0]." ");					if ($res)					{						$tmp_stock = mysql_fetch_array($res);						$transaction['beneficiaire'] = $tmp_stock['nom'];					}					if (isset($tab_beneficiaire[1]))					{						$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}section WHERE id=".$tab_beneficiaire[1]." ");						$transaction['beneficiaire'] .= " - ";						if ($res)						{							$tmp_stock = mysql_fetch_array($res);							$transaction['beneficiaire'] .= $tmp_stock['nom'];						}					}				}				$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}resp_asso WHERE id_asso=".$asso_demandeur." ");				$is_authorized = false;				if ($res)				{					while ($tmp_array = mysql_fetch_array($res))						if ($tmp_array['id_adh'] == $_SESSION['uid'])							$is_authorized = true;				}				if ($is_authorized == true || $_SESSION['privilege'] == 1)				{					$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE id=".$transaction['enregistreur']." ");					if ($res)					{						$tmp_stock = mysql_fetch_array($res);						$transaction['enregistreur'] = $tmp_stock['prenom']." ".$tmp_stock['nom'];					}					$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE id=".$transaction['signataire']." ");					if ($res)					{						$tmp_stock = mysql_fetch_array($res);						$transaction['signataire'] = $tmp_stock['prenom']." ".$tmp_stock['nom'];					}					// Affichage complet ou partiel					if (isset($_POST['detail_confirmation']) && $_POST['detail_confirmation'] == 'all')					{						$name_confirmed = "";						$res = doQuery("SELECT * FROM {$GLOBALS['prefix_db']}adherent WHERE id=".$transaction['confirmed_by']." ");						if ($res && mysql_num_rows($res) > 0)						{							$tmp_stock_id = mysql_fetch_array($res);							$name_confirmed = $tmp_stock_id['prenom']." ".$tmp_stock_id['nom'];						}						if ($date_debut_confirmation < strtotime($transaction['date_enregistrement']) && $date_fin_confirmation > strtotime($transaction['date_enregistrement']))							print "<tr align='center'><td>".$transaction['type']."</td><td>".$transaction['type_transaction']."</td><td>".$transaction['num_transaction']."</td><td>".$transaction['date_transaction']."</td><td>".$transaction['emetteur']."</td><td>".$transaction['beneficiaire']."</td><td>".$transaction['montant']."�</td><td>".($transaction['confirmation'] != 0 ? "".($transaction['confirmation'] == 1 ? "Accept�" : "Refus�")."" : "<form method='POST' action='index.php?page=16'><input type='hidden' name='id_transa' value=".$transaction['id']." /><input type='submit' name='confirmation' value='Oui'/><input type='submit' name='confirmation' value='Non'/></form>")."</td><td>".($transaction['confirmed_by'] == 0 ? "Non trait�" : $name_confirmed)."</td><td>".($transaction['confirmed_date'] == 0 ? "Non trait�" : $transaction['confirmed_date'])."</td></tr>";					}					else					{						if ($transaction['confirmation'] == 0 && $date_debut_confirmation < strtotime($transaction['date_enregistrement']) && $date_fin_confirmation > strtotime($transaction['date_enregistrement']))							print "<tr align='center'><td>".$transaction['type']."</td><td>".$transaction['type_transaction']."</td><td>".$transaction['num_transaction']."</td><td>".$transaction['date_transaction']."</td><td>".$transaction['emetteur']."</td><td>".$transaction['beneficiaire']."</td><td>".$transaction['montant']."�</td><td>".($transaction['confirmation'] != 0 ? "".($transaction['confirmation'] == 1 ? "Accept�" : "Refus�")."" : "<form method='POST' action='index.php?page=16'><input type='hidden' name='id_transa' value=".$transaction['id']." /><input type='submit' name='confirmation' value='Oui'/><input type='submit' name='confirmation' value='Non'/></form>")."</td></tr>";					}				}			}			print 	"</table>";		}	}}?>