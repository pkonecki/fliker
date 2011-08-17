<?php

function getParam($id){
	$query= "SELECT valeur FROM config WHERE id='$id' ";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	$tab = array();
	$ret = mysql_result($results,0,"valeur");
	include("closedb.php");
	return $ret;
}

function week_isonumber ($time) {
// When strftime("%V") fails, some unoptimized workaround
//
// http://en.wikipedia.org/wiki/ISO_8601 : week 1 is "the week with the year's first Thursday in it (the formal ISO definition)"

    $year = strftime("%Y", $time);

    $first_day = strftime("%w", mktime(0, 0, 0, 1, 1, $year));
    $last_day = strftime("%w", mktime(0, 0, 0, 12, 31, $year));
        
    $number = $isonumber = strftime("%W", $time);

    // According to strftime("%W"), 1st of january is in week 1 if and only if it is a monday
    if ($first_day == 1)
        $isonumber--;

    // 1st of january is between monday and thursday; starting (now) at 0 when it should be 1
    if ($first_day >= 1 && $first_day <= 4)
        $isonumber++;
    else if ($number == 0)
        $isonumber = week_isonumber(mktime(0, 0, 0, 12, 31, $year - 1));

    if ($isonumber == 53 && ($last_day == 1 || $last_day == 2 || $last_day == 3))
        $isonumber = 1;

    return sprintf("%02d", $isonumber);
}

function modifPresence($adh,$cre,$week,$promo,$present){
	include("opendb.php");
	if($present) $query="INSERT INTO presence(id_adh,id_cre,week,promo) VALUES ($adh,$cre,$week,$promo)";
	else $query="DELETE FROM presence WHERE id_adh=$adh AND id_cre=$cre AND week=$week AND promo=$promo";
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	include("closedb.php");
}

function etaitPresent($adh,$cre,$week,$promo){
	
	$query="SELECT * FROM presence WHERE id_adh=$adh AND id_cre=$cre AND week=$week AND promo=$promo";
	include("opendb.php");
	$results = mysql_query($query);
	if (!$results) echo mysql_error();
	if (mysql_num_rows($results) >0 ) $ret = true;
	else $ret=false;
	include("closedb.php");	
	return $ret;
}


?>