<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Engineering';
$functionName = 'CONVERT';
$description = 'Converts a number from one measurement system to another';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$conversions = [
    [1, '"lbm"', '"kg"'],
    [1, '"gal"', '"l"'],
    [24, '"in"', '"ft"'],
    [100, '"yd"', '"m"'],
    [500, '"mi"', '"km"'],
    [7.5, '"min"', '"sec"'],
    [5, '"F"', '"C"'],
    [32, '"C"', '"K"'],
    [100, '"m2"', '"ft2"'],
];
$testDataCount = count($conversions);

$worksheet->fromArray($conversions, null, 'A1');

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('D' . $row, '=CONVERT(' . implode(',', $conversions[$row - 1]) . ')');
}

$worksheet->setCellValue('H1', '=CONVERT(CONVERT(100,"m","ft"),"m","ft")');

for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(sprintf(
        '(A%d): Unit of Measure Conversion Formula %s - %d %s is %f %s',
        $row,
        $worksheet->getCell('D' . $row)->getValue(),
        $worksheet->getCell('A' . $row)->getValue(),
        trim($worksheet->getCell('B' . $row)->getValue(), '"'),
        $worksheet->getCell('D' . $row)->getCalculatedValue(),
        trim($worksheet->getCell('C' . $row)->getValue(), '"')
    ));
}

$helper->log('Old method for area conversions, before MS Excel introduced area Units of Measure');

$helper->log(sprintf(
    '(A%d): Unit of Measure Conversion Formula %s result is %s',
    $row,
    $worksheet->getCell('H1')->getValue(),
    $worksheet->getCell('H1')->getCalculatedValue()
));
