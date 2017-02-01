<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category   PhpSpreadsheet
 *
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class RowIterator implements \Iterator
{
    /**
     * \PhpOffice\PhpSpreadsheet\Worksheet to iterate.
     *
     * @var \PhpOffice\PhpSpreadsheet\Worksheet
     */
    private $subject;

    /**
     * Current iterator position.
     *
     * @var int
     */
    private $position = 1;

    /**
     * Start position.
     *
     * @var int
     */
    private $startRow = 1;

    /**
     * End position.
     *
     * @var int
     */
    private $endRow = 1;

    /**
     * Create a new row iterator.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $subject The worksheet to iterate over
     * @param int $startRow The row number at which to start iterating
     * @param int $endRow Optionally, the row number at which to stop iterating
     */
    public function __construct(\PhpOffice\PhpSpreadsheet\Worksheet $subject, $startRow = 1, $endRow = null)
    {
        // Set subject
        $this->subject = $subject;
        $this->resetEnd($endRow);
        $this->resetStart($startRow);
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->subject);
    }

    /**
     * (Re)Set the start row and the current row pointer.
     *
     * @param int $startRow The row number at which to start iterating
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     *
     * @return RowIterator
     */
    public function resetStart($startRow = 1)
    {
        if ($startRow > $this->subject->getHighestRow()) {
            throw new \PhpOffice\PhpSpreadsheet\Exception("Start row ({$startRow}) is beyond highest row ({$this->subject->getHighestRow()})");
        }

        $this->startRow = $startRow;
        if ($this->endRow < $this->startRow) {
            $this->endRow = $this->startRow;
        }
        $this->seek($startRow);

        return $this;
    }

    /**
     * (Re)Set the end row.
     *
     * @param int $endRow The row number at which to stop iterating
     *
     * @return RowIterator
     */
    public function resetEnd($endRow = null)
    {
        $this->endRow = ($endRow) ? $endRow : $this->subject->getHighestRow();

        return $this;
    }

    /**
     * Set the row pointer to the selected row.
     *
     * @param int $row The row number to set the current pointer at
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     *
     * @return RowIterator
     */
    public function seek($row = 1)
    {
        if (($row < $this->startRow) || ($row > $this->endRow)) {
            throw new \PhpOffice\PhpSpreadsheet\Exception("Row $row is out of range ({$this->startRow} - {$this->endRow})");
        }
        $this->position = $row;

        return $this;
    }

    /**
     * Rewind the iterator to the starting row.
     */
    public function rewind()
    {
        $this->position = $this->startRow;
    }

    /**
     * Return the current row in this worksheet.
     *
     * @return Row
     */
    public function current()
    {
        return new Row($this->subject, $this->position);
    }

    /**
     * Return the current iterator key.
     *
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Set the iterator to its next value.
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Set the iterator to its previous value.
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function prev()
    {
        if ($this->position <= $this->startRow) {
            throw new \PhpOffice\PhpSpreadsheet\Exception("Row is already at the beginning of range ({$this->startRow} - {$this->endRow})");
        }

        --$this->position;
    }

    /**
     * Indicate if more rows exist in the worksheet range of rows that we're iterating.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->position <= $this->endRow;
    }
}
