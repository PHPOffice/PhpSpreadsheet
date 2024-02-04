<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$category = 'Date/Time';
$functionName = 'TODAY';
$description = 'Returns the serial number of the current date';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

$worksheet->setCellValue('A1', '=TODAY()');
$worksheet->getStyle('A1')
    ->getNumberFormat()
    ->setFormatCode('yyyy-mm-dd');

// Test the formulae
$helper->log(
    'Today is '
    . $worksheet->getCell('A1')->getCalculatedValue()
    . ' ('
    . $worksheet->getCell('A1')->getFormattedValue()
    . ')'
);
