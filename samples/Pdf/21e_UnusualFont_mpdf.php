<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf2;

require __DIR__ . '/../Header.php';
require_once __DIR__ . '/Mpdf2.php';

$spreadsheet = new Spreadsheet();

$helper->log('Show print grid lines');
$sheet = $spreadsheet->getActiveSheet();
$sheet->setPrintGridLines(true);

$sheet->getCell('A1')->setValue('First Cell');
$sheet->getCell('A2')->setValue('Second Cell');
$sheet->getCell('B1')->setValue('Third Cell');
$sheet->getCell('B2')->setValue('Fourth Cell');

$helper->log('Set style to unusual font');
$sheet->getStyle('A1:B2')->getFont()->setName('Shadows Into Light');

$helper->log('Write to Mpdf');
$writer = new Mpdf2($spreadsheet);
$filename = $helper->getFileName(__FILE__, 'pdf');
$writer->save($filename);
$helper->log("Saved $filename");
if (PHP_SAPI !== 'cli') {
    echo '<a href="/download.php?type=pdf&name=' . basename($filename) . '">Download ' . basename($filename) . '</a><br />';
}
$spreadsheet->disconnectWorksheets();
