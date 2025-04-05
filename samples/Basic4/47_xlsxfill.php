<?php

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

$helper->log('Read spreadsheet');
$reader = new Xlsx();
$spreadsheet = $reader->load(__DIR__ . '/../templates/47_xlsxfill.xlsx');

// Save
$helper->write($spreadsheet, __FILE__, ['Xlsx']);
$spreadsheet->disconnectWorksheets();
