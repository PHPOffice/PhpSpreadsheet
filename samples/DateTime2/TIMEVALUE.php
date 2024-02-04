<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Date/Time';
$functionName = 'DATEVALUE';
$description = 'Converts a time in the form of text to an Excel serial number';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testDates = ['3:15', '13:15', '15:15:15', '3:15 AM', '3:15 PM', '5PM', '9:15AM', '13:15AM',
];
$testDateCount = count($testDates);

for ($row = 1; $row <= $testDateCount; ++$row) {
    $worksheet->setCellValue('A' . $row, $testDates[$row - 1]);
    $worksheet->setCellValue('B' . $row, '=TIMEVALUE(A' . $row . ')');
    $worksheet->setCellValue('C' . $row, '=B' . $row);
}

$worksheet->getStyle('C1:C' . $testDateCount)
    ->getNumberFormat()
    ->setFormatCode('hh:mm:ss');

// Test the formulae
for ($row = 1; $row <= $testDateCount; ++$row) {
    $helper->log("(A{$row}) Time String: " . $worksheet->getCell('A' . $row)->getFormattedValue());
    $helper->log('Formula: ' . $worksheet->getCell('B' . $row)->getValue());
    $helper->log('Excel TimeStamp: ' . $worksheet->getCell('B' . $row)->getFormattedValue());
    $helper->log('Formatted TimeStamp: ' . $worksheet->getCell('C' . $row)->getFormattedValue());
    $helper->log('');
}
