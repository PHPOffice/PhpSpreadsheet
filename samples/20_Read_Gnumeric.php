<?php

require __DIR__ . '/Header.php';

$filename = __DIR__ . '/templates/GnumericTest.gnumeric';
$callStartTime = microtime(true);
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);
$helper->logRead('Gnumeric', $filename, $callStartTime);

// Save
$helper->write($spreadsheet, __FILE__);
