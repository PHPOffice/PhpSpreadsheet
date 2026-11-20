<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Mpdf;

use Mpdf\Mpdf as MpdfClass;
use Mpdf\PageFormat;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PHPUnit\Framework\TestCase;

class BadPaperSizeTest extends TestCase
{
    public function testGoodPaperSize(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getCell('A1')->setValue('hello');
        $file = File::temporaryFilename();

        try {
            $writer = new Mpdf($spreadsheet);
            $writer->save($file);
            // Returns [width, height] in points (pt)
            $dimensions = PageFormat::getSizeFromName('A4');
            $expectedWidth = $dimensions[0] / 72; // convert to inches
            $expectedHeight = $dimensions[1] / 72; // convert to inches
            $mpdf = new MpdfClass();
            $mpdf->setSourceFile($file);
            $tplId = $mpdf->importPage(1);
            /** @var array{width: float, height: float} results in millimeters */
            $size = $mpdf->getTemplateSize($tplId);
            $actualWidth = $size['width'] / 25.4; // convert to inches
            $actualHeight = $size['height'] / 25.4; // convert to inches
            self::assertEqualsWithDelta($expectedWidth, $actualWidth, 1e-8);
            self::assertEqualsWithDelta($expectedHeight, $actualHeight, 1e-8);
        } finally {
            $spreadsheet->disconnectWorksheets();
            unlink($file);
        }
    }

    public function testBadPaperSize(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setPaperSize(999999);
        $sheet->getCell('A1')->setValue('hello');
        $file = File::temporaryFilename();

        try {
            $writer = new Mpdf($spreadsheet);
            $writer->save($file);
            // Returns [width, height] in points (pt)
            $dimensions = PageFormat::getSizeFromName('LETTER');
            $expectedWidth = $dimensions[0] / 72; // convert to inches
            $expectedHeight = $dimensions[1] / 72; // convert to inches
            $mpdf = new MpdfClass();
            $mpdf->setSourceFile($file);
            $tplId = $mpdf->importPage(1);
            /** @var array{width: float, height: float} results in millimeters */
            $size = $mpdf->getTemplateSize($tplId);
            $actualWidth = $size['width'] / 25.4; // convert to inches
            $actualHeight = $size['height'] / 25.4; // convert to inches
            self::assertEqualsWithDelta($expectedWidth, $actualWidth, 1e-8);
            self::assertEqualsWithDelta($expectedHeight, $actualHeight, 1e-8);
        } finally {
            $spreadsheet->disconnectWorksheets();
            unlink($file);
        }
    }
}
