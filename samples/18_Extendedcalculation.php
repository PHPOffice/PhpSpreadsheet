<?php


require __DIR__ . '/Header.php';

// List functions
$helper->log('List implemented functions');
$calc = \PhpOffice\PhpSpreadsheet\Calculation::getInstance();
print_r($calc->getImplementedFunctionNames());

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

// Add some data, we will use some formulas here
$helper->log('Add some data');
$spreadsheet->getActiveSheet()->setCellValue('A14', 'Count:');

$spreadsheet->getActiveSheet()->setCellValue('B1', 'Range 1');
$spreadsheet->getActiveSheet()->setCellValue('B2', 2);
$spreadsheet->getActiveSheet()->setCellValue('B3', 8);
$spreadsheet->getActiveSheet()->setCellValue('B4', 10);
$spreadsheet->getActiveSheet()->setCellValue('B5', true);
$spreadsheet->getActiveSheet()->setCellValue('B6', false);
$spreadsheet->getActiveSheet()->setCellValue('B7', 'Text String');
$spreadsheet->getActiveSheet()->setCellValue('B9', '22');
$spreadsheet->getActiveSheet()->setCellValue('B10', 4);
$spreadsheet->getActiveSheet()->setCellValue('B11', 6);
$spreadsheet->getActiveSheet()->setCellValue('B12', 12);

$spreadsheet->getActiveSheet()->setCellValue('B14', '=COUNT(B2:B12)');

$spreadsheet->getActiveSheet()->setCellValue('C1', 'Range 2');
$spreadsheet->getActiveSheet()->setCellValue('C2', 1);
$spreadsheet->getActiveSheet()->setCellValue('C3', 2);
$spreadsheet->getActiveSheet()->setCellValue('C4', 2);
$spreadsheet->getActiveSheet()->setCellValue('C5', 3);
$spreadsheet->getActiveSheet()->setCellValue('C6', 3);
$spreadsheet->getActiveSheet()->setCellValue('C7', 3);
$spreadsheet->getActiveSheet()->setCellValue('C8', '0');
$spreadsheet->getActiveSheet()->setCellValue('C9', 4);
$spreadsheet->getActiveSheet()->setCellValue('C10', 4);
$spreadsheet->getActiveSheet()->setCellValue('C11', 4);
$spreadsheet->getActiveSheet()->setCellValue('C12', 4);

$spreadsheet->getActiveSheet()->setCellValue('C14', '=COUNT(C2:C12)');

$spreadsheet->getActiveSheet()->setCellValue('D1', 'Range 3');
$spreadsheet->getActiveSheet()->setCellValue('D2', 2);
$spreadsheet->getActiveSheet()->setCellValue('D3', 3);
$spreadsheet->getActiveSheet()->setCellValue('D4', 4);

$spreadsheet->getActiveSheet()->setCellValue('D5', '=((D2 * D3) + D4) & " should be 10"');

$spreadsheet->getActiveSheet()->setCellValue('E1', 'Other functions');
$spreadsheet->getActiveSheet()->setCellValue('E2', '=PI()');
$spreadsheet->getActiveSheet()->setCellValue('E3', '=RAND()');
$spreadsheet->getActiveSheet()->setCellValue('E4', '=RANDBETWEEN(5, 10)');

$spreadsheet->getActiveSheet()->setCellValue('E14', 'Count of both ranges:');
$spreadsheet->getActiveSheet()->setCellValue('F14', '=COUNT(B2:C12)');

// Calculated data
$helper->log('Calculated data');
echo 'Value of B14 [=COUNT(B2:B12)]: ' . $spreadsheet->getActiveSheet()->getCell('B14')->getCalculatedValue() . "\r\n";

$helper->logEndingNotes();
