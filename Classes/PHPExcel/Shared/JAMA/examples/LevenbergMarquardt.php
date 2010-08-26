<?php

// Levenberg-Marquardt in PHP

// http://www.idiom.com/~zilla/Computer/Javanumeric/LM.java

class LevenbergMarquardt {

	/**
	 * Calculate the current sum-squared-error
	 *
	 * Chi-squared is the distribution of squared Gaussian errors,
	 * thus the name.
	 *
	 * @param double[][] $x
	 * @param double[] $a
	 * @param double[] $y,
	 * @param double[] $s,
	 * @param object $f
	 */
	function chiSquared($x, $a, $y, $s, $f) {
		$npts = count($y);
		$sum = 0.0;

		for ($i = 0; $i < $npts; ++$i) {
			$d = $y[$i] - $f->val($x[$i], $a);
			$d = $d / $s[$i];
			$sum = $sum + ($d*$d);
		}

		return $sum;
	}	//	function chiSquared()


	/**
	 * Minimize E = sum {(y[k] - f(x[k],a)) / s[k]}^2
	 * The individual errors are optionally scaled by s[k].
	 * Note that LMfunc implements the value and gradient of f(x,a),
	 * NOT the value and gradient of E with respect to a!
	 *
	 * @param x array of domain points, each may be multidimensional
	 * @param y corresponding array of values
	 * @param a the parameters/state of the model
	 * @param vary false to indicate the corresponding a[k] is to be held fixed
	 * @param s2 sigma^2 for point i
	 * @param lambda blend between steepest descent (lambda high) and
	 *	jump to bottom of quadratic (lambda zero).
	 * 	Start with 0.001.
	 * @param termepsilon termination accuracy (0.01)
	 * @param maxiter	stop and return after this many iterations if not done
	 * @param verbose	set to zero (no prints), 1, 2
	 *
	 * @return the new lambda for future iterations.
	 *  Can use this and maxiter to interleave the LM descent with some other
	 *  task, setting maxiter to something small.
	 */
	function solve($x, $a, $y, $s, $vary, $f, $lambda, $termepsilon, $maxiter, $verbose) {
		$npts = count($y);
		$nparm = count($a);

		if ($verbose > 0) {
			print("solve x[".count($x)."][".count($x[0])."]");
			print(" a[".count($a)."]");
			println(" y[".count(length)."]");
		}

		$e0 = $this->chiSquared($x, $a, $y, $s, $f);

		//double lambda = 0.001;
		$done = false;

		// g = gradient, H = hessian, d = step to minimum
		// H d = -g, solve for d
		$H = array();
		$g = array();

		//double[] d = new double[nparm];

		$oos2 = array();

		for($i = 0; $i < $npts; ++$i) {
			$oos2[$i] = 1./($s[$i]*$s[$i]);
		}
		$iter = 0;
		$term = 0;	// termination count test

		do {
			++$iter;

			// hessian approximation
			for( $r = 0; $r < $nparm; ++$r) {
				for( $c = 0; $c < $nparm; ++$c) {
					for( $i = 0; $i < $npts; ++$i) {
						if ($i == 0) $H[$r][$c] = 0.;
						$xi = $x[$i];
						$H[$r][$c] += ($oos2[$i] * $f->grad($xi, $a, $r) * $f->grad($xi, $a, $c));
					}  //npts
				} //c
			} //r

			// boost diagonal towards gradient descent
			for( $r = 0; $r < $nparm; ++$r)
				$H[$r][$r] *= (1. + $lambda);

			// gradient
			for( $r = 0; $r < $nparm; ++$r) {
				for( $i = 0; $i < $npts; ++$i) {
					if ($i == 0) $g[$r] = 0.;
					$xi = $x[$i];
					$g[$r] += ($oos2[$i] * ($y[$i]-$f->val($xi,$a)) * $f->grad($xi, $a, $r));
				}
			} //npts

			// scale (for consistency with NR, not necessary)
			if ($false) {
				for( $r = 0; $r < $nparm; ++$r) {
					$g[$r] = -0.5 * $g[$r];
					for( $c = 0; $c < $nparm; ++$c) {
						$H[$r][$c] *= 0.5;
					}
				}
			}

			// solve H d = -g, evaluate error at new location
			//double[] d = DoubleMatrix.solve(H, g);
//			double[] d = (new Matrix(H)).lu().solve(new Matrix(g, nparm)).getRowPackedCopy();
			//double[] na = DoubleVector.add(a, d);
//			double[] na = (new Matrix(a, nparm)).plus(new Matrix(d, nparm)).getRowPackedCopy();
//			double e1 = chiSquared(x, na, y, s, f);

//			if (verbose > 0) {
//				System.out.println("\n\niteration "+iter+" lambda = "+lambda);
//				System.out.print("a = ");
//				(new Matrix(a, nparm)).print(10, 2);
//				if (verbose > 1) {
//					System.out.print("H = ");
//					(new Matrix(H)).print(10, 2);
//					System.out.print("g = ");
//					(new Matrix(g, nparm)).print(10, 2);
//					System.out.print("d = ");
//					(new Matrix(d, nparm)).print(10, 2);
//				}
//				System.out.print("e0 = " + e0 + ": ");
//				System.out.print("moved from ");
//				(new Matrix(a, nparm)).print(10, 2);
//				System.out.print("e1 = " + e1 + ": ");
//				if (e1 < e0) {
//					System.out.print("to ");
//					(new Matrix(na, nparm)).print(10, 2);
//				} else {
//					System.out.println("move rejected");
//				}
//			}

			// termination test (slightly different than NR)
//			if (Math.abs(e1-e0) > termepsilon) {
//				term = 0;
//			} else {
//				term++;
//				if (term == 4) {
//					System.out.println("terminating after " + iter + " iterations");
//					done = true;
//				}
//			}
//			if (iter >= maxiter) done = true;

			// in the C++ version, found that changing this to e1 >= e0
			// was not a good idea.  See comment there.
			//
//			if (e1 > e0 || Double.isNaN(e1)) { // new location worse than before
//				lambda *= 10.;
//			} else {		// new location better, accept new parameters
//				lambda *= 0.1;
//				e0 = e1;
//				// simply assigning a = na will not get results copied back to caller
//				for( int i = 0; i < nparm; i++ ) {
//					if (vary[i]) a[i] = na[i];
//				}
//			}
		} while(!$done);

		return $lambda;
	}	//	function solve()

}	//	class LevenbergMarquardt
