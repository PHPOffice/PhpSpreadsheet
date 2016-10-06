<?php

require __DIR__ . '/Header.php';

// Create temporary file that will be read
$sampleSpreadsheet = require __DIR__ . '/templates/sampleSpreadsheet.php';
$filename = $helper->getTemporaryFilename();
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($sampleSpreadsheet);
$writer->save($filename);

// Use PCLZip rather than ZipArchive to read the Xlsx OfficeOpenXML file
\PhpSpreadsheet\Settings::setZipClass(\PhpOffice\PhpSpreadsheet\Settings::PCLZIP);

$callStartTime = microtime(true);
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);
$helper->logRead('Xlsx', $filename, $callStartTime);
$callEndTime = microtime(true);

// Save
$helper->write($spreadsheet, __FILE__);
