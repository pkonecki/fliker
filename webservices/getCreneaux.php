<?php

function getCreneaux($userid){
	if(!empty($_SESSION['user'])){
		if($_SESSION['privilege']==="1"){
			$query = "SELECT A.id id_asso, A.nom nom_asso, S.id id_sec, S.nom nom_sec, AC.id id_act, AC.nom nom_act, CR.id id_cre, CR.jour jour_cre, CR.debut debut_cre 
						FROM activite AC, creneau CR, section S, association A, asso_section HS
						WHERE CR.id_act=AC.id
						AND AC.id_sec=S.id
						AND A.id=HS.id_asso
						AND HS.id_sec=S.id ";
		} else {
			if (!empty($userid)) {
				$query = "SELECT A.id id_asso, A.nom nom_asso, S.id id_sec, S.nom nom_sec, AC.id id_act, AC.nom nom_act, CR.id id_cre, CR.jour jour_cre, CR.debut debut_cre 
						FROM activite AC, creneau CR, section S, association A, asso_section HS
						WHERE CR.id_act=AC.id
						AND AC.id_sec=S.id
						AND A.id=HS.id_asso
						AND HS.id_sec=S.id
							AND
							(
							S.id IN (SELECT id_sec FROM resp_section WHERE id_adh = '".$userid."')
							OR AC.id IN (SELECT id_act FROM resp_act WHERE id_adh = '".$userid."')
							OR CR.id IN (SELECT id_cre FROM resp_cren WHERE id_adh = '".$userid."')
							OR A.id IN (SELECT id_asso FROM resp_asso WHERE id_adh = '".$userid."')
							)
						";
			}
			else return;
		}

		include("opendb.php");
		$results = mysql_query($query);
		if (!$results) echo mysql_error();
		$tab = array();
		while($row = mysql_fetch_array($results)){
			$tab[$row['id_cre']] = $row;
		}
		include("closedb.php");
		return $tab;
	}
}

?>