<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotField;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotFieldGroup;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotTableBuilder;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */

$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()->setTitle('PhpSpreadsheet PivotTable Grouping Sample');

$helper->log('Add source data');
$dataSheet = $spreadsheet->getActiveSheet();
$dataSheet->setTitle('Data');
$dataSheet->fromArray(
    [
        ['Customer', 'Age', 'OrderDate', 'Amount'],
        ['Alice', 23, '2024-01-15', 100],
        ['Bob', 37, '2024-06-20', 150],
        ['Carol', 45, '2025-02-10', 200],
        ['Dan', 51, '2025-11-05', 250],
        ['Erin', 29, '2024-09-01', 175],
        ['Frank', 62, '2025-07-22', 300],
    ],
    null,
    'A1'
);

// --- Numeric range grouping: bucket Age into bands of 10 (20-30, 30-40, ...) ---
$helper->log('Build pivot table grouping Age into numeric ranges');
$agePivotSheet = $spreadsheet->createSheet();
$agePivotSheet->setTitle('ByAgeRange');

$ageBuilder = new PivotTableBuilder($dataSheet, 'A1:D7');
$ageBuilder
    ->groupFieldByNumericRange('Age', 10, 20, 70)
    ->addRowField('Age')
    ->addDataField('Amount', PivotField::SUBTOTAL_SUM);
$ageBuilder->build($agePivotSheet, 'A3', 'SalesByAgeRange');

// --- Date grouping: group OrderDate by quarter ---
$helper->log('Build pivot table grouping OrderDate by quarter');
$datePivotSheet = $spreadsheet->createSheet();
$datePivotSheet->setTitle('ByQuarter');

$dateBuilder = new PivotTableBuilder($dataSheet, 'A1:D7');
$dateBuilder
    ->groupFieldByDate('OrderDate', PivotFieldGroup::GROUP_BY_QUARTERS)
    ->addRowField('OrderDate')
    ->addDataField('Amount', PivotField::SUBTOTAL_SUM);
$dateBuilder->build($datePivotSheet, 'A3', 'SalesByQuarter');

$helper->log(
    'Grouping is written into the pivot cache; the spreadsheet application '
    . 'materialises the buckets when it refreshes the pivot on open.'
);

$helper->write($spreadsheet, __FILE__, ['Xlsx']);
