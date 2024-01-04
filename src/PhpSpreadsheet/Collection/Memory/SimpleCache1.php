<?php

namespace PhpOffice\PhpSpreadsheet\Collection\Memory;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

/**
 * This is the default implementation for in-memory cell collection.
 *
 * Alternatives implementation should leverage off-memory, non-volatile storage
 * to reduce overall memory usage.
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

    /**
     * @param mixed  $default
     */
    public function get(string $key, $default = null): mixed
    {
        if ($this->has($key)) {
            return $this->cache[$key];
        }

        return $default;
    }

    /**
     * @param mixed    $default
     */
    public function getMultiple(iterable $keys, $default = null): iterable
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

    /**
     * @param mixed                  $value
     * @param null|DateInterval|int $ttl
     */
    public function set(string $key, $value, $ttl = null): bool
    {
        $this->cache[$key] = $value;

        return true;
    }

    /**
     * @param null|DateInterval|int $ttl
     */
    public function setMultiple(iterable $values, $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }

        return true;
    }
}
