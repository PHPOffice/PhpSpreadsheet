<?php

use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require_once __DIR__ . '/../Header.php';

$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->setActiveSheetIndex(0);

$clients = [
    'Client #1 - Full Hourly Rate' => [
        '2020-0-06' => 2.5,
        '2020-0-07' => 2.25,
        '2020-0-08' => 6.0,
        '2020-0-09' => 3.0,
        '2020-0-10' => 2.25,
    ],
    'Client #2 - Full Hourly Rate' => [
        '2020-0-06' => 1.5,
        '2020-0-07' => 2.75,
        '2020-0-08' => 0.0,
        '2020-0-09' => 4.5,
        '2020-0-10' => 3.5,
    ],
    'Client #3 - Reduced Hourly Rate' => [
        '2020-0-06' => 3.5,
        '2020-0-07' => 2.5,
        '2020-0-08' => 1.5,
        '2020-0-09' => 0.0,
        '2020-0-10' => 1.25,
    ],
];

foreach ($clients as $clientName => $workHours) {
    $worksheet = $spreadsheet->addSheet(new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $clientName));

    // Set up some basic data for a timesheet
    $worksheet
        ->setCellValue('A1', 'Charge Rate/hour:')
        ->setCellValue('B1', '7.50')
        ->setCellValue('A3', 'Date')
        ->setCellValue('B3', 'Hours')
        ->setCellValue('C3', 'Charge');

    // Define named ranges
    // CHARGE_RATE is an absolute cell reference that always points to cell B1
    $spreadsheet->addNamedRange(new NamedRange('CHARGE_RATE', $worksheet, '=$B$1', true));
    // HOURS_PER_DAY is a relative cell reference that always points to column B, but to a cell in the row where it is used
    $spreadsheet->addNamedRange(new NamedRange('HOURS_PER_DAY', $worksheet, '=$B1', true));

    // Populate the Timesheet
    $startRow = 4;
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
    $spreadsheet->addNamedRange(new NamedRange('COLUMN_TOTAL', $worksheet, "=A\${$startRow}:A\${$endRow}", true));

    ++$row;
    $worksheet
        ->setCellValue("B{$row}", '=SUM(COLUMN_TOTAL)')
        ->setCellValue("C{$row}", '=SUM(COLUMN_TOTAL)');
}
$spreadsheet->removeSheetByIndex(0);

// Set the reduced charge rate for our special client
$worksheet
    ->setCellValue('B1', 4.5);

foreach ($spreadsheet->getAllSheets() as $worksheet) {
    $helper->log(sprintf(
        'Worked %.2f hours for "%s" at a rate of %.2f - Charge to the client is %.2f',
        $worksheet->getCell("B{$row}")->getCalculatedValue(),
        $worksheet->getTitle(),
        $worksheet->getCell('B1')->getValue(),
        $worksheet->getCell("C{$row}")->getCalculatedValue()
    ));
}
$worksheet = $spreadsheet->setActiveSheetIndex(0);

$helper->write($spreadsheet, __FILE__, ['Xlsx']);
