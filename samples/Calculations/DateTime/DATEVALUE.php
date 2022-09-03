<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Date/Time';
$functionName = 'DATEVALUE';
$description = 'Converts a date in the form of text to an Excel serial number';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testDates = ['26 March 2012', '29 Feb 2012', 'April 1, 2012', '25/12/2012',
    '2012-Oct-31', '5th November', 'January 1st', 'April 2012',
    '17-03', '03-17', '03-2012', '29 Feb 2011', '03-05-07',
    '03-MAY-07', '03-13-07', '13-03-07', '03/13/07', '13/03/07',
];
$testDateCount = count($testDates);

for ($row = 1; $row <= $testDateCount; ++$row) {
    $worksheet->setCellValue('A' . $row, $testDates[$row - 1]);
    $worksheet->setCellValue('B' . $row, '=DATEVALUE(A' . $row . ')');
    $worksheet->setCellValue('C' . $row, '=B' . $row);
}

$worksheet->getStyle('C1:C' . $testDateCount)
    ->getNumberFormat()
    ->setFormatCode('yyyy-mm-dd');

// Test the formulae
$helper->log('<strong>Warning: </strong>The PhpSpreadsheet DATEVALUE() function accepts a wider range of date formats than MS Excel DATEFORMAT() function.');
for ($row = 1; $row <= $testDateCount; ++$row) {
    $helper->log("(A{$row}) Date String: " . $worksheet->getCell('A' . $row)->getFormattedValue());
    $helper->log('Formula: ' . $worksheet->getCell('B' . $row)->getValue());
    $helper->log('Excel DateStamp: ' . $worksheet->getCell('B' . $row)->getCalculatedValue());
    $helper->log('Formatted DateStamp: ' . $worksheet->getCell('C' . $row)->getFormattedValue());
    $helper->log('');
}
