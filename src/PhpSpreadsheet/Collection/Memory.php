<?php

namespace PhpOffice\PhpSpreadsheet\Collection;

use Psr\SimpleCache\CacheInterface;

/**
 * This is the default implementation for in-memory cell collection.
 *
 * Alternatives implementation should leverage off-memory, non-volatile storage
 * to reduce overall memory usage.
 */
class Memory implements CacheInterface
{
    private $cache = [];

    public function clear()
    {
        $this->cache = [];

        return true;
    }

    public function delete($key)
    {
        unset($this->cache[$key]);

        return true;
    }

    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return $this->cache[$key];
        }

        return $default;
    }

    public function getMultiple($keys, $default = null)
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $default);
        }

        return $results;
    }

    public function has($key)
    {
        return array_key_exists($key, $this->cache);
    }

    public function set($key, $value, $ttl = null)
    {
        $this->cache[$key] = $value;

        return true;
    }

    public function setMultiple($values, $ttl = null)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }

        return true;
    }
}
