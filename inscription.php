<?php
session_start();

$header = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
 <head>
  <title>::Fliker::Inscription</title>
  <link rel="stylesheet" type="text/css" href="./includes/style.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
	<script type="text/javascript" src="./includes/js/jquery.js"></script>
	<script type="text/javascript" src="./includes/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="./includes/js/jquery-ui.js"></script>
	<script type="text/javascript" src="./includes/js/jquery.ui.datepicker-fr.js"></script>
 	</head>
	<body>';

$footer = '</body></html>';

define('_VALID_INCLUDE', TRUE);
include("./includes/paths.php");
include("General.php");
include("Adherent.php");
include("Select.php");
include("saveImage.php");
include_once("class.imageconverter.php");
include_once("EspaceMembre.class.php");
$dest_dossier = "./photos";

print $header;
$EspaceMembre = new EspaceMembre;
$EspaceMembre->showMenu();
print '<h2>Inscription</h2>';
if (isset($_POST['action']) && $_POST['action'] == 'submitted')
{
	if(!(strcmp($_SESSION['uid'],"") == 0))
	{
		$_SESSION = $_POST;
		$tab = getChampsAdherents();
		print "<h2>R�capitulatif</h2>";
		print '<TABLE BORDER="1">';
		foreach($tab as $row){
			if($row['inscription']==1)
			{
				print '<TR>';
				if($row['type']==="varchar")
					print '<TD>'.$row['description'].'</TD><TD>'.$_SESSION[$row['nom']].'</TD>';
				if($row['type']==="date")
					print '<TD>'.$row['description'].'</TD><TD>'.$_SESSION[$row['nom']].'</TD>';
				if($row['type']==="tinyint")
				{
					if (isset($_SESSION[$row['nom']]) && $_SESSION[$row['nom']]==="on")
						print '<TD>'.$row['description'].'</TD><TD>Oui</TD>';
					else
						print '<TD>'.$row['description'].'</TD><TD>Non</TD>';
				}
				if($row['type']==='file')
				{
					print '<TD>'.$row['description'].'</TD><TD>'.$_FILES[$row['nom']]['name'].'</TD>';
					saveImage($_SESSION['email'],$row['nom']);
				}
				if($row['type']==="select")
				{
					$tab=getSelect($row['nom']);
					print '<TD>'.$row['description'].'</TD><TD>'.$tab[$_SESSION['id_'.$row['nom']]].'</TD>';
				}
			}
			print '</TR>';
		}
		print '</TABLE>';
		print '<button type="button" onclick="history.go(-1)">
			Modifier
		</button> ';
		print '<FORM action="inscription.php" method="POST">
		<input type=\'hidden\' name=\'action\' value=\'confirmed\' />
		<INPUT type=\'submit\' value=\'Confirmer\'>
		</FORM>
		';
		print $footer;
	}
	else
		header("location: inscription.php") ;
}
else if (isset($_POST['action']) && $_POST['action'] == 'confirmed')
{
	$cryptinstall="./includes/cryptographp.fct.php";
	include $cryptinstall; 
	include("includes/captcha.php");
}
else if (isset($_POST['action']) && $_POST['action'] == 'check_code')
{
	$cryptinstall="./includes/cryptographp.fct.php";
	include $cryptinstall; 
	if (isset($_POST['code']) && chk_crypt($_POST['code']))
	{
		$EspaceMembre->addUser($_SESSION);
		print "<h2>F�licitations !</h2><p>Votre pr�-inscription a �t� enregistr�e. Veuillez SVP cliquer sur le lien de validation dans l'email qui vient de vous �tre envoy�, afin d'activer votre compte.</p><p>(Vous pouvez fermer cette fen�tre.)</p>";
		session_unset();
		session_destroy();
	}
	else
	{
		echo "<center><a><font color='#FF0000'>=> Erreur, le code est incorrect</font></a></center>" ;
		include("includes/captcha.php");
	}
}
else
{
	$tab = getChampsAdherents();
	print '<br/><FORM id="f_inscription" action="inscription.php?';
	echo SID;
	print '" enctype="multipart/form-data" method="POST">';
	print '<table border=0>';
	foreach($tab as $row)
	{
		if($row['inscription']==1)
		{
			$format =$row['format'];
			if ($row['required']==1)
				$format ="class=\"{$format}_req\"";
			else
				$format="class=\"$format\"";
			if($row['format'] === "categorie")
			{
				print '<tr ><td class="label"><LABEL for ='.$row['nom'].' >'.$row['description'].'</LABEL> : </td>
					<td>
					<INPUT type=radio name='.$row['nom'].' '.$format.' value="M">Masculin
					<INPUT type=radio name='.$row['nom'].' '.$format.' value="F">F�minin
					</td>
					</tr>
					</div>';
			}
			else
			if($row['type']==='varchar')
				print '<tr><td class="label"><LABEL for ='.$row['nom'].' >'.$row['description'].'</LABEL> : </td><td><INPUT type=text name="'.$row['nom'].'" id="'.$row['nom'].'" '.$format.' ></td></tr>';
			else
			if($row['type']==='date')
				print '<tr><td class="label"><LABEL for ="datepicker" >'.$row['description'].'</LABEL> : </td><td><INPUT type=text readonly name="'.$row['nom'].'" id ="datepicker" '.$format.' ></td></tr>';
			else
			if($row['type']==='tinyint')
				print '<tr><td class="label"><LABEL for ='.$row['nom'].' >'.$row['description'].'</LABEL> : </td><td><INPUT type=checkbox name='.$row['nom'].' '.$format.'></td></tr>';
			else
			if($row['type']==='file')
				print '<tr><td class="label"><LABEL for ='.$row['nom'].' >'.$row['description'].'</LABEL> : </td><td><INPUT type=file name='.$row['nom'].' '.$format.'></td></tr>';
			else
			if($row['type']==='select'){
				$values = getSelect($row['nom']);
				print '<tr><td class="label"><LABEL for ='.$row['nom'].' >'.$row['description'].'</LABEL> : </td><td><SELECT name="id_'.$row['nom'].'" id="id_'.$row['nom'].'" '.$format.'>';
                                       print '<OPTION value="" selected>S�lectionnez SVP :</OPTION>';
				foreach($values as $key => $value){
					print '<OPTION value="'.$key.'">'.$value.'</OPTION>';
				}
				print '</SELECT></td></tr>';
			}
		}
	}
	print '<input type=\'hidden\' name=\'action\' value=\'submitted\' />';
	print '<tr><td colspan="2"><INPUT type=\'submit\' name="submit" value=\'Envoyer\'></td></tr>';
	print '</table>';
	print '</FORM>';
	$_SESSION['uid']=session_id();
	print $footer;
}

