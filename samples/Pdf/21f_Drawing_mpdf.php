<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
require_once __DIR__ . '/Mpdf2.php';

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->getCell('A1')->setValue('A1');
$sheet->getCell('B1')->setValue('B');
$sheet->getCell('C1')->setValue('C');
$sheet->getCell('D1')->setValue('D');
$sheet->getCell('E1')->setValue('E');
$sheet->getCell('F1')->setValue('F');
$sheet->getCell('G1')->setValue('G');
$sheet->getCell('A2')->setValue('A2');
$sheet->getCell('A3')->setValue('A3');
$sheet->getCell('A4')->setValue('A4');
$sheet->getCell('A5')->setValue('A5');
$sheet->getCell('A6')->setValue('A6');
$sheet->getCell('A7')->setValue('A7');
$sheet->getCell('A8')->setValue('A8');

$helper->log('Add drawing to worksheet');
$drawing = new Drawing();
$drawing->setName('Blue Square');
$path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'images/blue_square.png';
$drawing->setPath($path);
$drawing->setResizeProportional(false);
$drawing->setWidth(320);
$drawing->setCoordinates('B2');
$drawing->setCoordinates2('G6');
$drawing->setWorksheet($sheet, true);

$helper->log('Merge drawing cells for Pdf');
$spreadsheet->mergeDrawingCellsForPdf();

$helper->log('Write to Mpdf');
$writer = new Mpdf($spreadsheet);
$filename = $helper->getFileName(__FILE__, 'pdf');
$writer->save($filename);
$helper->log("Saved $filename");
if (PHP_SAPI !== 'cli') {
    echo '<a href="/download.php?type=pdf&name=' . basename($filename) . '">Download ' . basename($filename) . '</a><br />';
}
$spreadsheet->disconnectWorksheets();
