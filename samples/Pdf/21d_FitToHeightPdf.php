<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

require __DIR__ . '/../Header.php';

// Issue 3266 - spreadsheet specified fitToHeight.
$helper->log('Read spreadsheet');
$filename = '21d_FitToHeightPdf.xlsx';
$fileWithPath = __DIR__ . "/../templates/$filename";
$reader = new Xlsx();
$spreadsheet = $reader->load($fileWithPath);
$sheet = $spreadsheet->getActiveSheet();

$helper->log('Write to Mpdf');
IOFactory::registerWriter('Pdf', PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class);
$helper->write($spreadsheet, __FILE__, ['Pdf']);
$spreadsheet->disconnectWorksheets();
