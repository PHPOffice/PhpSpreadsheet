<?php

namespace PhpOffice\PhpSpreadsheet\Collection;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Psr\SimpleCache\CacheInterface;

class Cells
{
    protected const MAX_COLUMN_ID = 16384;

    private CacheInterface $cache;

    /**
     * Parent worksheet.
     */
    private ?Worksheet $parent;

    /**
     * The currently active Cell.
     */
    private ?Cell $currentCell = null;

    /**
     * Coordinate of the currently active Cell.
     */
    private ?string $currentCoordinate = null;

    /**
     * Flag indicating whether the currently active Cell requires saving.
     */
    private bool $currentCellIsDirty = false;

    /**
     * An index of existing cells. int pointer to the coordinate (0-base-indexed row * 16,384 + 1-base indexed column)
     *    indexed by their coordinate.
     *
     * @var int[]
     */
    private array $index = [];

    /**
     * Flag to avoid sorting the index every time.
     */
    private bool $indexSorted = false;

    /**
     * Index keys cache to avoid recalculating on large arrays.
     *
     * @var null|string[]
     */
    private ?array $indexKeysCache = null;

    /**
     * Index values cache to avoid recalculating on large arrays.
     *
     * @var null|int[]
     */
    private ?array $indexValuesCache = null;

    /**
     * Prefix used to uniquely identify cache data for this worksheet.
     */
    private string $cachePrefix;

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
     */
    public function getParent(): ?Worksheet
    {
        return $this->parent;
    }

    /**
     * Whether the collection holds a cell for the given coordinate.
     *
     * @param string $cellCoordinate Coordinate of the cell to check
     */
    public function has(string $cellCoordinate): bool
    {
        return ($cellCoordinate === $this->currentCoordinate) || isset($this->index[$cellCoordinate]);
    }

    public function has2(string $cellCoordinate): bool
    {
        return isset($this->index[$cellCoordinate]);
    }

    /**
     * Add or update a cell in the collection.
     *
     * @param Cell $cell Cell to update
     */
    public function update(Cell $cell): Cell
    {
        return $this->add($cell->getCoordinate(), $cell);
    }

    /**
     * Delete a cell in cache identified by coordinate.
     *
     * @param string $cellCoordinate Coordinate of the cell to delete
     */
    public function delete(string $cellCoordinate): void
    {
        if ($cellCoordinate === $this->currentCoordinate && $this->currentCell !== null) {
            $this->currentCell->detach();
            $this->currentCoordinate = null;
            $this->currentCell = null;
            $this->currentCellIsDirty = false;
        }

        unset($this->index[$cellCoordinate]);

        // Clear index caches
        $this->indexKeysCache = null;
        $this->indexValuesCache = null;

        // Delete the entry from cache
        $this->cache->delete($this->cachePrefix . $cellCoordinate);
    }

    /**
     * Get a list of all cell coordinates currently held in the collection.
     *
     * @return string[]
     */
    public function getCoordinates(): array
    {
        // Build or rebuild index keys cache
        if ($this->indexKeysCache === null) {
            $this->indexKeysCache = array_keys($this->index);
        }

        return $this->indexKeysCache;
    }

    /**
     * Get a sorted list of all cell coordinates currently held in the collection by row and column.
     *
     * @return string[]
     */
    public function getSortedCoordinates(): array
    {
        // Sort only when required
        if (!$this->indexSorted) {
            asort($this->index);
            $this->indexSorted = true;
            // Clear unsorted cache
            $this->indexKeysCache = null;
            $this->indexValuesCache = null;
        }

        // Build or rebuild index keys cache
        if ($this->indexKeysCache === null) {
            $this->indexKeysCache = array_keys($this->index);
        }

        return $this->indexKeysCache;
    }

    /**
     * Get a sorted list of all cell coordinates currently held in the collection by index (16384*row+column).
     *
     * @return int[]
     */
    public function getSortedCoordinatesInt(): array
    {
        if (!$this->indexSorted) {
            asort($this->index);
            $this->indexSorted = true;
            // Clear unsorted cache
            $this->indexKeysCache = null;
            $this->indexValuesCache = null;
        }

        if ($this->indexValuesCache === null) {
            $this->indexValuesCache = array_values($this->index);
        }

        return $this->indexValuesCache;
    }

    /**
     * Return the cell coordinate of the currently active cell object.
     */
    public function getCurrentCoordinate(): ?string
    {
        return $this->currentCoordinate;
    }

    /**
     * Return the column coordinate of the currently active cell object.
     */
    public function getCurrentColumn(): string
    {
        $column = 0;
        $row = '';
        sscanf($this->currentCoordinate ?? '', '%[A-Z]%d', $column, $row);

        return (string) $column;
    }

