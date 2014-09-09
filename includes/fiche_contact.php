<?php

include("opendb.php");


$query = mysql_query (" SELECT * FROM {$GLOBALS['prefix_db']}association  ");


while($data = mysql_fetch_assoc($query)){

	$nom .= '<td bgcolor="#'.$data['couleur'].'"><h3 style="padding:0;">'.$data['nom'].'</h3></td>';

	$description .= '<td bgcolor="#'.$data['couleur'].'">'.nl2br($data['description']).'</td>';

	$url .= '<td bgcolor="#'.$data['couleur'].'"><a href="'.$data['url'].'" target="_blank">'.$data['url'].'</a></td>';

	$image = 'logo_asso/'.$data['id'].'.jpg';
	if(!file_exists($image))
		$logo .= '<td bgcolor="#'.$data['couleur'].'"></td>';
	else	
		$logo .= '<td bgcolor="#'.$data['couleur'].'"><img src="'.$image.'"></td>';

}


echo '
<style>
td{border:1px solid;}
</style>


<h1>Contacts</h1>


<br /><br /><br />
<table>

<tr>'.$nom.'</tr>
<tr>'.$description.'</tr>
<tr>'.$url.'</tr>
<tr>'.$logo.'</tr>

</table>
';


?>