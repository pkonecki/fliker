<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN"> 
<html> 
 <head> 
  <title>Inscription</title> 
  <link rel="stylesheet" type="text/css" href="../includes/style.css" /> 
  <link rel="stylesheet" type="text/css" href="../includes/css/ui-lightness/jquery-ui-1.8.11.custom.css" /> 
	<script type="text/javascript" src="../includes/js/jquery.js"></script>
	<script type="text/javascript" src="../includes/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>
	<script>
		
		  $(document).ready(function(){
			$("#f_inscription").validate();
		  });
		  $(function() {
			$( "#datepicker" ).datepicker({ changeYear: true , yearRange: '-100:+0' , changeMonth: true });

	});
		
	</script>
 </head> 
 <body> 
<h1>Inscription</h1> 
<?php
	include("../includes/paths.php");
	include("normalTask_getChampsAdherents.php");
	if ($_POST['action'] == 'submitted') {
$tab = getChampsAdherents();
		print "<h2>Recapitulatif</h2>";
		print '<TABLE BORDER="1">';
		foreach($tab as $row){
			if($row[inscription]==1){
			print '<TR>';
			if($row[type]==="varchar")
			print '<TD>'.$row[description].'</TD><TD>'.$_POST[$row[nom]].'</TD>';
			
			if($row[type]==="tinyint"){
				if ($_POST[$row[nom]]==="on")
				print '<TD>'.$row[description].'</TD><TD>Oui</TD>';
				else
				print '<TD>'.$row[description].'</TD><TD>Non</TD>';
			}
			}
			print '</TR>';
		}
		print '</TABLE>';
		print '<button type="button" onclick="history.go(-1)">
			Modifier
		</button> ';

	} else {
	
		$tab = getChampsAdherents();
		print '<h2>Exemple Inscription</h2>';
		print '<FORM id=\'f_inscription\' action=\'index.php\' method=\'POST\'>';
		foreach($tab as $row){
			if($row[inscription]==1){
			if($row[type]==='varchar')
			print '<LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : <INPUT type=text name='.$row[nom].' id='.$row[nom].' value=\''.$_POST[$row[nom]].'\' class=\'required error\' minlength=\'2\' ><br/>';
			if($row[type]==='date')
			print '<LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : <INPUT type=text name='.$row[nom].' id ="datepicker"><br/>';
			if($row[type]==='tinyint')
			print '<LABEL for ='.$row[nom].' >'.$row[description].'</LABEL> : <INPUT type=checkbox name='.$row[nom].'><br/>';
			}
		}
		print '<input type=\'hidden\' name=\'action\' value=\'submitted\' />';
		print '<INPUT type=\'submit\' value=\'Send\'>';
		print '</FORM>';
	}
	


?>
</body></html> 