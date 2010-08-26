<?php

require_once "../Matrix.php";

/**
 * Given n points (x0,y0)...(xn-1,yn-1), the following method computes
 * the polynomial factors of the n-1't degree polynomial passing through
 * the n points.
 *
 * Example: Passing in three points (2,3) (1,4) and (3,7) will produce
 * the results [2.5, -8.5, 10] which means that the points are on the
 * curve y = 2.5x² - 8.5x + 10.
 *
 * @see http://geosoft.no/software/lagrange/LagrangeInterpolation.java.html
 * @see http://source.freehep.org/jcvsweb/ilc/LCSIM/wdview/lcsim/src/org/lcsim/fit/polynomial/PolynomialFitter.java
 * @author Jacob Dreyer
 * @author Paul Meagher (port to PHP and minor changes)
 *
 * @param x[] float
 * @param y[] float
 */
class LagrangeInterpolation {

	public function findPolynomialFactors($x, $y) {
		$n = count($x);

		$data = array();  // double[n][n];
		$rhs  = array();  // double[n];

		for ($i = 0; $i < $n; ++$i) {
			$v = 1;
			for ($j = 0; $j < $n; ++$j) {
				$data[$i][$n-$j-1] = $v;
				$v *= $x[$i];
			}
			$rhs[$i] = $y[$i];
		}

		// Solve m * s = b
		$m = new Matrix($data);
		$b = new Matrix($rhs, $n);

		$s = $m->solve($b);

		return $s->getRowPackedCopy();
	}	//	function findPolynomialFactors()

}	//	class LagrangeInterpolation


$x = array(2.0, 1.0, 3.0);
$y = array(3.0, 4.0, 7.0);

$li = new LagrangeInterpolation;
$f = $li->findPolynomialFactors($x, $y);

for ($i = 0; $i < 3; ++$i) {
	echo $f[$i]."<br />";
}
