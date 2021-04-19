<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$helper->log('Returns the Future Value of an investment with periodic constant payments and a constant interest rate.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    ['Interest Rate', 0.05, 0.10],
    ['Pament Frequency', 12, 4],
    ['Duration (Years)', 5, 4],
    ['Investment', -1000.00, -2000.00],
    ['Payment Type', 0, 1],
];

// Some basic formatting for the data
$worksheet->fromArray($arguments, null, 'A1');
$worksheet->getStyle('B1:C1')->getNumberFormat()->setFormatCode('0.00%');
$worksheet->getStyle('B4:C4')->getNumberFormat()->setFormatCode('$#,##0.00');

// Now the formula
$worksheet->setCellValue('B8', '=FV(B1/B2, B3*B2, B4)');
$worksheet->setCellValue('C8', '=FV(C1/C2, C3*C2, C4, null, C5)');
$worksheet->getStyle('B8:C8')->getNumberFormat()->setFormatCode('$#,##0.00');

$helper->log($worksheet->getCell('B8')->getValue());
$helper->log('FV() Result is ' . $worksheet->getCell('B8')->getFormattedValue());

$helper->log($worksheet->getCell('C6')->getValue());
$helper->log('FV() Result is ' . $worksheet->getCell('C8')->getFormattedValue());
