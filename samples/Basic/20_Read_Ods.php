<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/../Header.php';

$filename = __DIR__ . '/../templates/OOCalcTest.ods';
$callStartTime = microtime(true);
$spreadsheet = IOFactory::load($filename);
$helper->logRead('Ods', $filename, $callStartTime);

// Save
$helper->write($spreadsheet, __FILE__);
