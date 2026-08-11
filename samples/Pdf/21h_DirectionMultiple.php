<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;

require __DIR__ . '/../Header.php';

/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet1 = $spreadsheet->getActiveSheet();
$sheet2 = $spreadsheet->createSheet();
$sheet3 = $spreadsheet->createSheet();
$cells = [
    ['a1', 'b1', 'c1'],
    ['a2', 'b2', 'c2'],
];
$sheet1->fromArray($cells);
$sheet1->setRightToLeft(true);
$sheet2->fromArray($cells);
$sheet3->fromArray($cells);
$sheet3->setRightToLeft(true);

$helper->log('Write to html, mpdf');
// Save
$helper->write(
    $spreadsheet,
    __FILE__,
    ['Html', 'Mpdf'],
    writerCallback: function (HtmlWriter $writer): void {
            $writer->writeAllSheets();
    }
);

$spreadsheet->disconnectWorksheets();
