<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Date/Time';
$functionName = 'HOUR';
$description = 'Returns the hour of a time value. The hour is given as an integer, ranging from 0 (12:00 AM) to 23 (11:00 PM)';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testTimes = [
    [0, 6, 0],
    [1, 12, 15],
    [3, 30, 12],
    [5, 17, 31],
    [8, 15, 45],
    [12, 45, 11],
    [14, 0, 30],
    [17, 55, 50],
    [19, 21, 8],
    [21, 10, 10],
    [23, 59, 59],
];
$testTimeCount = count($testTimes);

$worksheet->fromArray($testTimes, null, 'A1', true);

for ($row = 1; $row <= $testTimeCount; ++$row) {
    $worksheet->setCellValue('D' . $row, '=TIME(A' . $row . ',B' . $row . ',C' . $row . ')');
    $worksheet->setCellValue('E' . $row, '=D' . $row);
    $worksheet->setCellValue('F' . $row, '=HOUR(D' . $row . ')');
}
$worksheet->getStyle('E1:E' . $testTimeCount)
    ->getNumberFormat()
    ->setFormatCode('hh:mm:ss');

// Test the formulae
for ($row = 1; $row <= $testTimeCount; ++$row) {
    $helper->log(sprintf('(E%d): %s', $row, $worksheet->getCell('E' . $row)->getFormattedValue()));
    $helper->log('Hour is: ' . $worksheet->getCell('F' . $row)->getCalculatedValue());
}
