<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/../Header.php';

$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';

// Write temporary file
$filename = $helper->getTemporaryFilename('xls');
$writer = IOFactory::createWriter($spreadsheet, 'Xls');
$callStartTime = microtime(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);

// Read Xls file
$callStartTime = microtime(true);
$spreadsheet = IOFactory::load($filename);
$helper->logRead('Xls', $filename, $callStartTime);
unlink($filename);

// Save
$helper->write($spreadsheet, __FILE__);
