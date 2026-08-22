<?php

namespace PhpOffice\PhpSpreadsheet\Collection\Memory;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

/**
 * This is the default implementation for in-memory cell collection.
 *
 * Alternative implementation should leverage off-memory, non-volatile storage
 * to reduce overall memory usage.
 */
class SimpleCache3 implements CacheInterface
{
    private array $cache = [];

    public function clear(): bool
    {
        $this->cache = [];

        return true;
    }

    public function delete(string $key): bool
    {
        unset($this->cache[$key]);

        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->has($key)) {
            return $this->cache[$key];
        }

        return $default;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $default);
        }

        return $results;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->cache);
    }

    public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        $this->cache[$key] = $value;

        return true;
    }

    public function setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }

        return true;
    }
}
