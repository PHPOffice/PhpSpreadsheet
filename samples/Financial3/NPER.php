<?php

use PhpOffice\PhpSpreadsheet\Calculation\Financial\Constants as FinancialConstants;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$helper->log('Returns the number of periods required to pay off a loan, for a constant periodic payment');
$helper->log('and a constant interest rate.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    ['Interest Rate', 0.04, 0.06],
    ['Payments per Year', 1, 4],
    ['Payment Amount', -6000.00, -2000],
    ['Present Value', 50000, 60000],
    ['Future Value', null, 30000],
    ['Payment Type', null, FinancialConstants::PAYMENT_BEGINNING_OF_PERIOD],
];

// Some basic formatting for the data
$worksheet->fromArray($arguments, null, 'A1');
$worksheet->getStyle('B1:C1')->getNumberFormat()->setFormatCode('0.00%');
$worksheet->getStyle('B3:C5')->getNumberFormat()->setFormatCode('$#,##0.00');

// Now the formula
$worksheet->setCellValue('B8', '=NPER(B1/B2, B3, B4)');

$helper->log($worksheet->getCell('B8')->getValue());
$helper->log('NPER() Result is ' . $worksheet->getCell('B8')->getFormattedValue());

$worksheet->setCellValue('C8', '=NPER(C1/C2, C3, C4, C5, C6)');

$helper->log($worksheet->getCell('C8')->getValue());
$helper->log('NPER() Result is ' . $worksheet->getCell('C8')->getFormattedValue());
