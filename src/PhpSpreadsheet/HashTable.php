<?php

namespace PhpOffice\PhpSpreadsheet;

class HashTable
{
    /**
     * HashTable elements.
     *
     * @var IComparable[]
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
     * @param IComparable[] $source Optional source array to create HashTable from
     */
    public function __construct($source = null)
    {
        if ($source !== null) {
            // Create HashTable
            $this->addFromSource($source);
        }
    }

    /**
     * Add HashTable items from source.
     *
     * @param IComparable[] $source Source array to create HashTable from
     */
    public function addFromSource(?array $source = null): void
    {
        // Check if an array was passed
        if ($source == null) {
            return;
        }

        foreach ($source as $item) {
            $this->add($item);
        }
    }

    /**
     * Add HashTable item.
     *
     * @param IComparable $source Item to add
     */
    public function add(IComparable $source): void
    {
        $hash = $source->getHashCode();
        if (!isset($this->items[$hash])) {
            $this->items[$hash] = $source;
            $this->keyMap[count($this->items) - 1] = $hash;
        }
    }

    /**
     * Remove HashTable item.
     *
     * @param IComparable $source Item to remove
     */
    public function remove(IComparable $source): void
    {
        $hash = $source->getHashCode();
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
     * @param string $hashCode
     *
     * @return false|int Index
     */
    public function getIndexForHashCode($hashCode)
    {
        return array_search($hashCode, $this->keyMap);
    }

    /**
     * Get by index.
     *
     * @param int $index
     *
     * @return IComparable
     */
    public function getByIndex($index)
    {
        if (isset($this->keyMap[$index])) {
            return $this->getByHashCode($this->keyMap[$index]);
        }

        return null;
    }

    /**
     * Get by hashcode.
     *
     * @param string $hashCode
     *
     * @return IComparable
     */
    public function getByHashCode($hashCode)
    {
        if (isset($this->items[$hashCode])) {
            return $this->items[$hashCode];
        }

        return null;
    }

    /**
     * HashTable to array.
     *
     * @return IComparable[]
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
