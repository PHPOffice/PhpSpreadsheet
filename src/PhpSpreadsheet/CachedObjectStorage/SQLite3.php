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
class SQLite3 extends CacheBase implements ICache
{
    /**
     * Database table name.
     *
     * @var string
     */
    private $TableName = null;

    /**
     * Database handle.
     *
     * @var resource
     */
    private $DBHandle = null;

    /**
     * Prepared statement for a SQLite3 select query.
     *
     * @var SQLite3Stmt
     */
    private $selectQuery;

    /**
     * Prepared statement for a SQLite3 insert query.
     *
     * @var SQLite3Stmt
     */
    private $insertQuery;

    /**
     * Prepared statement for a SQLite3 update query.
     *
     * @var SQLite3Stmt
     */
    private $updateQuery;

    /**
     * Prepared statement for a SQLite3 delete query.
     *
     * @var SQLite3Stmt
     */
    private $deleteQuery;

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

            $this->insertQuery->bindValue('id', $this->currentObjectID, SQLITE3_TEXT);
            $this->insertQuery->bindValue('data', serialize($this->currentObject), SQLITE3_BLOB);
            $result = $this->insertQuery->execute();
            if ($result === false) {
                throw new \PhpOffice\PhpSpreadsheet\Exception($this->DBHandle->lastErrorMsg());
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

        $this->selectQuery->bindValue('id', $pCoord, SQLITE3_TEXT);
        $cellResult = $this->selectQuery->execute();
        if ($cellResult === false) {
            throw new \PhpOffice\PhpSpreadsheet\Exception($this->DBHandle->lastErrorMsg());
        }
        $cellData = $cellResult->fetchArray(SQLITE3_ASSOC);
        if ($cellData === false) {
            //    Return null if requested entry doesn't exist in cache
            return null;
        }

        //    Set current entry to the requested entry
        $this->currentObjectID = $pCoord;

        $this->currentObject = unserialize($cellData['value']);
        //    Re-attach this as the cell's parent
        $this->currentObject->attach($this);

        //    Return requested entry
        return $this->currentObject;
    }

    /**
     *    Is a value set for an indexed cell?
     *
     * @param string $pCoord Coordinate address of the cell to check
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     *
     * @return bool
     */
    public function isDataSet($pCoord)
    {
        if ($pCoord === $this->currentObjectID) {
            return true;
        }

        //    Check if the requested entry exists in the cache
        $this->selectQuery->bindValue('id', $pCoord, SQLITE3_TEXT);
        $cellResult = $this->selectQuery->execute();
        if ($cellResult === false) {
            throw new \PhpOffice\PhpSpreadsheet\Exception($this->DBHandle->lastErrorMsg());
        }
        $cellData = $cellResult->fetchArray(SQLITE3_ASSOC);

        return ($cellData === false) ? false : true;
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
        if ($pCoord === $this->currentObjectID) {
            $this->currentObject->detach();
            $this->currentObjectID = $this->currentObject = null;
        }

        //    Check if the requested entry exists in the cache
        $this->deleteQuery->bindValue('id', $pCoord, SQLITE3_TEXT);
        $result = $this->deleteQuery->execute();
        if ($result === false) {
            throw new \PhpOffice\PhpSpreadsheet\Exception($this->DBHandle->lastErrorMsg());
        }

        $this->currentCellIsDirty = false;
    }

    /**
     * Move a cell object from one address to another.
     *
     * @param string $fromAddress Current address of the cell to move
     * @param string $toAddress Destination address of the cell to move
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     *
     * @return bool
     */
    public function moveCell($fromAddress, $toAddress)
    {
        if ($fromAddress === $this->currentObjectID) {
            $this->currentObjectID = $toAddress;
        }

        $this->deleteQuery->bindValue('id', $toAddress, SQLITE3_TEXT);
        $result = $this->deleteQuery->execute();
        if ($result === false) {
            throw new \PhpOffice\PhpSpreadsheet\Exception($this->DBHandle->lastErrorMsg());
        }

        $this->updateQuery->bindValue('toid', $toAddress, SQLITE3_TEXT);
        $this->updateQuery->bindValue('fromid', $fromAddress, SQLITE3_TEXT);
        $result = $this->updateQuery->execute();
        if ($result === false) {
            throw new \PhpOffice\PhpSpreadsheet\Exception($this->DBHandle->lastErrorMsg());
        }

        return true;
    }

