<?php
/**
 * @package JAMA
 */

define('RAND_MAX', mt_getrandmax());
define('RAND_MIN', 0);

/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
	/**
	 * @ignore
	 */
	define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../../');
	require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
	PHPExcel_Autoloader::Register();
	PHPExcel_Shared_ZipStreamWrapper::register();
	// check mbstring.func_overload
	if (ini_get('mbstring.func_overload') & 2) {
		throw new Exception('Multibyte function overloading in PHP must be disabled for string functions (2).');
	}
}

require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/JAMA/utils/Error.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/JAMA/utils/Maths.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/JAMA/CholeskyDecomposition.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/JAMA/LUDecomposition.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/JAMA/QRDecomposition.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/JAMA/EigenvalueDecomposition.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/JAMA/SingularValueDecomposition.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/String.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Calculation/Functions.php';

/*
 *	Matrix class
 *
 *	@author Paul Meagher
 *	@author Michael Bommarito
 *	@author Lukasz Karapuda
 *	@author Bartek Matosiuk
 *	@version 1.8
 *	@license PHP v3.0
 *	@see http://math.nist.gov/javanumerics/jama/
 */
class Matrix {

	/**
	 *	Matrix storage
	 *
	 *	@var array
	 *	@access public
	 */
	public $A = array();

	/**
	 *	Matrix row dimension
	 *
	 *	@var int
	 *	@access private
	 */
	private $m;

	/**
	 *	Matrix column dimension
	 *
	 *	@var int
	 *	@access private
	 */
	private $n;


	/**
	 *	Polymorphic constructor
	 *
	 *	As PHP has no support for polymorphic constructors, we hack our own sort of polymorphism using func_num_args, func_get_arg, and gettype. In essence, we're just implementing a simple RTTI filter and calling the appropriate constructor.
	 */
	public function __construct() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$match = implode(",", array_map('gettype', $args));

