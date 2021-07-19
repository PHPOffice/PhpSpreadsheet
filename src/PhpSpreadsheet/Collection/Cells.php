<?php

namespace PhpOffice\PhpSpreadsheet\Collection;

use Generator;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Psr\SimpleCache\CacheInterface;

class Cells
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * Parent worksheet.
     *
     * @var null|Worksheet
     */
    private $parent;

    /**
     * The currently active Cell.
     *
     * @var null|Cell
     */
    private $currentCell;

    /**
     * Coordinate of the currently active Cell.
     *
     * @var null|string
     */
    private $currentCoordinate;

    /**
     * Flag indicating whether the currently active Cell requires saving.
     *
     * @var bool
     */
    private $currentCellIsDirty = false;

    /**
     * An index of existing cells. Cells indexed by 4-bytes (64 cells) blocks.
     *
     * @var int[][]
     */
    private $index = [];

    /**
     * Prefix used to uniquely identify cache data for this worksheet.
     *
     * @var string
     */
    private $cachePrefix;

    /**
     * Initialise this new cell collection.
     *
     * @param Worksheet $parent The worksheet for this cell collection
     */
    public function __construct(Worksheet $parent, CacheInterface $cache)
    {
        // Set our parent worksheet.
        // This is maintained here to facilitate re-attaching it to Cell objects when
        // they are woken from a serialized state
        $this->parent = $parent;
        $this->cache = $cache;
        $this->cachePrefix = $this->getUniqueID();
    }

    /**
     * Return the parent worksheet for this cell collection.
     *
     * @return Worksheet
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Whether the collection holds a cell for the given coordinate.
     *
     * @param string $pCoord Coordinate of the cell to check
     *
     * @return bool
     */
    public function has($pCoord)
    {
        if ($pCoord === $this->currentCoordinate) {
            return true;
        }

        $column = '';
        $row = 0;
        sscanf($pCoord, '%[A-Z]%d', $column, $row);

        $cellBlock = (int)(($row - 1) / 64);
        if (!isset($this->index[$column][$cellBlock])) {
            return false;
        }

        // Check if the requested entry exists in the index
        return $this->index[$column][$cellBlock] & (1 << (($row - 1) % 64));
    }

    /**
     * Add or update a cell in the collection.
     *
     * @param Cell $cell Cell to update
     *
     * @return Cell
     */
    public function update(Cell $cell)
    {
        return $this->add($cell->getCoordinate(), $cell);
    }

    /**
     * Delete a cell in cache identified by coordinate.
     *
     * @param string $pCoord Coordinate of the cell to delete
     */
    public function delete($pCoord): void
    {
        if ($pCoord === $this->currentCoordinate && $this->currentCell !== null) {
            $this->currentCell->detach();
            $this->currentCoordinate = null;
            $this->currentCell = null;
            $this->currentCellIsDirty = false;
        }

        $column = '';
        $row = 0;
        sscanf($pCoord, '%[A-Z]%d', $column, $row);

        $cellBlock = (int)(($row - 1) / 64);
        if (!isset($this->index[$column][$cellBlock])) {
            return;
        }

        // Check if the requested entry exists in the index
        $this->index[$column][$cellBlock] &= ~(1 << (($row - 1) % 64));

        // Clean index
        if ($this->index[$column][$cellBlock] === 0) {
            unset($this->index[$column][$cellBlock]);
            if (empty($this->index[$column])) {
                unset($this->index[$column]);
            }
        }

        // Delete the entry from cache
        $this->cache->delete($this->cachePrefix . $pCoord);
    }

    /**
     * Get a list of all cell coordinates currently held in the collection.
     *
     * @return string[]
     */
    public function getCoordinates()
    {
        foreach ($this->index as $column => $cellBlocks) {
            foreach ($cellBlocks as $cellBlockId => $cellBlock) {
                foreach (preg_split('//u', strrev(sprintf('%064b', $cellBlock)), -1, PREG_SPLIT_NO_EMPTY) as $pos => $char) {
                    if ($char === "0") {
                        continue;
                    }
                    
                    yield $column . ($cellBlockId * 64 + $pos + 1);
                }
            }
        }
    }

    /**
     * Get a sorted list of all cell coordinates currently held in the collection by row and column.
     *
     * @return string[]
     */
    public function getSortedCoordinates()
    {
        $existingBlocksIds = [];
        foreach ($this->index as $cellBlocks) {
            foreach ($cellBlocks as $cellBlockId => $cellBlock) {
                $existingBlocksIds[$cellBlockId] = true;
            }
        }

        foreach ($existingBlocksIds as $blocksId => $v) {
            for ($offset = 0; $offset < 64; $offset++) {
                foreach ($this->index as $column => $cellBlocks) {
                    if (!isset($cellBlocks[$blocksId])) {
                        continue;
                    }
                    
                    if (($cellBlocks[$blocksId] >> $offset) & 1) {
                        yield $column . ($blocksId * 64 + $offset + 1);
                    }
                }
            }
        }
    }

    /**
     * Get highest worksheet column and highest row that have cell records.
     *
     * @return array Highest column name and highest row number
     */
    public function getHighestRowAndColumn()
    {
        // Lookup highest column and highest row
        $highestRow = 1;
        $highestColumn = '1A';
        foreach ($this->getCoordinates() as $coord) {
            $c = '';
            $r = 0;
            sscanf($coord, '%[A-Z]%d', $c, $r);
            if ($r > $highestRow) {
                $highestRow = $r;
            }
            $currentCol = strlen($c) . $c;
            if ($currentCol > $highestColumn) {
                $highestColumn = $currentCol;
            }
        }

        // Determine highest column and row
        $highestColumn = substr($highestColumn, 1);

        return [
            'row' => $highestRow,
            'column' => $highestColumn,
        ];
    }

    /**
     * Return the cell coordinate of the currently active cell object.
     *
     * @return string
     */
    public function getCurrentCoordinate()
    {
        return $this->currentCoordinate;
    }

    /**
     * Return the column coordinate of the currently active cell object.
     *
     * @return string
     */
    public function getCurrentColumn()
    {
        $column = '';
        $row = 0;

        sscanf($this->currentCoordinate, '%[A-Z]%d', $column, $row);

        return $column;
    }

    /**
     * Return the row coordinate of the currently active cell object.
     *
     * @return int
     */
    public function getCurrentRow()
    {
        $column = '';
        $row = 0;

        sscanf($this->currentCoordinate, '%[A-Z]%d', $column, $row);

        return (int) $row;
    }

    /**
     * Get highest worksheet column.
     *
     * @param string $row Return the highest column for the specified row,
     *                    or the highest column of any row if no row number is passed
     *
     * @return string Highest column name
     */
    public function getHighestColumn($row = null)
    {
        if ($row === null) {
            $colRow = $this->getHighestRowAndColumn();

            return $colRow['column'];
        }

        $maxColId = 1;
        foreach ($this->getCoordinates() as $coord) {
            $c = '';
            $r = 0;

            sscanf($coord, '%[A-Z]%d', $c, $r);
            if ($r != $row) {
                continue;
            }
            $colId =  Coordinate::columnIndexFromString($c);
            if ($colId > $maxColId) {
                $maxColId = $colId;
            }
        }

        return Coordinate::stringFromColumnIndex($maxColId);
    }

    /**
     * Get highest worksheet row.
     *
     * @param string $column Return the highest row for the specified column,
     *                       or the highest row of any column if no column letter is passed
     *
     * @return int Highest row number
     */
    public function getHighestRow($column = null)
    {
        if ($column === null) {
            $colRow = $this->getHighestRowAndColumn();

            return $colRow['row'];
        }

        $maxRow = 0;
        foreach ($this->getCoordinates() as $coord) {
            $c = '';
            $r = 0;

            sscanf($coord, '%[A-Z]%d', $c, $r);
            if ($c != $column) {
                continue;
            }
            if ($r > $maxRow) {
                $maxRow = $r;
            }
        }

        return $maxRow;
    }

    /**
     * Generate a unique ID for cache referencing.
     *
     * @return string Unique Reference
     */
    private function getUniqueID()
    {
        return uniqid('phpspreadsheet.', true) . '.';
    }

    /**
     * Clone the cell collection.
     *
     * @param Worksheet $parent The new worksheet that we're copying to
     *
     * @return self
     */
    public function cloneCellCollection(Worksheet $parent)
    {
        $this->storeCurrentCell();
        $newCollection = clone $this;

        $newCollection->parent = $parent;
        if (($newCollection->currentCell !== null) && (is_object($newCollection->currentCell))) {
            $newCollection->currentCell->attach($this);
        }

        // Get old values
        $oldKeys = $newCollection->getAllCacheKeys();
        $oldValues = $newCollection->cache->getMultiple($oldKeys);
        $newValues = [];
        $oldCachePrefix = $newCollection->cachePrefix;

        // Change prefix
        $newCollection->cachePrefix = $newCollection->getUniqueID();
        foreach ($oldValues as $oldKey => $value) {
            $newValues[str_replace($oldCachePrefix, $newCollection->cachePrefix, $oldKey)] = clone $value;
        }

        // Store new values
        $stored = $newCollection->cache->setMultiple($newValues);
        if (!$stored) {
            $newCollection->__destruct();

            throw new PhpSpreadsheetException('Failed to copy cells in cache');
        }

        return $newCollection;
    }

    /**
     * Remove a row, deleting all cells in that row.
     *
     * @param string $row Row number to remove
     */
    public function removeRow($row): void
    {
        foreach ($this->getCoordinates() as $coord) {
            $c = '';
            $r = 0;

            sscanf($coord, '%[A-Z]%d', $c, $r);
            if ($r == $row) {
                $this->delete($coord);
            }
        }
    }

    /**
     * Remove a column, deleting all cells in that column.
     *
     * @param string $column Column ID to remove
     */
    public function removeColumn($column): void
    {
        foreach ($this->getCoordinates() as $coord) {
            $c = '';
            $r = 0;

            sscanf($coord, '%[A-Z]%d', $c, $r);
            if ($c == $column) {
                $this->delete($coord);
            }
        }
    }

    /**
     * Store cell data in cache for the current cell object if it's "dirty",
     * and the 'nullify' the current cell object.
     */
    private function storeCurrentCell(): void
    {
        if ($this->currentCellIsDirty && !empty($this->currentCoordinate)) {
            $this->currentCell->detach();

            $stored = $this->cache->set($this->cachePrefix . $this->currentCoordinate, $this->currentCell);
            if (!$stored) {
                $this->__destruct();

                throw new PhpSpreadsheetException("Failed to store cell {$this->currentCoordinate} in cache");
            }
            $this->currentCellIsDirty = false;
        }

        $this->currentCoordinate = null;
        $this->currentCell = null;
    }

    /**
     * Add or update a cell identified by its coordinate into the collection.
     *
     * @param string $pCoord Coordinate of the cell to update
     * @param Cell $cell Cell to update
     *
     * @return Cell
     */
    public function add($pCoord, Cell $cell)
    {
        if ($pCoord !== $this->currentCoordinate) {
            $this->storeCurrentCell();
        }
        
        $column = '';
        $row = 0;
        sscanf($pCoord, '%[A-Z]%d', $column, $row);
        
        $cellBlock = (int)(($row - 1) / 64);
        if (!isset($this->index[$column][$cellBlock])) {
            $this->index[$column][$cellBlock] = 0;
        }
        $this->index[$column][$cellBlock] |= (1 << (($row - 1) % 64));

        $this->currentCoordinate = $pCoord;
        $this->currentCell = $cell;
        $this->currentCellIsDirty = true;

        return $cell;
    }

    /**
     * Get cell at a specific coordinate.
     *
     * @param string $pCoord Coordinate of the cell
     *
     * @return null|Cell Cell that was found, or null if not found
     */
    public function get($pCoord)
    {
        if ($pCoord === $this->currentCoordinate) {
            return $this->currentCell;
        }
        $this->storeCurrentCell();

        // Return null if requested entry doesn't exist in collection
        if (!$this->has($pCoord)) {
            return null;
        }

        // Check if the entry that has been requested actually exists
        $cell = $this->cache->get($this->cachePrefix . $pCoord);
        if ($cell === null) {
            throw new PhpSpreadsheetException("Cell entry {$pCoord} no longer exists in cache. This probably means that the cache was cleared by someone else.");
        }

        // Set current entry to the requested entry
        $this->currentCoordinate = $pCoord;
        $this->currentCell = $cell;
        // Re-attach this as the cell's parent
        $this->currentCell->attach($this);

        // Return requested entry
        return $this->currentCell;
    }

    /**
     * Clear the cell collection and disconnect from our parent.
     */
    public function unsetWorksheetCells(): void
    {
        if ($this->currentCell !== null) {
            $this->currentCell->detach();
            $this->currentCell = null;
            $this->currentCoordinate = null;
        }

        // Flush the cache
        $this->__destruct();

        $this->index = [];

        // detach ourself from the worksheet, so that it can then delete this object successfully
        $this->parent = null;
    }

    /**
     * Destroy this cell collection.
     */
    public function __destruct()
    {
        $this->cache->deleteMultiple($this->getAllCacheKeys());
    }

    /**
     * Returns all known cache keys.
     *
     * @return Generator|string[]
     */
    private function getAllCacheKeys()
    {
        foreach ($this->getCoordinates() as $coordinate) {
            yield $this->cachePrefix . $coordinate;
        }
    }
}
