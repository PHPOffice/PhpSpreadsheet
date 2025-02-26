<?php

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Helpers as DateHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$helper->log('Returns the accrued interest for a security that pays periodic interest.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    ['Issue Date', DateHelper::getDateValue('01-Jan-2012')],
    ['First Interest Date', DateHelper::getDateValue('01-Apr-2012')],
    ['Settlement Date', DateHelper::getDateValue('31-Dec-2013')],
    ['Annual Coupon Rate', 0.08],
    ['Par Value', 10000],
    ['Frequency', 4],
];

// Some basic formatting for the data
$worksheet->fromArray($arguments, null, 'A1');
$worksheet->getStyle('B1:B3')->getNumberFormat()->setFormatCode('dd-mmm-yyyy');
$worksheet->getStyle('B4')->getNumberFormat()->setFormatCode('0.00%');
$worksheet->getStyle('B5')->getNumberFormat()->setFormatCode('$#,##0.00');

// Now the formula
$worksheet->setCellValue('B10', '=ACCRINT(B1, B2, B3, B4, B5, B6)');
$worksheet->getStyle('B10')->getNumberFormat()->setFormatCode('$#,##0.00');

$helper->log($worksheet->getCell('B10')->getValue());
$helper->log('ACCRINT() Result is ' . $worksheet->getCell('B10')->getFormattedValue());
