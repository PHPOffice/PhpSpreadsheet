<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Date/Time';
$functionName = 'DATE';
$description = 'Returns the Excel serial number of a particular date';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testDates = [[2012, 3, 26], [2012, 2, 29], [2012, 4, 1], [2012, 12, 25],
    [2012, 10, 31], [2012, 11, 5], [2012, 1, 1], [2012, 3, 17],
    [2011, 2, 29], [7, 5, 3], [2012, 13, 1], [2012, 11, 45],
    [2012, 0, 0], [2012, 1, 0], [2012, 0, 1],
    [2012, -2, 2], [2012, 2, -2], [2012, -2, -2],
];
$testDateCount = count($testDates);

$worksheet->fromArray($testDates, null, 'A1', true);

for ($row = 1; $row <= $testDateCount; ++$row) {
    $worksheet->setCellValue('D' . $row, '=DATE(A' . $row . ',B' . $row . ',C' . $row . ')');
    $worksheet->setCellValue('E' . $row, '=D' . $row);
}
$worksheet->getStyle('E1:E' . $testDateCount)
    ->getNumberFormat()
    ->setFormatCode('yyyy-mm-dd');

// Test the formulae
for ($row = 1; $row <= $testDateCount; ++$row) {
    $helper->log("(A{$row}) Year: " . $worksheet->getCell('A' . $row)->getFormattedValue());
    $helper->log("(B{$row}) Month: " . $worksheet->getCell('B' . $row)->getFormattedValue());
    $helper->log("(C{$row}) Day: " . $worksheet->getCell('C' . $row)->getFormattedValue());
    $helper->log('Formula: ' . $worksheet->getCell('D' . $row)->getValue());
    $helper->log('Excel DateStamp: ' . $worksheet->getCell('D' . $row)->getCalculatedValue());
    $helper->log('Formatted DateStamp: ' . $worksheet->getCell('E' . $row)->getFormattedValue());
    $helper->log('');
}
