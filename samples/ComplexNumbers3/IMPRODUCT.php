<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$category = 'Engineering';
$functionName = 'IMPRODUCT';
$description = 'Returns the product of two or more complex numbers in x + yi or x + yj text format';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData = [
    ['3+4i', '5-3i'],
    ['3+4i', '5+3i'],
    ['-238+240i', '10+24i'],
    ['1+2i', 30],
    ['1+2i', '2i'],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('C' . $row, '=IMPRODUCT(A' . $row . ', B' . $row . ')');
}

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(
        "(E$row): The Product of "
        . $worksheet->getCell('A' . $row)->getValueString()
        . ' and '
        . $worksheet->getCell('B' . $row)->getValueString()
        . ' is '
        . $worksheet->getCell('C' . $row)->getCalculatedValueString()
    );
}
