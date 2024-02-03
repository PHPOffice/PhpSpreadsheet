<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$helper->log('Returns the Internal Rate of Return for a supplied series of periodic cash flows.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    ['Initial Investment', -100.00],
    ['Year 1 Income', 20.00],
    ['Year 2 Income', 24.00, 'IRR after 3 Years'],
    ['Year 3 Income', 28.80],
    ['Year 4 Income', 34.56, 'IRR after 5 Years'],
    ['Year 5 Income', 41.47],
];

// Some basic formatting for the data
$worksheet->fromArray($arguments, null, 'A1');
$worksheet->getStyle('B1:B6')->getNumberFormat()->setFormatCode('$#,##0.00;-$#,##0.00');

// Now the formula
$worksheet->setCellValue('C4', '=IRR(B1:B4)');
$worksheet->getStyle('C4')->getNumberFormat()->setFormatCode('0.00%');

$helper->log($worksheet->getCell('C4')->getValue());
$helper->log('IRR() Result is ' . $worksheet->getCell('C4')->getFormattedValue());

$worksheet->setCellValue('C6', '=IRR(B1:B6)');
$worksheet->getStyle('C6')->getNumberFormat()->setFormatCode('0.00%');

$helper->log($worksheet->getCell('C6')->getValue());
$helper->log('IRR() Result is ' . $worksheet->getCell('C6')->getFormattedValue());
