<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2015 PHPExcel
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
 * @package	PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license	http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version	##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Worksheet_RowIterator
 *
 * Used to iterate rows in a PHPExcel_Worksheet
 *
 * @category   PHPExcel
 * @package	PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_RowIterator implements Iterator
{
	/**
	 * PHPExcel_Worksheet to iterate
	 *
	 * @var PHPExcel_Worksheet
	 */
	private $_subject;

	/**
	 * Current iterator position
	 *
	 * @var int
	 */
	private $_position = 1;

	/**
	 * Start position
	 *
	 * @var int
	 */
	private $_startRow = 1;


	/**
	 * End position
	 *
	 * @var int
	 */
	private $_endRow = 1;


	/**
	 * Create a new row iterator
	 *
	 * @param	PHPExcel_Worksheet	$subject	The worksheet to iterate over
	 * @param	integer				$startRow	The row number at which to start iterating
	 * @param	integer				$endRow	    Optionally, the row number at which to stop iterating
	 */
	public function __construct(PHPExcel_Worksheet $subject = null, $startRow = 1, $endRow = null) {
		// Set subject
		$this->_subject = $subject;
		$this->resetEnd($endRow);
		$this->resetStart($startRow);
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		unset($this->_subject);
	}

	/**
	 * (Re)Set the start row and the current row pointer
	 *
	 * @param integer	$startRow	The row number at which to start iterating
     * @return PHPExcel_Worksheet_RowIterator
	 */
	public function resetStart($startRow = 1) {
		$this->_startRow = $startRow;
		$this->seek($startRow);

        return $this;
	}

	/**
	 * (Re)Set the end row
	 *
	 * @param integer	$endRow	The row number at which to stop iterating
     * @return PHPExcel_Worksheet_RowIterator
	 */
	public function resetEnd($endRow = null) {
		$this->_endRow = ($endRow) ? $endRow : $this->_subject->getHighestRow();

        return $this;
	}

	/**
	 * Set the row pointer to the selected row
	 *
	 * @param integer	$row	The row number to set the current pointer at
     * @return PHPExcel_Worksheet_RowIterator
     * @throws PHPExcel_Exception
	 */
	public function seek($row = 1) {
        if (($row < $this->_startRow) || ($row > $this->_endRow)) {
            throw new PHPExcel_Exception("Row $row is out of range ({$this->_startRow} - {$this->_endRow})");
        }
		$this->_position = $row;

        return $this;
	}

	/**
	 * Rewind the iterator to the starting row
	 */
	public function rewind() {
		$this->_position = $this->_startRow;
	}

	/**
	 * Return the current row in this worksheet
	 *
	 * @return PHPExcel_Worksheet_Row
	 */
	public function current() {
		return new PHPExcel_Worksheet_Row($this->_subject, $this->_position);
	}

	/**
	 * Return the current iterator key
	 *
	 * @return int
	 */
	public function key() {
		return $this->_position;
	}

	/**
	 * Set the iterator to its next value
	 */
	public function next() {
		++$this->_position;
	}

	/**
	 * Set the iterator to its previous value
	 */
	public function prev() {
        if ($this->_position <= $this->_startRow) {
            throw new PHPExcel_Exception("Row is already at the beginning of range ({$this->_startRow} - {$this->_endRow})");
        }

        --$this->_position;
	}

	/**
	 * Indicate if more rows exist in the worksheet range of rows that we're iterating
	 *
	 * @return boolean
	 */
	public function valid() {
		return $this->_position <= $this->_endRow;
	}
}
