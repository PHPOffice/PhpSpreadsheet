<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Spreadsheet */
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';

/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$filename = $helper->getFilename(__FILE__, 'xls');
$writer = IOFactory::createWriter($spreadsheet, 'Xls');

$callStartTime = microtime(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);
