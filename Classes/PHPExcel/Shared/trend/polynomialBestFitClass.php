<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2011 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared_Best_Fit
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/trend/bestFitClass.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/JAMA/Matrix.php';


/**
 * PHPExcel_Polynomial_Best_Fit
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared_Best_Fit
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Polynomial_Best_Fit extends PHPExcel_Best_Fit
{
	protected $_bestFitType		= 'polynomial';

	protected $_order			= 0;


	public function getOrder() {
		return $this->_order;
	}	//	function getOrder()


	public function getValueOfYForX($xValue) {
		$retVal = $this->getIntersect();
		$slope = $this->getSlope();
		foreach($slope as $key => $value) {
			if ($value != 0.0) {
				$retVal += $value * pow($xValue, $key + 1);
			}
		}
		return $retVal;
	}	//	function getValueOfYForX()


	public function getValueOfXForY($yValue) {
		return ($yValue - $this->getIntersect()) / $this->getSlope();
	}	//	function getValueOfXForY()


	public function getEquation($dp=0) {
		$slope = $this->getSlope($dp);
		$intersect = $this->getIntersect($dp);

		$equation = 'Y = '.$intersect;
		foreach($slope as $key => $value) {
			if ($value != 0.0) {
				$equation .= ' + '.$value.' * X';
				if ($key > 0) {
					$equation .= '^'.($key + 1);
				}
			}
		}
		return $equation;
	}	//	function getEquation()


	public function getSlope($dp=0) {
		if ($dp != 0) {
			$coefficients = array();
			foreach($this->_slope as $coefficient) {
				$coefficients[] = round($coefficient,$dp);
			}
			return $coefficients;
		}
		return $this->_slope;
	}	//	function getSlope()


	public function getCoefficients($dp=0) {
		return array_merge(array($this->getIntersect($dp)),$this->getSlope($dp));
	}	//	function getCoefficients()


	private function _polynomial_regression($order, $yValues, $xValues, $const) {
		// calculate sums
		$x_sum = array_sum($xValues);
		$y_sum = array_sum($yValues);
		$xx_sum = $xy_sum = 0;
		for($i = 0; $i < $this->_valueCount; ++$i) {
			$xy_sum += $xValues[$i] * $yValues[$i];
			$xx_sum += $xValues[$i] * $xValues[$i];
			$yy_sum += $yValues[$i] * $yValues[$i];
		}
		/*
		 *	This routine uses logic from the PHP port of polyfit version 0.1
		 *	written by Michael Bommarito and Paul Meagher
		 *
		 *	The function fits a polynomial function of order $order through
		 *	a series of x-y data points using least squares.
		 *
		 */
		for ($i = 0; $i < $this->_valueCount; ++$i) {
			for ($j = 0; $j <= $order; ++$j) {
				$A[$i][$j] = pow($xValues[$i], $j);
			}
		}
		for ($i=0; $i < $this->_valueCount; ++$i) {
			$B[$i] = array($yValues[$i]);
		}
		$matrixA = new Matrix($A);
		$matrixB = new Matrix($B);
		$C = $matrixA->solve($matrixB);

		$coefficients = array();
		for($i = 0; $i < $C->m; ++$i) {
			$r = $C->get($i, 0);
			if (abs($r) <= pow(10, -9)) {
				$r = 0;
			}
			$coefficients[] = $r;
		}

		$this->_intersect = array_shift($coefficients);
		$this->_slope = $coefficients;

		$this->_calculateGoodnessOfFit($x_sum,$y_sum,$xx_sum,$yy_sum,$xy_sum);
		foreach($this->_xValues as $xKey => $xValue) {
			$this->_yBestFitValues[$xKey] = $this->getValueOfYForX($xValue);
		}
	}	//	function _polynomial_regression()


	function __construct($order, $yValues, $xValues=array(), $const=True) {
		if (parent::__construct($yValues, $xValues) !== False) {
			if ($order < $this->_valueCount) {
				$this->_bestFitType .= '_'.$order;
				$this->_order = $order;
				$this->_polynomial_regression($order, $yValues, $xValues, $const);
				if (($this->getGoodnessOfFit() < 0.0) || ($this->getGoodnessOfFit() > 1.0)) {
					$this->_error = True;
				}
			} else {
				$this->_error = True;
			}
		}
	}	//	function __construct()

}	//	class polynomialBestFit