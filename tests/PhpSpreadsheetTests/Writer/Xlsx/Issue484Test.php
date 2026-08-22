<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\SheetView;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue484Test extends AbstractFunctional
{
    public function testHeaderFooter(): void
    {
        $spreadsheet = new Spreadsheet();
        $headerImage = new HeaderFooterDrawing();
        $headerImage->setName('Header Logo');
        $headerImage->setPath('samples/images/blue_square.png');
        $headerImage->setHeight(12);
        $footerImage = new HeaderFooterDrawing();
        $footerImage->setName('Footer Logo');
        $footerImage->setPath('samples/images/paid.png');
        $footerImage->setHeight(12);

        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->getSheetView()
            ->setView(SheetView::SHEETVIEW_PAGE_LAYOUT);

        $worksheet->getHeaderFooter()->setDifferentFirst(true);
        $worksheet->getHeaderFooter()->setFirstHeader('&C&G&R&D');
        $worksheet->getHeaderFooter()->addImage($headerImage, HeaderFooter::IMAGE_HEADER_CENTER_FIRST);

        $worksheet->getHeaderFooter()->setDifferentOddEven(true);
        $worksheet->getHeaderFooter()->setEvenHeader('&L&G&R&D');
        $worksheet->getHeaderFooter()->addImage($headerImage, HeaderFooter::IMAGE_HEADER_LEFT_EVEN);
        $worksheet->getHeaderFooter()->setEvenFooter('&C&G&R&D');
        $worksheet->getHeaderFooter()->addImage($footerImage, HeaderFooter::IMAGE_FOOTER_CENTER_EVEN);

        $worksheet->getHeaderFooter()->setOddHeader('&C&D');

        for ($currentRow = 1; $currentRow < 130; ++$currentRow) {
            $worksheet->setCellValue("A$currentRow", 'Bill');
            $worksheet->setCellValue("B$currentRow", 'Smith');
        }

        // Save spreadsheet to file and read it back
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $sheet = $reloadedSpreadsheet->getActiveSheet();
        $images = $sheet->getHeaderFooter()->getImages();
        self::assertSame(['LHEVEN', 'CHFIRST', 'CFEVEN'], array_keys($images));
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