    /**
     * Return the row coordinate of the currently active cell object.
     */
    public function getCurrentRow(): int
    {
        $column = 0;
        $row = '';
        sscanf($this->currentCoordinate ?? '', '%[A-Z]%d', $column, $row);

        return (int) $row;
    }

    /**
     * Get highest worksheet column and highest row that have cell records.
     *
     * @return array{row: int, column: string} Highest column name and highest row number
     */
    public function getHighestRowAndColumn(): array
    {
        // Lookup highest column and highest row
        $maxRow = $maxColumn = 1;
        foreach ($this->index as $coordinate) {
            $row = (int) floor(($coordinate - 1) / self::MAX_COLUMN_ID) + 1;
            $maxRow = ($maxRow > $row) ? $maxRow : $row;
            $column = ($coordinate % self::MAX_COLUMN_ID) ?: self::MAX_COLUMN_ID;
            $maxColumn = ($maxColumn > $column) ? $maxColumn : $column;
        }

        return [
            'row' => $maxRow,
            'column' => Coordinate::stringFromColumnIndex($maxColumn),
        ];
    }

    /**
     * Get highest worksheet column.
     *
     * @param null|int|string $row Return the highest column for the specified row,
     *                    or the highest column of any row if no row number is passed
     *
     * @return string Highest column name
     */
    public function getHighestColumn($row = null): string
    {
        if ($row === null) {
            return $this->getHighestRowAndColumn()['column'];
        }

        $row = (int) $row;
        if ($row <= 0) {
            throw new PhpSpreadsheetException('Row number must be a positive integer');
        }

        $maxColumn = 1;
        $toRow = $row * self::MAX_COLUMN_ID;
        $fromRow = --$row * self::MAX_COLUMN_ID;
        foreach ($this->index as $coordinate) {
            if ($coordinate < $fromRow || $coordinate >= $toRow) {
                continue;
            }
            $column = ($coordinate % self::MAX_COLUMN_ID) ?: self::MAX_COLUMN_ID;
            $maxColumn = $maxColumn > $column ? $maxColumn : $column;
        }

        return Coordinate::stringFromColumnIndex($maxColumn);
    }

    /**
     * Get highest worksheet row.
     *
     * @param null|string $column Return the highest row for the specified column,
     *                       or the highest row of any column if no column letter is passed
     *
     * @return int Highest row number
     */
    public function getHighestRow(?string $column = null): int
    {
        if ($column === null) {
            return $this->getHighestRowAndColumn()['row'];
        }

        $maxRow = 1;
        $columnIndex = Coordinate::columnIndexFromString($column);
        foreach ($this->index as $coordinate) {
            if ($coordinate % self::MAX_COLUMN_ID !== $columnIndex) {
                continue;
            }
            $row = (int) floor($coordinate / self::MAX_COLUMN_ID) + 1;
            $maxRow = ($maxRow > $row) ? $maxRow : $row;
        }

        return $maxRow;
    }

    /**
     * Generate a unique ID for cache referencing.
     *
     * @return string Unique Reference
     */
    private function getUniqueID(): string
    {
        $cacheType = Settings::getCache();

        return ($cacheType instanceof Memory\SimpleCache1 || $cacheType instanceof Memory\SimpleCache3)
            ? random_bytes(7) . ':'
            : uniqid('phpspreadsheet.', true) . '.';
    }

    /**
     * Clone the cell collection.
     */
    public function cloneCellCollection(Worksheet $worksheet): static
    {
        $this->storeCurrentCell();
        $newCollection = clone $this;

        $newCollection->parent = $worksheet;
        $newCollection->cachePrefix = $newCollection->getUniqueID();

        foreach ($this->index as $key => $value) {
            $newCollection->index[$key] = $value;
            $stored = $newCollection->cache->set(
                $newCollection->cachePrefix . $key,
                clone $this->getCache($key)
            );
            if ($stored === false) {
                $this->destructIfNeeded($newCollection, 'Failed to copy cells in cache');
            }
        }

        // Clear index sorted flag and index caches
        $newCollection->indexSorted = false;
        $newCollection->indexKeysCache = null;
        $newCollection->indexValuesCache = null;

        return $newCollection;
    }

