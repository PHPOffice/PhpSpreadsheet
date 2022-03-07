<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$helper->log('Returns the number of columns in an array or reference.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

$worksheet->getCell('A1')->setValue('=COLUMNS(C1:G4)');
$worksheet->getCell('A2')->setValue('=COLUMNS({1,2,3;4,5,6})');
$worksheet->getCell('A3')->setValue('=ROWS(C1:E4 D3:G5)');
$worksheet->getCell('A4')->setValue('=COLUMNS(1:1)');

for ($row = 1; $row <= 4; ++$row) {
    $cell = $worksheet->getCell("A{$row}");
    $helper->log("A{$row}: {$cell->getValue()} => {$cell->getCalculatedValue()}");
}
