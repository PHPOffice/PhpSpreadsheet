<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotField;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotTableBuilder;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()->setTitle('PhpSpreadsheet PivotTable Page Filter Sample');

$helper->log('Add source data');
$dataSheet = $spreadsheet->getActiveSheet();
$dataSheet->setTitle('Data');
$dataSheet->fromArray(
    [
        ['Region', 'Product', 'Quarter', 'Amount'],
        ['East', 'Widget', 'Q1', 100],
        ['East', 'Gadget', 'Q1', 200],
        ['West', 'Widget', 'Q1', 150],
        ['West', 'Gadget', 'Q1', 250],
        ['East', 'Widget', 'Q2', 120],
        ['East', 'Gadget', 'Q2', 220],
        ['West', 'Widget', 'Q2', 170],
        ['West', 'Gadget', 'Q2', 270],
    ],
    null,
    'A1'
);

$pivotSheet = $spreadsheet->createSheet();
$pivotSheet->setTitle('PivotTable');

// The Quarter field is placed on the page axis, becoming a report filter that
// the user can switch between "(All)", "Q1" and "Q2" in the spreadsheet app.
$helper->log('Build pivot table with a page (report filter) field');
$builder = new PivotTableBuilder($dataSheet, 'A1:D9');
$builder
    ->addPageField('Quarter')
    ->addRowField('Region')
    ->addColumnField('Product')
    ->addDataField('Amount', PivotField::SUBTOTAL_SUM);

$pivotTable = $builder->build($pivotSheet, 'A4', 'SalesFilteredByQuarter');

$helper->log('Page fields: ' . implode(', ', array_map(
    static fn ($field) => $field->getName(),
    $pivotTable->getPageFields()
)));

$helper->write($spreadsheet, __FILE__, ['Xlsx']);
