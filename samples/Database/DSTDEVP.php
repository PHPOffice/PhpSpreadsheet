<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$category = 'Database';
$functionName = 'DSTDEVP';
$description = 'Calculates the standard deviation based on the entire population of selected database entries';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$database = [['Tree', 'Height', 'Age', 'Yield', 'Profit'],
    ['Apple', 18, 20, 14, 105.00],
    ['Pear', 12, 12, 10, 96.00],
    ['Cherry', 13, 14, 9, 105.00],
    ['Apple', 14, 15, 10, 75.00],
    ['Pear', 9, 8, 8, 76.80],
    ['Apple', 8, 9, 6, 45.00],
];
$criteria = [['Tree', 'Height', 'Age', 'Yield', 'Profit', 'Height'],
    ['="=Apple"', '>10', null, null, null, '<16'],
    ['="=Pear"', null, null, null, null, null],
];

$worksheet->fromArray($criteria, null, 'A1');
$worksheet->fromArray($database, null, 'A4');

$worksheet->setCellValue('A12', 'The standard deviation in the yield of Apple and Pear trees');
$worksheet->setCellValue('B12', '=DSTDEVP(A4:E10,"Yield",A1:A3)');

$worksheet->setCellValue('A13', 'The standard deviation in height of Apple and Pear trees');
$worksheet->setCellValue('B13', '=DSTDEVP(A4:E10,2,A1:A3)');

$helper->log('Database');

$databaseData = $worksheet->rangeToArray('A4:E10', null, true, true, true);
$helper->displayGrid($databaseData);

// Test the formulae
$helper->log('Criteria');

$criteriaData = $worksheet->rangeToArray('A1:A3', null, true, true, true);
$helper->displayGrid($criteriaData);

$helper->logCalculationResult($worksheet, $functionName, 'B12', 'A12');

$helper->log('Criteria');

$criteriaData = $worksheet->rangeToArray('A1:A3', null, true, true, true);
$helper->displayGrid($criteriaData);

$helper->logCalculationResult($worksheet, $functionName, 'B13', 'A13');
