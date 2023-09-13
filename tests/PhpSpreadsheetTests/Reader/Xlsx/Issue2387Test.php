<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PHPUnit\Framework\TestCase;

class Issue2387Test extends TestCase
{
    public function testIssue2387(): void
    {
        // Theme was not being handled.
        $filename = 'tests/data/Reader/XLSX/issue.2387.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        // Font color being tested uses theme color with tint.
        // Excel shows final color as 305496.
        $expectedColor = '305496';
        $calculatedColor = $sheet->getCell('B2')->getStyle()->getFont()->getColor()->getRgb();
        self::assertSame($expectedColor, RgbTintTest::compareColors($calculatedColor, $expectedColor));
        self::assertSame(Fill::FILL_NONE, $sheet->getCell('B2')->getStyle()->getFill()->getFillType());
        self::assertSame('FFFFFF', $sheet->getCell('C2')->getStyle()->getFont()->getColor()->getRgb());
        self::assertSame('000000', $sheet->getCell('C2')->getStyle()->getFill()->getStartColor()->getRgb());
        self::assertSame(Fill::FILL_SOLID, $sheet->getCell('C2')->getStyle()->getFill()->getFillType());

        $spreadsheet->disconnectWorksheets();
    }
}
