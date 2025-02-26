<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$category = 'Engineering';
$functionName = 'DELTA';
$description = 'Tests whether two values are equal. Returns 1 if number1 = number2; returns 0 otherwise. This function is also known as the Kronecker Delta function';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData = [
    [4, 5],
    [3, 3],
    [0.5, 0],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('C' . $row, '=DELTA(A' . $row . ',B' . $row . ')');
}

$comparison = [
    0 => 'The values are not equal',
    1 => 'The values are equal',
];

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(
        "(E$row): Compare values "
        . $worksheet->getCell('A' . $row)->getValue()
        . ' and '
        . $worksheet->getCell('B' . $row)->getValue()
        . ' - Result is '
        . $worksheet->getCell('C' . $row)->getCalculatedValue()
        . ' - '
        . $comparison[$worksheet->getCell('C' . $row)->getCalculatedValue()]
    );
}
