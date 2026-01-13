<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$helper->log('Emulate table border, cellspacing, cellpadding attributes');

function addCellspacing(string $html): string
{
    return str_replace(
        'table { border-collapse:collapse }',
        'table { border-collapse:separate; border-spacing: 5px; }'
            . "\ntable.sheet0 {border: 1px solid red}"
            . "\ntd, th {padding: 3px}",
        $html
    );
}

function writerCallback(HtmlWriter $writer): void
{
    $writer->setEditHtmlCallback(addCellSpacing(...));
    $writer->setLineEnding("\n")
        ->writeAllSheets()
        ->setEditHtmlCallback(addCellspacing(...));
}

$spreadsheet = new Spreadsheet();
$helper->log('Sheet1 will have no borders inside, red border outside from callback');
$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle('Sheet1');
$sheet1->setShowGridlines(false);
$sheet1->setPrintGridlines(false);
$sheet1->fromArray([
    [11, 12, 13],
    [14, 15, 16],
]);
$helper->log('Sheet2 will have no borders (display), separate borders (print) from callback');
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('Sheet2');
$sheet2->setShowGridlines(false);
$sheet2->setPrintGridlines(true);
$sheet2->fromArray([
    [21, 22, 23],
    [24, 25, 26],
]);
$helper->log('Sheet3 will have no borders (print), separate borders (display) from callback');
$sheet3 = $spreadsheet->createSheet();
$sheet3->setTitle('Sheet3');
$sheet3->setShowGridlines(true);
$sheet3->setPrintGridlines(false);
$sheet3->fromArray([
    [31, 32, 33],
    [34, 35, 36],
]);
$helper->log('Sheet4 will have separate borders (print and display) from callback');
$sheet4 = $spreadsheet->createSheet();
$sheet4->setTitle('Sheet4');
$sheet4->setShowGridlines(true);
$sheet4->setPrintGridlines(true);
$sheet4->fromArray([
    [41, 42, 43],
    [44, 45, 46],
]);

$helper->log('Auto-size column dimensions');
foreach ([$sheet1, $sheet2, $sheet3, $sheet4] as $sheet) {
    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
}

// Save
$helper->write($spreadsheet, __FILE__, ['Html'], false, writerCallback: writerCallback(...));
