<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Database';
$functionName = 'DAVERAGE';
$description = 'Returns the average of selected database entries that match criteria';

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

$worksheet->setCellValue('A12', 'The Average yield of Apple trees over 10\' in height');
$worksheet->setCellValue('B12', '=DAVERAGE(A4:E10,"Yield",A1:B2)');

$worksheet->setCellValue('A13', 'The Average age of all Apple and Pear trees in the orchard');
$worksheet->setCellValue('B13', '=DAVERAGE(A4:E10,3,A1:A3)');

$helper->log('Database');

$databaseData = $worksheet->rangeToArray('A4:E10', null, true, true, true);
$helper->displayGrid($databaseData);

// Test the formulae
$helper->log('Criteria');

$criteriaData = $worksheet->rangeToArray('A1:B2', null, true, true, true);
$helper->displayGrid($criteriaData);

$helper->logCalculationResult($worksheet, $functionName, 'B12', 'A12');

$helper->log('Criteria');

$criteriaData = $worksheet->rangeToArray('A1:A3', null, true, true, true);
$helper->displayGrid($criteriaData);

$helper->logCalculationResult($worksheet, $functionName, 'B13', 'A13');
