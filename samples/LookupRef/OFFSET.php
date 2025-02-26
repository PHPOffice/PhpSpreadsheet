<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$helper->log('Returns a cell range that is a specified number of rows and columns from a cell or range of cells.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

$data = [
    [null, 'Week 1', 'Week 2', 'Week 3', 'Week 4'],
    ['Sunday', 4500, 2200, 3800, 1500],
    ['Monday', 5500, 6100, 5200, 4800],
    ['Tuesday', 7000, 6200, 5000, 7100],
    ['Wednesday', 8000, 4000, 3900, 7600],
    ['Thursday', 5900, 5500, 6900, 7100],
    ['Friday', 4900, 6300, 6900, 5200],
    ['Saturday', 3500, 3900, 5100, 4100],
];
$worksheet->fromArray($data, null, 'A3');

$worksheet->getCell('H1')->setValue('=OFFSET(A3, 3, 1)');
$worksheet->getCell('H2')->setValue('=SUM(OFFSET(A3, 3, 1, 1, 4))');
$worksheet->getCell('H3')->setValue('=SUM(OFFSET(B3:E3, 3, 0))');
$worksheet->getCell('H4')->setValue('=SUM(OFFSET(E3, 1, -3, 7))');

for ($row = 1; $row <= 4; ++$row) {
    $cell = $worksheet->getCell("H{$row}");
    $helper->log("H{$row}: " . $cell->getValue() . ' => ' . $cell->getCalculatedValue());
}
