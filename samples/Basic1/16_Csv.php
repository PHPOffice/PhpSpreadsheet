<?php

use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;

require __DIR__ . '/../Header.php';
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';

$helper->log('Write to CSV format');
$writer = new CsvWriter($spreadsheet);
$writer->setDelimiter(',')
    ->setEnclosure('"')
    ->setSheetIndex(0);

$callStartTime = microtime(true);
$filename = $helper->getTemporaryFilename('csv');
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);

$helper->log('Read from CSV format');

$reader = new CsvReader();
$reader->setDelimiter(',')
    ->setEnclosure('"')
    ->setSheetIndex(0);

$callStartTime = microtime(true);
$spreadsheetFromCSV = $reader->load($filename);
$helper->logRead('Csv', $filename, $callStartTime);
unlink($filename);

// Write Xlsx
$helper->write($spreadsheetFromCSV, __FILE__, ['Xlsx']);

// Write CSV
$filenameCSV = $helper->getFilename(__FILE__, 'csv');
$writerCSV = new CsvWriter($spreadsheetFromCSV);
//$writerCSV->setExcelCompatibility(true);
$writerCSV->setUseBom(true); // because of non-ASCII chars

$callStartTime = microtime(true);
$writerCSV->save($filenameCSV);
$helper->logWrite($writerCSV, $filenameCSV, $callStartTime);
