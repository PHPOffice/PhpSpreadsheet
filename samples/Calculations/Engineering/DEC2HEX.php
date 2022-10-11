<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Engineering';
$functionName = 'DEC2HEX';
$description = 'Converts a decimal number to hexadecimal';

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
    [12345678],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('B' . $row, '=DEC2HEX(A' . $row . ')');
}

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(sprintf(
        '(B%d): Decimal %s is hexadecimal %s',
        $row,
        $worksheet->getCell('A' . $row)->getValue(),
        $worksheet->getCell('B' . $row)->getCalculatedValue(),
    ));
}
