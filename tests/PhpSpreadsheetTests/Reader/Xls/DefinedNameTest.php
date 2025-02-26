<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PHPUnit\Framework\TestCase;

class DefinedNameTest extends TestCase
{
    public function testAbsoluteNamedRanges(): void
    {
        // Named Ranges were being converted from absolute to relative
        $filename = 'tests/data/Reader/XLS/DefinedNameTest.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame($sheet->getCell('A7')->getCalculatedValue(), $sheet->getCell('B7')->getCalculatedValue());
        self::assertSame($sheet->getCell('A8')->getCalculatedValue(), $sheet->getCell('B8')->getCalculatedValue());
        self::assertSame($sheet->getCell('A9')->getCalculatedValue(), $sheet->getCell('B9')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
