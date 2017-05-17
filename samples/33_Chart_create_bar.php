<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/Header.php';

$spreadsheet = require __DIR__ . '/templates/chartSpreadsheet.php';

// Save Excel 2007 file
$filename = $helper->getFilename(__FILE__);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->setIncludeCharts(true);
$callStartTime = microtime(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);
