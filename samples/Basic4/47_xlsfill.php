<?php

require __DIR__ . '/../Header.php';

use PhpOffice\PhpSpreadsheet\Reader\Xls;

$helper->log('Read spreadsheet');
$reader = new Xls();
$spreadsheet = $reader->load(__DIR__ . '/../templates/47_xlsfill.xls');

// Save
$helper->write($spreadsheet, __FILE__, ['Xls']);
$spreadsheet->disconnectWorksheets();
