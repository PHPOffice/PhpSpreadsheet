<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

require __DIR__ . '/../Header.php';

// Issue 2432 - styles were too large to fit in first Mpdf chunk, causing problems.

function addHeadersFootersMpdf2000(string $html): string
{
    $pagerepl = <<<EOF
        @page page0 {
        odd-header-name: html_myHeader1;
        odd-footer-name: html_myFooter2;

        EOF;
    $html = preg_replace('/@page page0 {/', $pagerepl, $html) ?? '';
    $bodystring = '/<body>/';
    $simulatedBodyStart = Mpdf::SIMULATED_BODY_START;
    $bodyrepl = <<<EOF
        <body>
            <htmlpageheader name="myHeader1" style="display:none">
                <div style="text-align: right; border-bottom: 1px solid #000000; font-weight: bold; font-size: 10pt;">
                    My document header
                </div>
            </htmlpageheader>

            <htmlpagefooter name="myFooter2" style="display:none">
                <table width="100%">
                    <tr>
                        <td width="33%">My document</td>
                        <td width="33%" align="center">Page {PAGENO} of {nbpg}</td>
                        <td width="33%" style="text-align: right;">{DATE Y-m-j}</td>
                    </tr>
                </table>
            </htmlpagefooter>
            $simulatedBodyStart

        EOF;

    return preg_replace($bodystring, $bodyrepl, $html) ?? '';
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$counter = 0;
$helper->log('Populate spreadsheet');
for ($row = 1; $row < 1001; ++$row) {
    $sheet->getCell("A$row")->setValue(++$counter);
    // Add many styles by using slight variations of font color for each.
    $sheet->getCell("A$row")->getStyle()->getFont()->getColor()->setRgb(sprintf('%06x', $counter));
    $sheet->getCell("B$row")->setValue(++$counter);
    $sheet->getCell("C$row")->setValue(++$counter);
}

$helper->log('Write to Mpdf');
IOFactory::registerWriter('Pdf', Mpdf::class);
$helper->write($spreadsheet, __FILE__, ['Pdf']);
$helper->write(
    $spreadsheet,
    __FILE__,
    ['Pdf'],
    false,
    function (Mpdf $writer): void {
        $writer->setEditHtmlCallback('addHeadersFootersMpdf2000');
    }
);
$spreadsheet->disconnectWorksheets();
