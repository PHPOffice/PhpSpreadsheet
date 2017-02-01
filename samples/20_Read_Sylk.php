<?php

require __DIR__ . '/Header.php';

$filename = __DIR__ . '/templates/SylkTest.slk';
$callStartTime = microtime(true);
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);
$helper->logRead('Slk', $filename, $callStartTime);

// Save
$helper->write($spreadsheet, __FILE__);
