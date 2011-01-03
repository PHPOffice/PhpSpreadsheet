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


require_once(PHPEXCEL_ROOT . 'PHPExcel/Shared/trend/bestFitClass.php');


/**
 * PHPExcel_Exponential_Best_Fit
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared_Best_Fit
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Exponential_Best_Fit extends PHPExcel_Best_Fit
{
	protected $_bestFitType		= 'exponential';


	public function getValueOfYForX($xValue) {
		return $this->getIntersect() * pow($this->getSlope(),($xValue - $this->_Xoffset));
	}	//	function getValueOfYForX()


	public function getValueOfXForY($yValue) {
		return log(($yValue + $this->_Yoffset) / $this->getIntersect()) / log($this->getSlope());
	}	//	function getValueOfXForY()


	public function getEquation($dp=0) {
		$slope = $this->getSlope($dp);
		$intersect = $this->getIntersect($dp);

		return 'Y = '.$intersect.' * '.$slope.'^X';
	}	//	function getEquation()


	public function getSlope($dp=0) {
		if ($dp != 0) {
			return round(exp($this->_slope),$dp);
		}
		return exp($this->_slope);
	}	//	function getSlope()


	public function getIntersect($dp=0) {
		if ($dp != 0) {
			return round(exp($this->_intersect),$dp);
		}
		return exp($this->_intersect);
	}	//	function getIntersect()


	private function _exponential_regression($yValues, $xValues, $const) {
		foreach($yValues as &$value) {
			if ($value < 0.0) {
				$value = 0 - log(abs($value));
			} elseif ($value > 0.0) {
				$value = log($value);
			}
		}
		unset($value);

		$this->_leastSquareFit($yValues, $xValues, $const);
	}	//	function _exponential_regression()


	function __construct($yValues, $xValues=array(), $const=True) {
		if (parent::__construct($yValues, $xValues) !== False) {
			$this->_exponential_regression($yValues, $xValues, $const);
		}
	}	//	function __construct()

}	//	class exponentialBestFit