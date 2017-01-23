<?php

namespace PhpOffice\PhpSpreadsheet\CachedObjectStorage;

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
class APC extends CacheBase implements ICache
{
    /**
     * Prefix used to uniquely identify cache data for this worksheet.
     *
     * @var string
     */
    private $cachePrefix = null;

    /**
     * Cache timeout.
     *
     * @var int
     */
    private $cacheTime = 600;

    /**
     * Store cell data in cache for the current cell object if it's "dirty",
     * and the 'nullify' the current cell object.
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function storeData()
    {
        if ($this->currentCellIsDirty && !empty($this->currentObjectID)) {
            $this->currentObject->detach();

            if (!apc_store(
                $this->cachePrefix . $this->currentObjectID . '.cache',
                serialize($this->currentObject),
                $this->cacheTime
            )) {
                $this->__destruct();
                throw new \PhpOffice\PhpSpreadsheet\Exception('Failed to store cell ' . $this->currentObjectID . ' in APC');
            }
            $this->currentCellIsDirty = false;
        }
        $this->currentObjectID = $this->currentObject = null;
    }

    /**
     * Add or Update a cell in cache identified by coordinate address.
     *
     * @param string $pCoord Coordinate address of the cell to update
     * @param \PhpOffice\PhpSpreadsheet\Cell $cell Cell to update
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     *
     * @return \PhpOffice\PhpSpreadsheet\Cell
     */
    public function addCacheData($pCoord, \PhpOffice\PhpSpreadsheet\Cell $cell)
    {
        if (($pCoord !== $this->currentObjectID) && ($this->currentObjectID !== null)) {
            $this->storeData();
        }
        $this->cellCache[$pCoord] = true;

        $this->currentObjectID = $pCoord;
        $this->currentObject = $cell;
        $this->currentCellIsDirty = true;

        return $cell;
    }

    /**
     * Is a value set in the current \PhpOffice\PhpSpreadsheet\CachedObjectStorage\ICache for an indexed cell?
     *
     * @param string $pCoord Coordinate address of the cell to check
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     *
     * @return bool
     */
    public function isDataSet($pCoord)
    {
        //    Check if the requested entry is the current object, or exists in the cache
        if (parent::isDataSet($pCoord)) {
            if ($this->currentObjectID == $pCoord) {
                return true;
            }
            //    Check if the requested entry still exists in apc
            $success = apc_fetch($this->cachePrefix . $pCoord . '.cache');
            if ($success === false) {
                //    Entry no longer exists in APC, so clear it from the cache array
                parent::deleteCacheData($pCoord);
                throw new \PhpOffice\PhpSpreadsheet\Exception('Cell entry ' . $pCoord . ' no longer exists in APC cache');
            }

            return true;
        }

        return false;
    }

    /**
     * Get cell at a specific coordinate.
     *
     * @param string $pCoord Coordinate of the cell
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     *
     * @return \PhpOffice\PhpSpreadsheet\Cell Cell that was found, or null if not found
     */
    public function getCacheData($pCoord)
    {
        if ($pCoord === $this->currentObjectID) {
            return $this->currentObject;
        }
        $this->storeData();

        //    Check if the entry that has been requested actually exists
        if (parent::isDataSet($pCoord)) {
            $obj = apc_fetch($this->cachePrefix . $pCoord . '.cache');
            if ($obj === false) {
                //    Entry no longer exists in APC, so clear it from the cache array
                parent::deleteCacheData($pCoord);
                throw new \PhpOffice\PhpSpreadsheet\Exception('Cell entry ' . $pCoord . ' no longer exists in APC cache');
            }
        } else {
            //    Return null if requested entry doesn't exist in cache
            return null;
        }

        //    Set current entry to the requested entry
        $this->currentObjectID = $pCoord;
        $this->currentObject = unserialize($obj);
        //    Re-attach this as the cell's parent
        $this->currentObject->attach($this);

        //    Return requested entry
        return $this->currentObject;
    }

    /**
     * Get a list of all cell addresses currently held in cache.
     *
     * @return string[]
     */
    public function getCellList()
    {
        if ($this->currentObjectID !== null) {
            $this->storeData();
        }

        return parent::getCellList();
    }

    /**
     * Delete a cell in cache identified by coordinate address.
     *
     * @param string $pCoord Coordinate address of the cell to delete
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function deleteCacheData($pCoord)
    {
        //    Delete the entry from APC
        apc_delete($this->cachePrefix . $pCoord . '.cache');

        //    Delete the entry from our cell address array
        parent::deleteCacheData($pCoord);
    }

    /**
     * Clone the cell collection.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $parent The new worksheet that we're copying to
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function copyCellCollection(\PhpOffice\PhpSpreadsheet\Worksheet $parent)
    {
        parent::copyCellCollection($parent);
        //    Get a new id for the new file name
        $baseUnique = $this->getUniqueID();
        $newCachePrefix = substr(md5($baseUnique), 0, 8) . '.';
        $cacheList = $this->getCellList();
        foreach ($cacheList as $cellID) {
            if ($cellID != $this->currentObjectID) {
                $obj = apc_fetch($this->cachePrefix . $cellID . '.cache');
                if ($obj === false) {
                    //    Entry no longer exists in APC, so clear it from the cache array
                    parent::deleteCacheData($cellID);
                    throw new \PhpOffice\PhpSpreadsheet\Exception('Cell entry ' . $cellID . ' no longer exists in APC');
                }
                if (!apc_store($newCachePrefix . $cellID . '.cache', $obj, $this->cacheTime)) {
                    $this->__destruct();
                    throw new \PhpOffice\PhpSpreadsheet\Exception('Failed to store cell ' . $cellID . ' in APC');
                }
            }
        }
        $this->cachePrefix = $newCachePrefix;
    }

    /**
     * Clear the cell collection and disconnect from our parent.
     */
    public function unsetWorksheetCells()
    {
        if ($this->currentObject !== null) {
            $this->currentObject->detach();
            $this->currentObject = $this->currentObjectID = null;
        }

        //    Flush the APC cache
        $this->__destruct();

        $this->cellCache = [];

        //    detach ourself from the worksheet, so that it can then delete this object successfully
        $this->parent = null;
    }

    /**
     * Initialise this new cell collection.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $parent The worksheet for this cell collection
     * @param array $arguments Additional initialisation arguments
     */
    public function __construct(\PhpOffice\PhpSpreadsheet\Worksheet $parent, $arguments)
    {
        $cacheTime = (isset($arguments['cacheTime'])) ? $arguments['cacheTime'] : 600;

        if ($this->cachePrefix === null) {
            $baseUnique = $this->getUniqueID();
            $this->cachePrefix = substr(md5($baseUnique), 0, 8) . '.';
            $this->cacheTime = $cacheTime;

            parent::__construct($parent);
        }
    }

    /**
     * Destroy this cell collection.
     */
    public function __destruct()
    {
        $cacheList = $this->getCellList();
        foreach ($cacheList as $cellID) {
            apc_delete($this->cachePrefix . $cellID . '.cache');
        }
    }

    /**
     * Identify whether the caching method is currently available
     * Some methods are dependent on the availability of certain extensions being enabled in the PHP build.
     *
     * @return bool
     */
    public static function cacheMethodIsAvailable()
    {
        if (!function_exists('apc_store')) {
            return false;
        }
        if (apc_sma_info() === false) {
            return false;
        }

        return true;
    }
}
