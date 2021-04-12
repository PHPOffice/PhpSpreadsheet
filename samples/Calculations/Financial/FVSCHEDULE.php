<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$helper->log('Returns the Future Value of an initial principal, after applying a series of compound interest rates.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    ['Principal'],
    [10000.00],
    [null],
    ['Schedule'],
    [0.05],
    [0.05],
    [0.035],
    [0.035],
    [0.035],
];

// Some basic formatting for the data
$worksheet->fromArray($arguments, null, 'A1');
$worksheet->getStyle('A2')->getNumberFormat()->setFormatCode('$#,##0.00');
$worksheet->getStyle('A5:A9')->getNumberFormat()->setFormatCode('0.00%');

// Now the formula
$worksheet->setCellValue('B1', '=FVSCHEDULE(A2, A5:A9)');
$worksheet->getStyle('B1')->getNumberFormat()->setFormatCode('$#,##0.00');

$helper->log($worksheet->getCell('B1')->getValue());
$helper->log('FVSCHEDULE() Result is ' . $worksheet->getCell('B1')->getFormattedValue());
