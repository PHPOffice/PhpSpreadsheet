<?php

namespace PhpOffice\PhpSpreadsheet\CachedObjectStorage;

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
abstract class CacheBase
{
    /**
     * Parent worksheet
     *
     * @var \PhpOffice\PhpSpreadsheet\Worksheet
     */
    protected $parent;

    /**
     * The currently active Cell
     *
     * @var \PhpOffice\PhpSpreadsheet\Cell
     */
    protected $currentObject = null;

    /**
     * Coordinate address of the currently active Cell
     *
     * @var string
     */
    protected $currentObjectID = null;

    /**
     * Flag indicating whether the currently active Cell requires saving
     *
     * @var bool
     */
    protected $currentCellIsDirty = true;

    /**
     * An array of cells or cell pointers for the worksheet cells held in this cache,
     *        and indexed by their coordinate address within the worksheet
     *
     * @var array of mixed
     */
    protected $cellCache = [];

    /**
     * Initialise this new cell collection
     *
     * @param    \PhpOffice\PhpSpreadsheet\Worksheet    $parent        The worksheet for this cell collection
     */
    public function __construct(\PhpOffice\PhpSpreadsheet\Worksheet $parent)
    {
        //    Set our parent worksheet.
        //    This is maintained within the cache controller to facilitate re-attaching it to \PhpOffice\PhpSpreadsheet\Cell objects when
        //        they are woken from a serialized state
        $this->parent = $parent;
    }

    /**
     * Return the parent worksheet for this cell collection
     *
     * @return    \PhpOffice\PhpSpreadsheet\Worksheet
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Is a value set in the current \PhpOffice\PhpSpreadsheet\CachedObjectStorage\ICache for an indexed cell?
     *
     * @param    string        $pCoord        Coordinate address of the cell to check
     * @return    bool
     */
    public function isDataSet($pCoord)
    {
        if ($pCoord === $this->currentObjectID) {
            return true;
        }
        //    Check if the requested entry exists in the cache
        return isset($this->cellCache[$pCoord]);
    }

    /**
     * Move a cell object from one address to another
     *
     * @param    string        $fromAddress    Current address of the cell to move
     * @param    string        $toAddress        Destination address of the cell to move
     * @return    bool
     */
    public function moveCell($fromAddress, $toAddress)
    {
        if ($fromAddress === $this->currentObjectID) {
            $this->currentObjectID = $toAddress;
        }
        $this->currentCellIsDirty = true;
        if (isset($this->cellCache[$fromAddress])) {
            $this->cellCache[$toAddress] = &$this->cellCache[$fromAddress];
            unset($this->cellCache[$fromAddress]);
        }

        return true;
    }

    /**
     * Add or Update a cell in cache
     *
     * @param    \PhpOffice\PhpSpreadsheet\Cell    $cell        Cell to update
     * @throws   \PhpOffice\PhpSpreadsheet\Exception
     * @return   \PhpOffice\PhpSpreadsheet\Cell
     */
    public function updateCacheData(\PhpOffice\PhpSpreadsheet\Cell $cell)
    {
        return $this->addCacheData($cell->getCoordinate(), $cell);
    }

    /**
     * Delete a cell in cache identified by coordinate address
     *
     * @param    string            $pCoord        Coordinate address of the cell to delete
     * @throws   \PhpOffice\PhpSpreadsheet\Exception
     */
    public function deleteCacheData($pCoord)
    {
        if ($pCoord === $this->currentObjectID && !is_null($this->currentObject)) {
            $this->currentObject->detach();
            $this->currentObjectID = $this->currentObject = null;
        }

        if (is_object($this->cellCache[$pCoord])) {
            $this->cellCache[$pCoord]->detach();
            unset($this->cellCache[$pCoord]);
        }
        $this->currentCellIsDirty = false;
    }

    /**
     * Get a list of all cell addresses currently held in cache
     *
     * @return    string[]
     */
    public function getCellList()
    {
        return array_keys($this->cellCache);
    }

    /**
     * Sort the list of all cell addresses currently held in cache by row and column
     *
     * @return    string[]
     */
    public function getSortedCellList()
    {
        $sortKeys = [];
        foreach ($this->getCellList() as $coord) {
            sscanf($coord, '%[A-Z]%d', $column, $row);
            $sortKeys[sprintf('%09d%3s', $row, $column)] = $coord;
        }
        ksort($sortKeys);

        return array_values($sortKeys);
    }

