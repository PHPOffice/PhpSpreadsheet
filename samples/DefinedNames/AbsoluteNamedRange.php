<?php

use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->setActiveSheetIndex(0);

// Set up some basic data for a timesheet
$worksheet
    ->setCellValue('A1', 'Charge Rate/hour:')
    ->setCellValue('B1', '7.50')
    ->setCellValue('A3', 'Date')
    ->setCellValue('B3', 'Hours')
    ->setCellValue('C3', 'Charge');

// Define named range using an absolute cell reference
$spreadsheet->addNamedRange(new NamedRange('CHARGE_RATE', $worksheet, '=$B$1'));

$workHours = [
    '2020-0-06' => 7.5,
    '2020-0-07' => 7.25,
    '2020-0-08' => 6.5,
    '2020-0-09' => 7.0,
    '2020-0-10' => 5.5,
];

// Populate the Timesheet
$startRow = 4;
$row = $startRow;
foreach ($workHours as $date => $hours) {
    $worksheet
        ->setCellValue("A{$row}", $date)
        ->setCellValue("B{$row}", $hours)
        ->setCellValue("C{$row}", "=B{$row}*CHARGE_RATE");
    ++$row;
}
$endRow = $row - 1;

++$row;
$worksheet
    ->setCellValue("B{$row}", "=SUM(B{$startRow}:B{$endRow})")
    ->setCellValue("C{$row}", "=SUM(C{$startRow}:C{$endRow})");

/** @var float */
$calc1 = $worksheet->getCell("B{$row}")->getCalculatedValue();
/** @var float */
$value = $worksheet->getCell('B1')->getValue();
/** @var float */
$calc2 = $worksheet->getCell("C{$row}")->getCalculatedValue();
$helper->log(sprintf(
    'Worked %.2f hours at a rate of %.2f - Charge to the client is %.2f',
    $calc1,
    $value,
    $calc2
));

$helper->write($spreadsheet, __FILE__, ['Xlsx']);
