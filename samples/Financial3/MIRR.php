<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$helper->log('Returns the Modified Internal Rate of Return for a supplied series of periodic cash flows.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    ['Initial Investment', -100.00],
    ['Year 1 Income', 18.00],
    ['Year 2 Income', 22.50, 'MIRR after 3 Years'],
    ['Year 3 Income', 28.00],
    ['Year 4 Income', 35.50, 'MIRR after 5 Years'],
    ['Year 5 Income', 45.00],
    [null],
    ['Finance Rate', 0.055],
    ['Re-invest Rate', 0.05],
];

// Some basic formatting for the data
$worksheet->fromArray($arguments, null, 'A1');
$worksheet->getStyle('B1:B6')->getNumberFormat()->setFormatCode('$#,##0.00;-$#,##0.00');
$worksheet->getStyle('B8:B9')->getNumberFormat()->setFormatCode('0.00%');

// Now the formula
$worksheet->setCellValue('C4', '=MIRR(B1:B4, B8, B9)');
$worksheet->getStyle('C4')->getNumberFormat()->setFormatCode('0.00%');

$helper->log($worksheet->getCell('C4')->getValue());
$helper->log('MIRR() Result is ' . $worksheet->getCell('C4')->getFormattedValue());

$worksheet->setCellValue('C6', '=MIRR(B1:B6, B8, B9)');
$worksheet->getStyle('C6')->getNumberFormat()->setFormatCode('0.00%');

$helper->log($worksheet->getCell('C6')->getValue());
$helper->log('MIRR() Result is ' . $worksheet->getCell('C6')->getFormattedValue());
