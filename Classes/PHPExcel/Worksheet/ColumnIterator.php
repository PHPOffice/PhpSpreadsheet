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
 * @package	PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license	http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version	##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Worksheet_ColumnIterator
 *
 * Used to iterate columns in a PHPExcel_Worksheet
 *
 * @category   PHPExcel
 * @package	PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_ColumnIterator implements Iterator
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
	private $_position = 0;

	/**
	 * Start position
	 *
	 * @var int
	 */
	private $_startColumn = 0;


	/**
	 * End position
	 *
	 * @var int
	 */
	private $_endColumn = 0;


	/**
	 * Create a new column iterator
	 *
	 * @param	PHPExcel_Worksheet	$subject	The worksheet to iterate over
	 * @param	string				$startColumn	The column address at which to start iterating
	 * @param	string				$endColumn	    Optionally, the column address at which to stop iterating
	 */
	public function __construct(PHPExcel_Worksheet $subject = null, $startColumn = 'A', $endColumn = null) {
		// Set subject
		$this->_subject = $subject;
		$this->resetEnd($endColumn);
		$this->resetStart($startColumn);
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		unset($this->_subject);
	}

	/**
	 * (Re)Set the start column and the current column pointer
	 *
	 * @param integer	$startColumn	The column address at which to start iterating
     * @return PHPExcel_Worksheet_ColumnIterator
	 */
	public function resetStart($startColumn = 'A') {
        $startColumnIndex = PHPExcel_Cell::columnIndexFromString($startColumn) - 1;
		$this->_startColumn = $startColumnIndex;
		$this->seek($startColumn);

        return $this;
	}

	/**
	 * (Re)Set the end column
	 *
	 * @param string	$endColumn	The column address at which to stop iterating
     * @return PHPExcel_Worksheet_ColumnIterator
	 */
	public function resetEnd($endColumn = null) {
		$endColumn = ($endColumn) ? $endColumn : $this->_subject->getHighestColumn();
		$this->_endColumn = PHPExcel_Cell::columnIndexFromString($endColumn) - 1;

        return $this;
	}

	/**
	 * Set the column pointer to the selected column
	 *
	 * @param string	$column	The column address to set the current pointer at
     * @return PHPExcel_Worksheet_ColumnIterator
     * @throws PHPExcel_Exception
	 */
	public function seek($column = 'A') {
        $column = PHPExcel_Cell::columnIndexFromString($column) - 1;
        if (($column < $this->_startColumn) || ($column > $this->_endColumn)) {
            throw new PHPExcel_Exception("Column $column is out of range ({$this->_startColumn} - {$this->_endColumn})");
        }
		$this->_position = $column;

        return $this;
    }

	/**
	 * Rewind the iterator to the starting column
	 */
	public function rewind() {
		$this->_position = $this->_startColumn;
	}

	/**
	 * Return the current column in this worksheet
	 *
	 * @return PHPExcel_Worksheet_Column
	 */
	public function current() {
		return new PHPExcel_Worksheet_Column($this->_subject, PHPExcel_Cell::stringFromColumnIndex($this->_position));
	}

	/**
	 * Return the current iterator key
	 *
	 * @return string
	 */
	public function key() {
		return PHPExcel_Cell::stringFromColumnIndex($this->_position);
	}

	/**
	 * Set the iterator to its next value
	 */
	public function next() {
		++$this->_position;
	}

	/**
	 * Set the iterator to its previous value
     *
     * @throws PHPExcel_Exception
	 */
	public function prev() {
        if ($this->_position <= $this->_startColumn) {
            throw new PHPExcel_Exception(
                "Column is already at the beginning of range (" . 
                PHPExcel_Cell::stringFromColumnIndex($this->_endColumn) . " - " . 
                PHPExcel_Cell::stringFromColumnIndex($this->_endColumn) . ")"
            );
        }

        --$this->_position;
	}

	/**
	 * Indicate if more columns exist in the worksheet range of columns that we're iterating
	 *
	 * @return boolean
	 */
	public function valid() {
		return $this->_position <= $this->_endColumn;
	}
}