    /**
     * Remove a row, deleting all cells in that row.
     *
     * @param int|string $row Row number to remove
     */
    public function removeRow($row): void
    {
        $this->storeCurrentCell();
        $row = (int) $row;
        if ($row <= 0) {
            throw new PhpSpreadsheetException('Row number must be a positive integer');
        }

        $toRow = $row * self::MAX_COLUMN_ID;
        $fromRow = --$row * self::MAX_COLUMN_ID;
        foreach ($this->index as $coordinate) {
            if ($coordinate >= $fromRow && $coordinate < $toRow) {
                $row = (int) floor($coordinate / self::MAX_COLUMN_ID) + 1;
                $column = Coordinate::stringFromColumnIndex($coordinate % self::MAX_COLUMN_ID);
                $this->delete("{$column}{$row}");
            }
        }
    }

    /**
     * Remove a column, deleting all cells in that column.
     *
     * @param string $column Column ID to remove
     */
    public function removeColumn(string $column): void
    {
        $this->storeCurrentCell();

        $columnIndex = Coordinate::columnIndexFromString($column);
        foreach ($this->index as $coordinate) {
            if ($coordinate % self::MAX_COLUMN_ID === $columnIndex) {
                $row = (int) floor($coordinate / self::MAX_COLUMN_ID) + 1;
                $column = Coordinate::stringFromColumnIndex($coordinate % self::MAX_COLUMN_ID);
                $this->delete("{$column}{$row}");
            }
        }
    }

    /**
     * Store cell data in cache for the current cell object if it's "dirty",
     * and the 'nullify' the current cell object.
     */
    private function storeCurrentCell(): void
    {
        if ($this->currentCellIsDirty && isset($this->currentCoordinate, $this->currentCell)) {
            $this->currentCell->detach();

            $stored = $this->cache->set($this->cachePrefix . $this->currentCoordinate, $this->currentCell);
            if ($stored === false) {
                $this->destructIfNeeded($this, "Failed to store cell {$this->currentCoordinate} in cache");
            }
            $this->currentCellIsDirty = false;
        }

        $this->currentCoordinate = null;
        $this->currentCell = null;
    }

    private function destructIfNeeded(self $cells, string $message): void
    {
        $cells->__destruct();

        throw new PhpSpreadsheetException($message);
    }

    /**
     * Add or update a cell identified by its coordinate into the collection.
     *
     * @param string $cellCoordinate Coordinate of the cell to update
     * @param Cell $cell Cell to update
     */
    public function add(string $cellCoordinate, Cell $cell): Cell
    {
        if ($cellCoordinate !== $this->currentCoordinate) {
            $this->storeCurrentCell();
        }
        $column = 0;
        $row = '';
        sscanf($cellCoordinate, '%[A-Z]%d', $column, $row);
        /** @var int $row */
        $this->index[$cellCoordinate] = (--$row * self::MAX_COLUMN_ID) + Coordinate::columnIndexFromString((string) $column);

        // Clear index sorted flag and index caches
        $this->indexSorted = false;
        $this->indexKeysCache = null;
        $this->indexValuesCache = null;

        $this->currentCoordinate = $cellCoordinate;
        $this->currentCell = $cell;
        $this->currentCellIsDirty = true;

        return $cell;
    }

    /**
     * Get cell at a specific coordinate.
     *
     * @param string $cellCoordinate Coordinate of the cell
     *
     * @return null|Cell Cell that was found, or null if not found
     */
    public function get(string $cellCoordinate): ?Cell
    {
        if ($cellCoordinate === $this->currentCoordinate) {
            return $this->currentCell;
        }
        $this->storeCurrentCell();

        // Return null if requested entry doesn't exist in collection
        if ($this->has($cellCoordinate) === false) {
            return null;
        }

        $cell = $this->getcache($cellCoordinate);

        // Set current entry to the requested entry
        $this->currentCoordinate = $cellCoordinate;
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

        // Clear index sorted flag and index caches
        $this->indexSorted = false;
        $this->indexKeysCache = null;
        $this->indexValuesCache = null;

        // detach ourself from the worksheet, so that it can then delete this object successfully
        $this->parent = null;
    }

    /**
     * Destroy this cell collection.
     */
    public function __destruct()
    {
        $this->cache->deleteMultiple($this->getAllCacheKeys());
        $this->parent = null;
    }

    /**
     * Returns all known cache keys.
     *
     * @return iterable<string>
     */
    private function getAllCacheKeys(): iterable
    {
        foreach ($this->index as $coordinate => $value) {
            yield $this->cachePrefix . $coordinate;
        }
    }

    private function getCache(string $cellCoordinate): Cell
    {
        $cell = $this->cache->get($this->cachePrefix . $cellCoordinate);
        if (!($cell instanceof Cell)) {
            throw new PhpSpreadsheetException("Cell entry {$cellCoordinate} no longer exists in cache. This probably means that the cache was cleared by someone else.");
        }

        return $cell;
    }
}
