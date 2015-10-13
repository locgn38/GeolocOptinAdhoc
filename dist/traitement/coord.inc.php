<?php
//fonction php de conversion de coordonnées
//<!-- D° MM' SS.SS''  -->
function convertSexa($var, $cor) {
	if ($cor == 'lat') {
		if ($var > 0)$card = 'N';
		else $card = 'S';
	}
	if ($cor == 'long') {
		if ($var > 0)$card = 'E';
		else $card = 'O';
	}
	$var = abs($var);
	$deg = intval($var);
	$min = ($var - $deg)*60;
	$sec = ($min - intval($min))*60;
	return str_pad($deg, 2, '0', STR_PAD_LEFT).' '.intval($min)."'".number_format($sec, 2).'"'.$card;
}
//<!-- D° MM.MMM'  -->
function convertMM($var, $cor) {
	if ($cor == 'lat'){
		if ($var > 0)$card = 'N';
		else $card = 'S';
	}
	if ($cor == 'long'){
		if ($var > 0)$card = 'E';
		else $card = 'O';
	}
	$var = abs($var);
	$deg = intval($var);
	$min = ($var - $deg)*60;
	return str_pad($deg, 2, '0', STR_PAD_LEFT).' '.number_format($min, 3)."'".$card;
}
//<!-- D.D  -->
function convertDD($var, $cor) {
	if ($cor == 'lat'){
		if ($var > 0)$card = 'N';
		else $card = 'S';
	}
	if ($cor == 'long'){
		if ($var > 0)$card = 'E';
		else $card = 'O';
	}
	$var = abs($var);
	return number_format($var, 5)." ".$card;
}
?>