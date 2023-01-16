<?php

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

require __DIR__ . '/../Header.php';

// Issue 3266 - spreadsheet specified fitToHeight.
$helper->log('Read spreadsheet');
$filename = '21d_FitToHeightPdf.xlsx';
$fileWithPath = __DIR__ . "/../templates/$filename";
$reader = new Xlsx();
$spreadsheet = $reader->load($fileWithPath);
$sheet = $spreadsheet->getActiveSheet();

$helper->log('Write to Mpdf');
$writer = new Mpdf($spreadsheet);
$filename = $helper->getfilename($filename, 'pdf');
$writer->save($filename);
$helper->log("Saved $filename");
$spreadsheet->disconnectWorksheets();
