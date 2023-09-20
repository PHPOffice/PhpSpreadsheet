<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Engineering';
$functionName = 'OCT2DEC';
$description = 'Converts an octal number to decimal';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData = [
    [3],
    [7],
    [42],
    [70],
    [72],
    [77],
    [100],
    [127],
    [177],
    [456],
    [4567],
    [7777700001],
    [7777776543],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('B' . $row, '=OCT2DEC(A' . $row . ')');
}

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(sprintf(
        '(B%d): Octal %s is decimal %s',
        $row,
        $worksheet->getCell('A' . $row)->getValue(),
        $worksheet->getCell('B' . $row)->getCalculatedValue(),
    ));
}
