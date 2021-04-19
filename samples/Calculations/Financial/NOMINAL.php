<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$helper->log('Returns the nominal interest rate for a given effective interest rate and number of');
$helper->log('compounding periods per year.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    [0.10, 4],
    [0.10, 2],
    [0.025, 12],
];

$worksheet->fromArray($arguments, null, 'A1');
$worksheet->getStyle('B1:B3')->getNumberFormat()->setFormatCode('0.00%');

// Now the formula
for ($row = 1; $row <= 3; ++$row) {
    $worksheet->setCellValue("C{$row}", "=NOMINAL(A{$row}, B{$row})");
    $worksheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode('0.00%');

    $helper->log($worksheet->getCell("C{$row}")->getValue());
    $helper->log('NOMINAL() Result is ' . $worksheet->getCell("C{$row}")->getFormattedValue());
}
