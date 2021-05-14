<?php

use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;

require __DIR__ . '/../Header.php';
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';

$helper->log('Write to CSV format');
/** @var \PhpOffice\PhpSpreadsheet\Writer\Csv $writer */
$writer = new CsvWriter($spreadsheet);
$writer->setDelimiter(',')
    ->setEnclosure('"')
    ->setSheetIndex(0);

$callStartTime = microtime(true);
$filename = $helper->getTemporaryFilename('csv');
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);

$helper->log('Read from CSV format');

/** @var \PhpOffice\PhpSpreadsheet\Reader\Csv $reader */
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
/** @var \PhpOffice\PhpSpreadsheet\Writer\Csv $writerCSV */
$writerCSV = new CsvWriter($spreadsheetFromCSV);
//$writerCSV->setExcelCompatibility(true);
$writerCSV->setUseBom(true); // because of non-ASCII chars

$callStartTime = microtime(true);
$writerCSV->save($filenameCSV);
$helper->logWrite($writerCSV, $filenameCSV, $callStartTime);
