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
 * @category	PHPExcel
 * @package		PHPExcel_Worksheet
 * @copyright	Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license		http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version		##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Worksheet_AutoFilter_Column
 *
 * @category	PHPExcel
 * @package		PHPExcel_Worksheet
 * @copyright	Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_AutoFilter_Column
{
	/* Column Datatypes */
	const AUTOFILTER_COLUMN_DATATYPE_STRING	= 's';
	const AUTOFILTER_COLUMN_DATATYPE_NUMBER	= 'n';
	const AUTOFILTER_COLUMN_DATATYPE_DATE	= 'd';

	private static $_dataTypes = array(
		self::AUTOFILTER_COLUMN_DATATYPE_STRING,
		self::AUTOFILTER_COLUMN_DATATYPE_NUMBER,
		self::AUTOFILTER_COLUMN_DATATYPE_DATE,
	);

	/* Multiple Rule Connections */
	const AUTOFILTER_COLUMN_ANDOR_AND	= 'and';
	const AUTOFILTER_COLUMN_ANDOR_OR	= 'or';

	private static $_ruleConnections = array(
		self::AUTOFILTER_COLUMN_ANDOR_AND,
		self::AUTOFILTER_COLUMN_ANDOR_OR,
	);

	/**
	 * Autofilter
	 *
	 * @var PHPExcel_Worksheet_AutoFilter
	 */
	private $_parent = NULL;


	/**
	 * Autofilter Column Index
	 *
	 * @var string
	 */
	private $_columnIndex = '';


	/**
	 * Autofilter Column DataType
	 *
	 * @var string
	 */
	private $_dataType = NULL;


	/**
	 * Autofilter Multiple Rules And/Or
	 *
	 * @var string
	 */
	private $_andOr = self::AUTOFILTER_COLUMN_ANDOR_OR;


	/**
	 * Autofilter Column Rules
	 *
	 * @var array of PHPExcel_Worksheet_AutoFilter_Column_Rule
	 */
	private $_ruleset = array();


	/**
	 * Create a new PHPExcel_Worksheet_AutoFilter_Column
	 */
	public function __construct($pColumn, PHPExcel_Worksheet_AutoFilter $pParent = NULL)
	{
		$this->_columnIndex = $pColumn;
		$this->_parent = $pParent;
	}

	/**
	 * Get AutoFilter Column Index
	 *
	 * @return string
	 */
	public function getColumnIndex() {
		return $this->_columnIndex;
	}

	/**
	 *	Set AutoFilter Column Index
	 *
	 *	@param	string		$pColumn		Column (e.g. A)
	 *	@throws	Exception
	 *	@return PHPExcel_Worksheet_AutoFilter_Column
	 */
	public function setColumnIndex($pColumn) {
		// Uppercase coordinate
		$pColumn = strtoupper($pColumn);
		if ($this->_parent !== NULL) {
			$this->_parent->_testColumnInRange($pColumn);
		}

		$this->_columnIndex = $pColumn;

		return $this;
	}

	/**
	 * Get this Column's AutoFilter Parent
	 *
	 * @return PHPExcel_Worksheet_AutoFilter
	 */
	public function getParent() {
		return $this->_parent;
	}

	/**
	 * Set this Column's AutoFilter Parent
	 *
	 * @param PHPExcel_Worksheet_AutoFilter
	 * @return PHPExcel_Worksheet_AutoFilter_Column
	 */
	public function setParent(PHPExcel_Worksheet_AutoFilter $pParent = NULL) {
		$this->_parent = $pParent;

		return $this;
	}

	/**
	 * Get AutoFilter Multiple Rules And/Or
	 *
	 * @return string
	 */
	public function getAndOr() {
		return $this->_andOr;
	}

	/**
	 *	Set AutoFilter Multiple Rules And/Or
	 *
	 *	@param	string		$pAndOr		And/Or
	 *	@throws	Exception
	 *	@return PHPExcel_Worksheet_AutoFilter_Column
	 */
	public function setAndOr($pAndOr = self::AUTOFILTER_COLUMN_ANDOR_OR) {
		// Lowercase And/Or
		$pAndOr = strtolower($pAndOr);
		if (!in_array($pAndOr,self::$_ruleConnections)) {
			throw new PHPExcel_Exception('Invalid rule connection for column AutoFilter.');
		}

		$this->_andOr = $pAndOr;

		return $this;
	}

	/**
	 * Get AutoFilter Column Data Type
	 *
	 * @return string
	 */
	public function getDataType() {
		if ($this->_dataType === NULL) {
		}

		return $this->_dataType;
	}

	/**
	 *	Set AutoFilter Column Data Type
	 *
	 *	@param	string		$pDataType		Data Type
	 *	@throws	Exception
	 *	@return PHPExcel_Worksheet_AutoFilter_Column
	 */
	public function setDataType($pDataType = self::AUTOFILTER_COLUMN_DATATYPE_STRING) {
		// Lowercase datatype
		$pDataType = strtolower($pDataType);
		if (!in_array($pDataType,self::$_dataTypes)) {
			throw new PHPExcel_Exception('Invalid datatype for column AutoFilter.');
		}

		$this->_dataType = $pDataType;

		return $this;
	}

	/**
	 * Get all AutoFilter Column Rules
	 *
	 * @throws	PHPExcel_Exception
	 * @return array of PHPExcel_Worksheet_AutoFilter_Column_Rule
	 */
	public function getRules() {
		return $this->_ruleset;
	}

	/**
	 * Get a specified AutoFilter Column Rule
	 *
	 * @param	integer	$pIndex		Rule index in the ruleset array
	 * @return	PHPExcel_Worksheet_AutoFilter_Column_Rule
	 */
	public function getRule($pIndex) {
		if (!isset($this->_ruleset[$pIndex])) {
			$this->_ruleset[$pIndex] = new PHPExcel_Worksheet_AutoFilter_Column_Rule($this);
		}
		return $this->_ruleset[$pIndex];
	}

	/**
	 * Create a new AutoFilter Column Rule in the ruleset
	 *
	 * @return	PHPExcel_Worksheet_AutoFilter_Column_Rule
	 */
	public function createRule() {
		$this->_ruleset[] = new PHPExcel_Worksheet_AutoFilter_Column_Rule($this);

		return end($this->_ruleset);
	}

	/**
	 * Add a new AutoFilter Column Rule to the ruleset
	 *
	 * @param	PHPExcel_Worksheet_AutoFilter_Column_Rule	$pRule
	 * @param	boolean	$returnRule 	Flag indicating whether the rule object or the column object should be returned
	 * @return	PHPExcel_Worksheet_AutoFilter_Column|PHPExcel_Worksheet_AutoFilter_Column_Rule
	 */
	public function addRule(PHPExcel_Worksheet_AutoFilter_Column_Rule $pRule, $returnRule=TRUE) {
		$pRule->setParent($this);
		$this->_ruleset[] = $pRule;

		return ($returnRule) ? $pRule : $this;
	}

	/**
	 * Delete a specified AutoFilter Column Rule
	 *
	 * @param	integer	$pIndex		Rule index in the ruleset array
	 * @return	PHPExcel_Worksheet_AutoFilter_Column
	 */
	public function deleteRule($pIndex) {
		if (isset($this->_ruleset[$pIndex])) {
			unset($this->_ruleset[$pIndex]);
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
				if ($key == '_parent') {
					//	Detach from autofilter parent
					$this->$key = NULL;
				} else {
					$this->$key = clone $value;
				}
			} elseif ((is_array($value)) && ($key == '_ruleset')) {
				//	The columns array of PHPExcel_Worksheet_AutoFilter objects
				$this->$key = array();
				foreach ($value as $k => $v) {
					$this->$key[$k] = clone $v;
				}
			} else {
				$this->$key = $value;
			}
		}
	}

}
