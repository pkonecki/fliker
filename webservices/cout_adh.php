<?PHP
include_once("../includes/paths.php");
$where=" false ";
$id_statut_adh=$_GET['id_statut_adh'];
foreach($_GET as $key => $asso ){
	if(preg_match('/^cre_\d+/',$key)) {
		$cre = preg_replace('/^cre_(\d+)/','$1',$key);
		$where.="OR (CR.id=$cre AND A.id=$asso ) ";
	}
}
$tout ="SELECT A.id id_asso, A.nom nom_asso, S.id id_sec, S.nom nom_sec, AC.id id_act, AC.nom nom_act, CR.id id_cre, CR.jour jour_cre, CR.debut debut_cre, CR.fin fin_cre, CR.lieu lieu
					FROM activite AC, creneau CR, section S, association A, asso_section HS
					WHERE CR.id_act=AC.id
					AND AC.id_sec=S.id
					AND A.id=HS.id_asso
					AND HS.id_sec=S.id 
					AND ($where)
					ORDER BY nom_sec";
$assos = "SELECT DISTINCT sup.valeur as valeur, S1.id_asso FROM ($tout) AS S1 ,sup 
			WHERE sup.id_asso_adh IS NULL AND sup.id_asso_paie = S1.id_asso AND sup.id_statut=$id_statut_adh";
$secs = "SELECT DISTINCT sup.valeur as valeur, S1.id_asso, S1.id_sec FROM ($tout) AS S1 ,sup,sup_fk 
			WHERE sup.id_statut IS NULL AND sup.id_asso_adh = S1.id_asso AND S1.id_sec=sup_fk.id_ent AND sup_fk.id_sup=sup.id";
$acts = "SELECT DISTINCT sup.valeur as valeur, S1.id_asso, S1.id_act FROM ($tout) AS S1 ,sup,sup_fk 
			WHERE sup.id_statut IS NULL AND sup.id_asso_adh = S1.id_asso AND S1.id_act=sup_fk.id_ent AND sup_fk.id_sup=sup.id";
$cres = "SELECT DISTINCT sup.valeur as valeur, S1.id_asso, S1.id_cre FROM ($tout) AS S1 ,sup,sup_fk 
			WHERE sup.id_statut IS NULL AND sup.id_asso_adh = S1.id_asso AND S1.id_cre=sup_fk.id_ent AND sup_fk.id_sup=sup.id";

$t_assos = "SELECT SUM(A.valeur) t_assos FROM ($assos) AS A ";
$t_secs = "SELECT SUM(A.valeur) t_secs FROM ($secs) AS A ";
$t_acts = "SELECT SUM(A.valeur) t_acts FROM ($acts) AS A ";
$t_cres = "SELECT SUM(A.valeur) t_cres FROM ($cres) AS A ";
$total = "SELECT ( IFNULL(A.t_assos,0) + IFNULL(S.t_secs,0) + IFNULL(AC.t_acts,0) + IFNULL(C.t_cres,0) ) total  FROM ($t_assos) AS A, ($t_secs) AS S,($t_acts) AS AC, ($t_cres) AS C";
include("opendb.php");
$results = mysql_query($total);
if (!$results) echo mysql_error();
$ret = mysql_result($results,0,"total");
$resultat = array('total' => $ret);
print(json_encode($resultat));
include("closedb.php");
?>