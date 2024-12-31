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
