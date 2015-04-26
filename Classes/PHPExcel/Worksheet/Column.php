<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2014 PHPExcel
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
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Worksheet_Column
 *
 * Represents a column in PHPExcel_Worksheet, used by PHPExcel_Worksheet_ColumnIterator
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_Column
{
	/**
	 * PHPExcel_Worksheet
	 *
	 * @var PHPExcel_Worksheet
	 */
	private $_parent;

	/**
	 * Column index
	 *
	 * @var string
	 */
	private $_columnIndex;

	/**
	 * Create a new column
	 *
	 * @param PHPExcel_Worksheet 	$parent
	 * @param string				$columnIndex
	 */
	public function __construct(PHPExcel_Worksheet $parent = null, $columnIndex = 'A') {
		// Set parent and column index
		$this->_parent 		= $parent;
		$this->_columnIndex = $columnIndex;
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		unset($this->_parent);
	}

	/**
	 * Get column index
	 *
	 * @return int
	 */
	public function getColumnIndex() {
		return $this->_columnIndex;
	}

	/**
	 * Get cell iterator
	 *
	 * @param	integer				$startRow	    The row number at which to start iterating
	 * @param	integer				$endRow	        Optionally, the row number at which to stop iterating
	 * @return PHPExcel_Worksheet_CellIterator
	 */
	public function getCellIterator($startRow = 1, $endRow = null) {
		return new PHPExcel_Worksheet_ColumnCellIterator($this->_parent, $this->_columnIndex, $startRow, $endRow);
	}
}
