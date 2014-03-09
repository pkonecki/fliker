<?php
if (!defined('MEDIAWIKI')) die();

// {{{ DB ACCESS CONFIGURATION
$sportsDBi = NULL;
$sportsDBHostname = "localhost";
$sportsDBUser = "root";
$sportsDBPasswd = "";
$sportsDBName = "fliker";
$sportsDBPrefix = "fliker_";
// Choix de l'asso a afficher
$sportsDBAsso = "all";
//$promo = 2012;

// EXTENSION SETUP
$wgExtensionFunctions[] = "wfSports";
function wfSports() {
	global $IP, $wgMessageCache;
//	global $wgParser;
	require_once( "$IP/includes/SpecialPage.php" );
	$wgMessageCache->addMessages( array(
// Here you should define the article name that contains the Special Page's Title as shown in [[Special:Specialpages]]
// Where 'specialpagename' will be MediaWiki:<specialpagename> eg. Special:Allpages might be 'allpages'
// The part after '=>' is the default value of the title so again, using Special:Allpages as an example you would have...
// 'allpages' => 'All Pages';
// the part BEFORE the => must be all Lowercase.
		'sports' => 'Sports'
	) );

// SPECIALPAGE FABRIC
   	class Sports extends SpecialPage {

	// CONSTRUCTOR
	function Sports() {
		SpecialPage::SpecialPage( 'Sports' );
		#$this->includable( true );
	}

	function processSports( $affAsso ) {
	global $wgRequest, $wgOut, $sportsDBi, $sportsDBPrefix;

	function calc_cout_cre($id_asso, $id_sec, $id_act, $id_cre) {
	global $sportsDBi, $sportsDBPrefix;
	$total = 0;
	$res = $sportsDBi->select("`".$sportsDBPrefix."sup_fk` a INNER JOIN `".$sportsDBPrefix."sup` b ON a.id_sup=b.id", array('id_ent', 'valeur', 'id_asso_adh', 'promo'), "id_asso_adh = $id_asso AND id_ent IN ($id_sec, $id_act, $id_cre)", 'Database::select', array());
	while ($tmp_array = $sportsDBi->fetchObject($res))
		$total += $tmp_array->valeur;
	return ($total);
	}

	$PageName = "?title=Spécial:Sports";

	$sort = $wgRequest->getText('sort');
	$order = $wgRequest->getText('order');
	$creneaux = null;
	$activites = null;
	$section = null;
	$assos = null;
	$OrderSec = array();
	$OrderAct = array();
	$OrderCre = array();
	$OrderAsso = array();
	$listStatut = null;
	$listAsso = null;
	$listSup = null;
	$count = 0;
	$out = "";
	$currentAsso = null;
	$currentStatut = null;
	$coutCotis = 0;
	$options_cre = null;
	$list_id = null;
	$total = 0;
	
	if ($wgRequest->getText('choix_statut') != null)
		$currentStatut = $wgRequest->getText('choix_statut');
	if ($wgRequest->getText('choix_asso') != null)
		$currentAsso = $wgRequest->getText('choix_asso');
	$out .= "<center><big>Les informations présentes dans ce tableau sont susceptibles d'être modifiées en cours d'année, pensez à venir les consulter régulièrement !</big></center><br/>";
	if ($sort == "jour")
		$OrderCre = array("ORDER BY" => "CASE jour
                 WHEN 'Lundi' THEN 1 
                 WHEN 'Mardi' THEN 2 
                 WHEN 'Mercredi' THEN 3 
                 WHEN 'Jeudi' THEN 4 
                 WHEN 'Vendredi' THEN 5 
                 WHEN 'Samedi' THEN 6 
                 WHEN 'Dimanche' THEN 7 END $order, debut, lieu");
	else if ($sort == "debut")
		$OrderCre = array("ORDER BY" => "debut $order, lieu, CASE jour
                 WHEN 'Lundi' THEN 1 
                 WHEN 'Mardi' THEN 2 
                 WHEN 'Mercredi' THEN 3 
                 WHEN 'Jeudi' THEN 4 
                 WHEN 'Vendredi' THEN 5 
                 WHEN 'Samedi' THEN 6 
                 WHEN 'Dimanche' THEN 7 END");
	else if ($sort == "lieu")
		$OrderCre = array("ORDER BY" => "lieu $order, CASE jour
                 WHEN 'Lundi' THEN 1 
                 WHEN 'Mardi' THEN 2 
                 WHEN 'Mercredi' THEN 3 
                 WHEN 'Jeudi' THEN 4 
                 WHEN 'Vendredi' THEN 5 
                 WHEN 'Samedi' THEN 6 
                 WHEN 'Dimanche' THEN 7 END, debut");
	else if ($sort == "section" || $sort == "activite" || $sort == "asso" || $sort == null)
		$OrderCre = array("ORDER BY" => "CASE jour
					 WHEN 'Lundi' THEN 1 
					 WHEN 'Mardi' THEN 2 
					 WHEN 'Mercredi' THEN 3 
					 WHEN 'Jeudi' THEN 4 
					 WHEN 'Vendredi' THEN 5 
					 WHEN 'Samedi' THEN 6 
					 WHEN 'Dimanche' THEN 7 END, debut, lieu");
	// Récupération des créneaux
	$res = $sportsDBi->select("`".$sportsDBPrefix."creneau`", array('id', 'jour', 'debut', 'fin', 'lieu', 'id_act'), array(), 'Database::select', $OrderCre);
	while ($tmp_array = $sportsDBi->fetchObject($res))
		$creneaux[$tmp_array->id] = $tmp_array;

	// Récupération des activités
	if ($sort == "activite")
		$OrderAct = array('ORDER BY' => "nom $order");
	else if ($sort == "section" || $sort == "asso" || $sort == null)
		$OrderAct = array('ORDER BY' => "nom");
	$res = $sportsDBi->select("`".$sportsDBPrefix."activite`", array('id', 'nom', 'url', 'id_sec'), array(), 'Database::select', $OrderAct);

	while ($tmp_array = $sportsDBi->fetchObject($res))
		$activites[$tmp_array->id] = $tmp_array;
	
	// Récupération des sections
	if ($sort == "section")
		$OrderSec = array('ORDER BY' => "nom $order");
	else if ($sort == null || $sort == "asso")
		$OrderSec = array('ORDER BY' => "nom");
	$res = $sportsDBi->select("`".$sportsDBPrefix."section`", array('id', 'nom', 'url'), array(), 'Database::select', $OrderSec);

	while ($tmp_array = $sportsDBi->fetchObject($res))
		$sections[$tmp_array->id] = $tmp_array;
	
	// Récupération des associations
	if ($sort == "asso")
		$OrderAsso = array('ORDER BY' => "b.nom $order, nom_sec");
	else if ($sort == null)
		$OrderAsso = array('ORDER BY' => "b.nom, nom_sec");
	if ($affAsso == "all")
		$tabCondAsso = array();
	else
		$tabCondAsso = array('b.nom' => $affAsso);
	$res = $sportsDBi->select("(`".$sportsDBPrefix."asso_section` a INNER JOIN `".$sportsDBPrefix."association` b ON a.id_asso=b.id) INNER JOIN `".$sportsDBPrefix."section` c ON a.id_sec=c.id", array('id_asso', 'id_sec', 'b.id as id_asso', 'b.nom', 'b.url', 'c.id', 'c.nom as nom_sec'), $tabCondAsso, 'Database::select', $OrderAsso);
	
	$i = 0;
	while ($tmp_array = $sportsDBi->fetchObject($res))
	{
		$assos[$i] = $tmp_array;
		$i++;
	}
	
	// Calcul du supplément pour chaque section
	$res = $sportsDBi->select("(`".$sportsDBPrefix."sup_fk` a INNER JOIN `".$sportsDBPrefix."section` b ON a.id_ent=b.id) INNER JOIN `".$sportsDBPrefix."sup` c ON a.id_sup=c.id", array('id_ent', 'valeur', 'id_statut', 'id_asso_adh'), array(), 'Database::select', array());
	while ($tmp_array = $sportsDBi->fetchObject($res))
	{
		if (isset($listSup[$tmp_array->id_asso_adh."-".$tmp_array->id_ent]))
			$listSup[$tmp_array->id_asso_adh."-".$tmp_array->id_ent] += $tmp_array->valeur;
		else
			$listSup[$tmp_array->id_asso_adh."-".$tmp_array->id_ent] = $tmp_array->valeur;
	}
	
	// Affichage de la liste des statuts
	$res = $sportsDBi->select("`".$sportsDBPrefix."statut`", array('id', 'nom'), array(), 'Database::select', array('ORDER BY' => 'nom'));
	while ($tmp_array = $sportsDBi->fetchObject($res))
		$listStatut[$tmp_array->id] = $tmp_array->nom;
		
	if ($affAsso == "all")
	{
		$res = $sportsDBi->select("`".$sportsDBPrefix."association`", array('id', 'nom'), array(), 'Database::select', array('ORDER BY' => 'nom'));
		while ($tmp_array = $sportsDBi->fetchObject($res))
			$listAsso[$tmp_array->id] = $tmp_array->nom;
	}

	$out .= "<form method='POST' action='".$PageName."'>Choisissez votre ".($affAsso == "all" ? "association ainsi que votre " : "")."statut pour calculer le coût de vos inscriptions :<br/>";
	if ($affAsso == "all")
	{
		$out .= "Association <select name='choix_asso'>";
		foreach ($listAsso as $key => $value)
			$out .= "<option ".($currentAsso == $key ? "selected" : "")." name='choix' value='".$key."'>".$value."</option>";
		$out .= "</select><br/>";
	}
	$out .= "Statut <select name='choix_statut'>";
	foreach ($listStatut as $key => $value)
		$out .= "<option ".($currentStatut == $key ? "selected" : "")." name='choix' value='".$key."'>".$value."</option>";
	$out .= "</select><br/>";
	$out .= "<input type='submit' name='statut_sub' value='Calculer'/></form><br/>";
	
	if ($currentStatut != null && $currentAsso != null)
	{
		$res = $sportsDBi->select("`".$sportsDBPrefix."sup_fk` a INNER JOIN `".$sportsDBPrefix."sup` b ON a.id_sup=b.id" , array('id_ent', 'id_statut', 'valeur'), "id_statut = $currentStatut AND id_ent = $currentAsso", 'Database::select', array());
		$count = 0;
		while ($tmp_array = $sportsDBi->fetchObject($res))
		{
			$coutCotis += $tmp_array->valeur;
			$count++;
		}
		if ($count == 0)
			$out .= "Vous ne pouvez pas adhérer à cette association avec ce statut. Vous pouvez essayer avec un autre statut ou choisir une autre association.";
		else if ($count > 1)
			$out .= "Plusieurs prix existent pour ce statut, impossible de faire le calcul.";
		else
			$out .= "Le prix de votre cotisation sera de <b>".$coutCotis." &euro;</b>";
		$out .= "<br/><br/>";
	}
	else if ($currentStatut != null && $affAsso != "all")
	{
		$res = $sportsDBi->select("`".$sportsDBPrefix."sup_fk` a INNER JOIN `".$sportsDBPrefix."sup` b ON a.id_sup=b.id" , array('id_ent', 'id_statut', 'valeur'), "id_statut = $currentStatut AND id_ent IN (SELECT id FROM `".$sportsDBPrefix."association` WHERE nom = '".$affAsso."')", 'Database::select', array());
		$count = 0;
		while ($tmp_array = $sportsDBi->fetchObject($res))
		{
			$coutCotis += $tmp_array->valeur;
			$count++;
		}
		if ($count > 1)
			$out .= "Plusieurs prix existent pour ce statut, impossible de faire le calcul.";
		else
			$out .= "Le prix de votre cotisation sera de <b>".$coutCotis." &euro;</b>";
		$out .= "<br/><br/>";
	}
	
	// Affichage du tableau
	$out .= "Si vous souhaitez choisir plusieurs créneaux de sports, merci de les sélectionner puis calculer le prix total en bas de page :
	<table border='1'><form method='POST' action='".$PageName."'><tr>
	<th></th>
	<th><a href='./".$PageName."&sort=asso&order=".($sort == "asso" && $order == "asc" ? "desc" : "asc")."'>Association ".($sort == null || $sort == "asso" ? "".($order == null || $order == "asc" ? "<img width='15' weight='15' src='../images/fleche_down.png'/>" : "<img width='15' weight='15' src='../images/fleche_up.png'/>")."" : "")."</a></th>
	<th><a href='./".$PageName."&sort=section&order=".($sort == "section" && $order == "asc" ? "desc" : "asc")."'>Section ".($sort == "section" ? "".($order == "asc" ? "<img width='15' weight='15' src='../images/fleche_down.png'/>" : "<img width='15' weight='15' src='../images/fleche_up.png'/>")."" : "")."</a></th>
	<th><a href='./".$PageName."&sort=activite&order=".($sort == "activite" && $order == "asc" ? "desc" : "asc")."'>Activité ".($sort == "activite" ? "".($order == "asc" ? "<img width='15' weight='15' src='../images/fleche_down.png'/>" : "<img width='15' weight='15' src='../images/fleche_up.png'/>")."" : "")."</a></th>
	<th><a href='./".$PageName."&sort=jour&order=".($sort == "jour" && $order == "asc" ? "desc" : "asc")."'>Jour ".($sort == "jour" ? "".($order == "asc" ? "<img width='15' weight='15' src='../images/fleche_down.png'/>" : "<img width='15' weight='15' src='../images/fleche_up.png'/>")."" : "")."</a></th>
	<th><a href='./".$PageName."&sort=debut&order=".($sort == "debut" && $order == "asc" ? "desc" : "asc")."'>Heure ".($sort == "debut" ? "".($order == "asc" ? "<img width='15' weight='15' src='../images/fleche_down.png'/>" : "<img width='15' weight='15' src='../images/fleche_up.png'/>")."" : "")."</a></th>
	<th><a href='./".$PageName."&sort=lieu&order=".($sort == "lieu" && $order == "asc" ? "desc" : "asc")."'>Lieu ".($sort == "lieu" ? "".($order == "asc" ? "<img width='15' weight='15' src='../images/fleche_down.png'/>" : "<img width='15' weight='15' src='../images/fleche_up.png'/>")."" : "")."</a></th>
	<th><a href='./".$PageName."&sort=date&order=".($sort == "date" && $order == "asc" ? "desc" : "asc")."'>Date de rentrée ".($sort == "date" ? "".($order == "asc" ? "<img width='15' weight='15' src='../images/fleche_down.png'/>" : "<img width='15' weight='15' src='../images/fleche_up.png'/>")."" : "")."</a></th>
	<!-- th>Coût pour <font color='red'>un seul</font> créneau<br/><i>(Cotisation non comprise)</i></th></tr -->
	<th>Encadrant</th></tr>";
	
	// Affichage du tri par asso
	if ($sort == "asso" || $sort == null)
	{
		foreach ($assos as $asso)
			foreach ($sections as $section)
				foreach ($activites as $activite)
					foreach ($creneaux as $creneau)
						if ($asso->id_sec == $section->id && $activite->id_sec == $section->id && $creneau->id_act == $activite->id)
							$out .= "<tr align='center'><td><input type='checkbox' name='options_cre_".$asso->id_asso."[]' value=', ".$creneau->id.", ".$activite->id.", ".$section->id."' /></td><td>".($asso->url != "" ? "<a href='".$asso->url."'>".$asso->nom."</a>" : $asso->nom)."</td><td>".($section->url != "" ? "<a href='".$section->url."'>".$section->nom."</a>" : $section->nom)."</td><td>".($activite->url != "" ? "<a href='".$activite->url."'>".$activite->nom."</a>" : $activite->nom)."</td><td>".$creneau->jour."</td><td>".$creneau->debut." - ".$creneau->fin."</td><td>".$creneau->lieu."</td><td></td><td>".calc_cout_cre($asso->id_asso, $section->id, $activite->id, $creneau->id)."&euro;</td></tr>";
	}
	
	// Affichage du tri par section
	if ($sort == "section")
	{
		foreach ($sections as $section)
			foreach ($activites as $activite)
				foreach ($creneaux as $creneau)
					foreach ($assos as $asso)
						if ($asso->id_sec == $section->id && $activite->id_sec == $section->id && $creneau->id_act == $activite->id)
							$out .= "<tr align='center'><td><input type='checkbox' name='options_cre_".$asso->id_asso."[]' value=', ".$creneau->id.", ".$activite->id.", ".$section->id."' /></td><td>".($asso->url != "" ? "<a href='".$asso->url."'>".$asso->nom."</a>" : $asso->nom)."</td><td>".($section->url != "" ? "<a href='".$section->url."'>".$section->nom."</a>" : $section->nom)."</td><td>".($activite->url != "" ? "<a href='".$activite->url."'>".$activite->nom."</a>" : $activite->nom)."</td><td>".$creneau->jour."</td><td>".$creneau->debut." - ".$creneau->fin."</td><td>".$creneau->lieu."</td><td></td><td>".calc_cout_cre($asso->id_asso, $section->id, $activite->id, $creneau->id)."&euro;</td></tr>";
	}

	// Affichage du tri par activité
	else if ($sort == "activite")
	{	
		foreach ($activites as $activite)
			foreach ($creneaux as $creneau)
				foreach ($assos as $asso)
					foreach ($sections as $section)
						if ($asso->id_sec == $section->id && $activite->id_sec == $section->id && $creneau->id_act == $activite->id)
							$out .= "<tr align='center'><td><input type='checkbox' name='options_cre_".$asso->id_asso."[]' value=', ".$creneau->id.", ".$activite->id.", ".$section->id."' /></td><td>".($asso->url != "" ? "<a href='".$asso->url."'>".$asso->nom."</a>" : $asso->nom)."</td><td>".($section->url != "" ? "<a href='".$section->url."'>".$section->nom."</a>" : $section->nom)."</td><td>".($activite->url != "" ? "<a href='".$activite->url."'>".$activite->nom."</a>" : $activite->nom)."</td><td>".$creneau->jour."</td><td>".$creneau->debut." - ".$creneau->fin."</td><td>".$creneau->lieu."</td><td></td><td>".calc_cout_cre($asso->id_asso, $section->id, $activite->id, $creneau->id)."&euro;</td></tr>";
	}
	// Affichage du tri par créneau
	else if ($sort == "jour" || $sort == "debut" || $sort == "lieu")
	{	
		foreach ($creneaux as $creneau)
			foreach ($assos as $asso)
				foreach ($sections as $section)
					foreach ($activites as $activite)
						if ($asso->id_sec == $section->id && $activite->id_sec == $section->id && $creneau->id_act == $activite->id)
							$out .= "<tr align='center'><td><input type='checkbox' name='options_cre_".$asso->id_asso."[]' value=', ".$creneau->id.", ".$activite->id.", ".$section->id."' /></td><td>".($asso->url != "" ? "<a href='".$asso->url."'>".$asso->nom."</a>" : $asso->nom)."</td><td>".($section->url != "" ? "<a href='".$section->url."'>".$section->nom."</a>" : $section->nom)."</td><td>".($activite->url != "" ? "<a href='".$activite->url."'>".$activite->nom."</a>" : $activite->nom)."</td><td>".$creneau->jour."</td><td>".$creneau->debut." - ".$creneau->fin."</td><td>".$creneau->lieu."</td><td></td><td>".calc_cout_cre($asso->id_asso, $section->id, $activite->id, $creneau->id)."&euro;</td></tr>";
	}

	$out .= "</table><br/>";

	if ($wgRequest->getText('list_choice') != null)
	{
		$total = 0;
		foreach ($listAsso as $key => $value)
		{
			if (($options_cre = $wgRequest->getArray("options_cre_$key")) != null)
			{
				$list_id = "";
				foreach ($options_cre as $opt_cre)
					$list_id .= $opt_cre;
				$list_id[0] = " ";
				
				$res = $sportsDBi->select("`".$sportsDBPrefix."sup_fk` a INNER JOIN `".$sportsDBPrefix."sup` b ON a.id_sup=b.id", array('id_ent', 'valeur', 'id_asso_adh', 'promo'), "id_asso_adh = $key AND id_ent IN ($list_id)", 'Database::select', array());

				while ($tmp_array = $sportsDBi->fetchObject($res))
					$total += $tmp_array->valeur;
			}
		}
		$out .= "<table border='1'><tr><th>Coût Total des créneaux sélectionné<br/><i>(Cotisation non comprise)</i></th></tr><tr align='center'><td>$total&euro;</td></tr></table>";
	}

	$out .= "<input type='submit' name='list_choice' value='Calculer Prix' /></form>";

	$wgOut->addHTML(utf8_encode($out));
	}

	// execute
	function execute( $par = null ) {
//		global $wgUser, $wgRequest, $wgOut;
		global $wgRequest, $wgOut;
		global $sportsDBi, $sportsDBHostname, $sportsDBUser, $sportsDBPasswd, $sportsDBName, $sportsDBPrefix, $sportsDBAsso;
		
		$sportsDBi = Database::newFromParams($sportsDBHostname, $sportsDBUser, $sportsDBPasswd, $sportsDBName);

//		switch ($wgRequest->getVal('what')) {
//		case "submit":
			$this->processSports( $sportsDBAsso );
//			break;
//		case "list":
//			$this->printAllQuests();
//			break;
//		default:
//			$this->printQuests();
//		}
		$sportsDBi->close();
	}
	}
	SpecialPage::addPage( new Sports );
}
?>
