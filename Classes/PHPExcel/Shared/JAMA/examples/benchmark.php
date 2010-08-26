<?php

error_reporting(E_ALL);

/**
 * @package JAMA
 */

require_once '../Matrix.php';
require_once 'Stats.php';


/**
 * Example of use of Matrix Class, featuring magic squares.
 */
class Benchmark {
	public $stat;


	/**
	 * Simple function to replicate PHP 5 behaviour
	 */
	function microtime_float() {
		list($usec, $sec) = explode(" ", microtime());

		return ((float)$usec + (float)$sec);
	}	//	function microtime_float()


	function displayStats($times = null) {
		$this->stat->setData($times);
		$stats = $this->stat->calcFull();

		echo '<table style="margin-left:32px;">';
		echo '<tr><td style="text-align:right;"><b>n:</b><td style="text-align:right;">' . $stats['count'] . ' </td></tr>';
		echo '<tr><td style="text-align:right;"><b>Mean:</b><td style="text-align:right;">' . $stats['mean'] . ' </td></tr>';
		echo '<tr><td style="text-align:right;"><b>Min.:</b><td style="text-align:right;">' . $stats['min'] . ' </td></tr>';
		echo '<tr><td style="text-align:right;"><b>Max.:</b><td style="text-align:right;">' . $stats['max'] . ' </td></tr>';
		echo '<tr><td style="text-align:right;"><b>&sigma;:</b><td style="text-align:right;">' . $stats['stdev'] . ' </td></tr>';
		echo '<tr><td style="text-align:right;"><b>Variance:</b><td style="text-align:right;">' . $stats['variance'] . ' </td></tr>';
		echo '<tr><td style="text-align:right;"><b>Range:</b><td style="text-align:right;">' . $stats['range'] . ' </td></tr>';
		echo '</table>';

		return $stats;
	}	//	function displayStats()


	function runEig($n = 4, $t = 100) {
		$times = array();

		for ($i = 0; $i < $t; ++$i) {
			$M = Matrix::random($n, $n);
			$start_time = $this->microtime_float();
			$E = new EigenvalueDecomposition($M);
			$stop_time = $this->microtime_float();
			$times[] = $stop_time - $start_time;
		}

		return $times;
	}	//	function runEig()


	function runLU($n = 4, $t = 100) {
		$times = array();

		for ($i = 0; $i < $t; ++$i) {
			$M = Matrix::random($n, $n);
			$start_time = $this->microtime_float();
			$E = new LUDecomposition($M);
			$stop_time = $this->microtime_float();
			$times[] = $stop_time - $start_time;
		}

		return $times;
	}	//	function runLU()


	function runQR($n = 4, $t = 100) {
		$times = array();

		for ($i = 0; $i < $t; ++$i) {
			$M = Matrix::random($n, $n);
			$start_time = $this->microtime_float();
			$E = new QRDecomposition($M);
			$stop_time = $this->microtime_float();
			$times[] = $stop_time - $start_time;
		}

		return $times;
	}	//	function runQR()


	function runCholesky($n = 4, $t = 100) {
		$times = array();

		for ($i = 0; $i < $t; ++$i) {
			$M = Matrix::random($n, $n);
			$start_time = $this->microtime_float();
			$E = new CholeskyDecomposition($M);
			$stop_time = $this->microtime_float();
			$times[] = $stop_time - $start_time;
		}

		return $times;
	}	//	function runCholesky()


	function runSVD($n = 4, $t = 100) {
		$times = array();

		for ($i = 0; $i < $t; ++$i) {
			$M = Matrix::random($n, $n);
			$start_time = $this->microtime_float();
			$E = new SingularValueDecomposition($M);
			$stop_time = $this->microtime_float();
			$times[] = $stop_time - $start_time;
		}

		return $times;
	}	//	function runSVD()


