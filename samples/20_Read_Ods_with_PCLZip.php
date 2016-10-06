<?php

require __DIR__ . '/Header.php';

// Use PCLZip rather than ZipArchive to read the Xlsx OfficeOpenXML file
\PhpOffice\PhpSpreadsheet\Settings::setZipClass(\PhpOffice\PhpSpreadsheet\Settings::PCLZIP);

$filename = __DIR__ . '/templates/OOCalcTest.ods';
$callStartTime = microtime(true);
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);
$helper->logRead('Ods', $filename, $callStartTime);

// Save
$helper->write($spreadsheet, __FILE__);
