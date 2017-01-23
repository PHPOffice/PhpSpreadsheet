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
class RowDimension extends Dimension
{
    /**
     * Row index.
     *
     * @var int
     */
    private $rowIndex;

    /**
     * Row height (in pt).
     *
     * When this is set to a negative value, the row height should be ignored by IWriter
     *
     * @var float
     */
    private $height = -1;

    /**
     * ZeroHeight for Row?
     *
     * @var bool
     */
    private $zeroHeight = false;

    /**
     * Create a new RowDimension.
     *
     * @param int $pIndex Numeric row index
     */
    public function __construct($pIndex = 0)
    {
        // Initialise values
        $this->rowIndex = $pIndex;

        // set dimension as unformatted by default
        parent::__construct(null);
    }

    /**
     * Get Row Index.
     *
     * @return int
     */
    public function getRowIndex()
    {
        return $this->rowIndex;
    }

    /**
     * Set Row Index.
     *
     * @param int $pValue
     *
     * @return RowDimension
     */
    public function setRowIndex($pValue)
    {
        $this->rowIndex = $pValue;

        return $this;
    }

    /**
     * Get Row Height.
     *
     * @return float
     */
    public function getRowHeight()
    {
        return $this->height;
    }

    /**
     * Set Row Height.
     *
     * @param float $pValue
     *
     * @return RowDimension
     */
    public function setRowHeight($pValue = -1)
    {
        $this->height = $pValue;

        return $this;
    }

    /**
     * Get ZeroHeight.
     *
     * @return bool
     */
    public function getZeroHeight()
    {
        return $this->zeroHeight;
    }

    /**
     * Set ZeroHeight.
     *
     * @param bool $pValue
     *
     * @return RowDimension
     */
    public function setZeroHeight($pValue = false)
    {
        $this->zeroHeight = $pValue;

        return $this;
    }
}
