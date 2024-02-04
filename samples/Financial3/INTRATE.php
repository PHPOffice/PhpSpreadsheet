<?php

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Helpers as DateHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$helper->log('Returns the interest rate for a fully invested security.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    ['Settlement Date', DateHelper::getDateValue('01-Apr-2005')],
    ['Maturity Date', DateHelper::getDateValue('31-Mar-2010')],
    ['Investment', 1000.00],
    ['Investment', 2125.00],
];

// Some basic formatting for the data
$worksheet->fromArray($arguments, null, 'A1');
$worksheet->getStyle('B1:B2')->getNumberFormat()->setFormatCode('dd-mmm-yyyy');
$worksheet->getStyle('B3:B4')->getNumberFormat()->setFormatCode('$#,##0.00');

// Now the formula
$worksheet->setCellValue('B7', '=INTRATE(B1, B2, B3, B4)');
$worksheet->getStyle('B7')->getNumberFormat()->setFormatCode('0.00%');

$helper->log($worksheet->getCell('B7')->getValue());
$helper->log('INTRATE() Result is ' . $worksheet->getCell('B7')->getFormattedValue());
