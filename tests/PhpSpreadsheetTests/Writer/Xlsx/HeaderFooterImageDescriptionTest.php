<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class HeaderFooterImageDescriptionTest extends AbstractFunctional
{
    private static function spreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $headerFooter = $spreadsheet->getActiveSheet()->getHeaderFooter();
        $headerFooter->setOddHeader('&L&G&R&G');
        foreach ([HeaderFooter::IMAGE_HEADER_LEFT => 'Company logo', HeaderFooter::IMAGE_HEADER_RIGHT => ''] as $location => $description) {
            $image = new HeaderFooterDrawing();
            $image->setName("Image $location");
            $image->setDescription($description);
            $image->setPath('samples/images/blue_square.png');
            $image->setHeight(12);
            $headerFooter->addImage($image, $location);
        }

        return $spreadsheet;
    }

    public function testWrittenAsAlt(): void
    {
        $spreadsheet = self::spreadsheet();
        $vml = (new XlsxWriter($spreadsheet))->getWriterPartDrawing()->writeVMLHeaderFooterImages($spreadsheet->getActiveSheet());
        self::assertMatchesRegularExpression('/<v:shape id="LH"[^>]* alt="Company logo"><v:imagedata [^>]*o:title="Image LH"/', $vml);
        self::assertSame(1, substr_count($vml, ' alt='));
        $spreadsheet->disconnectWorksheets();
    }

    public function testDescriptionSurvivesTheRoundTrip(): void
    {
        $spreadsheetOld = self::spreadsheet();
        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Xlsx');
        $spreadsheetOld->disconnectWorksheets();
        $images = $spreadsheet->getActiveSheet()->getHeaderFooter()->getImages();
        self::assertSame('Company logo', $images['LH']->getDescription());
        self::assertSame('Image LH', $images['LH']->getName());
        self::assertSame('', $images['RH']->getDescription());
        $spreadsheet->disconnectWorksheets();
    }
}
