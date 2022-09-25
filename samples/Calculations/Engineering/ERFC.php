<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Engineering';
$functionName = 'ERFC';
$description = 'Returns the complementary ERF function integrated between x and infinity';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData = [
    [0],
    [0.5],
    [1],
    [-1],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('C' . $row, '=ERFC(A' . $row . ')');
}

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(sprintf(
        '(E%d): %s The complementary error function integrated by %f and infinity is %f',
        $row,
        $worksheet->getCell('C' . $row)->getValue(),
        $worksheet->getCell('A' . $row)->getValue(),
        $worksheet->getCell('C' . $row)->getCalculatedValue(),
    ));
}
