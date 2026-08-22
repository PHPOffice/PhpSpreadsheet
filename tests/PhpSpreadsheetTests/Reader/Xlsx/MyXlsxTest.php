<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PHPUnit\Framework\TestCase;

class MyXlsxTest extends TestCase
{
    public function testCustomSpreadsheetCustomLoader(): void
    {
        $reader = new MyXlsxReader();
        $infile = 'tests/data/Reader/XLSX/colorscale.xlsx';
        /** @var MySpreadsheet */
        $mySpreadsheet = $reader->load($infile);
        self::assertSame(64, $mySpreadsheet->calcSquare('A3'));
        $mySpreadsheet->disconnectWorksheets();
    }
}
