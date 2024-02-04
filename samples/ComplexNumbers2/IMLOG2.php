<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$category = 'Engineering';
$functionName = 'IMLOG2';
$description = 'Returns the base-2 logarithm of a complex number in x + yi or x + yj text format';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData = [
    ['3+4i'],
    ['5-12i'],
    ['3.25+7.5i'],
    ['3.25-12.5i'],
    ['-3.25+7.5i'],
    ['-3.25-7.5i'],
    ['0-j'],
    ['0-2.5j'],
    ['0+j'],
    ['0+1.25j'],
    [4],
    [-2.5],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('B' . $row, '=IMLOG2(A' . $row . ')');
}

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(
        "(E$row): The Base-2 Logarithm of "
        . $worksheet->getCell('A' . $row)->getValue()
        . ' is '
        . $worksheet->getCell('B' . $row)->getCalculatedValue()
    );
}
