<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
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
    $helper->log(
        "(E$row): Bitwise OR of "
        . $worksheet->getCell('A' . $row)->getValueString()
        . ' ('
        . $worksheet->getCell('C' . $row)->getCalculatedValueString()
        . ') and '
        . $worksheet->getCell('B' . $row)->getValueString()
        . '('
        . $worksheet->getCell('D' . $row)->getCalculatedValueString()
        . ') is '
        . $worksheet->getCell('E' . $row)->getCalculatedValueString()
        . ' ('
        . $worksheet->getCell('F' . $row)->getCalculatedValueString()
        . ')'
    );
}
