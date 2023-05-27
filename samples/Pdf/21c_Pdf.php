<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

// Issue 2432 - styles were too large to fit in first Mpdf chunk, causing problems.
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$counter = 0;
$helper->log('Populate spreadsheet');
for ($row = 1; $row < 501; ++$row) {
    $sheet->getCell("A$row")->setValue(++$counter);
    // Add many styles by using slight variations of font color for each.
    $sheet->getCell("A$row")->getStyle()->getFont()->getColor()->setRgb(sprintf('%06x', $counter));
    $sheet->getCell("B$row")->setValue(++$counter);
    $sheet->getCell("C$row")->setValue(++$counter);
}

$helper->log('Write to Mpdf');
IOFactory::registerWriter('Pdf', \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class);
$helper->write($spreadsheet, __FILE__, ['Pdf']);
$spreadsheet->disconnectWorksheets();
