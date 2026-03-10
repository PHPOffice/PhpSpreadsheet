<?php

namespace PhpOffice\PhpSpreadsheet\Collection\Memory;

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

    public function __construct(int $maxSize = 0)
    {
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
            // Move to end to mark as recently used
            $value = $this->cache[$key];
            unset($this->cache[$key]);
            $this->cache[$key] = $value;

            return $value;
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
            reset($this->cache);
            $lruKey = key($this->cache);
            unset($this->cache[$lruKey]);
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
