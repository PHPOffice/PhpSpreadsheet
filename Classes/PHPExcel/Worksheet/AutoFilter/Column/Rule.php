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
 * PHPExcel_Worksheet_AutoFilter_Column_Rule
 *
 * @category	PHPExcel
 * @package		PHPExcel_Worksheet
 * @copyright	Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_AutoFilter_Column_Rule
{
	/* Rule Operators (Numeric and String) */
	const AUTOFILTER_COLUMN_RULE_EQUAL				= 'equal';
	/* Rule Operators (Numeric, Boolean etc) */
	const AUTOFILTER_COLUMN_RULE_NOTEQUAL			= 'notEqual';
	const AUTOFILTER_COLUMN_RULE_GREATERTHAN		= 'greaterThan';
	const AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL	= 'greaterThanOrEqual';
	const AUTOFILTER_COLUMN_RULE_LESSTHAN			= 'lessThan';
	const AUTOFILTER_COLUMN_RULE_LESSTHANOREQUAL	= 'lessThanOrEqual';
	const AUTOFILTER_COLUMN_RULE_BETWEEN			= 'between';		//	greaterThanOrEqual 1 && lessThanOrEqual 2
	/* Rule Operators (Numeric Special) which are translated to standard numeric operators with calculated values */
	const AUTOFILTER_COLUMN_RULE_TOPTEN				= 'topTen';			//	greaterThan calculated value
	const AUTOFILTER_COLUMN_RULE_TOPTENPERCENT		= 'topTenPercent';	//	greaterThan calculated value
	const AUTOFILTER_COLUMN_RULE_ABOVEAVERAGE		= 'aboveAverage';	//	Value is calculated as the average
	const AUTOFILTER_COLUMN_RULE_BELOWAVERAGE		= 'belowAverage';	//	Value is calculated as the average
	/* Rule Operators (String) which are set as wild-carded values */
	const AUTOFILTER_COLUMN_RULE_BEGINSWITH			= 'beginsWith';			// A*
	const AUTOFILTER_COLUMN_RULE_ENDSWITH			= 'endsWith';			// *Z
	const AUTOFILTER_COLUMN_RULE_CONTAINS			= 'contains';			// *B*
	const AUTOFILTER_COLUMN_RULE_DOESNTCONTAIN		= 'notEqual';			//	notEqual *B*
	/* Rule Operators (Date Special) which are translated to standard numeric operators with calculated values */
	const AUTOFILTER_COLUMN_RULE_BEFORE				= 'lessThan';
	const AUTOFILTER_COLUMN_RULE_AFTER				= 'greaterThan';
	const AUTOFILTER_COLUMN_RULE_YESTERDAY			= 'yesterday';
	const AUTOFILTER_COLUMN_RULE_TODAY				= 'today';
	const AUTOFILTER_COLUMN_RULE_TOMORROW			= 'tomorrow';
	const AUTOFILTER_COLUMN_RULE_LASTWEEK			= 'lastWeek';
	const AUTOFILTER_COLUMN_RULE_THISWEEK			= 'thisWeek';
	const AUTOFILTER_COLUMN_RULE_NEXTWEEK			= 'nextWeek';
	const AUTOFILTER_COLUMN_RULE_LASTMONTH			= 'lastMonth';
	const AUTOFILTER_COLUMN_RULE_THISMONTH			= 'thisMonth';
	const AUTOFILTER_COLUMN_RULE_NEXTMONTH			= 'nextMonth';
	const AUTOFILTER_COLUMN_RULE_LASTQUARTER		= 'lastQuarter';
	const AUTOFILTER_COLUMN_RULE_THISQUARTER		= 'thisQuarter';
	const AUTOFILTER_COLUMN_RULE_NEXTQUARTER		= 'nextQuarter';
	const AUTOFILTER_COLUMN_RULE_LASTYEAR			= 'lastYear';
	const AUTOFILTER_COLUMN_RULE_THISYEAR			= 'thisYear';
	const AUTOFILTER_COLUMN_RULE_NEXTYEAR			= 'nextYear';
	const AUTOFILTER_COLUMN_RULE_YEARTODATE			= 'yearToDate';			//	<dynamicFilter val="40909" type="yearToDate" maxVal="41113"/>
	const AUTOFILTER_COLUMN_RULE_ALLDATESINMONTH	= 'allDatesInMonth';	//	<dynamicFilter type="M2"/> for Month/February
	const AUTOFILTER_COLUMN_RULE_ALLDATESINQUARTER	= 'allDatesInQuarter';	//	<dynamicFilter type="Q2"/> for Quarter 2

	/**
	 * Autofilter Column
	 *
	 * @var PHPExcel_Worksheet_AutoFilter_Column
	 */
	private $_parent = NULL;


	/**
	 * Autofilter Rule Value
	 *
	 * @var string
	 */
	private $_ruleValue = '';

	/**
	 * Autofilter Rule Operator
	 *
	 * @var string
	 */
	private $_ruleOperator = '';


	/**
	 * Create a new PHPExcel_Worksheet_AutoFilter_Column_Rule
	 */
	public function __construct(PHPExcel_Worksheet_AutoFilter_Column $pParent = NULL)
	{
		$this->_parent = $pParent;
	}

	/**
	 * Get AutoFilter Rule Value
	 *
	 * @return string
	 */
	public function getValue() {
		return $this->_value;
	}

	/**
	 *	Set AutoFilter Rule Value
	 *
	 *	@param	string		$pValue
	 *	@throws	Exception
	 *	@return PHPExcel_Worksheet_AutoFilter_Column_Rule
	 */
	public function setValue($pValue = '') {
		$this->_value = $pValue;

		return $this;
	}

	/**
	 * Get AutoFilter Rule Operator
	 *
	 * @return string
	 */
	public function getOperator() {
		return $this->_operator;
	}

	/**
	 *	Set AutoFilter Rule Operator
	 *
	 *	@param	string		$pOperator
	 *	@throws	Exception
	 *	@return PHPExcel_Worksheet_AutoFilter_Column_Rule
	 */
	public function setOperator($pOperator = self::AUTOFILTER_COLUMN_RULE_EQUAL) {
		if (empty($pOperator))
			$pOperator = self::AUTOFILTER_COLUMN_RULE_EQUAL;
		$this->_operator = $pOperator;

		return $this;
	}

	/**
	 *	Set AutoFilter Rule Operator
	 *
	 *	@param	string		$pOperator
	 *	@throws	Exception
	 *	@return PHPExcel_Worksheet_AutoFilter_Column_Rule
	 */
	public function setRule($pOperator = self::AUTOFILTER_COLUMN_RULE_EQUAL, $pValue = '') {
		$this->setOperator($pOperator);
		$this->setValue($pValue);

		return $this;
	}

	/**
	 * Get this Rule's AutoFilter Column Parent
	 *
	 * @return PHPExcel_Worksheet_AutoFilter_Column
	 */
	public function getParent() {
		return $this->_parent;
	}

	/**
	 * Set this Rule's AutoFilter Column Parent
	 *
	 * @param PHPExcel_Worksheet_AutoFilter_Column
	 * @return PHPExcel_Worksheet_AutoFilter_Column_Rule
	 */
	public function setParent(PHPExcel_Worksheet_AutoFilter_Column $pParent = NULL) {
		$this->_parent = $pParent;

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
					//	Detach from autofilter column parent
					$this->$key = NULL;
				} else {
					$this->$key = clone $value;
				}
			} else {
				$this->$key = $value;
			}
		}
	}

}
