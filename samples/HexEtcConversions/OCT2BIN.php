<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$category = 'Engineering';
$functionName = 'OCT2BIN';
$description = 'Converts an octal number to binary';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData = [
    [3],
    [7],
    [42],
    [70],
    [72],
    [77],
    [100],
    [127],
    [177],
    [456],
    [567],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('B' . $row, '=OCT2BIN(A' . $row . ')');
}

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(
        "(B$row): "
        . 'Octal ' . $worksheet->getCell("A$row")->getValue()
        . ' is binary ' . $worksheet->getCell("B$row")->getCalculatedValue()
    );
}
