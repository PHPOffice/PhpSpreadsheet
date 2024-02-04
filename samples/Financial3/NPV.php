<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$helper->log('Returns the Net Present Value of an investment, based on a supplied discount rate,');
$helper->log('and a series of future payments and income.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    ['Annual Discount Rate', 0.02, 0.05],
    ['Initial Investment Cost', -5000.00, -10000],
    ['Return from Year 1', 800.00, 2000.00],
    ['Return from Year 2', 950.00, 2400.00],
    ['Return from Year 3', 1080.00, 2900.00],
    ['Return from Year 4', 1220.00, 3500.00],
    ['Return from Year 5', 1500.00, 4100.00],
];

// Some basic formatting for the data
$worksheet->fromArray($arguments, null, 'A1');
$worksheet->getStyle('B1:C1')->getNumberFormat()->setFormatCode('0.00%');
$worksheet->getStyle('B2:C7')->getNumberFormat()->setFormatCode('$#,##0.00');

// Now the formula
// When initial investment is made at the end of the first period
$worksheet->setCellValue('B10', '=NPV(B1, B2:B7)');
$worksheet->getStyle('B10')->getNumberFormat()->setFormatCode('$#,##0.00');

$helper->log($worksheet->getCell('B10')->getValue());
$helper->log('NPV() Result is ' . $worksheet->getCell('B10')->getFormattedValue());

// When initial investment is made at the start of the first period
$worksheet->setCellValue('C10', '=NPV(C1, C3:C7) + C2');
$worksheet->getStyle('C10')->getNumberFormat()->setFormatCode('$#,##0.00');

$helper->log($worksheet->getCell('C10')->getValue());
$helper->log('NPV() Result is ' . $worksheet->getCell('C10')->getFormattedValue());
