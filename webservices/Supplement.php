<?php

function getAssosCreneaux(){
//Pour chaque asso, somme des supplément suivant le statut de l'adhérent
$q1="SELECT  sup.id_statut id_statut_sup, A.id id_asso, A.nom nom_asso
FROM association A, sup, sup_fk
WHERE sup_fk.id_ent = A.id
AND sup.id_asso_adh IS NULL
AND sup.id=sup_fk.id_sup
GROUP BY id_asso,id_statut_sup";

//Pour chaque créneau, somme des supplément suivant l'asso de l'adhérent
$q2="SELECT sup.id_asso_adh id_asso_adh, S.id id_sec, S.nom nom_sec, AC.id id_act, AC.nom nom_act, CR.id id_cre, CR.jour jour_cre, CR.debut debut_cre, CR.fin fin_cre, CR.lieu lieu
FROM activite AC, creneau CR, section S, sup, sup_fk
WHERE CR.id_act=AC.id
AND AC.id_sec=S.id 
AND (
sup_fk.id_ent = S.id
OR sup_fk.id_ent = AC.id
OR sup_fk.id_ent = CR.id
)
AND sup.id_statut IS NULL
AND sup.id=sup_fk.id_sup
GROUP BY id_cre,id_asso_adh
ORDER BY id_cre";

$query="SELECT  S1.id_statut_sup, S1.id_asso, S1.nom_asso, S2.id_cre FROM
($q1) AS S1, ($q2) AS S2, asso_section HS
WHERE S1.id_asso = S2.id_asso_adh
AND HS.id_asso=S1.id_asso
AND HS.id_sec=S2.id_sec";


include("opendb.php");
$results = mysql_query($query);
if (!$results) echo mysql_error();
$tab = array();
while($row = mysql_fetch_array($results)){
	$tab[$row['id_statut_sup']][$row['id_cre']][$row['id_asso']]=$row['nom_asso'];
}
include("closedb.php");
return $tab;

}

?>