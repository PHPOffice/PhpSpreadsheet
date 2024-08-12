<?php

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Helpers as DateHelper;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Constants as FinancialConstants;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$helper->log('Returns the prorated linear depreciation of an asset for a specified accounting period.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    ['Cost', 150.00],
    ['Date Purchased', DateHelper::getDateValue('01-Jan-2015')],
    ['First Period Date', DateHelper::getDateValue('30-Sep-2015')],
    ['Salvage Value', 20.00],
    ['Period', 1],
    ['Depreciation Rate', 0.20],
    ['Basis', FinancialConstants::BASIS_DAYS_PER_YEAR_360_EUROPEAN],
];

// Some basic formatting for the data
$worksheet->fromArray($arguments, null, 'A1');
$worksheet->getStyle('B1')->getNumberFormat()->setFormatCode('$#,##0.00');
$worksheet->getStyle('B2:B3')->getNumberFormat()->setFormatCode('dd-mmm-yyyy');
$worksheet->getStyle('B4')->getNumberFormat()->setFormatCode('$#,##0.00');
$worksheet->getStyle('B6')->getNumberFormat()->setFormatCode('0.00%');

// Now the formula
$worksheet->setCellValue('B10', '=AMORLINC(B1, B2, B3, B4, B5, B6, B7)');
$worksheet->getStyle('B10')->getNumberFormat()->setFormatCode('$#,##0.00');

$helper->log($worksheet->getCell('B10')->getValue());
$helper->log('AMORLINC() Result is ' . $worksheet->getCell('B10')->getFormattedValue());
