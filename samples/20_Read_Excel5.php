<?php

require __DIR__ . '/Header.php';

$spreadsheet = require __DIR__ . '/templates/sampleSpreadsheet.php';

// Write temporary file
$filename = $helper->getTemporaryFilename('xls');
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Excel5');
$callStartTime = microtime(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);

// Read Excel5 file
$callStartTime = microtime(true);
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);
$helper->logRead('Excel5', $filename, $callStartTime);

// Save
$helper->write($spreadsheet, __FILE__);
