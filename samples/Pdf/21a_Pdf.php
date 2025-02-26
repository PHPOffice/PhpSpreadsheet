<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

require __DIR__ . '/../Header.php';
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';

$helper->log('Hide grid lines');
$spreadsheet->getActiveSheet()->setShowGridLines(false);

$helper->log('Set orientation to landscape');
$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
$spreadsheet->setActiveSheetIndex(0)->setPrintGridlines(true);
// Issue 2299 - mpdf can't handle hide rows without kludge
$spreadsheet->getActiveSheet()->getRowDimension(2)->setVisible(false);

function changeGridlines(string $html): string
{
    return str_replace('{border: 1px solid black;}', '{border: 2px dashed red;}', $html);
}

$helper->log('Write to Mpdf');
IOFactory::registerWriter('Pdf', Mpdf::class);
$helper->write(
    $spreadsheet,
    __FILE__,
    ['Pdf'],
    false,
    function (Mpdf $writer): void {
        $writer->setEmbedImages(true);
        $writer->setEditHtmlCallback('changeGridlines');
    }
);