    /**
     * Get a list of all cell addresses currently held in cache.
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     *
     * @return string[]
     */
    public function getCellList()
    {
        if ($this->currentObjectID !== null) {
            $this->storeData();
        }

        $query = 'SELECT id FROM kvp_' . $this->TableName;
        $cellIdsResult = $this->DBHandle->query($query);
        if ($cellIdsResult === false) {
            throw new \PhpOffice\PhpSpreadsheet\Exception($this->DBHandle->lastErrorMsg());
        }

        $cellKeys = [];
        while ($row = $cellIdsResult->fetchArray(SQLITE3_ASSOC)) {
            $cellKeys[] = $row['id'];
        }

        return $cellKeys;
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
        $this->currentCellIsDirty;
        $this->storeData();

        //    Get a new id for the new table name
        $tableName = str_replace('.', '_', $this->getUniqueID());
        if (!$this->DBHandle->exec('CREATE TABLE kvp_' . $tableName . ' (id VARCHAR(12) PRIMARY KEY, value BLOB)
            AS SELECT * FROM kvp_' . $this->TableName)
        ) {
            throw new \PhpOffice\PhpSpreadsheet\Exception($this->DBHandle->lastErrorMsg());
        }

        //    Copy the existing cell cache file
        $this->TableName = $tableName;
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
        //    detach ourself from the worksheet, so that it can then delete this object successfully
        $this->parent = null;

        //    Close down the temporary cache file
        $this->__destruct();
    }

    /**
     * Initialise this new cell collection.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $parent The worksheet for this cell collection
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function __construct(\PhpOffice\PhpSpreadsheet\Worksheet $parent)
    {
        parent::__construct($parent);
        if (is_null($this->DBHandle)) {
            $this->TableName = str_replace('.', '_', $this->getUniqueID());
            $_DBName = ':memory:';

            $this->DBHandle = new \SQLite3($_DBName);
            if ($this->DBHandle === false) {
                throw new \PhpOffice\PhpSpreadsheet\Exception($this->DBHandle->lastErrorMsg());
            }
            if (!$this->DBHandle->exec('CREATE TABLE kvp_' . $this->TableName . ' (id VARCHAR(12) PRIMARY KEY, value BLOB)')) {
                throw new \PhpOffice\PhpSpreadsheet\Exception($this->DBHandle->lastErrorMsg());
            }
        }

        $this->selectQuery = $this->DBHandle->prepare('SELECT value FROM kvp_' . $this->TableName . ' WHERE id = :id');
        $this->insertQuery = $this->DBHandle->prepare('INSERT OR REPLACE INTO kvp_' . $this->TableName . ' VALUES(:id,:data)');
        $this->updateQuery = $this->DBHandle->prepare('UPDATE kvp_' . $this->TableName . ' SET id=:toId WHERE id=:fromId');
        $this->deleteQuery = $this->DBHandle->prepare('DELETE FROM kvp_' . $this->TableName . ' WHERE id = :id');
    }

    /**
     * Destroy this cell collection.
     */
    public function __destruct()
    {
        if (!is_null($this->DBHandle)) {
            $this->DBHandle->exec('DROP TABLE kvp_' . $this->TableName);
            $this->DBHandle->close();
        }
        $this->DBHandle = null;
    }

    /**
     * Identify whether the caching method is currently available
     * Some methods are dependent on the availability of certain extensions being enabled in the PHP build.
     *
     * @return bool
     */
    public static function cacheMethodIsAvailable()
    {
        if (!class_exists('SQLite3', false)) {
            return false;
        }

        return true;
    }
}
