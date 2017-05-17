<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require __DIR__ . '/Header.php';

// Create temporary file that will be read
$sampleSpreadsheet = require __DIR__ . '/templates/chartSpreadsheet.php';
$filename = $helper->getTemporaryFilename();
$writer = new Xlsx($sampleSpreadsheet);
$writer->save($filename);

$helper->log('Load from Xlsx file');
$reader = IOFactory::createReader('Xlsx');
$reader->setIncludeCharts(true);
$spreadsheet = $reader->load($filename);

$helper->log('Update cell data values that are displayed in the chart');
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->fromArray(
    [
    [50 - 12, 50 - 15, 50 - 21],
    [50 - 56, 50 - 73, 50 - 86],
    [50 - 52, 50 - 61, 50 - 69],
    [50 - 30, 50 - 32, 50],
        ],
    null,
    'B2'
);

// Save Excel 2007 file
$filename = $helper->getFilename(__FILE__);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->setIncludeCharts(true);
$callStartTime = microtime(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);
