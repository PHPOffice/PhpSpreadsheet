<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/../Header.php';

// Read from Xlsx (.xls) template
$helper->log('Load Xlsx template file');
$reader = IOFactory::createReader('Xlsx');
$spreadsheet = $reader->load(__DIR__ . '/../templates/27template.xlsx');

// Save
$helper->write($spreadsheet, __FILE__);
