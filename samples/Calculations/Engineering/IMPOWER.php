<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Engineering';
$functionName = 'IMPOWER';
$description = 'Returns a complex number in x + yi or x + yj text format raised to a power';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData = [
    ['3+4i', 2],
    ['5-12i', 2],
    ['3.25+7.5i', 3],
    ['3.25-12.5i', 2],
    ['-3.25+7.5i', 3],
    ['-3.25-7.5i', 4],
    ['0-j', 5],
    ['0-2.5j', 3],
    ['0+j', 2.5],
    ['0+1.25j', 2],
    [4, 3],
    [-2.5, 2],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('C' . $row, '=IMPOWER(A' . $row . ', B' . $row . ')');
}

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(sprintf(
        '(E%d): %s raised to the power of %s is %s',
        $row,
        $worksheet->getCell('A' . $row)->getValue(),
        $worksheet->getCell('B' . $row)->getValue(),
        $worksheet->getCell('C' . $row)->getCalculatedValue(),
    ));
}
