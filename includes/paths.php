<?php
function add_include_path ($path){
    foreach (func_get_args() AS $path)    {
        if (!file_exists($path) OR (file_exists($path) && filetype($path) !== 'dir'))        {
            trigger_error("Include path '{$path}' not exists", E_USER_WARNING);
            continue;
        }

        $paths = explode(PATH_SEPARATOR, get_include_path());

        if (array_search($path, $paths) === false)
            array_push($paths, $path);

        set_include_path(implode(PATH_SEPARATOR, $paths));
    }
}

function remove_include_path ($path){
    foreach (func_get_args() AS $path)    {
        $paths = explode(PATH_SEPARATOR, get_include_path());

        if (($k = array_search($path, $paths)) !== false)
            unset($paths[$k]);
        else
            continue;

        if (!count($paths))        {
            trigger_error("Include path '{$path}' can not be removed because it is the only", E_USER_NOTICE);
            continue;
        }

        set_include_path(implode(PATH_SEPARATOR, $paths));
    }
}

function protect($string){
	$string = trim(strip_tags(addslashes($string)));
	return $string;
}

function get_extension($nom) {
	$nom = explode(".", $nom);
	$nb = count($nom);
	return strtolower($nom[$nb-1]);
}

function print_r_html ($arr) {
print '<pre>';
print_r($arr);
print '<pre>';
}

$GLOBALS['root']=$_SERVER['DOCUMENT_ROOT']."/fliker";
$webservices=$GLOBALS['root']."/webservices";
$includes=$GLOBALS['root']."/includes";
$inscription=$GLOBALS['root']."/inscription";
$photos=$GLOBALS['root']."/photo";
$prefix_db="fliker_";
add_include_path($webservices);
add_include_path($includes);
add_include_path($inscription);


?>