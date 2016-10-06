<?php

require __DIR__ . '/Header.php';

// Read from Xls (.xls) template
$helper->log('Load Xlsx template file');
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xls');
$spreadsheet = $reader->load(__DIR__ . '/templates/27template.xls');

// Save
$helper->write($spreadsheet, __FILE__);
