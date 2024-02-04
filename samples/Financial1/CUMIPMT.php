<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$helper->log('Returns the cumulative interest paid on a loan or investment, between two specified periods.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    ['Interest Rate (per period)', 0.05 / 12],
    ['Number of Periods', 5 * 12],
    ['Present Value', 50000],
];

// Some basic formatting for the data
$worksheet->fromArray($arguments, null, 'A1');
$worksheet->getStyle('B1')->getNumberFormat()->setFormatCode('0.00%');
$worksheet->getStyle('B3')->getNumberFormat()->setFormatCode('$#,##0.00');

// Now the formula
$baseRow = 5;
for ($year = 1; $year <= 5; ++$year) {
    $row = (string) ($baseRow + $year);
    $yearStartPeriod = (int) $year * 12 - 11;
    $yearEndPeriod = (int) $year * 12;

    $worksheet->setCellValue("A{$row}", "Yr {$year}");
    $worksheet->setCellValue("B{$row}", "=CUMIPMT(\$B\$1, \$B\$2, \$B\$3, {$yearStartPeriod}, {$yearEndPeriod}, 0)");
    $worksheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('$#,##0.00;-$#,##0.00');

    $helper->log($worksheet->getCell("B{$row}")->getValue());
    $helper->log("CUMIPMT() Year {$year} Result is " . $worksheet->getCell("B{$row}")->getFormattedValue());
}
