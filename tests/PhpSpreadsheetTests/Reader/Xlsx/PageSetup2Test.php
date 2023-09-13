<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class PageSetup2Test extends TestCase
{
    private const TESTBOOK = 'tests/data/Reader/XLSX/autofilter2.xlsx';

    public function testHeaderFooter(): void
    {
        $spreadsheet = IOFactory::load(self::TESTBOOK);
        $sheets = 0;
        foreach ($spreadsheet->getAllSheets() as $worksheet) {
            ++$sheets;
            $hf = $worksheet->getHeaderFooter();
            self::assertTrue($hf->getDifferentOddEven());
            self::assertTrue($hf->getDifferentFirst());
        }
        self::assertSame(4, $sheets);

        $spreadsheet->disconnectWorksheets();
    }

    public function testColumnBreak(): void
    {
        $spreadsheet = IOFactory::load(self::TESTBOOK);
        $sheet = $spreadsheet->getSheetByNameOrThrow('colbreak');
        $breaks = $sheet->getBreaks();
        self::assertCount(1, $breaks);
        $break = $breaks['D1'] ?? null;
        self::assertNotNull($break);
        self::assertSame($break, Worksheet::BREAK_COLUMN);

        $spreadsheet->disconnectWorksheets();
    }
}
