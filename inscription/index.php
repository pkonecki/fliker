<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN"> 
<html> 
 <head> 
  <title>Inscription</title> 
 </head> 
 <body> 
<h1>Inscription</h1> 
<?php
	include("../includes/paths.php");
	include($webservices."/normalTask_getChampsAdherents.php");
	$tab = getChampsAdherents();
	print "<h2>Exemple Inscription</h2>";
	print "<FORM>";
	foreach($tab as $row){
		if($row[inscription]==1){
		if($row[type]==="varchar")
		print "".$row[description]." : <INPUT type=text name=".$row[nom]."><br/>";
		
		if($row[type]==="tinyint")
		print "".$row[description]." : <INPUT type=checkbox name=".$row[nom]."><br/>";
		}
	}
	print "</FORM>";
	print "<h2>Exemple modification</h2>";
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

?>
</body></html> 