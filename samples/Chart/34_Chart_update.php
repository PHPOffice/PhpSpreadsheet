<?php

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

require __DIR__ . '/../Header.php';

// Create temporary file that will be read
$sampleSpreadsheet = require __DIR__ . '/../templates/chartSpreadsheet.php';
$filename = $helper->getTemporaryFilename();
$writer = new XlsxWriter($sampleSpreadsheet);
$writer->setIncludeCharts(true);
$writer->save($filename);

$helper->log('Load from Xlsx file');
$reader = new XlsxReader();
$reader->setIncludeCharts(true);
$spreadsheet = $reader->load($filename);
unlink($filename);

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
$helper->write($spreadsheet, __FILE__, ['Xlsx'], true);
