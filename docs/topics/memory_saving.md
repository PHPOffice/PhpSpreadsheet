# Memory saving

PhpSpreadsheet uses an average of about 1k per cell in your worksheets, so
large workbooks can quickly use up available memory. Cell caching
provides a mechanism that allows PhpSpreadsheet to maintain the cell
objects in a smaller size of memory, or off-memory (eg: on disk, in APCu,
memcache or redis). This allows you to reduce the memory usage for large
workbooks, although at a cost of speed to access cell data.

By default, PhpSpreadsheet holds all cell objects in memory, but
you can specify alternatives by providing your own
[PSR-16](https://www.php-fig.org/psr/psr-16/) implementation. PhpSpreadsheet keys
are automatically namespaced, and cleaned up after use, so a single cache
instance may be shared across several usage of PhpSpreadsheet or even with other
cache usages.

To enable cell caching, you must provide your own implementation of cache like so:

```php
$cache = new MyCustomPsr16Implementation();

\PhpOffice\PhpSpreadsheet\Settings::setCache($cache);
```

A separate cache is maintained for each individual worksheet, and is
automatically created when the worksheet is instantiated based on the
settings that you have configured. You cannot change
the configuration settings once you have started to read a workbook, or
have created your first worksheet.

## Beware of TTL

As opposed to common cache concept, PhpSpreadsheet data cannot be re-generated
from scratch. If some data is stored and later is not retrievable,
PhpSpreadsheet will throw an exception.

That means that the data stored in cache **must not be deleted** by a
third-party or via TTL mechanism.

So be sure that TTL is either de-activated or long enough to cover the entire
usage of PhpSpreadsheet.

## Common use cases

PhpSpreadsheet does not ship with alternative cache implementation. It is up to
you to select the most appropriate implementation for your environment. You
can either implement [PSR-16](https://www.php-fig.org/psr/psr-16/) from scratch,
or use [pre-existing libraries](https://packagist.org/search/?q=psr-16).

One such library is [PHP Cache](https://www.php-cache.com/) which
provides a wide range of alternatives. Refers to their documentation for
details, but here are a few suggestions that should get you started.

### APCu

Require the packages into your project:

```sh
composer require cache/simple-cache-bridge cache/apcu-adapter
```

Configure PhpSpreadsheet with something like:

```php
$pool = new \Cache\Adapter\Apcu\ApcuCachePool();
$simpleCache = new \Cache\Bridge\SimpleCache\SimpleCacheBridge($pool);

\PhpOffice\PhpSpreadsheet\Settings::setCache($simpleCache);
```

### Redis

Require the packages into your project:

```sh
composer require cache/simple-cache-bridge cache/redis-adapter
```

Configure PhpSpreadsheet with something like:

```php
$client = new \Redis();
$client->connect('127.0.0.1', 6379);
$pool = new \Cache\Adapter\Redis\RedisCachePool($client);
$simpleCache = new \Cache\Bridge\SimpleCache\SimpleCacheBridge($pool);

\PhpOffice\PhpSpreadsheet\Settings::setCache($simpleCache);
```

### Memcache

Require the packages into your project:

```sh
composer require cache/simple-cache-bridge cache/memcache-adapter
```

Configure PhpSpreadsheet with something like:

```php
$client = new \Memcache();
$client->connect('localhost', 11211);
$pool = new \Cache\Adapter\Memcache\MemcacheCachePool($client);
$simpleCache = new \Cache\Bridge\SimpleCache\SimpleCacheBridge($pool);

\PhpOffice\PhpSpreadsheet\Settings::setCache($simpleCache);
```
