<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$category = 'Date/Time';
$functionName = 'TIME';
$description = 'Returns the Excel serial number of a particular time';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testDates = [[3, 15], [13, 15], [15, 15, 15], [3, 15, 30],
    [15, 15, 15], [5], [9, 15, 0], [9, 15, -1],
    [13, -14, -15], [0, 0, -1],
];
$testDateCount = count($testDates);

$worksheet->fromArray($testDates, null, 'A1', true);

for ($row = 1; $row <= $testDateCount; ++$row) {
    $worksheet->setCellValue('D' . $row, '=TIME(A' . $row . ',B' . $row . ',C' . $row . ')');
    $worksheet->setCellValue('E' . $row, '=D' . $row);
}
$worksheet->getStyle('E1:E' . $testDateCount)
    ->getNumberFormat()
    ->setFormatCode('hh:mm:ss');

// Test the formulae
for ($row = 1; $row <= $testDateCount; ++$row) {
    $helper->log("(A{$row}) Hour: " . $worksheet->getCell('A' . $row)->getFormattedValue());
    $helper->log("(B{$row}) Minute: " . $worksheet->getCell('B' . $row)->getFormattedValue());
    $helper->log("(C{$row}) Second: " . $worksheet->getCell('C' . $row)->getFormattedValue());
    $helper->log('Formula: ' . $worksheet->getCell('D' . $row)->getValue());
    $helper->log('Excel TimeStamp: ' . $worksheet->getCell('D' . $row)->getCalculatedValue());
    $helper->log('Formatted TimeStamp: ' . $worksheet->getCell('E' . $row)->getFormattedValue());
    $helper->log('');
}
