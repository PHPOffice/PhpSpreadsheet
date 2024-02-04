<?php

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Helpers as DateHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$helper->log('Returns the number of days in the coupon period that contains the settlement date.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    ['Settlement Date', DateHelper::getDateValue('01-Jan-2011')],
    ['Maturity Date', DateHelper::getDateValue('25-Oct-2012')],
    ['Frequency', 4],
];

// Some basic formatting for the data
$worksheet->fromArray($arguments, null, 'A1');
$worksheet->getStyle('B1:B2')->getNumberFormat()->setFormatCode('dd-mmm-yyyy');

// Now the formula
$worksheet->setCellValue('B6', '=COUPDAYS(B1, B2, B3)');

$helper->log($worksheet->getCell('B6')->getValue());
$helper->log('COUPDAYS() Result is ' . $worksheet->getCell('B6')->getFormattedValue());
