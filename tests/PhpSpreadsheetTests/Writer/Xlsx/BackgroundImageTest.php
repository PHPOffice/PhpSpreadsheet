<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
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

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $reloadedWorksheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame($image, $reloadedWorksheet->getBackgroundImage());
        self::assertSame('image/png', $reloadedWorksheet->getBackgroundMime());
        self::assertSame('png', $reloadedWorksheet->getBackgroundExtension());
        self::assertSame(2, $reloadedWorksheet->getCell('B1')->getValue());
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testInvalidImage(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $imageFile = __FILE__;
        $image = (string) file_get_contents($imageFile);
        self::assertNotSame('', $image);
        $sheet->setBackgroundImage($image);
        self::assertSame('', $sheet->getBackgroundImage());
        self::assertSame('', $sheet->getBackgroundMime());
        self::assertSame('', $sheet->getBackgroundExtension());
        $spreadsheet->disconnectWorksheets();
    }
}