			switch($match) {
				//Square matrix - n x n
				case 'integer':
						$this->m = $args[0];
						$this->n = $args[0];
						$this->A = array_fill(0, $this->m, array_fill(0, $this->n, 0));
						break;
				//Rectangular matrix - m x n
				case 'integer,integer':
						$this->m = $args[0];
						$this->n = $args[1];
						$this->A = array_fill(0, $this->m, array_fill(0, $this->n, 0));
						break;
				//Rectangular matrix constant-filled - m x n filled with c
				case 'integer,integer,integer':
						$this->m = $args[0];
						$this->n = $args[1];
						$this->A = array_fill(0, $this->m, array_fill(0, $this->n, $args[2]));
						break;
				//Rectangular matrix constant-filled - m x n filled with c
				case 'integer,integer,double':
						$this->m = $args[0];
						$this->n = $args[1];
						$this->A = array_fill(0, $this->m, array_fill(0, $this->n, $args[2]));
						break;
				//Rectangular matrix - m x n initialized from 2D array
				case 'array':
						$this->m = count($args[0]);
						$this->n = count($args[0][0]);
						$this->A = $args[0];
						break;
				//Rectangular matrix - m x n initialized from 2D array
				case 'array,integer,integer':
						$this->m = $args[1];
						$this->n = $args[2];
						$this->A = $args[0];
						break;
				//Rectangular matrix - m x n initialized from packed array
				case 'array,integer':
						$this->m = $args[1];
						if ($this->m != 0) {
							$this->n = count($args[0]) / $this->m;
						} else {
							$this->n = 0;
						}
						if (($this->m * $this->n) == count($args[0])) {
							for($i = 0; $i < $this->m; ++$i) {
								for($j = 0; $j < $this->n; ++$j) {
									$this->A[$i][$j] = $args[0][$i + $j * $this->m];
								}
							}
						} else {
							throw new Exception(JAMAError(ArrayLengthException));
						}
						break;
				default:
						throw new Exception(JAMAError(PolymorphicArgumentException));
						break;
			}
		} else {
			throw new Exception(JAMAError(PolymorphicArgumentException));
		}
	}	//	function __construct()


	/**
	 *	getArray
	 *
	 *	@return array Matrix array
	 */
	public function getArray() {
		return $this->A;
	}	//	function getArray()


	/**
	 *	getArrayCopy
	 *
	 *	@return array Matrix array copy
	 */
	public function getArrayCopy() {
		return $this->A;
	}	//	function getArrayCopy()


	/**
	 *	constructWithCopy
	 *	Construct a matrix from a copy of a 2-D array.
	 *
	 *	@param double A[][]		Two-dimensional array of doubles.
	 *	@exception	IllegalArgumentException All rows must have the same length
	 */
	public function constructWithCopy($A) {
		$this->m = count($A);
		$this->n = count($A[0]);
		$newCopyMatrix = new Matrix($this->m, $this->n);
		for ($i = 0; $i < $this->m; ++$i) {
			if (count($A[$i]) != $this->n) {
				throw new Exception(JAMAError(RowLengthException));
			}
			for ($j = 0; $j < $this->n; ++$j) {
				$newCopyMatrix->A[$i][$j] = $A[$i][$j];
			}
		}
		return $newCopyMatrix;
	}	//	function constructWithCopy()


	/**
	 *	getColumnPackedCopy
	 *
	 *	Get a column-packed array
	 *	@return array Column-packed matrix array
	 */
	public function getColumnPackedCopy() {
		$P = array();
		for($i = 0; $i < $this->m; ++$i) {
			for($j = 0; $j < $this->n; ++$j) {
				array_push($P, $this->A[$j][$i]);
			}
		}
		return $P;
	}	//	function getColumnPackedCopy()


	/**
	 *	getRowPackedCopy
	 *
	 *	Get a row-packed array
	 *	@return array Row-packed matrix array
	 */
	public function getRowPackedCopy() {
		$P = array();
		for($i = 0; $i < $this->m; ++$i) {
			for($j = 0; $j < $this->n; ++$j) {
				array_push($P, $this->A[$i][$j]);
			}
		}
		return $P;
	}	//	function getRowPackedCopy()


	/**
	 *	getRowDimension
	 *
	 *	@return int Row dimension
	 */
	public function getRowDimension() {
		return $this->m;
	}	//	function getRowDimension()


	/**
	 *	getColumnDimension
	 *
	 *	@return int Column dimension
	 */
	public function getColumnDimension() {
		return $this->n;
	}	//	function getColumnDimension()


	/**
	 *	get
	 *
	 *	Get the i,j-th element of the matrix.
	 *	@param int $i Row position
	 *	@param int $j Column position
	 *	@return mixed Element (int/float/double)
	 */
	public function get($i = null, $j = null) {
		return $this->A[$i][$j];
	}	//	function get()


	/**
	 *	getMatrix
	 *
	 *	Get a submatrix
	 *	@param int $i0 Initial row index
	 *	@param int $iF Final row index
	 *	@param int $j0 Initial column index
	 *	@param int $jF Final column index
	 *	@return Matrix Submatrix
	 */
	public function getMatrix() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$match = implode(",", array_map('gettype', $args));

			switch($match) {
				//A($i0...; $j0...)
				case 'integer,integer':
						list($i0, $j0) = $args;
						if ($i0 >= 0) { $m = $this->m - $i0; } else { throw new Exception(JAMAError(ArgumentBoundsException)); }
						if ($j0 >= 0) { $n = $this->n - $j0; } else { throw new Exception(JAMAError(ArgumentBoundsException)); }
						$R = new Matrix($m, $n);
						for($i = $i0; $i < $this->m; ++$i) {
							for($j = $j0; $j < $this->n; ++$j) {
								$R->set($i, $j, $this->A[$i][$j]);
							}
						}
						return $R;
						break;
				//A($i0...$iF; $j0...$jF)
				case 'integer,integer,integer,integer':
						list($i0, $iF, $j0, $jF) = $args;
						if (($iF > $i0) && ($this->m >= $iF) && ($i0 >= 0)) { $m = $iF - $i0; } else { throw new Exception(JAMAError(ArgumentBoundsException)); }
						if (($jF > $j0) && ($this->n >= $jF) && ($j0 >= 0)) { $n = $jF - $j0; } else { throw new Exception(JAMAError(ArgumentBoundsException)); }
						$R = new Matrix($m+1, $n+1);
						for($i = $i0; $i <= $iF; ++$i) {
							for($j = $j0; $j <= $jF; ++$j) {
								$R->set($i - $i0, $j - $j0, $this->A[$i][$j]);
							}
						}
						return $R;
						break;
				//$R = array of row indices; $C = array of column indices
				case 'array,array':
						list($RL, $CL) = $args;
						if (count($RL) > 0) { $m = count($RL); } else { throw new Exception(JAMAError(ArgumentBoundsException)); }
						if (count($CL) > 0) { $n = count($CL); } else { throw new Exception(JAMAError(ArgumentBoundsException)); }
						$R = new Matrix($m, $n);
						for($i = 0; $i < $m; ++$i) {
							for($j = 0; $j < $n; ++$j) {
								$R->set($i - $i0, $j - $j0, $this->A[$RL[$i]][$CL[$j]]);
							}
						}
						return $R;
						break;
				//$RL = array of row indices; $CL = array of column indices
				case 'array,array':
						list($RL, $CL) = $args;
						if (count($RL) > 0) { $m = count($RL); } else { throw new Exception(JAMAError(ArgumentBoundsException)); }
						if (count($CL) > 0) { $n = count($CL); } else { throw new Exception(JAMAError(ArgumentBoundsException)); }
						$R = new Matrix($m, $n);
						for($i = 0; $i < $m; ++$i) {
							for($j = 0; $j < $n; ++$j) {
								$R->set($i, $j, $this->A[$RL[$i]][$CL[$j]]);
							}
						}
						return $R;
						break;
				//A($i0...$iF); $CL = array of column indices
				case 'integer,integer,array':
						list($i0, $iF, $CL) = $args;
						if (($iF > $i0) && ($this->m >= $iF) && ($i0 >= 0)) { $m = $iF - $i0; } else { throw new Exception(JAMAError(ArgumentBoundsException)); }
						if (count($CL) > 0) { $n = count($CL); } else { throw new Exception(JAMAError(ArgumentBoundsException)); }
						$R = new Matrix($m, $n);
						for($i = $i0; $i < $iF; ++$i) {
							for($j = 0; $j < $n; ++$j) {
								$R->set($i - $i0, $j, $this->A[$RL[$i]][$j]);
							}
						}
						return $R;
						break;
				//$RL = array of row indices
				case 'array,integer,integer':
						list($RL, $j0, $jF) = $args;
						if (count($RL) > 0) { $m = count($RL); } else { throw new Exception(JAMAError(ArgumentBoundsException)); }
						if (($jF >= $j0) && ($this->n >= $jF) && ($j0 >= 0)) { $n = $jF - $j0; } else { throw new Exception(JAMAError(ArgumentBoundsException)); }
						$R = new Matrix($m, $n+1);
						for($i = 0; $i < $m; ++$i) {
							for($j = $j0; $j <= $jF; ++$j) {
								$R->set($i, $j - $j0, $this->A[$RL[$i]][$j]);
							}
						}
						return $R;
						break;
				default:
						throw new Exception(JAMAError(PolymorphicArgumentException));
						break;
			}
		} else {
			throw new Exception(JAMAError(PolymorphicArgumentException));
		}
	}	//	function getMatrix()


	/**
	 *	setMatrix
	 *
	 *	Set a submatrix
	 *	@param int $i0 Initial row index
	 *	@param int $j0 Initial column index
	 *	@param mixed $S Matrix/Array submatrix
	 *	($i0, $j0, $S) $S = Matrix
	 *	($i0, $j0, $S) $S = Array
	 */
	public function setMatrix() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$match = implode(",", array_map('gettype', $args));

			switch($match) {
				case 'integer,integer,object':
						if ($args[2] instanceof Matrix) { $M = $args[2]; } else { throw new Exception(JAMAError(ArgumentTypeException)); }
						if (($args[0] + $M->m) <= $this->m) { $i0 = $args[0]; } else { throw new Exception(JAMAError(ArgumentBoundsException)); }
						if (($args[1] + $M->n) <= $this->n) { $j0 = $args[1]; } else { throw new Exception(JAMAError(ArgumentBoundsException)); }
						for($i = $i0; $i < $i0 + $M->m; ++$i) {
							for($j = $j0; $j < $j0 + $M->n; ++$j) {
								$this->A[$i][$j] = $M->get($i - $i0, $j - $j0);
							}
						}
						break;
				case 'integer,integer,array':
						$M = new Matrix($args[2]);
						if (($args[0] + $M->m) <= $this->m) { $i0 = $args[0]; } else { throw new Exception(JAMAError(ArgumentBoundsException)); }
						if (($args[1] + $M->n) <= $this->n) { $j0 = $args[1]; } else { throw new Exception(JAMAError(ArgumentBoundsException)); }
						for($i = $i0; $i < $i0 + $M->m; ++$i) {
							for($j = $j0; $j < $j0 + $M->n; ++$j) {
								$this->A[$i][$j] = $M->get($i - $i0, $j - $j0);
							}
						}
						break;
				default:
						throw new Exception(JAMAError(PolymorphicArgumentException));
						break;
			}
		} else {
			throw new Exception(JAMAError(PolymorphicArgumentException));
		}
	}	//	function setMatrix()


	/**
	 *	checkMatrixDimensions
	 *
	 *	Is matrix B the same size?
	 *	@param Matrix $B Matrix B
	 *	@return boolean
	 */
	public function checkMatrixDimensions($B = null) {
		if ($B instanceof Matrix) {
			if (($this->m == $B->getRowDimension()) && ($this->n == $B->getColumnDimension())) {
				return true;
			} else {
				throw new Exception(JAMAError(MatrixDimensionException));
			}
		} else {
			throw new Exception(JAMAError(ArgumentTypeException));
		}
	}	//	function checkMatrixDimensions()



	/**
	 *	set
	 *
	 *	Set the i,j-th element of the matrix.
	 *	@param int $i Row position
	 *	@param int $j Column position
	 *	@param mixed $c Int/float/double value
	 *	@return mixed Element (int/float/double)
	 */
	public function set($i = null, $j = null, $c = null) {
		// Optimized set version just has this
		$this->A[$i][$j] = $c;
		/*
		if (is_int($i) && is_int($j) && is_numeric($c)) {
			if (($i < $this->m) && ($j < $this->n)) {
				$this->A[$i][$j] = $c;
			} else {
				echo "A[$i][$j] = $c<br />";
				throw new Exception(JAMAError(ArgumentBoundsException));
			}
		} else {
			throw new Exception(JAMAError(ArgumentTypeException));
		}
		*/
	}	//	function set()


	/**
	 *	identity
	 *
	 *	Generate an identity matrix.
	 *	@param int $m Row dimension
	 *	@param int $n Column dimension
	 *	@return Matrix Identity matrix
	 */
	public function identity($m = null, $n = null) {
		return $this->diagonal($m, $n, 1);
	}	//	function identity()


	/**
	 *	diagonal
	 *
	 *	Generate a diagonal matrix
	 *	@param int $m Row dimension
	 *	@param int $n Column dimension
	 *	@param mixed $c Diagonal value
	 *	@return Matrix Diagonal matrix
	 */
	public function diagonal($m = null, $n = null, $c = 1) {
		$R = new Matrix($m, $n);
		for($i = 0; $i < $m; ++$i) {
			$R->set($i, $i, $c);
		}
		return $R;
	}	//	function diagonal()


	/**
	 *	filled
	 *
	 *	Generate a filled matrix
	 *	@param int $m Row dimension
	 *	@param int $n Column dimension
	 *	@param int $c Fill constant
	 *	@return Matrix Filled matrix
	 */
	public function filled($m = null, $n = null, $c = 0) {
		if (is_int($m) && is_int($n) && is_numeric($c)) {
			$R = new Matrix($m, $n, $c);
			return $R;
		} else {
			throw new Exception(JAMAError(ArgumentTypeException));
		}
	}	//	function filled()

	/**
	 *	random
	 *
	 *	Generate a random matrix
	 *	@param int $m Row dimension
	 *	@param int $n Column dimension
	 *	@return Matrix Random matrix
	 */
	public function random($m = null, $n = null, $a = RAND_MIN, $b = RAND_MAX) {
		if (is_int($m) && is_int($n) && is_numeric($a) && is_numeric($b)) {
			$R = new Matrix($m, $n);
			for($i = 0; $i < $m; ++$i) {
				for($j = 0; $j < $n; ++$j) {
					$R->set($i, $j, mt_rand($a, $b));
				}
			}
			return $R;
		} else {
			throw new Exception(JAMAError(ArgumentTypeException));
		}
	}	//	function random()


	/**
	 *	packed
	 *
	 *	Alias for getRowPacked
	 *	@return array Packed array
	 */
	public function packed() {
		return $this->getRowPacked();
	}	//	function packed()


	/**
	 *	getMatrixByRow
	 *
	 *	Get a submatrix by row index/range
	 *	@param int $i0 Initial row index
	 *	@param int $iF Final row index
	 *	@return Matrix Submatrix
	 */
	public function getMatrixByRow($i0 = null, $iF = null) {
		if (is_int($i0)) {
			if (is_int($iF)) {
				return $this->getMatrix($i0, 0, $iF + 1, $this->n);
			} else {
				return $this->getMatrix($i0, 0, $i0 + 1, $this->n);
			}
		} else {
			throw new Exception(JAMAError(ArgumentTypeException));
		}
	}	//	function getMatrixByRow()


	/**
	 *	getMatrixByCol
	 *
	 *	Get a submatrix by column index/range
	 *	@param int $i0 Initial column index
	 *	@param int $iF Final column index
	 *	@return Matrix Submatrix
	 */
	public function getMatrixByCol($j0 = null, $jF = null) {
		if (is_int($j0)) {
			if (is_int($jF)) {
				return $this->getMatrix(0, $j0, $this->m, $jF + 1);
			} else {
				return $this->getMatrix(0, $j0, $this->m, $j0 + 1);
			}
		} else {
			throw new Exception(JAMAError(ArgumentTypeException));
		}
	}	//	function getMatrixByCol()


	/**
	 *	transpose
	 *
	 *	Tranpose matrix
	 *	@return Matrix Transposed matrix
	 */
	public function transpose() {
		$R = new Matrix($this->n, $this->m);
		for($i = 0; $i < $this->m; ++$i) {
			for($j = 0; $j < $this->n; ++$j) {
				$R->set($j, $i, $this->A[$i][$j]);
			}
		}
		return $R;
	}	//	function transpose()


	/**
	 *	norm1
	 *
	 *	One norm
	 *	@return float Maximum column sum
	 */
	public function norm1() {
		$r = 0;
		for($j = 0; $j < $this->n; ++$j) {
			$s = 0;
			for($i = 0; $i < $this->m; ++$i) {
				$s += abs($this->A[$i][$j]);
			}
			$r = ($r > $s) ? $r : $s;
		}
		return $r;
	}	//	function norm1()


	/**
	 *	norm2
	 *
	 *	Maximum singular value
	 *	@return float Maximum singular value
	 */
	public function norm2() {
	}	//	function norm2()


	/**
	 *	normInf
	 *
	 *	Infinite norm
	 *	@return float Maximum row sum
	 */
	public function normInf() {
		$r = 0;
		for($i = 0; $i < $this->m; ++$i) {
			$s = 0;
			for($j = 0; $j < $this->n; ++$j) {
				$s += abs($this->A[$i][$j]);
			}
			$r = ($r > $s) ? $r : $s;
		}
		return $r;
	}	//	function normInf()


	/**
	 *	normF
	 *
	 *	Frobenius norm
	 *	@return float Square root of the sum of all elements squared
	 */
	public function normF() {
		$f = 0;
		for ($i = 0; $i < $this->m; ++$i) {
			for ($j = 0; $j < $this->n; ++$j) {
				$f = hypo($f,$this->A[$i][$j]);
			}
		}
		return $f;
	}	//	function normF()


	/**
	 *	Matrix rank
	 *
	 *	@return effective numerical rank, obtained from SVD.
	 */
	public function rank () {
		$svd = new SingularValueDecomposition($this);
		return $svd->rank();
	}	//	function rank ()


	/**
	 *	Matrix condition (2 norm)
	 *
	 *	@return ratio of largest to smallest singular value.
	 */
	public function cond () {
		$svd = new SingularValueDecomposition($this);
		return $svd->cond();
	}	//	function cond ()


	/**
	 *	trace
	 *
	 *	Sum of diagonal elements
	 *	@return float Sum of diagonal elements
	 */
	public function trace() {
		$s = 0;
		$n = min($this->m, $this->n);
		for($i = 0; $i < $n; ++$i) {
			$s += $this->A[$i][$i];
		}
		return $s;
	}	//	function trace()


	/**
	 *	uminus
	 *
	 *	Unary minus matrix -A
	 *	@return Matrix Unary minus matrix
	 */
	public function uminus() {
	}	//	function uminus()


	/**
	 *	plus
	 *
	 *	A + B
	 *	@param mixed $B Matrix/Array
	 *	@return Matrix Sum
	 */
	public function plus() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$match = implode(",", array_map('gettype', $args));

			switch($match) {
				case 'object':
						if ($args[0] instanceof Matrix) { $M = $args[0]; } else { throw new Exception(JAMAError(ArgumentTypeException)); }
						break;
				case 'array':
						$M = new Matrix($args[0]);
						break;
				default:
						throw new Exception(JAMAError(PolymorphicArgumentException));
						break;
			}
			$this->checkMatrixDimensions($M);
			for($i = 0; $i < $this->m; ++$i) {
				for($j = 0; $j < $this->n; ++$j) {
					$M->set($i, $j, $M->get($i, $j) + $this->A[$i][$j]);
				}
			}
			return $M;
		} else {
			throw new Exception(JAMAError(PolymorphicArgumentException));
		}
	}	//	function plus()


	/**
	 *	plusEquals
	 *
	 *	A = A + B
	 *	@param mixed $B Matrix/Array
	 *	@return Matrix Sum
	 */
	public function plusEquals() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$match = implode(",", array_map('gettype', $args));

			switch($match) {
				case 'object':
						if ($args[0] instanceof Matrix) { $M = $args[0]; } else { throw new Exception(JAMAError(ArgumentTypeException)); }
						break;
				case 'array':
						$M = new Matrix($args[0]);
						break;
				default:
						throw new Exception(JAMAError(PolymorphicArgumentException));
						break;
			}
			$this->checkMatrixDimensions($M);
			for($i = 0; $i < $this->m; ++$i) {
				for($j = 0; $j < $this->n; ++$j) {
					$validValues = True;
					$value = $M->get($i, $j);
					if ((is_string($this->A[$i][$j])) && (strlen($this->A[$i][$j]) > 0) && (!is_numeric($this->A[$i][$j]))) {
						$this->A[$i][$j] = trim($this->A[$i][$j],'"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($this->A[$i][$j]);
					}
					if ((is_string($value)) && (strlen($value) > 0) && (!is_numeric($value))) {
						$value = trim($value,'"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($value);
					}
					if ($validValues) {
						$this->A[$i][$j] += $value;
					} else {
						$this->A[$i][$j] = PHPExcel_Calculation_Functions::NaN();
					}
				}
			}
			return $this;
		} else {
			throw new Exception(JAMAError(PolymorphicArgumentException));
		}
	}	//	function plusEquals()


	/**
	 *	minus
	 *
	 *	A - B
	 *	@param mixed $B Matrix/Array
	 *	@return Matrix Sum
	 */
	public function minus() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$match = implode(",", array_map('gettype', $args));

			switch($match) {
				case 'object':
						if ($args[0] instanceof Matrix) { $M = $args[0]; } else { throw new Exception(JAMAError(ArgumentTypeException)); }
						break;
				case 'array':
						$M = new Matrix($args[0]);
						break;
				default:
						throw new Exception(JAMAError(PolymorphicArgumentException));
						break;
			}
			$this->checkMatrixDimensions($M);
			for($i = 0; $i < $this->m; ++$i) {
				for($j = 0; $j < $this->n; ++$j) {
					$M->set($i, $j, $M->get($i, $j) - $this->A[$i][$j]);
				}
			}
			return $M;
		} else {
			throw new Exception(JAMAError(PolymorphicArgumentException));
		}
	}	//	function minus()


	/**
	 *	minusEquals
	 *
	 *	A = A - B
	 *	@param mixed $B Matrix/Array
	 *	@return Matrix Sum
	 */
	public function minusEquals() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$match = implode(",", array_map('gettype', $args));

			switch($match) {
				case 'object':
						if ($args[0] instanceof Matrix) { $M = $args[0]; } else { throw new Exception(JAMAError(ArgumentTypeException)); }
						break;
				case 'array':
						$M = new Matrix($args[0]);
						break;
				default:
						throw new Exception(JAMAError(PolymorphicArgumentException));
						break;
			}
			$this->checkMatrixDimensions($M);
			for($i = 0; $i < $this->m; ++$i) {
				for($j = 0; $j < $this->n; ++$j) {
					$validValues = True;
					$value = $M->get($i, $j);
					if ((is_string($this->A[$i][$j])) && (strlen($this->A[$i][$j]) > 0) && (!is_numeric($this->A[$i][$j]))) {
						$this->A[$i][$j] = trim($this->A[$i][$j],'"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($this->A[$i][$j]);
					}
					if ((is_string($value)) && (strlen($value) > 0) && (!is_numeric($value))) {
						$value = trim($value,'"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($value);
					}
					if ($validValues) {
						$this->A[$i][$j] -= $value;
					} else {
						$this->A[$i][$j] = PHPExcel_Calculation_Functions::NaN();
					}
				}
			}
			return $this;
		} else {
			throw new Exception(JAMAError(PolymorphicArgumentException));
		}
	}	//	function minusEquals()


	/**
	 *	arrayTimes
	 *
	 *	Element-by-element multiplication
	 *	Cij = Aij * Bij
	 *	@param mixed $B Matrix/Array
	 *	@return Matrix Matrix Cij
	 */
	public function arrayTimes() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$match = implode(",", array_map('gettype', $args));

			switch($match) {
				case 'object':
						if ($args[0] instanceof Matrix) { $M = $args[0]; } else { throw new Exception(JAMAError(ArgumentTypeException)); }
						break;
				case 'array':
						$M = new Matrix($args[0]);
						break;
				default:
						throw new Exception(JAMAError(PolymorphicArgumentException));
						break;
			}
			$this->checkMatrixDimensions($M);
			for($i = 0; $i < $this->m; ++$i) {
				for($j = 0; $j < $this->n; ++$j) {
					$M->set($i, $j, $M->get($i, $j) * $this->A[$i][$j]);
				}
			}
			return $M;
		} else {
			throw new Exception(JAMAError(PolymorphicArgumentException));
		}
	}	//	function arrayTimes()


	/**
	 *	arrayTimesEquals
	 *
	 *	Element-by-element multiplication
	 *	Aij = Aij * Bij
	 *	@param mixed $B Matrix/Array
	 *	@return Matrix Matrix Aij
	 */
	public function arrayTimesEquals() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$match = implode(",", array_map('gettype', $args));

			switch($match) {
				case 'object':
						if ($args[0] instanceof Matrix) { $M = $args[0]; } else { throw new Exception(JAMAError(ArgumentTypeException)); }
						break;
				case 'array':
						$M = new Matrix($args[0]);
						break;
				default:
						throw new Exception(JAMAError(PolymorphicArgumentException));
						break;
			}
			$this->checkMatrixDimensions($M);
			for($i = 0; $i < $this->m; ++$i) {
				for($j = 0; $j < $this->n; ++$j) {
					$validValues = True;
					$value = $M->get($i, $j);
					if ((is_string($this->A[$i][$j])) && (strlen($this->A[$i][$j]) > 0) && (!is_numeric($this->A[$i][$j]))) {
						$this->A[$i][$j] = trim($this->A[$i][$j],'"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($this->A[$i][$j]);
					}
					if ((is_string($value)) && (strlen($value) > 0) && (!is_numeric($value))) {
						$value = trim($value,'"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($value);
					}
					if ($validValues) {
						$this->A[$i][$j] *= $value;
					} else {
						$this->A[$i][$j] = PHPExcel_Calculation_Functions::NaN();
					}
				}
			}
			return $this;
		} else {
			throw new Exception(JAMAError(PolymorphicArgumentException));
		}
	}	//	function arrayTimesEquals()


	/**
	 *	arrayRightDivide
	 *
	 *	Element-by-element right division
	 *	A / B
	 *	@param Matrix $B Matrix B
	 *	@return Matrix Division result
	 */
	public function arrayRightDivide() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$match = implode(",", array_map('gettype', $args));

			switch($match) {
				case 'object':
						if ($args[0] instanceof Matrix) { $M = $args[0]; } else { throw new Exception(JAMAError(ArgumentTypeException)); }
						break;
				case 'array':
						$M = new Matrix($args[0]);
						break;
				default:
						throw new Exception(JAMAError(PolymorphicArgumentException));
						break;
			}
			$this->checkMatrixDimensions($M);
			for($i = 0; $i < $this->m; ++$i) {
				for($j = 0; $j < $this->n; ++$j) {
					$validValues = True;
					$value = $M->get($i, $j);
					if ((is_string($this->A[$i][$j])) && (strlen($this->A[$i][$j]) > 0) && (!is_numeric($this->A[$i][$j]))) {
						$this->A[$i][$j] = trim($this->A[$i][$j],'"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($this->A[$i][$j]);
					}
					if ((is_string($value)) && (strlen($value) > 0) && (!is_numeric($value))) {
						$value = trim($value,'"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($value);
					}
					if ($validValues) {
						if ($value == 0) {
							//	Trap for Divide by Zero error
							$M->set($i, $j, '#DIV/0!');
						} else {
							$M->set($i, $j, $this->A[$i][$j] / $value);
						}
					} else {
						$this->A[$i][$j] = PHPExcel_Calculation_Functions::NaN();
					}
				}
			}
			return $M;
		} else {
			throw new Exception(JAMAError(PolymorphicArgumentException));
		}
	}	//	function arrayRightDivide()


	/**
	 *	arrayRightDivideEquals
	 *
	 *	Element-by-element right division
	 *	Aij = Aij / Bij
	 *	@param mixed $B Matrix/Array
	 *	@return Matrix Matrix Aij
	 */
	public function arrayRightDivideEquals() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$match = implode(",", array_map('gettype', $args));

			switch($match) {
				case 'object':
						if ($args[0] instanceof Matrix) { $M = $args[0]; } else { throw new Exception(JAMAError(ArgumentTypeException)); }
						break;
				case 'array':
						$M = new Matrix($args[0]);
						break;
				default:
						throw new Exception(JAMAError(PolymorphicArgumentException));
						break;
			}
			$this->checkMatrixDimensions($M);
			for($i = 0; $i < $this->m; ++$i) {
				for($j = 0; $j < $this->n; ++$j) {
					$this->A[$i][$j] = $this->A[$i][$j] / $M->get($i, $j);
				}
			}
			return $M;
		} else {
			throw new Exception(JAMAError(PolymorphicArgumentException));
		}
	}	//	function arrayRightDivideEquals()


	/**
	 *	arrayLeftDivide
	 *
	 *	Element-by-element Left division
	 *	A / B
	 *	@param Matrix $B Matrix B
	 *	@return Matrix Division result
	 */
	public function arrayLeftDivide() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$match = implode(",", array_map('gettype', $args));

			switch($match) {
				case 'object':
						if ($args[0] instanceof Matrix) { $M = $args[0]; } else { throw new Exception(JAMAError(ArgumentTypeException)); }
						break;
				case 'array':
						$M = new Matrix($args[0]);
						break;
				default:
						throw new Exception(JAMAError(PolymorphicArgumentException));
						break;
			}
			$this->checkMatrixDimensions($M);
			for($i = 0; $i < $this->m; ++$i) {
				for($j = 0; $j < $this->n; ++$j) {
					$M->set($i, $j, $M->get($i, $j) / $this->A[$i][$j]);
				}
			}
			return $M;
		} else {
			throw new Exception(JAMAError(PolymorphicArgumentException));
		}
	}	//	function arrayLeftDivide()


	/**
	 *	arrayLeftDivideEquals
	 *
	 *	Element-by-element Left division
	 *	Aij = Aij / Bij
	 *	@param mixed $B Matrix/Array
	 *	@return Matrix Matrix Aij
	 */
	public function arrayLeftDivideEquals() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$match = implode(",", array_map('gettype', $args));

			switch($match) {
				case 'object':
						if ($args[0] instanceof Matrix) { $M = $args[0]; } else { throw new Exception(JAMAError(ArgumentTypeException)); }
						break;
				case 'array':
						$M = new Matrix($args[0]);
						break;
				default:
						throw new Exception(JAMAError(PolymorphicArgumentException));
						break;
			}
			$this->checkMatrixDimensions($M);
			for($i = 0; $i < $this->m; ++$i) {
				for($j = 0; $j < $this->n; ++$j) {
					$this->A[$i][$j] = $M->get($i, $j) / $this->A[$i][$j];
				}
			}
			return $M;
		} else {
			throw new Exception(JAMAError(PolymorphicArgumentException));
		}
	}	//	function arrayLeftDivideEquals()


	/**
	 *	times
	 *
	 *	Matrix multiplication
	 *	@param mixed $n Matrix/Array/Scalar
	 *	@return Matrix Product
	 */
	public function times() {
		if (func_num_args() > 0) {
			$args  = func_get_args();
			$match = implode(",", array_map('gettype', $args));

			switch($match) {
				case 'object':
						if ($args[0] instanceof Matrix) { $B = $args[0]; } else { throw new Exception(JAMAError(ArgumentTypeException)); }
						if ($this->n == $B->m) {
							$C = new Matrix($this->m, $B->n);
							for($j = 0; $j < $B->n; ++$j) {
								for ($k = 0; $k < $this->n; ++$k) {
									$Bcolj[$k] = $B->A[$k][$j];
								}
								for($i = 0; $i < $this->m; ++$i) {
									$Arowi = $this->A[$i];
									$s = 0;
									for($k = 0; $k < $this->n; ++$k) {
										$s += $Arowi[$k] * $Bcolj[$k];
									}
									$C->A[$i][$j] = $s;
								}
							}
							return $C;
						} else {
							throw new Exception(JAMAError(MatrixDimensionMismatch));
						}
						break;
				case 'array':
						$B = new Matrix($args[0]);
						if ($this->n == $B->m) {
							$C = new Matrix($this->m, $B->n);
							for($i = 0; $i < $C->m; ++$i) {
								for($j = 0; $j < $C->n; ++$j) {
									$s = "0";
									for($k = 0; $k < $C->n; ++$k) {
										$s += $this->A[$i][$k] * $B->A[$k][$j];
									}
									$C->A[$i][$j] = $s;
								}
							}
							return $C;
						} else {
							throw new Exception(JAMAError(MatrixDimensionMismatch));
						}
						return $M;
						break;
				case 'integer':
						$C = new Matrix($this->A);
						for($i = 0; $i < $C->m; ++$i) {
							for($j = 0; $j < $C->n; ++$j) {
								$C->A[$i][$j] *= $args[0];
							}
						}
						return $C;
						break;
				case 'double':
						$C = new Matrix($this->m, $this->n);
						for($i = 0; $i < $C->m; ++$i) {
							for($j = 0; $j < $C->n; ++$j) {
								$C->A[$i][$j] = $args[0] * $this->A[$i][$j];
							}
						}
						return $C;
						break;
				case 'float':
						$C = new Matrix($this->A);
						for($i = 0; $i < $C->m; ++$i) {
							for($j = 0; $j < $C->n; ++$j) {
								$C->A[$i][$j] *= $args[0];
							}
						}
						return $C;
						break;
				default:
						throw new Exception(JAMAError(PolymorphicArgumentException));
						break;
			}
		} else {
			throw new Exception(PolymorphicArgumentException);
		}
	}	//	function times()


	/**
	 *	power
	 *
	 *	A = A ^ B
	 *	@param mixed $B Matrix/Array
	 *	@return Matrix Sum
	 */
	public function power() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$match = implode(",", array_map('gettype', $args));

			switch($match) {
				case 'object':
						if ($args[0] instanceof Matrix) { $M = $args[0]; } else { throw new Exception(JAMAError(ArgumentTypeException)); }
						break;
				case 'array':
						$M = new Matrix($args[0]);
						break;
				default:
						throw new Exception(JAMAError(PolymorphicArgumentException));
						break;
			}
			$this->checkMatrixDimensions($M);
			for($i = 0; $i < $this->m; ++$i) {
				for($j = 0; $j < $this->n; ++$j) {
					$validValues = True;
					$value = $M->get($i, $j);
					if ((is_string($this->A[$i][$j])) && (strlen($this->A[$i][$j]) > 0) && (!is_numeric($this->A[$i][$j]))) {
						$this->A[$i][$j] = trim($this->A[$i][$j],'"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($this->A[$i][$j]);
					}
					if ((is_string($value)) && (strlen($value) > 0) && (!is_numeric($value))) {
						$value = trim($value,'"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($value);
					}
					if ($validValues) {
						$this->A[$i][$j] = pow($this->A[$i][$j],$value);
					} else {
						$this->A[$i][$j] = PHPExcel_Calculation_Functions::NaN();
					}
				}
			}
			return $this;
		} else {
			throw new Exception(JAMAError(PolymorphicArgumentException));
		}
	}	//	function power()


	/**
	 *	concat
	 *
	 *	A = A & B
	 *	@param mixed $B Matrix/Array
	 *	@return Matrix Sum
	 */
	public function concat() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$match = implode(",", array_map('gettype', $args));

			switch($match) {
				case 'object':
						if ($args[0] instanceof Matrix) { $M = $args[0]; } else { throw new Exception(JAMAError(ArgumentTypeException)); }
				case 'array':
						$M = new Matrix($args[0]);
						break;
				default:
						throw new Exception(JAMAError(PolymorphicArgumentException));
						break;
			}
			$this->checkMatrixDimensions($M);
			for($i = 0; $i < $this->m; ++$i) {
				for($j = 0; $j < $this->n; ++$j) {
//					$this->A[$i][$j] = '"'.trim($this->A[$i][$j],'"').trim($M->get($i, $j),'"').'"';
					$this->A[$i][$j] = trim($this->A[$i][$j],'"').trim($M->get($i, $j),'"');
				}
			}
			return $this;
		} else {
			throw new Exception(JAMAError(PolymorphicArgumentException));
		}
	}	//	function concat()


	/**
	 *	chol
	 *
	 *	Cholesky decomposition
	 *	@return Matrix Cholesky decomposition
	 */
	public function chol() {
		return new CholeskyDecomposition($this);
	}	//	function chol()


	/**
	 *	lu
	 *
	 *	LU decomposition
	 *	@return Matrix LU decomposition
	 */
	public function lu() {
		return new LUDecomposition($this);
	}	//	function lu()


	/**
	 *	qr
	 *
	 *	QR decomposition
	 *	@return Matrix QR decomposition
	 */
	public function qr() {
		return new QRDecomposition($this);
	}	//	function qr()


	/**
	 *	eig
	 *
	 *	Eigenvalue decomposition
	 *	@return Matrix Eigenvalue decomposition
	 */
	public function eig() {
		return new EigenvalueDecomposition($this);
	}	//	function eig()


	/**
	 *	svd
	 *
	 *	Singular value decomposition
	 *	@return Singular value decomposition
	 */
	public function svd() {
		return new SingularValueDecomposition($this);
	}	//	function svd()


	/**
	 *	Solve A*X = B.
	 *
	 *	@param Matrix $B Right hand side
	 *	@return Matrix ... Solution if A is square, least squares solution otherwise
	 */
	public function solve($B) {
		if ($this->m == $this->n) {
			$LU = new LUDecomposition($this);
			return $LU->solve($B);
		} else {
			$QR = new QRDecomposition($this);
			return $QR->solve($B);
		}
	}	//	function solve()


	/**
	 *	Matrix inverse or pseudoinverse.
	 *
	 *	@return Matrix ... Inverse(A) if A is square, pseudoinverse otherwise.
	 */
	public function inverse() {
		return $this->solve($this->identity($this->m, $this->m));
	}	//	function inverse()


	/**
	 *	det
	 *
	 *	Calculate determinant
	 *	@return float Determinant
	 */
	public function det() {
		$L = new LUDecomposition($this);
		return $L->det();
	}	//	function det()


	/**
	 *	Older debugging utility for backwards compatability.
	 *
	 *	@return html version of matrix
	 */
	public function mprint($A, $format="%01.2f", $width=2) {
		$m = count($A);
		$n = count($A[0]);
		$spacing = str_repeat('&nbsp;',$width);

		for ($i = 0; $i < $m; ++$i) {
			for ($j = 0; $j < $n; ++$j) {
				$formatted = sprintf($format, $A[$i][$j]);
				echo $formatted.$spacing;
			}
			echo "<br />";
		}
	}	//	function mprint()


	/**
	 *	Debugging utility.
	 *
	 *	@return Output HTML representation of matrix
	 */
	public function toHTML($width=2) {
		print('<table style="background-color:#eee;">');
		for($i = 0; $i < $this->m; ++$i) {
			print('<tr>');
			for($j = 0; $j < $this->n; ++$j) {
				print('<td style="background-color:#fff;border:1px solid #000;padding:2px;text-align:center;vertical-align:middle;">' . $this->A[$i][$j] . '</td>');
			}
			print('</tr>');
		}
		print('</table>');
	}	//	function toHTML()

}	//	class Matrix
