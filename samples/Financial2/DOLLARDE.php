<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$helper->log('Returns the dollar value in fractional notation, into a dollar value expressed as a decimal.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    [1.01, 16],
    [1.1, 16],
    [1.03, 32],
    [1.3, 32],
    [1.12, 32],
];

$worksheet->fromArray($arguments, null, 'A1');

// Now the formula
for ($row = 1; $row <= 5; ++$row) {
    $worksheet->setCellValue("C{$row}", "=DOLLARDE(A{$row}, B{$row})");

    $helper->log($worksheet->getCell("C{$row}")->getValue());
    $helper->log('DOLLARDE() Result is ' . $worksheet->getCell("C{$row}")->getFormattedValue());
}
