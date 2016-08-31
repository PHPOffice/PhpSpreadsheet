<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet
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
 * @category   PhpSpreadsheet
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class ColumnCellIterator extends CellIterator implements \Iterator
{
    /**
     * Column index
     *
     * @var string
     */
    protected $columnIndex;

    /**
     * Start position
     *
     * @var int
     */
    protected $startRow = 1;

    /**
     * End position
     *
     * @var int
     */
    protected $endRow = 1;

    /**
     * Create a new row iterator
     *
     * @param    \PhpOffice\PhpSpreadsheet\Worksheet    $subject        The worksheet to iterate over
     * @param    string              $columnIndex    The column that we want to iterate
     * @param    int                $startRow        The row number at which to start iterating
     * @param    int                $endRow            Optionally, the row number at which to stop iterating
     */
    public function __construct(\PhpOffice\PhpSpreadsheet\Worksheet $subject = null, $columnIndex = 'A', $startRow = 1, $endRow = null)
    {
        // Set subject
        $this->subject = $subject;
        $this->columnIndex = \PhpOffice\PhpSpreadsheet\Cell::columnIndexFromString($columnIndex) - 1;
        $this->resetEnd($endRow);
        $this->resetStart($startRow);
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->subject);
    }

    /**
     * (Re)Set the start row and the current row pointer
     *
     * @param int    $startRow    The row number at which to start iterating
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @return ColumnCellIterator
     */
    public function resetStart($startRow = 1)
    {
        $this->startRow = $startRow;
        $this->adjustForExistingOnlyRange();
        $this->seek($startRow);

        return $this;
    }

    /**
     * (Re)Set the end row
     *
     * @param int    $endRow    The row number at which to stop iterating
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @return ColumnCellIterator
     */
    public function resetEnd($endRow = null)
    {
        $this->endRow = ($endRow) ? $endRow : $this->subject->getHighestRow();
        $this->adjustForExistingOnlyRange();

        return $this;
    }

    /**
     * Set the row pointer to the selected row
     *
     * @param int    $row    The row number to set the current pointer at
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @return ColumnCellIterator
     */
    public function seek($row = 1)
    {
        if (($row < $this->startRow) || ($row > $this->endRow)) {
            throw new \PhpOffice\PhpSpreadsheet\Exception("Row $row is out of range ({$this->startRow} - {$this->endRow})");
        } elseif ($this->onlyExistingCells && !($this->subject->cellExistsByColumnAndRow($this->columnIndex, $row))) {
            throw new \PhpOffice\PhpSpreadsheet\Exception('In "IterateOnlyExistingCells" mode and Cell does not exist');
        }
        $this->position = $row;

        return $this;
    }

    /**
     * Rewind the iterator to the starting row
     */
    public function rewind()
    {
        $this->position = $this->startRow;
    }

    /**
     * Return the current cell in this worksheet column
     *
     * @return null|\PhpOffice\PhpSpreadsheet\Cell
     */
    public function current()
    {
        return $this->subject->getCellByColumnAndRow($this->columnIndex, $this->position);
    }

    /**
     * Return the current iterator key
     *
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Set the iterator to its next value
     */
    public function next()
    {
        do {
            ++$this->position;
        } while (($this->onlyExistingCells) &&
            (!$this->subject->cellExistsByColumnAndRow($this->columnIndex, $this->position)) &&
            ($this->position <= $this->endRow));
    }

    /**
     * Set the iterator to its previous value
     */
    public function prev()
    {
        if ($this->position <= $this->startRow) {
            throw new \PhpOffice\PhpSpreadsheet\Exception("Row is already at the beginning of range ({$this->startRow} - {$this->endRow})");
        }

        do {
            --$this->position;
        } while (($this->onlyExistingCells) &&
            (!$this->subject->cellExistsByColumnAndRow($this->columnIndex, $this->position)) &&
            ($this->position >= $this->startRow));
    }

    /**
     * Indicate if more rows exist in the worksheet range of rows that we're iterating
     *
     * @return bool
     */
    public function valid()
    {
        return $this->position <= $this->endRow;
    }

    /**
     * Validate start/end values for "IterateOnlyExistingCells" mode, and adjust if necessary
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function adjustForExistingOnlyRange()
    {
        if ($this->onlyExistingCells) {
            while ((!$this->subject->cellExistsByColumnAndRow($this->columnIndex, $this->startRow)) &&
                ($this->startRow <= $this->endRow)) {
                ++$this->startRow;
            }
            if ($this->startRow > $this->endRow) {
                throw new \PhpOffice\PhpSpreadsheet\Exception('No cells exist within the specified range');
            }
            while ((!$this->subject->cellExistsByColumnAndRow($this->columnIndex, $this->endRow)) &&
                ($this->endRow >= $this->startRow)) {
                --$this->endRow;
            }
            if ($this->endRow < $this->startRow) {
                throw new \PhpOffice\PhpSpreadsheet\Exception('No cells exist within the specified range');
            }
        }
    }
}
