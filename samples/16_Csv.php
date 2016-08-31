<?php

require __DIR__ . '/Header.php';
$spreadsheet = require __DIR__ . '/templates/sampleSpreadsheet.php';

$helper->log('Write to CSV format');
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'CSV')->setDelimiter(',')
        ->setEnclosure('"')
        ->setSheetIndex(0);

$callStartTime = microtime(true);
$filename = $helper->getTemporaryFilename('csv');
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);

$helper->log('Read from CSV format');

$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('CSV')->setDelimiter(',')
        ->setEnclosure('"')
        ->setSheetIndex(0);

$callStartTime = microtime(true);
$spreadsheetFromCSV = $reader->load($filename);
$helper->logRead('CSV', $filename, $callStartTime);

// Write Excel2007
$helper->write($spreadsheetFromCSV, __FILE__, ['Excel2007' => 'xlsx']);

// Write CSV
$filenameCSV = $helper->getFilename(__FILE__, 'csv');
$writerCSV = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheetFromCSV, 'CSV');
$writerCSV->setExcelCompatibility(true);

$callStartTime = microtime(true);
$writerCSV->save($filenameCSV);
$helper->logWrite($writerCSV, $filenameCSV, $callStartTime);
