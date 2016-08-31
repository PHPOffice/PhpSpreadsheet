<?php

require __DIR__ . '/Header.php';

// Read from Excel5 (.xls) template
$helper->log('Load Excel2007 template file');
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Excel5');
$spreadsheet = $reader->load(__DIR__ . '/templates/27template.xls');

// Save
$helper->write($spreadsheet, __FILE__);
