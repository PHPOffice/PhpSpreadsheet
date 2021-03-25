<?php

use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$helper->log('Returns the cell specified by a text string.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

$data = [
    [8, 9, 0],
    [3, 4, 5],
    [9, 1, 3],
    [4, 6, 2],
];
$worksheet->fromArray($data, null, 'C1');

$spreadsheet->addNamedRange(new NamedRange('NAMED_RANGE_FOR_CELL_D4', $worksheet, '="$D$4"'));

$worksheet->getCell('A1')->setValue('=INDIRECT("C1")');
$worksheet->getCell('A2')->setValue('=INDIRECT("D"&4)');
$worksheet->getCell('A3')->setValue('=INDIRECT("E"&ROW())');
$worksheet->getCell('A4')->setValue('=SUM(INDIRECT("$C$4:$E$4"))');
$worksheet->getCell('A5')->setValue('=INDIRECT(NAMED_RANGE_FOR_CELL_D4)');

for ($row = 1; $row <= 5; ++$row) {
    $cell = $worksheet->getCell("A{$row}");
    $helper->log("A{$row}: {$cell->getValue()} => {$cell->getCalculatedValue()}");
}
