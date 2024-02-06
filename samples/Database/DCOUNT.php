<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$category = 'Database';
$functionName = 'DCOUNT';
$description = 'Counts the cells that contain numbers in a set of database records that match criteria';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$database = [['Tree', 'Height', 'Age', 'Yield', 'Profit'],
    ['Apple', 18, 20, 14, 105.00],
    ['Pear', 12, 12, 10, 96.00],
    ['Cherry', 13, 14, 9, 105.00],
    ['Apple', 14, 'N/A', 10, 75.00],
    ['Pear', 9, 8, 8, 77.00],
    ['Apple', 12, 11, 6, 45.00],
];
$criteria = [['Tree', 'Height', 'Age', 'Yield', 'Profit', 'Height'],
    ['="=Apple"', '>10', null, null, null, '<16'],
    ['="=Pear"', null, null, null, null, null],
];

$worksheet->fromArray($criteria, null, 'A1');
$worksheet->fromArray($database, null, 'A4');

$worksheet->setCellValue('A12', 'The Number of Apple trees between 10\' and 16\' in height whose age is known');
$worksheet->setCellValue('B12', '=DCOUNT(A4:E10,"Age",A1:F2)');

$worksheet->setCellValue('A13', 'The Number of Apple and Pear trees in the orchard with a numeric value in column 3 ("Age")');
$worksheet->setCellValue('B13', '=DCOUNT(A4:E10,3,A1:A3)');

$helper->log('Database');

$databaseData = $worksheet->rangeToArray('A4:E10', null, true, true, true);
$helper->displayGrid($databaseData);

// Test the formulae
$helper->log('Criteria');

$criteriaData = $worksheet->rangeToArray('A1:F2', null, true, true, true);
$helper->displayGrid($criteriaData);

$helper->logCalculationResult($worksheet, $functionName, 'B12', 'A12');

$helper->log('Criteria');

$criteriaData = $worksheet->rangeToArray('A1:A3', null, true, true, true);
$helper->displayGrid($criteriaData);

$helper->logCalculationResult($worksheet, $functionName, 'B13', 'A13');
