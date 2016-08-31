<?php

require __DIR__ . '/Header.php';

$cacheMethod = \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::CACHE_IN_MEMORY_GZIP;
if (\PhpOffice\PhpSpreadsheet\Settings::setCacheStorageMethod($cacheMethod)) {
    $helper->log('Enable Cell Caching using ' . $cacheMethod . ' method');
} else {
    $helper->log('ERROR: Unable to set Cell Caching using ' . $cacheMethod . ' method, reverting to memory');
}

$spreadsheet = require __DIR__ . '/templates/largeSpreadsheet.php';

// Save
$helper->write($spreadsheet, __FILE__);