?>
<script type="text/javascript">
function populatectlStatuts() {
    $.getJSON('./includes/statuts.php', function(data) {
		  var items = [];
		  $.each(data, function(key, val) {
		    $('#id_statut').append('<option value="' + val + '">' + key + '</option>');
		  });
    });
}

$(document).ready(function() {
		  	$.extend($.validator.messages, {
		        required: "Ce champs est requis",
		        number: "Veuillez entrer un num�ro correct",
				minlength: "Veuillez entrer au moins {0} caract�res",
				maxlength: "Veuillez ne pas entrer plus de {0} caract�res",
				email_req :{
					remote: "Cet email existe d�j�"
				}
    		});
			$.validator.addClassRules({
				number_req: {
					required: true,
					number: true
				},
				def_req: {
					required: true
				},
				def: {
				},
				date:{
					date: true
				},
				date_req: {
					required: true
				},
				email: {
					email: true,
					remote: "emails.php"
				},
				email_req: {
					required: true,
					email: true,
					remote: "emails.php"
				},
				categorie_req: {
					required : true
				},
				telephone: {
					number: true,
					minlength:10,
					maxlength:10
				},
				telephone_req: {
					required: true,
					number: true,
					minlength:10,
					maxlength:10
				}
			});
			$("#f_inscription").validate({
				messages: {
        			email: {
						required: "Ce champs est requis",
						email: "Entrez une adresse email valide",
						remote: "L\'adresse email est d�j� utilis�e"
					},
					categorie : "Ce champs est requis"
				},
				errorPlacement: function(error, element) {
	            	if ( element.is(":radio") )
	                	error.appendTo( element.parent() );
	          		else
                		error.appendTo( element.parent() );
        		},
				success: function(label) {
					// set   as text for IE
					label.html("&nbsp;").addClass("checked");
				}
			});
});

$(function() {
	$("#datepicker").datepicker({ 
		changeYear: true, yearRange: "-100:+0", changeMonth: true, dateFormat: "yy-mm-dd"  
	});
});

</script>
