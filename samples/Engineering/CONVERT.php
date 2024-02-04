<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$category = 'Engineering';
$functionName = 'CONVERT';
$description = 'Converts a number from one measurement system to another';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$conversions = [
    [1, 'lbm', 'kg'],
    [1, 'gal', 'l'],
    [24, 'in', 'ft'],
    [100, 'yd', 'm'],
    [500, 'mi', 'km'],
    [7.5, 'min', 'sec'],
    [5, 'F', 'C'],
    [32, 'C', 'K'],
    [100, 'm2', 'ft2'],
];
$testDataCount = count($conversions);

$worksheet->fromArray($conversions, null, 'A1');

for ($row = 1; $row <= $testDataCount; ++$row) {
    $data = $conversions[$row - 1];
    $worksheet->setCellValue("D$row", "=CONVERT({$data[0]},\"{$data[1]}\",\"{$data[2]}\")");
}

$worksheet->setCellValue('H1', '=CONVERT(CONVERT(100,"m","ft"),"m","ft")');

for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(
        "(A$row): Unit of Measure Conversion Formula "
        . $worksheet->getCell('D' . $row)->getValue()
        . ' - '
        . $worksheet->getCell('A' . $row)->getValue()
        . ' '
        . $worksheet->getCell('B' . $row)->getValue()
        . ' is '
        . $worksheet->getCell('D' . $row)->getCalculatedValue()
        . ' '
        . $worksheet->getCell('C' . $row)->getValue()
    );
}

$helper->log('Old method for area conversions, before MS Excel introduced area Units of Measure');

$helper->log(
    "(A$row): Unit of Measure Conversion Formula "
    . $worksheet->getCell('H1')->getValue()
    . ' result is '
    . $worksheet->getCell('H1')->getCalculatedValue()
);
