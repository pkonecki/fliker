<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN"> 
<html> 
 <head> 
  <title>Inscription</title> 
 </head> 
 <body> 
<h1>Inscription</h1> 
<?php
	include("../includes/paths.php");
	include("normalTask_getChampsAdherents.php");
	if ($_POST['action'] == 'submitted') {
$tab = getChampsAdherents();
		print "<h2>Bla</h2>";
		print "<FORM action=\"index.php\" method=\"POST\">";
		foreach($tab as $row){
			if($row[inscription]==1){
			if($row[type]==="varchar")
			print "<LABEL for =".$row[nom]." >".$row[description]."</LABEL> : <INPUT type=text name=".$row[nom]."  value=".$_POST[$row[nom]]."><br/>";
			
			if($row[type]==="tinyint"){
				print "<LABEL for =".$row[nom]." >".$row[description]."</LABEL> : <INPUT type=checkbox name=".$row[nom]." value=".$_POST[$row[nom]]."><br/>";
				print $_POST[$row[nom]];
			}
			}
		}
		print "<input type=\"hidden\" name=\"action\" value=\"submitted\" />";
		print "<INPUT type=\"submit\" value=\"Send\">";
		print "</FORM>";

	} else {
	
		$tab = getChampsAdherents();
		print "<h2>Exemple Inscription</h2>";
		print "<FORM action=\"index.php\" method=\"POST\">";
		foreach($tab as $row){
			if($row[inscription]==1){
			if($row[type]==="varchar")
			print "<LABEL for =".$row[nom]." >".$row[description]."</LABEL> : <INPUT type=text name=".$row[nom]."><br/>";
			
			if($row[type]==="tinyint")
			print "<LABEL for =".$row[nom]." >".$row[description]."</LABEL> : <INPUT type=checkbox name=".$row[nom]."><br/>";
			}
		}
		print "<input type=\"hidden\" name=\"action\" value=\"submitted\" />";
		print "<INPUT type=\"submit\" value=\"Send\">";
		print "</FORM>";
	}
	
	/*print "<h2>Exemple modification</h2>";
	print "<FORM>";
	foreach($tab as $row){
		if($row[user_editable]==1){
		if($row[type]==="varchar")
		print "".$row[description]." : <INPUT type=text name=".$row[nom]."><br/>";
		
		if($row[type]==="tinyint")
		print "".$row[description]." : <INPUT type=checkbox name=".$row[nom]."><br/>";
		}
	}
	print "</FORM>";
	*/	

?>
</body></html> 