# PHPExcel Developer Documentation


## Configuration Settings

Once you have included the PHPExcel files in your script, but before instantiating a PHPExcel object or loading a workbook file, there are a number of configuration options that can be set which will affect the subsequent behaviour of the script.

### Cell Caching

PHPExcel uses an average of about 1k/cell in your worksheets, so large workbooks can quickly use up available memory. Cell caching provides a mechanism that allows PHPExcel to maintain the cell objects in a smaller size of memory, on disk, or in APC, memcache or Wincache, rather than in PHP memory. This allows you to reduce the memory usage for large workbooks, although at a cost of speed to access cell data.

By default, PHPExcel still holds all cell objects in memory, but you can specify alternatives. To enable cell caching, you must call the PHPExcel_Settings::setCacheStorageMethod() method, passing in the caching method that you wish to use.

```php
$cacheMethod = PHPExcel_CachedObjectStorageFactory::CACHE_IN_MEMORY;

PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
```

setCacheStorageMethod() will return a boolean true on success, false on failure (for example if trying to cache to APC when APC is not enabled).

A separate cache is maintained for each individual worksheet, and is automatically created when the worksheet is instantiated based on the caching method and settings that you have configured. You cannot change the configuration settings once you have started to read a workbook, or have created your first worksheet.

Currently, the following caching methods are available.

#### PHPExcel_CachedObjectStorageFactory::CACHE_IN_MEMORY

The default. If you don't initialise any caching method, then this is the method that PHPExcel will use. Cell objects are maintained in PHP memory as at present.

#### PHPExcel_CachedObjectStorageFactory::CACHE_IN_MEMORY_SERIALIZED

Using this caching method, cells are held in PHP memory as an array of serialized objects, which reduces the memory footprint with minimal performance overhead.

#### PHPExcel_CachedObjectStorageFactory::CACHE_IN_MEMORY_GZIP

Like cache_in_memory_serialized, this method holds cells in PHP memory as an array of serialized objects, but gzipped to reduce the memory usage still further, although access to read or write a cell is slightly slower.

#### PHPExcel_CachedObjectStorageFactory::CACHE_IGBINARY

Uses PHPs igbinary extension (if its available) to serialize cell objects in memory. This is normally faster and uses less memory than standard PHP serialization, but isnt available in most hosting environments.

#### PHPExcel_CachedObjectStorageFactory::CACHE_TO_DISCISAM

When using CACHE_TO_DISCISAM all cells are held in a temporary disk file, with only an index to their location in that file maintained in PHP memory. This is slower than any of the CACHE_IN_MEMORY methods, but significantly reduces the memory footprint. By default, PHPExcel will use PHP's temp directory for the cache file, but you can specify a different directory when initialising CACHE_TO_DISCISAM.

```php
$cacheMethod = PHPExcel_CachedObjectStorageFactory::CACHE_TO_DISCISAM;
$cacheSettings = array(
    'dir' => '/usr/local/tmp'
);
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
```

The temporary disk file is automatically deleted when your script terminates.

#### PHPExcel_CachedObjectStorageFactory::CACHE_TO_PHPTEMP

Like CACHE_TO_DISCISAM, when using CACHE_TO_PHPTEMP all cells are held in the php://temp I/O stream, with only an index to their location maintained in PHP memory. In PHP, the php://memory wrapper stores data in the memory: php://temp behaves similarly, but uses a temporary file for storing the data when a certain memory limit is reached. The default is 1 MB, but you can change this when initialising CACHE_TO_PHPTEMP.

```php
$cacheMethod = PHPExcel_CachedObjectStorageFactory::CACHE_TO_PHPTEMP;
$cacheSettings = array(
    'memoryCacheSize' => '8MB'
);
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
```

The php://temp file is automatically deleted when your script terminates.

#### PHPExcel_CachedObjectStorageFactory::CACHE_TO_APC

When using CACHE_TO_APC, cell objects are maintained in APC with only an index maintained in PHP memory to identify that the cell exists. By default, an APC cache timeout of 600 seconds is used, which should be enough for most applications: although it is possible to change this when initialising CACHE_TO_APC.

```php
$cacheMethod = PHPExcel_CachedObjectStorageFactory::CACHE_TO_APC;
$cacheSettings = array(
    'cacheTime' => 600
);
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
```

When your script terminates all entries will be cleared from APC, regardless of the cacheTime value, so it cannot be used for persistent storage using this mechanism.

#### PHPExcel_CachedObjectStorageFactory::CACHE_TO_MEMCACHE

When using CACHE_TO_MEMCACHE, cell objects are maintained in memcache with only an index maintained in PHP memory to identify that the cell exists.

By default, PHPExcel looks for a memcache server on localhost at port 11211. It also sets a memcache timeout limit of 600 seconds. If you are running memcache on a different server or port, then you can change these defaults when you initialise CACHE_TO_MEMCACHE:

```php
$cacheMethod = PHPExcel_CachedObjectStorageFactory::CACHE_TO_MEMCACHE;
$cacheSettings = array(
    'memcacheServer' => 'localhost',
    'memcachePort'   => 11211,
    'cacheTime'      => 600
);
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
```

When your script terminates all entries will be cleared from memcache, regardless of the cacheTime value, so it cannot be used for persistent storage using this mechanism.

#### PHPExcel_CachedObjectStorageFactory::CACHE_TO_WINCACHE

When using CACHE_TO_WINCACHE, cell objects are maintained in Wincache with only an index maintained in PHP memory to identify that the cell exists. By default, a Wincache cache timeout of 600 seconds is used, which should be enough for most applications: although it is possible to change this when initialising CACHE_TO_WINCACHE.

```php
$cacheMethod = PHPExcel_CachedObjectStorageFactory::CACHE_TO_WINCACHE;
$cacheSettings = array(
    'cacheTime' => 600
);
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
```

When your script terminates all entries will be cleared from Wincache, regardless of the cacheTime value, so it cannot be used for persistent storage using this mechanism.

#### PHPExcel_CachedObjectStorageFactory::CACHE_TO_SQLITE

Uses an SQLite 2 "in-memory" database for caching cell data. Unlike other caching methods, neither cells nor an index are held in PHP memory - an indexed database table makes it unnecessary to hold any index in PHP memory, which makes this the most memory-efficient of the cell caching methods.

#### PHPExcel_CachedObjectStorageFactory::CACHE_TO_SQLITE3;

Uses an SQLite 3 "in-memory" database for caching cell data. Unlike other caching methods, neither cells nor an index are held in PHP memory - an indexed database table makes it unnecessary to hold any index in PHP memory, which makes this the most memory-efficient of the cell caching methods.


### Language/Locale

Some localisation elements have been included in PHPExcel. You can set a locale by changing the settings. To set the locale to Brazilian Portuguese you would use:

```php
$locale = 'pt_br';
$validLocale = PHPExcel_Settings::setLocale($locale);
if (!$validLocale) {
    echo 'Unable to set locale to ' . $locale . " - reverting to en_us" . PHP_EOL;
}
```

If Brazilian Portuguese language files aren't available, then Portuguese will be enabled instead: if Portuguese language files aren't available, then the setLocale() method will return an error, and American English (en_us) settings will be used throughout.

More details of the features available once a locale has been set, including a list of the languages and locales currently supported, can be found in the section of this document entitled "Locale Settings for Formulae".

