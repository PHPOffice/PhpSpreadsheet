<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/Header.php';
$spreadsheet = require __DIR__ . '/templates/sampleSpreadsheet.php';

$filename = $helper->getFilename(__FILE__, 'xls');
$writer = IOFactory::createWriter($spreadsheet, 'Xls');

$callStartTime = microtime(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);
