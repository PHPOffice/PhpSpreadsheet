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
	 * Autofilter Range
	 *
	 * @var string
	 */
	private $_range = '';


    /**
     * Create a new PHPExcel_Worksheet_AutoFilter
     */
    public function __construct($pRange = '')
    {
		$this->_range = $pRange;
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
	 *	@throws	Exception
	 *	@return PHPExcel_Worksheet_AutoFilter
	 */
	public function setRange($pRange = '') {
		// Uppercase coordinate
		$pRange = strtoupper($pRange);

		if (strpos($pRange,':') !== FALSE) {
			$this->_range = $pRange;
		} elseif(empty($pRange)) {
			$this->_range = '';
		} else {
			throw new Exception('Autofilter must be set on a range of cells.');
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
				$this->$key = clone $value;
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
		return $this->_range;
	}

}
