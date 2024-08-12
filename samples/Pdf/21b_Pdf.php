<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf as TcpdfClass;

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

    return preg_replace($bodystring, $bodyrepl, $html) ?? '';
}

require __DIR__ . '/../Header.php';
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';

$helper->log('Hide grid lines');
$spreadsheet->getActiveSheet()->setShowGridLines(false);

$helper->log('Set orientation to landscape');
$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

$helper->log('Write to Dompdf');
IOFactory::registerWriter('Pdf', Dompdf::class);
$filename = str_replace('.php', '_dompdf.php', __FILE__);
$helper->write(
    $spreadsheet,
    $filename,
    ['Pdf'],
    false,
    function (Dompdf $writer): void {
        $writer->setEditHtmlCallback('replaceBody');
    }
);

$helper->log('Write to Mpdf');
IOFactory::registerWriter('Pdf', Mpdf::class);
$filename = str_replace('.php', '_mpdf.php', __FILE__);
$helper->write(
    $spreadsheet,
    $filename,
    ['Pdf'],
    false,
    function (Mpdf $writer): void {
        $writer->setEditHtmlCallback('replaceBody');
    }
);

$helper->log('Write to Tcpdf');
IOFactory::registerWriter('Pdf', TcpdfClass::class);
$filename = str_replace('.php', '_tcpdf.php', __FILE__);
$helper->write(
    $spreadsheet,
    $filename,
    ['Pdf'],
    false,
    function (TcpdfClass $writer): void {
        $writer->setEditHtmlCallback('replaceBody');
    }
);
