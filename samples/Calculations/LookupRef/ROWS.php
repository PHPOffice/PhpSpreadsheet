<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$helper->log('Returns the row index of a cell.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

$worksheet->getCell('A1')->setValue('=ROWS(C1:E4)');
$worksheet->getCell('A2')->setValue('=ROWS({1,2,3;4,5,6})');
$worksheet->getCell('A3')->setValue('=ROWS(C1:E4 D3:G5)');

for ($row = 1; $row <= 3; ++$row) {
    $cell = $worksheet->getCell("A{$row}");
    $helper->log("A{$row}: {$cell->getValue()} => {$cell->getCalculatedValue()}");
}
