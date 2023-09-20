<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Date/Time';
$functionName = 'NOW';
$description = 'Returns the serial number of the current date and time';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

$worksheet->setCellValue('A1', '=NOW()');
$worksheet->getStyle('A1')
    ->getNumberFormat()
    ->setFormatCode('yyyy-mm-dd hh:mm:ss');

// Test the formulae
$helper->log(sprintf(
    'Today is %f (%s)',
    $worksheet->getCell('A1')->getCalculatedValue(),
    $worksheet->getCell('A1')->getFormattedValue()
));
