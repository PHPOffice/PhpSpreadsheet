<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class Issue5102Test extends TestCase
{
    private const INFILE = 'tests/data/Reader/XLSX/benign_min1_max16384.xlsx';

    public function testDefaultUsesHighestColumn(): void
    {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load(self::INFILE);
        self::assertFalse($spreadsheet->getUseHighestDataColumn());
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('XFD', $sheet->getHighestColumn());
        self::assertCount(16384, $sheet->toArray()[0]);
        self::assertSame(16384, iterator_count($sheet->getRowIterator()->current()->getCellIterator()));
        $spreadsheet->disconnectWorksheets();
    }

    public function testUseHighestDataColumn(): void
    {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load(self::INFILE);
        $spreadsheet->setUseHighestDataColumn(true);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('XFD', $sheet->getHighestColumn());
        self::assertSame([['hi']], $sheet->toArray());
        self::assertSame(1, iterator_count($sheet->getRowIterator()->current()->getCellIterator()));
        $spreadsheet->disconnectWorksheets();
    }
}
