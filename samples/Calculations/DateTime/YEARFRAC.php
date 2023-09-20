<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Date/Time';
$functionName = 'DAYS360';
$description = 'Returns the number of days between two dates based on a 360-day year';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testDates = [
    [1900, 1, 1],
    [1904, 1, 1],
    [1936, 3, 17],
    [1960, 12, 19],
    [1999, 12, 31],
    [2000, 1, 1],
    [2019, 2, 14],
    [2020, 7, 4],
    [2020, 2, 29],
    [2029, 12, 31],
    [2525, 1, 1],
];
$testDateCount = count($testDates);

$worksheet->fromArray($testDates, null, 'A1', true);

for ($row = 1; $row <= $testDateCount; ++$row) {
    $worksheet->setCellValue('D' . $row, '=DATE(A' . $row . ',B' . $row . ',C' . $row . ')');
    $worksheet->setCellValue('E' . $row, '=D' . $row);
    $worksheet->setCellValue('F' . $row, '=DATE(2022,12,31)');
    $worksheet->setCellValue('G' . $row, '=YEARFRAC(D' . $row . ', F' . $row . ')');
    $worksheet->setCellValue('H' . $row, '=YEARFRAC(D' . $row . ', F' . $row . ', 1)');
    $worksheet->setCellValue('I' . $row, '=YEARFRAC(D' . $row . ', F' . $row . ', 2)');
    $worksheet->setCellValue('J' . $row, '=YEARFRAC(D' . $row . ', F' . $row . ', 3)');
    $worksheet->setCellValue('K' . $row, '=YEARFRAC(D' . $row . ', F' . $row . ', 4)');
}
$worksheet->getStyle('E1:F' . $testDateCount)
    ->getNumberFormat()
    ->setFormatCode('yyyy-mm-dd');

// Test the formulae
for ($row = 1; $row <= $testDateCount; ++$row) {
    $helper->log(sprintf(
        'Between: %s and %s',
        $worksheet->getCell('E' . $row)->getFormattedValue(),
        $worksheet->getCell('F' . $row)->getFormattedValue()
    ));
    $helper->log(sprintf(
        'Days: %f - US (NASD) 30/360',
        $worksheet->getCell('G' . $row)->getCalculatedValue()
    ));
    $helper->log(sprintf(
        'Days: %f - Actual',
        $worksheet->getCell('H' . $row)->getCalculatedValue()
    ));
    $helper->log(sprintf(
        'Days: %f - Actual/360',
        $worksheet->getCell('I' . $row)->getCalculatedValue()
    ));
    $helper->log(sprintf(
        'Days: %f - Actual/365',
        $worksheet->getCell('J' . $row)->getCalculatedValue()
    ));
    $helper->log(sprintf(
        'Days: %f - European 30/360',
        $worksheet->getCell('K' . $row)->getCalculatedValue()
    ));
}
