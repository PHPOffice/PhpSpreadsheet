<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Engineering';
$functionName = 'COMPLEX';
$description = 'Converts real and imaginary coefficients into a complex number of the form x + yi or x + yj';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData = [
    [3, 4],
    [3, 4, '"j"'],
    [3.5, 4.75],
    [0, 1],
    [1, 0],
    [0, -1],
    [0, 2],
    [2, 0],
];
$testDataCount = count($testData);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('A' . $row, '=COMPLEX(' . implode(',', $testData[$row - 1]) . ')');
}

for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(sprintf(
        '(A%d): Formula %s result is %s',
        $row,
        $worksheet->getCell('A' . $row)->getValue(),
        $worksheet->getCell('A' . $row)->getCalculatedValue()
    ));
}
