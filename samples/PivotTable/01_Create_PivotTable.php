<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotField;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotTableBuilder;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()
    ->setCreator('PhpSpreadsheet')
    ->setTitle('PhpSpreadsheet PivotTable Sample')
    ->setCategory('PivotTable');

// Fill a worksheet with the source data for the pivot table.
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

// Create a worksheet to hold the pivot table.
$pivotSheet = $spreadsheet->createSheet();
$pivotSheet->setTitle('PivotTable');

// Build a pivot table:
//   rows    = Region
//   columns = Product
//   values  = Sum of Amount
$helper->log('Build pivot table');
$builder = new PivotTableBuilder($dataSheet, 'A1:D9');
$builder
    ->addRowField('Region')
    ->addColumnField('Product')
    ->addDataField('Amount', PivotField::SUBTOTAL_SUM);

$pivotTable = $builder->build($pivotSheet, 'A3', 'SalesByRegion');

$helper->log('Pivot table "' . $pivotTable->getName() . '" created at ' . $pivotTable->getLocation());
$helper->log(
    'The pivot table is written with refresh-on-load set, so the spreadsheet '
    . 'application fills in the summarised values when the file is opened.'
);

// Pivot tables are an Xlsx feature.
$helper->write($spreadsheet, __FILE__, ['Xlsx']);
