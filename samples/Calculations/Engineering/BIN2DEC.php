<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Engineering';
$functionName = 'BIN2DEC';
$description = 'Converts a binary number to decimal';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData = [
    [101],
    [110110],
    [1_000_000],
    [11_111_111],
    [100_010_101],
    [110_001_100],
    [111_111_111],
    [1_111_111_111],
    [1_100_110_011],
    [1_000_000_000],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('B' . $row, '=BIN2DEC(A' . $row . ')');
}

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(
        "(B$row): "
        . 'Binary ' . $worksheet->getCell("A$row")->getValue()
        . ' is decimal ' . $worksheet->getCell("B$row")->getCalculatedValue()
    );
}
