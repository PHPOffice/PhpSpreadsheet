<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$category = 'Engineering';
$functionName = 'HEX2DEC';
$description = 'Converts a hexadecimal number to decimal';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData = [
    ['08'],
    ['42'],
    ['A2'],
    ['400'],
    ['1000'],
    ['1234'],
    ['ABCD'],
    ['C3B0'],
    ['FFFFFFFFF'],
    ['FFFFFFFFFF'],
    ['FFFFFFF800'],
    ['FEDCBA9876'],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('B' . $row, '=HEX2DEC(A' . $row . ')');
}

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(
        "(B$row): "
        . 'Hexadecimal ' . $worksheet->getCell("A$row")->getValue()
        . ' is decimal ' . $worksheet->getCell("B$row")->getCalculatedValue()
    );
}
