<?php
function redirect($to='../') {
	global $basepath;
	header('HTTP/1.1 301 Moved Permanently');
	if (substr($to,0,4) == 'http')
		header('Location: '.$to);
	else
		header('Location: '.$basepath.trim($to,'/'));
	exit();
}

function verifica_cnp($cnp_primit)
{

$cnp['cnp primit'] = $cnp_primit;
// prima cifra din cnp reprezinta sexul si nu poate fi decat 1,2,5,6 (pentru cetatenii romani)
// 1, 2 pentru cei nascuti intre anii 1900 si 1999
// 5, 6 pentru cei nsacuti dupa anul 2000
$cnp['sex'] = $cnp['cnp primit']{0};
// cifrele 2 si 3 reprezinta anul nasterii
$cnp['an'] = $cnp['cnp primit']{1}.$cnp['cnp primit']{2};
// cifrele 4 si 5 reprezinta luna (nu poate fi decat intre 1 si 12)
$cnp['luna']    = $cnp['cnp primit']{3}.$cnp['cnp primit']{4};
// cifrele 6 si 7 reprezinta ziua (nu poate fi decat intre 1 si 31)
$cnp['zi']    = $cnp['cnp primit']{5}.$cnp['cnp primit']{6};
// cifrele 8 si 9 reprezinta codul judetului (nu poate fi decat intre 1 si 52)
$cnp['judet'] = $cnp['cnp primit']{7}.$cnp['cnp primit']{8};

$cnp['suma de control'] = $cnp['cnp primit']{0} * 2 + $cnp['cnp primit']{1} * 7 +
	$cnp['cnp primit']{2} * 9 + $cnp['cnp primit']{3} * 1 + $cnp['cnp primit']{4} * 4 +
	$cnp['cnp primit']{5} * 6 + $cnp['cnp primit']{6} * 3 + $cnp['cnp primit']{7} * 5 +
	$cnp['cnp primit']{8} * 8 + $cnp['cnp primit']{9} * 2 + $cnp['cnp primit']{10} * 7 +
	$cnp['cnp primit']{11} * 9;
$cnp['rest'] = fmod($cnp['suma de control'], 11);


	if (! is_numeric($cnp['cnp primit']))
		return false;

	if (strlen($cnp['cnp primit']) != 13)
		{
		$cifre = strlen($cnp['cnp primit']);
		return false;
		}
	if($cnp['sex'] != 1 && $cnp['sex'] != 2 && $cnp['sex'] != 5 && $cnp['sex'] != 6)
		return false;

	if($cnp['luna'] > 12 || $cnp['luna'] == 0 )
		return false;

	if($cnp['zi'] > 31 || $cnp['zi'] == 0)
		return false;

	if ( is_numeric($cnp['luna']) && is_numeric($cnp['zi']) && is_numeric($cnp['an']) )
	{
		if (! checkdate($cnp['luna'],$cnp['zi'],$cnp['an']))
			 return false;
	}

	if($cnp['judet'] > 52 || $cnp['judet'] == 0)
		return false;

	if (($cnp['rest'] < 10 && $cnp['rest'] != $cnp['cnp primit']{12})
		|| ($cnp['rest'] >= 10 && $cnp['cnp primit']{12} != 1))
		return false;

	return true;
}

function verifica_data($input, $extended = false) {
	/*
	list ($an, $luna, $zi, $rest) = sscanf($input,'%d-%d-%d%s');
	$an = (int) $an; $luna = (int) $luna; $zi = (int) $zi;
	if (!checkdate( $luna, $zi, $an))
		return false;
	if ($extended) {

	}*/

	list($zi,$luna,$an) = sscanf($input,'%d.%d.%d');
	return checkdate($luna,$zi,$an);

	return true;
}

function create_array($no_items = 0) {
	$array = array();
	for ($i =0 ; $i< $no_items; $i++)
		$array[] = false;
	return $array;
}

function formatData($date, $invers = false) {
	if ($invers) {
		sscanf($date, "%2s.%2s.%4s", $zi, $luna, $an);
		return "$an-$luna-$zi";
	}

	sscanf($date, "%4s-%2s-%2s", $an, $luna, $zi);
	if (!$an || !$luna || !$zi)
		return $date;
	return "$zi.$luna.$an";
}

function formatTimp($date, $excludeTimp = 0) {
	sscanf($date, "%4s-%2s-%2s %s", $an, $luna, $zi, $rest);
	if ($excludeTimp)
		return "$zi.$luna.$an";
	return "$zi.$luna.$an $rest";
}

function benchmark() {
	static $start = NULL, $total = NULL;
	if( is_null($start) ) {
		$start = get_microtime();
	} else {
		$benchmark = get_microtime() - $start;
		$total += $benchmark;
		$start = get_microtime();
		return round($total, 4).' '.round($benchmark, 4);
	}
}

function get_microtime() {
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

function set_cookie($name, $value='', $expire=0, $path='', $domain='', $secure=false, $httponly=false) {
    //set a cookie as usual, but ALSO add it to $_COOKIE so the current page load has access
    $_COOKIE[$name] = $value;
    return setcookie($name,$value,time() + $expire,$path,$domain,$secure,$httponly);
}

//@include 'inc/excel_reader2.php';