<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require __DIR__ . '/../Header.php';

// Create temporary file that will be read
/** @var PhpOffice\PhpSpreadsheet\Spreadsheet */
$sampleSpreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$filename = $helper->getTemporaryFilename();
$writer = new Xlsx($sampleSpreadsheet);
$writer->save($filename);

$callStartTime = microtime(true);
$spreadsheet = IOFactory::load($filename);
$helper->logRead('Xlsx', $filename, $callStartTime);

// Save
$helper->write($spreadsheet, __FILE__);
unlink($filename);
