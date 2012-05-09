<?php
defined('_VALID_INCLUDE') or die('Direct access not allowed.');
/*session_start();*/
if (!isset($_GET['adh']) or $_GET['adh']==$_SESSION['uid']) {
	$id_adh =$_SESSION['uid'];
	$edit=true;
} else if($_SESSION['privilege']==1){
	$admin=true;
	$id_adh=$_GET['adh'];
}
else {
	if(count(getMyAssos($_SESSION['uid'])) > 0 ) {
		$resp_asso=true;
		$assos_resp=getMyAssos($_SESSION['uid']);
	}
	$tab = getMyAdherents($_SESSION['uid']);
	if (isset($tab[$_GET['adh']])) $id_adh=$_GET['adh'];
	else {
		print 'Vous n\'avez pas accès à cette page';
		die();
	}

}
$adh = getAdherent($id_adh);
print '<ul id="submenu"><li><a class="selected" href="index.php?page=1&adh='.$id_adh.'">Fiche Adhérent</a></li><li><a href="index.php?page=7&adh='.$id_adh.'">Adhésions</a></li></ul>';

$dest_dossier = "../photos";
	if (isset($_POST['action']) && $_POST['action'] == 'modification' && $edit) {
		$tab = getChampsAdherents();
		print '<FORM id="f_adherent_modif" action="index.php?page=1" enctype="multipart/form-data" method="POST">';
		print '<table border=0>';
		foreach($tab as $row){
			if($row[user_editable]==1){
				$format =$row['format'];
				if ($row[required]==1) $format ="class=\"{$format}_req\"";
				else $format="class=\"$format\"";
				if($row[format] === "categorie"){
					if($adh[$row['nom']]==='M'){
						$homme='checked';
						$femme='';
					} else {
						$homme='';
						$femme='checked';

					}
					print '<tr ><td class="label"><LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : </td>
						<td>
						<INPUT type=radio name='.$row[nom].' '.$format.' value="M" '.$homme.' >Masculin
						<INPUT type=radio name='.$row[nom].' '.$format.' value="F" '.$femme.' >Féminin
						</td>
						</tr>
						</div>';
				}
				else if($row[type]==='varchar')
					print '<tr><td class="label"><LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : </td><td><INPUT type=text name="'.$row[nom].'" id="'.$row[nom].'" '.$format.' value="'.$adh[$row['nom']].'"></td></tr>';
				else if($row[type]==='date')
					print '<tr><td class="label"><LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : </td><td><INPUT type=text name="'.$row[nom].'" id="datepicker" '.$format.'  value="'.$adh[$row['nom']].'"></td></tr>';
				else if($row[type]==='tinyint')
					print '<tr><td class="label"><LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : </td><td><INPUT type=checkbox name='.$row[nom].' '.$format.'  '.($adh[$row['nom']]==1 ? "checked" : "").'></td></tr>';
				else if($row[type]==='file')
					print '<tr><td class="label"><LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : </td><td><INPUT type=file name='.$row[nom].' '.$format.'  ></td></tr>';
				else if($row['type']==='select')
				{
					$values = getSelect($row['nom']);

					print '<tr><td class="label"><LABEL for ='.$row['nom'].' >'.$row['description'].'</LABEL> : </td><td><SELECT name="id_'.$row['nom'].'" id="id_'.$row['nom'].'" '.$format.'>';
					foreach($values as $key => $value)
					{
						print '<OPTION value="'.$key.'">'.$value.'</OPTION>';
					}
					print '</SELECT></td></tr>';
				}
			}
		}
		print '<input type=\'hidden\' name=\'action\' value=\'submitted\' />';
		print "<INPUT type=\"hidden\" name=\"id_adh\" value =\"$id_adh\">";
		print "<INPUT type=\"hidden\" name=\"email\" value =\"{$adh['email']}\">";
		print '<tr><td colspan="2"><INPUT type=\'submit\' value=\'Send\'></td></tr>';

		print '</table>';
		print '</FORM>';
	}
	else {
		if (isset($_POST['action']) && $_POST['action'] == 'submitted' && $edit){

			modifAdherent($_POST);
			$adh = getAdherent($id_adh);
		}
		if(!(strcmp($_SESSION['user'],"") == 0)){

			$tab = getChampsAdherents();
			print '<div id="fiche">';
			print "<h2>Fiche de {$adh['prenom']} {$adh['nom']}</h2>";
			print "<div class=\"tip\">".getParam('text_adherent')."</div>";
			print '<TABLE BORDER="0">';
			foreach($tab as $row){
				if($row['user_viewable']==1){
					print '<TR>';
					if($row['type']==="varchar")
						print '<TD>'.$row['description'].'</TD><TD>'.$adh[$row['nom']].'</TD>';

					if($row['type']==="date")
						print '<TD>'.$row['description'].'</TD><TD>'.$adh[$row['nom']].'</TD>';

					if($row['type']==="tinyint"){
						if ($adh[$row['nom']]==1)
							print '<TD>'.$row['description'].'</TD><TD>Oui</TD>';
						else
							print '<TD>'.$row['description'].'</TD><TD>Non</TD>';
					}
					if($row['type']==='file'){
						$_SESSION['auth_thumb']='true';
						$photo="includes/thumb.php?folder=".$row['nom']."&file=".$adh['email'].".jpg";
						print '<TD>'.$row['description'].'</TD><TD><a href="'.$row['nom'].'/'.$adh['email'].'.jpg"><img src="'.$photo.'" height="150"></a></TD>';
					}
					if($row['type']==="select"){
						$tab=getSelect($row['nom']);
						print '<TD>'.$row['description'].'</TD><TD>'.$tab[$adh[$row['nom']]].'</TD>';
					}

				}
				print '</TR>';
			}
			print '</TABLE>';
			if($edit) print '<FORM action="index.php?page=1" method="POST">
			<input type=\'hidden\' name=\'action\' value=\'modification\' />
			<INPUT type=\'submit\' value=\'Modifier\'>
			</FORM>
			</div>
			';

		}
		else {
			print "<p>Vous n'êtes pas connecté</p>";
		}
	}



?>