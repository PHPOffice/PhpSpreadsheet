<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$category = 'Engineering';
$functionName = 'HEX2BIN';
$description = 'Converts a hexadecimal number to binary';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData = [
    [3],
    [8],
    [42],
    [99],
    ['A2'],
    ['F0'],
    ['100'],
    ['128'],
    ['1AB'],
    ['1FF'],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('B' . $row, '=HEX2BIN(A' . $row . ')');
}

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(
        "(B$row): "
        . 'Hexadecimal ' . $worksheet->getCell("A$row")->getValue()
        . ' is binary ' . $worksheet->getCell("B$row")->getCalculatedValue()
    );
}
