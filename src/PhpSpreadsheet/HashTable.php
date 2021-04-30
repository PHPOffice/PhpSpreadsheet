<?php

namespace PhpOffice\PhpSpreadsheet;

/**
 * @template T of IComparable
 */
class HashTable
{
    /**
     * HashTable elements.
     *
     * @var T[]
     */
    protected $items = [];

    /**
     * HashTable key map.
     *
     * @var string[]
     */
    protected $keyMap = [];

    /**
     * Create a new \PhpOffice\PhpSpreadsheet\HashTable.
     *
     * @param T[] $pSource Optional source array to create HashTable from
     */
    public function __construct($pSource = null)
    {
        if ($pSource !== null) {
            // Create HashTable
            $this->addFromSource($pSource);
        }
    }

    /**
     * Add HashTable items from source.
     *
     * @param T[] $pSource Source array to create HashTable from
     */
    public function addFromSource(?array $pSource = null): void
    {
        // Check if an array was passed
        if ($pSource == null) {
            return;
        }

        foreach ($pSource as $item) {
            $this->add($item);
        }
    }

    /**
     * Add HashTable item.
     *
     * @param T $pSource Item to add
     */
    public function add(IComparable $pSource): void
    {
        $hash = $pSource->getHashCode();
        if (!isset($this->items[$hash])) {
            $this->items[$hash] = $pSource;
            $this->keyMap[count($this->items) - 1] = $hash;
        }
    }

    /**
     * Remove HashTable item.
     *
     * @param T $pSource Item to remove
     */
    public function remove(IComparable $pSource): void
    {
        $hash = $pSource->getHashCode();
        if (isset($this->items[$hash])) {
            unset($this->items[$hash]);

            $deleteKey = -1;
            foreach ($this->keyMap as $key => $value) {
                if ($deleteKey >= 0) {
                    $this->keyMap[$key - 1] = $value;
                }

                if ($value == $hash) {
                    $deleteKey = $key;
                }
            }
            unset($this->keyMap[count($this->keyMap) - 1]);
        }
    }

    /**
     * Clear HashTable.
     */
    public function clear(): void
    {
        $this->items = [];
        $this->keyMap = [];
    }

    /**
     * Count.
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Get index for hash code.
     *
     * @param string $pHashCode
     *
     * @return int Index
     */
    public function getIndexForHashCode($pHashCode)
    {
        return array_search($pHashCode, $this->keyMap);
    }

    /**
     * Get by index.
     *
     * @param int $pIndex
     *
     * @return null|T
     */
    public function getByIndex($pIndex)
    {
        if (isset($this->keyMap[$pIndex])) {
            return $this->getByHashCode($this->keyMap[$pIndex]);
        }

        return null;
    }

    /**
     * Get by hashcode.
     *
     * @param string $pHashCode
     *
     * @return null|T
     */
    public function getByHashCode($pHashCode)
    {
        if (isset($this->items[$pHashCode])) {
            return $this->items[$pHashCode];
        }

        return null;
    }

    /**
     * HashTable to array.
     *
     * @return T[]
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            }
        }
    }
}
