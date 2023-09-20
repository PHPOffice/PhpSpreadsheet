<?php

use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require_once __DIR__ . '/../Header.php';

$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->setActiveSheetIndex(0);
$worksheet->setTitle('Base Data');

// Set up some basic data for a timesheet
$worksheet
    ->setCellValue('A1', 'Charge Rate/hour:')
    ->setCellValue('B1', '7.50');

// Define a global named range on the first worksheet for our Charge Rate
// CHARGE_RATE is an absolute cell reference that always points to cell B1
// Because it is defined globally, it will still be usable from any worksheet in the spreadsheet
$spreadsheet->addNamedRange(new NamedRange('CHARGE_RATE', $worksheet, '=$B$1'));

// Create a second worksheet as our client timesheet
$worksheet = $spreadsheet->addSheet(new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Client Timesheet'));

// Define named ranges
// HOURS_PER_DAY is a relative cell reference that always points to column B, but to a cell in the row where it is used
$spreadsheet->addNamedRange(new NamedRange('HOURS_PER_DAY', $worksheet, '=$B1'));

// Set up some basic data for a timesheet
$worksheet
    ->setCellValue('A1', 'Date')
    ->setCellValue('B1', 'Hours')
    ->setCellValue('C1', 'Charge');

$workHours = [
    '2020-0-06' => 7.5,
    '2020-0-07' => 7.25,
    '2020-0-08' => 6.5,
    '2020-0-09' => 7.0,
    '2020-0-10' => 5.5,
];

// Populate the Timesheet
$startRow = 2;
$row = $startRow;
foreach ($workHours as $date => $hours) {
    $worksheet
        ->setCellValue("A{$row}", $date)
        ->setCellValue("B{$row}", $hours)
        ->setCellValue("C{$row}", '=HOURS_PER_DAY*CHARGE_RATE');
    ++$row;
}
$endRow = $row - 1;

// COLUMN_TOTAL is another relative cell reference that always points to the same range of rows but to cell in the column where it is used
$spreadsheet->addNamedRange(new NamedRange('COLUMN_DATA_VALUES', $worksheet, "=A\${$startRow}:A\${$endRow}"));

++$row;
$worksheet
    ->setCellValue("B{$row}", '=SUM(COLUMN_DATA_VALUES)')
    ->setCellValue("C{$row}", '=SUM(COLUMN_DATA_VALUES)');

$helper->log(sprintf(
    'Worked %.2f hours at a rate of %s - Charge to the client is %.2f',
    $worksheet->getCell("B{$row}")->getCalculatedValue(),
    $chargeRateCellValue = $spreadsheet
        ->getSheetByName($spreadsheet->getNamedRange('CHARGE_RATE')->getWorksheet()->getTitle())
        ->getCell($spreadsheet->getNamedRange('CHARGE_RATE')->getCellsInRange()[0])->getValue(),
    $worksheet->getCell("C{$row}")->getCalculatedValue()
));

$helper->write($spreadsheet, __FILE__, ['Xlsx']);
