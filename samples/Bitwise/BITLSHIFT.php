<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$category = 'Engineering';
$functionName = 'BITLSHIFT';
$description = 'Returns a number shifted left by the specified number of bits';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData = [
    [1],
    [3],
    [9],
    [15],
    [26],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('B' . $row, '=DEC2BIN(A' . $row . ')');
    $worksheet->setCellValue('C' . $row, '=BITLSHIFT(A' . $row . ',1)');
    $worksheet->setCellValue('D' . $row, '=DEC2BIN(C' . $row . ')');
    $worksheet->setCellValue('E' . $row, '=BITLSHIFT(A' . $row . ',2)');
    $worksheet->setCellValue('F' . $row, '=DEC2BIN(E' . $row . ')');
    $worksheet->setCellValue('G' . $row, '=BITLSHIFT(A' . $row . ',3)');
    $worksheet->setCellValue('H' . $row, '=DEC2BIN(G' . $row . ')');
}

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(
        "(E$row): Bitwise Left Shift of "
        . $worksheet->getCell('A' . $row)->getValueString()
        . ' ('
        . $worksheet->getCell('B' . $row)->getCalculatedValueString()
        . ') by 1 bit is '
        . $worksheet->getCell('C' . $row)->getCalculatedValueString()
        . ' ('
        . $worksheet->getCell('D' . $row)->getCalculatedValueString()
        . ')'
    );
    $helper->log(
        "(E$row): Bitwise Left Shift of "
        . $worksheet->getCell('A' . $row)->getValueString()
        . ' ('
        . $worksheet->getCell('B' . $row)->getCalculatedValueString()
        . ') by 2 bits is '
        . $worksheet->getCell('E' . $row)->getCalculatedValueString()
        . ' ('
        . $worksheet->getCell('F' . $row)->getCalculatedValueString()
        . ')'
    );
    $helper->log(
        "(E$row): Bitwise Left Shift of "
        . $worksheet->getCell('A' . $row)->getValueString()
        . ' ('
        . $worksheet->getCell('B' . $row)->getCalculatedValueString()
        . ') by 3 bits is '
        . $worksheet->getCell('G' . $row)->getCalculatedValueString()
        . ' ('
        . $worksheet->getCell('H' . $row)->getCalculatedValueString()
        . ')'
    );
}
