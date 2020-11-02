<?php

use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf;

function replaceBody(string $html): string
{
    $lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
    $bodystring = '~<body>.*</body>~ms';
    $bodyrepl = <<<EOF
<body>
<h1>Serif</h1>
<p style='font-family: serif; font-size: 12pt;'>$lorem</p>
<h1>Sans-Serif</h1>
<p style='font-family: sans-serif; font-size: 12pt;'>$lorem</p>
<h1>Monospace</h1>
<p style='font-family: monospace; font-size: 12pt;'>$lorem</p>
</body>
EOF;

    return preg_replace($bodystring, $bodyrepl, $html);
}

require __DIR__ . '/../Header.php';
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';

$helper->log('Hide grid lines');
$spreadsheet->getActiveSheet()->setShowGridLines(false);

$helper->log('Set orientation to landscape');
$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

if (\PHP_VERSION_ID < 80000) {
    $helper->log('Write to Dompdf');
    $writer = new Dompdf($spreadsheet);
    $filename = $helper->getFileName('21b_Pdf_dompdf.xlsx', 'pdf');
    $writer->setEditHtmlCallback('replaceBody');
    $writer->save($filename);
}

$helper->log('Write to Mpdf');
$writer = new Mpdf($spreadsheet);
$filename = $helper->getFileName('21b_Pdf_mpdf.xlsx', 'pdf');
$writer->setEditHtmlCallback('replaceBody');
$writer->save($filename);

if (\PHP_VERSION_ID < 80000) {
    $helper->log('Write to Tcpdf');
    $writer = new Tcpdf($spreadsheet);
    $filename = $helper->getFileName('21b_Pdf_tcpdf.xlsx', 'pdf');
    $writer->setEditHtmlCallback('replaceBody');
    $writer->save($filename);
}
