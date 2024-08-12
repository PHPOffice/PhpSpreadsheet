<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$category = 'Date/Time';
$functionName = 'WORKDAY';
$description = 'Returns a number that represents a date that is the indicated number of working days before or after a starting date. Working days exclude weekends and any dates identified as holidays';

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

$workdayStep = 10;
for ($days = $workdayStep; $days <= 366; $days += $workdayStep) {
    $worksheet->setCellValue('B' . ((int) $days / $workdayStep + 1), $days);
    $worksheet->setCellValue('C' . ((int) $days / $workdayStep + 1), '=WORKDAY(A1, B' . ((int) $days / $workdayStep + 1) . ')');
    $worksheet->setCellValue('D' . ((int) $days / $workdayStep + 1), '=WORKDAY(A1, B' . ((int) $days / $workdayStep + 1) . ', J1:J' . $holidayCount . ')');
}

$worksheet->getStyle('A1')
    ->getNumberFormat()
    ->setFormatCode('yyyy-mm-dd');

$worksheet->getStyle('C1:D50')
    ->getNumberFormat()
    ->setFormatCode('yyyy-mm-dd');

// Test the formulae
$helper->log('UK Public Holidays');
$holidayData = $worksheet->rangeToArray('J1:K' . $holidayCount, null, true, true, true);
$helper->displayGrid($holidayData);

for ($days = $workdayStep; $days <= 366; $days += $workdayStep) {
    $helper->log(sprintf(
        '%d workdays from %s is %s; %s with public holidays',
        $worksheet->getCell('B' . ((int) $days / $workdayStep + 1))->getFormattedValue(),
        $worksheet->getCell('A1')->getFormattedValue(),
        $worksheet->getCell('C' . ((int) $days / $workdayStep + 1))->getFormattedValue(),
        $worksheet->getCell('D' . ((int) $days / $workdayStep + 1))->getFormattedValue()
    ));
}
