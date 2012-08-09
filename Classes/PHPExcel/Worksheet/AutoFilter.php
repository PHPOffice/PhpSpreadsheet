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
