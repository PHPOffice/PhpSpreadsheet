<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PHPUnit\Framework\TestCase;

class SimpleCsvTest extends TestCase
{
    public static function testSimpleCsv(): void
    {
        $reader = new SimpleCsv();
        $spreadsheet = $reader->load('tests/data/Reader/CSV/linend.win.csv');
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame([['A', '1'], ['2', '3']], $sheet->toArray());
        $spreadsheet->disconnectWorksheets();
    }
}
