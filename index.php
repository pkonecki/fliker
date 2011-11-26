<?php
session_start();
define('_VALID_INCLUDE', TRUE);
include_once("./includes/paths.php");
include_once("Adherent.php");
include_once("Activite.php");
include_once('Creneau.php');
include_once("Asso.php");
include_once("Section.php");
include_once('Select.php');
include_once('Adhesion.php');
include_once('Supplement.php');
include_once("Paiement.php");
include_once("General.php");

$current_promo=getParam('promo');
$GLOBALS['current_promo']=$current_promo;
setlocale(LC_ALL, 'fr_FR');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
  <title>::Fliker::Accueil</title>
  <link rel="stylesheet" type="text/css" href="./includes/style.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery-ui-timepicker.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery.multiselect.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery.multiselect.filter.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery.checkboxtree.min.css" />
  <script type="text/javascript" src="./includes/js/jquery.js"></script>
  <script type="text/javascript" src="./includes/js/jquery-ui.js"></script>
  <script type="text/javascript" src="./includes/js/jquery.ui.timepicker.js"></script>
  <script type="text/javascript" src="./includes/js/jquery.checkboxtree.min.js"></script>
  <script type="text/javascript" src="./includes/js/jquery.confirm.js"></script>
  <script type="text/javascript" src="./includes/js/jquery.validate.min.js"></script>
  <script type="text/javascript" src="./includes/js/jquery.ui.datepicker-fr.js"></script>
 </head>
 <body>
<div id="top">

<?php
print '<span id="title">';
print getParam('text_top');
print '</span>';
if(!(strcmp($_SESSION['user'],"") == 0)) include("menu.php");
include("userdiv.php");
print '</div>';
if(!(strcmp($_SESSION['user'],"") == 0)){

	print '<div id="content">';
	if(empty($_GET['page'])) $_GET['page']=1;

	switch($_GET['page']){
		case 1:
			include("fiche_adherent.php");
			break;
		case 2:
			include("search.php");
			break;
		case 3:
			include("fiche_asso.php");
		break;
		case 4:
			include("fiche_section.php");
		break;
		case 5:
			include("fiche_activite.php");
		break;
		case 6:
			include("fiche_creneau.php");
		break;
		case 7:
			include("fiche_adhesion.php");
		break;
		case 8:
			include("fiche_presence.php");
		break;
		case 9:
			include("fiche_admin.php");
		break;
		case 10:
			include("action.php");
		break;
	}
	print '</div>';
} else print 'Vous n\'êtes pas connecté';
$die_footer="</div></body></html>";

?>
</body></html>

<script type="text/javascript">
$('.confirm').confirm({
  timeout:3000,
  msg:'Etes-vous sur?',
  wrapper:'<div class="conf_dial"></div>',
  buttons: {
	ok:'Oui',
	cancel:'Non',
    wrapper:'<button></button>',
    separator:'  '
  }  
});
$.extend({
  getUrlVars: function(){
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
      hash = hashes[i].split('=');
      vars.push(hash[0]);
      vars[hash[0]] = hash[1];
    }
    return vars;
  },
  getUrlVar: function(name){
    return $.getUrlVars()[name];
  }
});
$(document).ready(function() {
	


		  	$.extend($.validator.messages, {
		        required: "Ce champs est requis",
		        number: "Veuillez entrer un numéro correct",
				minlength: "Veuillez entrer au moins {0} caractères",
				maxlength: "Veuillez ne pas entrer plus de {0} caractères",
				email_req :{
					remote: "Cet email existe déjà"
				}

    		});
			$.validator.addClassRules({
				number_req: {
					required: true,
					number: true
				},
				def_req: {
					required: true,
				},
				def: {
				},
				date:{
					date:true,
				},
				date_req:{
					required:true,
					date:true
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
			$("#f_adherent_modif").validate({
				messages: {
        			email: {
						required: "Ce champs est requis",
						email: "Entrez une adresse email valide",
						remote: "L\'adresse email est déjà utilisée"
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
	$( "#datepicker" ).datepicker({ 
		changeYear: true , yearRange: "-100:+0" , changeMonth: true , dateFormat: "yy-mm-dd"  
	});
});

</script>