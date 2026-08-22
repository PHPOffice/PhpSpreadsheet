<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class XlsBugPr3734Test extends TestCase
{
    /**
     * Test XLS file including data with missing fonts?
     */
    public function testXlsFileWithNoFontIndex(): void
    {
        $fileName = 'tests/data/Reader/XLS/bug-pr-3734.xls';
        $spreadsheet = IOFactory::load($fileName);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Calibri', $sheet->getStyle('A1')->getFont()->getName());
        $spreadsheet->disconnectWorksheets();
    }
}
