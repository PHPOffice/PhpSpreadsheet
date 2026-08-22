<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$filename = __DIR__ . '/../templates/excel2003.xml';
$callStartTime = microtime(true);
$spreadsheet = IOFactory::load($filename);
$helper->logRead('Xml', $filename, $callStartTime);

// Save
$helper->write($spreadsheet, __FILE__);
