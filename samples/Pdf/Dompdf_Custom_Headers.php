<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;

require __DIR__ . '/../Header.php';

// Issue 2432 - styles were too large to fit in first Mpdf chunk, causing problems.

function addHeadersFootersDompdf2000(string $html): string
{
    $headerFooter = <<<EOF
        <style type='text/css'>
        header {
            position: fixed;
            top: -0.5in; /* Position in top margin area */
            height: 0.5in;
            width: 100%;
            text-align: center;
        }
        footer {
            position: fixed;
            bottom: -0.5in; /* Position in bottom margin area */
            height: 0.5in;
            width: 100%;
            text-align: right;
        }
        .pagenum:before {content: counter(page);}
        </style>

        EOF;
    $endhead = '</head>';
    $html = str_replace($endhead, "$headerFooter$endhead", $html);
    $bodystring = '<body>';
    $bodyrepl = <<<EOF
        <body>
            <header>Dompdf Fixed header</header>
            <footer>Page <span class="pagenum"></span></footer>
        EOF;

    return str_replace($bodystring, $bodyrepl, $html);
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$counter = 0;
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$helper->log('Populate spreadsheet');
for ($row = 1; $row < 1001; ++$row) {
    $sheet->getCell("A$row")->setValue(++$counter);
    // Add many styles by using slight variations of font color for each.
    $sheet->getCell("A$row")->getStyle()->getFont()
        ->getColor()->setRgb(sprintf('%06x', $counter));
    $sheet->getCell("B$row")->setValue(++$counter);
    $sheet->getCell("C$row")->setValue(++$counter);
}

$helper->log('Write to Dompdf');
IOFactory::registerWriter('Pdf', Dompdf::class);
$helper->write(
    $spreadsheet,
    __FILE__,
    ['Pdf'],
    false,
    function (Dompdf $writer): void {
        $writer->setEditHtmlCallback('addHeadersFootersDompdf2000');
    }
);
$spreadsheet->disconnectWorksheets();
