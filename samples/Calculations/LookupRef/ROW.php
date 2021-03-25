<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$helper->log('Returns the row index of a cell.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

$worksheet->getCell('A1')->setValue('=ROW(C13)');
$worksheet->getCell('A2')->setValue('=ROW(E19:G21)');
$worksheet->getCell('A3')->setValue('=ROW()');

for ($row = 1; $row <= 3; ++$row) {
    $cell = $worksheet->getCell("A{$row}");
    $helper->log("A{$row}: {$cell->getValue()} => {$cell->getCalculatedValue()}");
}
