<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';
$helper->log('Estimates variance based on a sample from selected database entries.');

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

$worksheet->setCellValue('A12', 'The estimated variance in the yield of Apple and Pear trees');
$worksheet->setCellValue('B12', '=DVAR(A4:E10,"Yield",A1:A3)');

$worksheet->setCellValue('A13', 'The estimated variance in height of Apple and Pear trees');
$worksheet->setCellValue('B13', '=DVAR(A4:E10,2,A1:A3)');

$helper->log('Database');

$databaseData = $worksheet->rangeToArray('A4:E10', null, true, true, true);
var_dump($databaseData);

// Test the formulae
$helper->log('Criteria');

$criteriaData = $worksheet->rangeToArray('A1:A3', null, true, true, true);
var_dump($criteriaData);

$helper->log($worksheet->getCell('A12')->getValue());
$helper->log('DVAR() Result is ' . $worksheet->getCell('B12')->getCalculatedValue());

$helper->log('Criteria');

$criteriaData = $worksheet->rangeToArray('A1:A3', null, true, true, true);
var_dump($criteriaData);

$helper->log($worksheet->getCell('A13')->getValue());
$helper->log('DVAR() Result is ' . $worksheet->getCell('B13')->getCalculatedValue());
