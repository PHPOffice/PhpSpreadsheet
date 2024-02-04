<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$category = 'Engineering';
$functionName = 'BIN2OCT';
$description = 'Converts a binary number to octal';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData = [
    [101],
    [110110],
    [1000000],
    [11111111],
    [100010101],
    [110001100],
    [111111111],
    [1111111111],
    [1100110011],
    [1000000000],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('B' . $row, '=BIN2OCT(A' . $row . ')');
}

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(
        "(B$row): "
        . 'Binary ' . $worksheet->getCell("A$row")->getValue()
        . ' is octal ' . $worksheet->getCell("B$row")->getCalculatedValue()
    );
}
