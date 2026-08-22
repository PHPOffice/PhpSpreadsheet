<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\TcpdfNoDie;
use TCPDF as VendorTcpdf;

require __DIR__ . '/../Header.php';

// Override PhpSpreadsheet class.
class Tcpdf2 extends TcpdfNoDie // phpcs:ignore
{
    protected bool $writeHeader = true;

    protected bool $writeFooter = true;

    protected function createExternalWriterInstance(string $orientation, string $unit, $paperSize): VendorTcpdf
    {
        $this->defines();

        return new Tcpdf2Class($orientation, $unit, $paperSize);
    }
}

// Override vendor class.
class Tcpdf2Class extends VendorTcpdf // phpcs:ignore
{
    // Page header
    public function Header(): void // phpcs:ignore
    {
        // Position at 15 mm from top
        $this->setY(15);
        // Set font
        $this->setFont('helvetica', 'B', 12);
        // Title
        $pageNum = $this->getPage();
        if ($pageNum === 1) {
            $this->Cell(0, 15, 'Tcpdf first header', 0, 0, 'C', false, '', 0, false, 'M', 'M');
        } elseif ($pageNum % 2 === 0) {
            $this->Cell(0, 15, 'Tcpdf even header', 0, 0, 'C', false, '', 0, false, 'M', 'M');
        } else {
            $this->Cell(0, 15, 'Tcpdf odd header', 0, 0, 'C', false, '', 0, false, 'M', 'M');
        }
    }

    // Page footer
    public function Footer(): void // phpcs:ignore
    {
        // Position at 15 mm from bottom
        $this->setY(-15);
        // Set font
        $this->setFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C', false, '', 0, false, 'T', 'M');
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

$helper->log('Write to Tcpdf with headers and footers');
IOFactory::registerWriter('Pdf', Tcpdf2::class);
$helper->write($spreadsheet, __FILE__, ['Pdf']);
$spreadsheet->disconnectWorksheets();
