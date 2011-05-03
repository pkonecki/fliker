<?php
include("./includes/paths.php");

include("opendb.php");
$queryString = $_SERVER['QUERY_STRING'];

$query = "SELECT * FROM adherent";

$result = mysql_query($query) or die(mysql_error());

  while($row = mysql_fetch_array($result)){

    if ($queryString == $row["activationkey"]){

       echo "Bravo!" . $row["prenom"] . ", votre compte a été activé.";

       $sql="UPDATE adherent SET activationkey = '', active=1 WHERE (id = $row[id])";

       if (!mysql_query($sql))

  {

        die('Error: ' . mysql_error());

  }

    }

  }

include("closedb.php");
?>