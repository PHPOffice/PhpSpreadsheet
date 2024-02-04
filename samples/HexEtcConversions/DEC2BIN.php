<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$category = 'Engineering';
$functionName = 'DEC2BIN';
$description = 'Converts a decimal number to binary';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData = [
    [-255],
    [-123],
    [-15],
    [-1],
    [5],
    [7],
    [19],
    [51],
    [121],
    [256],
    [511],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('B' . $row, '=DEC2BIN(A' . $row . ')');
}

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(
        "(B$row): "
        . 'Decimal ' . $worksheet->getCell("A$row")->getValue()
        . ' is binary ' . $worksheet->getCell("B$row")->getCalculatedValue()
    );
}
