<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

require __DIR__ . '/../Header.php';

$inputFileName = __DIR__ . '/sampleData/non-existing-file.xls';
$helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' using IOFactory to identify the format');

try {
    $spreadsheet = IOFactory::load($inputFileName);
} catch (ReaderException $e) {
    $helper->log('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
}
