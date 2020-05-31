<?php

use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf;

require __DIR__ . '/../Header.php';
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';

$helper->log('Hide grid lines');
$spreadsheet->getActiveSheet()->setShowGridLines(false);

$helper->log('Set orientation to landscape');
$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

function yellowBody(string $html): string
{
    $newstyle = <<<EOF
<style type='text/css'>
body {
background-color: yellow;
}
</style>

EOF;

    return preg_replace('~</head>~', "$newstyle</head>", $html);
}

$helper->log('Write to Dompdf');
$writer = new Dompdf($spreadsheet);
$filename = $helper->getFileName('21a_Pdf_dompdf.xlsx', 'pdf');
$writer->setEditHtmlCallback('yellowBody');
$writer->save($filename);

$helper->log('Write to Mpdf');
$writer = new Mpdf($spreadsheet);
$filename = $helper->getFileName('21a_Pdf_mpdf.xlsx', 'pdf');
$writer->setEditHtmlCallback('yellowBody');
$writer->save($filename);

$helper->log('Write to Tcpdf');
$writer = new Tcpdf($spreadsheet);
$filename = $helper->getFileName('21a_Pdf_tcpdf.xlsx', 'pdf');
$writer->setEditHtmlCallback('yellowBody');
$writer->save($filename);
