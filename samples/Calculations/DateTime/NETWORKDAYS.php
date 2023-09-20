<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Date/Time';
$functionName = 'NETWORKDAYS';
$description = 'Returns the number of whole working days between start_date and end_date. Working days exclude weekends and any dates identified in holidays';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$publicHolidays = [
    [2022, 1, 3, '=DATE(G1, H1, I1)', 'New Year'],
    [2022, 4, 15, '=DATE(G2, H2, I2)', 'Good Friday'],
    [2022, 4, 18, '=DATE(G3, H3, I3)', 'Easter Monday'],
    [2022, 5, 2, '=DATE(G4, H4, I4)', 'Early May Bank Holiday'],
    [2022, 6, 2, '=DATE(G5, H5, I5)', 'Spring Bank Holiday'],
    [2022, 6, 3, '=DATE(G6, H6, I6)', 'Platinum Jubilee Bank Holiday'],
    [2022, 8, 29, '=DATE(G7, H7, I7)', 'Summer Bank Holiday'],
    [2022, 12, 26, '=DATE(G8, H8, I8)', 'Boxing Day'],
    [2022, 12, 27, '=DATE(G9, H9, I9)', 'Christmas Day'],
];

$holidayCount = count($publicHolidays);
$worksheet->fromArray($publicHolidays, null, 'G1', true);

$worksheet->getStyle('J1:J' . $holidayCount)
    ->getNumberFormat()
    ->setFormatCode('yyyy-mm-dd');

$worksheet->setCellValue('A1', '=DATE(2022,1,1)');

for ($numberOfMonths = 0; $numberOfMonths < 12; ++$numberOfMonths) {
    $worksheet->setCellValue('B' . ($numberOfMonths + 1), '=EOMONTH(A1, ' . $numberOfMonths . ')');
    $worksheet->setCellValue('C' . ($numberOfMonths + 1), '=NETWORKDAYS(A1, B' . ($numberOfMonths + 1) . ')');
    $worksheet->setCellValue('D' . ($numberOfMonths + 1), '=NETWORKDAYS(A1, B' . ($numberOfMonths + 1) . ', J1:J' . $holidayCount . ')');
}

$worksheet->getStyle('A1')
    ->getNumberFormat()
    ->setFormatCode('yyyy-mm-dd');

$worksheet->getStyle('B1:B12')
    ->getNumberFormat()
    ->setFormatCode('yyyy-mm-dd');

// Test the formulae
$helper->log('UK Public Holidays');
$holidayData = $worksheet->rangeToArray('J1:K' . $holidayCount, null, true, true, true);
$helper->displayGrid($holidayData);

for ($row = 1; $row <= 12; ++$row) {
    $helper->log(sprintf(
        'Between %s and %s is %d working days; %d with public holidays',
        $worksheet->getCell('A1')->getFormattedValue(),
        $worksheet->getCell('B' . $row)->getFormattedValue(),
        $worksheet->getCell('C' . $row)->getCalculatedValue(),
        $worksheet->getCell('D' . $row)->getCalculatedValue()
    ));
}
