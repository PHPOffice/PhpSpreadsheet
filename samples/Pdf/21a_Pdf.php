<?php

use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

require __DIR__ . '/../Header.php';
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';

$helper->log('Hide grid lines');
$spreadsheet->getActiveSheet()->setShowGridLines(false);

$helper->log('Set orientation to landscape');
$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
$spreadsheet->setActiveSheetIndex(0)->setPrintGridlines(true);

function changeGridlines(string $html): string
{
    return str_replace('{border: 1px solid black;}', '{border: 2px dashed red;}', $html);
}

$helper->log('Write to Mpdf');
$writer = new Mpdf($spreadsheet);
$filename = $helper->getFileName('21a_Pdf_mpdf.xlsx', 'pdf');
$writer->setEditHtmlCallback('changeGridlines');
$writer->save($filename);
