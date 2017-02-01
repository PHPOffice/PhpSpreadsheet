<?php

namespace PhpOffice\PhpSpreadsheet;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category   PhpSpreadsheet
 *
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class CachedObjectStorageFactory
{
    const CACHE_IN_MEMORY = 'Memory';
    const CACHE_IN_MEMORY_GZIP = 'MemoryGZip';
    const CACHE_IN_MEMORY_SERIALIZED = 'MemorySerialized';
    const CACHE_IGBINARY = 'Igbinary';
    const CACHE_TO_DISCISAM = 'DiscISAM';
    const CACHE_TO_APC = 'APC';
    const CACHE_TO_MEMCACHE = 'Memcache';
    const CACHE_TO_PHPTEMP = 'PHPTemp';
    const CACHE_TO_WINCACHE = 'Wincache';
    const CACHE_TO_SQLITE = 'SQLite';
    const CACHE_TO_SQLITE3 = 'SQLite3';

    /**
     * Name of the method used for cell cacheing.
     *
     * @var string
     */
    private static $cacheStorageMethod;

    /**
     * Name of the class used for cell cacheing.
     *
     * @var string
     */
    private static $cacheStorageClass;

    /**
     * List of all possible cache storage methods.
     *
     * @var string[]
     */
    private static $storageMethods = [
        self::CACHE_IN_MEMORY,
        self::CACHE_IN_MEMORY_GZIP,
        self::CACHE_IN_MEMORY_SERIALIZED,
        self::CACHE_IGBINARY,
        self::CACHE_TO_PHPTEMP,
        self::CACHE_TO_DISCISAM,
        self::CACHE_TO_APC,
        self::CACHE_TO_MEMCACHE,
        self::CACHE_TO_WINCACHE,
        self::CACHE_TO_SQLITE,
        self::CACHE_TO_SQLITE3,
    ];

    /**
     * Default arguments for each cache storage method.
     *
     * @var array of mixed array
     */
    private static $storageMethodDefaultParameters = [
        self::CACHE_IN_MEMORY => [],
        self::CACHE_IN_MEMORY_GZIP => [],
        self::CACHE_IN_MEMORY_SERIALIZED => [],
        self::CACHE_IGBINARY => [],
        self::CACHE_TO_PHPTEMP => [
            'memoryCacheSize' => '1MB',
        ],
        self::CACHE_TO_DISCISAM => [
            'dir' => null,
        ],
        self::CACHE_TO_APC => [
            'cacheTime' => 600,
        ],
        self::CACHE_TO_MEMCACHE => [
            'memcacheServer' => 'localhost',
            'memcachePort' => 11211,
            'cacheTime' => 600,
        ],
        self::CACHE_TO_WINCACHE => [
            'cacheTime' => 600,
        ],
        self::CACHE_TO_SQLITE => [],
        self::CACHE_TO_SQLITE3 => [],
    ];

    /**
     * Arguments for the active cache storage method.
     *
     * @var mixed[]
     */
    private static $storageMethodParameters = [];

    /**
     * Return the current cache storage method.
     *
     * @return string|null
     **/
    public static function getCacheStorageMethod()
    {
        return self::$cacheStorageMethod;
    }

    /**
     * Return the current cache storage class.
     *
     * @return string
     **/
    public static function getCacheStorageClass()
    {
        return self::$cacheStorageClass;
    }

    /**
     * Return the list of all possible cache storage methods.
     *
     * @return string[]
     **/
    public static function getAllCacheStorageMethods()
    {
        return self::$storageMethods;
    }

    /**
     * Return the list of all available cache storage methods.
     *
     * @return string[]
     **/
    public static function getCacheStorageMethods()
    {
        $activeMethods = [];
        foreach (self::$storageMethods as $storageMethod) {
            $cacheStorageClass = '\\PhpOffice\\PhpSpreadsheet\\CachedObjectStorage\\' . $storageMethod;
            if (call_user_func([$cacheStorageClass, 'cacheMethodIsAvailable'])) {
                $activeMethods[] = $storageMethod;
            }
        }

        return $activeMethods;
    }

    /**
     * Identify the cache storage method to use.
     *
     * @param string $method Name of the method to use for cell cacheing
     * @param mixed[] $arguments Additional arguments to pass to the cell caching class
     *                                        when instantiating
     *
     * @return bool
     **/
    public static function initialize($method = self::CACHE_IN_MEMORY, $arguments = [])
    {
        if (!in_array($method, self::$storageMethods)) {
            return false;
        }

        $cacheStorageClass = '\\PhpOffice\\PhpSpreadsheet\\CachedObjectStorage\\' . $method;
        if (!call_user_func([$cacheStorageClass, 'cacheMethodIsAvailable'])) {
            return false;
        }

        self::$storageMethodParameters[$method] = self::$storageMethodDefaultParameters[$method];
        foreach ($arguments as $argument => $value) {
            if (isset(self::$storageMethodParameters[$method][$argument])) {
                self::$storageMethodParameters[$method][$argument] = $value;
            }
        }

        if (self::$cacheStorageMethod === null) {
            self::$cacheStorageClass = '\\PhpOffice\\PhpSpreadsheet\\CachedObjectStorage\\' . $method;
            self::$cacheStorageMethod = $method;
        }

        return true;
    }

    /**
     * Initialise the cache storage.
     *
     * @param Worksheet $parent Enable cell caching for this worksheet
     *
     * @return CachedObjectStorage\ICache
     **/
    public static function getInstance(Worksheet $parent)
    {
        $cacheMethodIsAvailable = true;
        if (self::$cacheStorageMethod === null) {
            $cacheMethodIsAvailable = self::initialize();
        }

        if ($cacheMethodIsAvailable) {
            $instance = new self::$cacheStorageClass(
                $parent,
                self::$storageMethodParameters[self::$cacheStorageMethod]
            );
            if ($instance !== null) {
                return $instance;
            }
        }

        return false;
    }

    /**
     * Clear the cache storage.
     **/
    public static function finalize()
    {
        self::$cacheStorageMethod = null;
        self::$cacheStorageClass = null;
        self::$storageMethodParameters = [];
    }
}
