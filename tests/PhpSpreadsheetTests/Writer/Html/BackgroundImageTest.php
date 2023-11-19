<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class BackgroundImageTest extends AbstractFunctional
{
    public function testBackgroundImage(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('B1')->setValue(2);
        $sheet->getCell('A2')->setValue(3);
        $sheet->getCell('B2')->setValue(4);
        $imageFile = 'tests/data/Writer/XLSX/backgroundtest.png';
        $image = (string) file_get_contents($imageFile);
        $sheet->setBackgroundImage($image);
        self::assertSame('image/png', $sheet->getBackgroundMime());
        self::assertSame('png', $sheet->getBackgroundExtension());
        $writer = new Html($spreadsheet);
        $header = $writer->generateHTMLHeader(true);
        self::assertStringContainsString('table.sheet0 { background-image:url(data:image/png;base64,', $header);
        $spreadsheet->disconnectWorksheets();
    }
}
