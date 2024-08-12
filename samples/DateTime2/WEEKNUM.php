<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$category = 'Date/Time';
$functionName = 'WEEKNUM';
$description = 'Returns the week number of a specific date';

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
    $worksheet->setCellValue('F' . $row, '=WEEKNUM(D' . $row . ')');
    $worksheet->setCellValue('G' . $row, '=WEEKNUM(D' . $row . ', 21)');
}
$worksheet->getStyle('E1:E' . $testDateCount)
    ->getNumberFormat()
    ->setFormatCode('yyyy-mm-dd');

// Test the formulae
for ($row = 1; $row <= $testDateCount; ++$row) {
    $helper->log(sprintf('(E%d): %s', $row, $worksheet->getCell('E' . $row)->getFormattedValue()));
    $helper->log('System 1 Week number is: ' . $worksheet->getCell('F' . $row)->getCalculatedValue());
    $helper->log('System 2 (ISO-8601) Week number is: ' . $worksheet->getCell('G' . $row)->getCalculatedValue());
}
