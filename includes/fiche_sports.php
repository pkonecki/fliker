<?php

include("opendb.php");

$classement = "nom_section, nom_activite, CASE creneau
                 WHEN 'Lundi' THEN 1 
                 WHEN 'Mardi' THEN 2 
                 WHEN 'Mercredi' THEN 3 
                 WHEN 'Jeudi' THEN 4 
                 WHEN 'Vendredi' THEN 5 
                 WHEN 'Samedi' THEN 6 
                 WHEN 'Dimanche' THEN 7 END";
if($_GET['classement'] == "activite")
	$classement = "nom_activite";

elseif($_GET['classement'] == "creneau")
	$classement = "CASE creneau
                 WHEN 'Lundi' THEN 1 
                 WHEN 'Mardi' THEN 2 
                 WHEN 'Mercredi' THEN 3 
                 WHEN 'Jeudi' THEN 4 
                 WHEN 'Vendredi' THEN 5 
                 WHEN 'Samedi' THEN 6 
                 WHEN 'Dimanche' THEN 7 END
";

elseif($_GET['classement'] == "lieu")
	$classement = "batiment, salle";


$texte_famille = '<h2>Affichage de tous les sports</h2>';

if(isset($_GET['famille'])){

$select_famille = ' AND F.id='.$_GET['famille'].'';
$texte_famille = '<h2>Affichage des sports : "'.$_GET['nom_famille'].'"</h2>';

}


$query = mysql_query ("
SELECT F.nom AS nom_famille, F.id AS id_famille, S.nom AS nom_section, A.nom AS nom_activite, C.id AS id_creneau, C.jour AS creneau,
C.debut AS debut, C.fin AS fin, C.lieu AS lieu, BAT.nom AS batiment, SALLE.nom AS salle,
A.url AS Aurl, S.url AS Surl, ASSO.couleur AS couleur, ASSO.id AS id_asso,
S.description AS Sdescription, A.description AS Adescription,
GROUP_CONCAT(DISTINCT ' ', ADH.prenom , ' ' , ADH.nom ORDER BY ADH.nom) AS encadrants,
GROUP_CONCAT(DISTINCT '', ASSO.couleur) AS couleurs
FROM {$GLOBALS['prefix_db']}activite A, {$GLOBALS['prefix_db']}creneau C, {$GLOBALS['prefix_db']}section S,
{$GLOBALS['prefix_db']}batiment BAT, {$GLOBALS['prefix_db']}salle SALLE,
{$GLOBALS['prefix_db']}resp_cren RC, {$GLOBALS['prefix_db']}adherent ADH, {$GLOBALS['prefix_db']}famille_section FS,
{$GLOBALS['prefix_db']}famille F, {$GLOBALS['prefix_db']}asso_section ASSOSEC, {$GLOBALS['prefix_db']}association ASSO
WHERE C.id_act=A.id AND A.id_sec=S.id
AND C.lieu=SALLE.id AND SALLE.id_batiment=BAT.id
AND RC.id_cre=C.id AND RC.id_adh=ADH.id
AND RC.promo=".$current_promo."
AND FS.id_sec=S.id AND FS.id_famille=F.id ".$select_famille."
AND ASSOSEC.id_sec=S.id AND ASSOSEC.id_asso=ASSO.id
GROUP BY C.id
ORDER BY ".$classement."
");


$query_famille = mysql_query (" SELECT * FROM {$GLOBALS['prefix_db']}famille ORDER BY nom ");

echo '
<p>Vous pouvez afficher <a href="index.php?page=21">tous les sports</a> ou bien les trier selon une catégorie précise :</p>
<ul>
';

while($data_famille = mysql_fetch_assoc($query_famille)){

echo '<li><a href="index.php?page=21&famille='.$data_famille['id'].'&nom_famille='.$data_famille['nom'].'" style="font-size:16px;">'.$data_famille['nom'].'</a></li>
';

}

echo '</ul>';
echo $texte_famille;

$texte = "";
while($data = mysql_fetch_assoc($query)){

$A_lien = "";
if($data['Aurl'] != "")
	$A_lien = '<a href="'.$data['Aurl'].'">'.$data['nom_activite'].'</a>';
else
	$A_lien = $data['nom_activite'];


$S_lien = "";
if($data['Surl'] != "")
	$S_lien = '<a href="'.$data['Surl'].'">'.$data['nom_section'].'</a>';
else
	$S_lien = $data['nom_section'];



if(strpos($data['couleurs'], ',') !== false)
	$couleur = "18C8F9";
else
	$couleur = $data['couleurs'];


$texte .= '
<tr bgcolor="#'.$couleur.'">
<td title="'.$data['Sdescription'].'">'.$S_lien.'</td>
<td title="'.$data['Adescription'].'">'.$A_lien.'</td>
<td>'.$data['creneau'].'<br />'.date("H\hi", strtotime($data['debut'])).'-'.date("H\hi", strtotime($data['fin'])).'</td>
<td>'.$data['batiment'].' '.$data['salle'].'</td>
<td>'.$data['encadrants'].'</td>
</tr>
';

}


if(isset($_GET['famille']))
	$search_famille = '&famille='.$_GET['famille'].'&nom_famille='.$_GET['nom_famille'].'';


echo '
<style>
td, th{border:1px solid;}
</style>

<table>
<tr>
<th><a href="index.php?page=21'.$search_famille.'">Section</a></th>
<th><a href="index.php?page=21&classement=activite'.$search_famille.'">Activité</a></th>
<th><a href="index.php?page=21&classement=creneau'.$search_famille.'">Créneau</a></th>
<th><a href="index.php?page=21&classement=lieu'.$search_famille.'">Lieu</a></th>
<th>Encadrant</th>
</tr>

'.$texte.'
</table>
';


?>