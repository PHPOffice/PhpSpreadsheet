<?php

use Dompdf\Canvas;
use Dompdf\FontMetrics;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;

require __DIR__ . '/../Header.php';

// Issue 2432 - styles were too large to fit in first Mpdf chunk, causing problems.

// Override PhpSpreadsheet class.
class Dompdf_Canvas_Headers extends Dompdf // phpcs:ignore
{
    protected function callPageScript(\Dompdf\Dompdf $dompdf): void
    {
        $dompdf->getCanvas()->page_script(function (
            int $pageNumber,
            int $pageCount,
            Canvas $canvas,
            FontMetrics $fontMetrics
        ): void {
            // Get font metrics for dynamic content like page numbers
            $fontName = 'helvetica'; // note lowercase
            $fontType = 'bold'; // note lowercase
            $font = $fontMetrics->getFont($fontName, $fontType) ?? throw new Exception("no font metrics for $fontName $fontType");
            // Example text placement (adjust coordinates and font as needed)
            if ($pageNumber === 1) {
                $header = 'Dompdf First Page';
            } elseif ($pageNumber % 2 === 0) {
                $header = 'Dompdf Even Page';
            } else {
                $header = 'Dompdf Odd Page';
            }
            $text = "$header          Page $pageNumber of $pageCount";
            $canvas->text(40, 20, $text, $font, 12);
        });
    }
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

$helper->log('Write to Dompdf with headers added by page_script');
IOFactory::registerWriter('Pdf', Dompdf_Canvas_Headers::class);
$helper->write($spreadsheet, __FILE__, ['Pdf']);
$spreadsheet->disconnectWorksheets();
