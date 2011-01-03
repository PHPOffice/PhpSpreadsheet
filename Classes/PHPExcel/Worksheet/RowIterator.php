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
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Worksheet_RowIterator
 *
 * Used to iterate rows in a PHPExcel_Worksheet
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_RowIterator extends CachingIterator
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
	 * Create a new row iterator
	 *
	 * @param PHPExcel_Worksheet 		$subject
	 */
	public function __construct(PHPExcel_Worksheet $subject = null) {
		// Set subject
		$this->_subject = $subject;
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		unset($this->_subject);
	}

	/**
	 * Rewind iterator
	 */
    public function rewind() {
        $this->_position = 1;
    }

    /**
     * Current PHPExcel_Worksheet_Row
     *
     * @return PHPExcel_Worksheet_Row
     */
    public function current() {
    	return new PHPExcel_Worksheet_Row($this->_subject, $this->_position);
    }

    /**
     * Current key
     *
     * @return int
     */
    public function key() {
        return $this->_position;
    }

    /**
     * Next value
     */
    public function next() {
        ++$this->_position;
    }

    /**
     * More PHPExcel_Worksheet_Row instances available?
     *
     * @return boolean
     */
    public function valid() {
        return $this->_position <= $this->_subject->getHighestRow();
    }
}
