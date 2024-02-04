<?php

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Helpers as DateHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$helper->log('Returns the accrued interest for a security that pays interest at maturity.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    ['Issue Date', DateHelper::getDateValue('01-Jan-2012')],
    ['Settlement Date', DateHelper::getDateValue('31-Dec-2012')],
    ['Annual Coupon Rate', 0.08],
    ['Par Value', 10000],
];

// Some basic formatting for the data
$worksheet->fromArray($arguments, null, 'A1');
$worksheet->getStyle('B1:B2')->getNumberFormat()->setFormatCode('dd-mmm-yyyy');
$worksheet->getStyle('B3')->getNumberFormat()->setFormatCode('0.00%');
$worksheet->getStyle('B4')->getNumberFormat()->setFormatCode('$#,##0.00');

// Now the formula
$worksheet->setCellValue('B6', '=ACCRINTM(B1, B2, B3, B4)');
$worksheet->getStyle('B6')->getNumberFormat()->setFormatCode('$#,##0.00');

$helper->log($worksheet->getCell('B6')->getValue());
$helper->log('ACCRINTM() Result is ' . $worksheet->getCell('B6')->getFormattedValue());
