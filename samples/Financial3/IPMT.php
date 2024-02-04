<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$helper->log('Returns the interest payment, during a specific period of a loan or investment that is paid in,');
$helper->log('constant periodic payments, with a constant interest rate.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    ['Interest Rate', 0.05],
    ['Number of Years', 5],
    ['Present Value', 50000.00],
];

// Some basic formatting for the data
$worksheet->fromArray($arguments, null, 'A1');
$worksheet->getStyle('B1')->getNumberFormat()->setFormatCode('0.00%');
$worksheet->getStyle('B3')->getNumberFormat()->setFormatCode('$#,##0.00');

// Now the formula
$baseRow = 6;
for ($month = 1; $month <= 12; ++$month) {
    $row = (string) ($baseRow + $month);

    $worksheet->setCellValue("A{$row}", "Payment for Mth {$month}");
    $worksheet->setCellValue("B{$row}", "=IPMT(\$B\$1/12, {$month}, \$B\$2*12, \$B\$3)");
    $worksheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('$#,##0.00;-$#,##0.00');

    $helper->log($worksheet->getCell("B{$row}")->getValue());
    $helper->log("IPMT() Month {$month} Result is " . $worksheet->getCell("B{$row}")->getFormattedValue());
}
