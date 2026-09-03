<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotField;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotFieldGroup;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotTableBuilder;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()->setTitle('PhpSpreadsheet PivotTable Group Item Counts Sample');

$helper->log('Add source data');
$dataSheet = $spreadsheet->getActiveSheet();
$dataSheet->setTitle('Data');
$dataSheet->fromArray(
    [
        ['Customer', 'Score', 'OrderDate', 'Amount'],
        ['Alice', 0.15, '2024-01-15', 100],
        ['Bob', 0.35, '2024-06-20', 150],
        ['Carol', 0.55, '2025-02-10', 200],
        ['Dan', 0.75, '2025-11-05', 250],
        ['Erin', 0.95, '2024-09-01', 175],
    ],
    null,
    'A1'
);

// Older releases of Excel validate a pivot table strictly: every item that a
// pivot field can display must be enumerated in the pivotTableDefinition, and
// the number of those items has to agree with the group items recorded in the
// pivot cache. When the two disagree the workbook is reported as damaged and
// the pivot table is dropped, so both parts are derived from one enumeration.

// --- Fractional interval: Score bucketed 0..1 in steps of 0.1 ---
// A fractional interval is the interesting case. The bucket boundaries are
// computed by index (start + i * interval) rather than by repeatedly adding
// the interval, because repeated addition drifts: the tenth boundary would
// land just under 1.0 and produce a spurious zero-width "1-1" bucket.
$helper->log('Build pivot table grouping Score into fractional numeric ranges');
$scoreSheet = $spreadsheet->createSheet();
$scoreSheet->setTitle('ByScoreBand');

$scoreBuilder = new PivotTableBuilder($dataSheet, 'A1:D6');
$scoreBuilder
    ->groupFieldByNumericRange('Score', 0.1, 0.0, 1.0)
    ->addRowField('Score')
    ->addDataField('Amount', PivotField::SUBTOTAL_SUM);
$scoreBuilder->build($scoreSheet, 'A3', 'SalesByScoreBand');

$helper->log('Score yields 10 buckets (0-0.1 ... 0.9-1) plus the "<0" and ">1" bounds');

// --- Date grouping: OrderDate by quarter ---
// Date groups use the fixed labels Excel expects for the chosen unit, so the
// item count is the length of that label set.
$helper->log('Build pivot table grouping OrderDate by quarter');
$quarterSheet = $spreadsheet->createSheet();
$quarterSheet->setTitle('ByQuarter');

$quarterBuilder = new PivotTableBuilder($dataSheet, 'A1:D6');
$quarterBuilder
    ->groupFieldByDate('OrderDate', PivotFieldGroup::GROUP_BY_QUARTERS)
    ->addRowField('OrderDate')
    ->addDataField('Amount', PivotField::SUBTOTAL_SUM);
$quarterBuilder->build($quarterSheet, 'A3', 'SalesByQuarter');

$helper->log('OrderDate yields 6 items (<1/1/1900, Qtr1..Qtr4, >12/31/9999)');

// --- Ungrouped field: item count comes from the distinct source values ---
$helper->log('Build pivot table on an ungrouped field');
$customerSheet = $spreadsheet->createSheet();
$customerSheet->setTitle('ByCustomer');

$customerBuilder = new PivotTableBuilder($dataSheet, 'A1:D6');
$customerBuilder
    ->addRowField('Customer')
    ->addDataField('Amount', PivotField::SUBTOTAL_SUM);
$customerBuilder->build($customerSheet, 'A3', 'SalesByCustomer');

$helper->log('Customer yields one item per distinct value in the source column');

$helper->write($spreadsheet, __FILE__, ['Xlsx']);
