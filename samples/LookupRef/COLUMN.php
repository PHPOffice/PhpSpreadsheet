<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$helper->log('Returns the column index of a cell.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

$worksheet->getCell('A1')->setValue('=COLUMN(C13)');
$worksheet->getCell('A2')->setValue('=COLUMN(E13:G15)');
$worksheet->getCell('F1')->setValue('=COLUMN()');

for ($row = 1; $row <= 2; ++$row) {
    $cell = $worksheet->getCell("A{$row}");
    $helper->log("A{$row}: " . $cell->getValue() . ' => ' . $cell->getCalculatedValue());
}

$cell = $worksheet->getCell('F1');
$helper->log('F1: ' . $cell->getValue() . ' => ' . $cell->getCalculatedValue());
