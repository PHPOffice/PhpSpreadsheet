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
interface ICache
{
    /**
     * Add or Update a cell in cache identified by coordinate address
     *
     * @param    string            $pCoord        Coordinate address of the cell to update
     * @param    \PhpOffice\PhpSpreadsheet\Cell    $cell        Cell to update
     * @throws   \PhpOffice\PhpSpreadsheet\Exception
     * @return   \PhpOffice\PhpSpreadsheet\Cell
     */
    public function addCacheData($pCoord, \PhpOffice\PhpSpreadsheet\Cell $cell);

    /**
     * Add or Update a cell in cache
     *
     * @param    \PhpOffice\PhpSpreadsheet\Cell    $cell        Cell to update
     * @throws   \PhpOffice\PhpSpreadsheet\Exception
     * @return   \PhpOffice\PhpSpreadsheet\Cell
     */
    public function updateCacheData(\PhpOffice\PhpSpreadsheet\Cell $cell);

    /**
     * Fetch a cell from cache identified by coordinate address
     *
     * @param   string            $pCoord        Coordinate address of the cell to retrieve
     * @throws  \PhpOffice\PhpSpreadsheet\Exception
     * @return  \PhpOffice\PhpSpreadsheet\Cell     Cell that was found, or null if not found
     */
    public function getCacheData($pCoord);

    /**
     * Delete a cell in cache identified by coordinate address
     *
     * @param    string            $pCoord        Coordinate address of the cell to delete
     * @throws   \PhpOffice\PhpSpreadsheet\Exception
     */
    public function deleteCacheData($pCoord);

    /**
     * Is a value set in the current \PhpOffice\PhpSpreadsheet\CachedObjectStorage\ICache for an indexed cell?
     *
     * @param    string        $pCoord        Coordinate address of the cell to check
     * @return    bool
     */
    public function isDataSet($pCoord);

    /**
     * Get a list of all cell addresses currently held in cache
     *
     * @return    string[]
     */
    public function getCellList();

    /**
     * Get the list of all cell addresses currently held in cache sorted by column and row
     *
     * @return    string[]
     */
    public function getSortedCellList();

    /**
     * Clone the cell collection
     *
     * @param  \PhpOffice\PhpSpreadsheet\Worksheet    $parent        The new worksheet that we're copying to
     */
    public function copyCellCollection(\PhpOffice\PhpSpreadsheet\Worksheet $parent);

    /**
     * Identify whether the caching method is currently available
     * Some methods are dependent on the availability of certain extensions being enabled in the PHP build
     *
     * @return    bool
     */
    public static function cacheMethodIsAvailable();
}
