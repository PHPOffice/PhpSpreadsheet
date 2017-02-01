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
class Column
{
    /**
     * \PhpOffice\PhpSpreadsheet\Worksheet.
     *
     * @var \PhpOffice\PhpSpreadsheet\Worksheet
     */
    private $parent;

    /**
     * Column index.
     *
     * @var string
     */
    private $columnIndex;

    /**
     * Create a new column.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $parent
     * @param string $columnIndex
     */
    public function __construct(\PhpOffice\PhpSpreadsheet\Worksheet $parent = null, $columnIndex = 'A')
    {
        // Set parent and column index
        $this->parent = $parent;
        $this->columnIndex = $columnIndex;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->parent);
    }

    /**
     * Get column index.
     *
     * @return string
     */
    public function getColumnIndex()
    {
        return $this->columnIndex;
    }

    /**
     * Get cell iterator.
     *
     * @param int $startRow The row number at which to start iterating
     * @param int $endRow Optionally, the row number at which to stop iterating
     *
     * @return ColumnCellIterator
     */
    public function getCellIterator($startRow = 1, $endRow = null)
    {
        return new ColumnCellIterator($this->parent, $this->columnIndex, $startRow, $endRow);
    }
}