    /**
     * Get highest worksheet column and highest row that have cell records
     *
     * @return array Highest column name and highest row number
     */
    public function getHighestRowAndColumn()
    {
        // Lookup highest column and highest row
        $col = ['A' => '1A'];
        $row = [1];
        foreach ($this->getCellList() as $coord) {
            sscanf($coord, '%[A-Z]%d', $c, $r);
            $row[$r] = $r;
            $col[$c] = strlen($c) . $c;
        }
        if (!empty($row)) {
            // Determine highest column and row
            $highestRow = max($row);
            $highestColumn = substr(max($col), 1);
        }

        return [
            'row' => $highestRow,
            'column' => $highestColumn,
        ];
    }

    /**
     * Return the cell address of the currently active cell object
     *
     * @return    string
     */
    public function getCurrentAddress()
    {
        return $this->currentObjectID;
    }

    /**
     * Return the column address of the currently active cell object
     *
     * @return    string
     */
    public function getCurrentColumn()
    {
        sscanf($this->currentObjectID, '%[A-Z]%d', $column, $row);

        return $column;
    }

    /**
     * Return the row address of the currently active cell object
     *
     * @return    int
     */
    public function getCurrentRow()
    {
        sscanf($this->currentObjectID, '%[A-Z]%d', $column, $row);

        return (integer) $row;
    }

    /**
     * Get highest worksheet column
     *
     * @param   string     $row        Return the highest column for the specified row,
     *                                     or the highest column of any row if no row number is passed
     * @return  string     Highest column name
     */
    public function getHighestColumn($row = null)
    {
        if ($row == null) {
            $colRow = $this->getHighestRowAndColumn();

            return $colRow['column'];
        }

        $columnList = [1];
        foreach ($this->getCellList() as $coord) {
            sscanf($coord, '%[A-Z]%d', $c, $r);
            if ($r != $row) {
                continue;
            }
            $columnList[] = \PhpOffice\PhpSpreadsheet\Cell::columnIndexFromString($c);
        }

        return \PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex(max($columnList) - 1);
    }

    /**
     * Get highest worksheet row
     *
     * @param   string     $column     Return the highest row for the specified column,
     *                                     or the highest row of any column if no column letter is passed
     * @return  int        Highest row number
     */
    public function getHighestRow($column = null)
    {
        if ($column == null) {
            $colRow = $this->getHighestRowAndColumn();

            return $colRow['row'];
        }

        $rowList = [0];
        foreach ($this->getCellList() as $coord) {
            sscanf($coord, '%[A-Z]%d', $c, $r);
            if ($c != $column) {
                continue;
            }
            $rowList[] = $r;
        }

        return max($rowList);
    }

    /**
     * Generate a unique ID for cache referencing
     *
     * @return string Unique Reference
     */
    protected function getUniqueID()
    {
        if (function_exists('posix_getpid')) {
            $baseUnique = posix_getpid();
        } else {
            $baseUnique = mt_rand();
        }

        return uniqid($baseUnique, true);
    }

    /**
     * Clone the cell collection
     *
     * @param  \PhpOffice\PhpSpreadsheet\Worksheet    $parent        The new worksheet that we're copying to
     */
    public function copyCellCollection(\PhpOffice\PhpSpreadsheet\Worksheet $parent)
    {
        $this->currentCellIsDirty;
        $this->storeData();

        $this->parent = $parent;
        if (($this->currentObject !== null) && (is_object($this->currentObject))) {
            $this->currentObject->attach($this);
        }
    }

    /**
     * Remove a row, deleting all cells in that row
     *
     * @param string    $row    Row number to remove
     */
    public function removeRow($row)
    {
        foreach ($this->getCellList() as $coord) {
            sscanf($coord, '%[A-Z]%d', $c, $r);
            if ($r == $row) {
                $this->deleteCacheData($coord);
            }
        }
    }

    /**
     * Remove a column, deleting all cells in that column
     *
     * @param string    $column    Column ID to remove
     */
    public function removeColumn($column)
    {
        foreach ($this->getCellList() as $coord) {
            sscanf($coord, '%[A-Z]%d', $c, $r);
            if ($c == $column) {
                $this->deleteCacheData($coord);
            }
        }
    }

    /**
     * Identify whether the caching method is currently available
     * Some methods are dependent on the availability of certain extensions being enabled in the PHP build
     *
     * @return    bool
     */
    public static function cacheMethodIsAvailable()
    {
        return true;
    }
}
