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
class DiscISAM extends CacheBase implements ICache
{
    /**
     * Name of the file for this cache.
     *
     * @var string
     */
    private $fileName = null;

    /**
     * File handle for this cache file.
     *
     * @var resource
     */
    private $fileHandle = null;

    /**
     * Directory/Folder where the cache file is located.
     *
     * @var string
     */
    private $cacheDirectory = null;

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

            fseek($this->fileHandle, 0, SEEK_END);

            $this->cellCache[$this->currentObjectID] = [
                'ptr' => ftell($this->fileHandle),
                'sz' => fwrite($this->fileHandle, serialize($this->currentObject)),
            ];
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

        $this->currentObjectID = $pCoord;
        $this->currentObject = $cell;
        $this->currentCellIsDirty = true;

        return $cell;
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
        if (!isset($this->cellCache[$pCoord])) {
            //    Return null if requested entry doesn't exist in cache
            return null;
        }

        //    Set current entry to the requested entry
        $this->currentObjectID = $pCoord;
        fseek($this->fileHandle, $this->cellCache[$pCoord]['ptr']);
        $this->currentObject = unserialize(fread($this->fileHandle, $this->cellCache[$pCoord]['sz']));
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
     * Clone the cell collection.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $parent The new worksheet that we're copying to
     */
    public function copyCellCollection(\PhpOffice\PhpSpreadsheet\Worksheet $parent)
    {
        parent::copyCellCollection($parent);
        //    Get a new id for the new file name
        $baseUnique = $this->getUniqueID();
        $newFileName = $this->cacheDirectory . '/PhpSpreadsheet.' . $baseUnique . '.cache';
        //    Copy the existing cell cache file
        copy($this->fileName, $newFileName);
        $this->fileName = $newFileName;
        //    Open the copied cell cache file
        $this->fileHandle = fopen($this->fileName, 'a+');
    }

    /**
     * Clear the cell collection and disconnect from our parent.
     */
    public function unsetWorksheetCells()
    {
        if (!is_null($this->currentObject)) {
            $this->currentObject->detach();
            $this->currentObject = $this->currentObjectID = null;
        }
        $this->cellCache = [];

        //    detach ourself from the worksheet, so that it can then delete this object successfully
        $this->parent = null;

        //    Close down the temporary cache file
        $this->__destruct();
    }

    /**
     * Initialise this new cell collection.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $parent The worksheet for this cell collection
     * @param array of mixed $arguments    Additional initialisation arguments
     */
    public function __construct(\PhpOffice\PhpSpreadsheet\Worksheet $parent, $arguments)
    {
        $this->cacheDirectory = ((isset($arguments['dir'])) && ($arguments['dir'] !== null))
                                    ? $arguments['dir']
                                    : \PhpOffice\PhpSpreadsheet\Shared\File::sysGetTempDir();

        parent::__construct($parent);
        if (is_null($this->fileHandle)) {
            $baseUnique = $this->getUniqueID();
            $this->fileName = $this->cacheDirectory . '/PhpSpreadsheet.' . $baseUnique . '.cache';
            $this->fileHandle = fopen($this->fileName, 'a+');
        }
    }

    /**
     * Destroy this cell collection.
     */
    public function __destruct()
    {
        if (!is_null($this->fileHandle)) {
            fclose($this->fileHandle);
            unlink($this->fileName);
        }
        $this->fileHandle = null;
    }
}
