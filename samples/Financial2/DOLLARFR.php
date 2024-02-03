<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$helper->log('Returns the dollar value expressed as a decimal number, into a dollar price, expressed as a fraction.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    [1.0625, 16],
    [1.625, 16],
    [1.09375, 32],
    [1.9375, 32],
    [1.375, 32],
];

$worksheet->fromArray($arguments, null, 'A1');

// Now the formula
for ($row = 1; $row <= 5; ++$row) {
    $worksheet->setCellValue("C{$row}", "=DOLLARFR(A{$row}, B{$row})");

    $helper->log($worksheet->getCell("C{$row}")->getValue());
    $helper->log('DOLLARFR() Result is ' . $worksheet->getCell("C{$row}")->getFormattedValue());
}
