<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Date/Time';
$functionName = 'DAY';
$description = 'Returns the day of a date, an integer ranging from 1 to 31';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testDates = [
    [1900, 1, 1],
    [1904, 2, 14],
    [1936, 3, 17],
    [1964, 4, 29],
    [1999, 5, 18],
    [2000, 6, 21],
    [2019, 7, 4],
    [2020, 8, 31],
    [1956, 9, 10],
    [2010, 10, 10],
    [1982, 11, 30],
    [1960, 12, 19],
    ['=YEAR(TODAY())', '=MONTH(TODAY())', '=DAY(TODAY())'],
];
$testDateCount = count($testDates);

$worksheet->fromArray($testDates, null, 'A1', true);

for ($row = 1; $row <= $testDateCount; ++$row) {
    $worksheet->setCellValue('D' . $row, '=DATE(A' . $row . ',B' . $row . ',C' . $row . ')');
    $worksheet->setCellValue('E' . $row, '=D' . $row);
    $worksheet->setCellValue('F' . $row, '=DAY(D' . $row . ')');
}
$worksheet->getStyle('E1:E' . $testDateCount)
    ->getNumberFormat()
    ->setFormatCode('yyyy-mm-dd');

// Test the formulae
for ($row = 1; $row <= $testDateCount; ++$row) {
    $helper->log(sprintf('(E%d): %s', $row, $worksheet->getCell('E' . $row)->getFormattedValue()));
    $helper->log('Day is: ' . $worksheet->getCell('F' . $row)->getCalculatedValue());
}
