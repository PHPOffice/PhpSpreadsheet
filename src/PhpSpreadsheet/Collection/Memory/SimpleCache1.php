<?php

namespace PhpOffice\PhpSpreadsheet\Collection\Memory;

use InvalidArgumentException;
use Psr\SimpleCache\CacheInterface;

/**
 * This is the default implementation for in-memory cell collection.
 *
 * Alternative implementation should leverage off-memory, non-volatile storage
 * to reduce overall memory usage.
 *
 * Either SimpleCache1 or SimpleCache3, but not both, may be used.
 * For code coverage testing, it will always be SimpleCache3.
 *
 * @warning Setting maxSize > 0 is UNSAFE when this cache is used as PhpSpreadsheet's
 *          cell cache (via Settings::setCache()). The Cells class maintains its own
 *          $index that assumes entries remain in cache — LRU eviction will cause
 *          PhpSpreadsheetException. Only use a bounded cache for non-cell-cache workloads.
 *
 * @codeCoverageIgnore
 */
class SimpleCache1 implements CacheInterface
{
    /**
     * @var array Cell Cache
     */
    private array $cache = [];

    /**
     * Maximum number of entries (0 = unlimited).
     */
    private int $maxSize;

    /**
     * @param int $maxSize Maximum number of entries (0 = unlimited).
     *                     WARNING: maxSize > 0 is UNSAFE when used as PhpSpreadsheet's cell cache
     *                     (via Settings::setCache()). The Cells class maintains its own $index that
     *                     assumes entries stay in cache — eviction causes PhpSpreadsheetException.
     */
    public function __construct(int $maxSize = 0)
    {
        if ($maxSize < 0) {
            throw new InvalidArgumentException('maxSize must be >= 0');
        }

        $this->maxSize = $maxSize;
    }

    public function clear(): bool
    {
        $this->cache = [];

        return true;
    }

    public function delete($key): bool
    {
        unset($this->cache[$key]);

        return true;
    }

    public function deleteMultiple($keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    public function get($key, $default = null): mixed
    {
        if ($this->has($key)) {
            // Only promote if LRU eviction is active
            if ($this->maxSize > 0 && array_key_last($this->cache) !== $key) {
                $value = $this->cache[$key];
                unset($this->cache[$key]);
                $this->cache[$key] = $value;
            }

            return $this->cache[$key];
        }

        return $default;
    }

    public function getMultiple($keys, $default = null): iterable
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $default);
        }

        return $results;
    }

    public function has($key): bool
    {
        return array_key_exists($key, $this->cache);
    }

    public function set($key, $value, $ttl = null): bool
    {
        // If key already exists, remove it first so it moves to end
        if (array_key_exists($key, $this->cache)) {
            unset($this->cache[$key]);
        } elseif ($this->maxSize > 0 && count($this->cache) >= $this->maxSize) {
            // Evict the least recently used entry (first element)
            unset($this->cache[array_key_first($this->cache)]);
        }

        $this->cache[$key] = $value;

        return true;
    }

    public function setMultiple($values, $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }

        return true;
    }
}
