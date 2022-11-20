<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Engineering';
$functionName = 'BITOR';
$description = "Returns a bitwise 'OR' of two numbers";

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData = [
    [1, 5],
    [3, 5],
    [1, 6],
    [9, 6],
    [13, 25],
    [23, 10],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('C' . $row, '=TEXT(DEC2BIN(A' . $row . '), "00000")');
    $worksheet->setCellValue('D' . $row, '=TEXT(DEC2BIN(B' . $row . '), "00000")');
    $worksheet->setCellValue('E' . $row, '=BITOR(A' . $row . ',B' . $row . ')');
    $worksheet->setCellValue('F' . $row, '=TEXT(DEC2BIN(E' . $row . '), "00000")');
}

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(sprintf(
        '(E%d): Bitwise OR of %d (%s) and %d (%s) is %d (%s)',
        $row,
        $worksheet->getCell('A' . $row)->getValue(),
        $worksheet->getCell('C' . $row)->getCalculatedValue(),
        $worksheet->getCell('B' . $row)->getValue(),
        $worksheet->getCell('D' . $row)->getCalculatedValue(),
        $worksheet->getCell('E' . $row)->getCalculatedValue(),
        $worksheet->getCell('F' . $row)->getCalculatedValue(),
    ));
}
