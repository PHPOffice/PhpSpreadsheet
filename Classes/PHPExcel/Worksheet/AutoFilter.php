<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2012 PHPExcel
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
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Worksheet_AutoFilter
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_AutoFilter
{
	/**
	 * Autofilter Worksheet
	 *
	 * @var PHPExcel_Worksheet
	 */
	private $_workSheet = NULL;


	/**
	 * Autofilter Range
	 *
	 * @var string
	 */
	private $_range = '';


	/**
	 * Autofilter Column Ruleset
	 *
	 * @var array of PHPExcel_Worksheet_AutoFilter_Column
	 */
	private $_columns = array();


    /**
     * Create a new PHPExcel_Worksheet_AutoFilter
     */
    public function __construct($pRange = '', PHPExcel_Worksheet $pSheet = NULL)
    {
		$this->_range = $pRange;
		$this->_workSheet = $pSheet;
    }

	/**
	 * Get AutoFilter Parent Worksheet
	 *
	 * @return PHPExcel_Worksheet
	 */
	public function getParent() {
		return $this->_workSheet;
	}

	/**
	 * Set AutoFilter Parent Worksheet
	 *
	 * @param PHPExcel_Worksheet
	 * @return PHPExcel_Worksheet_AutoFilter
	 */
	public function setParent(PHPExcel_Worksheet $pSheet = NULL) {
		$this->_workSheet = $pSheet;

		return $this;
	}

	/**
	 * Get AutoFilter Range
	 *
	 * @return string
	 */
	public function getRange() {
		return $this->_range;
	}

	/**
	 *	Set AutoFilter Range
	 *
	 *	@param	string		$pRange		Cell range (i.e. A1:E10)
	 *	@throws	PHPExcel_Exception
	 *	@return PHPExcel_Worksheet_AutoFilter
	 */
	public function setRange($pRange = '') {
		// Uppercase coordinate
		$cellAddress = explode('!',strtoupper($pRange));
		if (count($cellAddress) > 1) {
			list($worksheet,$pRange) = $cellAddress;
		}

		if (strpos($pRange,':') !== FALSE) {
			$this->_range = $pRange;
		} elseif(empty($pRange)) {
			$this->_range = '';
		} else {
			throw new PHPExcel_Exception('Autofilter must be set on a range of cells.');
		}

		if (empty($pRange)) {
			//	Discard all column rules
			$this->_columns = array();
		} else {
			//	Discard any column rules that are no longer valid within this range
			list($rangeStart,$rangeEnd) = PHPExcel_Cell::rangeBoundaries($this->_range);
			foreach($this->_columns as $key => $value) {
				$colIndex = PHPExcel_Cell::columnIndexFromString($key);
				if (($rangeStart[0] > $colIndex) || ($rangeEnd[0] < $colIndex)) {
					unset($this->_columns[$key]);
				}
			}
		}

		return $this;
	}

	/**
	 * Get all AutoFilter Columns
	 *
	 * @throws	PHPExcel_Exception
	 * @return array of PHPExcel_Worksheet_AutoFilter_Column
	 */
	public function getColumns() {
		return $this->_columns;
	}

	/**
	 * Validate that the specified column is in the AutoFilter range
	 *
	 * @param	string	$column		Column name (e.g. A)
	 * @throws	PHPExcel_Exception
	 * @return	integer	The column offset within the autofilter range
	 */
	protected function _testColumnInRange($column) {
		if (empty($this->_range)) {
			throw new PHPExcel_Exception("No autofilter range is defined.");
		}

		$columnIndex = PHPExcel_Cell::columnIndexFromString($column);
		list($rangeStart,$rangeEnd) = PHPExcel_Cell::rangeBoundaries($this->_range);
		if (($rangeStart[0] > $columnIndex) || ($rangeEnd[0] < $columnIndex)) {
			throw new PHPExcel_Exception("Column is outside of current autofilter range.");
		}

		return $columnIndex - $rangeStart[0];
	}

	/**
	 * Get a specified AutoFilter Column Offset within the defined AutoFilter range
	 *
	 * @param	string	$pColumn		Column name (e.g. A)
	 * @throws	PHPExcel_Exception
	 * @return integer	The offset of the specified column within the autofilter range
	 */
	public function getColumnOffset($pColumn) {
		return $this->_testColumnInRange($pColumn);
	}

	/**
	 * Get a specified AutoFilter Column
	 *
	 * @param	string	$pColumn		Column name (e.g. A)
	 * @throws	PHPExcel_Exception
	 * @return PHPExcel_Worksheet_AutoFilter_Column
	 */
	public function getColumn($pColumn) {
		$this->_testColumnInRange($pColumn);

		if (!isset($this->_columns[$pColumn])) {
			$this->_columns[$pColumn] = new PHPExcel_Worksheet_AutoFilter_Column($pColumn, $this);
		}

		return $this->_columns[$pColumn];
	}

	/**
	 * Get a specified AutoFilter Column by it's offset
	 *
	 * @param	integer	$pColumnOffset		Column offset within range (starting from 0)
	 * @throws	PHPExcel_Exception
	 * @return PHPExcel_Worksheet_AutoFilter_Column
	 */
	public function getColumnByOffset($pColumnOffset = 0) {
		list($rangeStart,$rangeEnd) = PHPExcel_Cell::rangeBoundaries($this->_range);
		$pColumn = PHPExcel_Cell::stringFromColumnIndex($rangeStart[0] + $pColumnOffset - 1);

		return $this->getColumn($pColumn);
	}

	/**
	 *	Set AutoFilter
	 *
	 *	@param	PHPExcel_Worksheet_AutoFilter_Column|string		$pColumn
	 *			A simple string containing a Column ID like 'A' is permitted
	 *	@throws	PHPExcel_Exception
	 *	@return PHPExcel_Worksheet_AutoFilter
	 */
	public function setColumn($pColumn)
	{
		if ((is_string($pColumn)) && (!empty($pColumn))) {
			$column = $pColumn;
		} elseif(is_object($pColumn) && ($pColumn instanceof PHPExcel_Worksheet_AutoFilter_Column)) {
			$column = $pColumn->getColumnIndex();
		} else {
			throw new PHPExcel_Exception("Column is not within the autofilter range.");
		}
		$this->_testColumnInRange($column);

		if (is_string($pColumn)) {
			$this->_columns[$pColumn] = new PHPExcel_Worksheet_AutoFilter_Column($pColumn, $this);
		} elseif(is_object($pColumn) && ($pColumn instanceof PHPExcel_Worksheet_AutoFilter_Column)) {
			$pColumn->setParent($this);
			$this->_columns[$column] = $pColumn;
		}

		return $this;
	}

	private static function _filterTestInSimpleDataSet($cellValue,$dataSet)
	{
		$dataSetValues = $dataSet['filterValues'];
		$blanks = $dataSet['blanks'];
		if (($cellValue == '') || ($cellValue === NULL)) {
			return $blanks;
		}
		return in_array($cellValue,$dataSetValues);
	}

	private static function _filterTestInDateGroupSet($cellValue,$dataSet)
	{
		$dateSet = $dataSet['filterValues'];
		$blanks = $dataSet['blanks'];
		if (($cellValue == '') || ($cellValue === NULL)) {
			return $blanks;
		}
		if (is_numeric($cellValue)) {
			$dateValue = PHPExcel_Shared_Date::ExcelToPHP($cellValue);
			if ($cellValue < 1) {
				//	Just the time part
				$dtVal = date('His',$dateValue);
				$dateSet = $dateSet['time'];
			} elseif($cellValue == floor($cellValue)) {
				//	Just the date part
				$dtVal = date('Ymd',$dateValue);
				$dateSet = $dateSet['date'];
			} else {
				//	date and time parts
				$dtVal = date('YmdHis',$dateValue);
				$dateSet = $dateSet['dateTime'];
			}
			foreach($dateSet as $dateValue) {
				//	Use of substr to extract value at the appropriate group level
				if (substr($dtVal,0,strlen($dateValue)) == $dateValue)
					return TRUE;
			}
		}

		return FALSE;
	}

	private static function _filterTypeCustomFilters($cellValue,$ruleSet)
	{
		$dataSet = $ruleSet['filterRules'];
		$join = $ruleSet['join'];

		$returnVal = ($join == PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_COLUMN_ANDOR_AND);
		foreach($dataSet as $rule) {
			if (is_numeric($rule['value'])) {
				//	Numeric values are tested using the appropriate operator
				switch ($rule['operator']) {
					case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_EQUAL :
						$retVal	= ($cellValue == $rule['value']);
						break;
					case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL :
						$retVal	= ($cellValue != $rule['value']);
						break;
					case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_GREATERTHAN :
						$retVal	= ($cellValue > $rule['value']);
						break;
					case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL :
						$retVal	= ($cellValue >= $rule['value']);
						break;
					case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_LESSTHAN :
						$retVal	= ($cellValue < $rule['value']);
						break;
					case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_LESSTHANOREQUAL :
						$retVal	= ($cellValue <= $rule['value']);
						break;
				}
			} else {
				//	String values are always tested for equality
				$retVal	= preg_match('/^'.$rule['value'].'$/',$cellValue);
			}
			//	If there are multiple conditions, then we need to test both using the appropriate join operator
			switch ($join) {
				case PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_COLUMN_ANDOR_OR :
					$returnVal = $returnVal || $retVal;
					//	Break as soon as we have a match for OR joins
					if ($returnVal)
						return $returnVal;
					break;
				case PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_COLUMN_ANDOR_AND :
					$returnVal = $returnVal && $retVal;
					break;
			}
		}

		return $returnVal;
	}

	private static function _filterTypeDynamicFilters($cellValue,$testSet)
	{
		return TRUE;
	}

	private static function _filterTypeTopTenFilters($cellValue,$testSet)
	{
		return TRUE;
	}

	private static $_fromReplace = array('\*', '\?', '~~', '~.*', '~.?');
	private static $_toReplace   = array('.*', '.',  '~',  '\*',  '\?');

	/**
	 *	Apply the AutoFilter rules to the AutoFilter Range
	 *
	 *	@throws	PHPExcel_Exception
	 *	@return PHPExcel_Worksheet_AutoFilter
	 */
	public function showHideRows()
	{
		list($rangeStart,$rangeEnd) = PHPExcel_Cell::rangeBoundaries($this->_range);

		//	The heading row should always be visible
		echo 'AutoFilter Heading Row ',$rangeStart[1],' is always SHOWN',PHP_EOL;
		$this->_workSheet->getRowDimension($rangeStart[1])->setVisible(TRUE);

		$columnFilterTests = array();
		foreach($this->_columns as $columnID => $filterColumn) {
			$rules = $filterColumn->getRules();
			switch ($filterColumn->getFilterType()) {
				case PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_FILTER :
					$ruleValues = array();
					//	Build a list of the filter value selections
					foreach($rules as $rule) {
						$ruleType = $rule->getRuleType();
						$ruleValues[] = $rule->getValue();
					}
					//	Test if we want to include blanks in our filter criteria
					$blanks = FALSE;
					$ruleDataSet = array_filter($ruleValues);
					if (count($ruleValues) != count($ruleDataSet))
						$blanks = TRUE;
					if ($ruleType == PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_FILTER) {
						//	Filter on absolute values
						$columnFilterTests[$columnID] = array(
							'method' => '_filterTestInSimpleDataSet',
							'arguments' => array( 'filterValues' => $ruleDataSet,
												  'blanks' => $blanks
												)
						);
					} else {
						//	Filter on date group values
						$arguments = array();
						foreach($ruleDataSet as $ruleValue) {
							$date = $time = '';
							if ((isset($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_YEAR])) &&
								($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_YEAR] !== ''))
								$date .= sprintf('%04d',$ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_YEAR]);
							if ((isset($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_MONTH])) &&
								($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_MONTH] != ''))
								$date .= sprintf('%02d',$ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_MONTH]);
							if ((isset($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_DAY])) &&
								($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_DAY] !== ''))
								$date .= sprintf('%02d',$ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_DAY]);
							if ((isset($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_HOUR])) &&
								($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_HOUR] !== ''))
								$time .= sprintf('%02d',$ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_HOUR]);
							if ((isset($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_MINUTE])) &&
								($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_MINUTE] !== ''))
								$time .= sprintf('%02d',$ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_MINUTE]);
							if ((isset($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_SECOND])) &&
								($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_SECOND] !== ''))
								$time .= sprintf('%02d',$ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_SECOND]);
							$dateTime = $date . $time;
							$arguments['date'][] = $date;
							$arguments['time'][] = $time;
							$arguments['dateTime'][] = $dateTime;
						}
						//	Remove empty elements
						$arguments['date'] = array_filter($arguments['date']);
						$arguments['time'] = array_filter($arguments['time']);
						$arguments['dateTime'] = array_filter($arguments['dateTime']);
						$columnFilterTests[$columnID] = array(
							'method' => '_filterTestInDateGroupSet',
							'arguments' => array( 'filterValues' => $arguments,
												  'blanks' => $blanks
												)
						);
					}
					break;
				case PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER :
					$ruleValues = array();
					//	Build a list of the filter value selections
					foreach($rules as $rule) {
						$ruleType = $rule->getRuleType();
						$ruleValue = $rule->getValue();
						if (!is_numeric($ruleValue)) {
							//	Convert to a regexp allowing for regexp reserved characters, wildcards and escaped wildcards
var_dump($ruleValue);
echo ' = > ';
							$ruleValue = preg_quote($ruleValue);
var_dump($ruleValue);
echo ' = > ';
							$ruleValue = str_replace(self::$_fromReplace,self::$_toReplace,$ruleValue);
var_dump($ruleValue);
						}
						$ruleValues[] = array( 'operator' => $rule->getOperator(),
											   'value' => $ruleValue
											 );
					}
					$join = $filterColumn->getAndOr();
					$columnFilterTests[$columnID] = array(
						'method' => '_filterTypeCustomFilters',
						'arguments' => array( 'filterRules' => $ruleValues,
											  'join' => $join
											)
					);
					break;
				case PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER :
					$columnFilterTests[$columnID] = array(
						'method' => '_filterTypeDynamicFilters',
						'arguments' => $ruleValues
					);
				break;
				case PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_TOPTENFILTER :
					$columnFilterTests[$columnID] = array(
						'method' => '_filterTypeTopTenFilters',
						'arguments' => $ruleValues
					);
					break;
			}
		}

		echo 'Column Filter Test CRITERIA',PHP_EOL;
		var_dump($columnFilterTests);

		for ($row = $rangeStart[1]+1; $row <= $rangeEnd[1]; ++$row) {
			echo 'Testing Row = ',$row,PHP_EOL;
			$result = TRUE;
			foreach($columnFilterTests as $columnID => $columnFilterTest) {
				echo 'Testing cell ',$columnID.$row,PHP_EOL;
				$cellValue = $this->_workSheet->getCell($columnID.$row)->getCalculatedValue();
				echo 'Value is ',$cellValue,PHP_EOL;
				//	Execute the filter test
				$result = $result &&
					call_user_func_array(
						array('PHPExcel_Worksheet_AutoFilter',$columnFilterTest['method']),
						array(
							$cellValue,
							$columnFilterTest['arguments']
						)
					);
					echo (($result) ? 'VALID' : 'INVALID'),PHP_EOL;
				//	If filter test has resulted in FALSE, exit straightaway rather than running any more tests
				if (!$result)
					break;
			}
			echo (($result) ? 'SHOW' : 'HIDE'),PHP_EOL;
			$this->_workSheet->getRowDimension($row)->setVisible($result);
		}

		return $this;
	}


	/**
	 * Implement PHP __clone to create a deep clone, not just a shallow copy.
	 */
	public function __clone() {
		$vars = get_object_vars($this);
		foreach ($vars as $key => $value) {
			if (is_object($value)) {
				if ($key == '_workSheet') {
					//	Detach from worksheet
					$this->$key = NULL;
				} else {
					$this->$key = clone $value;
				}
			} elseif ((is_array($value)) && ($key == '_columns')) {
				//	The columns array of PHPExcel_Worksheet_AutoFilter objects
				$this->$key = array();
				foreach ($value as $k => $v) {
					$this->$key[$k] = clone $v;
					// attache the new cloned Column to this new cloned Autofilter object
					$this->$key[$k]->setParent($this);
				}
			} else {
				$this->$key = $value;
			}
		}
	}

	/**
	 * toString method replicates previous behavior by returning the range if object is
	 *    referenced as a property of its parent.
	 */
	public function __toString() {
		return (string) $this->_range;
	}

}
