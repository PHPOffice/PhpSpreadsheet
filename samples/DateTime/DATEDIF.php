<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$category = 'Date/Time';
$functionName = 'DATEDIF';
$description = 'Calculates the number of days, months, or years between two dates';

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
];
$testDateCount = count($testDates);

$worksheet->fromArray($testDates, null, 'A1', true);

for ($row = 1; $row <= $testDateCount; ++$row) {
    $worksheet->setCellValue('D' . $row, '=DATE(A' . $row . ',B' . $row . ',C' . $row . ')');
    $worksheet->setCellValue('E' . $row, '=D' . $row);
    $worksheet->setCellValue('F' . $row, '=TODAY()');
    $worksheet->setCellValue('G' . $row, '=DATEDIF(D' . $row . ', F' . $row . ', "Y")');
    $worksheet->setCellValue('H' . $row, '=DATEDIF(D' . $row . ', F' . $row . ', "M")');
    $worksheet->setCellValue('I' . $row, '=DATEDIF(D' . $row . ', F' . $row . ', "D")');
    $worksheet->setCellValue('J' . $row, '=DATEDIF(D' . $row . ', F' . $row . ', "MD")');
    $worksheet->setCellValue('K' . $row, '=DATEDIF(D' . $row . ', F' . $row . ', "YM")');
    $worksheet->setCellValue('L' . $row, '=DATEDIF(D' . $row . ', F' . $row . ', "YD")');
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
    $helper->log('In years ("Y"): ' . $worksheet->getCell('G' . $row)->getCalculatedValue());
    $helper->log('In months ("M"): ' . $worksheet->getCell('H' . $row)->getCalculatedValue());
    $helper->log('In days ("D"): ' . $worksheet->getCell('I' . $row)->getCalculatedValue());
    $helper->log('In days ignoring months and years ("MD"): ' . $worksheet->getCell('J' . $row)->getCalculatedValue());
    $helper->log('In months ignoring days and years ("YM"): ' . $worksheet->getCell('K' . $row)->getCalculatedValue());
    $helper->log('In days ignoring years ("YD"): ' . $worksheet->getCell('L' . $row)->getCalculatedValue());
}
