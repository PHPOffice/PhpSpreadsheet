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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category	PHPExcel
 * @package		PHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license		http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version		##VERSION##, ##DATE##
 */


/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
	/**
	 * @ignore
	 */
	define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
	require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}


/**
 * PHPExcel_Calculation_Database
 *
 * @category	PHPExcel
 * @package		PHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Calculation_Database {


	private static function __fieldExtract($database,$field) {
		$field = strtoupper(PHPExcel_Calculation_Functions::flattenSingleValue($field));
		$fieldNames = array_map('strtoupper',array_shift($database));

		if (is_numeric($field)) {
			$keys = array_keys($fieldNames);
			return $keys[$field-1];
		}
		$key = array_search($field,$fieldNames);
		return ($key) ? $key : null;
	}

	private static function __filter($database,$criteria) {
		$fieldNames = array_shift($database);
		$criteriaNames = array_shift($criteria);

		//	Convert the criteria into a set of AND/OR conditions with [:placeholders]
		$testConditions = $testValues = array();
		$testConditionsCount = 0;
		foreach($criteriaNames as $key => $criteriaName) {
			$testCondition = array();
			$testConditionCount = 0;
			foreach($criteria as $row => $criterion) {
				if ($criterion[$key] > '') {
					$testCondition[] = '[:'.$criteriaName.']'.PHPExcel_Calculation_Functions::_ifCondition($criterion[$key]);
					$testConditionCount++;
				}
			}
			if ($testConditionCount > 1) {
				$testConditions[] = 'OR('.implode(',',$testCondition).')';
				$testConditionsCount++;
			} elseif($testConditionCount == 1) {
				$testConditions[] = $testCondition[0];
				$testConditionsCount++;
			}
		}
		if ($testConditionsCount > 1) {
			$testConditionSet = 'AND('.implode(',',$testConditions).')';
		} elseif($testConditionsCount == 1) {
			$testConditionSet = $testConditions[0];
		}

		//	Loop through each row of the database
		foreach($database as $dataRow => $dataValues) {
			//	Substitute actual values from the database row for our [:placeholders]
			$testConditionList = $testConditionSet;
			foreach($criteriaNames as $key => $criteriaName) {
				$k = array_search($criteriaName,$fieldNames);
				if (isset($dataValues[$k])) {
					$dataValue = $dataValues[$k];
					$dataValue = (is_string($dataValue)) ? PHPExcel_Calculation::_wrapResult(strtoupper($dataValue)) : $dataValue;
					$testConditionList = str_replace('[:'.$criteriaName.']',$dataValue,$testConditionList);
				}
			}
			//	evaluate the criteria against the row data
			$result = PHPExcel_Calculation::getInstance()->_calculateFormulaValue('='.$testConditionList);
			//	If the row failed to meet the criteria, remove it from the database
			if (!$result) {
				unset($database[$dataRow]);
			}
		}

		return $database;
	}


	/**
	 *	DAVERAGE
	 *
	 */
	public static function DAVERAGE($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::AVERAGE($colData);
	}	//	function DAVERAGE()

	/**
	 *	DCOUNT
	 *
	 */
	public static function DCOUNT($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::COUNT($colData);
	}	//	function DCOUNT()

	/**
	 *	DCOUNTA
	 *
	 */
	public static function DCOUNTA($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::COUNTA($colData);
	}	//	function DCOUNTA()

	/**
	 *	DGET
	 *
	 */
	public static function DGET($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		if (count($colData) > 1) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		return $colData[0];
	}	//	function DGET()

	/**
	 *	DMAX
	 *
	 */
	public static function DMAX($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::MAX($colData);
	}	//	function DMAX()

	/**
	 *	DMIN
	 *
	 */
	public static function DMIN($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::MIN($colData);
	}	//	function DMIN()

	/**
	 *	DPRODUCT
	 *
	 */
	public static function DPRODUCT($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_MathTrig::PRODUCT($colData);
	}	//	function DPRODUCT()

	/**
	 *	DSTDEV
	 *
	 */
	public static function DSTDEV($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::STDEV($colData);
	}	//	function DSTDEV()

	/**
	 *	DSTDEVP
	 *
	 */
	public static function DSTDEVP($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::STDEVP($colData);
	}	//	function DSTDEVP()

	/**
	 *	DSUM
	 *
	 */
	public static function DSUM($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_MathTrig::SUM($colData);
	}	//	function DSUM()

	/**
	 *	DVAR
	 *
	 */
	public static function DVAR($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::VARFunc($colData);
	}	//	function DVAR()

	/**
	 *	DVARP
	 *
	 */
	public static function DVARP($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::VARP($colData);
	}	//	function DVARP()


}	//	class PHPExcel_Calculation_Database
