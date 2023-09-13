<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class Issue2494Test extends TestCase
{
    public function testIssue2494(): void
    {
        // Fill style incorrect.
        $filename = 'tests/data/Reader/XLSX/issue.2494.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertTrue($sheet->getCell('C3')->getStyle()->getFont()->getBold());
        self::assertSame('FFBFBFBF', $sheet->getCell('D8')->getStyle()->getFill()->getStartColor()->getArgb());
        $spreadsheet->disconnectWorksheets();
    }
}
