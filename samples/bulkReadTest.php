<?php

// The following code can be used to submit an issue with the PHPSpreadsheet calculation engine

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\IOFactory;

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('UTC');

// Adjust the path as required to reference the PHPSpreadsheet Bootstrap file
require_once __DIR__ . '/../vendor/autoload.php';

foreach (glob(__DIR__ . '/*') as $fileName) {
    if (!in_array(pathinfo($fileName, PATHINFO_EXTENSION), ['xls', 'xlsx', 'gnumeric', 'ods'])) {
        continue;
    }
    $callStartTime = microtime(true);
    echo 'Current memory usage: ' , (memory_get_usage(true) / 1024) , ' KB' , PHP_EOL;
    $spreadsheet = IOFactory::load($fileName);
    echo 'Loaded spreadsheet file ', pathinfo($fileName, PATHINFO_FILENAME), PHP_EOL;
    $callEndTime = microtime(true);
    $loadCallTime = $callEndTime - $callStartTime;
    echo 'Call time to load spreadsheet file was ' , sprintf('%.4f', $loadCallTime) , ' seconds' , PHP_EOL;
    echo 'Current memory usage: ' , (memory_get_usage(true) / 1024) , ' KB' , PHP_EOL;
    $spreadsheet->disconnectWorksheets();
    $spreadsheet->garbageCollect();
    unset($spreadsheet);
    echo 'Unset Spreadsheet', PHP_EOL;
    gc_collect_cycles();
    echo 'Memory usage after unset: ' , (memory_get_usage(true) / 1024) , ' KB' , PHP_EOL;
}

// Echo memory usage
echo ' Current memory usage: ' , (memory_get_usage(true) / 1024) , ' KB' , PHP_EOL;
echo '    Peak memory usage: ' , (memory_get_peak_usage(true) / 1024) , ' KB' , PHP_EOL;
