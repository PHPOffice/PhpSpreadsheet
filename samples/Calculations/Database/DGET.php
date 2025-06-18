<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$helper->log('Extracts a single value from a column of a list or database that matches conditions that you specify.');

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

$worksheet->setCellValue('A12', 'The height of the Apple tree between 10\' and 16\' tall');
$worksheet->setCellValue('B12', '=DGET(A4:E10,"Height",A1:F2)');

$helper->log('Database');

$databaseData = $worksheet->rangeToArray('A4:E10', null, true, true, true);
var_dump($databaseData);

// Test the formulae
$helper->log('Criteria');

$helper->log('ALL');

$helper->log($worksheet->getCell('A12')->getValue());
$helper->log('DMAX() Result is ' . $worksheet->getCell('B12')->getCalculatedValue());

$helper->log('Criteria');

$criteriaData = $worksheet->rangeToArray('A1:A2', null, true, true, true);
var_dump($criteriaData);

$helper->log($worksheet->getCell('A13')->getValue());
$helper->log('DMAX() Result is ' . $worksheet->getCell('B13')->getCalculatedValue());
