<?php
session_start();

	if(!isset($_GET['page'])){
		if(isset($_SESSION['user'])){$_GET['page']=7;}
		else{$_GET['page']=23;}
	}
	
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
include_once("saveImage.php");
include_once("EspaceMembre.class.php");

date_default_timezone_set(getParam('timezone.conf'));
// Définition de la promo et de la localisation
$current_promo = getParam('promo.conf');
$GLOBALS['current_promo']=$current_promo;
setlocale(LC_ALL, 'fr_FR');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
 <head>
  <title>::Fliker::Accueil</title>
  <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery-ui-timepicker.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery.multiselect.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery.multiselect.filter.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery.checkboxtree.min.css" />
  <link rel="stylesheet" type="text/css" href="./includes/style.css" />
  <link rel="stylesheet" type="text/css" href="./includes/css/psud-style.css" />
  <script type="text/javascript" src="./includes/js/jquery.js"></script>
  <script type="text/javascript" src="./includes/js/jquery-ui.js"></script>
  <script type="text/javascript" src="./includes/js/jquery.ui.timepicker.js"></script>
  <script type="text/javascript" src="./includes/js/jquery.checkboxtree.min.js"></script>
  <script type="text/javascript" src="./includes/js/jquery.confirm.js"></script>
  <script type="text/javascript" src="./includes/js/jquery.validate.min.js"></script>
  <script type="text/javascript" src="./includes/js/jquery.ui.datepicker-fr.js"></script>
  <script type="text/javascript" src="./includes/js/jquery.multiselect.min.js"></script>
  <script type="text/javascript" src="./includes/js/jquery.multiselect.filter.js"></script>
 </head>
 
<?php
print '<body>';

$EspaceMembre = new EspaceMembre;

if (isset($_GET['page']))
	$EspaceMembre->showMenu($_GET['page']);
else
	$EspaceMembre->showMenu(1);

if (isset($_GET['page']) && $_GET['page'] == "logout"){
	$EspaceMembre->logout();
	include("fiche_accueil.php");
	}

	

	print '<div id="content">';
	if(empty($_GET['page'])){
		if(isset($_SESSION['user'])){$_GET['page']=21;}
		else{$_GET['page']=23;}
	}
	switch($_GET['page'])
	{
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
		case 11:
			include("fiche_notifications.php");
			break;
		case 12:
			include("fiche_utilisateurs.php");
			break;
		case 13:
			include("fiche_messages.php");
			break;
		case 14:
			include("fiche_recapitulatif.php");
			break;
		case 16:
			include("fiche_operations.php");
			break;
		case 17:
			include("fiche_inventaire.php");
			break;
		case 18:
			include("fiche_bordereaux.php");
			break;
		case 19:
			include("fiche_champs.php");
			break;
		case 20:
			include("fiche_statistiques.php");
			break;
		case 21:
			include("fiche_sports.php");
			break;
		case 22:
			include("fiche_contact.php");
			break;
		case 23:
			include("fiche_accueil.php");
			break;

	}
	print '</div>';

	include("fiche_u-psud.php");

?>

<script type="text/javascript">
$('.confirm').confirm({
  timeout:3000,
  msg:'Confirmez-vous cette action ?',
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
$(function() {
	$( ".datepicker" ).datepicker({ 
		changeYear: true , yearRange: "-100:+0" , changeMonth: true , dateFormat: "yy-mm-dd"  
	});
});
$(".filterselect").multiselect({
multiple: false,
header: "Choisissez",
noneSelectedText: "Choisissez",
selectedList: 1
}).multiselectfilter();
</script>
