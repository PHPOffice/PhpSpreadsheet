<?php
require_once "../Matrix.php";
/*
* @package JAMA
* @author Michael Bommarito
* @author Paul Meagher
* @version 0.1
*
* Function to fit an order n polynomial function through
* a series of x-y data points using least squares.
*
* @param $X array x values
* @param $Y array y values
* @param $n int order of polynomial to be used for fitting
* @returns array $coeffs of polynomial coefficients
* Pre-Conditions: the system is not underdetermined: sizeof($X) > $n+1
*/
function polyfit($X, $Y, $n) {
	for ($i = 0; $i < sizeof($X); ++$i)
		for ($j = 0; $j <= $n; ++$j)
			$A[$i][$j] = pow($X[$i], $j);
	for ($i=0; $i < sizeof($Y); ++$i)
		$B[$i] = array($Y[$i]);
	$matrixA = new Matrix($A);
	$matrixB = new Matrix($B);
	$C = $matrixA->solve($matrixB);
	return $C->getMatrix(0, $n, 0, 1);
}

function printpoly( $C = null ) {
	for($i = $C->m - 1; $i >= 0; --$i) {
		$r = $C->get($i, 0);
		if ( abs($r) <= pow(10, -9) )
			$r = 0;
		if ($i == $C->m - 1)
			echo $r . "x<sup>$i</sup>";
		else if ($i < $C->m - 1)
			echo " + " . $r . "x<sup>$i</sup>";
		else if ($i == 0)
			echo " + " . $r;
	}
}

$X = array(0,1,2,3,4,5);
$Y = array(4,3,12,67,228, 579);
$points = new Matrix(array($X, $Y));
$points->toHTML();
printpoly(polyfit($X, $Y, 4));

echo '<hr />';

$X = array(0,1,2,3,4,5);
$Y = array(1,2,5,10,17, 26);
$points = new Matrix(array($X, $Y));
$points->toHTML();
printpoly(polyfit($X, $Y, 2));

echo '<hr />';

$X = array(0,1,2,3,4,5,6);
$Y = array(-90,-104,-178,-252,-26, 1160, 4446);
$points = new Matrix(array($X, $Y));
$points->toHTML();
printpoly(polyfit($X, $Y, 5));

echo '<hr />';

$X = array(0,1,2,3,4);
$Y = array(mt_rand(0, 10), mt_rand(40, 80), mt_rand(240, 400), mt_rand(1800, 2215), mt_rand(8000, 9000));
$points = new Matrix(array($X, $Y));
$points->toHTML();
printpoly(polyfit($X, $Y, 3));
?>
