# Configuration Settings

Once you have included the PhpSpreadsheet files in your script, but
before instantiating a `Spreadsheet` object or loading a workbook file,
there are a number of configuration options that can be set which will
affect the subsequent behaviour of the script.

## Cell Caching

PhpSpreadsheet uses an average of about 1k/cell in your worksheets, so
large workbooks can quickly use up available memory. Cell caching
provides a mechanism that allows PhpSpreadsheet to maintain the cell
objects in a smaller size of memory, on disk, or in APC, memcache or
Wincache, rather than in PHP memory. This allows you to reduce the
memory usage for large workbooks, although at a cost of speed to access
cell data.

By default, PhpSpreadsheet still holds all cell objects in memory, but
you can specify alternatives. To enable cell caching, you must call the
\PhpOffice\PhpSpreadsheet\Settings::setCacheStorageMethod() method,
passing in the caching method that you wish to use.

``` php
$cacheMethod = \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::CACHE_IN_MEMORY;

\PhpOffice\PhpSpreadsheet\Settings::setCacheStorageMethod($cacheMethod);
```

setCacheStorageMethod() will return a boolean true on success, false on
failure (for example if trying to cache to APC when APC is not enabled).

A separate cache is maintained for each individual worksheet, and is
automatically created when the worksheet is instantiated based on the
caching method and settings that you have configured. You cannot change
the configuration settings once you have started to read a workbook, or
have created your first worksheet.

Currently, the following caching methods are available.

### \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::CACHE\_IN\_MEMORY

The default. If you don't initialise any caching method, then this is
the method that PhpSpreadsheet will use. Cell objects are maintained in
PHP memory as at present.

### \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::CACHE\_IN\_MEMORY\_SERIALIZED

Using this caching method, cells are held in PHP memory as an array of
serialized objects, which reduces the memory footprint with minimal
performance overhead.

### \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::CACHE\_IN\_MEMORY\_GZIP

Like cache\_in\_memory\_serialized, this method holds cells in PHP
memory as an array of serialized objects, but gzipped to reduce the
memory usage still further, although access to read or write a cell is
slightly slower.

### \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::CACHE\_IGBINARY

Uses PHPs igbinary extension (if its available) to serialize cell
objects in memory. This is normally faster and uses less memory than
standard PHP serialization, but isnt available in most hosting
environments.

### \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::CACHE\_TO\_DISCISAM

When using CACHE\_TO\_DISCISAM all cells are held in a temporary disk
file, with only an index to their location in that file maintained in
PHP memory. This is slower than any of the CACHE\_IN\_MEMORY methods,
but significantly reduces the memory footprint. By default,
PhpSpreadsheet will use PHP's temp directory for the cache file, but you
can specify a different directory when initialising CACHE\_TO\_DISCISAM.

``` php
$cacheMethod = \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::CACHE_TO_DISCISAM;
$cacheSettings = array(
    'dir' => '/usr/local/tmp'
);
\PhpOffice\PhpSpreadsheet\Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
```

The temporary disk file is automatically deleted when your script
terminates.

### \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::CACHE\_TO\_PHPTEMP

Like CACHE\_TO\_DISCISAM, when using CACHE\_TO\_PHPTEMP all cells are
held in the php://temp I/O stream, with only an index to their location
maintained in PHP memory. In PHP, the php://memory wrapper stores data
in the memory: php://temp behaves similarly, but uses a temporary file
for storing the data when a certain memory limit is reached. The default
is 1 MB, but you can change this when initialising CACHE\_TO\_PHPTEMP.

``` php
$cacheMethod = \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::CACHE_TO_PHPTEMP;
$cacheSettings = array(
    'memoryCacheSize' => '8MB'
);
\PhpOffice\PhpSpreadsheet\Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
```

The php://temp file is automatically deleted when your script
terminates.

### \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::CACHE\_TO\_APC

When using CACHE\_TO\_APC, cell objects are maintained in APC with only
an index maintained in PHP memory to identify that the cell exists. By
default, an APC cache timeout of 600 seconds is used, which should be
enough for most applications: although it is possible to change this
when initialising CACHE\_TO\_APC.

``` php
$cacheMethod = \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::CACHE_TO_APC;
$cacheSettings = array(
    'cacheTime' => 600
);
\PhpOffice\PhpSpreadsheet\Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
```

When your script terminates all entries will be cleared from APC,
regardless of the cacheTime value, so it cannot be used for persistent
storage using this mechanism.

### \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::CACHE\_TO\_MEMCACHE

When using CACHE\_TO\_MEMCACHE, cell objects are maintained in memcache
with only an index maintained in PHP memory to identify that the cell
exists.

By default, PhpSpreadsheet looks for a memcache server on localhost at
port 11211. It also sets a memcache timeout limit of 600 seconds. If you
are running memcache on a different server or port, then you can change
these defaults when you initialise CACHE\_TO\_MEMCACHE:

``` php
$cacheMethod = \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::CACHE_TO_MEMCACHE;
$cacheSettings = array(
    'memcacheServer' => 'localhost',
    'memcachePort'   => 11211,
    'cacheTime'      => 600
);
\PhpOffice\PhpSpreadsheet\Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
```

When your script terminates all entries will be cleared from memcache,
regardless of the cacheTime value, so it cannot be used for persistent
storage using this mechanism.

### \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::CACHE\_TO\_WINCACHE

When using CACHE\_TO\_WINCACHE, cell objects are maintained in Wincache
with only an index maintained in PHP memory to identify that the cell
exists. By default, a Wincache cache timeout of 600 seconds is used,
which should be enough for most applications: although it is possible to
change this when initialising CACHE\_TO\_WINCACHE.

``` php
$cacheMethod = \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::CACHE_TO_WINCACHE;
$cacheSettings = array(
    'cacheTime' => 600
);
\PhpOffice\PhpSpreadsheet\Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
```

When your script terminates all entries will be cleared from Wincache,
regardless of the cacheTime value, so it cannot be used for persistent
storage using this mechanism.

### \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::CACHE\_TO\_SQLITE3;

Uses an SQLite 3 "in-memory" database for caching cell data. Unlike
other caching methods, neither cells nor an index are held in PHP memory
- an indexed database table makes it unnecessary to hold any index in
PHP memory, which makes this the most memory-efficient of the cell
caching methods.

## Language/Locale

Some localisation elements have been included in PhpSpreadsheet. You can
set a locale by changing the settings. To set the locale to Brazilian
Portuguese you would use:

``` php
$locale = 'pt_br';
$validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale($locale);
if (!$validLocale) {
    echo 'Unable to set locale to ' . $locale . " - reverting to en_us" . PHP_EOL;
}
```

- If Brazilian Portuguese language files aren't available, then Portuguese
will be enabled instead
- If Portuguese language files aren't available,
then the `setLocale()` method will return an error, and American English
(en\_us) settings will be used throughout.

More details of the features available once a locale has been set,
including a list of the languages and locales currently supported, can
be found in [Locale Settings for
Formulae](./recipes.md#locale-settings-for-formulae).
