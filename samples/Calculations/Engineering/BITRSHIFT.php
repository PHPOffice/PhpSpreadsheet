<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Engineering';
$functionName = 'BITRSHIFT';
$description = 'Returns a number shifted right by the specified number of bits';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData = [
    [9],
    [15],
    [26],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('B' . $row, '=DEC2BIN(A' . $row . ')');
    $worksheet->setCellValue('C' . $row, '=BITRSHIFT(A' . $row . ',1)');
    $worksheet->setCellValue('D' . $row, '=DEC2BIN(C' . $row . ')');
    $worksheet->setCellValue('E' . $row, '=BITRSHIFT(A' . $row . ',2)');
    $worksheet->setCellValue('F' . $row, '=DEC2BIN(E' . $row . ')');
    $worksheet->setCellValue('G' . $row, '=BITRSHIFT(A' . $row . ',3)');
    $worksheet->setCellValue('H' . $row, '=DEC2BIN(G' . $row . ')');
}

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(sprintf(
        '(E%d): Bitwise Right Shift of %d (%s) by 1 bit is %d (%s)',
        $row,
        $worksheet->getCell('A' . $row)->getValue(),
        /** @scrutinizer ignore-type */
        $worksheet->getCell('B' . $row)->getCalculatedValue(),
        /** @scrutinizer ignore-type */
        $worksheet->getCell('C' . $row)->getCalculatedValue(),
        /** @scrutinizer ignore-type */
        $worksheet->getCell('D' . $row)->getCalculatedValue(),
    ));
    $helper->log(sprintf(
        '(E%d): Bitwise Right Shift of %d (%s) by 2 bits is %d (%s)',
        $row,
        $worksheet->getCell('A' . $row)->getValue(),
        /** @scrutinizer ignore-type */
        $worksheet->getCell('B' . $row)->getCalculatedValue(),
        /** @scrutinizer ignore-type */
        $worksheet->getCell('E' . $row)->getCalculatedValue(),
        /** @scrutinizer ignore-type */
        $worksheet->getCell('F' . $row)->getCalculatedValue(),
    ));
    $helper->log(sprintf(
        '(E%d): Bitwise Right Shift of %d (%s) by 3 bits is %d (%s)',
        $row,
        $worksheet->getCell('A' . $row)->getValue(),
        /** @scrutinizer ignore-type */
        $worksheet->getCell('B' . $row)->getCalculatedValue(),
        /** @scrutinizer ignore-type */
        $worksheet->getCell('G' . $row)->getCalculatedValue(),
        /** @scrutinizer ignore-type */
        $worksheet->getCell('H' . $row)->getCalculatedValue(),
    ));
}
