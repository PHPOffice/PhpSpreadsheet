<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Engineering';
$functionName = 'ERF';
$description = 'Returns the error function integrated between lower_limit and upper_limit';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData1 = [
    [0.745],
    [1],
    [1.5],
    [-2],
];

$testData2 = [
    [0, 1.5],
    [1, 2],
    [-2, 1],
];
$testDataCount1 = count($testData1);
$testDataCount2 = count($testData2);
$testData2StartRow = $testDataCount1 + 1;

$worksheet->fromArray($testData1, null, 'A1', true);
$worksheet->fromArray($testData2, null, "A{$testData2StartRow}", true);

for ($row = 1; $row <= $testDataCount1; ++$row) {
    $worksheet->setCellValue('C' . $row, '=ERF(A' . $row . ')');
}

for ($row = $testDataCount1 + 1; $row <= $testDataCount2 + $testDataCount1; ++$row) {
    $worksheet->setCellValue('C' . $row, '=ERF(A' . $row . ', B' . $row . ')');
}

// Test the formulae
$helper->log('ERF() With a single argument');
for ($row = 1; $row <= $testDataCount1; ++$row) {
    $helper->log(sprintf(
        '(C%d): %s The error function integrated between 0 and %f is %f',
        $row,
        $worksheet->getCell('C' . $row)->getValue(),
        $worksheet->getCell('A' . $row)->getValue(),
        $worksheet->getCell('C' . $row)->getCalculatedValue(),
    ));
}

$helper->log('ERF() With two arguments');
for ($row = $testDataCount1 + 1; $row <= $testDataCount2 + $testDataCount1; ++$row) {
    $helper->log(sprintf(
        '(C%d): %s The error function integrated between %f and %f is %f',
        $row,
        $worksheet->getCell('C' . $row)->getValue(),
        $worksheet->getCell('A' . $row)->getValue(),
        $worksheet->getCell('B' . $row)->getValue(),
        $worksheet->getCell('C' . $row)->getCalculatedValue(),
    ));
}
