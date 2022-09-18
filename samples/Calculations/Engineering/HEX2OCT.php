<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Engineering';
$functionName = 'HEX2OCT';
$description = 'Converts a hexadecimal number to octal';

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
    ['100'],
    ['1234'],
    ['ABCD'],
    ['C3B0'],
    ['FFFFFFFFFF'],
    ['FFFFFFF800'],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('B' . $row, '=HEX2OCT(A' . $row . ')');
}

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(sprintf(
        '(B%d): Hexadecimal %s is octal %s',
        $row,
        $worksheet->getCell('A' . $row)->getValue(),
        $worksheet->getCell('B' . $row)->getCalculatedValue(),
    ));
}
