<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';
$helper->log('Counts the cells that contain numbers in a database.');

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

$worksheet->setCellValue('A12', 'The Number of Apple trees over 10\' in height');
$worksheet->setCellValue('B12', '=DCOUNT(A4:E10,"Yield",A1:B2)');

$worksheet->setCellValue('A13', 'The Number of Apple and Pear trees in the orchard');
$worksheet->setCellValue('B13', '=DCOUNT(A4:E10,3,A1:A3)');

$helper->log('Database');

$databaseData = $worksheet->rangeToArray('A4:E10', null, true, true, true);
var_dump($databaseData);

// Test the formulae
$helper->log('Criteria');

$criteriaData = $worksheet->rangeToArray('A1:B2', null, true, true, true);
var_dump($criteriaData);

$helper->log($worksheet->getCell('A12')->getValue());
$helper->log('DCOUNT() Result is ' . $worksheet->getCell('B12')->getCalculatedValue());

$helper->log('Criteria');

$criteriaData = $worksheet->rangeToArray('A1:A3', null, true, true, true);
var_dump($criteriaData);

$helper->log($worksheet->getCell('A13')->getValue());
$helper->log('DCOUNT() Result is ' . $worksheet->getCell('B13')->getCalculatedValue());
