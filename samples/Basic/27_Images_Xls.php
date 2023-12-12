<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/../Header.php';

// Read from Xls (.xls) template
$helper->log('Load Xls template file');
$reader = IOFactory::createReader('Xls');
$spreadsheet = $reader->load(__DIR__ . '/../templates/27template.xls');

// Save
$helper->write($spreadsheet, __FILE__);