	function run() {
		$n = 8;
		$t = 16;
		$sum = 0;
		echo "<b>Cholesky decomposition: $t random {$n}x{$n} matrices</b><br />";
		$r = $this->displayStats($this->runCholesky($n, $t));
		$sum += $r['mean'] * $n;

		echo '<hr />';

		echo "<b>Eigenvalue decomposition: $t random {$n}x{$n} matrices</b><br />";
		$r = $this->displayStats($this->runEig($n, $t));
		$sum += $r['mean'] * $n;

		echo '<hr />';

		echo "<b>LU decomposition: $t random {$n}x{$n} matrices</b><br />";
		$r = $this->displayStats($this->runLU($n, $t));
		$sum += $r['mean'] * $n;

		echo '<hr />';

		echo "<b>QR decomposition: $t random {$n}x{$n} matrices</b><br />";
		$r = $this->displayStats($this->runQR($n, $t));
		$sum += $r['mean'] * $n;

		echo '<hr />';

		echo "<b>Singular Value decomposition: $t random {$n}x{$n} matrices</b><br />";
		$r = $this->displayStats($this->runSVD($n, $t));
		$sum += $r['mean'] * $n;

		return $sum;
	}	//	function run()


	public function __construct() {
		$this->stat = new Base();
	}	//	function Benchmark()

}  // class Benchmark		(end MagicSquareExample)


$benchmark = new Benchmark();

switch($_REQUEST['decomposition']) {
	case 'cholesky':
		$m = array();
		for ($i = 2; $i <= 8; $i *= 2) {
			$t = 32 / $i;
			echo "<b>Cholesky decomposition: $t random {$i}x{$i} matrices</b><br />";
			$s = $benchmark->displayStats($benchmark->runCholesky($i, $t));
			$m[$i] = $s['mean'];
			echo "<br />";
		}
		echo '<pre>';
		foreach($m as $x => $y) {
			echo "$x\t" . 1000*$y . "\n";
		}
		echo '</pre>';
		break;
	case 'eigenvalue':
		$m = array();
		for ($i = 2; $i <= 8; $i *= 2) {
			$t = 32 / $i;
			echo "<b>Eigenvalue decomposition: $t random {$i}x{$i} matrices</b><br />";
			$s = $benchmark->displayStats($benchmark->runEig($i, $t));
			$m[$i] = $s['mean'];
			echo "<br />";
		}
		echo '<pre>';
		foreach($m as $x => $y) {
			echo "$x\t" . 1000*$y . "\n";
		}
		echo '</pre>';
		break;
	case 'lu':
		$m = array();
		for ($i = 2; $i <= 8; $i *= 2) {
			$t = 32 / $i;
			echo "<b>LU decomposition: $t random {$i}x{$i} matrices</b><br />";
			$s = $benchmark->displayStats($benchmark->runLU($i, $t));
			$m[$i] = $s['mean'];
			echo "<br />";
		}
		echo '<pre>';
		foreach($m as $x => $y) {
			echo "$x\t" . 1000*$y . "\n";
		}
		echo '</pre>';
		break;
	case 'qr':
		$m = array();
		for ($i = 2; $i <= 8; $i *= 2) {
			$t = 32 / $i;
			echo "<b>QR decomposition: $t random {$i}x{$i} matrices</b><br />";
			$s = $benchmark->displayStats($benchmark->runQR($i, $t));
			$m[$i] = $s['mean'];
			echo "<br />";
		}
		echo '<pre>';
		foreach($m as $x => $y) {
			echo "$x\t" . 1000*$y . "\n";
		}
		echo '</pre>';
		break;
	case 'svd':
		$m = array();
		for($i = 2; $i <= 8; $i *= 2) {
			$t = 32 / $i;
			echo "<b>Singular value decomposition: $t random {$i}x{$i} matrices</b><br />";
			$s = $benchmark->displayStats($benchmark->runSVD($i, $t));
			$m[$i] = $s['mean'];
			echo "<br />";
		}
		echo '<pre>';
		foreach($m as $x => $y) {
			echo "$x\t" . 1000*$y . "\n";
		}
		echo '</pre>';
		break;
	case 'all':
		$s = $benchmark->run();
		print("<br /><b>Total<b>: {$s}s<br />");
		break;
	default:
		?>
		<ul>
			<li><a href="benchmark.php?decomposition=all">Complete Benchmark</a>
				<ul>
					<li><a href="benchmark.php?decomposition=cholesky">Cholesky</a></li>
					<li><a href="benchmark.php?decomposition=eigenvalue">Eigenvalue</a></li>
					<li><a href="benchmark.php?decomposition=lu">LU</a></li>
					<li><a href="benchmark.php?decomposition=qr">QR</a></li>
					<li><a href="benchmark.php?decomposition=svd">Singular Value</a></li>
				</ul>
			</li>
		</ul>
		<?php
		break;
}
