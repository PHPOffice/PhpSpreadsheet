<?php

require __DIR__ . '/Header.php';

// Use PCLZip rather than ZipArchive to read the Excel2007 OfficeOpenXML file
PhpSpreadsheet\Settings::setZipClass(PhpSpreadsheet\Settings::PCLZIP);

$filename = __DIR__ . '/templates/OOCalcTest.ods';
$callStartTime = microtime(true);
$spreadsheet = \PhpSpreadsheet\IOFactory::load($filename);
$helper->logRead('OOCalc', $filename, $callStartTime);

// Save
$helper->write($spreadsheet, __FILE__);
