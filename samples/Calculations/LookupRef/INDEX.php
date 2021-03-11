<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$helper->log('Returns the row index of a cell.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

$data1 = [
    ['Apples', 'Lemons'],
    ['Bananas', 'Pears'],
];

$data2 = [
    [4, 6],
    [5, 3],
    [6, 9],
    [7, 5],
    [8, 3],
];

$worksheet->fromArray($data1, null, 'A1');
$worksheet->fromArray($data2, null, 'C1');

$worksheet->getCell('A11')->setValue('=INDEX(A1:B2, 2, 2)');
$worksheet->getCell('A12')->setValue('=INDEX(A1:B2, 2, 1)');
$worksheet->getCell('A13')->setValue('=INDEX({1,2;3,4}, 0, 2)');
$worksheet->getCell('A14')->setValue('=INDEX(C1:C5, 5)');
$worksheet->getCell('A15')->setValue('=INDEX(C1:D5, 5, 2)');
$worksheet->getCell('A16')->setValue('=SUM(INDEX(C1:D5, 5, 0))');

for ($row = 11; $row <= 16; ++$row) {
    $cell = $worksheet->getCell("A{$row}");
    $helper->log("A{$row}: {$cell->getValue()} => {$cell->getCalculatedValue()}");
}
