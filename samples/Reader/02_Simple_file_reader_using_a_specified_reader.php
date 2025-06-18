<?php

use PhpOffice\PhpSpreadsheet\Reader\Xls;

require __DIR__ . '/../Header.php';
$inputFileName = __DIR__ . '/sampleData/example1.xls';

$helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' using ' . Xls::class);
$reader = new Xls();
$spreadsheet = $reader->load($inputFileName);

$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
var_dump($sheetData);
