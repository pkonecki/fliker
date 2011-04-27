<?php
include "../includes/paths.php";
include "opendb.php";
if(mysql_num_rows(mysql_query("SELECT email FROM adherent WHERE email='".$_GET['email']."'"))){
	echo "L'adresse email est déjà utilisée";

} else {
	echo "true";
}
include "closedb.php";

?>