<?php
if($_POST['action']==="submitted"){

	print_r($_POST);
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link rel="stylesheet" type="text/css" href="../includes/style.css" />
		<link rel="stylesheet" type="text/css" href="../includes/css/ui-lightness/ui.multiselect.css" />
		<link rel="stylesheet" type="text/css" href="../includes/css/ui-lightness/jquery-ui-1.8.11.custom.css" />

		<script type="text/javascript" src="../includes/js/jquery.js"></script>
		<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>
		<script type="text/javascript" src="../includes/js/ui.multiselect.js"></script>
		<script type="text/javascript" src="../includes/js/ui-multiselect-fr.js"></script>
	</head><body>
<h1>Recherche</h1>

<form id="f_search" method="post" action="index.php">
<fieldset id="main">
	<input type="hidden" name="field_count" value="1" />
	<input type="hidden" name="action" value="submitted" />
	<div id="solde">
		<label for="select_solde">Solde est</label>
		<select id="select_solde" name="select_solde">
			<option label="Indiff&eacute;rrent" value="1">Indiff&eacute;rrent</option>
			<option label="Positif" value="2">Positif</option>
			<option label="N&eacute;gatif" value="3">N&eacute;gatif</option>
			<option label="Nul" value="4">Nul</option>
		</select>
	</div>
	<div id="set1">
	<select id="set1_type" name="set1_type">
		<option label="Nom" value="1">Nom</option>
		<option label="Pr&eacute;bom" value="2">Pr&eacute;nom</option>
		<option label="Email" value="3">Email</option>
		<option label="Cat&eacute;gorie" value="4">Cat&eacute;gorie</option>
	</select>
	<select id="set1_action" name="set1_action">
		<option label="Contient" value="1">Contient</option>
		<option label="Commence" value="2">Commence</option>
		<option label="Est" value="3">Est</option>
	</select>
	<input type="text" id="set1_text" name="set1_text" />
	</div>
	<div id="filters"></div>
	<button type="button" id="add_field">+</button>
	<button type="reset" id="reset">Reset</button>
	<input type="submit" />
</fieldset>
	<div id="assos">
	<select id="select_assos" multiple="multiple" class="multiselect" name="select_assos[]">
		<option value="asesco" >Asesco</option>
		<option value="psuc" >PSUC</option>
	</select>
	</div>
	<div id="sports">
		<select id="select_sport" multiple="multiple" class="multiselect" name="select_sport[]">
		<option value="asesco" >Gym</option>
		<option value="psuc" >Baby Foot</option>
	</select>
	</div>

</form>

<script type="text/javascript">
$('#add_field').click(function() {
	var nr_of_field = parseInt($('#f_search [name=field_count]').val()) + 1;
	$('#filters').append('<div id="set'+nr_of_field+'"><select id="set'+nr_of_field+'_type" name="set'+nr_of_field+'_type"><option label="Nom" value="1">Nom</option><option label="Prénom" value="2">Prénom</option><option label="Email" value="3">Email</option><option label="Catégorie" value="4">Catégorie</option></select><select id="set'+nr_of_field+'_action" name="set'+nr_of_field+'_action" ><option label="Contient" value="1">Contient</option><option label="Commence" value="2">Commence</option><option label="Est" value="3">Est</option></select><input type="text" id="set'+nr_of_field+'_text" name="set'+nr_of_field+'_text"/></div>');
	$('#f_search [name=field_count]').val(nr_of_field)
});
$('#reset').click(function() {
	    $('#filters').children().remove();
});
$(function(){
  // choose either the full version
  //$(".multiselect").multiselect();
  // or disable some features
  $(".multiselect").multiselect({sortable: false, searchable: false, dividerLocation: 0.5});
});
</script>

</body></html>